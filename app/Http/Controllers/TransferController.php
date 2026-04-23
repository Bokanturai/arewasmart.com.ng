<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\ServiceField;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TransferController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Fetch last distinct P2P recipients from transactions table
        $recentRecipients = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('status', 'completed')
            ->where('metadata->service', 'P2P')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($trx) {
                return [
                    'account_no' => $trx->metadata['receiver_wallet'] ?? '',
                    'account_name' => $trx->metadata['receiver_name'] ?? 'User',
                    'bank_name' => 'Arewa Smart User',
                    'bank_url' => null, // We'll use a default icon in blade
                    'bank_code' => 'P2P'
                ];
            })
            ->filter(fn($item) => !empty($item['account_no']))
            ->unique('account_no')
            ->values()
            ->take(15);

        return view('wallet.p2p', compact('recentRecipients'));
    }

    public function verifyUser(Request $request)
    {
        $user = Auth::user();
        if (($user->status ?? 'inactive') !== 'active') {
            return response()->json(['success' => false, 'message' => "Your account is currently " . ($user->status ?? 'inactive') . ". Access denied."]);
        }

        $request->validate([
            'wallet_id' => 'required|string',
        ]);

        $query = $request->wallet_id;

        // Try Wallet ID first
        $wallet = Wallet::where('wallet_number', $query)->first();
        $targetUser = $wallet ? $wallet->user : null;

        if (!$targetUser) {
            // Try Email or Phone
            $targetUser = User::where('email', $query)
                ->orWhere('phone_no', $query)
                ->first();
            
            if ($targetUser) {
                $wallet = $targetUser->wallet;
            }
        }

        if ($targetUser && $wallet) {
            $fullName = trim($targetUser->first_name . ' ' . $targetUser->last_name . ' ' . $targetUser->middle_name);
            
            // Determine photo URL
            $photoUrl = null;
            if ($targetUser->photo) {
                $photoUrl = asset('storage/' . $targetUser->photo);
            } elseif ($targetUser->profile_photo_url) {
                $photoUrl = $targetUser->profile_photo_url;
            }

            return response()->json([
                'success' => true,
                'user_name' => $fullName,
                'wallet_id' => $wallet->wallet_number,
                'photo' => $photoUrl
            ]);
        }

        return response()->json(['success' => false, 'message' => 'User not found']);
    }

    public function processTransfer(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|exists:wallets,wallet_number',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
            'pin' => 'required|digits:5',
        ]);

        $user = Auth::user();
        // 0. Preliminary Status Checks
        if ($user->status !== 'active') {
             if ($request->wantsJson()) {
                 return response()->json(['success' => false, 'message' => "Your account is currently {$user->status}. Access denied."], 403);
             }
             return redirect()->back()->with('error', "Your account is currently {$user->status}. Access denied.");
        }

        $senderWallet = $user->wallet;
        $amount = $request->amount;
        
        // Verify PIN or Biometric
        $isBiometricValid = $request->biometric_auth && 
                           session('biometric_verified_at') && 
                           (now()->timestamp - session('biometric_verified_at')) < 60; // 60 seconds window

        if (!$isBiometricValid && !Hash::check($request->pin, $user->pin)) {
             if ($request->wantsJson()) {
                 return response()->json(['success' => false, 'message' => 'Incorrect Transaction PIN.'], 403);
             }
             return back()->with('error', 'Incorrect Transaction PIN.');
        }


        // Clear biometric flag after use for security
        if ($isBiometricValid) session()->forget('biometric_verified_at');

        // Get Receiver
        $receiverWallet = Wallet::where('wallet_number', $request->wallet_id)->first();
        if (!$receiverWallet) {
            if ($request->wantsJson()) return response()->json(['success' => false, 'message' => 'Receiver wallet not found.'], 404);
            return back()->with('error', 'Receiver wallet not found.');
        }
        
        if ($senderWallet->id === $receiverWallet->id) {
            if ($request->wantsJson()) return response()->json(['success' => false, 'message' => 'You cannot transfer money to yourself.'], 422);
            return back()->with('error', 'You cannot transfer money to yourself.');
        }


        // Get Service Charge
        // Assuming there is a service field for P2P. 
        // If not found, we might default to 0 or throw error. 
        // User said "get the service name P2P and get service price from the servicefield"
        $serviceField = ServiceField::where('field_name', 'P2P')->first();
        $charge = 0;
        
        if ($serviceField) {
            // charge the user based on role
            $charge = $serviceField->getPriceForUserType($user->role ?? 'user'); 
        }

        $totalDeduction = $amount + $charge;

        if ($senderWallet->balance < $totalDeduction) {
            if ($request->wantsJson()) return response()->json(['success' => false, 'message' => 'Insufficient balance.'], 422);
            return back()->with('error', 'Insufficient balance.');
        }


        DB::beginTransaction();

        try {
            // Debit Sender
            $senderWallet->balance -= $totalDeduction;
            $senderWallet->save();

            // Credit Receiver
            $receiverWallet->balance += $amount;
            $receiverWallet->save();

            $transactionRef = 'TRX-' . strtoupper(Str::random(10));

            // Create Sender Transaction
            $senderTransaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id' => $user->id,
                'amount' => $amount,
                'fee' => $charge,
                'net_amount' => $totalDeduction,
                'description' => $request->description ?? 'Transfer to ' . $receiverWallet->user->first_name,
                'type' => 'debit',
                'status' => 'completed',
                'metadata' => [
                    'service' => 'P2P',
                    'receiver_wallet' => $receiverWallet->wallet_number,
                    'receiver_name' => $receiverWallet->user->first_name . ' ' . $receiverWallet->user->last_name
                ],
                'performed_by' => $user->id,
            ]);

            // Create Receiver Transaction
            Transaction::create([
                'transaction_ref' => 'TRX-' . strtoupper(Str::random(10)), // Unique ref for receiver? Or same? Usually different or linked.
                'user_id' => $receiverWallet->user->id,
                'amount' => $amount,
                'fee' => 0,
                'net_amount' => $amount,
                'description' => 'Received from ' . $user->first_name,
                'type' => 'credit',
                'status' => 'completed',
                'metadata' => [
                    'service' => 'P2P',
                    'sender_wallet' => $senderWallet->wallet_number,
                    'sender_name' => $user->first_name . ' ' . $user->last_name
                ],
                'performed_by' => $user->id,
            ]);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transfer successful!',
                    'data' => [
                        'transaction' => $senderTransaction,
                        'amount' => $amount,
                        'receiver' => $receiverWallet->user->first_name
                    ]
                ]);
            }

            return view('thankyou2', [
                'transaction' => $senderTransaction,
                'sender' => $user,
                'receiver' => $receiverWallet->user,
                'amount' => $amount,
                'date' => now()
            ]);


        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) return response()->json(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()], 500);
            return back()->with('error', 'Transaction failed: ' . $e->getMessage());
        }

    }
}

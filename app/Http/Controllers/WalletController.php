<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VirtualAccount;
use App\Repositories\VirtualAccountRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;


class WalletController extends Controller
{
    /**
     * Show wallet dashboard
     */
    public function index()
    {
        $userId = Auth::id();

        $virtualAccount = VirtualAccount::where('user_id', $userId)->first();
        $wallet = Wallet::where('user_id', $userId)->first();

        $walletData = [
            'balance'           => $wallet->balance ?? 0,
            'bonus'             => $wallet->bonus ?? 0,
            'status'            => $wallet->status ?? 'inactive',
            'available_balance' => $wallet->available_balance ?? 0,
        ];

        // Dynamic rewards & bonus spend progress
        $currentSpend = Transaction::where('user_id', $userId)
            ->where('type', 'debit')
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount') ?? 0;

        $bonusTarget = 50000;
        $nextBonusTarget = 50000;
        $bonusProgress = $bonusTarget > 0 ? min(100, round(($currentSpend / $bonusTarget) * 100)) : 0;

        return view('wallet.index', compact(
            'virtualAccount', 
            'walletData', 
            'currentSpend', 
            'bonusTarget', 
            'nextBonusTarget', 
            'bonusProgress'
        ));
    }

    /**
     * Create Virtual Wallet
     */
    public function createWallet(Request $request)
    {
        $loginUserId = Auth::id(); 
        $user = User::find($loginUserId);

        // Check KYC details
        if (empty($user->bvn) || empty($user->phone_no)) {
            return redirect()->route('wallet')->with([
                'error' => 'Please complete your registration by providing your BVN and Phone Number to open a virtual account.'
            ]);
        }

        // Repository call
        $repObj2 = new VirtualAccountRepository;
        $result = $repObj2->createVirtualAccount($loginUserId);

        // Handle failure
        if (!is_array($result) || !isset($result['success']) || !$result['success']) {
            $message = is_array($result) && isset($result['message'])
                ? $result['message']
                : 'Wallet creation failed. Please try again later.';

            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $message], 400);
            }
            return redirect()->route('wallet')->with(['error' => $message]);
        }

        // Success
        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => $result['message'], 'data' => $result]);
        }
        return redirect()->route('wallet')->with(['success' => $result['message']]);
    }

    /**
     * Claim bonus: move bonus to wallet_balance and record transaction
     */
    public function claimBonus(Request $request)
    {
        $userId = Auth::id();

        try {
            $bonusAmount = DB::transaction(function () use ($userId) {
                // Fetch the wallet with a pessimistic lock INSIDE the active database transaction
                $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();

                if (!$wallet || $wallet->bonus <= 0) {
                    throw new \Exception('No bonus available to claim.');
                }

                $bonus = $wallet->bonus;

                // Update wallet balances
                $wallet->balance += $bonus;
                $wallet->bonus = 0;
                $wallet->save();

                // Performed by
                $user = User::find($userId);
                $performedBy = $user ? $user->first_name . ' ' . $user->last_name : 'System';

                // Save transaction
                Transaction::create([
                    'user_id'         => $userId,
                    'type'            => 'credit',
                    'amount'          => $bonus,
                    'fee'             => 0.00,
                    'net_amount'      => $bonus,
                    'description'     => 'Bonus claimed and credited to wallet balance',
                    'status'          => 'completed',
                    'transaction_ref' => 'BONUS-' . strtoupper(uniqid()),
                    'performed_by'    => $performedBy,
                ]);

                return $bonus;
            });

            return redirect()->route('wallet')->with([
                'success' => 'Bonus of ₦' . number_format($bonusAmount, 2) . ' successfully claimed and added to your wallet balance.'
            ]);

        } catch (\Exception $e) {
            return redirect()->route('wallet')->with([
                'error' => $e->getMessage()
            ]);
        }
    }

}
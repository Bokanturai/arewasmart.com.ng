<?php

namespace App\Http\Controllers\Action;

use App\Helpers\RequestIdHelper;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Http\Requests\Action\BuyAirtimeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class AirtimeController extends Controller
{
    protected $loginUserId;

    public function __construct()
    {
        $this->loginUserId = Auth::id();
    }

    /**
     * Show Airtime purchase form
     */
    public function airtime()
    {
        $user = Auth::user();

        // Wallet is already ensured via middleware or should be checked here
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'status' => 'active']
        );

        // Fetch unique recent airtime recipients
        $recentRecipients = \App\Models\Report::where('user_id', $user->id)
            ->where('type', 'airtime')
            ->where('status', 'successful')
            ->orderBy('created_at', 'desc')
            ->limit(50) // Get a larger set first to filter unique phone numbers
            ->get()
            ->unique('phone_number')
            ->take(15)
            ->map(function ($report) {
                $network = strtolower($report->network);
                $img = match (true) {
                    str_contains($network, 'mtn')      => 'mtn.jpg',
                    str_contains($network, 'airtel')   => 'Airtel.png',
                    str_contains($network, 'glo')      => 'glo.jpg',
                    str_contains($network, 'etisalat'),
                    str_contains($network, '9mobile')  => '9Mobile.jpg',
                    default                            => 'default.png',
                };

                return [
                    'account_no'   => $report->phone_number,
                    'account_name' => $report->phone_number,
                    'bank_name'    => strtoupper($report->network),
                    'bank_code'    => $report->network,
                    'bank_url'     => asset('assets/img/apps/' . $img)
                ];
            })
            ->values();

        return view('utilities.index', [
            'user'             => $user,
            'wallet'           => $wallet,
            'recentRecipients' => $recentRecipients
        ]);
    }

    /**
     * Handle Airtime Purchase
     */
    /**
     * Handle Airtime Purchase
     */
    public function buyAirtime(BuyAirtimeRequest $request)
    {
        $user       = Auth::user();
        $networkKey = strtolower($request->network);
        $mobile     = $request->mobileno;
        $amount     = $request->amount;
        $requestId  = RequestIdHelper::generateRequestId();
        
        // 0. Preliminary Status Checks
        if ($user->status !== 'active') {
             if ($request->wantsJson()) {
                 return response()->json(['status' => 'error', 'message' => "Your account is currently {$user->status}. Access denied."], 403);
             }
             return redirect()->back()->with('error', "Your account is currently {$user->status}. Access denied.");
        }


        // 1. Idempotency Check (Prevent duplicate requests within 60 seconds)
        $recentTransaction = \App\Models\Report::where('user_id', $user->id)
            ->where('phone_number', $mobile)
            ->where('amount', $amount)
            ->where('type', 'airtime')
            ->where('created_at', '>=', now()->subMinute())
            ->first();

        if ($recentTransaction) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'A similar transaction was recently processed. Please wait a moment.'], 422);
            }
            return redirect()->back()->with('error', 'A similar transaction was recently processed. Please wait a moment before trying again.');
        }


        // 2. Find the Airtime Service & Field
        $service = Service::where('name', 'Airtime')->first();
        if (!$service) {
             $service = Service::firstOrCreate(['name' => 'Airtime'], ['status' => 'active']);
        }

        // Exact match preferred for security over LIKE
        $serviceField = \App\Models\ServiceField::where('service_id', $service->id)
            ->where(function($q) use ($networkKey) {
                $q->where('field_code', $networkKey)
                  ->orWhere('field_name', 'LIKE', "%{$networkKey}%");
            })->orderByRaw("field_code = ? DESC", [$networkKey]) // Prioritize exact code match
            ->first();

        // 3. Calculate Discount
        $discountPercentage = 0;
        if ($serviceField) {
            $userType = $user->user_type ?? 'personal'; 
            $servicePrice = \App\Models\ServicePrice::where('service_fields_id', $serviceField->id)
                ->where('user_type', $userType)
                ->first();

            $discountPercentage = $servicePrice ? $servicePrice->price : ($serviceField->base_price ?? 0);
        }

        $discountAmount = ($amount * $discountPercentage) / 100;
        $payableAmount = $amount - $discountAmount;

        // 4. Check Wallet Balance & Status
        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet || $wallet->balance < $payableAmount) {

            $msg = 'Insufficient wallet balance! You need ₦' . number_format($payableAmount, 2);
            if ($request->wantsJson()) return response()->json(['status' => 'error', 'message' => $msg], 422);
            return redirect()->back()->with('error', $msg);
        }

        if ($wallet->status !== 'active') {
            if ($request->wantsJson()) return response()->json(['status' => 'error', 'message' => 'Your wallet is not active.'], 403);
            return redirect()->back()->with('error', 'Your wallet is not active. Please contact support.');
        }


        // 5. Initialize Records (Mark as Processing)
        DB::beginTransaction();
        try {
            $oldBalance = $wallet->balance;
            $wallet->decrement('balance', $payableAmount);
            $newBalance = $wallet->balance;

            $transaction = Transaction::create([
                'transaction_ref' => $requestId,
                'user_id'         => $user->id,
                'amount'          => $amount,
                'fee'             => 0,
                'net_amount'      => $payableAmount,
                'description'     => "Airtime purchase of ₦{$amount} for {$mobile} ({$networkKey})",
                'type'            => 'debit',
                'status'          => 'processing',
                'performed_by'    => $user->first_name . ' ' . $user->last_name,
                'approved_by'     => $user->id,
            ]);


            $report = \App\Models\Report::create([
                'user_id'      => $user->id,
                'phone_number' => $mobile,
                'network'      => $networkKey,
                'ref'          => $requestId,
                'amount'       => $amount,
                'status'       => 'processing', // Use processing state
                'type'         => 'airtime',
                'description'  => "Processing: Airtime purchase for {$mobile}",
                'old_balance'  => $oldBalance,
                'new_balance'  => $newBalance,
                'service_id'   => $serviceField ? $serviceField->id : null,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Airtime Initialization Error: ' . $e->getMessage());
            if ($request->wantsJson()) return response()->json(['status' => 'error', 'message' => 'Failed to initialize transaction.'], 500);
            return redirect()->back()->with('error', 'Failed to initialize transaction. Please try again.');
        }


        // 6. Call Airtime API (OUTSIDE the DB Transaction)
        try {
            $response = Http::withHeaders([
                'api-key'    => env('API_KEY'),
                'secret-key' => env('SECRET_KEY'),
            ])->timeout(30)->post(env('MAKE_PAYMENT'), [
                'request_id' => $requestId,
                'serviceID'  => $networkKey,
                'amount'     => $amount,
                'phone'      => $mobile,
            ]);

            $data = $response->json();
            $successCodes = ['0', '00', '000', '200'];
            $isSuccessful = false;
            
            if ($response->successful()) {
                 if (isset($data['code']) && in_array((string)$data['code'], $successCodes)) {
                    $isSuccessful = true;
                } elseif (isset($data['status']) && strtolower($data['status']) === 'success') {
                    $isSuccessful = true;
                }
            }

            if ($isSuccessful) {
                $transaction->update([
                    'status'   => 'completed', 
                    'metadata' => json_encode([
                        'phone'        => $mobile,
                        'network'      => $networkKey,
                        'discount'     => $discountAmount,
                        'api_response' => $data,
                    ]),
                ]);
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Airtime purchase successful!',
                        'data' => [
                            'ref' => $requestId,
                            'mobile' => $mobile,
                            'amount' => $amount,
                            'paid' => $payableAmount,
                            'network' => $networkKey
                        ]
                    ]);
                }

                return redirect()->route('thankyou')->with([
                    'success' => 'Airtime purchase successful!',
                    'ref'     => $requestId,
                    'mobile'  => $mobile,
                    'amount'  => $amount,
                    'paid'    => $payableAmount,
                    'network' => $networkKey
                ]);

            }

            // API Failed - REFUND
            DB::transaction(function () use ($wallet, $payableAmount, $transaction, $report, $data) {
                $wallet->increment('balance', $payableAmount);
                $transaction->update(['status' => 'failed']);
                $report->update([
                    'status'      => 'failed',
                    'description' => "Failed: " . ($data['message'] ?? 'API Provider Error'),
                ]);
            });

            Log::error('Airtime API Error', ['response' => $data, 'ref' => $requestId]);
            $msg = 'Airtime purchase failed. ' . ($data['message'] ?? 'Unknown error');
            if ($request->wantsJson()) return response()->json(['status' => 'error', 'message' => $msg], 400);
            return redirect()->back()->with('error', $msg);


        } catch (\Exception $e) {
            // Unexpected Error (e.g. timeout) - Mark as failed and REFUND
            DB::transaction(function () use ($wallet, $payableAmount, $transaction, $report, $e) {
                $wallet->increment('balance', $payableAmount);
                $transaction->update(['status' => 'failed']);
                $report->update([
                    'status'      => 'failed',
                    'description' => "Error: " . $e->getMessage(),
                ]);
            });

            Log::error('Airtime API Exception: ' . $e->getMessage());
            if ($request->wantsJson()) return response()->json(['status' => 'error', 'message' => 'Connection error. Your wallet has been refunded.'], 500);
            return redirect()->back()->with('error', 'Connection error. Your wallet has been refunded.');
        }

    }
}

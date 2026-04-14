<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\PalmPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WithdrawController extends Controller
{
    protected $palmPay;

    public function __construct(PalmPayService $palmPay)
    {
        $this->palmPay = $palmPay;
    }

    /**
     * Show withdrawal index page.
     */
    public function index()
    {
        $user = Auth::user();

        // Ensure Withdrawal service and fields exist
        $service = Service::firstOrCreate(
            ['name' => 'Withdrawal'],
            ['description' => 'Wallet Withdrawal Service', 'is_active' => true]
        );

        ServiceField::firstOrCreate(
            ['service_id' => $service->id, 'field_name' => 'withdrawal fee'],
            ['field_code' => 'WDL_001', 'description' => 'Fee charged for withdrawals', 'base_price' => 0, 'is_active' => true]
        );

        ServiceField::firstOrCreate(
            ['service_id' => $service->id, 'field_name' => 'withdrawal eligibility'],
            ['field_code' => 'WDL_002', 'description' => 'Minimum transaction volume for eligibility', 'base_price' => 2000000, 'is_active' => true]
        );

        $banks = Bank::where('is_active', true)->orderBy('bank_name')->get();

        // Calculate total transaction volume
        $totalVolume = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('status', 'completed')
            ->sum('amount');

        // Fetch the "withdrawal eligibility" service field and calculate role-based amount
        $eligibilityField = ServiceField::where('field_name', 'withdrawal eligibility')->first();
        $eligibilityAmount = $eligibilityField ? $eligibilityField->getPriceForUserType($user->role) : 2000000;

        // Fetch last 5 unique bank recipients from report table (type: withdrawal)
        $recentRecipients = \App\Models\Report::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($report) use ($banks) {
                $bank = $banks->firstWhere('bank_code', $report->bank_code);
                return [
                    'bank_code' => $report->bank_code,
                    'account_no' => $report->account_number,
                    'account_name' => $report->account_name,
                    'bank_name' => $report->bank_name ?? 'Bank',
                    'bank_url' => $bank->bank_url ?? null
                ];
            })
            ->filter(fn($item) => !empty($item['bank_code']) && !empty($item['account_no']))
            ->unique(fn($item) => $item['bank_code'] . $item['account_no'])
            ->values()
            ->take(15);

        return view('wallet.withdraw', compact('banks', 'totalVolume', 'user', 'recentRecipients', 'eligibilityAmount'));
    }

    /**
     * Sync banks from PalmPay.
     */
    public function syncBanks()
    {
        if (Auth::user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $response = $this->palmPay->queryBankList();

        if (isset($response['respCode']) && $response['respCode'] === '00000000') {
            $banksData = $response['data'];

            foreach ($banksData as $bank) {
                Bank::updateOrCreate(
                    ['bank_code' => $bank['bankCode']],
                    [
                        'bank_name' => $bank['bankName'],
                        'bank_url' => $bank['bankUrl'] ?? null,
                        'bg_url' => $bank['bgUrl'] ?? null,
                        'is_active' => true,
                    ]
                );
            }

            return back()->with('success', 'Banks synced successfully.');
        }

        return back()->with('error', 'Failed to sync banks: ' . ($response['respMsg'] ?? 'Unknown error'));
    }

    /**
     * Verify bank account name.
     */
    public function verifyAccount(Request $request)
    {
        $request->validate([
            'bank_code' => 'required|string',
            'account_no' => 'required|string|digits:10',
        ]);

        $response = $this->palmPay->queryBankAccount($request->bank_code, $request->account_no);

        if (isset($response['respCode']) && $response['respCode'] === '00000000') {
            if ($response['data']['Status'] === 'Success') {
                return response()->json([
                    'success' => true,
                    'account_name' => $response['data']['accountName']
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => $response['data']['errorMessage'] ?? 'Account verification failed.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'PalmPay API Error: ' . ($response['respMsg'] ?? 'Unable to verify account.')
        ]);
    }

    /**
     * Process withdrawal.
     */
    public function processWithdrawal(Request $request)
    {
        $request->validate([
            'bank_code' => 'required|exists:banks,bank_code',
            'account_no' => 'required|digits:10',
            'account_name' => 'required|string',
            'amount' => 'required|numeric|min:100', // Minimum 100 NGN
            'pin' => 'required|digits:5',
        ]);

        $user = Auth::user();

        // 1. One Active Withdrawal at a Time (Check for Processing/Pending)
        $hasPending = \App\Models\Report::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'You have an active withdrawal being processed. Please wait for it to complete.');
        }

        // 2. Lock User While Transaction is Processing
        $lockKey = 'withdrawal_lock_' . $user->id;
        $lock = \Illuminate\Support\Facades\Cache::lock($lockKey, 60); // 60 seconds lock

        if (!$lock->get()) {
            return back()->with('error', 'Your previous transaction is still processing.');
        }

        try {
            // 3. Prevent Duplicate: Return old result for identical recent request (within 2 mins)
            $recentDuplicate = Transaction::where('user_id', $user->id)
                ->where('type', 'debit')
                ->where('status', 'completed')
                ->where('amount', $request->amount)
                ->where('metadata->account_no', $request->account_no)
                ->where('created_at', '>=', now()->subMinutes(2))
                ->first();

            if ($recentDuplicate) {
                $bankName = Bank::where('bank_code', $request->bank_code)->value('bank_name') ?? 'Bank';
                return view('thankyou2', [
                    'transaction' => $recentDuplicate,
                    'sender' => $user,
                    'receiver_name' => $request->account_name . ' (' . $bankName . ')',
                    'service_name' => 'Wallet Withdrawal',
                    'amount' => $request->amount,
                    'date' => $recentDuplicate->created_at
                ]);
            }

            // 4. PIN Verification & Rate Limiting (Max 5 attempts / 15 mins)
            $pinLimiterKey = 'withdraw_pin_' . $user->id;

            if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($pinLimiterKey, 5)) {
                $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($pinLimiterKey);
                return back()->with('error', "Too many incorrect PIN attempts. Please try again in {$seconds} seconds.");
            }

            if (!Hash::check($request->pin, $user->pin)) {
                \Illuminate\Support\Facades\RateLimiter::hit($pinLimiterKey, 900); // 15 mins
                return back()->with('error', 'Incorrect transaction PIN.');
            }

            \Illuminate\Support\Facades\RateLimiter::clear($pinLimiterKey);

            // 5. Service Check / Auto-creation
            $service = Service::firstOrCreate(
                ['name' => 'Withdrawal'],
                ['description' => 'Wallet Withdrawal Service', 'is_active' => true]
            );

            if (!$service->is_active) {
                return back()->with('error', 'Withdrawal service is currently inactive.');
            }

            // 3. Service Fields (Fee and Eligibility) from DB
            $feeField = ServiceField::firstOrCreate(
                ['service_id' => $service->id, 'field_name' => 'withdrawal fee'],
                ['field_code' => 'WDL_001', 'description' => 'Fee charged for withdrawals', 'base_price' => 0, 'is_active' => true]
            );

            $eligibilityField = ServiceField::firstOrCreate(
                ['service_id' => $service->id, 'field_name' => 'withdrawal eligibility'],
                ['field_code' => 'WDL_002', 'description' => 'Minimum transaction volume for eligibility', 'base_price' => 2000000, 'is_active' => true]
            );

            // Calculate Pricing & Eligibility based on role
            $fee = $feeField->getPriceForUserType($user->role);
            $eligibilityAmount = $eligibilityField->getPriceForUserType($user->role);

            // Eligibility Check - Total Transaction Volume
            $totalVolume = Transaction::where('user_id', $user->id)
                ->where('type', 'debit')
                ->where('status', 'completed')
                ->sum('amount');

            if ($totalVolume < $eligibilityAmount) {
                return back()->with('error', 'You must perform at least ' . number_format($eligibilityAmount, 2) . ' in total transactions to be eligible for withdrawal.');
            }

            // 4. Withdrawal Limit Check
            if ($request->amount > $user->limit) {
                return back()->with('error', 'Amount exceeds your withdrawal limit of ' . number_format($user->limit, 2));
            }

            $totalCharge = $request->amount + $fee;

            // Phase 1: Deduct Wallet & Create Pending Records safely inside a DB lock
            DB::beginTransaction();
            try {
                // 5. Lock wallet row and check balance/status
                $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

                if (!$wallet) {
                    throw new \Exception('Wallet not found.');
                }

                if ($wallet->status !== 'active') {
                    throw new \Exception('Your wallet is not active.');
                }

                if ($wallet->balance < $totalCharge) {
                    throw new \Exception('Insufficient wallet balance. Total required: ' . number_format($totalCharge, 2));
                }

                $oldBalance = $wallet->balance;
                $newBalance = $oldBalance - $totalCharge;

                // 6. Create Transaction Record (Pending)
                $transactionRef = 'WDL' . strtoupper(Str::random(12));
                $performedBy = trim($user->first_name . ' ' . ($user->middle_name ?? '') . ' ' . $user->last_name);

                $transaction = Transaction::create([
                    'transaction_ref' => $transactionRef,
                    'user_id' => $user->id,
                    'amount' => $request->amount,
                    'fee' => $fee,
                    'net_amount' => $totalCharge,
                    'description' => "Withdrawal to {$request->account_name} ({$request->account_no})",
                    'type' => 'debit',
                    'status' => 'pending',
                    'performed_by' => $performedBy,
                    'metadata' => [
                        'service' => 'withdrawal',
                        'bank_code' => $request->bank_code,
                        'account_no' => $request->account_no,
                        'account_name' => $request->account_name,
                        'user_role' => $user->role,
                        'price_details' => [
                            'amount' => $request->amount,
                            'fee' => $fee,
                            'total' => $totalCharge,
                        ],
                    ],
                ]);

                // 7. Create Report Record (Pending)
                $bankName = Bank::where('bank_code', $request->bank_code)->value('bank_name') ?? 'Bank';

                $report = \App\Models\Report::create([
                    'user_id' => $user->id,
                    'phone_number' => $request->account_no,
                    'account_number' => $request->account_no,
                    'account_name' => $request->account_name,
                    'bank_code' => $request->bank_code,
                    'bank_name' => $bankName,
                    'network' => 'Withdrawal',
                    'ref' => $transactionRef,
                    'amount' => $totalCharge,
                    'status' => 'pending',
                    'type' => 'withdrawal',
                    'description' => "Withdrawal to {$request->account_name} ({$request->account_no})",
                    'old_balance' => $oldBalance,
                    'new_balance' => $newBalance,
                    'service_id' => $service->id,
                ]);

                // 8. Debit Wallet
                $wallet->balance = $newBalance;
                $wallet->save();

                DB::commit(); // SAFELY COMMIT FUNDS DEDUCTION BEFORE API CALL
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Withdrawal DB Initialization failed: ' . $e->getMessage());
                return back()->with('error', 'Withdrawal initialization failed: ' . $e->getMessage());
            }

            // Phase 2: Execute API Call (No DB Lock Held)
            try {
                // 9. Execute Payout via PalmPay
                $payoutResponse = $this->palmPay->transfer([
                    'orderId' => $transactionRef,
                    'payeeName' => $request->account_name,
                    'payeeBankCode' => $request->bank_code,
                    'payeeBankAccNo' => $request->account_no,
                    'payeePhoneNo' => $user->phone_number ?? '0000000000',
                    'amount' => (int) ($request->amount * 100), // Convert to unit (e.g., kobo for NGN)
                    'currency' => 'NGN',
                    'notifyUrl' => url('/api/palmpay/webhook'),
                    'remark' => 'Withdrawal from ' . config('app.name'),
                ]);

                // 10. Finalize Logic
                $meta = $transaction->metadata;
                $meta['api_response'] = $payoutResponse;
                $transaction->metadata = $meta;

                if (isset($payoutResponse['respCode']) && $payoutResponse['respCode'] === '00000000') {
                    // Success - Finalize completion
                    $transaction->status = 'completed';
                    $transaction->save();

                    $report->status = 'completed';
                    $report->save();

                    return view('thankyou2', [
                        'transaction' => $transaction,
                        'sender' => $user,
                        'receiver_name' => $request->account_name . ' (' . $bankName . ')',
                        'service_name' => 'Wallet Withdrawal',
                        'amount' => $request->amount,
                        'date' => now()
                    ]);
                } else {
                    // EXPLICIT API REJECTION - We must refund the user!
                    $transaction->status = 'failed';
                    $transaction->save();

                    $report->status = 'failed';
                    $report->save();

                    // Refund the wallet securely
                    DB::transaction(function () use ($user, $totalCharge) {
                        $walletToRefund = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                        if ($walletToRefund) {
                            $walletToRefund->balance += $totalCharge;
                            $walletToRefund->save();
                        }
                    });

                    $errorMsg = $payoutResponse['respMsg'] ?? 'PalmPay rejected the transaction.';
                    Log::error('PalmPay Withdrawal Rejected: ' . $errorMsg);
                    return back()->with('error', 'Withdrawal Rejected: ' . $errorMsg . ' (Funds refunded).');
                }

            } catch (\Exception $e) {
                // TIMEOUT OR NETWORK ERROR OR FATAL API EXCEPTION!
                // Do NOT refund the user automatically. PalmPay might still be processing it.
                // Leave transaction as 'pending'.
                Log::error('Withdrawal API Timeout/Exception (Not Refunded Automatically): ' . $e->getMessage());

                // Note: A background script or webhook should eventually verify and resolve this 'pending' transaction.
                return back()->with('success', 'Your withdrawal is being processed. We are awaiting final confirmation from the bank. Please check your dashboard later.');
            }

        } finally {
            // Unlock immediately after transaction processing completes
            if (isset($lock)) {
                $lock->release();
            }
        }
    }
}

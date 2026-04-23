<?php

namespace App\Http\Controllers\Action;

use App\Helpers\RequestIdHelper;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\SmeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Traits\ActiveUsers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;


class SmeDataController extends Controller
{
    use ActiveUsers;

    // API Configuration - matching DataController
    private function getApiBaseUrl()
    {
        return env('SME_ENDPOINT', 'https://datastationapi.com/api/data/');
    }

    private function getApiToken()
    {
        return env('AUTH_TOKEN');
    }

    /**
     * Show SME Data Purchase Page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'status' => 'active']
        );

        $networks = SmeData::select('network')->distinct()->get();

        // Fetch recent SME transactions for the sideboard
        $recentPurchases = Transaction::where('user_id', $user->id)
            ->where('description', 'LIKE', '%SME Data%')
            ->latest()
            ->take(15)
            ->get();

        // Fetch reliable SME plans (those with 0 recent failures)
        $reliablePlans = SmeData::where('status', 'enabled')
            ->where('failure_count', 0)
            ->latest()
            ->take(6)
            ->get();

        // Fallback for reliable plans if none recently found
        if ($reliablePlans->isEmpty()) {
            $reliablePlans = SmeData::where('status', 'enabled')->take(6)->get();
        }

        return view('utilities.buy-sme-data', compact(
            'user', 
            'wallet', 
            'networks',
            'recentPurchases',
            'reliablePlans'
        ));
    }

    /**
     * Fetch Data Types for a Network
     */
    public function fetchDataType(Request $request)
    {
        $network = $request->id;
        $types = SmeData::where('network', $network)
            ->select('plan_type')
            ->distinct()
            ->get();
        return response()->json($types);
    }

    /**
     * Fetch Data Plans for a Network and Type
     */
    public function fetchDataPlan(Request $request)
    {
        $network = $request->id;
        $type = $request->type;
        $plans = SmeData::where('network', $network)
            ->where('plan_type', $type)
            ->where('status', 'enabled')
            ->get();
        return response()->json($plans);
    }

    /**
     * Fetch Plan Price
     */
    public function fetchSmeBundlePrice(Request $request)
    {
        $planId = $request->id;
        $plan = SmeData::where('data_id', $planId)->first();
        
        if (!$plan) {
            return response()->json("0.00");
        }

        $user = Auth::user();
        $finalPrice = $plan->calculatePriceForRole($user->role ?? 'user');

        return response()->json(number_format((float)$finalPrice, 2));
    }

    /**
     * Buy SME Data Bundle
     */
    public function buySMEdata(Request $request)
    {
        $request->validate([
            'network'  => 'required|string|in:MTN,AIRTEL,GLO,9MOBILE',
            'type'     => 'required|string',
            'plan'     => 'required|string',
            'mobileno' => 'required|numeric|digits:11',
            'pin'      => 'required|numeric|digits:5'
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }

        // Backend Idempotency/Double-Click Prevention
        $lockKey = 'sme_purchase_lock_' . $user->id;
        $lock = Cache::lock($lockKey, 30); // 30-second lock

        if (!$lock->get()) {
            return back()->with('error', 'A transaction is already in progress. Please wait a moment.');
        }

        try {
            // Verify Transaction PIN
            if (!Hash::check($request->pin, $user->pin)) {
                $lock->release();
                return back()->with('error', 'Invalid transaction PIN.');
            }

            // status check for user account
            if (($user->status ?? 'inactive') !== 'active') {
                $lock->release();
                return redirect()->back()->with('error', "Your account is currently " . ($user->status ?? 'inactive') . ". Access denied.");
            }

            $requestId = RequestIdHelper::generateRequestId();
            $mobileno = $request->mobileno;
            $planId = $request->plan;
            
            $plan = SmeData::where('data_id', $planId)
                ->where('network', strtoupper($request->network))
                ->where('status', 'enabled')
                ->first();

            if (!$plan) {
                $lock->release();
                return back()->with('error', 'Invalid or disabled data plan selected.');
            }

            // Calculate Final Price (SmeData Amount + Service Field Fees)
            $payableAmount = $plan->calculatePriceForRole($user->role ?? 'user');
            $description = "{$plan->size} {$plan->plan_type} for {$mobileno} ({$plan->network})";

            // Check Wallet Balance
            $wallet = Wallet::where('user_id', $user->id)->first();
            if (!$wallet || $wallet->balance < $payableAmount) {
                $lock->release();
                return redirect()->back()->with('error', 'Insufficient wallet balance! You need ₦' . number_format($payableAmount, 2));
            }

            if (($wallet->status ?? 'inactive') !== 'active') {
                $lock->release();
                return redirect()->back()->with('error', 'Your wallet is not active. Please contact support.');
            }

            DB::beginTransaction();

            try {
                // 4. Create Preliminary Record & Charge Wallet
                $wallet->decrement('balance', $payableAmount);

                // Create Transaction (Pending)
                $transaction = Transaction::create([
                    'transaction_ref' => $requestId,
                    'user_id'         => $user->id,
                    'amount'          => $payableAmount,
                    'description'     => "SME Data purchase: " . $description,
                    'type'            => 'debit',
                    'status'          => 'pending',
                    'metadata'        => json_encode([
                        'phone'        => $mobileno,
                        'network'      => $plan->network,
                        'plan_type'    => $plan->plan_type,
                        'data_id'      => $plan->data_id,
                        'request_id'   => $requestId
                    ]),
                    'performed_by'    => $user->first_name . ' ' . $user->last_name,
                    'approved_by'     => $user->id,
                ]);

                // Upstream API Call (DataStation)
                $response = $this->callDataStation($requestId, $plan, $mobileno);

                if (!$response['success']) {
                    // API Failed - REFUND
                    $wallet->increment('balance', $payableAmount);
                    $transaction->update([
                        'status'   => 'failed',
                        'metadata' => json_encode(array_merge(json_decode($transaction->metadata, true), [
                            'api_error' => $response['message'] ?? 'Unknown API Error'
                        ]))
                    ]);
                    
                    // Health Tracking & Auto-deactivation
                    $errorMessage = $response['message'] ?? '';
                    $terminalErrors = [
                        'out of stock',
                        'service unavailable',
                        'network down',
                        'invalid plan',
                        'currently not available'
                    ];

                    $isTerminal = false;
                    foreach ($terminalErrors as $term) {
                        if (str_contains(strtolower($errorMessage), $term)) {
                            $isTerminal = true;
                            break;
                        }
                    }

                    $plan->increment('failure_count');
                    $plan->update(['last_failure_at' => now()]);

                    if ($isTerminal || $plan->failure_count >= 5) {
                        $plan->update(['status' => 'disabled']);
                        $reason = $isTerminal ? "Terminal Error: $errorMessage" : "Threshold reached (5 failures)";
                        Log::warning("SME Data plan [{$plan->data_id}] ({$plan->network} {$plan->size}) deactivated. Reason: {$reason}");
                    }

                    DB::commit();
                    $lock->release();
                    return redirect()->back()->with('error', $response['message'] ?? 'Data purchase failed. Please try again later.');
                }

                // API Success - Finalize
                $plan->update(['failure_count' => 0]); // Reset on success
                $transactionRef = $response['transaction_ref'] ?? $requestId;
                $apiData = $response['data'] ?? [];

                $transaction->update([
                    'status'          => 'completed',
                    'transaction_ref' => $transactionRef,
                    'metadata'        => json_encode(array_merge(json_decode($transaction->metadata, true), [
                        'api_response' => $apiData
                    ]))
                ]);

                DB::commit();
                $lock->release();

                return redirect()->route('thankyou')->with([
                    'success'  => 'Data purchase successful!',
                    'ref'      => $transactionRef,
                    'mobile'   => $mobileno,
                    'network'  => $plan->network . ' Data',
                    'amount'   => $payableAmount,
                    'paid'     => $payableAmount,
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                $lock->release();
                Log::error('SME Data Purchase Exception: ' . $e->getMessage());
                return back()->with('error', 'Something went wrong. Please try again.');
            }
        } catch (\Exception $e) {
            if (isset($lock)) $lock->release();
            Log::error('SME Data Outer Exception: ' . $e->getMessage());
            return back()->with('error', 'An error occurred. Please try again.');
        }
    }


    /**
     * Call DataStation API for purchase
     */
    private function callDataStation($requestId, $plan, $mobileNumber)
    {
        $networkMap = [
            'MTN'      => 1,
            'GLO'      => 2,
            '9MOBILE'  => 3,
            'AIRTEL'   => 4,
            'ETISALAT' => 3,
        ];

        $networkId = $networkMap[strtoupper($plan->network)] ?? 1;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->getApiToken(),
                'Content-Type'  => 'application/json',
            ])->post($this->getApiBaseUrl(), [
                'network'       => $networkId,
                'mobile_number' => $mobileNumber,
                'plan'          => (int)$plan->data_id,
                'Ported_number' => false,
            ]);

            $data = $response->json();
            Log::info('DataStation API Response', ['response' => $data]);

            if ($response->successful() && isset($data['Status']) && strtolower($data['Status']) === 'successful') {
                return [
                    'success'         => true,
                    'data'            => $data,
                    'transaction_ref' => $data['id'] ?? $requestId
                ];
            }

            // Extract error message from DataStation standard format
            $errorMessage = 'Purchase failed at upstream provider.';
            if (isset($data['error']) && is_array($data['error']) && !empty($data['error'])) {
                $errorMessage = $data['error'][0];
            } elseif (isset($data['msg'])) {
                $errorMessage = $data['msg'];
            }

            return [
                'success' => false,
                'message' => $errorMessage,
            ];

        } catch (\Exception $e) {
            Log::error('DataStation API Connection Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Could not connect to data provider. Please try again later.',
            ];
        }
    }
}

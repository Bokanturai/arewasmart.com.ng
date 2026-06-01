<?php

namespace App\Http\Controllers\Action;

use App\Helpers\RequestIdHelper;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Traits\ActiveUsers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DataController extends Controller
{
    use ActiveUsers;

    protected $loginUserId;

     public function __construct()
    {
        $this->loginUserId = Auth::id();
    }

    /**
     * Show Data Services & Price Lists
     */
    public function data(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'status' => 'active']
        );

        try {
            // Fetch recent data purchases for the sideboard
            $recentPurchases = \App\Models\Report::where('user_id', $user->id)
                ->where('type', 'data')
                ->latest()
                ->take(15)
                ->get();

            // Fetch reliable plans (success rate > 90% or recently successful)
            $reliablePlans = DB::table('data_variations')
                ->where('status', 'enabled')
                ->whereIn('variation_code', function($query) {
                    $query->select('ref') // This is a placeholder logic
                        ->from('report')
                        ->where('type', 'data')
                        ->where('status', 'successful')
                        ->where('created_at', '>=', now()->subDays(1));
                })
                ->take(10)
                ->get();
            
            // If none found by logic, just show some popular ones
            if ($reliablePlans->isEmpty()) {
                $reliablePlans = DB::table('data_variations')
                    ->where('status', 'enabled')
                    ->take(5)
                    ->get();
            }

            return view('utilities.buy-data', compact(
                'wallet', 'recentPurchases', 'reliablePlans'
            ));
        } catch (\Exception $e) {
            Log::error("Error loading data services: " . $e->getMessage());
            return back()->with('error', 'Unable to load data services. Please try again.');
        }
    }

    /**
     * Verify transaction PIN
     */
    public function verifyPin(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['valid' => false, 'message' => 'Unauthorized']);
        }

        $isValid = Hash::check($request->pin, $user->pin);
        return response()->json(['valid' => $isValid]);
    }

    /**
     * Buy Data Bundle
     */
    public function buydata(Request $request)
    {
        $request->validate([
            'network'  => 'required|string',
            'mobileno' => 'required|numeric|digits:11',
            'bundle'   => 'required|string',
            'pin'      => 'required|numeric|digits:5'
        ]);

        $user = Auth::user();

        // Verify Transaction PIN
        if (!\Illuminate\Support\Facades\Hash::check($request->pin, $user->pin)) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Invalid transaction PIN.'], 403);
            }
            return back()->with('error', 'Invalid transaction PIN.');
        }
        
        // Backend Double-Click/Idempotency Prevention
        $lockKey = 'data_purchase_lock_' . $user->id;
        $lock = Cache::lock($lockKey, 30); // 30-second lock

        if (!$lock->get()) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'A transaction is already in progress. Please wait a moment.'], 429);
            }
            return back()->with('error', 'A transaction is already in progress. Please wait a moment.');
        }

        $requestId = RequestIdHelper::generateRequestId();
        $user      = Auth::user();

        // 0. Preliminary Account Status Check
        if (($user->status ?? 'inactive') !== 'active') {
             $msg = "Your account is currently " . ($user->status ?? 'inactive') . ". Access denied.";
             if ($request->wantsJson()) {
                 return response()->json(['status' => 'error', 'message' => $msg], 403);
             }
             return redirect()->back()->with('error', $msg);
        }

        $wallet    = Wallet::where('user_id', $user->id)->first();

        if (!$wallet) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Wallet not found.'], 404);
            }
            return back()->with('error', 'Wallet not found.');
        }

        // Fetch Bundle Details with network compatibility check
        $variation = DB::table('data_variations')
            ->where('variation_code', $request->bundle)
            ->where('service_id', 'LIKE', $request->network . '%')
            ->where('status', 'enabled')
            ->first();

        if (!$variation) {
             if ($request->wantsJson()) {
                 return response()->json(['status' => 'error', 'message' => 'Invalid data bundle selected for the chosen network.'], 400);
             }
             return back()->with('error', 'Invalid data bundle selected for the chosen network.');
        }
        
        $amount = $variation->variation_amount; // Face value / API price
        $description = $variation->name ?? 'Data Bundle';
        $networkKey = $request->network; // e.g., mtn-data

        // --- Discount Logic Start ---
        // Mapping network keys to specific field codes provided by USER
        $fieldCodeMap = [
            'mtn-data'      => '104',
            'airtel-data'   => '105',
            'glo-data'      => '106',
            'etisalat-data' => '107',
        ];

        $targetFieldCode = $fieldCodeMap[$networkKey] ?? $networkKey;

        // 1. Find the Service (e.g., Data)
        $service = \App\Models\Service::where('name', 'Data')->first();
        if (!$service) {
             $service = \App\Models\Service::firstOrCreate(['name' => 'Data'], ['status' => 'active']);
        }

        // 2. Find the specific Network Field using field_code
        $serviceField = \App\Models\ServiceField::where('service_id', $service->id)
            ->where('field_code', $targetFieldCode)
            ->first();

        // Fallback for older searches if field_code mapping is not set up in DB yet
        if (!$serviceField) {
            $serviceField = \App\Models\ServiceField::where('service_id', $service->id)
                ->where(function($q) use ($networkKey) {
                    $q->where('field_name', 'LIKE', "%{$networkKey}%")
                      ->orWhere('field_code', 'LIKE', "%{$networkKey}%");
                })->first();
        }

        // 3. Calculate Discount
        $discountPercentage = 0;
        if ($serviceField) {
            $userRole = $user->role ?? 'personal';
            $servicePrice = \App\Models\ServicePrice::where('service_fields_id', $serviceField->id)
                ->where('user_type', $userRole)
                ->first();

            if ($servicePrice) {
                $discountPercentage = $servicePrice->price; // e.g., 10 for 10%
            } else {
                $discountPercentage = $serviceField->base_price ?? 0;
            }
        }

        // Apply percentage discount
        $discountAmount = ($amount * $discountPercentage) / 100;
        $payableAmount = $amount - $discountAmount;
        // --- Discount Logic End ---

        DB::beginTransaction();

        try {
            // 4. Fetch Wallet with Row Locking to prevent race conditions
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            
            if (!$wallet) {
                DB::rollBack();
                if ($request->wantsJson()) {
                    return response()->json(['status' => 'error', 'message' => 'Wallet not found.'], 404);
                }
                return back()->with('error', 'Wallet not found.');
            }

            if ($wallet->balance < $payableAmount) {
                DB::rollBack();
                $msg = 'Insufficient wallet balance! You need ₦' . number_format($payableAmount, 2);
                if ($request->wantsJson()) {
                    return response()->json(['status' => 'error', 'message' => $msg], 402);
                }
                return back()->with('error', $msg);
            }

            // Wallet Status Check
            if (($wallet->status ?? 'inactive') !== 'active') {
                DB::rollBack();
                $msg = 'Your wallet is not active. Please contact support.';
                if ($request->wantsJson()) {
                    return response()->json(['status' => 'error', 'message' => $msg], 403);
                }
                return back()->with('error', $msg);
            }

            $oldBalance = $wallet->balance;
            $wallet->decrement('balance', $payableAmount);
            $newBalance = $wallet->balance;

            $payer_name = $user->first_name . ' ' . $user->last_name;

            // Create Transaction Record (Pending)
            $transaction = Transaction::create([
                'transaction_ref' => $requestId,
                'user_id'         => $user->id,
                'amount'          => $payableAmount,
                'description'     => "Data purchase of {$description} for {$request->mobileno}",
                'type'            => 'debit',
                'status'          => 'pending',
                'performed_by'    => $payer_name,
                'approved_by'     => $user->id,
            ]);

            // Create Report Record (Pending)
            $report = \App\Models\Report::create([
                'user_id'      => $user->id,
                'phone_number' => $request->mobileno,
                'network'      => $networkKey,
                'ref'          => $requestId,
                'amount'       => $amount,
                'status'       => 'pending',
                'type'         => 'data',
                'description'  => "Data purchase: {$description} [{$request->bundle}]",
                'old_balance'  => $oldBalance,
                'new_balance'  => $newBalance,
                'service_id'   => $serviceField ? $serviceField->id : null,
            ]);

            // 5. Make Payment via VTPass
            $response = Http::withHeaders([
                'api-key'    => config('services.vtpass.api_key'),
                'secret-key' => config('services.vtpass.secret_key'),
            ])->post(config('services.vtpass.payment_url'), [
                'request_id'     => $requestId,
                'serviceID'      => $variation->service_id,
                'billersCode'    => config('services.vtpass.biller_code'),
                'variation_code' => $request->bundle,
                'phone'          => $request->mobileno,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Check success codes
                $successCodes = ['0','00','000','200'];
                $isSuccessful = (isset($data['code']) && in_array((string)$data['code'], $successCodes)) ||
                                (isset($data['status']) && strtolower($data['status']) === 'success');

                if ($isSuccessful) {
                    // Finalize Records
                    $transaction->update([
                        'status'   => 'completed',
                        'metadata' => json_encode([
                            'phone'        => $request->mobileno,
                            'network'      => $networkKey,
                            'original_amt' => $amount,
                            'discount'     => $discountAmount,
                            'api_response' => $data,
                        ]),
                    ]);

                    $report->update(['status' => 'successful']);

                    DB::commit();

                    if ($request->wantsJson()) {
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Data purchase successful!',
                            'data' => [
                                'ref' => $requestId,
                                'mobile' => $request->mobileno,
                                'amount' => $amount,
                                'paid' => $payableAmount,
                                'network' => $networkKey
                            ]
                        ]);
                    }

                    return redirect()->route('thankyou')->with([
                        'success' => 'Data purchase successful!',
                        'ref'     => $requestId,
                        'mobile'  => $request->mobileno,
                        'amount'  => $amount,
                        'paid'    => $payableAmount,
                        'network' => $networkKey
                    ]);
                }

                Log::error('Data API Response Error', ['response' => $data]);
                $errorMsg = $data['message'] ?? 'Data purchase failed. Please try again.';
                
                // If it's a known failure code from API, we refund.
                $wallet->increment('balance', $payableAmount);
                $transaction->update(['status' => 'failed']);
                $report->update([
                    'status'      => 'failed',
                    'description' => "Failed: " . (isset($data['message']) ? $data['message'] : 'API Error'),
                ]);
            } else {
                // HTTP Connection Failure / Timeout - DANGEROUS TO REFUND IMMEDIATELY
                Log::error('Data API HTTP Error', ['status' => $response->status(), 'body' => $response->body()]);
                $errorMsg = 'Service is temporarily unresponsive. Your transaction is pending verification.';
                
                // We keep it as "pending" for admin to verify
                $transaction->update(['status' => 'pending_verification']);
                $report->update([
                    'status'      => 'pending',
                    'description' => 'Connection timeout. Pending manual verification.',
                ]);
            }

            // Check if this plan has failed 5 times today for this user (or globally)
            $failCount = \App\Models\Report::where('type', 'data')
                ->where('description', 'LIKE', "%[{$request->bundle}]%")
                ->where('status', 'failed')
                ->whereDate('created_at', Carbon::today())
                ->count();

            if ($failCount >= 5) {
                DB::table('data_variations')
                    ->where('variation_code', $request->bundle)
                    ->update(['status' => 'disabled']);
                
                Log::warning("Data plan {$request->bundle} deactivated due to 5 failures today.");
            }

            DB::commit();
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMsg], 400);
            }
            return back()->with('error', $errorMsg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Data purchase exception: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong. Please try again.'], 500);
            }
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Fetch Bundles by Service ID
     */
    public function fetchBundles(Request $request)
    {
        try {
            $bundles = DB::table('data_variations')
                ->select(['name', 'variation_code'])
                ->where('service_id', 'LIKE', $request->id . '%')
                ->where('status', 'enabled')
                ->get();

            return response()->json($bundles);
        } catch (\Exception $e) {
            Log::error('Fetch bundles error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Fetch Bundle Price
     */
    public function fetchBundlePrice(Request $request)
    {
        try {
            $price = DB::table('data_variations')
                ->where('variation_code', $request->id)
                ->value('variation_amount');

            return response()->json(number_format((float)$price, 2));
        } catch (\Exception $e) {
            Log::error('Fetch bundle price error: ' . $e->getMessage());
            return response()->json("0.00", 500);
        }
    }
}

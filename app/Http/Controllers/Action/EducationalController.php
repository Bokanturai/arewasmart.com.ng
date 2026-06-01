<?php

namespace App\Http\Controllers\Action;

use App\Helpers\RequestIdHelper;
use App\Http\Controllers\Controller;
use App\Mail\EducationalPurchaseNotification;
use App\Models\Report;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Traits\ActiveUsers;
use Carbon\Carbon;

class EducationalController extends Controller
{
    use ActiveUsers;

    public function __construct()
    {
        $this->middleware('throttle:6,1')->only(['buypin', 'buyJamb']);
    }


    // ─────────────────────────────────────────────
    //  PAGE VIEWS
    // ─────────────────────────────────────────────

    public function pin(Request $request)
    {
        // 1. Authenticate user
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // 2. Check account status early
        if (($user->status ?? 'inactive') !== 'active') {
            return redirect()->route('dashboard')->with('error', 'Your account is currently ' . ($user->status ?? 'inactive') . '. Please contact support.');
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'status' => 'active']
        );

        $pins = DB::table('data_variations')
            ->whereIn('service_id', ['waec', 'waec-registration'])
            ->get();

        $history = Report::where('user_id', $user->id)
            ->where('type', 'education')
            ->latest()
            ->paginate(10);

        return view('utilities.buy-educational-pin')->with(compact('pins', 'wallet', 'history'));
    }

    /**
     * Show JAMB Purchase Page
     */
    public function jamb(Request $request)
    {
        // 1. Authenticate user
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // 2. Check account status early
        if (($user->status ?? 'inactive') !== 'active') {
            return redirect()->route('dashboard')->with('error', 'Your account is currently ' . ($user->status ?? 'inactive') . '. Please contact support.');
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'status' => 'active']
        );

        $history = Report::where('user_id', $user->id)
            ->where('type', 'jamb')
            ->latest()
            ->paginate(10);

        $variations = DB::table('data_variations')->where('service_id', 'jamb')->get();

        return view('utilities.buy-jamb', compact('wallet', 'history', 'variations'));
    }

    /**
     * Show Transaction Receipt
     */
    public function receipt($transactionRef)
    {
        $user = Auth::user();
        $transaction = Transaction::where('transaction_ref', $transactionRef)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $metadata = is_string($transaction->metadata) 
            ? json_decode($transaction->metadata, true) 
            : $transaction->metadata;

        return view('thankyou')->with([
            'success' => 'Transaction Receipt',
            'ref'     => $transaction->transaction_ref,
            'mobile'  => $metadata['phone'] ?? $metadata['profile_id'] ?? 'N/A',
            'amount'  => $transaction->amount,
            'token'   => $metadata['purchased_code'] ?? null,
            'network' => $metadata['service'] ?? $metadata['service_type'] ?? 'N/A',
        ]);
    }

    // ─────────────────────────────────────────────
    //  UTILITY ENDPOINTS
    // ─────────────────────────────────────────────

    /**
     * Verify Transaction PIN
     */
    public function verifyPin(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['valid' => false, 'message' => 'Unauthorized']);
        }

        return response()->json(['valid' => Hash::check($request->pin, $user->pin)]);
    }



    // ─────────────────────────────────────────────
    //  BUY EDUCATIONAL PIN  (WAEC / WAEC Registration)
    // ─────────────────────────────────────────────

    /**
     * Buy Educational Pin
     *
     * Standard flow:
     *  1. Authenticate user
     *  2. Validate request
     *  3. Check service active
     *  4. Calculate price
     *  5. Lock wallet row
     *  6. Check wallet active
     *  7. Check balance
     *  8-10. (inside DB transaction) Debit wallet → Call API → On success: create transaction + report → commit
     */
    public function buypin(Request $request)
    {
        // ── 1. Authenticate user ─────────────────
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        // ── 2. Validate request ──────────────────
        $request->validate([
            'service'  => ['required', 'string', 'in:waec-registration,waec'],
            'type'     => ['required', 'string'],
            'mobileno' => ['required', 'numeric', 'digits:11'],
            'pin'      => ['required', 'string', 'digits:5'],
        ]);

        // ── 2.1 Verify PIN ───────────────────────
        if (!Hash::check($request->pin, $user->pin)) {
            return redirect()->back()->with('error', 'Incorrect transaction PIN. Please try again.');
        }

        // ── 3. Check service active (user account) ─
        if (($user->status ?? 'inactive') !== 'active') {
            return redirect()->back()->with(
                'error',
                'Your account is currently ' . ($user->status ?? 'inactive') . '. Access denied.'
            );
        }

        // ── 4. Calculate price ───────────────────
        $variation = DB::table('data_variations')
            ->where('variation_code', $request->type)
            ->first();

        if (!$variation) {
            return redirect()->back()->with('error', 'Invalid variation selected.');
        }

        $fee         = $variation->variation_amount;
        $description = $variation->name ?? $request->service;

        // ── 5 & 6. Lock wallet row + Check wallet active ─
        $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

        if (!$wallet) {
            return redirect()->back()->with('error', 'Wallet not found. Please contact support.');
        }

        if (($wallet->status ?? 'inactive') !== 'active') {
            return redirect()->back()->with('error', 'Your wallet is inactive. Please contact support.');
        }

        // ── 7. Check balance ─────────────────────
        if ($wallet->balance < $fee) {
            return redirect()->back()->with('error', 'Insufficient wallet balance.');
        }

        $requestId  = RequestIdHelper::generateRequestId();
        $payerName  = trim($user->first_name . ' ' . $user->last_name);
        $oldBalance = $wallet->balance;

        // ── 8-10. DB Transaction ─────────────────
        DB::beginTransaction();

        try {
            // ── 8. Debit wallet ──────────────────
            $wallet->decrement('balance', $fee);
            $newBalance = $wallet->fresh()->balance;

            // ── 9. Call VTpass API (with timeout) ───────────────
            $startTime = microtime(true);
            $response = Http::withHeaders([
                'api-key'    => config('services.vtpass.api_key'),
                'secret-key' => config('services.vtpass.secret_key'),
            ])->timeout(30)->post(config('services.vtpass.payment_url'), [
                'request_id'     => $requestId,
                'serviceID'      => $request->service,
                'billersCode'    => '0123456789',
                'variation_code' => $request->type,
                'phone'          => $request->mobileno,
            ]);
            $elapsedTime = round(microtime(true) - $startTime, 2);
            Log::info("VTPass API (waec) took {$elapsedTime}s", ['request_id' => $requestId]);

            if (!$response->successful()) {
                // HTTP-level failure – rollback and abort without recording anything
                DB::rollBack();
                Log::error('Educational Pin HTTP Error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return back()->with('error', 'Service temporarily unavailable. Please try again.');
            }

            $result       = $response->json();
            $successCodes = ['0', '00', '000', '200'];
            $isSuccessful = (isset($result['code']) && in_array((string) $result['code'], $successCodes))
                         || (isset($result['status']) && strtolower($result['status']) === 'success');

            if (!$isSuccessful) {
                // API returned a failure code – rollback and abort without recording anything
                DB::rollBack();
                Log::error('Educational Pin API Error', ['response' => $result]);
                $errorMsg = $result['response_description'] ?? 'Purchase failed. Please try again.';
                return back()->with('error', $errorMsg);
            }

            // ── 10. Create service records (success only) ──
            $purchasedCode = $result['purchased_code']
                ?? $result['cards'][0]['Pin']
                ?? null;

            // Extract serial number if available from API response
            $serialNumber = $result['cards'][0]['Serial']
                ?? $result['cards'][0]['SerialNumber']
                ?? $result['serial_number']
                ?? $result['serialNumber']
                ?? null;

            $finalToken      = $purchasedCode ?? 'Check History';
            $transDescription = "Educational pin purchase ({$description}) - PIN: {$finalToken}";

            // Create Transaction (success)
            Transaction::create([
                'transaction_ref' => $requestId,
                'user_id'         => $user->id,
                'amount'          => $fee,
                'description'     => $transDescription,
                'type'            => 'debit',
                'status'          => 'completed',
                'performed_by'    => $payerName,
                'approved_by'     => $user->id,
                'metadata'        => json_encode([
                    'phone'          => $request->mobileno,
                    'service'        => $request->service,
                    'purchased_code' => $finalToken,
                    'serial_number'  => $serialNumber,
                    'api_response'   => $result,
                ]),
            ]);

            // Create Report (success)
            Report::create([
                'user_id'      => $user->id,
                'phone_number' => $request->mobileno,
                'network'      => $request->service,
                'ref'          => $requestId,
                'amount'       => $fee,
                'status'       => 'successful',
                'type'         => 'education',
                'description'  => $transDescription,
                'old_balance'  => $oldBalance,
                'new_balance'  => $newBalance,
                'service_id'   => $variation->id ?? null,
            ]);

            DB::commit();

            // Send email notification (non-blocking, after commit)
            $targetEmail = $request->email ?: $user->email;
            if ($targetEmail) {
                try {
                    Mail::to($targetEmail)->queue(new EducationalPurchaseNotification([
                        'customer_name'    => $user->name,
                        'pin'              => $finalToken,
                        'serial_number'    => $serialNumber,
                        'amount'           => $fee,
                        'reference'        => $requestId,
                        'service_type'     => $description,
                        'transaction_date' => now()->format('d M Y, h:i A'),
                    ]));
                } catch (\Exception $e) {
                    Log::error('WAEC Email Failed: ' . $e->getMessage());
                }
            }

            return redirect()->route('thankyou')->with([
                'success'     => 'Educational pin purchase successful!',
                'ref'         => $requestId,
                'mobile'      => $request->mobileno,
                'amount'      => $fee,
                'paid'        => $fee,
                'token'       => $finalToken,
                'serial'      => $serialNumber,
                'network'     => strtoupper($request->service),
                'serviceName' => 'Educational Pin (' . strtoupper($request->service) . ')',
                'date'        => now()->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Educational Pin Exception', ['error' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    // ─────────────────────────────────────────────
    //  JAMB
    // ─────────────────────────────────────────────

    /**
     * Verify JAMB Profile ID
     */
    public function verifyJamb(Request $request)
    {
        // 1. Authenticate user
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please log in first.']);
        }

        // 2. Validate request
        $request->validate([
            'service'    => 'required|string',
            'profile_id' => 'required|string',
        ]);

        // 3. Check account status
        if (($user->status ?? 'inactive') !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is currently ' . ($user->status ?? 'inactive') . '. Access denied.',
            ]);
        }

        try {
            $variationCode = $request->service;

            $variation = DB::table('data_variations')
                ->where('variation_code', $variationCode)
                ->first();

            if (!$variation) {
                Log::error('JAMB Verification: Variation not found', ['variation_code' => $variationCode]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JAMB service selected. Please refresh and try again.',
                ]);
            }

            $requestPayload = [
                'serviceID'   => 'jamb',
                'billersCode' => $request->profile_id,
                'type'        => $variationCode,
            ];

            Log::info('JAMB Verification Request', $requestPayload);

            $response = Http::withHeaders([
                'api-key'    => config('services.vtpass.api_key'),
                'secret-key' => config('services.vtpass.secret_key'),
            ])->timeout(30)->post(config('services.vtpass.base_url') . '/merchant-verify', $requestPayload);

            $data = $response->json();

            Log::info('JAMB Verification Response', [
                'status_code' => $response->status(),
                'response'    => $data,
            ]);

            if ($response->successful() && isset($data['code']) && $data['code'] == '000') {
                return response()->json([
                    'success'       => true,
                    'customer_name' => $data['content']['Customer_Name'] ?? 'Unknown',
                    'amount'        => $variation->variation_amount,
                ]);
            }

            $errorMessage = $data['response_description'] ?? $data['message'] ?? 'Invalid Profile ID';
            Log::error('JAMB Verification Failed', [
                'profile_id'    => $request->profile_id,
                'code'          => $data['code'] ?? 'N/A',
                'message'       => $errorMessage,
                'full_response' => $data,
            ]);

            return response()->json(['success' => false, 'message' => $errorMessage]);

        } catch (\Exception $e) {
            Log::error('JAMB Verification Exception', [
                'error'      => $e->getMessage(),
                'profile_id' => $request->profile_id ?? 'N/A',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during verification. Please try again.',
            ]);
        }
    }

    /**
     * Buy JAMB PIN
     *
     * Standard flow:
     *  1. Authenticate user
     *  2. Validate request
     *  3. Check service active
     *  4. Calculate price
     *  5. Lock wallet row
     *  6. Check wallet active
     *  7. Check balance
     *  8-10. (inside DB transaction) Debit wallet → Call API → On success: create transaction + report → commit
     */
    public function buyJamb(Request $request)
    {
        // ── 1. Authenticate user ─────────────────
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        // ── 2. Validate request ──────────────────
        $request->validate([
            'service'    => 'required|string',
            'profile_id' => 'required|string',
            'mobileno'   => 'required|numeric|digits:11',
            'email'      => 'nullable|email',
            'pin'        => 'required|string|digits:5',
        ]);

        // ── 2.1 Verify PIN ───────────────────────
        if (!Hash::check($request->pin, $user->pin)) {
            return redirect()->back()->with('error', 'Incorrect transaction PIN. Please try again.');
        }

        // ── 3. Check service active (user account) ─
        if (($user->status ?? 'inactive') !== 'active') {
            return redirect()->back()->with(
                'error',
                'Your account is currently ' . ($user->status ?? 'inactive') . '. Access denied.'
            );
        }

        // ── 4. Calculate price ───────────────────
        $variation = DB::table('data_variations')
            ->where('variation_code', $request->service)
            ->first();

        if (!$variation) {
            return redirect()->back()->with('error', 'Invalid JAMB service selected.');
        }

        $fee         = $variation->variation_amount;
        $description = $variation->name ?? 'JAMB';

        // ── 5 & 6. Lock wallet row + Check wallet active ─
        $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

        if (!$wallet) {
            return redirect()->back()->with('error', 'Wallet not found. Please contact support.');
        }

        if (($wallet->status ?? 'inactive') !== 'active') {
            return redirect()->back()->with('error', 'Your wallet is inactive. Please contact support.');
        }

        // ── 7. Check balance ─────────────────────
        if ($wallet->balance < $fee) {
            return redirect()->back()->with('error', 'Insufficient wallet balance.');
        }

        $requestId  = RequestIdHelper::generateRequestId();
        $payerName  = trim($user->first_name . ' ' . $user->last_name);
        $oldBalance = $wallet->balance;

        // ── 8-10. DB Transaction ─────────────────
        DB::beginTransaction();

        try {
            // ── 8. Debit wallet ──────────────────
            $wallet->decrement('balance', $fee);
            $newBalance = $wallet->fresh()->balance;

            // ── 9. Call VTpass API (with timeout) ───────────────
            $startTime = microtime(true);
            $response = Http::withHeaders([
                'api-key'    => config('services.vtpass.api_key'),
                'secret-key' => config('services.vtpass.secret_key'),
            ])->timeout(30)->post(config('services.vtpass.payment_url'), [
                'request_id'     => $requestId,
                'serviceID'      => 'jamb',
                'billersCode'    => $request->profile_id,
                'variation_code' => $request->service,
                'phone'          => $request->mobileno,
            ]);
            $elapsedTime = round(microtime(true) - $startTime, 2);
            Log::info("VTPass API (jamb) took {$elapsedTime}s", ['request_id' => $requestId]);

            if (!$response->successful()) {
                // HTTP-level failure – rollback and abort without recording anything
                DB::rollBack();
                Log::error('JAMB Purchase HTTP Error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return back()->with('error', 'Service temporarily unavailable. Please try again.');
            }

            $result       = $response->json();
            $successCodes = ['0', '00', '000', '200'];
            $isSuccessful = (isset($result['code']) && in_array((string) $result['code'], $successCodes))
                         || (isset($result['status']) && strtolower($result['status']) === 'success');

            if (!$isSuccessful) {
                // API returned a failure code – rollback and abort without recording anything
                DB::rollBack();
                Log::error('JAMB Purchase API Error', ['response' => $result]);
                $errorMsg = $result['response_description'] ?? 'Purchase failed. Please try again.';
                return back()->with('error', $errorMsg);
            }

            // ── 10. Create service records (success only) ──
            $purchasedCode = $result['Pin']
                ?? $result['purchased_code']
                ?? $result['content']['transactions']['Pin']
                ?? $result['cards'][0]['Pin']
                ?? null;

            // Extract serial number if available from API response
            $serialNumber = $result['cards'][0]['Serial']
                ?? $result['cards'][0]['SerialNumber']
                ?? $result['serial_number']
                ?? $result['serialNumber']
                ?? null;

            $finalToken      = $purchasedCode ?? 'Check History';
            $transDescription = "{$description} Purchase - Profile: {$request->profile_id} - PIN: {$finalToken}";

            // Create Transaction (success)
            Transaction::create([
                'transaction_ref' => $requestId,
                'user_id'         => $user->id,
                'amount'          => $fee,
                'description'     => $transDescription,
                'type'            => 'debit',
                'status'          => 'completed',
                'performed_by'    => $payerName,
                'approved_by'     => $user->id,
                'metadata'        => json_encode([
                    'profile_id'     => $request->profile_id,
                    'phone'          => $request->mobileno,
                    'service_type'   => $description,
                    'email'          => $request->email ?? null,
                    'purchased_code' => $finalToken,
                    'serial_number'  => $serialNumber,
                    'api_response'   => $result,
                ]),
            ]);

            // Create Report (success)
            Report::create([
                'user_id'      => $user->id,
                'phone_number' => $request->mobileno,
                'network'      => $request->service,
                'ref'          => $requestId,
                'amount'       => $fee,
                'status'       => 'successful',
                'type'         => 'jamb',
                'description'  => $transDescription,
                'old_balance'  => $oldBalance,
                'new_balance'  => $newBalance,
                'token'        => $finalToken,
            ]);

            DB::commit();

            // Send email notification (non-blocking, after commit)
            $targetEmail = $request->email ?: $user->email;
            if ($targetEmail) {
                try {
                    Mail::to($targetEmail)->queue(new EducationalPurchaseNotification([
                        'customer_name'    => $payerName,
                        'profile_id'       => $request->profile_id,
                        'pin'              => $finalToken,
                        'serial_number'    => $serialNumber,
                        'amount'           => $fee,
                        'reference'        => $requestId,
                        'service_type'     => $description,
                        'transaction_date' => now()->format('d M Y, h:i A'),
                    ]));
                } catch (\Exception $e) {
                    Log::error('JAMB Email Failed: ' . $e->getMessage());
                }
            }

            return redirect()->route('thankyou')->with([
                'success'     => 'JAMB PIN purchase successful!',
                'ref'         => $requestId,
                'mobile'      => $request->mobileno,
                'amount'      => $fee,
                'paid'        => $fee,
                'token'       => $finalToken,
                'serial'      => $serialNumber,
                'network'     => strtoupper($description),
                'serviceName' => $description . ' Purchase',
                'date'        => now()->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('JAMB Purchase Exception', ['error' => $e->getMessage()]);
            return back()->with('error', 'An error occurred during purchase. Please try again.');
        }
    }
}
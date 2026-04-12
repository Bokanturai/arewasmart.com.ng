<?php

namespace App\Http\Controllers\Action;

use App\Helpers\RequestIdHelper;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Service;
use App\Models\ServiceField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Traits\ActiveUsers;

class NecoNabtedController extends Controller
{
    use ActiveUsers;

    private function getApiBaseUrl()
    {
        return "https://datastationapi.com/api/epin/";
    }

    private function getApiToken()
    {
        return env('AUTH_TOKEN', '66f2e5c39ac8640f13cd888f161385b12f7e5');
    }

    /**
     * Display the NECO & NABTED purchase page.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if (($user->status ?? 'inactive') !== 'active') {
            return redirect()->route('dashboard')->with('error', 'Your account is currently ' . ($user->status ?? 'inactive') . '. Please contact support.');
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'status' => 'active']
        );

        $service = Service::where('name', 'NECO & NABTED')->first();
        $variations = $service ? $service->fields()->where('is_active', 1)->get() : collect();

        $fieldPrices = [];
        foreach ($variations as $variation) {
            $fieldPrices[$variation->field_code] = $variation->getPriceForUserType($user->role ?? 'user');
        }

        $history = Report::where('user_id', $user->id)
            ->whereIn('network', ['NECO', 'NABTED'])
            ->latest()
            ->paginate(10);

        return view('utilities.neco-nabted', compact('wallet', 'history', 'variations', 'user', 'fieldPrices'));
    }

    /**
     * Handle the purchase of NECO/NABTED pins.
     */
    public function purchase(Request $request)
    {
        $request->validate([
            'service'  => 'required|string', // This will be the field_code (N100 or N101)
            'mobileno' => 'required|numeric|digits:11',
            'pin'      => 'required|string|digits:5',
        ]);

        $user = Auth::user();
        if (!$user) {
            return back()->with('error', 'Please log in to continue.');
        }

        // Idempotency lock
        $lockKey = 'neco_nabted_lock_' . $user->id;
        $lock = Cache::lock($lockKey, 30);

        if (!$lock->get()) {
            return back()->with('error', 'A transaction is already in progress. Please wait.');
        }

        try {
            // Verify Transaction PIN
            if (!Hash::check($request->pin, $user->pin)) {
                $lock->release();
                return back()->with('error', 'Invalid transaction PIN.');
            }

            // User status check
            if (($user->status ?? 'inactive') !== 'active') {
                $lock->release();
                return back()->with('error', "Your account is currently " . ($user->status ?? 'inactive') . ".");
            }

            // Get Service Field and Calculate Price
            $field = ServiceField::where('field_code', $request->service)->first();
            if (!$field) {
                $lock->release();
                return back()->with('error', 'Invalid service selected.');
            }

            $payableAmount = (float)$field->getPriceForUserType($user->role ?? 'user');
            $examName = ($request->service == 'N100') ? 'neco' : 'nabteb';
            $description = strtoupper($examName) . " Pin Purchase for {$request->mobileno}";

            // Wallet Check
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet || $wallet->balance < $payableAmount) {
                $lock->release();
                return back()->with('error', 'Insufficient wallet balance. You need ₦' . number_format($payableAmount, 2));
            }

            if (($wallet->status ?? 'inactive') !== 'active') {
                $lock->release();
                return back()->with('error', 'Your wallet is inactive.');
            }

            $requestId = RequestIdHelper::generateRequestId();
            $oldBalance = $wallet->balance;

            DB::beginTransaction();

            try {
                // Debit Wallet
                $wallet->decrement('balance', $payableAmount);
                $newBalance = $wallet->fresh()->balance;

                // Call Datastation API
                $response = Http::withHeaders([
                    'Authorization' => 'Token ' . $this->getApiToken(),
                    'Content-Type'  => 'application/json',
                ])->timeout(45)->post($this->getApiBaseUrl(), [
                    'exam_name' => strtoupper($examName),
                    'quantity'  => 1,
                ]);

                $result = $response->json();
                Log::info('Datastation EPIN Response', ['response' => $result]);

                if (!$response->successful() || (isset($result['Status']) && strtolower($result['Status']) !== 'successful')) {
                    // API Failed - Rollback and Refund
                    DB::rollBack();
                    $lock->release();
                    
                    $errorMsg = $result['msg'] ?? $result['error'][0] ?? 'Provider error. Please try again later.';
                    return back()->with('error', $errorMsg);
                }

                // Success - Finalize
                $purchasedPin = $result['pins'] ?? 'Check History';
                
                // If 'pins' is empty but 'data' exists (and is a string), try parsing it
                if ($purchasedPin === 'Check History' && isset($result['data']) && is_string($result['data'])) {
                    $nestedData = json_decode($result['data'], true);
                    $purchasedPin = $nestedData['pin'] ?? 'Check History';
                }
                
                $finalDescription = $description . " - PIN: " . $purchasedPin;

                // Create Transaction
                Transaction::create([
                    'transaction_ref' => $requestId,
                    'user_id'         => $user->id,
                    'amount'          => $payableAmount,
                    'description'     => $finalDescription,
                    'type'            => 'debit',
                    'status'          => 'completed',
                    'performed_by'    => $user->first_name . ' ' . $user->last_name,
                    'approved_by'     => $user->id,
                    'metadata'        => json_encode([
                        'phone'        => $request->mobileno,
                        'exam_name'    => $examName,
                        'purchased_pin'=> $purchasedPin,
                        'api_response' => $result
                    ]),
                ]);

                // Create Report (success)
                Report::create([
                    'user_id'      => $user->id,
                    'phone_number' => $request->mobileno,
                    'network'      => strtoupper($examName),
                    'ref'          => $requestId,
                    'amount'       => $payableAmount,
                    'status'       => 'successful',
                    'type'         => 'education',
                    'description'  => $finalDescription,
                    'old_balance'  => $oldBalance,
                    'new_balance'  => $newBalance,
                    'service_id'   => $field->id,
                    'token'        => $purchasedPin,
                ]);

                DB::commit();
                $lock->release();

                return redirect()->route('thankyou')->with([
                    'success' => strtoupper($examName) . ' PIN purchase successful!',
                    'ref'     => $requestId,
                    'mobile'  => $request->mobileno,
                    'amount'  => $payableAmount,
                    'token'   => $purchasedPin,
                    'network' => strtoupper($examName),
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                $lock->release();
                Log::error('NECO/NABTED Purchase Exception: ' . $e->getMessage());
                return back()->with('error', 'Something went wrong. Please try again.');
            }

        } catch (\Exception $e) {
            if (isset($lock)) $lock->release();
            Log::error('NECO/NABTED Outer Exception: ' . $e->getMessage());
            return back()->with('error', 'An error occurred. Please try again.');
        }
    }
}

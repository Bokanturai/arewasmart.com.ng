<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\AgentService;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NinValidationController extends Controller
{
    public function index(Request $request)
    {
        $validationService = Service::where('name', 'Validation')->first();

        // Fetch fields for validation service
        $validationFields = $validationService ? $validationService->fields : collect();

        $services = collect();
        $user = Auth::user();
        $role = $user->role ?? 'user';

        foreach ($validationFields as $field) {
            $price = $field->prices()->where('user_type', $role)->value('price') ?? $field->base_price;
            $services->push([
                'id' => $field->id,
                'name' => $field->field_name,
                'price' => $price,
                'type' => 'validation',
                'service_id' => $field->service_id
            ]);
        }

        $wallet = Wallet::where('user_id', Auth::id())->first();

        $query = AgentService::where('user_id', Auth::id())
            ->where('service_type', 'nin_validation');

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where('nin', 'like', "%{$searchTerm}%");
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $submissions = $query->orderBy('created_at', 'desc')->paginate(5)->withQueryString();

        return view('nin.validation', compact('services', 'wallet', 'submissions'));
    }

    public function store(Request $request)
    {
        // 1. Authenticate user
        $user = Auth::user();

        // 2. Validate request
        $validated = $request->validate([
            'service_field' => 'required',
            'nin' => 'required|digits:11',
        ]);

        // 3. Check service active
        $fieldId = $request->service_field;
        $serviceField = ServiceField::with('service')->findOrFail($fieldId);

        if (!$serviceField->service || !$serviceField->service->is_active) {
            return back()->with('error', 'This service is currently inactive.');
        }

        // 4. Calculate price
        $role = $user->role ?? 'user';
        $servicePrice = $serviceField->prices()->where('user_type', $role)->value('price') ?? $serviceField->base_price;

        DB::beginTransaction();

        try {
            // 5. Lock wallet row
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            // 6. Check wallet active
            if (!$wallet || ($wallet->status ?? 'inactive') !== 'active') {
                DB::rollBack();
                return back()->with('error', 'Your wallet is not active. Please contact support.')->withInput();
            }

            // 7. Check balance
            if ($wallet->balance < $servicePrice) {
                DB::rollBack();
                return back()->with('error', 'Insufficient wallet balance.');
            }

            // 8. Create transaction (completed - non-refundable)
            $transactionRef = 'N' . strtoupper(Str::random(10));
            $performedBy = $user->first_name . ' ' . $user->last_name;

            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id' => $user->id,
                'amount' => $servicePrice,
                'description' => "NIN Validation for {$serviceField->field_name}",
                'type' => 'debit',
                'status' => 'completed',
                'performed_by' => $performedBy,
                'metadata' => [
                    'service' => 'Validation',
                    'service_field' => $serviceField->field_name,
                    'nin' => $request->nin,
                ],
            ]);

            // 9. Debit wallet
            $wallet->decrement('balance', $servicePrice);

            // 10. Create service record
            $agentService = AgentService::create([
                'reference' => 'V1' . strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'service_id' => $serviceField->service_id,
                'service_field_id' => $serviceField->id,
                'field_code' => $serviceField->field_code,
                'transaction_id' => $transaction->id,
                'service_type' => 'nin_validation',
                'nin' => $request->nin,
                'amount' => $servicePrice,
                'status' => 'processing',
                'comment' => 'your request is being processing we will update you one the request is treated',
                'submission_date' => now(),
                'service_field_name' => $serviceField->field_name,
                'description' => $request->description ?? $serviceField->field_name,
                'performed_by' => $performedBy,
            ]);

            // 11. Commit
            DB::commit();

            // Check if we already have this nin in the database (LOCAL CHECK)
            $existingService = AgentService::where('nin', $request->nin)
                ->where('service_type', 'nin_validation')
                ->where('id', '!=', $agentService->id)
                ->whereNotNull('comment')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existingService) {
                $status = $existingService->status;
                $cleanResponse = $existingService->comment;

                $agentService->update([
                    'status' => $status,
                    'comment' => $cleanResponse,
                ]);

                return back()->with('success', 'NIN Information retrieved successfully from records. Status: ' . $status);
            }

            // 12. External API Check (PROVIDER STATUS CHECK)
            $apiKey = env('IDENFY_API_KEY');
            $apiBaseUrl = env('IDENFY_API_BASE');

            // --- UPGRADE: Check Provider Status API before submitting new record ---
            $statusUrl = $apiBaseUrl . '/api/nin-validation-status';
            $statusResponse = Http::withToken($apiKey)->post($statusUrl, [
                'nin' => $request->nin,
            ]);

            if ($statusResponse->successful()) {
                $statusData = $statusResponse->json();
                $apiStatusText = $statusData['code'] ?? $statusData['status'] ?? $statusData['response'] ?? null;

                // If the provider has a record, use it
                if ($apiStatusText && !Str::contains(strtolower($apiStatusText), ['no record', 'not found', 'error'])) {
                    $cleanResponse = $this->cleanApiResponse($statusData);
                    $normStatus = $this->normalizeStatus($apiStatusText);

                    $agentService->update([
                        'status' => $normStatus,
                        'comment' => $cleanResponse,
                    ]);

                    return back()->with('success', 'NIN Information retrieved successfully from provider. Status: ' . $normStatus);
                }
            }

            // 13. API Call to Idenfy (NEW SUBMISSION)
            $apiUrl = $apiBaseUrl . '/api/nin-validation';
            $payload = [
                'nin' => $request->nin,
                'message' => $serviceField->field_name,
            ];

            $response = Http::withToken($apiKey)->post($apiUrl, $payload);
            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? false) === true) {
                $status = $this->normalizeStatus($data['code'] ?? 'processing');

                $agentService->update([
                    'status' => $status,
                    'comment' => $this->cleanApiResponse($data),
                ]);

                return back()->with('success', 'Request submitted successfully. Status: ' . $status);
            }

            // Non-Refundable: Even if submission fails at this stage, we keep the charge
            $cleanResponse = $this->cleanApiResponse($data);
            $agentService->update([
                'status' => 'failed',
                'comment' => $cleanResponse
            ]);

            return back()->with('error', 'API Submission Failed: ' . $cleanResponse . '. (Service is non-refundable)');

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('NIN Validation Error: ' . $e->getMessage());
            return back()->with('error', 'System Error: Failed to process request. Please contact support.');
        }
    }

    public function checkStatus(Request $request, $id = null)
    {
        $user = Auth::user();
        if (($user->status ?? 'inactive') !== 'active') {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => "Your account is " . ($user->status ?? 'inactive') . ". Access denied."]);
            }
            return redirect()->back()->with('error', "Your account is currently " . ($user->status ?? 'inactive') . ". Access denied.");
        }

        try {
            if ($id) {
                $agentService = AgentService::findOrFail($id);
            } else {
                $request->validate([
                    'nin' => 'required|string',
                ]);
                $agentService = AgentService::where('nin', $request->nin)
                    ->orderBy('created_at', 'desc')
                    ->firstOrFail();
            }

            $apiKey = env('IDENFY_API_KEY');
            $apiBaseUrl = env('IDENFY_API_BASE', 'https://www.idenfy.ng');
            $url = $apiBaseUrl . '/api/nin-validation-status';
            $payload = [
                'nin' => $agentService->nin,
            ];

            $response = Http::withToken($apiKey)->post($url, $payload);
            $apiResponse = $response->json();

            $apiStatus = $apiResponse['code'] ?? $apiResponse['status'] ?? $apiResponse['response'] ?? null;

            $updateData = [
                'comment' => $this->cleanApiResponse($apiResponse),
            ];

            if ($apiStatus !== null) {
                $updateData['status'] = $this->normalizeStatus($apiStatus);
            }

            $agentService->update($updateData);

            return back()->with('success', 'Status checked successfully. Current status: ' . $agentService->status);

        } catch (\Exception $e) {
            Log::error('Status Check Error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to check status: ' . $e->getMessage(),
                    'status' => 'error'
                ], 500);
            }
            return back()->with('error', 'Unable to complete the request. Please try again.');
        }
    }



    private function cleanApiResponse($response): string
    {
        if (is_array($response)) {
            $message = $response['message'] ?? '';
            $data = $response['data'] ?? [];
            
            // Safe extraction from nested 'data' or top-level
            $nin = '';
            $reply = '';
            
            if (is_array($data)) {
                $nin = $data['nin'] ?? '';
                $reply = $data['reply'] ?? '';
            }
            
            // Fallbacks for top-level if not in data
            if (!$nin) $nin = $response['nin'] ?? '';
            if (!$reply) $reply = $response['reply'] ?? '';

            $parts = array_filter([
                $message,
                $nin ? "NIN: $nin" : null,
                $reply ? "Reply: $reply" : null
            ]);

            $combined = implode(' - ', $parts);

            if ($combined) {
                return strip_tags($combined);
            }

            $jsonString = json_encode($response, JSON_PRETTY_PRINT);
        } else {
            $jsonString = (string) $response;
        }

        $cleanResponse = str_replace(['{', '}', '"', "'"], '', $jsonString);
        $cleanResponse = preg_replace('/\s+/', ' ', $cleanResponse);
        return trim(strip_tags($cleanResponse));
    }

    private function normalizeStatus($status): string
    {
        if (is_bool($status)) {
            return $status ? 'successful' : 'failed';
        }

        $s = strtolower(trim((string) $status));

        return match ($s) {
            'successful', 'success', 'resolved', 'in_progress', 'approved', 'completed' => 'successful',
            'processing', 'pending', 'submitted', 'request_submitted', 'new' => 'processing',
            'failed', 'rejected', 'error', 'declined', 'invalid', 'no record' => 'failed',
            'true' => 'successful',
            'false' => 'failed',
            default => 'pending',
        };
    }
}
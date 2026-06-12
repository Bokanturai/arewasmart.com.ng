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
                'status' => 'pending',
                'comment' => 'your request is being processing we will update you one the request is treated',
                'submission_date' => now(),
                'service_field_name' => $serviceField->field_name,
                'description' => $request->description ?? $serviceField->field_name,
                'performed_by' => $performedBy,
            ]);

            // 11. Commit
            DB::commit();

            return back()->with('success', 'Request submitted successfully. The administrator will review and process your request.');

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('NIN Validation Error: ' . $e->getMessage());
            return back()->with('error', 'System Error: Failed to process request. Please contact support.');
        }
    }
}
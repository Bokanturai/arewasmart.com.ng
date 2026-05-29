<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\AgentService;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    /**
     * Display the loan application page.
     *
     * Each ServiceField under the "Loan" service is a loan type
     * (Solar Loan, School Fees Loan, etc.).
     * ServicePrice per field + role = qualifying amount the user can apply for.
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role ?? 'personal';

        // Get the Loan service (must already exist from seeder)
        $loanService = Service::where('name', 'Loan')->first();
        
        // If loan service doesn't exist, show error (should be seeded)
        if (!$loanService) {
            Log::error('Loan service not found. Please run LoanSeeder.');
            return back()->with('error', 'Loan service is not configured. Please contact administrator.');
        }

        // Total completed debit volume — shown as the user's activity score
        $totalTransactions = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('status', 'completed')
            ->sum('amount');

        // Recent qualifying transactions for the "transaction project" display
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['amount', 'description', 'created_at', 'transaction_ref']);

        $wallet = Wallet::where('user_id', $user->id)->first();

        // Load all active loan types and attach the qualifying amount for this user's role
        $loanTypes = ServiceField::where('service_id', $loanService->id)
            ->where('is_active', true)
            ->where('field_code', '!=', 'LOAN-INTEREST')
            ->get()
            ->map(function (ServiceField $field) use ($role) {
                // ServicePrice.price = qualifying loan amount for this role
                $field->qualifying_amount = $field->getPriceForUserType($role);
                return $field;
            })
            ->filter(fn($field) => $field->qualifying_amount > 0)
            ->values();

        // Eligible if at least one loan type has a qualifying amount for this role
        $isEligible = $loanTypes->isNotEmpty();

        // Previous loan applications
        $submissions = AgentService::where('user_id', $user->id)
            ->where('service_type', 'loan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Security check: only allow new applications if they have no loan records OR if all existing ones are successful
        $latestLoan = AgentService::where('user_id', $user->id)
            ->where('service_type', 'loan')
            ->orderBy('created_at', 'desc')
            ->first();

        $latestStatus = $latestLoan ? strtolower($latestLoan->status) : null;
        $isPending = in_array($latestStatus, ['pending']);
        $isApproved = in_array($latestStatus, ['approved']);

        // User can apply if they do not have an active pending or approved loan AND their can_apply_loan permission is true
        $canApply = $user->can_apply_loan && !$isPending && !$isApproved;

        $hasNonSuccessfulLoan = !$canApply;

        // Get dynamic interest rate for this role (falls back to 20 if not set)
        $interestField = ServiceField::where('service_id', $loanService->id)
            ->where('field_code', 'LOAN-INTEREST')
            ->first();

        $interestRate = 20;
        if ($interestField) {
            $price = $interestField->getPriceForUserType($role);
            if ($price !== null) {
                $interestRate = $price;
            }
        }

        // Fetch repayment transactions from the transactions table
        $repayments = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->where(function ($query) {
                $query->where('description', 'like', '%repayment%')
                      ->orWhere('description', 'like', '%repay%')
                      ->orWhere('description', 'like', '%loan%');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.dashboard.loan', compact(
            'isEligible',
            'totalTransactions',
            'recentTransactions',
            'wallet',
            'loanService',
            'loanTypes',
            'submissions',
            'role',
            'latestLoan',
            'hasNonSuccessfulLoan',
            'interestRate',
            'repayments'
        ));
    }

    /**
     * Submit a new loan application.
     *
     * The requested amount must not exceed the qualifying amount defined in
     * ServicePrice for the selected ServiceField and user role.
     * No wallet is charged — this is purely an application.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $role = $user->role ?? 'personal';

        // ── Security Check: Ensure user is allowed to apply ──
        if (!$user->can_apply_loan) {
            return back()->with('error', 'Security Block: Your loan facility is currently locked. Please contact our support team to unlock your account.')->withInput();
        }

        $latestLoan = AgentService::where('user_id', $user->id)
            ->where('service_type', 'loan')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestLoan && in_array(strtolower($latestLoan->status), ['pending', 'approved'])) {
            return back()->with('error', 'Security Block: You already have an active or pending loan application under review. You can only apply for a new loan once your existing one is fully resolved.')->withInput();
        }

        // ── Basic validation ─────────────────────────────────────────────────
        $request->validate([
            'service_field_id' => 'required|exists:service_fields,id',
            'request_amount'   => 'required|numeric|min:1000',
            'payment_plan'     => 'required|string|in:weekly,biweekly,monthly',
        ]);

        // ── Load the selected loan type ──────────────────────────────────────
        $loanServiceField = ServiceField::with('service')
            ->where('is_active', true)
            ->findOrFail($request->service_field_id);

        $loanService = $loanServiceField->service;

        if (!$loanService || !$loanService->is_active || strtolower($loanService->name) !== 'loan') {
            return back()->with('error', 'Invalid loan type selected. Please try again.')->withInput();
        }

        // ── Get the qualifying amount (max) for this role from ServicePrice ──
        $qualifyingAmount = $loanServiceField->getPriceForUserType($role);

        if (!$qualifyingAmount || $qualifyingAmount <= 0) {
            return back()->with('error',
                'The ' . $loanServiceField->field_name . ' loan is not available for your ' . ucfirst($role) . ' account tier.'
            )->withInput();
        }

        // ── Requested amount must not exceed the qualifying limit ─────────────
        if ($request->request_amount > $qualifyingAmount) {
            return back()->with('error',
                'Your requested amount exceeds your qualifying limit of ₦' .
                number_format($qualifyingAmount, 2) . ' for the ' . $loanServiceField->field_name . '.'
            )->withInput();
        }

        // ── Submit the application ───────────────────────────────────────────
        try {
            $performedBy = trim($user->first_name . ' ' . $user->last_name);
            $reference   = 'LOAN' . strtoupper(Str::random(10));

            AgentService::create([
                'reference'          => $reference,
                'user_id'            => $user->id,
                'service_id'         => $loanService->id,
                'service_field_id'   => $loanServiceField->id,
                'field_code'         => $loanServiceField->field_code,
                'service_type'       => 'loan',
                'service_name'       => $loanServiceField->field_name,
                'service_field_name' => $loanServiceField->field_name,
                'amount'             => $request->request_amount,
                'description'        => 'Loan Type: ' . $loanServiceField->field_name .
                                        ' | Plan: ' . $request->payment_plan .
                                        ' | Qualifying Limit: ₦' . number_format($qualifyingAmount, 2),
                'status'             => 'pending',
                'submission_date'    => now(),
                'performed_by'       => $performedBy,
            ]);

            // Lock user's loan permission until reviewed and re-enabled by admin
            $user->update(['can_apply_loan' => false]);

            Log::info('Loan application submitted', [
                'user_id'          => $user->id,
                'reference'        => $reference,
                'loan_type'        => $loanServiceField->field_name,
                'requested_amount' => $request->request_amount,
                'qualifying_limit' => $qualifyingAmount,
                'role'             => $role,
            ]);

            return back()->with('success',
                'Your ' . $loanServiceField->field_name . ' application of ₦' .
                number_format($request->request_amount, 2) .
                ' has been submitted successfully and is under review. Reference: ' . $reference
            );

        } catch (\Exception $e) {
            Log::error('Loan application failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to submit loan request. Please try again or contact support.')->withInput();
        }
    }
}
<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\AgentService;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\ServicePrice;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    /**
     * Default loan types with per-role qualifying amounts.
     *
     * Each entry becomes a ServiceField under the "Loan" service.
     * The 'prices' array maps user role → max qualifying loan amount (₦).
     * These are ONLY used when auto-creating missing records; admins can
     * override amounts directly in the service_prices table at any time.
     */
    private const DEFAULT_LOAN_TYPES = [
        [
            'field_name'  => 'Solar Loan',
            'field_code'  => 'LOAN-SOLAR',
            'base_price'  => 100_000,
            'description' => 'Finance solar energy systems for your home or business.',
            'prices'      => [
                'personal'    => 100_000,
                'agent'       => 200_000,
                'business'    => 500_000,
                'partner'     => 500_000,
                'staff'       => 1_000_000,
                'checker'     => 1_000_000,
                'super_admin' => 2_000_000,
            ],
        ],
        [
            'field_name'  => 'School Fees Loan',
            'field_code'  => 'LOAN-SCHOOL',
            'base_price'  => 50_000,
            'description' => 'Cover tuition and educational expenses with ease.',
            'prices'      => [
                'personal'    => 50_000,
                'agent'       => 100_000,
                'business'    => 300_000,
                'partner'     => 300_000,
                'staff'       => 500_000,
                'checker'     => 500_000,
                'super_admin' => 1_000_000,
            ],
        ],
        [
            'field_name'  => 'Business Loan',
            'field_code'  => 'LOAN-BUSINESS',
            'base_price'  => 200_000,
            'description' => 'Grow your business with flexible 0% interest financing.',
            'prices'      => [
                'personal'    => 100_000,
                'agent'       => 300_000,
                'business'    => 1_000_000,
                'partner'     => 1_000_000,
                'staff'       => 1_500_000,
                'checker'     => 1_500_000,
                'super_admin' => 2_000_000,
            ],
        ],
        [
            'field_name'  => 'Emergency Loan',
            'field_code'  => 'LOAN-EMERGENCY',
            'base_price'  => 30_000,
            'description' => 'Quick access to funds for urgent personal needs.',
            'prices'      => [
                'personal'    => 30_000,
                'agent'       => 80_000,
                'business'    => 150_000,
                'partner'     => 200_000,
                'staff'       => 300_000,
                'checker'     => 300_000,
                'super_admin' => 500_000,
            ],
        ],
        [
            'field_name'  => 'Asset Finance Loan',
            'field_code'  => 'LOAN-ASSET',
            'base_price'  => 150_000,
            'description' => 'Purchase equipment, devices, or business assets.',
            'prices'      => [
                'personal'    => 80_000,
                'agent'       => 200_000,
                'business'    => 500_000,
                'partner'     => 750_000,
                'staff'       => 1_000_000,
                'checker'     => 1_000_000,
                'super_admin' => 2_000_000,
            ],
        ],
        [
            'field_name'  => 'Loan Interest',
            'field_code'  => 'LOAN-INTEREST',
            'base_price'  => 20,
            'description' => 'Interest rate for loan calculations (%).',
            'prices'      => [
                'personal'    => 20,
                'agent'       => 20,
                'business'    => 20,
                'partner'     => 20,
                'staff'       => 20,
                'checker'     => 20,
                'super_admin' => 20,
            ],
        ],
    ];

    // ────────────────────────────────────────────────────────────────────────────

    /**
     * Auto-create the Loan service, loan type fields, and per-role qualifying
     * amounts if they do not already exist in the database.
     *
     * Uses firstOrCreate throughout — completely safe to call on every page load.
     * Returns the Service model.
     */
    private function ensureLoanServiceExists(): Service
    {
        // 1. Create or fetch the Loan service
        $service = Service::firstOrCreate(
            ['name' => 'Loan'],
            [
                'description' => '0% Interest Loan Facility for Arewa Smart Users',
                'is_active'   => true,
            ]
        );

        // Reactivate if it was disabled
        if (!$service->is_active) {
            $service->update(['is_active' => true]);
        }

        // 2. Create each loan type (ServiceField) + prices (ServicePrice)
        foreach (self::DEFAULT_LOAN_TYPES as $loanType) {
            $field = ServiceField::firstOrCreate(
                [
                    'service_id' => $service->id,
                    'field_code' => $loanType['field_code'],
                ],
                [
                    'field_name'  => $loanType['field_name'],
                    'base_price'  => $loanType['base_price'],
                    'description' => $loanType['description'],
                    'is_active'   => true,
                ]
            );

            if (!$field->is_active) {
                $field->update(['is_active' => true]);
            }

            // Create per-role qualifying amounts (price = max loan amount for that role)
            foreach ($loanType['prices'] as $userType => $qualifyingAmount) {
                ServicePrice::firstOrCreate(
                    [
                        'service_id'        => $service->id,
                        'service_fields_id' => $field->id,
                        'user_type'         => $userType,
                    ],
                    ['price' => $qualifyingAmount]
                );
            }
        }

        return $service;
    }

    // ────────────────────────────────────────────────────────────────────────────

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

        // Auto-create the Loan service + fields + prices if missing
        $loanService = $this->ensureLoanServiceExists();

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

        $hasNonSuccessfulLoan = false;
        if ($latestLoan && !in_array(strtolower($latestLoan->status), ['successful', 'success'])) {
            $hasNonSuccessfulLoan = true;
        }

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

    // ────────────────────────────────────────────────────────────────────────────

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

        // ── Security Check: Ensure user does not have any active/unsuccessful loan ──
        $latestLoan = AgentService::where('user_id', $user->id)
            ->where('service_type', 'loan')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestLoan && !in_array(strtolower($latestLoan->status), ['successful', 'success'])) {
            return back()->with('error', 'Security Block: You already have a ' . ucfirst($latestLoan->status) . ' loan application. You are only allowed to apply for a new loan once your existing one is fully settled and successful.')->withInput();
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

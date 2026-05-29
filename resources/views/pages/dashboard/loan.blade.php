<x-app-layout>
    <title>Arewa Smart - low Interest Loans</title>

    <div class="page-body">
        <div class="container-fluid px-0 px-md-3">

            {{-- ========================= Hero Banner ========================= --}}
            <div class="row g-0 g-md-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 overflow-hidden shadow-lg position-relative hero-banner-card"
                         style="border-radius: 20px;">
                        <div class="card-body p-4 p-md-5">
                            <div class="row align-items-center">
                                <div class="col-lg-7 text-white animate__animated animate__fadeInLeft">
                                    <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold text-uppercase"
                                          style="letter-spacing: 1px;">Premium Opportunity</span>
                                    <h1 class="display-5 fw-bold mb-3">
                                        Empower Your Business with <span class="text-warning">low Interest</span>
                                    </h1>
                                    <p class="lead mb-4 opacity-75">
                                        Get the financial boost you need without hidden charges. Choose the loan type that fits your needs.
                                    </p>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-check bg-success rounded-circle p-1 fs-16"></i>
                                            <span>Low Interest</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-check bg-success rounded-circle p-1 fs-16"></i>
                                            <span>Quick Approval</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-check bg-success rounded-circle p-1 fs-16"></i>
                                            <span>Flexible Repayment</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 d-none d-lg-block text-center animate__animated animate__fadeInRight">
                                    <div class="p-4 bg-white bg-opacity-10 rounded-circle d-inline-block backdrop-blur">
                                        <i class="ti ti-wallet text-white" style="font-size: 8rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================= Main Grid ========================== --}}
            <div class="row g-0 g-md-4">

                {{-- ======= Left Column: Loan Types / Application Form ======= --}}
                <div class="col-12 col-xl-7">

                 
                    {{-- ======= Flash Messages (always visible) ======= --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-circle-check-filled fs-4"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-alert-circle-filled fs-4"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($hasNonSuccessfulLoan)
                        {{-- ========= ACTIVE / UNRESOLVED / FAILED LOAN STATE ========= --}}
                        @php
                            $statusLower = strtolower($latestLoan->status ?? '');
                            $isApproved  = in_array($statusLower, ['approved']);
                            $isPending   = in_array($statusLower, ['pending']);
                            $isDeclined  = !$isPending && !$isApproved;
                        @endphp
                        <div class="card border-0 shadow-sm mb-4 overflow-hidden animate__animated animate__fadeInUp"
                             style="border-radius: 20px;">
                            <div class="card-body p-4 text-center">
                                @if($isDeclined)
                                    <div class="mx-auto bg-danger-soft rounded-circle d-flex align-items-center justify-content-center mb-4"
                                         style="width: 100px; height: 100px;">
                                        <i class="ti ti-lock text-danger" style="font-size: 3rem;"></i>
                                    </div>
                                    <h4 class="fw-bold text-dark mb-3">Loan Facility Locked</h4>
                                    <p class="text-muted mb-4 mx-auto" style="max-width: 480px;">
                                        @if(!Auth::user()->can_apply_loan)
                                            Your loan facility is currently locked by the administration. To maintain system integrity, you are currently locked from reapplying. Please contact our credit team or support to unlock your account.
                                        @else
                                            Your previous loan application was not successful (declined, rejected, or failed). To maintain system integrity, you are currently locked from reapplying. Please contact our credit team or support to unlock your account.
                                        @endif
                                    </p>
                                @elseif($isApproved)
                                    <div class="mx-auto bg-success-soft rounded-circle d-flex align-items-center justify-content-center mb-4"
                                         style="width: 100px; height: 100px;">
                                        <i class="ti ti-discount-check text-success" style="font-size: 3.5rem;"></i>
                                    </div>
                                    <h4 class="fw-bold text-success mb-3">Congratulations! Your Loan is Approved</h4>
                                    <p class="text-muted mb-4 mx-auto" style="max-width: 480px;">
                                        Great news, <strong>{{ Auth::user()->first_name }}</strong>! Your application for the <strong>{{ $latestLoan->service_name ?? 'Loan' }}</strong> has been fully approved by our credit team. Your active loan details and repayment plan are ready below.
                                    </p>
                                @else
                                    <div class="mx-auto bg-warning-soft rounded-circle d-flex align-items-center justify-content-center mb-4"
                                         style="width: 100px; height: 100px;">
                                        <i class="ti ti-clock text-warning" style="font-size: 3rem;"></i>
                                    </div>
                                    <h4 class="fw-bold text-dark mb-3">Active Loan Application Under Review</h4>
                                    <p class="text-muted mb-4 mx-auto" style="max-width: 480px;">
                                        You currently have an active loan application that is under review. To maintain security, you are only allowed to submit a new loan application once your current active request is fully approved or settled.
                                    </p>
                                @endif
                                
                                @if($latestLoan)
                                    <div class="p-3 bg-light rounded-4 border mb-4 text-start mx-auto" style="max-width: 450px;">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Type:</span>
                                            <strong class="text-dark">{{ $latestLoan->service_name ?? 'Loan' }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Requested Amount:</span>
                                            <strong class="text-dark">₦{{ number_format($latestLoan->amount ?? 0, 2) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Status:</span>
                                            @php
                                                $badgeClass = match($latestLoan->status ?? '') {
                                                    'successful', 'success', 'approved' => 'success',
                                                    'pending'                           => 'warning',
                                                    'failed', 'rejected', 'declined'    => 'danger',
                                                    default                             => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}-soft text-{{ $badgeClass }} py-1 px-3 rounded-pill fw-bold" style="font-size: 10px;">
                                                {{ ucfirst($latestLoan->status ?? 'Pending') }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Reference:</span>
                                            <span class="font-monospace text-dark">{{ $latestLoan->reference ?? '—' }}</span>
                                        </div>
                                    </div>

                                    @if($isApproved)
                                        <a href="{{ route('transactions', ['search' => 'repayment', 'type' => 'debit']) }}" 
                                           class="btn btn-success rounded-pill px-4 py-2 fw-bold text-white"
                                           style="background: linear-gradient(135deg, #198754 0%, #2bb16f 100%); border: none; box-shadow: 0 4px 10px rgba(25, 135, 84, 0.25) !important;">
                                            <i class="ti ti-receipt me-2"></i> View Repayment History
                                        </a>
                                    @else
                                        <button type="button" 
                                                class="btn btn-primary rounded-pill px-4 py-2 fw-bold"
                                                style="background: linear-gradient(135deg, #F26522 0%, #ff8c52 100%); border: none; box-shadow: 0 4px 8px rgba(242, 101, 34, 0.2);"
                                                data-bs-toggle="modal"
                                                data-bs-target="#commentModal"
                                                data-comment="{{ $latestLoan->comment ?? ($isDeclined ? 'Your loan application was declined after checking credit score/account parameters. Contact support for help.' : 'Your loan application is currently under review by our credit team. We will update you shortly.') }}"
                                                data-ref="{{ $latestLoan->reference ?? '' }}"
                                                data-approved-by="{{ $latestLoan->approved_by ?? $latestLoan->completed_by ?? '' }}">
                                            <i class="ti ti-eye me-2"></i> {{ $isDeclined ? 'View Decline Reason' : 'View Application Details' }}
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('support.create') }}" 
                                       class="btn btn-primary rounded-pill px-4 py-2 fw-bold"
                                       style="background: linear-gradient(135deg, #F26522 0%, #ff8c52 100%); border: none; box-shadow: 0 4px 8px rgba(242, 101, 34, 0.2);">
                                        <i class="ti ti-headphones me-2"></i> Contact Support
                                    </a>
                                @endif
                            </div>
                        </div>

                    @elseif(!$isEligible)
                        {{-- ========= INELIGIBLE STATE ========= --}}
                        <div class="card border-0 shadow-sm mb-4 overflow-hidden animate__animated animate__fadeInUp"
                             style="border-radius: 20px;">
                            <div class="card-body p-4 text-center">
                                <div class="mx-auto bg-warning-soft rounded-circle d-flex align-items-center justify-content-center mb-4"
                                     style="width: 100px; height: 100px;">
                                    <i class="ti ti-lock-square text-warning" style="font-size: 3rem;"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-3">No Loan Types Available for Your Account</h4>
                                <p class="text-muted mb-4 mx-auto" style="max-width: 480px;">
                                    @if(!$loanService)
                                        The loan service is currently unavailable. Please check back later or contact support.
                                    @else
                                        No loan types have been configured for your <strong>{{ ucfirst($role) }}</strong> account tier yet.
                                        Please contact support to find out more.
                                    @endif
                                </p>
                                <a href="{{ route('support.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold">
                                    <i class="ti ti-headphones me-2"></i> Contact Support
                                </a>
                            </div>
                        </div>

                    @else
                        {{-- ========= ELIGIBLE STATE — LOAN TYPE CARDS + FORM ========= --}}

                        {{-- Step 1: Loan Type Cards --}}
                        <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeInUp" style="border-radius: 20px;">
                            <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                                    <i class="ti ti-cards text-primary fs-16"></i>
                                    Step 1 — Choose Your Loan Type
                                </h5>
                                <p class="text-muted small mb-0">
                                    Select the loan type that suits your needs. Your qualifying amount is shown for each type.
                                </p>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3" id="loanTypeCards">
                                    @foreach($loanTypes as $loanType)
                                        <div class="col-12 col-sm-6">
                                            <label class="loan-type-card w-100 cursor-pointer" for="loan_type_{{ $loanType->id }}">
                                                <input type="radio"
                                                       class="loan-type-radio d-none"
                                                       name="loan_type_selection"
                                                       id="loan_type_{{ $loanType->id }}"
                                                       value="{{ $loanType->id }}"
                                                       data-qualifying="{{ $loanType->qualifying_amount }}"
                                                       data-name="{{ $loanType->field_name }}">
                                                <div class="loan-card-inner p-3 rounded-4 border h-100 d-flex flex-column">
                                                    <div class="d-flex align-items-start justify-content-between mb-2">
                                                        <div class="loan-icon-wrap rounded-3 p-2 bg-primary-soft">
                                                            <i class="ti ti-cash text-primary fs-20"></i>
                                                        </div>
                                                        <span class="loan-check-icon badge bg-primary rounded-circle p-1 opacity-0">
                                                            <i class="ti ti-check"></i>
                                                        </span>
                                                    </div>
                                                    <div class="fw-bold text-dark mb-1" style="font-size: 14px;">
                                                        {{ $loanType->field_name }}
                                                    </div>
                                                    @if($loanType->description)
                                                        <div class="text-muted small mb-2" style="font-size: 12px;">
                                                            {{ Str::limit($loanType->description, 60) }}
                                                        </div>
                                                    @endif
                                                    <div class="mt-auto pt-2 border-top">
                                                        <div class="small text-muted">Your Qualifying Limit</div>
                                                        <div class="fw-bold text-success fs-16">
                                                            ₦{{ number_format($loanType->qualifying_amount, 2) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    @endif

                    {{-- ======= Loan Application Modal ======= --}}
                    @if($isEligible)
                    <div class="modal fade" id="loanApplicationModal" tabindex="-1" aria-labelledby="loanApplicationModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-md">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">

                                {{-- Modal Header --}}
                                <div class="modal-header border-0 p-0">
                                    <div class="w-100 p-4" style="background: linear-gradient(135deg, #8dacf3 0%, #F26522 100%);">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-white">
                                                <div class="small opacity-75 mb-1">Step 2 — Submit Application</div>
                                                <h5 class="fw-bold mb-0" id="loanApplicationModalLabel">
                                                    <i class="ti ti-edit me-2"></i>
                                                    <span id="selectedLoanName">Loan Application</span>
                                                </h5>
                                                <div class="small mt-1 opacity-90">
                                                    Qualifying Limit:
                                                    <strong id="selectedQualifyingAmount">₦0.00</strong>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white me-1" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Body --}}
                                <div class="modal-body p-4">

                                    {{-- Selected Loan Type Badge --}}
                                    <div class="d-flex align-items-center gap-2 mb-4 p-3 rounded-4 bg-light">
                                        <div class="rounded-3 p-2" style="background: rgba(242,101,34,0.12);">
                                            <i class="ti ti-cash text-primary fs-20"></i>
                                        </div>
                                        <div>
                                            <div class="small text-muted">Selected Loan Type</div>
                                            <div class="fw-bold text-dark" id="modalLoanTypeBadge">—</div>
                                        </div>
                                        <div class="ms-auto text-end">
                                            <div class="small text-muted">Max Limit</div>
                                            <div class="fw-bold text-success" id="modalMaxLimit">₦0.00</div>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('loan.store') }}" id="loanApplicationForm" class="no-loader">
                                        @csrf
                                        <input type="hidden" name="service_field_id" id="serviceFieldIdInput">

                                        {{-- Amount --}}
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Requested Amount (₦)</label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text bg-light border-0 fw-bold text-muted">₦</span>
                                                <input type="number"
                                                       name="request_amount"
                                                       id="requestAmountInput"
                                                       class="form-control bg-light border-0"
                                                       placeholder="e.g. 50,000"
                                                       min="1000"
                                                       step="100"
                                                       required>
                                            </div>
                                            <small class="text-muted mt-2 d-block">
                                                Minimum: ₦1,000.00 &nbsp;|&nbsp;
                                                Maximum: <strong class="text-success" id="maxAmountHint">₦0.00</strong>
                                            </small>
                                        </div>

                                        {{-- Repayment Plan --}}
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Repayment Plan</label>
                                            <select name="payment_plan" class="form-select form-select-lg bg-light border-0" required>
                                                <option value="">-- Select Repayment Plan --</option>
                                                <option value="weekly">Weekly &nbsp;(1 Month Duration)</option>
                                                <option value="biweekly">Bi-weekly &nbsp;(2 Months Duration)</option>
                                                <option value="monthly">Monthly &nbsp;(3 Months Duration)</option>
                                            </select>
                                        </div>

                                        {{-- Terms --}}
                                        <div class="mb-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                                <label class="form-check-label small text-muted" for="termsCheck">
                                                    I agree to the
                                                    <a href="#" class="text-primary text-decoration-none fw-semibold">Loan Terms &amp; Conditions</a>
                                                    and authorize Arewa Smart to evaluate my account data.
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Submit --}}
                                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm fs-16">
                                            <i class="ti ti-rocket me-2"></i> Submit Application
                                        </button>
                                        
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                {{-- ======= Right Column: History ======= --}}
                <div class="col-12 col-xl-5 mt-2 mt-md-0">

                    {{-- Application History --}}
                    <div class="card border-0 shadow-sm overflow-hidden animate__animated animate__fadeInUp animate__delay-1s"
                         style="border-radius: 20px;">
                        <div class="card-header bg-primary py-3">
                            <h5 class="fw-bold mb-0 text-white d-flex align-items-center gap-2">
                                <i class="ti ti-history fs-20"></i>
                                Application History
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex flex-column gap-3">
                                @forelse($submissions as $sub)
                                    @php
                                        $statusClass = match($sub->status) {
                                            'successful', 'success', 'approved' => 'success',
                                            'pending'                           => 'warning',
                                            'failed', 'rejected'                => 'danger',
                                            default                             => 'secondary'
                                        };
                                    @endphp
                                    <div class="p-3 rounded-4 border bg-white shadow-sm position-relative transition-all" 
                                         style="border-color: rgba(0,0,0,0.06) !important; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                        
                                        {{-- Top Row: Icon + Title + Status Badge --}}
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-3 p-2" style="background: rgba(242,101,34,0.08); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="ti ti-cash text-primary fs-16"></i>
                                                </div>
                                                <div class="fw-bold text-dark" style="font-size: 13.5px;">
                                                    {{ $sub->service_name ?? 'Loan Application' }}
                                                </div>
                                            </div>
                                            <span class="badge bg-{{ $statusClass }}-soft text-{{ $statusClass }} py-1 px-3 rounded-pill fw-bold" style="font-size: 9px;">
                                                {{ ucfirst($sub->status) }}
                                            </span>
                                        </div>

                                        {{-- Divider line --}}
                                        <hr class="my-2 opacity-5" style="border-color: rgba(0,0,0,0.1) !important;">

                                        {{-- Details Row --}}
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div>
                                                <div class="small text-muted" style="font-size: 10px;">Applied Amount</div>
                                                <div class="fw-bold text-dark" style="font-size: 13.5px;">
                                                    ₦{{ number_format($sub->amount, 2) }}
                                                </div>
                                            </div>
                                            
                                            <div class="text-end">
                                                <div class="small text-muted" style="font-size: 10px;">Submission Date</div>
                                                <div class="small text-dark" style="font-size: 11.5px; font-weight: 500;">
                                                    {{ $sub->created_at->format('M d, Y') }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- View Action Section --}}
                                        <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: rgba(0,0,0,0.04) !important;">
                                            <div class="small text-muted font-monospace" style="font-size: 10px;">
                                                Ref: {{ $sub->reference }}
                                            </div>
                                            
                                            @if(in_array(strtolower($sub->status), ['success', 'successful', 'approved']))
                                                <a href="{{ route('transactions', ['search' => 'repayment', 'type' => 'debit']) }}"
                                                   class="btn btn-xs rounded-pill px-3 text-white d-inline-flex align-items-center gap-1"
                                                   style="font-size: 10px; padding: 4px 10px; background: linear-gradient(135deg, #198754 0%, #2fb380 100%); border: none; box-shadow: 0 2px 5px rgba(25, 135, 84, 0.2); font-weight: 600; transition: all 0.2s ease-in-out;">
                                                    <i class="ti ti-receipt" style="font-size: 11px;"></i> Repayment
                                                </a>
                                            @else
                                                <button type="button"
                                                        class="btn btn-xs rounded-pill px-3 text-white d-inline-flex align-items-center gap-1"
                                                        style="font-size: 10px; padding: 4px 10px; background: linear-gradient(135deg, #F26522 0%, #ff8c52 100%); border: none; box-shadow: 0 2px 5px rgba(242, 101, 34, 0.2); font-weight: 600; transition: all 0.2s ease-in-out;"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#commentModal"
                                                        data-comment="{{ $sub->comment ?? 'Your loan application is currently under review by our credit team. We will update you shortly.' }}"
                                                        data-ref="{{ $sub->reference }}"
                                                        data-approved-by="{{ $sub->approved_by ?? $sub->completed_by ?? $sub->performed_by }}">
                                                    <i class="ti ti-eye" style="font-size: 11px;"></i> Details
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted bg-light rounded-4">
                                        <i class="ti ti-folder-off fs-1 d-block mb-3 opacity-25"></i>
                                        No loan applications yet.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        @if($submissions->hasPages())
                            <div class="card-footer bg-white border-0 py-3">
                                {{ $submissions->links('vendor.pagination.custom') }}
                            </div>
                        @endif
                    </div>

                   

                    @include('pages.comment')

                    {{-- ======= Repayment Details Modal ======= --}}
                    <div class="modal fade" id="repaymentModal" tabindex="-1" aria-labelledby="repaymentModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">

                                {{-- Modal Header --}}
                                <div class="modal-header border-0 p-0">
                                    <div class="w-100 p-4" style="background: linear-gradient(135deg, #c46903ff 0%, #a4f115ff 100%);">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-white">
                                                <div class="small opacity-75 mb-1">Repayment Details</div>
                                                <h5 class="fw-bold mb-0" id="repaymentModalLabel">
                                                    <i class="ti ti-cash me-2"></i>
                                                    <span id="repayModalLoanName">Loan Repayment</span>
                                                </h5>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white me-1" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Body --}}
                                <div class="modal-body p-4">

                                    {{-- Reference --}}
                                    <div class="mb-4">
                                        <div class="small text-muted mb-1">Loan Reference</div>
                                        <div class="font-monospace small bg-light px-3 py-2 rounded-3 text-dark fw-bold" id="repayModalRef">—</div>
                                    </div>

                                    {{-- Repayment Summary cards --}}
                                    <div class="row g-3 mb-4">
                                        <div class="col-6">
                                            <div class="p-3 bg-light rounded-4 border" style="border-color: rgba(0,0,0,0.06) !important;">
                                                <div class="small text-muted mb-1" style="font-size: 10px;">Principal Amount</div>
                                                <h5 class="fw-bold text-dark mb-0" id="repayModalPrincipal">₦0.00</h5>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 bg-light rounded-4 border" style="border-color: rgba(0,0,0,0.06) !important;">
                                                <div class="small text-muted mb-1" style="font-size: 10px;">Interest Charged</div>
                                                <h5 class="fw-bold text-danger mb-0" id="repayModalInterest">₦0.00</h5>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Total repayment card --}}
                                    <div class="p-3 rounded-4 mb-4 border d-flex align-items-center justify-content-between" style="background: rgba(25,135,84,0.04); border-color: rgba(25,135,84,0.15) !important;">
                                        <div>
                                            <div class="small text-success fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px; text-transform: uppercase;">Total Repayment Amount</div>
                                            <h3 class="fw-bold text-success mb-0" id="repayModalTotal">₦0.00</h3>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success text-white py-1 px-3 rounded-pill fw-bold" style="font-size: 10px;" id="repayModalPlan">
                                                Monthly
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Security disclaimer --}}
                                    <div class="alert alert-info border-0 rounded-4 p-3 mb-0" style="background: rgba(13,110,253,0.06);">
                                        <div class="d-flex gap-2">
                                            <i class="ti ti-info-circle text-primary fs-14 mt-1"></i>
                                            <div class="small text-muted" style="line-height: 1.6;">
                                                Repayments are deducted automatically from your active wallet balance according to your plan schedule. Please ensure your wallet is sufficiently funded.
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                {{-- Modal Footer --}}
                                <div class="modal-footer border-0 p-4 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4 w-100" data-bs-dismiss="modal">Close Details</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>{{-- end .row --}}
        </div>
    </div>

    {{-- ========================= Styles ========================= --}}
    <style>
        .bg-warning-soft   { background-color: rgba(255, 193,   7, 0.1); }
        .bg-primary-soft   { background-color: rgba( 13, 110, 253, 0.1); }
        .bg-success-soft   { background-color: rgba( 25, 135,  84, 0.1); }
        .bg-danger-soft    { background-color: rgba(220,  53,  69, 0.1); }
        .bg-secondary-soft { background-color: rgba(108, 117, 125, 0.1); }

        .bg-gradient-primary {
            background: linear-gradient(90deg, #F26522 0%, #ff8c5a 100%);
        }

        .backdrop-blur {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Loan type card */
        .loan-type-card { cursor: pointer; }

        .loan-card-inner {
            border-color: #dee2e6 !important;
            transition: all 0.2s ease;
            background: #fff;
        }

        [data-theme="dark"] .loan-card-inner {
            background-color: var(--dark-card, #161b22) !important;
            border-color: var(--dark-border, #30363d) !important;
        }

        .loan-type-card:hover .loan-card-inner {
            border-color: #F26522 !important;
            box-shadow: 0 4px 15px rgba(242, 101, 34, 0.12);
            transform: translateY(-2px);
        }

        .loan-type-radio:checked + .loan-card-inner {
            border-color: #F26522 !important;
            background: rgba(242, 101, 34, 0.04);
            box-shadow: 0 4px 15px rgba(242, 101, 34, 0.15);
        }

        [data-theme="dark"] .loan-type-radio:checked + .loan-card-inner {
            background: rgba(242, 101, 34, 0.15) !important;
            border-color: #F26522 !important;
        }

        .loan-type-radio:checked + .loan-card-inner .loan-check-icon {
            opacity: 1 !important;
        }

        .loan-type-radio:checked + .loan-card-inner .loan-icon-wrap {
            background: rgba(242, 101, 34, 0.15) !important;
        }

        /* Form */
        .form-control:focus, .form-select:focus {
            box-shadow: none;
            background-color: #f0f2f5 !important;
            border-color: #F26522;
        }

        .btn-primary {
            background: linear-gradient(135deg, #F26522 0%, #ff8c5a 100%);
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(242, 101, 34, 0.2) !important;
            background: linear-gradient(135deg, #e55a1b 0%, #f26522 100%);
        }

        tfoot td { border-top: 2px solid #dee2e6 !important; }

        .cursor-pointer { cursor: pointer; }

        /* Hero Banner Card Premium Theme Adjustments */
        .hero-banner-card {
            background: linear-gradient(135deg, #8dacf3 0%, #F26522 100%) !important;
        }
        .hero-banner-card h1,
        .hero-banner-card p,
        .hero-banner-card span,
        .hero-banner-card i {
            color: #ffffff !important;
        }
        .hero-banner-card p {
            opacity: 0.95 !important;
        }

        [data-theme="dark"] .hero-banner-card {
            background: linear-gradient(135deg, #1f2f4e 0%, #bf4610 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }
        [data-theme="dark"] .hero-banner-card h1,
        [data-theme="dark"] .hero-banner-card p,
        [data-theme="dark"] .hero-banner-card span,
        [data-theme="dark"] .hero-banner-card i {
            color: #ffffff !important;
        }
        [data-theme="dark"] .hero-banner-card p {
            opacity: 0.85 !important;
        }

        /* Dark Mode Form Input Controls */
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select,
        [data-theme="dark"] .input-group-text,
        [data-theme="dark"] input.bg-light,
        [data-theme="dark"] select.bg-light,
        [data-theme="dark"] .input-group-text.bg-light {
            background-color: var(--dark-bg, #0d1117) !important;
            border-color: var(--dark-border, #30363d) !important;
            color: #ffffff !important;
        }

        [data-theme="dark"] .form-control::placeholder {
            color: var(--dark-text-muted, #8b949e) !important;
            opacity: 0.6 !important;
        }

        [data-theme="dark"] select.form-select option {
            background-color: var(--dark-card, #161b22) !important;
            color: #ffffff !important;
        }

        [data-theme="dark"] div.bg-light {
            background-color: var(--dark-bg, #0d1117) !important;
        }

        /* Elevate SweetAlert2 in front of Bootstrap modals */
        .swal2-container {
            z-index: 999999 !important;
        }
    </style>

    {{-- ========================= Scripts ========================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── Loan Application Modal ────────────────────────────────────────
            const radios       = document.querySelectorAll('.loan-type-radio');
            const fieldInput   = document.getElementById('serviceFieldIdInput');
            const amtInput     = document.getElementById('requestAmountInput');
            const nameLabel    = document.getElementById('selectedLoanName');
            const limitLabel   = document.getElementById('selectedQualifyingAmount');
            const maxHint      = document.getElementById('maxAmountHint');
            const badgeLabel   = document.getElementById('modalLoanTypeBadge');
            const maxLimLabel  = document.getElementById('modalMaxLimit');
            const modalEl      = document.getElementById('loanApplicationModal');
            const bsModal      = modalEl ? new bootstrap.Modal(modalEl) : null;

            radios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    if (!this.checked || !bsModal) return;

                    const fieldId   = this.value;
                    const name      = this.dataset.name;
                    const maxAmount = parseFloat(this.dataset.qualifying);
                    const formatted = '₦' + maxAmount.toLocaleString('en-NG', { minimumFractionDigits: 2 });

                    fieldInput.value        = fieldId;
                    amtInput.max            = maxAmount;
                    amtInput.value          = '';
                    nameLabel.textContent   = name;
                    limitLabel.textContent  = formatted;
                    maxHint.textContent     = formatted;
                    if (badgeLabel)  badgeLabel.textContent  = name;
                    if (maxLimLabel) maxLimLabel.textContent = formatted;

                    bsModal.show();
                });
            });

            // Re-open on validation errors
            @if(old('service_field_id'))
                const oldId    = '{{ old('service_field_id') }}';
                const oldRadio = document.getElementById('loan_type_' + oldId);
                if (oldRadio) {
                    oldRadio.checked = true;
                    oldRadio.dispatchEvent(new Event('change'));
                }
            @endif

            // ── Form Double-Submit Protection & SweetAlert Confirmation ──────
            const loanForm = document.getElementById('loanApplicationForm');
            if (loanForm) {
                loanForm.addEventListener('submit', function (e) {
                    if (this.dataset.confirmed === 'true') {
                        return; // Let natural submission proceed!
                    }

                    // Check standard HTML5 validation first (required, type, min, etc.)
                    if (!this.checkValidity()) {
                        this.reportValidity(); // Triggers browser's native validation bubbles
                        return;
                    }

                    // Form is valid! Prevent immediate submit so we can show SweetAlert confirmation
                    e.preventDefault();

                    const amountVal = document.getElementById('requestAmountInput').value;
                    const amount = parseFloat(amountVal);
                    const planSelect = this.querySelector('select[name="payment_plan"]');
                    const plan = planSelect.options[planSelect.selectedIndex]?.text || '';

                    Swal.fire({
                        title: 'Confirm Loan Application',
                        html: `
                            <div class="text-center py-2">
                                <p class="mb-3">Are you sure you want to apply for this loan?</p>
                                <div class="p-3 bg-light rounded-4 mb-2 text-start" style="border: 1px dashed rgba(242, 101, 34, 0.2);">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Requested Amount:</span>
                                        <strong class="text-success fs-15">₦${amount.toLocaleString('en-NG', { minimumFractionDigits: 2 })}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Repayment Plan:</span>
                                        <strong class="text-dark small">${plan}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Interest Rate:</span>
                                        <strong class="text-primary small">Low Interest</strong>
                                    </div>
                                </div>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#F26522',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Submit Application',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-primary px-4 py-2 rounded-pill mx-2',
                            cancelButton: 'btn btn-secondary px-4 py-2 rounded-pill mx-2'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Set confirmation flag to true
                            loanForm.dataset.confirmed = 'true';

                            const submitBtn = loanForm.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Submitting...';
                            }
                            Swal.fire({
                                title: 'Submitting Request',
                                text: 'Please wait while we secure your transaction...',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Submit using requestSubmit to trigger standard submission pipeline
                            if (typeof loanForm.requestSubmit === 'function') {
                                loanForm.requestSubmit();
                            } else {
                                loanForm.submit();
                            }
                        }
                    });
                });
            }

            // ── Repayment Modal Populator ───────────────────────────────────
            const repayModalEl = document.getElementById('repaymentModal');
            if (repayModalEl) {
                repayModalEl.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return;

                    const name      = button.getAttribute('data-name');
                    const ref       = button.getAttribute('data-ref');
                    const principal = button.getAttribute('data-principal');
                    const interest  = button.getAttribute('data-interest');
                    const total     = button.getAttribute('data-total');
                    const plan      = button.getAttribute('data-plan');

                    document.getElementById('repayModalLoanName').textContent  = name + ' Repayment';
                    document.getElementById('repayModalRef').textContent       = ref;
                    document.getElementById('repayModalPrincipal').textContent = principal;
                    document.getElementById('repayModalInterest').textContent  = interest;
                    document.getElementById('repayModalTotal').textContent     = total;
                    document.getElementById('repayModalPlan').textContent      = plan;
                });
            }
        });
    </script>

</x-app-layout>


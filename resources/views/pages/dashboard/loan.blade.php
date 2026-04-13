<x-app-layout>
    <title>Arewa Smart - 0% Interest Loans</title>
    
    <div class="page-body">
        <div class="container-fluid px-0 px-md-3">
            <!-- Hero Advert Section -->
            <div class="row g-0 g-md-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 overflow-hidden shadow-lg position-relative" style="border-radius: 12px; background: linear-gradient(135deg, #8dacf3ff 0%, #F26522 100%);">
                        <div class="card-body p-4 p-md-5">
                            <div class="row align-items-center">
                                <div class="col-lg-7 text-white animate__animated animate__fadeInLeft">
                                    <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px;">Premium Opportunity</span>
                                    <h1 class="display-5 fw-bold mb-3">Empower Your Business with <span class="text-warning">0% Interest</span></h1>
                                    <p class="lead mb-4 opacity-75">Get the financial boost you need without any hidden charges. Our elite agents deserve the best growth opportunities.</p>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-check bg-success rounded-circle p-1 fs-6"></i>
                                            <span>No Interest</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-check bg-success rounded-circle p-1 fs-6"></i>
                                            <span>Quick Approval</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-check bg-success rounded-circle p-1 fs-6"></i>
                                            <span>Flexible Repayment</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 d-none d-lg-block text-center animate__animated animate__fadeInRight">
                                    <div class="position-absolute top-50 start-100 translate-middle-y opacity-10" style="font-size: 20rem; right: 5% !important;">
                                        <i class="ti ti-coin"></i>
                                    </div>
                                    <div class="p-4 bg-white bg-opacity-10 rounded-circle d-inline-block backdrop-blur">
                                        <i class="ti ti-wallet text-white" style="font-size: 8rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-0 g-md-4">
                <!-- Main Content -->
                <div class="col-12 col-xl-7">
                    @if(!$isEligible)
                        <!-- Ineligible State -->
                        <div class="card border-0 shadow-sm mb-4 overflow-hidden animate__animated animate__fadeInUp" style="border-radius: 12px;">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                                    <i class="ti ti-shield-alert text-warning fs-3"></i>
                                    Eligibility Status
                                </h5>
                            </div>
                            <div class="card-body p-4 text-center">
                                <div class="mb-4">
                                    <div class="mx-auto bg-warning-soft rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                        <i class="ti ti-lock-square text-warning" style="font-size: 3rem;"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold text-dark mb-3">You are not eligible for a loan now</h4>
                                <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                                    To unlock our 0% interest loan facility, you need to reach a minimum transaction volume of <strong>₦1,000,000.00</strong>. Keep making transactions to build your credit score!
                                </p>
                                
                                <div class="progress rounded-pill mb-3" style="height: 12px; background: #eaedf1;">
                                    @php
                                        $percentage = min(100, ($totalTransactions / $threshold) * 100);
                                    @endphp
                                    <div class="progress-bar bg-gradient-primary rounded-pill progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between mb-4 px-2">
                                    <span class="small fw-bold">Current Volume: ₦{{ number_format($totalTransactions, 2) }}</span>
                                    <span class="small text-muted">Goal: ₦1,000,000.00</span>
                                </div>

                                <div class="p-3 bg-light rounded-4 mb-4">
                                    <p class="small mb-0 text-dark">
                                        <i class="ti ti-info-circle-filled text-primary me-1"></i>
                                        For more information about our loan packages, please reach out to our team.
                                    </p>
                                </div>

                                <a href="{{ route('support.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold">
                                    <i class="ti ti-headphones me-2"></i> Contact Support
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Eligible State - Application Form -->
                        <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeInUp" style="border-radius: 12px;">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                                    <i class="ti ti-edit text-primary fs-3"></i>
                                    Apply for Loan
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show rounded-3">
                                        <i class="ti ti-circle-check-filled me-2"></i> {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show rounded-3">
                                        <i class="ti ti-alert-circle-filled me-2"></i> {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('loan.store') }}" class="row g-4">
                                    @csrf
                                    
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Requested Amount (₦)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">₦</span>
                                            <input type="number" name="request_amount" class="form-control bg-light border-0" placeholder="e.g. 50000" min="5000" step="100" required>
                                        </div>
                                        <small class="text-muted mt-2 d-block">Minimum: ₦5,000.00 | Maximum: ₦500,000.00</small>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold">Repayment Plan</label>
                                        <select name="payment_plan" class="form-select bg-light border-0" required>
                                            <option value="">-- Select Repayment Plan --</option>
                                            <option value="weekly">Weekly (1 Month Duration)</option>
                                            <option value="biweekly">Bi-weekly (2 Months Duration)</option>
                                            <option value="monthly">Monthly (3 Months Duration)</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                            <label class="form-check-label small text-muted" for="termsCheck">
                                                I agree to the <a href="#" class="text-primary text-decoration-none">Loan Terms & Conditions</a> and authorize Arewa Smart to evaluate my account data.
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm">
                                            <i class="ti ti-rocket me-2"></i> Submit Application
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- History Section -->
                <div class="col-12 col-xl-5 mt-2 mt-md-0">
                    <div class="card border-0 shadow-sm overflow-hidden animate__animated animate__fadeInUp animate__delay-1s" style="border-radius: 12px;">
                        <div class="card-header bg-primary py-3">
                            <h5 class="fw-bold mb-0 text-white d-flex align-items-center gap-2">
                                <i class="ti ti-history fs-3"></i>
                                Application History
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0 px-4 py-3">Amount</th>
                                            <th class="border-0 py-3">Status</th>
                                            <th class="border-0 px-4 py-3 text-end">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($submissions as $sub)
                                            <tr>
                                                <td class="px-4 fw-bold">₦{{ number_format($sub->amount, 2) }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = match($sub->status) {
                                                            'successful', 'success' => 'success',
                                                            'pending' => 'warning',
                                                            'failed', 'rejected' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $statusClass }}-soft text-{{ $statusClass }} py-1 px-3 rounded-pill fw-bold" style="font-size: 10px;">
                                                        {{ ucfirst($sub->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 text-end small text-muted">
                                                    {{ $sub->created_at->format('M d, Y') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-5 text-muted">
                                                    <i class="ti ti-folder-off fs-1 d-block mb-3 opacity-25"></i>
                                                    No loan applications found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($submissions->hasPages())
                            <div class="card-footer bg-white border-0 py-3">
                                {{ $submissions->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
        .bg-primary-soft { background-color: rgba(13, 110, 253, 0.1); }
        .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
        .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
        
        .bg-gradient-primary {
            background: linear-gradient(90deg, #F26522 0%, #ff8c5a 100%);
        }

        .backdrop-blur {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

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

        .progress-bar-animated {
            animation: 2s linear infinite progress-bar-stripes, 2s ease-in-out infinite pulse;
        }
        
        @keyframes pulse {
            0% { opacity: 0.8; }
            50% { opacity: 1; }
            100% { opacity: 0.8; }
        }
    </style>
</x-app-layout>

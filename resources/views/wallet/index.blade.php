<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Wallet Funding' }}</title>
    
    <div class="container-fluid px-0 px-md-3">
        <div class="row justify-content-center py-3 py-lg-4 g-0 g-md-4">
            <div class="col-12 col-xl-11 col-xxl-10">
                <div class="row g-0 g-md-4 align-items-stretch">
                    
                    
                    <!-- Left Part: Marketing & Encouragement -->
                    <div class="col-12 col-xl-5 order-2 order-lg-1 mt-2 mt-md-0">
                        <div class="card border-0 overflow-hidden shadow-sm bg-dark text-white hero-card h-100" style="border-radius: 20px;">
                            <div class="card-body p-4 p-md-5 d-flex flex-column justify-content-center">
                                <h1 class="h2 h1-lg fw-bold mb-3 text-primary">Power Up Your Digital Life</h1>
                                <p class="lead mb-4 opacity-75 small">
                                    Keep your Arewa Smart wallet funded and never experience a "Low Balance" moment again. 
                                    Enjoy instant, uninterrupted access to Airtime, Data, and Utility bills at the best rates.
                                </p>
                                
                                <div class="mt-auto">
                                    <div class="d-flex flex-column gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-25 rounded-circle p-2 me-3">
                                                <i class="bi bi-lightning-charge-fill text-primary"></i>
                                            </div>
                                            <span class="fw-semibold small">Instant Wallet Credit</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-25 rounded-circle p-2 me-3">
                                                <i class="bi bi-shield-check text-success"></i>
                                            </div>
                                            <span class="fw-semibold small">Secure & Encrypted Payments</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info bg-opacity-25 rounded-circle p-2 me-3">
                                                <i class="bi bi-clock-history text-info"></i>
                                            </div>
                                            <span class="fw-semibold small">24/7 Service Availability</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 d-none d-lg-block">
                                    <div class="opacity-25 text-end">
                                        <i class="bi bi-wallet2 display-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Part: Automatic Wallet Funding -->
                    <div class="col-12 col-xl-7 order-1 order-lg-2">
                        <div class="card shadow border-0 overflow-hidden h-100" style="border-radius: 20px;">
                            <div class="card-header border-0 py-3 bg-gradient text-white" style="border-radius: 20px 20px 0 0;">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                        <i class="bi bi-bank fs-4"></i>
                                    </div>
                                    <h4 class="mb-0 fw-bold">Automatic Funding</h4>
                                </div>
                            </div>
                            
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <div class="payment-icon-wrapper mb-3">
                                        <i class="bi bi-send-check text-primary fs-1"></i>
                                    </div>
                                    <h5 class="fw-bold">How it Works</h5>
                                    <p class="text-muted small px-2 px-md-4">
                                        Transfer any amount to your assigned virtual account below. 
                                        Your wallet will be credited <strong>instantly</strong>.
                                    </p>
                                </div>

                                <div class="px-1 px-md-2">
                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible fade show small py-2" role="alert">
                                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif
                                    
                                    @if (session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show small py-2" role="alert">
                                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    @if($virtualAccount)
                                        <div class="bg-light p-3 mb-4" style="border-radius: 20px;">
                                            <div class="mb-3">
                                                <label class="form-label text-uppercase small fw-bold text-muted mb-1">Account Name</label>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person text-primary me-2"></i>
                                                    <span class="fw-bold">{{ $virtualAccount->accountName }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label text-uppercase small fw-bold text-muted mb-1">Account Number</label>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-hash text-primary me-2"></i>
                                                    <span class="fw-bold fs-20 text-primary me-2" id="accNo">{{ $virtualAccount->accountNo }}</span>
                                                    <button class="btn btn-sm btn-link text-primary p-0" type="button" onclick="copyToClipboard('{{ $virtualAccount->accountNo }}')">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-0">
                                                <label class="form-label text-uppercase small fw-bold text-muted mb-1">Bank Name</label>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-building text-primary me-2"></i>
                                                    <span class="fw-bold">{{ $virtualAccount->bankName }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center">
                                            <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill small">
                                                <i class="bi bi-clock me-1"></i> Instant delivery 24/7
                                            </span>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <div class="mb-3">
                                                <img src="assets/img/apps/thankyou.png" alt="bank" class="img-fluid" style="max-width: 80px;">
                                            </div>
                                            <h6 class="fw-bold">No Virtual Account Found</h6>
                                            <p class="text-muted small mb-4">Generate a dedicated account for instant funding.</p>
                                            <button type="button" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#virtualAccountModal">
                                                <i class="bi bi-plus-circle me-2"></i> Create Account
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Virtual Account Modal -->
    <div class="modal fade" id="virtualAccountModal" tabindex="-1" aria-labelledby="virtualAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable mx-2 mx-lg-auto">
            <div class="modal-content shadow" style="border-radius: 20px;">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Create Virtual Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <form method="POST" action="{{ route('virtual.account.create') }}" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" class="form-control" name="name" 
                                   value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }} {{ auth()->user()->middle_name }}" 
                                   required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label small fw-bold">Phone Number</label>
                            <input type="tel" class="form-control" name="phone" 
                                   value="{{ auth()->user()->phone_no }}" 
                                   required>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirmCheck" required>
                                <label class="form-check-label small" for="confirmCheck">
                                    I confirm that the above details are accurate and consent to create a virtual account.
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-send-fill me-2"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light p-3">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i> Your virtual account will be generated instantly and linked to your wallet.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show feedback
                const btn = event.currentTarget;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check2 text-success"></i>';
                
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>

</x-app-layout>
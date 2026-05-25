<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Wallet Funding' }}</title>
    
    <div class="container-fluid px-0 px-md-3">
        <div class="row justify-content-center py-3 py-lg-4 g-0 g-md-4">
            <div class="col-12 col-xl-11 col-xxl-10">
                <div class="row g-0 g-md-4 align-items-stretch">
                    
                    
                    <!-- Left Part: Marketing & Bonus -->

                                        <div class="col-12 col-xl-5 order-2 order-lg-1 mt-3 mt-xl-0">
                                            <div class="d-flex flex-column gap-3 h-100">

                            <!-- Referral/Activity Bonus Card -->
                            <div class="card border-0 overflow-hidden position-relative transition-all bonus-reward-card @if(isset($walletData) && $walletData['bonus'] > 0) has-bonus @else no-bonus @endif"
                                 style="min-height: 180px;">
                                <!-- Decorative circles -->
                                <div class="position-absolute" style="width: 150px; height: 150px; background: rgba(255,255,255,0.06); border-radius: 50%; top: -30px; right: -30px; pointer-events: none;"></div>
                                <div class="position-absolute" style="width: 100px; height: 100px; background: rgba(255,255,255,0.04); border-radius: 50%; bottom: -20px; left: -20px; pointer-events: none;"></div>

                                <div class="card-body p-4 d-flex flex-column justify-content-between position-relative z-index-1">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="icon-wrapper">
                                                <i class="bi bi-gift-fill fs-18"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0 text-title small">Rewards & Bonus</h6>
                                                <span class="badge badge-claimable rounded-pill" style="font-size: 10px;">Claimable Balance</span>
                                            </div>
                                        </div>
                                        <i class="bi bi-award-fill fs-2 text-title opacity-20"></i>
                                    </div>

                                    <div class="mb-4">
                                        <span class="small text-subtitle d-block mb-1">Your Accumulated Bonus Balance</span>
                                        <div class="d-flex align-items-baseline">
                                            <span class="fs-24 fw-bold me-1 text-amount">₦{{ number_format($walletData['bonus'] ?? 0, 2) }}</span>
                                        </div>
                                    </div>

                                    <div>
                                        @if(isset($walletData) && $walletData['bonus'] > 0)
                                            <form method="POST" action="{{ route('wallet.claimBonus') }}" id="claimBonusForm" class="no-loader">
                                                @csrf
                                                <button type="submit" class="btn claim-btn w-100 rounded-pill py-2-5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 transition-all hover-scale"
                                                        style="font-size: 14px;">
                                                    <i class="bi bi-wallet2"></i>
                                                    Claim & Transfer to Main Wallet
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn claim-btn w-100 rounded-pill py-2-5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2"
                                                    style="font-size: 14px;" disabled>
                                                <i class="bi bi-lock-fill"></i>
                                                No Bonus Available to Claim
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- NEW: Encouragement & Motivation Card -->
                            <div class="card border-0 overflow-hidden position-relative encouragement-card"
                                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
                                <div class="card-body p-4">
                                    <!-- Animated icon -->
                                    <div class="text-center mb-3">
                                        <div class="position-relative d-inline-block">
                                            <i class="bi bi-stars fs-1 text-white"></i>
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning" style="font-size: 10px;">
                                                🔥 HOT
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Main encouragement message -->
                                    <h5 class="text-white text-center fw-bold mb-2">
                                        🚀 Turn Transactions into Rewards!
                                    </h5>

                                    <p class="text-white-50 text-center small mb-3">
                                        Every transaction brings you closer to amazing bonuses
                                    </p>

                                    <!-- Bonus tiers/progress -->
                                    <div class="bg-white bg-opacity-10 rounded-3 p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-white-50 small">Next Bonus Target</span>
                                            <span class="text-white fw-bold small">
                                                ₦{{ number_format($nextBonusTarget ?? 50000, 2) }}
                                            </span>
                                        </div>
                                        <div class="progress mb-2" style="height: 8px; background: rgba(255,255,255,0.2);">
                                            <div class="progress-bar bg-warning" role="progressbar"
                                                 style="width: {{ $bonusProgress ?? 0 }}%;"
                                                 aria-valuenow="{{ $bonusProgress ?? 0 }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100"></div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-white-50 small">Current: ₦{{ number_format($currentSpend ?? 0, 2) }}</span>
                                            <span class="text-white-50 small">Goal: ₦{{ number_format($bonusTarget ?? 50000, 2) }}</span>
                                        </div>
                                    </div>

                                    <!-- Bonus benefits list -->
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="bg-white bg-opacity-10 rounded-2 p-2 text-center">
                                                <i class="bi bi-currency-exchange text-warning me-1" style="font-size: 12px;"></i>
                                                <small class="text-white">5-10% Cashback</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="bg-white bg-opacity-10 rounded-2 p-2 text-center">
                                                <i class="bi bi-trophy-fill text-warning me-1" style="font-size: 12px;"></i>
                                                <small class="text-white">Exclusive Badges</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Call to action buttons -->
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('transactions') }}" class="btn btn-light rounded-pill fw-bold py-2 shadow-sm hover-scale"
                                           style="background: white; color: #667eea; transition: transform 0.2s;">
                                            <i class="bi bi-plus-circle-fill me-2"></i>
                                            Start a Transaction Now!
                                        </a>
                                        <a href="#" class="btn btn-outline-light rounded-pill py-2 small">
                                            <i class="bi bi-info-circle me-1"></i>
                                            How to earn more bonuses?
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick stats: What you're missing -->
                            @if(!isset($walletData) || $walletData['bonus'] == 0)
                            <div class="alert alert-warning bg-warning bg-opacity-10 border-0 rounded-3 py-2 px-3 fade show" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                    <small class="text-warning">
                                        💡 <strong>Pro tip:</strong> Complete just ₦10,000 more in transactions to unlock ₦500 bonus!
                                    </small>
                                </div>
                            </div>
                            @endif

                            <!-- Success message for active users -->
                            @if(isset($walletData) && $walletData['bonus'] > 500)
                            <div class="alert alert-success bg-success bg-opacity-10 border-0 rounded-3 py-2 px-3 fade show" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-emoji-laughing-fill text-success me-2"></i>
                                    <small class="text-success">
                                        🎉 Amazing! You've earned <strong>₦{{ number_format($walletData['bonus'] ?? 0, 2) }}</strong> in bonuses this month!
                                    </small>
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>



                    <!-- Right Part: Automatic Wallet Funding -->
                    <div class="col-12 col-xl-7 order-1 order-lg-2">
                        <div class="card shadow border-0 overflow-hidden h-100" style="border-radius: 20px;">
                            <div class="card-header border-0 py-3 bg-gradient text-white" style="border-radius: 20px 20px 0 0;">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                        <i class="bi bi-bank fs-14"></i>
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

                                    <!-- Warning Alert -->
                                    <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 15px; background-color: rgba(255, 193, 7, 0.1);">
                                        <div class="d-flex align-items-start align-items-md-center">
                                            <div class="bg-warning bg-opacity-25 rounded-circle p-2 me-3">
                                                <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">Important: Avoid Same-Amount Transfers</h6>
                                                <p class="mb-0 small text-muted" style="line-height: 1.4;">
                                                    To avoid funding delays, please do not make multiple transfers of the <strong>exact same amount</strong> within 3 minutes. Use slightly different amounts (e.g., ₦1,000 and ₦1,001) for instant credit.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

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
    <style>
        /* ==================== Encouragement & Hover Animations ==================== */
/* Hover effects */
.hover-scale:hover {
    transform: scale(0.98);
    transition: transform 0.2s ease;
}

.encouragement-card {
    animation: glow 2s ease-in-out infinite alternate;
}

@keyframes glow {
    from {
        box-shadow: 0 0 10px rgba(102, 126, 234, 0.3);
    }
    to {
        box-shadow: 0 0 20px rgba(102, 126, 234, 0.6);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .encouragement-card .card-body {
        padding: 1rem;
    }
    
    .encouragement-card h5 {
        font-size: 1rem;
    }
    
    .bg-white.bg-opacity-10 {
        padding: 0.5rem !important;
    }
}

/* Button pulse animation */
.btn-light:hover {
    animation: pulse 0.5s ease;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

        /* ==================== Referral & Activity Bonus Card Premium Theme ==================== */
        .bonus-reward-card {
            border-radius: 20px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.04), 0 6px 6px -6px rgba(0, 0, 0, 0.04) !important;
        }

        /* ── LIGHT MODE STYLING ── */
        .bonus-reward-card.has-bonus {
            background: linear-gradient(135deg, #FFF9E6 0%, #FFEFC2 100%) !important;
            border: 1px solid rgba(245, 158, 11, 0.18) !important;
        }
        .bonus-reward-card.no-bonus {
            background: linear-gradient(135deg, #F8F9FA 0%, #E9ECEF 100%) !important;
            border: 1px solid rgba(0, 0, 0, 0.04) !important;
        }

        .bonus-reward-card.has-bonus .text-title { color: #854d0e !important; }
        .bonus-reward-card.has-bonus .text-subtitle { color: #a16207 !important; }
        .bonus-reward-card.has-bonus .text-amount { color: #d97706 !important; }
        .bonus-reward-card.has-bonus .badge-claimable { background: rgba(217, 119, 6, 0.1) !important; color: #d97706 !important; }
        .bonus-reward-card.has-bonus .icon-wrapper { background: #F59E0B !important; color: #FFFFFF !important; }

        .bonus-reward-card.no-bonus .text-title { color: #495057 !important; }
        .bonus-reward-card.no-bonus .text-subtitle { color: #6c757d !important; }
        .bonus-reward-card.no-bonus .text-amount { color: #212529 !important; }
        .bonus-reward-card.no-bonus .badge-claimable { background: rgba(0, 0, 0, 0.06) !important; color: #495057 !important; }
        .bonus-reward-card.no-bonus .icon-wrapper { background: #6C757D !important; color: #FFFFFF !important; }

        .bonus-reward-card .icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
        }

        .bonus-reward-card.has-bonus .claim-btn {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%) !important;
            color: #FFFFFF !important;
            border: none !important;
            box-shadow: 0 4px 10px rgba(217, 119, 6, 0.2) !important;
        }
        .bonus-reward-card.has-bonus .claim-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 15px rgba(217, 119, 6, 0.3) !important;
        }

        .bonus-reward-card.no-bonus .claim-btn {
            background: rgba(0, 0, 0, 0.03) !important;
            color: #6C757D !important;
            border: 1px dashed rgba(0, 0, 0, 0.12) !important;
            cursor: not-allowed !important;
        }

        /* ── DARK MODE THEME OVERRIDES ── */
        [data-theme="dark"] .bonus-reward-card {
            border: 1px solid rgba(255, 255, 255, 0.03) !important;
            box-shadow: none !important;
        }
        [data-theme="dark"] .bonus-reward-card.has-bonus {
            background: linear-gradient(135deg, #2D1D02 0%, #1A0F00 100%) !important;
            border: 1px solid rgba(245, 158, 11, 0.22) !important;
        }
        [data-theme="dark"] .bonus-reward-card.no-bonus {
            background: linear-gradient(135deg, #161B22 0%, #0D1117 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.02) !important;
        }

        [data-theme="dark"] .bonus-reward-card.has-bonus .text-title { color: #fef3c7 !important; }
        [data-theme="dark"] .bonus-reward-card.has-bonus .text-subtitle { color: #fde68a !important; }
        [data-theme="dark"] .bonus-reward-card.has-bonus .text-amount { color: #f59e0b !important; }
        [data-theme="dark"] .bonus-reward-card.has-bonus .badge-claimable { background: rgba(245, 158, 11, 0.15) !important; color: #fde68a !important; }
        [data-theme="dark"] .bonus-reward-card.has-bonus .icon-wrapper { background: #F59E0B !important; color: #000000 !important; }

        [data-theme="dark"] .bonus-reward-card.no-bonus .text-title { color: #c9d1d9 !important; }
        [data-theme="dark"] .bonus-reward-card.no-bonus .text-subtitle { color: #8b949e !important; }
        [data-theme="dark"] .bonus-reward-card.no-bonus .text-amount { color: #ffffff !important; }
        [data-theme="dark"] .bonus-reward-card.no-bonus .badge-claimable { background: rgba(255, 255, 255, 0.04) !important; color: #8b949e !important; }
        [data-theme="dark"] .bonus-reward-card.no-bonus .icon-wrapper { background: #30363d !important; color: #8b949e !important; }

        [data-theme="dark"] .bonus-reward-card.no-bonus .claim-btn {
            background: rgba(255, 255, 255, 0.02) !important;
            color: rgba(255, 255, 255, 0.38) !important;
            border: 1px dashed rgba(255, 255, 255, 0.08) !important;
        }
    </style>

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
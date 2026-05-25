<!-- Quick Services Section -->
<section class="py-1 py-md-2">
        <div class="card border-0 shadow-lg overflow-hidden dashboard-card" style="border-radius: 30px !important;">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="fas fa-bolt text-warning"></i>
                    <h5 class="fw-bold mb-0">Quick Services</h5>
                </div>
                <p class="text-muted small mb-3 mb-md-4">Instant access to popular payments</p>

                @php
                    $services = [
                        ['route' => route('wallet'), 'icon' => 'ti-wallet', 'color' => 'primary', 'name' => 'Wallet'],
                        ['route' => route('withdraw.index'), 'icon' => 'ti ti-arrow-up-right', 'color' => 'success', 'name' => 'Transfer', 'hot' => true],
                        ['route' => route('airtime'), 'icon' => 'ti-phone-call', 'color' => 'info', 'name' => 'Airtime', 'hot' => true],
                        ['modal' => '#dataplans', 'icon' => 'ti-world', 'color' => 'warning', 'name' => 'Data', 'hot' => true],
                        ['route' => route('gift-card.index'), 'icon' => 'ti-gift', 'color' => 'info', 'name' => 'Gift Card', 'hot' => true],
                        ['route' => route('electricity'), 'icon' => 'ti-bolt', 'color' => 'danger', 'name' => 'Electricity'],
                        ['route' => route('cable'), 'icon' => 'ti-device-tv', 'color' => 'success', 'name' => 'Cable TV', 'hot' => true],
                        ['route' => route('education'), 'icon' => 'ti-school', 'color' => 'success', 'name' => 'Education'],
                        ['route' => route('jamb'), 'icon' => 'ti-certificate', 'color' => 'secondary', 'name' => 'Jamb Pin'],
                        ['route' => route('bvn-crm'), 'icon' => 'ti-users', 'color' => 'info', 'name' => 'CRM', 'hot' => true],
                        ['route' => route('send-vnin'), 'icon' => 'ti-fingerprint', 'color' => 'warning', 'name' => 'Vnin/Fas'],
                        ['route' => route('modification'), 'icon' => 'ti-user-edit', 'color' => 'danger', 'name' => 'BVN Mod', 'hot' => true],
                        ['route' => route('phone.search.index'), 'icon' => 'ti-search', 'color' => 'success', 'name' => 'Search BVN'],
                        ['modal' => '#agentEnrolmentModal', 'icon' => 'ti-user-plus', 'color' => 'secondary', 'name' => 'BVN Agent'],
                        ['route' => route('nin-modification'), 'icon' => 'ti-id', 'color' => 'info', 'name' => 'NIN Mod'],
                        ['route' => route('nin-validation.index'), 'icon' => 'ti-checkbox', 'color' => 'warning', 'name' => 'NIN Validation'],
                        ['route' => route('ipe-validation.index'), 'icon' => 'ti-clear-formatting', 'color' => 'info', 'name' => 'IPE'],
                        ['route' => route('website.index'), 'icon' => 'ti-browser', 'color' => 'danger', 'name' => 'Website'],
                        ['route' => route('cac.index'), 'icon' => 'ti-briefcase', 'color' => 'success', 'name' => 'CAC Reg'],
                        ['route' => route('enrolment.report'), 'icon' => 'ti-file-text', 'color' => 'secondary', 'name' => 'Report'],
                        ['modal' => '#verifyModal', 'icon' => 'ti-id-badge', 'color' => 'info', 'name' => 'Verify NIN'],
                        ['modal' => '#verifyModalbvn', 'icon' => 'ti-shield-check', 'color' => 'secondary', 'name' => 'Verify BVN'],
                        ['route' => route('loan.index'), 'icon' => 'ti-checkbox', 'color' => 'warning', 'name' => 'Loan', 'hot' => true],
                        ['route' => route('support.index'), 'icon' => 'ti-message-plus', 'color' => 'primary', 'name' => 'Support', 'hot' => true],
                    ];
                @endphp

                {{-- ── Service Grid ── --}}
                <div class="row row-cols-4 row-cols-md-4 row-cols-lg-6 g-2 g-md-3 text-center" id="services-grid">

                    @foreach ($services as $index => $sv)
                        {{-- On mobile: hide items beyond first 8. On desktop: always show everything --}}
                        <div class="col {{ $index >= 8 ? 'svc-extra d-none d-lg-block' : '' }}">
                            <a 
                                @if(isset($sv['route'])) href="{{ $sv['route'] }}" 
                                @elseif(isset($sv['modal'])) href="#" data-bs-toggle="modal" data-bs-target="{{ $sv['modal'] }}"
                                @else href="#"
                                @endif
                                class="text-decoration-none service-item"
                            >
                                <div class="service-content p-2 p-md-3 position-relative">
                                    @if(isset($sv['hot']) && $sv['hot'])
                                        <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger text-white border border-white" style="font-size: 8px; padding: 3px 6px; z-index: 1; margin-top: 10px; margin-right: 2px; animation: pulse-red 2s infinite;">
                                            HOT
                                        </span>
                                    @endif
                                    <div class="service-icon mb-2 mx-auto bg-{{ $sv['color'] }}-soft">
                                        <i class="ti {{ $sv['icon'] }} fs-15 fs-md-4 text-{{ $sv['color'] }}"></i>
                                    </div>
                                    <span class="service-name text-dark">{{ $sv['name'] }}</span>
                                </div>
                            </a>
                        </div>
                    @endforeach

                </div>

                {{-- ── View More / Less Button (Mobile Only) ── --}}
                <div class="text-center mt-3 d-lg-none">
                    <button id="svc-toggle-btn" type="button"
                        class="btn btn-svc-toggle px-4 py-2">
                        <span id="svc-toggle-label">
                            <i class="ti ti-layout-grid-add me-1"></i>
                            View More Services
                            <i class="ti ti-chevron-down ms-1 svc-chevron"></i>
                        </span>
                    </button>
                </div>
            </div>
        </div>
</section>
<!-- /Quick Services Section -->

<!-- Agent Enrolment Modal - Enhanced -->
<div class="modal fade" id="agentEnrolmentModal" tabindex="-1" aria-labelledby="agentEnrolmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            
            <!-- Header with gradient -->
            <div class="modal-header bg-gradient-primary text-white p-4 border-0 position-relative">
                <div class="pe-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="agentEnrolmentModalLabel">
                        <span class="badge bg-white text-primary p-2 rounded-3">
                            <i class="ti ti-id-badge fs-1"></i>
                        </span>
                        Become a Certified BVN Agent
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4">
                <!-- Hero Section -->
                <div class="text-center mb-4">
                    <div class="display-6 mb-2">🌟</div>
                    <p class="lead fs-1 fw-medium mb-0">Join thousands of trusted agents across Nigeria</p>
                    <p class="text-primary fw-semibold">Start earning instantly!</p>
                </div>

                <!-- Requirements Card -->
                <div class="card border-0 bg-light mb-4">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                            <span class="badge bg-danger rounded-3 p-2">
                                <i class="ti ti-checklist fs-1 text-white"></i>
                            </span>
                            Requirements
                        </h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="d-flex align-items-start gap-2 mb-2">
                                    <i class="ti ti-check text-success fs-5 flex-shrink-0"></i>
                                    <span class="small">Valid BVN, active bank account & government-issued ID</span>
                                </div>
                                <div class="d-flex align-items-start gap-2 mb-2">
                                    <i class="ti ti-check text-success fs-5 flex-shrink-0"></i>
                                    <span class="small">Working phone & email (unused on any BVN enrolment)</span>
                                </div>
                                <div class="d-flex align-items-start gap-2 mb-2">
                                    <i class="ti ti-check text-success fs-5 flex-shrink-0"></i>
                                    <span class="small">Business location (optional but recommended)</span>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <i class="ti ti-check text-success fs-5 flex-shrink-0"></i>
                                    <span class="small">Minimum ₦10,000 activation balance in wallet</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Benefits Grid -->
                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <span class="badge bg-success rounded-3 p-2">
                        <i class="ti ti-gift fs-6 text-white"></i>
                    </span>
                    Agent Benefits
                </h6>
                
                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <div class="benefit-item p-2">
                            <i class="ti ti-coin text-warning fs-5"></i>
                            <span class="small d-block mt-1">Attractive commissions</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="benefit-item p-2">
                            <i class="ti ti-shield text-primary fs-5"></i>
                            <span class="small d-block mt-1">Verified service provider</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="benefit-item p-2">
                            <i class="ti ti-rocket text-danger fs-5"></i>
                            <span class="small d-block mt-1">Priority support</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="benefit-item p-2">
                            <i class="ti ti-clock text-info fs-5"></i>
                            <span class="small d-block mt-1">48h BVN approval</span>
                        </div>
                    </div>
                </div>

                <!-- CTA Alert -->
                <div class="alert alert-success text-center py-3 mb-0 rounded-3">
                    <i class="ti ti-star-filled me-1"></i>
                    <strong>Start your enrollment today!</strong>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer p-3 border-0 bg-light">
                <a href="{{ route('bvn.index') }}" class="btn btn-primary w-100 py-3 fw-semibold rounded-3">
                    Start Registration
                    <i class="ti ti-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Verify Modal - Enhanced -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            
            <div class="modal-header bg-primary text-white p-3 border-0">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="verifyModalLabel">
                    <i class="ti ti-id-badge fs-1"></i>
                    Identity Verification
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <span class="badge bg-primary-soft text-primary p-3 rounded-4 mb-2">
                        <i class="ti ti-fingerprint fs-1"></i>
                    </span>
                    <h6 class="fw-bold">Choose Verification Type</h6>
                </div>

                @php
                    $verifyServices = [
                        [
                            'route' => route('nin.phone.index'),
                            'icon' => 'ti-phone',
                            'color' => 'success',
                            'name' => 'NIN Phone No.',
                            'desc' => 'Verify phone with NIN'
                        ],
                        [
                            'route' => route('nin.verification.index'),
                            'icon' => 'ti-id',
                            'color' => 'primary',
                            'name' => 'Verify NIN',
                            'desc' => 'NIN validation'
                        ],
                        [
                            'route' => route('nin.demo.index'),
                            'icon' => 'ti-file-text',
                            'color' => 'warning',
                            'name' => 'NIN Demo',
                            'desc' => 'Demo verification'
                        ],
                    ];
                @endphp

                <div class="row g-3">
                    @foreach ($verifyServices as $sv)
                        <div class="col-4">
                            <a href="{{ $sv['route'] }}" class="text-decoration-none">
                                <div class="card border-0 bg-light-soft h-100">
                                    <div class="card-body p-2 text-center">
                                        <span class="badge bg-{{ $sv['color'] }}-soft text-{{ $sv['color'] }} p-2 rounded-3 mb-2">
                                            <i class="ti {{ $sv['icon'] }} fs-1"></i>
                                        </span>
                                        <h6 class="small fw-bold mb-0">{{ $sv['name'] }}</h6>
                                        <small class="text-muted d-none d-md-block">{{ $sv['desc'] }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="modal-footer border-0 p-3 justify-content-center">
                <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Verify BVN/TIN Modal - Enhanced -->
<div class="modal fade" id="verifyModalbvn" tabindex="-1" aria-labelledby="verifyModalLabelBvn" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            
            <div class="modal-header bg-primary text-white p-3 border-0">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="verifyModalLabelBvn">
                    <i class="ti ti-shield fs-1"></i>
                    BVN / TIN Verification
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <span class="badge bg-primary-soft text-primary p-3 rounded-4 mb-2">
                        <i class="ti ti-file-certificate fs-1"></i>
                    </span>
                    <h6 class="fw-bold">Select Verification Service</h6>
                </div>

                @php
                    $verifyServicesBvn = [
                        [
                            'route' => route('bvn.verification.index'),
                            'icon' => 'ti-fingerprint',
                            'color' => 'success',
                            'name' => 'Verify BVN',
                            'desc' => 'Bank Verification Number'
                        ],
                        [
                            'route' => route('cac.tin'),
                            'icon' => 'ti-building',
                            'color' => 'danger',
                            'name' => 'Verify TIN',
                            'desc' => 'Tax Identification Number'
                        ],
                    ];
                @endphp

                <div class="row g-3 justify-content-center">
                    @foreach ($verifyServicesBvn as $sv)
                        <div class="col-6">
                            <a href="{{ $sv['route'] }}" class="text-decoration-none">
                                <div class="card border-0 bg-light-soft">
                                    <div class="card-body p-3 text-center">
                                        <span class="badge bg-{{ $sv['color'] }}-soft text-{{ $sv['color'] }} p-3 rounded-4 mb-2">
                                            <i class="ti {{ $sv['icon'] }} fs-2"></i>
                                        </span>
                                        <h6 class="fw-bold mb-1">{{ $sv['name'] }}</h6>
                                        <small class="text-muted">{{ $sv['desc'] }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="modal-footer border-0 p-3 justify-content-center">
                <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Data Plans Modal - Enhanced -->
<div class="modal fade" id="dataplans" tabindex="-1" aria-labelledby="dataplansLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            
            <div class="modal-header bg-primary text-white p-3 border-0">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="dataplansLabel">
                    <i class="ti ti-world fs-1"></i>
                    Data Plans
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <span class="badge bg-primary-soft text-primary p-3 rounded-4 mb-2">
                        <i class="ti ti-wifi fs-1"></i>
                    </span>
                    <h6 class="fw-bold">Choose Data Plan Type</h6>
                </div>

                @php
                    $dataPlans = [
                        [
                            'route' => route('buy-data'),
                            'icon' => 'ti-world',
                            'color' => 'success',
                            'name' => 'Direct Data',
                            'desc' => 'Regular data plans'
                        ],
                        [
                            'route' => route('buy-sme-data'),
                            'icon' => 'ti-building',
                            'color' => 'info',
                            'name' => 'SME Data',
                            'desc' => 'Business data plans'
                        ],
                    ];
                @endphp

                <div class="row g-3">
                    @foreach ($dataPlans as $plan)
                        <div class="col-6">
                            <a href="{{ $plan['route'] }}" class="text-decoration-none">
                                <div class="card border-0 bg-light-soft">
                                    <div class="card-body p-3 text-center">
                                        <span class="badge bg-{{ $plan['color'] }}-soft text-{{ $plan['color'] }} p-3 rounded-4 mb-2">
                                            <i class="ti {{ $plan['icon'] }} fs-2"></i>
                                        </span>
                                        <h6 class="fw-bold mb-1">{{ $plan['name'] }}</h6>
                                        <small class="text-muted">{{ $plan['desc'] }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="modal-footer border-0 p-3 justify-content-center">
                <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Premium Quick Services Styling */
:root {
    --service-icon-size: 60px;
    --service-icon-size-md: 70px;
    --service-icon-size-sm: 50px;
    --transition-smooth: all 0.3s ease;
}

/* Service Items */
.service-item {
    display: block;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}

.service-content {
    transition: var(--transition-smooth);
    border-radius: 20px;
}

.service-icon {
    width: var(--service-icon-size-sm);
    height: var(--service-icon-size-sm);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition-smooth);
    flex-shrink: 0;
}

.service-icon i {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Icon Backgrounds */
.bg-primary-soft { background-color: rgba(13, 110, 253, 0.1); }
.bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
.bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
.bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
.bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
.bg-secondary-soft { background-color: rgba(108, 117, 125, 0.1); }
.bg-light-soft { background-color: rgba(248, 249, 250, 0.8); }
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.service-name {
    font-size: 11px;
    font-weight: 500;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.2;
    min-height: 2.4em; /* Standardize height for 2 lines */
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Modal Benefits Grid */
.benefit-item {
    background: #f8f9fa;
    border-radius: 12px;
    text-align: center;
    transition: var(--transition-smooth);
}

.benefit-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

/* Active/Tap States */
.service-item:active .service-content {
    transform: scale(0.95);
    background-color: rgba(0, 0, 0, 0.02);
}

/* Hover Effects - Desktop */
@media (min-width: 768px) {
    .service-item:hover .service-content {
        transform: translateY(-3px);
    }
    
    .service-item:hover .service-icon {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .benefit-item {
        padding: 12px !important;
    }
}

/* Tablet */
@media (min-width: 576px) and (max-width: 767px) {
    .service-icon {
        width: var(--service-icon-size);
        height: var(--service-icon-size);
    }
    
    .service-name {
        font-size: 13px;
    }
}

@media (max-width: 575px) {
    .service-icon {
        width: 45px;
        height: 45px;
        border-radius: 15px;
    }
    
    .service-icon i {
        font-size: 20px !important;
    }
    
    .service-name {
        font-size: 9px;
        letter-spacing: -0.2px;
        min-height: 2.4em;
    }
    
    .service-content {
        padding: 6px 2px !important;
    }
    
    /* Modal mobile optimizations */
    .modal-dialog {
        margin: 10px;
    }
    
    .benefit-item {
        padding: 8px !important;
    }
    
    .benefit-item i {
        font-size: 18px !important;
    }
    
    .benefit-item span {
        font-size: 11px !important;
    }
}

/* Small phones */
@media (max-width: 360px) {
    .service-name {
        font-size: 9px;
    }
    
    .service-icon {
        width: 40px;
        height: 40px;
    }
    
    .service-icon i {
        font-size: 18px !important;
    }
}

/* Modal Animations */
.modal.fade .modal-dialog {
    transform: scale(0.95);
    transition: transform 0.2s ease-out;
}

.modal.show .modal-dialog {
    transform: scale(1);
}

/* Custom Scrollbar */
.modal-dialog-scrollable .modal-body::-webkit-scrollbar {
    width: 5px;
}

.modal-dialog-scrollable .modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb:hover {
    background: #555;
}
@keyframes pulse-red {
    0% { transform: translate(50%, -50%) scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { transform: translate(50%, -50%) scale(1); box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
    100% { transform: translate(50%, -50%) scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}

/* ── View More Button ── */
.btn-svc-toggle {
    background: linear-gradient(135deg, rgba(99,102,241,0.10), rgba(99,102,241,0.06));
    color: #6366f1;
    border: 1.5px dashed rgba(99,102,241,0.35);
    border-radius: 14px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.2px;
    transition: all 0.25s ease;
}
.btn-svc-toggle:hover {
    background: linear-gradient(135deg, rgba(99,102,241,0.18), rgba(99,102,241,0.10));
    border-color: #6366f1;
    color: #4f52e0;
    transform: translateY(-1px);
}
.btn-svc-toggle:active {
    transform: translateY(0);
}
.svc-chevron {
    display: inline-block;
    transition: transform 0.3s ease;
}
.svc-expanded .svc-chevron {
    transform: rotate(180deg);
}
/* Animate extra items in */
.svc-extra {
    animation: fadeInUp 0.25s ease both;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

<script>
(function () {
    const btn   = document.getElementById('svc-toggle-btn');
    const label = document.getElementById('svc-toggle-label');
    const extras = document.querySelectorAll('.svc-extra');
    let expanded = false;

    if (!btn) return;

    btn.addEventListener('click', function () {
        expanded = !expanded;

        extras.forEach(function (el) {
            if (expanded) {
                // Show: remove d-none but keep d-lg-block so desktop is unaffected
                el.classList.remove('d-none');
            } else {
                el.classList.add('d-none');
            }
        });

        btn.classList.toggle('svc-expanded', expanded);

        label.innerHTML = expanded
            ? '<i class="ti ti-layout-grid me-1"></i> Show Less <i class="ti ti-chevron-up ms-1 svc-chevron"></i>'
            : '<i class="ti ti-layout-grid-add me-1"></i> View More Services <i class="ti ti-chevron-down ms-1 svc-chevron"></i>';
    });
})();
</script>
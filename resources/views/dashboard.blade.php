<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Dashboard' }}</title>

    @if(isset($announcement) && $announcement)
        <div class="notification-container mt-3 mb-2">
            <div class="scrolling-text-container bg-primary text-white shadow-sm py-2" style="border-radius: 20px;">
                <div class="scrolling-text">
                    <span class="fw-bold me-3"><i class="fas fa-bullhorn"></i> ANNOUNCEMENT:</span>
                    {{ $announcement->message }}
                </div>
            </div>
        </div>
    @endif

    @push('styles')
        <style>
            .notification-container {
                overflow: hidden;
                width: 100%;
            }
            .scrolling-text-container {
                width: 100%;
                overflow: hidden;
                white-space: nowrap;
                position: relative;
            }
            .scrolling-text {
                display: inline-block;
                padding-left: 100%;
                animation: scroll-left 15s linear infinite;
            }
            @keyframes scroll-left {
                0% { transform: translateX(0); }
                100% { transform: translateX(-100%); }
            }
            .scrolling-text-container:hover .scrolling-text {
                animation-play-state: paused;
            }

            /* Action Wallet Card */
            .wallet-action-card {
                border-radius: 25px !important;
                border: none !important;
                box-shadow: 0 5px 20px rgba(0,0,0,0.05) !important;
                background: #fff !important;
            }
            .action-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-decoration: none !important;
                transition: all 0.3s ease;
            }
            .action-item:hover {
                transform: translateY(-3px);
            }
            .action-icon-box {
                width: 50px;
                height: 50px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
                margin-bottom: 10px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.03);
            }
            .action-label {
                font-size: 11px;
                font-weight: 600;
                color: #4b5563;
                letter-spacing: 0.2px;
            }
            .wallet-footer {
                border-top: 1px solid #f3f4f6;
                padding-top: 20px;
                margin-top: 5px;
            }
            .balance-title {
                color: #374151;
                font-weight: 600;
                font-size: 14px;
            }
            .balance-val {
                font-weight: 800;
                color: #111827;
                font-size: 20px;
                letter-spacing: -0.5px;
            }
        </style>
    @endpush

    @php
        $hour = date('H');
        $timeGreeting = 'Morning';
        if ($hour >= 12 && $hour < 17) {
            $timeGreeting = 'Afternoon';
        } elseif ($hour >= 17) {
            $timeGreeting = 'Evening';
        }
        
        $user = Auth::user();
    @endphp

    <div class="container-fluid px-0 px-md-3 mt-3">
        
        <!-- Welcome Greeting -->
        <div class="row mb-3 px-3">
            <div class="col-12">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ Auth::user()->photo ?? asset('assets/img/profiles/avatar-31.jpg') }}"
                         class="rounded-circle border border-2 border-primary shadow-sm"
                         style="width: 45px; height: 45px; object-fit: cover;"
                         alt="User Avatar">
                    <div>
                        <h5 class="fw-bold text-dark mb-0 fs-15">
                            Hello! {{ $timeGreeting }}, {{ Auth::user()->first_name ?? 'BOSS' }} 👋
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. Action Wallet Card -->
        <div class="row g-0">
            <div class="col-12 px-0 px-md-3">
                <div class="card wallet-action-card mb-4">
                    <div class="card-body p-4 p-md-5">
                        
                        <!-- Top Actions -->
                        <div class="row g-0 text-center">
                            <div class="col-3">
                                <a href="{{ route('wallet') }}" class="action-item">
                                    <div class="action-icon-box bg-info bg-opacity-10 text-info">
                                        <i class="ti ti-wallet"></i>
                                    </div>
                                    <span class="action-label">Deposit</span>
                                </a>
                            </div>
                            <div class="col-3">
                                <a href="{{ route('withdraw.index') }}" class="action-item">
                                    <div class="action-icon-box bg-success bg-opacity-10 text-success">
                                        <i class="ti ti-arrow-up-right"></i>
                                    </div>
                                    <span class="action-label">Withdraw</span>
                                </a>
                            </div>
                            <div class="col-3">
                                <a href="{{ route('airtime') }}" class="action-item">
                                    <div class="action-icon-box bg-danger bg-opacity-10 text-danger">
                                        <i class="ti ti-phone"></i>
                                    </div>
                                    <span class="action-label">Airtime</span>
                                </a>
                            </div>
                            <div class="col-3">
                                <a href="{{ route('buy-data') }}" class="action-item">
                                    <div class="action-icon-box bg-warning bg-opacity-10 text-primary">
                                        <i class="ti ti-wifi"></i>
                                    </div>
                                    <span class="action-label">Buy Data</span>
                                </a>
                            </div>
                        </div>

                        <!-- Bottom Balance -->
                        <div class="wallet-footer d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="balance-title">Balance:</span>
                                <button id="toggle-balance" class="btn btn-link text-primary p-0 border-0 shadow-none">
                                    <i class="fas fa-eye eye-icon fs-14"></i>
                                </button>
                            </div>
                            <span id="wallet-balance" class="balance-val text-primary">
                                ₦{{ number_format($wallet->balance ?? 0, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Alerts Section -->
        <div class="row g-0 mt-n2">
            <div class="col-12 px-0 px-md-3">
                @include('pages.alart')
            </div>
        </div>

        <!-- 3. Quick Services Section -->
        <div class="row g-0 mt-n2">
            <div class="col-12 px-0 px-md-3">
                @include('pages.dashboard.services')
            </div>
        </div>

        <!-- 4. Advertisement Section -->
        <div class="row g-0 mt-n2">
            <div class="col-12 px-0 px-md-3">
                @include('pages.dashboard.advert')
            </div>
        </div>

        <!-- 5. Transactions & Statistics Section -->
        <div class="row g-0 mt-n5 d-none d-lg-flex">
            <div class="col-12 px-0 px-md-3">
                @include('pages.dashboard.trans')
            </div>
        </div>
    </div>
</x-app-layout>
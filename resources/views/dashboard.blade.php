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

        /* Deep Rounding Class for Dashboard Sections */
        .dashboard-card {
            border-radius: 30px !important;
            overflow: hidden !important;
            border: none !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
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

    <div class="container-fluid px-0 px-md-3 mt-4">
        
        <!-- 1. Wallet Section -->
        <div class="row g-0">
            <div class="col-12 px-0 px-md-3">
                <div class="card dashboard-card mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ Auth::user()->photo ?? asset('assets/img/profiles/avatar-31.jpg') }}"
                                         class="rounded-circle border border-2 border-primary shadow-sm user-avatar"
                                         style="width: 50px; height: 50px; object-fit: cover;"
                                         alt="User Avatar">
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0 welcome-text">
                                         {{ $timeGreeting }}, {{ Auth::user()->first_name ?? 'BOSS' }} 👋
                                    </h5>
                                    @if($virtualAccount)
                                        <small class="text-success fw-medium d-flex align-items-center gap-1">
                                            <i class="ti ti-building-bank"></i>
                                            {{ $virtualAccount->accountNo }}
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2 bg-light px-3 py-2 rounded-pill shadow-sm">
                                <h4 id="wallet-balance" class="mb-0 @if(($wallet->status ?? 'inactive') == 'active') text-success @else text-danger @endif fw-bold balance-text">
                                    ₦{{ number_format($wallet->balance ?? 0, 2) }}
                                </h4>
                                <div class="d-flex gap-1">
                                    <button id="toggle-balance" class="btn btn-link text-muted p-0 toggle-btn" title="Toggle balance">
                                        <i class="fas fa-eye eye-icon"></i>
                                    </button>
                                    <a href="{{ route('wallet') }}" class="btn btn-link text-primary p-0 ms-1" title="View Wallet">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
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

        <!-- 3. Mini Widgets (Desktop Only) -->
        <div class="row g-0 mt-n2 d-none d-lg-flex">
            <div class="col-12 px-0 px-md-3">
                @include('pages.dashboard.wedget')
            </div>
        </div>

        <!-- 4. Quick Services Section -->
        <div class="row g-0 mt-n2">
             <div class="col-12 px-0 px-md-3">
                @include('pages.dashboard.services')
             </div>
        </div>

        <!-- 5. Advertisement Section -->
        <div class="row g-0 mt-n3">
             <div class="col-12 px-0 px-md-3">
                @include('pages.dashboard.advert')
             </div>
        </div>

        <!-- 6. Transactions & Statistics Section -->
        <div class="row g-0 mt-n4 d-none d-lg-flex">
            <div class="col-12 px-0 px-md-3 mt-1">
                @include('pages.dashboard.trans')
            </div>
        </div>
    </div>
</x-app-layout>
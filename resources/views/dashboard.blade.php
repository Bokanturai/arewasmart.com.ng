<x-app-layout>
     <title>Arewa Smart - {{ $title ?? 'Dashboard' }}</title>
    <!-- Add space between header and content -->
    
    @if(isset($announcement) && $announcement)
    <div class="notification-container mt-3 mb-2">
        <div class="scrolling-text-container bg-primary text-white shadow-sm py-2" style="border-radius: 12px;">
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
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-100%);
            }
        }
        /* Pause on hover */
        .scrolling-text-container:hover .scrolling-text {
            animation-play-state: paused;
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
        $fullName = trim(($user->first_name ?? '') . ' ' . ($user->middle_name ?? '') . ' ' . ($user->last_name ?? ''));
        $displayName = empty($fullName) ? 'BOSS' : $fullName;
    @endphp

    <div class="container-fluid px-0 px-md-3 mt-4">
    <!-- User + Wallet Section -->
    <div class="row g-0 g-md-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body user-wallet-wrap">
            <div class="d-flex align-items-center gap-3 flex-wrap">
            <!-- User Image -->
            <div class="avatar flex-shrink-0">
                <img src="{{ Auth::user()->photo ?? asset('assets/img/profiles/avatar-31.jpg') }}"
                     class="rounded-circle border border-3 border-primary shadow-sm user-avatar"
                     alt="User Avatar">
            </div>

            <!-- Welcome Message -->
            <div class="me-auto">
                <h4 class="fw-semibold text-dark mb-1 welcome-text">
                     {{ $timeGreeting}}, {{ Auth::user()->first_name . ' ' . Auth::user()->surname ?? 'User' }}  👋
                </h4>
                <div class="d-flex align-items-center gap-2 flex-wrap funding-info">
                        @if($virtualAccount)
                            <span class="text-muted d-none d-sm-inline">|</span>
                            <small class="text-success fw-bold">
                                <i class="ti ti-building-bank me-1"></i>
                                {{ $virtualAccount->bankName }}: <strong>{{ $virtualAccount->accountNo }}</strong>
                            </small>
                        @endif
                    </div>
                </div>
            <!-- Wallet Info -->
            <div class="d-flex align-items-center gap-2 ms-2">
                <span class="fw-medium text-muted small mb-0">Balance:</span>

                <h3 id="wallet-balance" class="mb-0 @if(($wallet->status ?? 'inactive') == 'active') text-success @else text-danger @endif fw-extrabold balance-text me-2">
                    ₦{{ number_format($wallet->balance ?? 0, 2) }}
                </h5>

                <!-- Toggle Balance Button -->
                <button id="toggle-balance" class="btn btn-sm btn-outline-secondary ms-1 p-1 toggle-btn"
                        aria-pressed="true" title="Toggle balance visibility">
                    <i class="fas fa-eye eye-icon" aria-hidden="true"></i>
                </button>

                <!-- Wallet Icon -->
                <a href="{{ route('wallet') }}" class="btn btn-light ms-1 border-0 p-0 wallet-btn"
                   title="View Wallet Details" aria-label="View wallet">
                    <i class="fas fa-wallet wallet-icon text-primary"></i>
                </a>
            </div>
            </div>
        </div>
    </div>
</div>

        

       <!-- Alerts (kept as in project) -->
        @include('pages.alart')

        <!-- Dashboard widgets and sections -->
        <div class="row mt-3 d-none d-lg-flex">
            @include('pages.dashboard.wedget')
        </div>

        <div class="row g-0 g-md-4">
            @include('pages.dashboard.services')
        </div>

          <!-- advert section for mobile Users -->
        <div class="row g-0 g-md-4">
         @include('pages.dashboard.advert')
        </div>

     
        <div class="row d-none d-lg-flex">
            @include('pages.dashboard.trans')
        </div>
    </div>
         
</x-app-layout>
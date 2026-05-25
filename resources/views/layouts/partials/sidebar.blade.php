<!-- Main Wrapper -->
<div class="main-wrapper">

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo d-flex justify-content-center align-items-center w-8 text-center">
        <a href="{{ route('dashboard') }}" class="logo logo-normal">
            <img src="{{ asset('assets/img/logo/app-logo.png') }}" alt="Logo" width="70" height="70">
        </a>
        <a href="{{ route('dashboard') }}" class="logo-small">
            <img src="{{ asset('assets/img/logo/app-logo.png') }}" alt="Logo" width="70" height="70">
        </a>
        <a href="{{ route('dashboard') }}" class="dark-logo">
            <img src="{{ asset('assets/img/logo/app-logo.png') }}" alt="Logo" width="70" height="70">
        </a>
    </div>
    <!-- /Logo -->
    
    <div class="modern-profile p-3 pb-0">
        <div class="text-center rounded bg-light p-3 mb-4 user-profile">
            <div class="avatar avatar-lg online mb-3">
                @if(Auth::user()->profile_photo_url)
                    <img src="{{ Auth::user()->profile_photo_url }}" alt="Img" class="img-fluid rounded-circle">
                @else
                    <div class="avatar-title rounded-circle bg-primary text-white fs-24">
                        {{ substr(Auth::user()->first_name, 0, 1) }}
                    </div>
                @endif
            </div>
            <h6 class="fs-12 fw-normal mb-1">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h6>
            <p class="fs-10">{{ Auth::user()->role ?? 'User' }}</p>
        </div>
    </div>
    
    <div class="sidebar-header p-3 pb-0 pt-2">
        <div class="d-flex align-items-center justify-content-between menu-item mb-3">
            <div class="me-3">
                <a href="{{ route('wallet') }}" class="btn btn-menubar">
                    <i class="ti ti-wallet"></i>
                </a>
            </div>
            <div class="me-3">
                <a href="{{ route('support.index') }}" class="btn btn-menubar position-relative">
                    <i class="ti ti-message-2"></i>
                </a>
            </div>
            <div class="me-0">
                <a href="{{ route('profile.edit') }}" class="btn btn-menubar">
                    <i class="ti ti-user-circle"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <!-- Main Menu -->
                <li class="menu-title"><span>Main Menu</span></li>
                
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="ti ti-home"></i><span>Dashboard</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('wallet') }}" class="{{ request()->routeIs('wallet') ? 'active' : '' }}">
                        <i class="ti ti-wallet"></i><span>Wallet</span>
                    </a>
                </li>

                <!-- Transfer Payment -->
                <li class="submenu">
                    <a href="javascript:void(0);"
                       class="{{ request()->routeIs('transfer.index', 'withdraw.index') ? 'active subdrop' : '' }} position-relative">
                        <i class="ti ti-arrows-left-right"></i>
                        <span>Transfer</span>
                        <span class="badge rounded-pill bg-danger text-white border border-white ms-2" style="font-size: 8px; padding: 2px 5px; animation: pulse-red-sidebar 2s infinite;">
                            HOT
                        </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul style="{{ request()->routeIs('transfer.index', 'withdraw.index') ? 'display: block;' : 'display: none;' }}">
                        <li>
                            <a href="{{ route('transfer.index') }}"
                               class="{{ request()->routeIs('transfer.index') ? 'active' : '' }}">
                                Transfer P2P
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('withdraw.index') }}"
                               class="{{ request()->routeIs('withdraw.index') ? 'active' : '' }}">
                                Transfer to Bank
                            </a>
                        </li>
                    </ul>
                </li>




                <!-- Utility Bill Payment -->
                <li class="submenu">
                    <a href="javascript:void(0);" class="{{ request()->routeIs('airtime', 'buy-data', 'electricity', 'cable') ? 'active subdrop' : '' }}">
                        <i class="ti ti-credit-card"></i>
                        <span>Bill Payment</span>
                         <span class="badge rounded-pill bg-danger text-white border border-white ms-2" style="font-size: 8px; padding: 2px 5px; animation: pulse-red-sidebar 2s infinite;">
                            HOT
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ request()->routeIs('airtime', 'buy-data', 'electricity', 'cable') ? 'display: block;' : 'display: none;' }}">
                        <li><a href="{{ route('airtime') }}" class="{{ request()->routeIs('airtime') ? 'active' : '' }}">Buy Airtime</a></li>
                        <li><a href="{{ route('buy-data') }}" class="{{ request()->routeIs('buy-data') ? 'active' : '' }}">Buy Data</a></li>
                         <li><a href="{{ route('buy-sme-data') }}" class="{{ request()->routeIs('buy-sme-data') ? 'active' : '' }}">Buy SME Data</a></li>
                        <li><a href="{{ route('electricity') }}" class="{{ request()->routeIs('electricity') ? 'active' : '' }}">Pay Electric</a></li>
                        <li><a href="{{ route('cable') }}" class="{{ request()->routeIs('cable') ? 'active' : '' }}">Pay Cable TV</a></li>
                    </ul>
                </li>

                <!-- Education Payment -->
                <li class="submenu">
                    <a href="javascript:void(0);" class="{{ request()->routeIs('education', 'jamb') ? 'active subdrop' : '' }}">
                        <i class="ti ti-school"></i>
                        <span>Education</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ request()->routeIs('education', 'jamb') ? 'display: block;' : 'display: none;' }}">
                        <li><a href="{{ route('education') }}" class="{{ request()->routeIs('education') ? 'active' : '' }}">Waec PIN & Exam</a></li>
                        <li><a href="{{ route('jamb') }}" class="{{ request()->routeIs('jamb') ? 'active' : '' }}">Jamb UTME & DE</a></li>
                        <li><a href="{{ route('neco-nabted') }}" class="{{ request()->routeIs('neco-nabted') ? 'active' : '' }}">Neco & Nabted</a></li>
                    </ul>
                </li>

                 <!-- Verification Services -->
                <li class="submenu">
                    <a href="javascript:void(0);" class="{{ request()->routeIs('nin.verification.index') ? 'active subdrop' : '' }}">
                        <i class="ti ti-user-check"></i>
                        <span>Verification</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ request()->routeIs('nin.verification.index') ? 'display: block;' : 'display: none;' }}">
                        <li><a href="{{ route('bvn.verification.index') }}" class="{{ request()->routeIs('bvn.verification.index') ? 'active' : '' }}">Verify BVN</a></li>
                        <li><a href="{{ route('nin.verification.index') }}" class="{{ request()->routeIs('nin.verification.index') ? 'active' : '' }}">Verify NIN</a></li>
                        <li><a href="{{ route('cac.tin') }}" class="{{ request()->routeIs('cac.tin') ? 'active' : '' }}">Verify TIN</a></li>
                        <li><a href="{{ route('nin.demo.index') }}" class="{{ request()->routeIs('nin.demo.index') ? 'active' : '' }}">NIN Demo</a></li>
                        <li><a href="{{ route('nin.phone.index') }}" class="{{ request()->routeIs('nin.phone.index') ? 'active' : '' }}">NIN Phone NO</a></li>
                    </ul>
                </li>

                <!-- BVN Services -->
                <li class="submenu">
                    <a href="javascript:void(0);" class="{{ request()->routeIs('modification', 'bvn-crm', 'bvn.index', 'send-vnin', 'phone.search.index') ? 'active subdrop' : '' }}">
                        <i class="ti ti-id"></i>
                        <span>BVN Services</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ request()->routeIs('modification', 'bvn-crm', 'bvn.index', 'send-vnin', 'phone.search.index') ? 'display: block;' : 'display: none;' }}">
                        <li><a href="{{ route('modification') }}" class="{{ request()->routeIs('modification') ? 'active' : '' }}">Modification</a></li>
                        <li><a href="{{ route('bvn-crm') }}" class="{{ request()->routeIs('bvn-crm') ? 'active' : '' }}">CRM</a></li>
                        <li><a href="{{ route('bvn.index') }}" class="{{ request()->routeIs('bvn.index') ? 'active' : '' }}">Enrolment Agent</a></li>
                        <li><a href="{{ route('send-vnin') }}" class="{{ request()->routeIs('send-vnin') ? 'active' : '' }}">VNIN & FAS</a></li>
                        <li><a href="{{ route('phone.search.index') }}" class="{{ request()->routeIs('phone.search.index') ? 'active' : '' }}">Search BVN</a></li>
                    </ul>
                </li>

                <!-- Gift Card Services -->
                <li class="submenu">
                    <a href="javascript:void(0);" class="{{ request()->routeIs('gift-card.index', 'gift-card.generate', 'gift-card.redeem') ? 'active subdrop' : '' }}">
                        <i class="ti ti-gift"></i>
                        <span>Gift Cards</span>
                        <span class="badge rounded-pill bg-danger text-white border border-white ms-2" style="font-size: 8px; padding: 2px 5px; animation: pulse-red-sidebar 2s infinite;">
                            HOT
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ request()->routeIs('gift-card.index', 'gift-card.generate', 'gift-card.redeem') ? 'display: block;' : 'display: none;' }}">
                        <li><a href="{{ route('gift-card.index') }}" class="{{ request()->routeIs('gift-card.index') ? 'active' : '' }}">My Cards</a></li>
                        <li><a href="{{ route('gift-card.generate') }}" class="{{ request()->routeIs('gift-card.generate') ? 'active' : '' }}">Generate Card</a></li>
                        <li><a href="{{ route('gift-card.index', ['showRedeemModal' => 1]) }}" class="{{ request()->routeIs('gift-card.redeem') ? 'active' : '' }}">Redeem Card</a></li>
                    </ul>
                </li>

                <!-- NIN Services -->
                <li class="submenu">
                    <a href="javascript:void(0);" class="{{ request()->routeIs('nin-modification') ? 'active subdrop' : '' }}">
                        <i class="ti ti-user-check"></i>
                        <span>NIN Services</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ request()->routeIs('nin-modification', 'nin-validation.index', 'ipe-validation.index') ? 'display: block;' : 'display: none;' }}">
                        <li><a href="{{ route('nin-modification') }}" class="{{ request()->routeIs('nin-modification') ? 'active' : '' }}">Modification</a></li>
                        <li><a href="{{ route('nin-validation.index') }}" class="{{ request()->routeIs('nin-validation.index') ? 'active' : '' }}">NIN Validation</a></li>
                        <li><a href="{{ route('ipe-validation.index') }}" class="{{ request()->routeIs('ipe-validation.index') ? 'active' : '' }}">IPE Clearance</a></li>
                    </ul>
                </li>

                 <!-- Agency Services -->
                <li class="submenu">
                    <a href="javascript:void(0);" class="{{ request()->routeIs('affidavit.index') ? 'active subdrop' : '' }}">
                        <i class="ti ti-user-plus"></i>
                        <span>Agency Services</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ request()->routeIs('affidavit.index') ? 'display: block;' : 'display: none;' }}">
                        <li><a href="{{ route('affidavit.index') }}" class="{{ request()->routeIs('affidavit.index') ? 'active' : '' }}">Affidavit Request</a></li>
                        <li><a href="{{ route('cac.index') }}" class="{{ request()->routeIs('cac.index') ? 'active' : '' }}">CAC Registration</a></li>
                    </ul>
                </li>

                  <li>
                    <a href="{{ route('website.index') }}" class="{{ request()->routeIs('website.index') ? 'active' : '' }}">
                        <i class="ti ti-world"></i><span>Own a website</span>
                         <span class="badge rounded-pill bg-danger text-white border border-white ms-2" style="font-size: 8px; padding: 2px 5px; animation: pulse-red-sidebar 2s infinite;">
                            HOT
                        </span>
                    </a>
                </li>

                <!-- Account Section -->
                <li class="menu-title"><span>Account</span></li>

                <li>
                    <a href="{{ route('referrals.index') }}" class="{{ request()->routeIs('referrals.index') ? 'active' : '' }}">
                        <i class="ti ti-user-plus"></i><span>Referral</span>
                         <span class="badge rounded-pill bg-danger text-white border border-white ms-2" style="font-size: 8px; padding: 2px 5px; animation: pulse-red-sidebar 2s infinite;">
                            HOT
                        </span>
                    </a>
                </li>
                
                <li>
                    <a href="{{route ('profile.edit')}}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <i class="ti ti-settings"></i><span>Settings</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('api.dashboard') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-world"></i><span>Smart API</span>
                         <span class="badge rounded-pill bg-danger text-white border border-white ms-2" style="font-size: 8px; padding: 2px 5px; animation: pulse-red-sidebar 2s infinite;">
                            HOT
                        </span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('transactions') }}" class="{{ request()->routeIs('transactions') ? 'active' : '' }}">
                        <i class="ti ti-receipt"></i><span>Transactions</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('enrolment.report') }}" class="{{ request()->routeIs('enrolment.report') ? 'active' : '' }}">
                        <i class="ti ti-report-analytics"></i><span>Enrolment Report</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('support.index') }}" class="{{ request()->routeIs('support.index', 'support.create', 'support.show') ? 'active' : '' }}">
                        <i class="ti ti-headset"></i><span>Support</span>
                    </a>
                </li>
                
                <li class="px-3 mt-4 mb-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="btn btn-primary w-100 text-white d-flex justify-content-center align-items-center shadow-sm" style="border-radius: 8px;">
                            <i class="ti ti-logout me-2"></i><span>Logout</span>
                        </a>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->

<style>

  /* Better icon and text spacing */
.sidebar-menu li a {
    display: flex;
    align-items: center;
    padding: 10px 15px;
}

.sidebar-menu li a i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.sidebar-logo {
    position: sticky;
    top: 0;
    z-index: 1020;
    background-color: transparent; 
}

/* Submenu styling */
.sidebar-menu .submenu ul {
    background: rgba(0, 0, 0, 0.02);
}

.sidebar-menu .submenu ul li a {
    padding-left: 45px;
    font-size: 13px;
}

/* Active state */
.sidebar-menu li a.active {
    background: #f1cfbfff;
    color: white;
}

/* Menu title spacing */
.menu-title {
    padding: 15px 15px 5px 15px;
    font-size: 12px;
    text-transform: uppercase;
    color: #6c757d;
    font-weight: 600;
}
@keyframes pulse-red-sidebar {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}
 </style>
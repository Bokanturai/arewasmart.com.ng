<!-- Header -->
<div class="header">
    <div class="main-header">
        <!-- Header Left - Logo -->
        <div class="header-left">
            <a href="{{ route('dashboard') }}" class="logo">
                <img src="{{ asset('assets/img/logo/logo1.png') }}" alt="Logo" style="height: 30px; width: auto;">
            </a>
            <a href="{{ route('dashboard') }}" class="dark-logo">
                <img src="{{ asset('assets/img/logo/logo1.png') }}" alt="Logo" style="height: 30px; width: auto;">
            </a>
        </div>

        <!-- Mobile Menu Button -->
        <a id="mobile_btn" class="mobile_btn" href="#sidebar">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </a>

        <!-- Header User Section -->
        <div class="header-user">
            <div class="nav user-menu nav-list">
                <!-- Left Side - Search & Controls -->
                <div class="me-auto d-flex align-items-center" id="header-search">
                    <!-- Toggle Button -->
                    <a id="toggle_btn" href="javascript:void(0);" class="btn btn-menubar me-1">
                        <i class="ti ti-arrow-bar-to-left"></i>
                    </a>
                    
                    <!-- Search Bar -->
                    <div class="input-group input-group-flat d-inline-flex me-1">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Search in services">
                        <span class="input-group-text">
                            <kbd>CTRL + /</kbd>
                        </span>
                    </div>
                    
                    <!-- CRM Dropdown (Simplified for now, can be expanded) -->
                    <div class="dropdown crm-dropdown">
                        <a href="#" class="btn btn-menubar me-1" data-bs-toggle="dropdown">
                            <i class="ti ti-layout-grid"></i>
                        </a>
                        <div class="dropdown-menu dropdown-lg dropdown-menu-start">
                            <div class="card mb-0 border-0 shadow-none">
                                <div class="card-header">
                                    <h4>Services</h4>
                                </div>
                                <div class="card-body pb-1">        
                                    <div class="row">
                                        <div class="col-sm-6">                            
                                            <a href="{{ route('bvn-crm') }}" class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
                                                <span class="d-flex align-items-center me-3">
                                                    <i class="ti ti-user-shield text-default me-2"></i>BVN CRM
                                                </span>
                                                <i class="ti ti-arrow-right"></i>
                                            </a>                            
                                            <a href="{{ route('nin-validation.index') }}" class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
                                                <span class="d-flex align-items-center me-3">
                                                    <i class="ti ti-id text-default me-2"></i>NIN Validation
                                                </span>
                                                <i class="ti ti-arrow-right"></i>
                                            </a>
                                            <a href="{{ route('ipe-validation.index') }}" class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
                                                <span class="d-flex align-items-center me-3">
                                                    <i class="ti ti-id-badge text-default me-2"></i>IPE Clearance
                                                </span>
                                                <i class="ti ti-arrow-right"></i>
                                            </a>
                                        </div>
                                        <div class="col-sm-6">                            
                                            <a href="{{ route('wallet') }}" class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
                                                <span class="d-flex align-items-center me-3">
                                                    <i class="ti ti-wallet text-default me-2"></i>Wallet
                                                </span>
                                                <i class="ti ti-arrow-right"></i>
                                            </a>                                
                                            <a href="{{ route('airtime') }}" class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
                                                <span class="d-flex align-items-center me-3">
                                                    <i class="ti ti-phone text-default me-2"></i>Airtime
                                                </span>
                                                <i class="ti ti-arrow-right"></i>
                                            </a>                                
                                        </div>
                                    </div>        
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Settings Button -->
                    <a href="{{ route('profile.edit') }}" class="btn btn-menubar">
                        <i class="ti ti-settings-cog"></i>
                    </a>    
                </div>

                <!-- Right Side - Icons & Profile -->
                <div class="d-flex align-items-center">
                    <!-- Theme Toggle (Dark/Light) -->
                    <div class="me-1">
                        <a href="javascript:void(0);" class="btn btn-menubar theme-toggle-btn" title="Switch to Dark Mode">
                            <i class="ti ti-moon"></i>
                        </a>
                    </div>
                    
                    <!-- Fullscreen -->
                    <div class="me-1">
                        <a href="#" class="btn btn-menubar btnFullscreen">
                            <i class="ti ti-maximize"></i>
                        </a>
                    </div>
                    
                    <!-- Applications Dropdown (Optional, keeping structure) -->
                    <div class="dropdown me-1">
                        <a href="#" class="btn btn-menubar" data-bs-toggle="dropdown">
                            <i class="ti ti-layout-grid-remove"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="card mb-0 border-0 shadow-none">
                                <div class="card-header">
                                    <h4>Applications</h4>
                                </div>
                                <div class="card-body">                                            
                                    <a href="{{ route('dashboard') }}" class="d-block pb-2 border-bottom mb-2 text-dark">
                                        <span class="avatar avatar-md bg-transparent-dark me-2"><i class="ti ti-layout-dashboard text-gray-9"></i></span>Dashboard
                                    </a>                                        
                                    <a href="{{ route('wallet') }}" class="d-block py-2 text-dark border-bottom mb-2">
                                        <span class="avatar avatar-md bg-transparent-dark me-2"><i class="ti ti-wallet text-gray-9"></i></span>Wallet
                                    </a>                                        
                                    <a href="javascript:void(0);" class="d-block py-2 install-app-btn text-dark" title="Install Application">
                                        <span class="avatar avatar-md bg-transparent-dark me-2"><i class="ti ti-download text-gray-9"></i></span>Install App
                                    </a>
                                </div>
                            </div>
                          </div>
                            </div>
                    
                    <!-- Chat (Placeholder) -->
                    <div class="me-1">
                        <a href="#" class="btn btn-menubar position-relative">
                            <i class="ti ti-brand-hipchat"></i>
                            <span class="badge bg-info rounded-pill d-flex align-items-center justify-content-center header-badge">0</span>
                        </a>
                    </div>
                    
                    <!-- Email (Placeholder) -->
                    <div class="me-1">
                        <a href="#" class="btn btn-menubar">
                            <i class="ti ti-mail"></i>
                        </a>
                    </div>
                    
                    <!-- Notifications (Placeholder) -->
                    <div class="me-1 notification_item">
                        <a href="#" class="btn btn-menubar position-relative me-1" id="notification_popup" data-bs-toggle="dropdown">
                            <i class="ti ti-bell"></i>
                            <span class="notification-status-dot"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown p-4">
                            <div class="d-flex align-items-center justify-content-between border-bottom p-0 pb-3 mb-3">
                                <h4 class="notification-title">Notifications (0)</h4>
                                <div class="d-flex align-items-center">
                                    <a href="#" class="text-primary fs-15 me-3 lh-1">Mark all as read</a>
                                </div>
                            </div>
                            <div class="d-flex p-0">
                                <a href="#" class="btn btn-light w-100 me-2">Cancel</a>
                                <a href="#" class="btn btn-primary w-100">View All</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile Dropdown -->
                    <div class="dropdown profile-dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm online">
                                <img src="{{ Auth::user()->photo ? asset(Auth::user()->photo) : asset('assets/img/profiles/avatar-01.jpg') }}" alt="Img" class="img-fluid rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                            </span>
                        </a>
                        <div class="dropdown-menu shadow-none">
                            <div class="card mb-0">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-lg me-2 avatar-rounded">
                                            <img src="{{ Auth::user()->photo ? asset(Auth::user()->photo) : asset('assets/img/profiles/avatar-01.jpg') }}" alt="img" style="width: 48px; height: 48px; object-fit: cover;">
                                        </span>
                                        <div>
                                            <h5 class="mb-0">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h5>
                                            <p class="fs-12 fw-medium mb-0">
                                                {{ Auth::user()->email }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="{{ route('profile.edit') }}">
                                        <i class="ti ti-user-circle me-1"></i>My Profile
                                    </a>
                                    <a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="{{ route('profile.edit') }}">
                                        <i class="ti ti-settings me-1"></i>Settings
                                    </a>
                                    <a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="{{ route('support.index') }}">
                                        <i class="ti ti-question-mark me-1"></i>Support
                                    </a>
                                </div>
                                <div class="card-footer">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                            <i class="ti ti-login me-2"></i>Logout
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="dropdown mobile-user-menu">
            <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-ellipsis-v"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <i class="ti ti-user-circle me-2"></i>My Profile
                </a>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <i class="ti ti-settings me-2"></i>Settings
                </a>
                <a class="dropdown-item theme-toggle-btn" href="javascript:void(0);">
                    <i class="ti ti-moon me-2"></i>Dark Mode
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="ti ti-login me-2"></i>Logout
                    </a>
                </form>
            </div>
        </div>
        <!-- /Mobile Menu -->
    </div>
</div>
<!-- /Header -->
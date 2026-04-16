<x-app-layout>
    <x-slot name="header">
        Account Settings
    </x-slot>

    <!-- Custom CSS for this page -->
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #e93c11 0%, #ff8c00 100%);
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.2);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --accent-glow: 0 0 15px rgba(233, 60, 17, 0.3);
        }
        
        .settings-container {
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }

        /* Tabs Styling */
        .settings-nav {
            background: #fff;
            padding: 0.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            display: flex;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .settings-nav::-webkit-scrollbar { display: none; }
        
        .settings-nav .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 600;
            padding: 0.8rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .settings-nav .nav-link i { font-size: 1.2rem; }
        .settings-nav .nav-link.active {
            background: var(--primary-gradient);
            color: #fff !important;
            box-shadow: var(--accent-glow);
        }
        @media (max-width: 768px) {
            .settings-nav {
                padding: 0.25rem;
                margin-left: -10px;
                margin-right: -10px;
                width: calc(100% + 20px);
                border-radius: 0;
            }
            .settings-nav .nav-link {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }
        .settings-nav .nav-link:hover:not(.active) {
            background: #f8f9fa;
            color: #e93c11;
        }

        /* Profile Header Glassmorphism */
        .profile-hero {
            position: relative;
            background: var(--primary-gradient);
            min-height: 200px;
            margin-bottom: 3rem;
            /* overflow: hidden; Removed to prevent clipping content-header */
        }
        .profile-hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        .profile-hero-pattern {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 86c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm66-3c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm-46-43c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm20-27c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM2 30c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm26-2c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm55 56c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-8-78c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-51 72c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM21 20c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm65 11c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM17 77c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm70 12c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
        
        .profile-content-header {
            position: absolute;
            bottom: -40px;
            left: 20px;
            right: 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 1.25rem;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            border: 1px solid var(--glass-border);
            box-shadow: var(--card-shadow);
            z-index: 10;
        }

        @media (max-width: 768px) {
            .profile-hero { 
                min-height: 160px; 
                margin-bottom: 5.5rem; 
                margin-left: -15px;
                margin-right: -15px;
                width: calc(100% + 30px);
                border-radius: 0 !important;
            }
            .profile-hero-pattern { border-radius: 0; }
            .settings-nav {
                padding: 0.25rem;
                margin-left: -15px;
                margin-right: -15px;
                width: calc(100% + 30px);
                border-radius: 0 !important;
            }
            .profile-content-header { 
                bottom: -70px; 
                left: 10px; 
                right: 10px; 
                padding: 1rem;
                flex-direction: column;
                text-align: center;
                gap: 0.75rem;
                border-radius: 1rem;
            }
            .profile-avatar-main { width: 80px; height: 80px; }
            .avatar-container { margin-top: -55px; margin-bottom: 5px; }
            .edit-badge-main { width: 28px; height: 28px; bottom: 2px; right: 2px; }
        }

        .avatar-container {
            position: relative;
            background: #fff;
            padding: 4px;
            border-radius: 50%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }
        .profile-avatar-main {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
        .edit-badge-main {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #e93c11;
            color: #fff;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 3px solid #fff;
        }

        /* Stats Cards */
        .stat-card {
            background: #fff;
            padding: 1.25rem;
            border: 1px solid #f1f1f1;
            transition: all 0.3s ease;
            height: 100%;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: var(--card-shadow); }
        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        /* Info Item Styling */
        .info-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 1rem;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }
        .info-item:hover {
            background: #fff;
            border-color: #e93c11;
            box-shadow: 0 5px 15px rgba(233, 60, 17, 0.05);
        }

        /* Progress Bar */
        .progress-slim { height: 8px; border-radius: 10px; }

        /* Security Status */
        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-badge.verified { background: rgba(25, 135, 84, 0.1); color: #198754; }
        
        /* Modal Customization */
        .modal-content-premium {
            border: none;
            border-radius: 1.5rem;
            background: #fff;
        }
    </style>

    <!-- Cropper.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />

    <div class="container settings-container py-4">
        
        <!-- Alerts -->
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center mb-4" role="alert" style="border-radius: 20px;">
                <i class="ti ti-circle-check fs-13 me-2"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Tabbed Navigation -->
        <div class="settings-nav nav" role="tablist" style="border-radius: 20px;">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview" type="button">
                <i class="ti ti-layout-dashboard"></i> Overview
            </button>
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile" type="button">
                <i class="ti ti-user"></i> Profile Details
            </button>
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#security" type="button">
                <i class="ti ti-shield-lock"></i> Security
            </button>
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#activity" type="button">
                <i class="ti ti-history"></i> Activity Log
            </button>
        </div>

        <div class="tab-content mt-4">
            <!-- OVERVIEW TAB -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="profile-hero" style="border-radius: 20px;">
                    <div class="profile-hero-bg" style="border-radius: 20px;">
                        <div class="profile-hero-pattern"></div>
                    </div>
                    <div class="profile-content-header" style="border-radius: 20px;">
                        <div class="avatar-container">
                            <img src="{{ $user->photo ? (str_starts_with($user->photo, 'http') ? $user->photo : asset($user->photo)) : asset('assets/img/profiles/avatar-01.jpg') }}" 
                                 class="profile-avatar-main" id="profileImagePreview">
                            <div class="edit-badge-main" data-bs-toggle="modal" data-bs-target="#photoModal">
                                <i class="ti ti-camera"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 w-100">
                            <h3 class="fw-bold mb-1 text-primary fs-14 fs-md-3">{{ $user->first_name }} {{ $user->last_name }}</h3>
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 gap-md-3 flex-wrap">
                                <span class="bg-light px-2 py-1 rounded-pill text-muted small"><i class="ti ti-mail me-1"></i>{{ $user->email }}</span>
                                <span class="status-badge verified small py-1 px-3 shadow-sm border bg-white rounded-pill">
                                    <i class="ti ti-shield-check me-1"></i> {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                        <div class="d-none d-lg-block">
                            <div class="text-end mb-1 small fw-bold text-muted">Profile Completion</div>
                            <div class="progress progress-slim" style="width: 150px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 85%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-0 g-md-4 mt-1">
                    <div class="col-12 col-md-3 mb-3 mb-md-0">
                        <div class="stat-card shadow-sm" style="border-radius: 20px;">
                            <div class="stat-icon bg-primary-subtle text-primary">
                                <i class="ti ti-crown"></i>
                            </div>
                            <div class="text-muted small mb-1">Account Role</div>
                            <h5 class="fw-bold mb-0 text-primary">{{ ucfirst($user->role ?? 'Standard User') }}</h5>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3 mb-md-0">
                        <div class="stat-card shadow-sm" style="border-radius: 20px;">
                            <div class="stat-icon bg-success-subtle text-success">
                                <i class="ti ti-calendar-event"></i>
                            </div>
                            <div class="text-muted small mb-1">Joined Date</div>
                            <h5 class="fw-bold mb-0 text-primary">{{ $user->created_at->format('M d, Y') }}</h5>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3 mb-md-0">
                        <div class="stat-card shadow-sm" style="border-radius: 20px;">
                            <div class="stat-icon bg-warning-subtle text-warning">
                                <i class="ti ti-wallet"></i>
                            </div>
                            <div class="text-muted small mb-1">Daily Limit</div>
                            <h5 class="fw-bold mb-0 text-primary">₦{{ number_format((float)$user->limit, 2) }}</h5>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="stat-card shadow-sm" style="border-radius: 20px;">
                            <div class="stat-icon bg-info-subtle text-info">
                                <i class="ti ti-device-mobile"></i>
                            </div>
                            <div class="text-muted small mb-1">Identity Verified</div>
                            <h5 class="fw-bold mb-0 text-primary">{{ $user->nin || $user->bvn ? 'Yes' : 'No' }}</h5>
                        </div>
                    </div>
                </div>

                <!-- Support Banner -->
                <div class="mt-5">
                    <div class="card border-0 overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #198754 0%, #1a4d2e 100%); border-radius: 20px;">
                        <div class="card-body p-4 text-white">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 1rem; display: flex; align-items: center; justify-content: center;">
                                        <i class="ti ti-brand-whatsapp fs-1"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0">Need Rapid Assistance?</h4>
                                        <p class="mb-0 opacity-75">Your dedicated account officer is just a message away.</p>
                                    </div>
                                </div>
                                <a href="https://wa.me/2348064333983" target="_blank" class="btn btn-light rounded-pill px-4 fw-bold">
                                    <i class="ti ti-brand-whatsapp me-2"></i> Contact Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PROFILE DETAILS TAB -->
            <div class="tab-pane fade" id="profile" role="tabpanel">
                <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-white border-0 py-4 px-4" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                        <h4 class="fw-bold mb-0 text-primary d-flex align-items-center">
                            <i class="ti ti-user-scan me-2 text-primary"></i> Personal Information
                        </h4>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-0 g-md-4">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase mb-1 fw-bold">Full Name</label>
                                    <div class="fw-bold text-primary fs-14">{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mt-2 mt-md-0">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase mb-1 fw-bold">Email Address</label>
                                    <div class="fw-bold text-primary fs-14 d-flex align-items-center gap-2">
                                        {{ $user->email }}
                                        <i class="ti ti-copy text-muted cursor-pointer btn-copy" data-text="{{ $user->email }}" title="Copy Email"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mt-2 mt-md-0">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase mb-1 fw-bold">Phone Number</label>
                                    <div class="fw-bold text-primary fs-14">{{ $user->phone_no ?: 'No provided' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mt-2 mt-md-0">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase mb-1 fw-bold">Business Name</label>
                                    <div class="fw-bold text-primary fs-14">{{ $user->business_name ?: 'No Business Set' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mt-2 mt-md-0">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase mb-1 fw-bold">Origin/Location</label>
                                    <div class="fw-bold text-primary fs-14">{{ $user->state ? $user->state . ' State' : 'Not Added' }}</div>
                                    @if($user->lga)<small class="text-muted">LGA: {{ $user->lga }}</small>@endif
                                </div>
                            </div>
                            <div class="col-12 mt-2 mt-md-0">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase mb-1 fw-bold">Full Address</label>
                                    <div class="fw-bold text-primary fs-16">{{ $user->address ?: 'Complete your profile with a valid address.' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: var(--primary-gradient); border: none;">
                                <i class="ti ti-edit me-1"></i> Edit Profile Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECURITY TAB -->
            <div class="tab-pane fade" id="security" role="tabpanel">
                <div class="row g-0 g-md-4">
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <div class="card shadow-lg border-0 h-100" style="border-radius: 20px; overflow: hidden;">
                            <div class="card-body p-4">
                                <div class="stat-icon bg-primary-subtle text-primary mb-3">
                                    <i class="ti ti-lock"></i>
                                </div>
                                <h4 class="fw-bold mb-2">Login Password</h4>
                                <p class="text-muted small mb-4">Manage your account security by updating your password regularly.</p>
                                <button class="btn btn-light w-100 rounded-pill py-3 fw-bold border" data-bs-toggle="modal" data-bs-target="#passwordModal">
                                    Update Password <i class="ti ti-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mt-2 mt-md-0">
                        <div class="card shadow-lg border-0 h-100" style="border-radius: 20px; overflow: hidden;">
                            <div class="card-body p-4">
                                <div class="stat-icon bg-danger-subtle text-danger mb-3">
                                    <i class="ti ti-key"></i>
                                </div>
                                <h4 class="fw-bold mb-2">Transaction PIN</h4>
                                <p class="text-muted small mb-4">This PIN is required for every disbursement and payment in the app.</p>
                                <button class="btn btn-light w-100 rounded-pill py-3 fw-bold border" data-bs-toggle="modal" data-bs-target="#pinModal">
                                    Reset Transaction PIN <i class="ti ti-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 mt-3">
                        <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                            <div class="card-body p-3">
                                <h5 class="fw-bold mb-3 fs-14">Login Sessions & Security</h5>
                                <div class="d-flex align-items-center flex-wrap flex-md-nowrap gap-3 p-3 bg-light" style="border-radius: 20px;">
                                    <div class="icon-circle bg-white shadow-sm rounded-circle flex-shrink-0" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                        <i class="ti ti-device-laptop text-primary fs-14"></i>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-bold text-primary">Current Session</div>
                                        <div class="text-muted small text-break" style="line-height: 1.4;">
                                            <span class="d-block d-md-inline mb-1 mb-md-0 me-md-2">IP: {{ request()->ip() }}</span>
                                            <span class="opacity-75">{{ request()->userAgent() }}</span>
                                        </div>
                                    </div>
                                    <div class="status-badge verified small flex-shrink-0">Active</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        <!-- Biometric Devices Management -->
                        <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h5 class="fw-bold mb-0">Biometric Devices (Passkeys)</h5>
                                    <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#biometricModal">
                                        <i class="ti ti-plus me-1 fs-15"></i> Add Device
                                    </button>
                                </div>
                                <div id="biometric-devices-list">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                                        <span class="ms-2 small text-muted">Loading devices...</span>
                                    </div>
                                </div>
                                <p class="text-muted small mt-3 mb-0">
                                    <i class="ti ti-info-circle me-1"></i> These devices allow you to sign in instantly without a password.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTIVITY TAB -->
            <div class="tab-pane fade" id="activity" role="tabpanel">
                <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                        <h4 class="fw-bold mb-0 text-primary">Recent Operations</h4>
                        <button class="btn btn-sm btn-light rounded-pill px-3">View All</button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Operation</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="pe-4">IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="p-2 bg-primary-subtle text-primary rounded-circle small"><i class="ti ti-login"></i></span>
                                                <span class="fw-bold">User Login</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success">Success</span></td>
                                        <td>{{ now()->format('M d, H:i') }}</td>
                                        <td class="pe-4 text-muted">{{ request()->ip() }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="p-2 bg-info-subtle text-info rounded-circle small"><i class="ti ti-user-edit"></i></span>
                                                <span class="fw-bold">Profile Update</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success">Success</span></td>
                                        <td>{{ now()->subHours(5)->format('M d, H:i') }}</td>
                                        <td class="pe-4 text-muted">{{ request()->ip() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALS -->
    
    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-premium shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Change Profile Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="photoUploadForm" action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4 text-center">
                        <div class="mb-4" id="uploadPlaceholder">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background: rgba(233,60,17,0.05);">
                                <i class="ti ti-photo-plus text-primary fs-1"></i>
                            </div>
                            <h6 class="fw-bold mt-3 mb-1">Pick a Face</h6>
                            <p class="text-muted small">JPG, WEBP or PNG. Ideal size: 400x400px.</p>
                        </div>
                        
                        <input type="file" id="photoInput" name="photo" class="form-control" accept="image/*" required>

                        <div id="cropperContainer" class="d-none mt-3" style="max-height: 400px; width: 100%; border-radius: 1rem; overflow: hidden;">
                            <img id="cropperImage" src="" style="max-width: 100%;">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Later</button>
                        <button type="button" id="cropAndUploadBtn" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: var(--primary-gradient); border: none;" disabled>Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-premium shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="ti ti-lock text-primary"></i> Change Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4 pt-0">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Current Password</label>
                            <div class="input-group border rounded-pill overflow-hidden bg-light px-3">
                                <span class="input-group-text bg-transparent border-0"><i class="ti ti-lock-open"></i></span>
                                <input type="password" name="current_password" class="form-control bg-transparent border-0" required placeholder="••••••••">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">New Password</label>
                            <div class="input-group border rounded-pill overflow-hidden bg-light px-3">
                                <span class="input-group-text bg-transparent border-0"><i class="ti ti-lock"></i></span>
                                <input type="password" name="password" class="form-control bg-transparent border-0" required placeholder="Minimum 8 characters">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Confirm New Password</label>
                            <div class="input-group border rounded-pill overflow-hidden bg-light px-3">
                                <span class="input-group-text bg-transparent border-0"><i class="ti ti-lock"></i></span>
                                <input type="password" name="password_confirmation" class="form-control bg-transparent border-0" required placeholder="Verify password">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: var(--primary-gradient); border: none;">Save Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PIN Modal -->
    <div class="modal fade" id="pinModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-premium shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                        <i class="ti ti-shield-security"></i> Reset Transaction PIN
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('profile.pin') }}">
                    @csrf
                    <div class="modal-body p-4 pt-0">
                        <div class="alert alert-danger bg-danger-subtle border-0 rounded-4 text-danger small mb-4">
                            <strong>Careful!</strong> This PIN is your ultimate authorization for spending. Never share it with anyone.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Confirm Account Password</label>
                            <div class="input-group border rounded-pill overflow-hidden bg-light px-3">
                                <span class="input-group-text bg-transparent border-0"><i class="ti ti-lock-access"></i></span>
                                <input type="password" name="current_password" class="form-control bg-transparent border-0" required placeholder="Your login password">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-bold text-muted small">New 5-Digit PIN</label>
                                <input type="password" name="pin" maxlength="5" pattern="\d{5}" class="form-control rounded-pill bg-light border-0 text-center fs-14 fw-bold" required placeholder="•••••" inputmode="numeric">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-muted small">Repeat PIN</label>
                                <input type="password" name="pin_confirmation" maxlength="5" pattern="\d{5}" class="form-control rounded-pill bg-light border-0 text-center fs-14 fw-bold" required placeholder="•••••" inputmode="numeric">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Activate New PIN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rename Biometric Modal -->
    <div class="modal fade" id="renameBiometricModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content modal-content-premium shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Rename Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="rename-device-id">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">Device Name</label>
                        <input type="text" id="rename-device-alias" class="form-control rounded-pill bg-light border-0 px-3 fw-bold" placeholder="e.g. My iPhone">
                    </div>
                    <button type="button" id="confirm-rename-btn" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm" style="background: var(--primary-gradient); border: none;">
                        Update Name
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('auth.biometric-modal')

</x-app-layout>

<!-- Cropper.js Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    // Integrated Settings Logic
    document.addEventListener('DOMContentLoaded', function() {
        // --- Copy to Clipboard Utility ---
        document.querySelectorAll('.btn-copy').forEach(btn => {
            btn.onclick = function() {
                const text = this.getAttribute('data-text');
                navigator.clipboard.writeText(text).then(() => {
                    const originalIcon = this.className;
                    this.className = 'ti ti-check text-success';
                    setTimeout(() => this.className = originalIcon, 2000);
                });
            }
        });

        // --- Profile Photo Cropper Logic ---
        let cropper;
        const photoInput = document.getElementById('photoInput');
        const cropperImage = document.getElementById('cropperImage');
        const cropperContainer = document.getElementById('cropperContainer');
        const uploadBtn = document.getElementById('cropAndUploadBtn');
        const photoForm = document.getElementById('photoUploadForm');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');

        if(photoInput) {
            photoInput.addEventListener('change', function (e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    const file = files[0];
                    const url = URL.createObjectURL(file);
                    
                    cropperImage.src = url;
                    cropperContainer.classList.remove('d-none');
                    if(uploadPlaceholder) uploadPlaceholder.classList.add('d-none');
                    uploadBtn.disabled = false;

                    if (cropper) cropper.destroy();

                    cropper = new Cropper(cropperImage, {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                    });
                }
            });
        }

        const styleCircle = document.createElement('style');
        styleCircle.innerHTML = `.cropper-view-box, .cropper-face { border-radius: 50%; }`;
        document.head.appendChild(styleCircle);

        if(uploadBtn) {
            uploadBtn.addEventListener('click', function () {
                if (!cropper) return;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                this.disabled = true;

                cropper.getCroppedCanvas({ width: 400, height: 400 }).toBlob((blob) => {
                    const formData = new FormData(photoForm);
                    formData.set('photo', blob, 'profile.jpg');

                    fetch(photoForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.ok ? window.location.reload() : alert('Failed'))
                    .catch(() => alert('Error uploading profile'));
                }, 'image/jpeg', 0.9);
            });
        }

        const photoModal = document.getElementById('photoModal');
        if(photoModal) {
            photoModal.addEventListener('hidden.bs.modal', function () {
                if (cropper) cropper.destroy();
                photoInput.value = '';
                cropperContainer.classList.add('d-none');
                uploadBtn.disabled = true;
                if(uploadPlaceholder) uploadPlaceholder.classList.remove('d-none');
            });
        }

        // --- Biometric Device Management (Passkeys) ---
        const devicesList = document.getElementById('biometric-devices-list');
        const renameModal = new bootstrap.Modal(document.getElementById('renameBiometricModal'));
        
        // Initial fetch
        fetchDevices();

        async function fetchDevices() {
            if (!devicesList) return;
            
            try {
                const response = await fetch('{{ route("webauthn.devices.index") }}');
                const devices = await response.json();
                
                if (devices.length === 0) {
                    devicesList.innerHTML = `
                        <div class="text-center py-4 bg-light rounded-4">
                            <i class="ti ti-fingerprint-off fs-1 text-muted opacity-25"></i>
                            <p class="text-muted small mt-2">No biometric devices registered yet.</p>
                        </div>
                    `;
                    return;
                }

                devicesList.innerHTML = devices.map(device => `
                    <div class="d-flex align-items-center gap-3 p-3 mb-2 border rounded-4 transition-all hover-shadow bg-white">
                        <div class="icon-circle bg-primary-subtle rounded-circle" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ${device.aaguid ? 'ti-device-mobile' : 'ti-device-laptop'} text-primary fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                ${device.alias || 'Unnamed Device'}
                                <span class="badge bg-light text-muted fw-normal" style="font-size: 10px; border: 1px solid #eee;">
                                    ${device.attestation_format}
                                </span>
                            </div>
                            <div class="text-muted small" style="font-size: 11px;">
                                <i class="ti ti-clock-hour-4 me-1"></i>Registered: ${new Date(device.created_at).toLocaleDateString()}
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-icon btn-light rounded-circle shadow-sm edit-device" 
                                    data-id="${device.id}" data-alias="${device.alias || ''}" title="Rename">
                                <i class="ti ti-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-icon btn-light rounded-circle shadow-sm text-danger delete-device" 
                                    data-id="${device.id}" title="Revoke">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('');

                attachDeviceListeners();
            } catch (error) {
                devicesList.innerHTML = '<div class="alert alert-danger py-2 small">Failed to load biometric devices.</div>';
            }
        }

        function attachDeviceListeners() {
            // Rename Action
            document.querySelectorAll('.edit-device').forEach(btn => {
                btn.onclick = () => {
                    document.getElementById('rename-device-id').value = btn.dataset.id;
                    document.getElementById('rename-device-alias').value = btn.dataset.alias;
                    renameModal.show();
                };
            });

            // Delete Action
            document.querySelectorAll('.delete-device').forEach(btn => {
                btn.onclick = async () => {
                    const result = await Swal.fire({
                        title: 'Revoke Access?',
                        text: "This biometric device will no longer be able to log in. You can re-register it at any time.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e93c11',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Revoke',
                        cancelButtonText: 'Cancel',
                        borderRadius: '20px',
                        background: '#fff',
                        customClass: {
                            confirmButton: 'rounded-pill px-4',
                            cancelButton: 'rounded-pill px-4'
                        }
                    });

                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`{{ url('webauthn/devices') }}/${btn.dataset.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            });
                            
                            if (response.ok) {
                                Swal.fire({ 
                                    icon: 'success', 
                                    title: 'Access Revoked', 
                                    text: 'Device removed successfully.',
                                    toast: true, 
                                    position: 'top-end', 
                                    showConfirmButton: false, 
                                    timer: 3000 
                                });
                                fetchDevices();
                            }
                        } catch (error) {
                            Swal.fire({ icon: 'error', title: 'Action Failed', text: 'Encountered an error while revoking access.' });
                        }
                    }
                };
            });
        }

        // Confirm Rename Modal Submit
        const confirmRenameBtn = document.getElementById('confirm-rename-btn');
        if (confirmRenameBtn) {
            confirmRenameBtn.onclick = async function() {
                const id = document.getElementById('rename-device-id').value;
                const alias = document.getElementById('rename-device-alias').value;
                
                if (!alias) {
                    alert('Please provide a name');
                    return;
                }

                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

                try {
                    const response = await fetch(`{{ url('webauthn/devices') }}/${id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ alias })
                    });

                    if (response.ok) {
                        renameModal.hide();
                        Swal.fire({ icon: 'success', title: 'Renamed', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                        fetchDevices();
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: 'Update Failed' });
                } finally {
                    this.disabled = false;
                    this.innerHTML = 'Update Name';
                }
            };
        }
    });
</script>

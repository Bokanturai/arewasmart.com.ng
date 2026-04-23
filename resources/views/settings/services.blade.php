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
            --item-bg: #f8f9fa;
            --item-border: transparent;
        }

        [data-theme="dark"] {
            --glass-bg: rgba(22, 27, 34, 0.8);
            --glass-border: rgba(255, 255, 255, 0.1);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            --item-bg: #0d1117;
            --item-border: rgba(255, 255, 255, 0.05);
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
            border-radius: 20px;
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
                margin-left: -15px;
                margin-right: -15px;
                width: calc(100% + 30px);
                border-radius: 0 !important;
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

        /* Profile Header Styling */
        .profile-hero {
            position: relative;
            background: var(--primary-gradient);
            min-height: 200px;
            margin-bottom: 3rem;
            border-radius: 20px;
        }
        .profile-hero-bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            overflow: hidden;
            z-index: 0;
            border-radius: 20px;
        }
        .profile-hero-pattern {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 86c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm66-3c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm-46-43c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm20-27c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM2 30c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm26-2c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm55 56c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-8-78c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-51 72c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM21 20c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm65 11c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
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
            .profile-content-header { 
                bottom: -70px; 
                left: 10px; 
                right: 10px; 
                padding: 1rem;
                flex-direction: column;
                text-align: center;
            }
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
            bottom: 5px; right: 5px;
            background: #e93c11;
            color: #fff;
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center; justify-content: center;
            cursor: pointer;
            border: 3px solid #fff;
        }

        /* Stats Cards */
        .stat-card {
            background: var(--glass-bg);
            padding: 1.25rem;
            border: 1px solid var(--item-border);
            transition: all 0.3s ease;
            height: 100%;
            border-radius: 20px;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: var(--card-shadow); }
        .stat-icon {
            width: 45px; height: 45px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center; justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        /* Info Item Styling */
        .info-item {
            background: var(--item-bg);
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

        /* Status Badges */
        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-badge.verified { background: rgba(25, 135, 84, 0.1); color: #198754; }
        
        .modal-content-premium {
            border: none;
            border-radius: 1.5rem;
            background: var(--glass-bg);
        }

        [data-theme="dark"] .settings-nav {
            background: var(--dark-card);
        }
        [data-theme="dark"] .settings-nav .nav-link:not(.active) {
            color: var(--dark-text-muted);
        }
        [data-theme="dark"] .settings-nav .nav-link:hover:not(.active) {
            background: rgba(255, 255, 255, 0.05);
        }
        [data-theme="dark"] .avatar-container {
            background: var(--dark-bg);
        }
        [data-theme="dark"] .stat-card h5 {
            color: #fff !important;
        }
        [data-theme="dark"] .edit-badge-main {
            border-color: var(--dark-card);
        }
        [data-theme="dark"] .info-item:hover {
            background: var(--dark-card);
        }
        [data-theme="dark"] .bg-primary-subtle {
            background: rgba(233, 60, 17, 0.15) !important;
        }
        [data-theme="dark"] .bg-success-subtle {
            background: rgba(25, 135, 84, 0.15) !important;
        }
        [data-theme="dark"] .bg-warning-subtle {
            background: rgba(255, 193, 7, 0.15) !important;
        }
        [data-theme="dark"] .bg-info-subtle {
            background: rgba(13, 110, 253, 0.15) !important;
        }
        [data-theme="dark"] .bg-danger-subtle {
            background: rgba(220, 53, 69, 0.15) !important;
        }
    </style>

    <!-- Cropper.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />

    <div class="container settings-container py-4">
        
        <!-- Flash Alerts -->
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center mb-4" role="alert" style="border-radius: 20px;">
                <i class="ti ti-circle-check fs-13 me-2"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Tabbed Navigation -->
        <div class="settings-nav nav" role="tablist">
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
                <!-- Profile Header -->
                <div class="profile-hero">
                    <div class="profile-hero-bg">
                        <div class="profile-hero-pattern"></div>
                    </div>
                    <div class="profile-content-header">
                        <div class="avatar-container">
                            <img src="{{ $user->photo ? (str_starts_with($user->photo, 'http') ? $user->photo : asset($user->photo)) : asset('assets/img/profiles/avatar-01.jpg') }}" 
                                 class="profile-avatar-main" id="profileImagePreview">
                            <div class="edit-badge-main" data-bs-toggle="modal" data-bs-target="#photoModal">
                                <i class="ti ti-camera"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 w-100">
                            <h3 class="fw-bold mb-1 text-primary fs-14 fs-md-3">{{ $user->first_name }} {{ $user->last_name }}</h3>
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 flex-wrap">
                                <span class="bg-light px-2 py-1 rounded-pill text-muted small"><i class="ti ti-mail me-1"></i>{{ $user->email }}</span>
                                <span class="status-badge verified small py-1 px-3 shadow-sm border bg-white rounded-pill">
                                    <i class="ti ti-shield-check me-1"></i> {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="row g-0 g-md-4 mt-1">
                    <div class="col-12 col-md-3 mb-3 mb-md-0">
                        <div class="stat-card shadow-sm">
                            <div class="stat-icon bg-primary-subtle text-primary"><i class="ti ti-crown"></i></div>
                            <div class="text-muted small mb-1">Account Role</div>
                            <h5 class="fw-bold mb-0 text-primary">{{ ucfirst($user->role ?? 'Standard User') }}</h5>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3 mb-md-0">
                        <div class="stat-card shadow-sm">
                            <div class="stat-icon bg-success-subtle text-success"><i class="ti ti-calendar-event"></i></div>
                            <div class="text-muted small mb-1">Joined Date</div>
                            <h5 class="fw-bold mb-0 text-primary">{{ $user->created_at->format('M d, Y') }}</h5>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3 mb-md-0">
                        <div class="stat-card shadow-sm">
                            <div class="stat-icon bg-warning-subtle text-warning"><i class="ti ti-wallet"></i></div>
                            <div class="text-muted small mb-1">Daily Limit</div>
                            <h5 class="fw-bold mb-0 text-primary">₦{{ number_format((float)$user->limit, 2) }}</h5>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="stat-card shadow-sm">
                            <div class="stat-icon bg-info-subtle text-info"><i class="ti ti-device-mobile"></i></div>
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
                    <div class="card-header bg-white border-0 py-4 px-4">
                        <h4 class="fw-bold mb-0 text-primary d-flex align-items-center">
                            <i class="ti ti-user-scan me-2"></i> Personal Information
                        </h4>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-0 g-md-4">
                            <div class="col-12 col-md-6 mb-3">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase mb-1 fw-bold">Full Name</label>
                                    <div class="fw-bold text-primary fs-14">{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase mb-1 fw-bold">Email Address</label>
                                    <div class="fw-bold text-primary fs-14 d-flex align-items-center gap-2">
                                        {{ $user->email }}
                                        <i class="ti ti-copy text-muted cursor-pointer btn-copy" data-text="{{ $user->email }}"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase mb-1 fw-bold">Phone Number</label>
                                    <div class="fw-bold text-primary fs-14">{{ $user->phone_no ?: 'None provided' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase mb-1 fw-bold">Business Name</label>
                                    <div class="fw-bold text-primary fs-14">{{ $user->business_name ?: 'No Business Set' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase mb-1 fw-bold">Origin/Location</label>
                                    <div class="fw-bold text-primary fs-14">{{ $user->state ? $user->state . ' State' : 'Not Added' }}</div>
                                    @if($user->lga)<small class="text-muted">LGA: {{ $user->lga }}</small>@endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase mb-1 fw-bold">Full Address</label>
                                    <div class="fw-bold text-primary fs-16">{{ $user->address ?: 'Profile incomplete.' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: var(--primary-gradient); border: none;">
                                <i class="ti ti-edit me-1"></i> Edit Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECURITY TAB -->
            <div class="tab-pane fade" id="security" role="tabpanel">
                <div class="row g-0 g-md-4">
                    <!-- Password Update -->
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <div class="card shadow-lg border-0 h-100" style="border-radius: 20px; overflow: hidden;">
                            <div class="card-body p-4">
                                <div class="stat-icon bg-primary-subtle text-primary mb-3"><i class="ti ti-lock"></i></div>
                                <h4 class="fw-bold mb-2">Login Password</h4>
                                <p class="text-muted small mb-4">Update your account security regularly.</p>
                                <button class="btn btn-light w-100 rounded-pill py-3 fw-bold border" data-bs-toggle="modal" data-bs-target="#passwordModal">
                                    Update Password <i class="ti ti-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- PIN Update -->
                    <div class="col-12 col-md-6">
                        <div class="card shadow-lg border-0 h-100" style="border-radius: 20px; overflow: hidden;">
                            <div class="card-body p-4">
                                <div class="stat-icon bg-danger-subtle text-danger mb-3"><i class="ti ti-key"></i></div>
                                <h4 class="fw-bold mb-2">Transaction PIN</h4>
                                <p class="text-muted small mb-4">Required for every payment authorization.</p>
                                <button class="btn btn-light w-100 rounded-pill py-3 fw-bold border" data-bs-toggle="modal" data-bs-target="#pinModal">
                                    Reset PIN <i class="ti ti-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Sessions -->
                    <div class="col-12 mt-3">
                        <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                            <div class="card-body p-3">
                                <h5 class="fw-bold mb-3 fs-14">Active Session</h5>
                                <div class="d-flex align-items-center flex-wrap gap-3 p-3 bg-light" style="border-radius: 20px;">
                                    <div class="icon-circle bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="ti ti-device-laptop text-primary fs-14"></i>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-bold text-primary">Current Device</div>
                                        <div class="text-muted small text-break">
                                            <span>IP: {{ request()->ip() }}</span> | <span>Active Now</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTIVITY TAB -->
            <div class="tab-pane fade" id="activity" role="tabpanel">
                <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0 text-primary">Security Log</h4>
                        <button class="btn btn-sm btn-light rounded-pill px-3">View More</button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Action</th>
                                        <th>Status</th>
                                        <th>Time</th>
                                        <th class="pe-4">IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="p-2 bg-primary-subtle text-primary rounded-circle small"><i class="ti ti-login"></i></span>
                                                <span class="fw-bold">Sign In</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success">Success</span></td>
                                        <td>{{ now()->format('M d, H:i') }}</td>
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
            <div class="modal-content modal-content-premium shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Update Avatar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="photoUploadForm" action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4 text-center">
                        <div class="mb-4" id="uploadPlaceholder">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background: rgba(233,60,17,0.05);">
                                <i class="ti ti-photo-plus text-primary fs-1"></i>
                            </div>
                            <h6 class="fw-bold mt-3">Select Image</h6>
                        </div>
                        <input type="file" id="photoInput" name="photo" class="form-control" accept="image/*" required>
                        <div id="cropperContainer" class="d-none mt-3" style="max-height: 400px; width: 100%; border-radius: 1rem; overflow: hidden;">
                            <img id="cropperImage" src="" style="max-width: 100%;">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="cropAndUploadBtn" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: var(--primary-gradient); border: none;" disabled>Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-premium shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Change Login Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4 pt-0">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Current Password</label>
                            <input type="password" name="current_password" class="form-control rounded-pill bg-light border-0 px-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">New Password</label>
                            <input type="password" name="password" class="form-control rounded-pill bg-light border-0 px-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-pill bg-light border-0 px-3" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 text-center">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2" style="background: var(--primary-gradient); border: none;">Apply New Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PIN Modal -->
    <div class="modal fade" id="pinModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-premium shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-danger">Reset Transaction PIN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('profile.pin') }}">
                    @csrf
                    <div class="modal-body p-4 pt-0">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Account Password</label>
                            <input type="password" name="current_password" class="form-control rounded-pill bg-light border-0 px-3" required>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-bold text-muted small">New PIN</label>
                                <input type="password" name="pin" maxlength="5" class="form-control rounded-pill bg-light border-0 text-center fw-bold" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-muted small">Repeat PIN</label>
                                <input type="password" name="pin_confirmation" maxlength="5" class="form-control rounded-pill bg-light border-0 text-center fw-bold" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-danger w-100 rounded-pill py-2">Activate PIN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Copy Utility
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

        // Profile Photo Logic
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
                    cropper = new Cropper(cropperImage, { aspectRatio: 1, viewMode: 1, autoCropArea: 1 });
                }
            });
        }

        const styleCircle = document.createElement('style');
        styleCircle.innerHTML = `.cropper-view-box, .cropper-face { border-radius: 50%; }`;
        document.head.appendChild(styleCircle);

        if(uploadBtn) {
            uploadBtn.addEventListener('click', function () {
                if (!cropper) return;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>';
                this.disabled = true;
                cropper.getCroppedCanvas({ width: 400, height: 400 }).toBlob((blob) => {
                    const formData = new FormData(photoForm);
                    formData.set('photo', blob, 'profile.jpg');
                    fetch(photoForm.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' }})
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
    });
</script>

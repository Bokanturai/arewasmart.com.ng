<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Referral Dashboard' }}</title>
    
    <div class="container-fluid px-3 px-md-4 mt-3 mt-md-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
                    <h3 class="fw-bold text-dark mb-0 fs-20 fs-md-3">
                        <i class="ti ti-user-plus me-2 text-primary"></i>Referral System
                    </h3>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill align-self-start align-self-sm-auto">
                        <i class="ti ti-gift me-1"></i> Earn Rewards
                    </span>
                </div>
            </div>
        </div>

        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 mb-4 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-circle-check fs-20 me-2"></i>
                    <div class="fw-semibold">{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 mb-4 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-triangle fs-20 me-2"></i>
                    <div class="fw-semibold">{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Stats Overview -->
        <div class="row g-3 g-md-4 mb-4">
            <div class="col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden stats-card">
                    <div class="card-body p-3 p-md-4 text-white position-relative" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="fs-12 fw-medium opacity-75 text-uppercase">Total Earnings</span>
                                <h2 class="fw-bold mb-1 mt-2 fs-2">₦{{ number_format($totalEarnings, 2) }}</h2>
                                <p class="mb-0 fs-12 opacity-75">Accumulated bonuses</p>
                            </div>
                            <div class="bg-opacity-20 rounded-3 p-2">
                                <i class="ti ti-currency-naira fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 g-md-4">
            <!-- Sharing Card -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="fw-bold mb-2 fs-20">Spread the word</h5>
                        <p class="text-muted mb-4 small">
                            Share your referral link with friends and business owners. When they register and meet the transaction milestones, you earn <strong>₦{{ number_format($referralAmount, 2) }}</strong> instantly!
                        </p>
                        
                        <!-- Referral Link Box -->
                        <div class="bg-light rounded-4 p-3 p-md-4 border border-dashed mb-4">
                            <label class="form-label fw-semibold text-dark fs-12 mb-2">
                                <i class="ti ti-link me-1"></i>Your Invitation Link
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-white border-0 fw-bold fs-14" id="referralLink" readonly value="{{ $referralLink }}">
                                <button class="btn btn-primary rounded-end px-3" onclick="copyReferral()" type="button">
                                    <i class="ti ti-copy me-1"></i> Copy
                                </button>
                            </div>
                            <div id="copySuccess" class="alert alert-success mt-3 py-2 px-3 fs-12 d-none mb-0">
                                <i class="ti ti-check me-1"></i> Link copied to clipboard!
                            </div>
                        </div>

                        <!-- Create Custom Referral Code -->
                        <div class="bg-light rounded-4 p-3 p-md-4 border mb-4 position-relative overflow-hidden" style="border-color: rgba(78, 115, 223, 0.15) !important;">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                                <label class="form-label fw-semibold text-dark mb-0">
                                    <i class="ti ti-edit me-1 text-primary"></i>Create Custom Referral Code
                                </label>
                                <span class="badge bg-primary bg-opacity-10 text-primary fs-11 align-self-start align-self-sm-auto" id="charCount">0 / 20</span>
                            </div>
                            
                            <form action="{{ route('referrals.updateCode') }}" method="POST" id="updateReferralForm">
                                @csrf
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control fw-bold border-0 bg-white" 
                                               name="referral_code" 
                                               id="customReferralCode" 
                                               maxlength="20" 
                                               value="{{ old('referral_code', $user->referral_code) }}"
                                               placeholder="Enter custom code"> 
                                        <button type="submit" class="btn btn-primary px-3">
                                            <i class="ti ti-device-floppy me-1"></i> Save
                                        </button>
                                    </div>
                                </div>
                                <div class="form-text fs-11 text-muted mb-2">
                                    <i class="ti ti-info-circle me-1"></i> Only alphanumeric characters allowed. Max 20 characters.
                                </div>
                                
                                <div id="availabilityStatus" class="mt-2 fs-11 fw-semibold d-none"></div>

                                <div class="mt-3 p-2 bg-white rounded-3 border fs-11 text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-link text-primary me-2 flex-shrink-0"></i>
                                        <div class="text-truncate">
                                            <strong>Link Preview:</strong>
                                            <code class="text-primary ms-1" id="referralPreview" style="background-color: transparent; padding: 0;">{{ config('app.url') }}/register?ref={{ $user->referral_code }}</code>
                                        </div>
                                    </div>
                                </div>

                                @error('referral_code')
                                    <div class="text-danger mt-2 small fw-semibold">
                                        <i class="ti ti-alert-triangle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </form>
                        </div>

                        <!-- Share Buttons -->
                        <h6 class="fw-bold mb-3 text-muted fs-12 text-uppercase">
                            <i class="ti ti-share me-1"></i>Fast Share
                        </h6>
                        <div class="d-grid gap-2 d-sm-flex flex-wrap">
                            <button class="btn btn-success flex-grow-1" onclick="shareWhatsApp()" type="button">
                                <i class="ti ti-brand-whatsapp me-2"></i> WhatsApp
                            </button>
                            <button class="btn btn-primary flex-grow-1" onclick="shareFacebook()" type="button">
                                <i class="ti ti-brand-facebook me-2"></i> Facebook
                            </button>
                            <button class="btn btn-info flex-grow-1 text-white" onclick="shareTwitter()" type="button">
                                <i class="ti ti-brand-twitter me-2"></i> Twitter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- How it works -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white">
                    <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-4 fs-20">
                                <i class="ti ti-help me-2"></i>How it works?
                            </h5>
                            
                            <div class="d-flex align-items-start mb-4">
                                <div class="step-number bg-white text-primary rounded-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px;">1</div>
                                <div>
                                    <h6 class="fw-bold mb-1 fs-15">Send Invitation</h6>
                                    <p class="mb-0 fs-13 opacity-75">Send your referral link to your friends and associates.</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-4">
                                <div class="step-number bg-white text-primary rounded-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px;">2</div>
                                <div>
                                    <h6 class="fw-bold mb-1 fs-15">Registration</h6>
                                    <p class="mb-0 fs-13 opacity-75">They sign up as a user or agent using your specific link.</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-4">
                                <div class="step-number bg-white text-primary rounded-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px;">3</div>
                                <div>
                                    <h6 class="fw-bold mb-1 fs-15">Qualify Milestone</h6>
                                    <p class="mb-0 fs-13 opacity-75">They complete 5 qualified transactions on their account.</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-3">
                                <div class="step-number bg-white text-primary rounded-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px;">4</div>
                                <div>
                                    <h6 class="fw-bold mb-1 fs-15">Get ₦{{ number_format($referralAmount, 2) }} Reward</h6>
                                    <p class="mb-0 fs-13 opacity-75">Get credited instantly in your claimable bonus balance when milestones are met!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Qualified Transaction Rules Info -->
                        <div class="mt-3 p-3 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-10">
                            <h6 class="fw-bold mb-2 fs-12 text-white text-uppercase">
                                <i class="ti ti-alert-circle me-1 text-warning"></i>Qualified Transaction Rules
                            </h6>
                            <ul class="list-unstyled mb-0 fs-12 opacity-90 ps-0">
                                <li class="mb-1"><i class="ti ti-checks text-warning me-1"></i><strong>Debit Purchases:</strong> Must be <strong>₦1,000</strong> or above per transaction.</li>
                                <li><i class="ti ti-checks text-warning me-1"></i><strong>Credit Deposits:</strong> Must be <strong>₦2,000</strong> or above per transaction.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referrals Table -->
            <div class="col-12 mt-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-3 px-md-4 pb-0">
                        <h5 class="fw-bold mb-0 fs-20">
                            <i class="ti ti-users me-2"></i>Active Referrals (In-Progress)
                        </h5>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 rounded-start py-3">Date</th>
                                        <th class="border-0 py-3">Referred User</th>
                                        <th class="border-0 py-3">Amount</th>
                                        <th class="border-0 rounded-end py-3 text-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bonusHistory as $history)
                                        <tr>
                                            <td class="py-3">
                                                <div class="d-flex flex-column">
                                                    <span class="fw-medium text-dark">{{ $history->created_at->format('M d, Y') }}</span>
                                                    <small class="text-muted">{{ $history->created_at->format('h:i A') }}</small>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                        <i class="ti ti-user fs-14"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold text-dark">
                                                            {{ $history->referredUser->first_name ?? 'New' }} 
                                                            {{ $history->referredUser->middle_name ?? '' }} 
                                                            {{ $history->referredUser->last_name ?? 'User' }}
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="ti ti-activity me-1"></i>
                                                            {{ $history->referred_user_transaction_count }}/5 Transactions
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <span class="fw-bold {{ $history->status == 'success' ? 'text-success' : 'text-warning' }}">
                                                    {{ $history->status == 'success' ? '+' : '' }} ₦{{ number_format($history->amount, 2) }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-end">
                                                @if($history->status == 'success')
                                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                                        <i class="ti ti-check me-1"></i> Credited
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                                                        <i class="ti ti-clock me-1"></i> Pending
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="ti ti-package fs-1 opacity-25 d-block mb-3"></i>
                                                No referral history found yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .stats-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .avatar-sm {
            width: 40px;
            height: 40px;
            min-width: 40px;
        }
        
        .avatar-sm i {
            font-size: 18px;
        }
        
        .customize-box:focus-within {
            border-color: #4e73df !important;
            box-shadow: 0 4px 18px rgba(78, 115, 223, 0.08);
        }
        
        #customReferralCode:focus {
            background-color: #f8f9fc !important;
            box-shadow: none;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .spinner {
            animation: spin 1s linear infinite;
            display: inline-block;
        }
        
        /* Responsive Table */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 13px;
            }
            
            .avatar-sm {
                width: 32px;
                height: 32px;
                min-width: 32px;
            }
            
            .avatar-sm i {
                font-size: 14px;
            }
            
            .step-number {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }
            
            .btn-sm-mobile {
                font-size: 12px;
                padding: 6px 12px;
            }
        }
        
        @media (max-width: 576px) {
            .stats-card h2 {
                font-size: 1.5rem !important;
            }
            
            .stats-card .fs-12 {
                font-size: 10px !important;
            }
            
            #referralLink, #customReferralCode {
                font-size: 12px !important;
            }
            
            .btn {
                font-size: 13px;
                padding: 8px 12px;
            }
        }
        
        /* Hover effects */
        .btn {
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Custom scrollbar for table */
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function copyReferral() {
            const copyText = document.getElementById("referralLink");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            
            const successMsg = document.getElementById("copySuccess");
            successMsg.classList.remove("d-none");
            setTimeout(() => {
                successMsg.classList.add("d-none");
            }, 3000);
        }

        function shareWhatsApp() {
            const link = document.getElementById("referralLink").value;
            const text = encodeURIComponent("Join me on Arewa Smart and earn bonuses! 🎉\n\nUse my referral link: " + link);
            window.open("https://api.whatsapp.com/send?text=" + text, "_blank");
        }

        function shareFacebook() {
            const url = encodeURIComponent(document.getElementById("referralLink").value);
            window.open("https://www.facebook.com/sharer/sharer.php?u=" + url, "_blank", "width=600,height=400");
        }

        function shareTwitter() {
            const link = document.getElementById("referralLink").value;
            const text = encodeURIComponent("Join me on Arewa Smart! Earn bonuses when you sign up: " + link);
            window.open("https://twitter.com/intent/tweet?text=" + text, "_blank", "width=600,height=400");
        }

        // Real-time validation for custom referral code
        document.addEventListener("DOMContentLoaded", function () {
            const referralInput = document.getElementById('customReferralCode');
            const charCountDisplay = document.getElementById('charCount');
            const previewDisplay = document.getElementById('referralPreview');
            const statusDiv = document.getElementById('availabilityStatus');
            const initialCode = "{{ $user->referral_code }}";
            let checkTimeout;
            
            if (referralInput && charCountDisplay) {
                const baseUrl = "{{ config('app.url') }}/register?ref=";
                
                const updateCount = () => {
                    const len = referralInput.value.length;
                    charCountDisplay.textContent = `${len} / 20`;
                    if (len >= 18) {
                        charCountDisplay.classList.remove('bg-primary', 'bg-opacity-10');
                        charCountDisplay.classList.add('bg-danger', 'bg-opacity-10', 'text-danger');
                    } else {
                        charCountDisplay.classList.remove('bg-danger', 'bg-opacity-10', 'text-danger');
                        charCountDisplay.classList.add('bg-primary', 'bg-opacity-10', 'text-primary');
                    }
                };
                
                referralInput.addEventListener('input', (e) => {
                    // Alphanumeric constraint
                    let sanitized = e.target.value.replace(/[^a-zA-Z0-9]/g, '');
                    if (sanitized !== e.target.value) {
                        e.target.value = sanitized;
                    }
                    
                    // Update preview
                    if (previewDisplay) {
                        previewDisplay.textContent = baseUrl + (e.target.value || 'YOURCODE');
                    }
                    
                    updateCount();
                    
                    // Check availability
                    const currentCode = e.target.value.trim();
                    clearTimeout(checkTimeout);
                    
                    if (currentCode === '' || currentCode.toLowerCase() === initialCode.toLowerCase()) {
                        statusDiv.classList.add('d-none');
                        return;
                    }
                    
                    statusDiv.className = "mt-2 fs-11 fw-semibold text-secondary";
                    statusDiv.innerHTML = '<i class="ti ti-refresh spinner d-inline-block me-1"></i> Checking availability...';
                    statusDiv.classList.remove('d-none');
                    
                    checkTimeout = setTimeout(() => {
                        fetch(`/referrals/check?code=${encodeURIComponent(currentCode)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.available) {
                                    statusDiv.className = "mt-2 fs-11 fw-semibold text-success";
                                    statusDiv.innerHTML = `<i class="ti ti-circle-check me-1"></i> ${data.message}`;
                                } else {
                                    statusDiv.className = "mt-2 fs-11 fw-semibold text-danger";
                                    statusDiv.innerHTML = `<i class="ti ti-alert-triangle me-1"></i> ${data.message}`;
                                }
                            })
                            .catch(err => {
                                statusDiv.classList.add('d-none');
                            });
                    }, 500);
                });
                
                updateCount();
            }
        });
    </script>
    @endpush
</x-app-layout>
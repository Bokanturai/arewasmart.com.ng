<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Buy Airtime' }}</title>

    {{-- Custom CSS --}}
    @push('styles')
    <style>
        .network-option {
            cursor: pointer;
            padding: 10px;
            border: 2px solid transparent;
            border-radius: 10px;
            transition: all 0.2s ease-in-out;
            position: relative;
        }
        .network-option:hover {
            background-color: #f8f9fa;
        }
        .network-option.active {
            border-color: #df6808ff;
            background-color: #e7f1ff;
        }
        .network-option .check-mark {
            display: none;
            position: absolute;
            top: -5px;
            right: -5px;
            background: #fff;
            border-radius: 50%;
            z-index: 5;
            line-height: 1;
        }
        .network-option.active .check-mark {
            display: block;
        }
        .small-note {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Recent Recipients Scrollable Container */
        #recentRecipientsBody {
            max-height: 500px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #df6808ff #f8f9fa;
        }

        #recentRecipientsBody::-webkit-scrollbar {
            width: 6px;
        }

        #recentRecipientsBody::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 10px;
        }

        #recentRecipientsBody::-webkit-scrollbar-thumb {
            background-color: #df6808ff;
            border-radius: 10px;
        }

        #recentRecipientsBody::-webkit-scrollbar-thumb:hover {
            background-color: #c55a06ff;
        }
    </style>
    @endpush

    <div class="container-fluid px-0 px-md-3">
        <div class="row g-0 g-md-4 justify-content-center mt-3">
            
            {{-- Left Column: Airtime Form --}}
            <div class="col-12 col-xl-5 mb-4">
                <div class="card shadow-lg border-0 rounded-20px h-100">

                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-3 p-md-4 rounded-20px rounded-bottom-0">
                        <div class="card-title fw-bold mb-0 fs-12 fs-md-5">
                            <i class="bi bi-phone me-2"></i> Buy Airtime
                        </div>
                        <span class="badge bg-white text-primary fw-bold px-2 px-md-3 py-2 rounded-pill">Top-up</span>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        {{-- Alerts --}}
                        <div class="mb-4">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show text-center rounded-3 border-0 shadow-sm small py-2">
                                    <i class="bi bi-check-circle-fill me-2"></i> {!! session('success') !!}
                                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show text-center rounded-3 border-0 shadow-sm small py-2">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm small py-2">
                                    <ul class="mb-0 text-start small list-unstyled">
                                        @foreach ($errors->all() as $error)
                                            <li><i class="bi bi-dot me-1"></i>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                        </div>

                        <div class="text-center mb-3 mb-md-4">
                            <div class="avatar-wrapper bg-primary bg-opacity-10 text-primary rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-broadcast fs-15"></i>
                            </div>
                            <h6 class="fw-bold small">Instant Airtime Top-up</h6>
                            <p class="text-muted small px-3">Top up any network instantly with zero hidden fees.</p>
                        </div>

                        {{-- Airtime Form --}}
                        <form id="buyAirtimeForm" method="POST" action="{{ route('buyairtime') }}">
                            @csrf
                            <input type="hidden" id="selectedNetwork" name="network" value="{{ old('network') }}">

                            {{-- Phone Number --}}
                            <div class="mb-3 mb-md-4">
                                <label for="mobileno" class="form-label text-dark small mb-1">Recipient phone number</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-telephone text-muted"></i>
                                    </span>
                                    <input type="tel" id="mobileno" name="mobileno" value="{{ old('mobileno') }}" class="form-control border-start-0 ps-0 text-center text-dark" maxlength="11" pattern="\d{11}" placeholder="080 0000 0000" required>
                                </div>
                                <div id="networkResult" class="mt-2 small-note text-center fw-bold text-primary" style="min-height: 1.2em;"></div>
                            </div>

                            {{-- Network Selection --}}
                            <div class="network-selection mb-3 mb-md-4">
                                <label class="form-label fw-semibold text-dark small mb-2 text-center d-block w-100">Select network operator</label>
                                <div class="row text-center g-2 g-sm-3 justify-content-center">
                                    @php
                                        $networks = [
                                            'mtn'      => ['name' => 'MTN',    'img' => 'mtn.jpg'],
                                            'airtel'   => ['name' => 'Airtel', 'img' => 'Airtel.png'],
                                            'glo'      => ['name' => 'Glo',    'img' => 'glo.jpg'],
                                            'etisalat' => ['name' => '9Mobile','img' => '9Mobile.jpg'],
                                        ];
                                    @endphp

                                    @foreach ($networks as $key => $network)
                                        <div class="col-3">
                                            <div class="network-option d-flex flex-column align-items-center p-2 rounded-4 border border-light shadow-sm" data-network="{{ $key }}" style="cursor: pointer; transition: all 0.2s;">
                                                <i class="bi bi-check-circle-fill text-primary check-mark"></i>
                                                <img src="{{ asset('assets/img/apps/' . $network['img']) }}" alt="{{ $network['name'] }}" class="rounded-circle mb-1 shadow-sm" style="width: 38px; height: 38px; object-fit: contain;" onerror="this.src='{{ asset('assets/img/apps/default.png') }}'">
                                                <div class="small fw-bold text-dark" style="font-size: 10px;">
                                                    {{ $network['name'] }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Amount --}}
                            <div class="mb-3 mb-md-4">
                                <label for="amount" class="form-label d-flex justify-content-between align-items-center fw-semibold text-dark small mb-2">
                                    <span>Amount</span>
                                    <small class="text-muted fw-normal d-flex align-items-center">
                                        <span>Bal: </span>
                                        <strong id="walletBalance" class="text-success ms-1 d-none">₦{{ number_format($wallet->balance ?? 0, 2) }}</strong>
                                        <strong id="hiddenBalance" class="text-success ms-1">₦ * * * *</strong>
                                        <i id="toggleBalance" class="bi bi-eye ms-2 text-primary" style="cursor: pointer; font-size: 0.9rem;"></i>
                                    </small>
                                </label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light border-end-0 fw-bold text-secondary">₦</span>
                                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}" class="form-control border-start-0 ps-0 text-center fw-bold text-dark fs-15" min="50" max="50000" placeholder="0.00" required>
                                </div>

                                {{-- Quick Amount Suggestions --}}
                                <div class="row g-2 mt-2">
                                    @php $amounts = [100, 200, 500, 1000, 2000]; @endphp
                                    @foreach ($amounts as $amt)
                                        <div class="col px-1">
                                            <button type="button" class="btn btn-light w-100 amount-btn btn-sm fw-bold border-0 shadow-sm text-muted rounded-pill py-1" style="font-size: 10px;" data-amount="{{ $amt }}">
                                                ₦{{ number_format($amt) }}
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="d-grid mt-4">
                                <button type="button" id="buy-airtime" class="btn btn-primary btn-lg fw-bold rounded-pill shadow-lg py-2 py-md-3">
                                    <i class="bi bi-lightning-charge-fill me-2"></i> Purchase Now
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer bg-white border-top text-center py-3 rounded-20px rounded-top-0">
                        <small class="text-muted d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-shield-lock-fill text-primary"></i>
                            Secure 256-bit Encrypted Transaction
                        </small>
                    </div>
                </div>
            </div>

            {{-- Recent Airtime Purchases Column --}}
            <div class="col-12 col-xl-7 mt-2 mt-md-0">
                <div class="card shadow-lg border-0 rounded-20px h-100">
                            {{-- Header --}}
                            <div class="card-header bg-white p-3 p-md-4 border-bottom d-flex align-items-center rounded-20px rounded-bottom-0">
                                <i class="bi bi-clock-history text-primary me-2 fs-15"></i>
                                <div>
                                    <h5 class="mb-0 fw-bold text-dark fs-15">Recent Airtime Purchases</h5>
                                    <p class="text-muted small mb-0">Tap a recent purchase to re-fill form details</p>
                                </div>
                            </div>

                            <div class="card-body p-3 p-md-4" id="recentRecipientsBody">
                                @if(isset($recentRecipients) && count($recentRecipients) > 0)
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($recentRecipients as $recipient)
                                            <div class="d-flex align-items-center p-3 rounded-4 bg-light border-0 shadow-sm recipient-item"
                                                 style="cursor:pointer; transition:all .2s;"
                                                 onclick="selectRecentRecipient('{{ $recipient['account_no'] }}', '{{ $recipient['bank_code'] }}')">

                                                <div class="bg-white rounded-circle shadow-sm me-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                                     style="width:42px;height:42px;border:1px solid rgba(0,0,0,.08);">
                                                    @if(!empty($recipient['bank_url']))
                                                        <img src="{{ $recipient['bank_url'] }}" alt="{{ $recipient['bank_name'] }}"
                                                             style="width:26px;height:26px;object-fit:contain;"
                                                             onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                                                        <i class="bi bi-phone-fill text-primary" style="display:none;"></i>
                                                    @else
                                                        <i class="bi bi-phone-fill text-primary"></i>
                                                    @endif
                                                </div>

                                                <div class="flex-grow-1 min-w-0">
                                                    <span class="fw-bold d-block small text-truncate text-dark">{{ $recipient['account_no'] }}</span>
                                                    <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                                        <span class="text-muted" style="font-size: 11px;">
                                                            {{ $recipient['bank_name'] }}
                                                        </span>
                                                        <span class="text-muted d-none d-sm-inline" style="font-size: 11px;">•</span>
                                                        <small class="text-muted" style="font-size: 11px;">
                                                            {{ $recipient['date'] }}
                                                        </small>
                                                    </div>
                                                </div>

                                                <div class="text-end ms-3 flex-shrink-0">
                                                    <span class="fw-bold d-block text-dark" style="font-size: 14px;">₦{{ number_format($recipient['amount'], 2) }}</span>
                                                    <div class="mt-1">
                                                        @if($recipient['status'] === 'successful' || $recipient['status'] === 'processing')
                                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill py-1 px-2" style="font-size: 9px; font-weight: 600;">
                                                                Success
                                                            </span>
                                                        @elseif($recipient['status'] === 'failed')
                                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill py-1 px-2" style="font-size: 9px; font-weight: 600;">
                                                                Failed
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill py-1 px-2" style="font-size: 9px; font-weight: 600;">
                                                                Processing
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width:64px;height:64px;">
                                            <i class="bi bi-phone fs-2 text-muted opacity-50"></i>
                                        </div>
                                        <h6 class="fw-semibold">No Recent Purchases</h6>
                                        <p class="small text-muted mb-0 px-4">
                                            Your recent purchases will appear here after your first airtime transaction.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer bg-white border-top text-center py-3 rounded-20px rounded-top-0">
                                <small class="text-muted d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-patch-check-fill text-primary"></i>
                                    Protected by Arewa Smart Multi-Factor Authentication
                                </small>
                            </div>
                        </div>
                    </div>

    {{-- PIN Modal --}}
    @include('pages.pin')

    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const networkOptions      = document.querySelectorAll('.network-option');
            const selectedNetworkInput = document.getElementById('selectedNetwork');
            const amountInput         = document.getElementById('amount');
            const amountButtons       = document.querySelectorAll('.amount-btn');
            const phoneInput          = document.getElementById('mobileno');
            const networkResultDiv    = document.getElementById('networkResult');
            const buyButton           = document.getElementById('buy-airtime');
            const toggleBalance       = document.getElementById('toggleBalance');
            const walletBalance       = document.getElementById('walletBalance');
            const hiddenBalance       = document.getElementById('hiddenBalance');

            // --- Toggle Balance Visibility ---
            if (toggleBalance) {
                toggleBalance.addEventListener('click', function() {
                    if (walletBalance.classList.contains('d-none')) {
                        walletBalance.classList.remove('d-none');
                        hiddenBalance.classList.add('d-none');
                        this.classList.replace('bi-eye', 'bi-eye-slash');
                    } else {
                        walletBalance.classList.add('d-none');
                        hiddenBalance.classList.remove('d-none');
                        this.classList.replace('bi-eye-slash', 'bi-eye');
                    }
                });
            }

            // --- Network selection ---
            networkOptions.forEach(option => {
                option.addEventListener('click', function () {
                    networkOptions.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');
                    selectedNetworkInput.value = this.dataset.network;
                    
                    // Add visual feedback
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => this.style.transform = 'scale(1)', 100);
                });
            });

            // --- Quick amount selection ---
            amountButtons.forEach(button => {
                button.addEventListener('click', function () {
                    amountInput.value = this.dataset.amount;
                    amountInput.classList.add('is-valid');
                    setTimeout(() => amountInput.classList.remove('is-valid'), 500);
                });
            });

            // --- Auto network detection ---
            const networkPrefixes = {
                'mtn':      ['0803','0806','0703','0706','0810','0813','0814','0816','0903','0906','0913','0916','07025','07026','0704','09065'],
                'glo':      ['0805','0807','0705','0811','0815','0905','0915'],
                'airtel':   ['0802','0808','0701','0708','0812','0901','0902','0904','0907','0912'],
                'etisalat': ['0809','0817','0818','0908','0909']
            };

            const detectNetwork = function () {
                const val = phoneInput.value.replace(/\s+/g, '');
                if (val.length >= 4) {
                    const prefix = val.substring(0, 4);
                    const prefix5 = val.substring(0, 5);
                    
                    for (const network in networkPrefixes) {
                        if (networkPrefixes[network].includes(prefix) || networkPrefixes[network].includes(prefix5)) {
                            const opt = document.querySelector(`.network-option[data-network="${network}"]`);
                            if (opt && !opt.classList.contains('active')) {
                                opt.click();
                                networkResultDiv.innerHTML = `<i class="bi bi-check-circle me-1"></i> ${network.toUpperCase()} detected`;
                            }
                            return;
                        }
                    }
                }
                // Don't clear manual selection if no match found, just clear the "detected" text
                if (val.length < 4) networkResultDiv.textContent = '';
            };

            phoneInput.addEventListener('input', detectNetwork);
            phoneInput.addEventListener('paste', () => setTimeout(detectNetwork, 100));

            // --- Recent Recipient selection ---
            window.selectRecentRecipient = function(number, network) {
                phoneInput.value = number;
                const option = document.querySelector(`.network-option[data-network="${network}"]`);
                if (option) {
                    option.click();
                } else {
                    phoneInput.dispatchEvent(new Event('input'));
                }
                phoneInput.focus();
            };

            // --- Open Modal (Step 1) ---
            if (buyButton) {
                buyButton.addEventListener('click', function () {
                    const amount = amountInput.value;
                    const number = phoneInput.value;
                    const network = selectedNetworkInput.value;

                    if (!number || number.length < 11) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Invalid Phone Number',
                            text: 'Please enter a valid 11-digit phone number.',
                            confirmButtonColor: '#ffc107',
                        });
                        phoneInput.focus();
                        return;
                    }
                    if (!network) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Select Operator',
                            text: 'Please select a network operator.',
                            confirmButtonColor: '#ffc107',
                        });
                        return;
                    }
                    if (!amount || amount < 50) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Invalid Amount',
                            text: 'Minimum recharge is ₦50.',
                            confirmButtonColor: '#ffc107',
                        });
                        amountInput.focus();
                        return;
                    }

                    // Populate Summary
                    document.getElementById('confirmAccountName').textContent = number;
                    document.getElementById('confirmBankName').textContent = network.toUpperCase() + ' Airtime';
                    document.getElementById('confirmAccountNo').textContent = number;
                    document.getElementById('confirmAmount').textContent = '₦' + parseFloat(amount).toLocaleString(undefined, {minimumFractionDigits: 2});

                    const pinModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('pinModal'));
                    pinModal.show();
                });
            }

            // --- Modal Step Navigation (Summary -> PIN) ---
            const btnGoToPin = document.getElementById('btnGoToPin');
            if (btnGoToPin) {
                btnGoToPin.addEventListener('click', () => {
                    document.getElementById('confirmationStep')?.classList.add('d-none');
                    document.getElementById('pinStep')?.classList.remove('d-none');
                    document.getElementById('confirmationStep_footer')?.classList.add('d-none');
                    document.getElementById('pinStep_footer')?.classList.remove('d-none');
                    
                    document.getElementById('modalTitle').textContent = 'Authorize Airtime';
                    document.getElementById('modalSubtitle').textContent = 'Step 2 of 2 — Security PIN';
                    
                    setTimeout(() => document.getElementById('pinInput')?.focus(), 100);
                });
            }

            // --- Final Authorization ---
            const confirmPinBtn = document.getElementById('confirmPinBtn');
            if (confirmPinBtn) {
                confirmPinBtn.addEventListener('click', function () {
                    const pin = document.getElementById('pinInput').value;
                    const pinError = document.getElementById('pinError');
                    const pinErrorText = document.getElementById('pinErrorText');
                    
                    if (!pin || pin.length !== 5) {
                        if (pinErrorText) pinErrorText.textContent = 'Please enter your 5-digit PIN.';
                        if (pinError) pinError.classList.remove('d-none');
                        return;
                    }

                    confirmPinBtn.disabled = true;
                    const loader = document.getElementById('pinLoader');
                    const btnText = document.getElementById('confirmPinText');
                    if (loader) loader.classList.remove('d-none');
                    if (btnText) btnText.textContent = 'Verifying...';

                    fetch("{{ route('verify.pin') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ pin })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.valid) {
                            const form = document.getElementById('buyAirtimeForm');
                            const hiddenPin = document.createElement('input');
                            hiddenPin.type = 'hidden';
                            hiddenPin.name = 'pin';
                            hiddenPin.value = pin;
                            form.appendChild(hiddenPin);
                            form.submit();
                        } else {
                            if (pinErrorText) pinErrorText.textContent = 'Incorrect PIN. Try again.';
                            if (pinError) pinError.classList.remove('d-none');
                            confirmPinBtn.disabled = false;
                            if (loader) loader.classList.add('d-none');
                            if (btnText) btnText.textContent = 'Authorize Now';
                            document.getElementById('pinInput').value = '';
                        }
                    })
                    .catch(() => {
                        if (pinErrorText) pinErrorText.textContent = 'Network error. Please try again.';
                        if (pinError) pinError.classList.remove('d-none');
                        confirmPinBtn.disabled = false;
                        if (loader) loader.classList.add('d-none');
                        if (btnText) btnText.textContent = 'Authorize Now';
                    });
                });
            }
        });
    </script>
    @endpush

</x-app-layout>

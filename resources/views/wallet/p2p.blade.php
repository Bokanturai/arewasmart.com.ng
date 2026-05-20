<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Transfer to Smart User' }}</title>
    
    {{-- Custom CSS --}}
    @push('styles')
    <style>
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
        <div class="row justify-content-center py-3 py-lg-4 g-0 g-md-4">
            <div class="col-12 col-xl-11 col-xxl-10">
                <div class="row g-0 g-md-4 align-items-stretch">
                    
                    <div class="col-12 col-xl-5 mb-4">
                        <div class="card shadow-lg border-0 h-100" style="border-radius: 20px;">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-3 p-md-4" style="border-radius: 20px 20px 0 0;">
                                <h5 class="mb-0 fw-bold fs-12 fs-md-5"><i class="bi bi-send me-2"></i>Transfer Funds</h5>
                                <span class="badge bg-white text-primary fw-bold px-2 px-md-3 py-2 rounded-pill">P2P</span>
                            </div>

                            <div class="card-body p-3 p-md-4">
                                <div class="text-center mb-3 mb-md-4">
                                    <div class="avatar-wrapper bg-primary bg-opacity-10 text-primary rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="bi bi-wallet2 fs-15"></i>
                                    </div>
                                    <h6 class="fw-bold small">Send Money Instantly</h6>
                                    <p class="text-muted small px-3">Enter the recipient's Wallet ID, Phone, or Email.</p>
                                </div>

                                {{-- Flash Messages --}}
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm small py-2" role="alert">
                                        <i class="bi bi-check-circle-fill me-2"></i> {!! session('success') !!}
                                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm small py-2" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm small py-2" role="alert">
                                        <ul class="mb-0 text-start small list-unstyled">
                                            @foreach ($errors->all() as $error)
                                                <li><i class="bi bi-dot me-1"></i>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                {{-- Transfer Form --}}
                                <form id="transferForm" method="POST" action="{{ route('transfer.process') }}">
                                    @csrf

                                    {{-- Wallet ID / Email / Phone --}}
                                    <div class="mb-3 mb-md-4 text-start">
                                        <label class="form-label fw-semibold text-dark small text-uppercase">Recipient Identifier</label>
                                        <div class="d-flex flex-column flex-sm-row gap-2">
                                            <div class="flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-badge text-muted"></i></span>
                                                    <input type="text" id="wallet_id" name="wallet_id"
                                                           class="form-control border-start-0 ps-0"
                                                           placeholder="Wallet ID, Email, or Phone"
                                                           required>
                                                </div>
                                            </div>
                                            <button class="btn btn-primary px-4" type="button" id="verifyBtn" onclick="verifyUser()" style="white-space: nowrap;">
                                                Verify
                                            </button>
                                        </div>
                                        
                                        {{-- Recipient Info Card (Photo + Name) --}}
                                        <div id="verifiedUserCard" class="d-none mt-3 p-3 bg-light border border-primary border-opacity-10" style="border-radius: 20px;">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-wrapper me-3">
                                                    <img id="recipientPhoto" src="{{ asset('assets/img/avatars/1.png') }}" alt="User" class="rounded-circle border" style="width: 50px; height: 50px; object-fit: cover;">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 id="recipientName" class="mb-0 fw-bold small text-dark"></h6>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-success bg-opacity-10 text-success p-1 rounded-circle me-1" style="font-size: 8px;">
                                                            <i class="bi bi-check-lg"></i>
                                                        </span>
                                                        <small class="text-muted small" style="font-size: 10px;">Verified Arewa Smart User</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-2">
                                            <small id="userErrorDisplay" class="text-danger fw-bold small"></small>
                                        </div>
                                    </div>

                                    {{-- Amount --}}
                                    <div class="mb-3 mb-md-4 text-start">
                                        <label for="amount" class="form-label fw-semibold d-flex flex-column flex-sm-row justify-content-between">
                                            <span>Amount</span>
                                            <small class="text-muted">Balance: 
                                                <strong class="text-success">
                                                    ₦{{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}
                                                </strong>
                                            </small>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-currency-naira text-muted"></i></span>
                                            <input type="number" id="amount" name="amount"
                                                   class="form-control border-start-0 ps-0"
                                                   placeholder="0.00"
                                                   min="0.01" step="0.01"
                                                   required>
                                        </div>
                                    </div>

                                    {{-- Description --}}
                                    <div class="mb-3 mb-md-4 text-start">
                                        <label class="form-label fw-semibold text-dark small text-uppercase">Description (Optional)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-card-text text-muted"></i></span>
                                            <textarea id="description" name="description" class="form-control border-start-0 ps-0" rows="2" placeholder="What is this for?"></textarea>
                                        </div>
                                    </div>

                                    {{-- Submit --}}
                                    <div class="d-grid mt-3 mt-md-4">
                                        <button type="button" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm py-2 py-md-3"
                                                id="proceedBtn" disabled>
                                            Proceed to Transfer <i class="bi bi-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-7 mt-2 mt-md-0">
                        <div class="card shadow-lg border-0 h-100" style="border-radius: 20px;">
                            {{-- Header --}}
                            <div class="card-header bg-white p-3 p-md-4 border-bottom d-flex align-items-center" style="border-radius: 20px 20px 0 0;">
                                <i class="bi bi-clock-history text-primary me-2 fs-15"></i>
                                <div>
                                    <h5 class="mb-0 fw-bold text-dark fs-15">Recent Recipients</h5>
                                    <p class="text-muted small mb-0">Tap a recipient to auto-fill the form</p>
                                </div>
                            </div>

                            <div class="card-body p-3 p-md-4" id="recentRecipientsBody">
                                @if(isset($recentRecipients) && count($recentRecipients) > 0)
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($recentRecipients as $recipient)
                                            <div class="d-flex align-items-center p-3 bg-light border-0 shadow-sm recipient-item"
                                                 style="cursor:pointer; transition:all .2s; border-radius: 20px;"
                                                 onclick="selectRecentBank('{{ $recipient['bank_code'] }}', '{{ $recipient['account_no'] }}', '{{ $recipient['account_name'] }}')">

                                                <div class="bg-white rounded-circle shadow-sm me-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                                     style="width:42px;height:42px;border:1px solid rgba(0,0,0,.08);">
                                                    @if(!empty($recipient['bank_url']))
                                                        <img src="{{ $recipient['bank_url'] }}" alt="{{ $recipient['bank_name'] }}"
                                                             style="width:26px;height:26px;object-fit:contain;"
                                                             onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                                                        <i class="bi bi-person-fill text-primary" style="display:none;"></i>
                                                    @else
                                                        <i class="bi bi-person-fill text-primary"></i>
                                                    @endif
                                                </div>

                                                <div class="flex-grow-1 min-w-0">
                                                    <span class="fw-bold d-block small text-truncate text-dark">{{ $recipient['account_name'] }}</span>
                                                    <small class="text-muted text-truncate d-block" style="font-size: 11px;">
                                                        {{ $recipient['bank_name'] }} &bull; {{ $recipient['account_no'] }}
                                                    </small>
                                                </div>

                                                <i class="bi bi-chevron-right text-muted small ms-2 flex-shrink-0"></i>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width:64px;height:64px;">
                                            <i class="bi bi-people fs-2 text-muted opacity-50"></i>
                                        </div>
                                        <h6 class="fw-semibold">No Recent Transfers</h6>
                                        <p class="small text-muted mb-0 px-4">
                                            Your trusted recipients will appear here after your first successful transfer.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer bg-white border-top text-center py-3" style="border-radius: 0 0 20px 20px;">
                                <small class="text-muted d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-patch-check-fill text-primary"></i>
                                    Protected by Arewa Smart Multi-Factor Authentication
                                </small>
                            </div>
                        </div>
                    </div>

                </div>{{-- /row-inner --}}
            </div>{{-- /col-inner --}}
        </div>{{-- /row-outer --}}
    </div>{{-- /container --}}

    {{-- PIN Modal --}}
    @include('pages.pin')

    <script>
        let verifiedRecipient = null;

        function verifyUser() {
            const walletId = document.getElementById('wallet_id').value;
            const userErrorDisplay = document.getElementById('userErrorDisplay');
            const verifiedUserCard = document.getElementById('verifiedUserCard');
            const recipientName = document.getElementById('recipientName');
            const recipientPhoto = document.getElementById('recipientPhoto');
            const proceedBtn = document.getElementById('proceedBtn');
            const verifyBtn = document.getElementById('verifyBtn');

            if (!walletId) {
                userErrorDisplay.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Please enter a Wallet ID, Phone, or Email.';
                verifiedUserCard.classList.add('d-none');
                return;
            }

            // UI Feedback
            userErrorDisplay.innerHTML = "";
            verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            verifyBtn.disabled = true;

            fetch("{{ route('transfer.verify') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ wallet_id: walletId })
            })
            .then(response => response.json())
            .then(data => {
                verifyBtn.innerHTML = 'Verify';
                verifyBtn.disabled = false;

                if (data.success) {
                    verifiedRecipient = data;
                    recipientName.textContent = data.user_name;
                    recipientPhoto.src = data.photo || "{{ asset('assets/img/avatars/1.png') }}";
                    verifiedUserCard.classList.remove('d-none');
                    userErrorDisplay.innerHTML = "";
                    proceedBtn.disabled = false;
                    
                    // If the user used email/phone, update the field to the canonical Wallet ID
                    document.getElementById('wallet_id').value = data.wallet_id;
                } else {
                    userErrorDisplay.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> User not found.';
                    verifiedUserCard.classList.add('d-none');
                    proceedBtn.disabled = true;
                    verifiedRecipient = null;
                }
            })
            .catch(err => {
                console.error("Verification failed:", err);
                verifyBtn.innerHTML = 'Verify';
                verifyBtn.disabled = false;
                userErrorDisplay.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Verification failed.';
                verifiedUserCard.classList.add('d-none');
                proceedBtn.disabled = true;
            });
        }

        // Logic for Proceed Button (Populate Modal & Show)
        document.getElementById('proceedBtn').addEventListener('click', function() {
            const amount = document.getElementById('amount').value;
            if (!amount || amount <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Amount',
                    text: 'Please enter a valid amount.',
                    confirmButtonColor: '#ffc107',
                });
                return;
            }

            if (!verifiedRecipient) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Recipient Not Verified',
                    text: 'Please verify the recipient first.',
                    confirmButtonColor: '#ffc107',
                });
                return;
            }

            // Populate Modal Fields (from pages.pin)
            document.getElementById('confirmAccountName').textContent = verifiedRecipient.user_name;
            document.getElementById('confirmBankName').textContent = 'Arewa Smart Wallet';
            document.getElementById('confirmAccountNo').textContent = verifiedRecipient.wallet_id;
            document.getElementById('confirmAmount').textContent = '₦' + parseFloat(amount).toLocaleString(undefined, {minimumFractionDigits: 2});

            // Show Modal
            const pinModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('pinModal'));
            pinModal.show();
        });

        // Modal Step Navigation (Summary -> PIN)
        const btnGoToPin = document.getElementById('btnGoToPin');
        if (btnGoToPin) {
            btnGoToPin.addEventListener('click', () => {
                const confStep = document.getElementById('confirmationStep');
                const pinStep = document.getElementById('pinStep');
                if (confStep) confStep.classList.add('d-none');
                if (pinStep) pinStep.classList.remove('d-none');
                
                // Update headers if they exist
                const mt = document.getElementById('modalTitle');
                const ms = document.getElementById('modalSubtitle');
                if (mt) mt.textContent = 'Authorize Payout';
                if (ms) ms.textContent = 'Step 2 of 2 — Security PIN';
                
                setTimeout(() => document.getElementById('pinInput')?.focus(), 100);
            });
        }

        // PIN Confirmation Logic
        document.getElementById('confirmPinBtn').addEventListener('click', function() {
            const confirmBtn = this;
            const loader = document.getElementById('pinLoader');
            const confirmText = document.getElementById('confirmPinText');
            const pinError = document.getElementById('pinError');
            const pinErrorText = document.getElementById('pinErrorText');
            const pin = document.getElementById('pinInput').value.trim();

            if (!pin || pin.length !== 5) {
                pinErrorText.textContent = 'Please enter a valid 5-digit PIN.';
                pinError.classList.remove('d-none');
                return;
            }

            confirmBtn.disabled = true;
            loader.classList.remove('d-none');
            confirmText.textContent = "Verifying...";
            pinError.classList.add('d-none');

            // Verify PIN via AJAX first
            fetch("{{ route('verify.pin') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ pin })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    // Append PIN to the form and submit
                    const form = document.getElementById('transferForm');
                    const pinHiddenInput = document.createElement('input');
                    pinHiddenInput.type = 'hidden';
                    pinHiddenInput.name = 'pin';
                    pinHiddenInput.value = pin;
                    form.appendChild(pinHiddenInput);
                    
                    form.submit();
                } else {
                    pinErrorText.textContent = 'Incorrect PIN. Please try again.';
                    pinError.classList.remove('d-none');
                    confirmBtn.disabled = false;
                    loader.classList.add('d-none');
                    confirmText.textContent = "Authorize Now";
                    
                    // Clear input
                    document.getElementById('pinInput').value = '';
                    document.getElementById('pinInput').focus();
                }
            })
            .catch(err => {
                console.error("PIN check failed:", err);
                pinErrorText.textContent = 'Network error. Please try again.';
                pinError.classList.remove('d-none');
                confirmBtn.disabled = false;
                loader.classList.add('d-none');
                confirmText.textContent = "Authorize Now";
            });
        });
    </script>
</x-app-layout>
<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Withdraw Funds' }}</title>
    
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

    <div class="page-body">
        <div class="container-fluid px-3">

            {{-- Page Title --}}
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <h3 class="fw-bold text-primary">
                            <i class="bi bi-bank2 me-2"></i>Secure Payout
                        </h3>
                        <p class="text-muted small mb-0">
                            Transfer funds directly to any Nigerian bank account.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <div class="container-fluid px-0 px-md-3">
            <div class="row g-0 g-md-4">
                <div class="col-12 col-xl-5 mb-0 mb-md-4">
                    <div class="card shadow-lg border-0" style="border-radius: 20px;">

                        {{-- Card Header --}}
                        <div class="card-header bg-primary text-white p-3 p-md-4 border-0" style="border-radius: 20px 20px 0 0;">
                            <h5 class="mb-0 fw-bold fs-15">
                                <i class="bi bi-send-fill me-2"></i>New Payout
                            </h5>
                            <p class="mb-0 small text-white-50 mt-1">
                                Please verify recipient details carefully before submitting.
                            </p>
                        </div>

                        <div class="card-body p-3 p-md-4">

                            {{-- Flash Messages --}}
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i>{!! session('success') !!}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- Eligibility Banner --}}
                            @if($totalVolume < $eligibilityAmount)
                                <div class="alert alert-warning rounded-3 shadow-sm border-0 d-flex align-items-center mb-4">
                                    <i class="bi bi-hourglass-split fs-15 me-3 text-warning"></i>
                                    <div class="small">
                                        <strong>Withdrawal Not Yet Unlocked</strong><br>
                                        Complete <strong>₦{{ number_format($eligibilityAmount - $totalVolume, 2) }}</strong> more in transactions to enable payouts.
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-success rounded-3 shadow-sm border-0 d-flex align-items-center mb-4">
                                    <i class="bi bi-shield-check-fill fs-15 me-3 text-success"></i>
                                    <div class="small">
                                        <strong>Account Verified</strong><br>
                                        Your account qualifies for instant bank settlement.
                                    </div>
                                </div>
                            @endif

                            {{-- Withdrawal Form --}}
                            <form id="withdrawForm" method="POST" action="{{ route('withdraw.process') }}" class="row g-4">
                                @csrf

                                {{-- Bank Preview (shown after selection) --}}
                                <div class="col-12" id="bankPreviewWrapper" style="display:none;">
                                    <div class="alert alert-info rounded-3 shadow-sm border-0 d-flex align-items-center mb-0" id="bankPreview">
                                        <div class="bg-white rounded-circle shadow-sm me-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width:40px;height:40px;border:1px solid rgba(0,0,0,.08);">
                                            <i class="bi bi-bank text-primary" id="defaultBankIcon"></i>
                                            <img src="" id="selectedBankLogo" class="d-none" style="width:26px;height:26px;object-fit:contain;">
                                        </div>
                                        <div>
                                            <p id="previewBankName" class="fw-bold mb-0 small text-dark">Select a Bank</p>
                                            <small id="previewAccountNo" class="text-muted">Enter details below</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Hidden native select for form submit & JS --}}
                                <select name="bankCode" id="bank_code" class="d-none" required>
                                    <option value="">Choose a bank...</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->bank_code }}"
                                            data-url="{{ $bank->bank_url ?? '' }}"
                                            data-bg="{{ $bank->bg_url ?? '' }}"
                                            data-name="{{ $bank->bank_name }}">
                                            {{ $bank->bank_name }}
                                        </option>
                                    @endforeach
                                </select>

                                {{-- Bank Picker --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">
                                        Select Bank <span class="text-danger">*</span>
                                    </label>

                                    <div class="position-relative" id="bankPickerWrapper">

                                        {{-- Trigger --}}
                                        <div class="input-group shadow-sm" id="bankPickerTrigger" style="cursor:pointer;" role="button" tabindex="0">
                                            <span class="input-group-text bg-white border-0" id="bankTriggerLogoWrap">
                                                <i class="bi bi-bank text-primary" id="bankPickerIcon"></i>
                                                <img src="" id="bankPickerLogo" alt="" class="d-none" style="width:22px;height:22px;object-fit:contain;">
                                            </span>
                                            <div class="form-control bg-light border-0 d-flex align-items-center justify-content-between pe-3"
                                                 style="cursor:pointer;">
                                                <span id="bankPickerLabel" class="text-muted">Choose a bank...</span>
                                                <i class="bi bi-chevron-down text-muted small" id="bankChevron"></i>
                                            </div>
                                        </div>

                                        {{-- Dropdown Panel --}}
                                        <div id="bankPickerDropdown"
                                             class="d-none position-absolute start-0 end-0 shadow-lg border-0 bg-white"
                                             style="z-index:3000; top:calc(100% + 6px); overflow:hidden; border-radius: 20px;">

                                            {{-- Search --}}
                                            <div class="p-2 border-bottom bg-light">
                                                <div class="input-group shadow-sm">
                                                    <span class="input-group-text bg-white border-0">
                                                        <i class="bi bi-search text-muted small"></i>
                                                    </span>
                                                    <input type="text"
                                                           id="bankSearchInput"
                                                           class="form-control bg-white border-0 ps-0"
                                                           placeholder="Search bank name..."
                                                           autocomplete="off">
                                                </div>
                                            </div>

                                            <ul class="list-unstyled mb-0" id="bankPickerList"
                                                style="max-height:260px;overflow-y:auto;">
                                                @foreach($banks as $bank)
                                                    <li class="bank-item d-flex align-items-center gap-3 px-3 py-2 border-bottom"
                                                        style="cursor:pointer;"
                                                        data-code="{{ $bank->bank_code }}"
                                                        data-name="{{ $bank->bank_name }}"
                                                        data-url="{{ $bank->bank_url ?? '' }}"
                                                        data-bg="{{ $bank->bg_url ?? '' }}">
                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                              style="width:36px;height:36px;border:1px solid rgba(0,0,0,.08);">
                                                            @if($bank->bank_url)
                                                                <img src="{{ $bank->bank_url }}" alt="{{ $bank->bank_name }}"
                                                                     style="width:23px;height:23px;object-fit:contain;"
                                                                     onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                                                                <i class="bi bi-bank text-primary" style="display:none;"></i>
                                                            @else
                                                                <i class="bi bi-bank text-primary small"></i>
                                                            @endif
                                                        </div>
                                                        <span class="small fw-semibold text-dark">{{ $bank->bank_name }}</span>
                                                    </li>
                                                @endforeach

                                                <li class="px-3 py-4 text-center text-muted small d-none" id="bankNoResults">
                                                    <i class="bi bi-search me-1"></i> No banks found
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Account Number --}}
                                <div class="col-12">
                                    <label for="account_no" class="form-label fw-semibold text-dark">
                                        Account Number <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-0">
                                            <i class="bi bi-credit-card-2-front text-primary"></i>
                                        </span>
                                        <input type="text"
                                               id="account_no"
                                               name="account_no"
                                               class="form-control bg-light border-0 ps-0"
                                               placeholder="Enter 10-digit account number"
                                               maxlength="10"
                                               inputmode="numeric"
                                               required>
                                    </div>
                                    <div class="mt-2" style="min-height:24px;">
                                        <div id="accountNameDisplay"></div>
                                        <div id="accountErrorDisplay"></div>
                                        <input type="hidden" name="account_name" id="account_name_hidden">
                                    </div>
                                </div>

                                {{-- Amount --}}
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="amount" class="form-label fw-semibold text-dark mb-0">
                                            Withdrawal Amount <span class="text-danger">*</span>
                                        </label>
                                        <small class="text-muted">
                                            Balance: <span class="text-success fw-bold">₦{{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}</span>
                                        </small>
                                    </div>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-0 fw-bold text-muted">₦</span>
                                        <input type="number"
                                               id="amount"
                                               name="amount"
                                               class="form-control bg-light border-0 ps-0"
                                               placeholder="0.00"
                                               min="100"
                                               step="any"
                                               required>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <small class="text-muted">Min: ₦100.00</small>
                                        <small class="text-muted">Limit: ₦{{ number_format($user->limit, 2) }}</small>
                                    </div>
                                </div>

                                {{-- Service Fee Info --}}
                                <div class="col-12">
                                    <div class="alert alert-info d-flex justify-content-between align-items-center mb-0 rounded-3 shadow-sm border-0">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-wallet2 fs-15 me-2 text-info"></i>
                                            <span class="fw-medium small">Instant Bank Settlement</span>
                                        </div>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">Active</span>
                                    </div>
                                </div>

                                {{-- Warning --}}
                                <div class="col-12">
                                    <div class="alert alert-warning py-3 rounded-3 shadow-sm border-0 d-flex align-items-center">
                                        <i class="bi bi-exclamation-circle text-warning fs-15 me-3"></i>
                                        <div class="small">
                                            <strong>Non-Reversible Transaction</strong><br>
                                            Please verify all recipient details carefully before proceeding.
                                        </div>
                                    </div>
                                </div>

                                {{-- Submit Button --}}
                                <div class="col-12 mt-2">
                                    <button type="button"
                                            id="proceedBtn"
                                            class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center gap-2"
                                            @if($totalVolume < $eligibilityAmount) disabled @endif>
                                        <i class="bi bi-lightning-charge-fill"></i>
                                        Authorize Payout
                                    </button>
                                </div>

                                @if(auth()->user()->role === 'super_admin')
                                    <div class="col-12 text-center">
                                        <a href="{{ route('withdraw.syncBanks') }}" class="btn btn-sm btn-light text-muted shadow-sm rounded-pill">
                                            <i class="bi bi-arrow-repeat me-1"></i> Sync Bank Infrastructure
                                        </a>
                                    </div>
                                @endif

                            </form>
                        </div>
                    </div>
                </div>

             <!--header -->
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
                                        <div class="d-flex align-items-center p-3 rounded-3 bg-light border-0 shadow-sm"
                                             style="cursor:pointer; transition:all .15s;"
                                             onclick="selectRecentBank('{{ $recipient['bank_code'] }}', '{{ $recipient['account_no'] }}', '{{ $recipient['account_name'] }}')"
                                             onmouseenter="this.classList.add('shadow')"
                                             onmouseleave="this.classList.remove('shadow')">

                                            <div class="bg-white rounded-circle shadow-sm me-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                                 style="width:42px;height:42px;border:1px solid rgba(0,0,0,.08);">
                                                @if(!empty($recipient['bank_url']))
                                                    <img src="{{ $recipient['bank_url'] }}" alt="{{ $recipient['bank_name'] }}"
                                                         style="width:26px;height:26px;object-fit:contain;"
                                                         onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                                                    <i class="bi bi-bank text-primary" style="display:none;"></i>
                                                @else
                                                    <i class="bi bi-bank text-primary"></i>
                                                @endif
                                            </div>

                                            <div class="flex-grow-1 min-w-0">
                                                <span class="fw-bold d-block small text-truncate">{{ $recipient['account_name'] }}</span>
                                                <small class="text-muted text-truncate d-block">
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
                                    <h6 class="fw-semibold">No Recent Payouts</h6>
                                    <p class="small text-muted mb-0 px-4">
                                        Your trusted recipients will appear here after your first successful withdrawal.
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

            </div>{{-- /row --}}

            {{-- PIN Modal --}}
            @include('pages.pin')

        </div>{{-- /container-fluid --}}
    </div>{{-- /page-body --}}

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        /* ── References ───────────────────────────────── */
        const accountNoInput    = document.getElementById('account_no');
        const bankCodeSelect    = document.getElementById('bank_code');
        const accountNameDisp   = document.getElementById('accountNameDisplay');
        const accountErrorDisp  = document.getElementById('accountErrorDisplay');
        const accountNameHidden = document.getElementById('account_name_hidden');
        const proceedBtn        = document.getElementById('proceedBtn');
        const amountInput       = document.getElementById('amount');

        // Bank preview
        const bankPreviewWrapper = document.getElementById('bankPreviewWrapper');
        const previewBankName    = document.getElementById('previewBankName');
        const previewAcctNo      = document.getElementById('previewAccountNo');
        const selBankLogo        = document.getElementById('selectedBankLogo');
        const defBankIcon        = document.getElementById('defaultBankIcon');

        // Modal
        const confirmationStep = document.getElementById('confirmationStep');
        const pinStep          = document.getElementById('pinStep');
        const btnGoToPin       = document.getElementById('btnGoToPin');
        const btnBackToConfirm = document.getElementById('btnBackToConfirm');
        const modalTitle       = document.getElementById('modalTitle');
        const modalSubtitle    = document.getElementById('modalSubtitle');

        let pinModal;
        try { pinModal = new bootstrap.Modal(document.getElementById('pinModal')); }
        catch (e) { console.error('Modal init failed:', e); }

        let verificationTimeout;

        /* ── Bank Preview ─────────────────────────────── */
        function updateBankPreview() {
            const opt    = bankCodeSelect.options[bankCodeSelect.selectedIndex];
            const name   = opt ? opt.text.trim() : '';
            const url    = opt ? opt.getAttribute('data-url') : null;
            const bgUrl  = opt ? opt.getAttribute('data-bg')  : null;
            const acctNo = accountNoInput.value;

            if (bankCodeSelect.value) {
                bankPreviewWrapper.style.display = 'block';
                previewBankName.textContent = name;
                previewAcctNo.textContent   = acctNo || 'Enter account number';

                if (url) {
                    selBankLogo.src = url;
                    selBankLogo.classList.remove('d-none');
                    defBankIcon.classList.add('d-none');
                } else {
                    selBankLogo.classList.add('d-none');
                    defBankIcon.classList.remove('d-none');
                }
            } else {
                bankPreviewWrapper.style.display = 'none';
            }
        }

        /* ── Account Verification ─────────────────────── */
        function performVerification() {
            const bankCode = bankCodeSelect.value;
            const acctNo   = accountNoInput.value;
            updateBankPreview();

            if (bankCode && acctNo.length === 10) {
                accountNameDisp.innerHTML  = '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1"><span class="spinner-border spinner-border-sm me-1" style="width:10px;height:10px;"></span> Verifying...</span>';
                accountErrorDisp.innerHTML = '';

                fetch("{{ route('withdraw.verifyAccount') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ bankCode: bankCode, account_no: acctNo })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        accountNameDisp.innerHTML  = `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i>${data.account_name}</span>`;
                        accountNameHidden.value    = data.account_name;
                        accountErrorDisp.innerHTML = '';
                    } else {
                        accountNameDisp.innerHTML  = '';
                        accountErrorDisp.innerHTML = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1"><i class="bi bi-x-circle-fill me-1"></i>${data.message}</span>`;
                        accountNameHidden.value    = '';
                    }
                })
                .catch(() => {
                    accountNameDisp.innerHTML  = '';
                    accountErrorDisp.innerHTML = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1"><i class="bi bi-wifi-off me-1"></i>Connection failed</span>';
                });
            }
        }

        accountNoInput.addEventListener('input', () => {
            clearTimeout(verificationTimeout);
            updateBankPreview();
            if (accountNoInput.value.length === 10) {
                verificationTimeout = setTimeout(performVerification, 500);
            } else {
                accountNameDisp.innerHTML  = '';
                accountErrorDisp.innerHTML = '';
                accountNameHidden.value    = '';
            }
        });

        bankCodeSelect.addEventListener('change', performVerification);

        /* ══ Custom Bank Picker ═══════════════════════════════════════ */
        const bankPickerTrigger  = document.getElementById('bankPickerTrigger');
        const bankPickerDropdown = document.getElementById('bankPickerDropdown');
        const bankSearchInput    = document.getElementById('bankSearchInput');
        const bankPickerLabel    = document.getElementById('bankPickerLabel');
        const bankPickerLogo     = document.getElementById('bankPickerLogo');
        const bankPickerIcon     = document.getElementById('bankPickerIcon');
        const bankChevron        = document.getElementById('bankChevron');
        const bankItems          = document.querySelectorAll('.bank-item');
        const bankNoResults      = document.getElementById('bankNoResults');

        function openBankPicker() {
            bankPickerDropdown.classList.remove('d-none');
            bankChevron.style.transform = 'rotate(180deg)';
            bankSearchInput.value = '';
            bankItems.forEach(el => el.classList.remove('d-none'));
            if (bankNoResults) bankNoResults.classList.add('d-none');
            setTimeout(() => bankSearchInput.focus(), 60);
        }
        function closeBankPicker() {
            bankPickerDropdown.classList.add('d-none');
            bankChevron.style.transform = '';
        }
        function applyBankSelection(code, name, url) {
            bankCodeSelect.value = code;
            bankCodeSelect.dispatchEvent(new Event('change'));

            bankPickerLabel.textContent = name;
            bankPickerLabel.classList.remove('text-muted');
            bankPickerLabel.classList.add('text-dark', 'fw-semibold');

            bankItems.forEach(el => {
                el.style.background = el.getAttribute('data-code') === code ? '#f0f7ff' : '';
            });

            if (url) {
                bankPickerLogo.src = url;
                bankPickerLogo.classList.remove('d-none');
                bankPickerIcon.classList.add('d-none');
            } else {
                bankPickerLogo.classList.add('d-none');
                bankPickerIcon.classList.remove('d-none');
            }
            closeBankPicker();
        }

        bankPickerTrigger.addEventListener('click', e => {
            e.stopPropagation();
            bankPickerDropdown.classList.contains('d-none') ? openBankPicker() : closeBankPicker();
        });

        bankSearchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            let visible = 0;
            bankItems.forEach(item => {
                const match = item.getAttribute('data-name').toLowerCase().includes(q);
                item.classList.toggle('d-none', !match);
                if (match) visible++;
            });
            bankNoResults.classList.toggle('d-none', visible > 0);
        });

        bankItems.forEach(item => {
            item.addEventListener('click', () => applyBankSelection(
                item.getAttribute('data-code'),
                item.getAttribute('data-name'),
                item.getAttribute('data-url')
            ));
            item.addEventListener('mouseenter', () => {
                if (item.getAttribute('data-code') !== bankCodeSelect.value) {
                    item.style.background = '#f8f9fa';
                }
            });
            item.addEventListener('mouseleave', () => {
                if (item.getAttribute('data-code') !== bankCodeSelect.value) {
                    item.style.background = '';
                }
            });
        });

        document.addEventListener('click', e => {
            if (!document.getElementById('bankPickerWrapper')?.contains(e.target)) {
                closeBankPicker();
            }
        });
        /* ══ End Bank Picker ══════════════════════════════════════════ */

        /* ── Quick Select Recent Recipient ────────────── */
        window.selectRecentBank = function (bankCode, accountNo, accountName) {
            const opt = bankCodeSelect.querySelector(`option[value="${bankCode}"]`);
            if (opt) {
                applyBankSelection(bankCode, opt.getAttribute('data-name') || opt.text.trim(), opt.getAttribute('data-url') || '');
            }
            accountNoInput.value = accountNo;
            accountNoInput.classList.add('is-valid');
            setTimeout(() => accountNoInput.classList.remove('is-valid'), 2000);

            accountNameDisp.innerHTML  = `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i>${accountName}</span>`;
            accountNameHidden.value    = accountName;
            accountErrorDisp.innerHTML = '';
            updateBankPreview();

            document.getElementById('amount').focus();
            document.getElementById('withdrawForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
        };

        /* ── Proceed Button → Open Modal ──────────────── */
        proceedBtn.addEventListener('click', function () {
            const amount      = amountInput.value;
            const bankName    = bankCodeSelect.options[bankCodeSelect.selectedIndex]?.text || '';
            const accountNo   = accountNoInput.value;
            const accountName = accountNameHidden.value;

            if (!amount || parseFloat(amount) < 100) {
                alert('Please enter a valid amount (Min ₦100)');
                return;
            }
            if (!accountName) {
                alert('Please wait for account name verification.');
                return;
            }

            document.getElementById('confirmAccountName').textContent = accountName;
            document.getElementById('confirmBankName').textContent    = bankName;
            document.getElementById('confirmAccountNo').textContent   = accountNo;
            document.getElementById('confirmAmount').textContent      = '₦' + parseFloat(amount).toLocaleString(undefined, { minimumFractionDigits: 2 });

            confirmationStep.classList.remove('d-none');
            pinStep.classList.add('d-none');
            modalTitle.textContent    = 'Confirm Transaction';
            modalSubtitle.textContent = 'Please review details carefully';

            (pinModal || new bootstrap.Modal(document.getElementById('pinModal'))).show();
        });

        /* ── Modal Step Navigation ────────────────────── */
        btnGoToPin.addEventListener('click', () => {
            confirmationStep.classList.add('d-none');
            pinStep.classList.remove('d-none');
            modalTitle.textContent    = 'Authorize Payout';
            modalSubtitle.textContent = 'Step 2 of 2 — Security PIN';
            document.getElementById('pinInput').focus();
        });

        btnBackToConfirm?.addEventListener('click', () => {
            pinStep.classList.add('d-none');
            confirmationStep.classList.remove('d-none');
            modalTitle.textContent    = 'Confirm Transaction';
            modalSubtitle.textContent = 'Please review details carefully';
        });

        /* ── PIN Submit ───────────────────────────────── */
        document.getElementById('confirmPinBtn').addEventListener('click', function () {
            const confirmBtn   = this;
            const loader       = document.getElementById('pinLoader');
            const confirmText  = document.getElementById('confirmPinText');
            const pinError     = document.getElementById('pinError');
            const pinErrorText = document.getElementById('pinErrorText');
            const pin          = document.getElementById('pinInput').value.trim();

            function setPinError(msg) {
                if (pinErrorText) pinErrorText.textContent = msg;
                pinError?.classList.remove('d-none');
            }

            if (!pin || pin.length !== 5) { setPinError('Please enter your 5-digit PIN.'); return; }

            confirmBtn.disabled = true;
            loader.classList.remove('d-none');
            confirmText.textContent = 'Verifying...';
            pinError?.classList.add('d-none');


            fetch("{{ route('verify.pin') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ pin })
            })
            .then(r => r.json())
            .then(data => {
                if (data.valid) {
                    const form = document.getElementById('withdrawForm');
                    const h = document.createElement('input');
                    h.type = 'hidden'; h.name = 'pin'; h.value = pin;
                    form.appendChild(h);
                    form.submit();
                } else {
                    setPinError('Incorrect PIN. Please try again.');
                    confirmBtn.disabled = false;
                    loader.classList.add('d-none');
                    confirmText.textContent = 'Authorize Now';
                    document.getElementById('pinInput').value = '';
                }
            })
            .catch(() => {
                setPinError('Connection error. Please try again.');
                confirmBtn.disabled = false;
                loader.classList.add('d-none');
                confirmText.textContent = 'Authorize Now';
            });
        });

    });
    </script>

</x-app-layout>
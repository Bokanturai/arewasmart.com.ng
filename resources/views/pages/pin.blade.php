{{--
 PIN Confirmation Modal — pages/pin.blade.php
 Reusable: @include('pages.pin') in any payment blade.

 The calling page must populate before calling pinModal.show():
   #confirmAccountName, #confirmBankName, #confirmAccountNo, #confirmAmount
 And wire: #confirmPinBtn click → verify pin → submit form.
--}}

{{-- ══ MODAL ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="pinModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered mx-auto" style="max-width: 400px; width: calc(100% - 1.5rem);">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">

            {{-- ── Header ───────────────────────────────────────────────── --}}
            <div class="modal-header bg-primary text-white border-0 px-4 py-3">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="modalTitle">Confirm Transaction</h5>
                    <small class="text-white-50" id="modalSubtitle">Please review details carefully</small>
                </div>
                <button type="button"
                        class="btn btn-sm btn-light text-primary shadow-sm rounded-circle d-flex align-items-center justify-content-center ms-auto"
                        style="width:32px;height:32px;"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    <i class="bi bi-x-lg small"></i>
                </button>
            </div>

            {{-- ── Body ─────────────────────────────────────────────────── --}}
            <div class="modal-body px-4 py-4">

                {{-- STEP 1 — Transaction Summary --}}
                <div id="confirmationStep">

                    <div class="text-center mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width:60px;height:60px;">
                            <i class="bi bi-send-check-fill text-primary fs-15"></i>
                        </div>
                        <p class="text-muted small mb-0">You are about to send funds to:</p>
                    </div>

                    {{-- Summary rows --}}
                    <div class="rounded-3 overflow-hidden border mb-3">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-light">
                            <span class="small text-muted fw-semibold">Account Name</span>
                            <span id="confirmAccountName" class="small fw-bold text-dark text-end" style="max-width:60%;">—</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-white">
                            <span class="small text-muted fw-semibold">Bank</span>
                            <span id="confirmBankName" class="small fw-bold text-dark text-end" style="max-width:60%;">—</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-light">
                            <span class="small text-muted fw-semibold">Account No.</span>
                            <span id="confirmAccountNo" class="small fw-bold text-dark" style="font-family:monospace;letter-spacing:.5px;">—</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 bg-white">
                            <span class="small text-muted fw-semibold">Amount</span>
                            <span id="confirmAmount" class="fw-bold text-primary fs-12">—</span>
                        </div>
                    </div>

                    {{-- Warning --}}
                    <div class="alert alert-warning py-2 rounded-3 border-0 shadow-sm d-flex align-items-start gap-2 small mb-0">
                        <i class="bi bi-exclamation-triangle-fill text-warning mt-1 flex-shrink-0"></i>
                        <span>Please verify all details carefully. Transactions <strong>cannot be reversed</strong> once authorized.</span>
                    </div>

                </div>

                {{-- STEP 2 — PIN Entry --}}
                <div id="pinStep" class="d-none">

                    <style>
                        .pin-keypad {
                            max-width: 260px;
                            margin: 20px auto 10px auto;
                        }
                        .keypad-btn {
                            width: 60px;
                            height: 60px;
                            font-size: 1.4rem;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            border-radius: 50% !important;
                            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
                            border: 1px solid rgba(0, 0, 0, 0.05);
                            background-color: #f8f9fa;
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04) !important;
                            margin: 0 auto;
                            user-select: none;
                        }
                        .keypad-btn:hover {
                            background-color: #e9ecef;
                            border-color: rgba(0, 0, 0, 0.08);
                            transform: scale(1.08);
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08) !important;
                        }
                        .keypad-btn:active {
                            background-color: #dee2e6;
                            transform: scale(0.92);
                            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06) !important;
                        }
                        .keypad-btn.text-danger:hover {
                            background-color: rgba(220, 53, 69, 0.1);
                            color: #dc3545 !important;
                        }
                        .keypad-btn.text-warning:hover {
                            background-color: rgba(255, 193, 7, 0.1);
                            color: #ffc107 !important;
                        }
                    </style>

                    <div class="text-center mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width:60px;height:60px;">
                            <i class="bi bi-lock-fill text-success fs-15"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Enter Transaction PIN</h6>
                        <p class="text-muted small mb-0">Enter your 5-digit security PIN to authorize this payout.</p>
                    </div>

                    {{-- PIN Input --}}
                    <div class="mb-3">
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-0">
                                <i class="bi bi-key-fill text-primary"></i>
                            </span>
                            <input type="password"
                                   id="pinInput"
                                   class="form-control bg-light border-0 ps-0 text-center fw-bold fs-15"
                                   placeholder="• • • • •"
                                   maxlength="5"
                                   inputmode="numeric"
                                   pattern="\d{5}"
                                   autocomplete="new-password"
                                   style="letter-spacing:8px;">
                            <button type="button"
                                    class="btn btn-light border-0 shadow-sm px-3"
                                    id="togglePinVisibility"
                                    aria-label="Toggle PIN visibility">
                                <i class="bi bi-eye" id="togglePinIcon"></i>
                            </button>
                        </div>

                        {{-- Error --}}
                        <div id="pinError" class="d-none mt-2">
                            <div class="alert alert-danger rounded-3 border-0 shadow-sm py-2 d-flex align-items-center gap-2 small mb-0">
                                <i class="bi bi-x-circle-fill flex-shrink-0"></i>
                                <span id="pinErrorText"></span>
                            </div>
                        </div>
                    </div>

                    {{-- On-Screen Keypad --}}
                    <div class="pin-keypad mt-3 mb-4">
                        <div class="d-grid gap-3" style="grid-template-columns: repeat(3, 1fr);">
                            @foreach([1,2,3,4,5,6,7,8,9] as $num)
                                <button type="button" class="btn btn-light keypad-btn fw-bold text-dark rounded-circle shadow-sm" data-val="{{ $num }}">
                                    {{ $num }}
                                </button>
                            @endforeach
                            <button type="button" class="btn btn-light text-danger keypad-btn fw-bold rounded-circle shadow-sm" data-action="clear" aria-label="Clear PIN">
                                C
                            </button>
                            <button type="button" class="btn btn-light keypad-btn fw-bold text-dark rounded-circle shadow-sm" data-val="0">
                                0
                            </button>
                            <button type="button" class="btn btn-light text-warning keypad-btn fw-bold rounded-circle shadow-sm" data-action="delete" aria-label="Delete last digit">
                                <i class="bi bi-backspace-fill"></i>
                            </button>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('profile.edit') }}#security" class="small text-muted">
                            <i class="bi bi-question-circle me-1"></i>Forgot your PIN? Reset it here
                        </a>
                    </div>

                </div>
            </div>{{-- /modal-body --}}

            {{-- ── Footer ───────────────────────────────────────────────── --}}

            {{-- Step 1 Footer --}}
            <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2 flex-nowrap" id="confirmationStep_footer">
                <button type="button" class="btn btn-light fw-semibold shadow-sm rounded-pill flex-fill" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary fw-bold shadow-sm rounded-pill flex-fill" id="btnGoToPin">
                    <i class="bi bi-arrow-right-circle-fill me-1"></i> Continue
                </button>
            </div>

            {{-- Step 2 Footer --}}
            <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2 flex-nowrap d-none" id="pinStep_footer">
                <button type="button" class="btn btn-light fw-semibold shadow-sm rounded-pill flex-fill" id="btnBackToConfirm">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-success fw-bold shadow-sm rounded-pill flex-fill" id="confirmPinBtn">
                    <span id="pinLoader" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                    <i class="bi bi-shield-lock-fill me-1" id="pinBtnIcon"></i>
                    <span id="confirmPinText">Authorize Now</span>
                </button>
            </div>

        </div>{{-- /modal-content --}}
    </div>{{-- /modal-dialog --}}
</div>{{-- /modal --}}

{{-- ══ MODAL INTERNAL SCRIPT (self-contained) ══════════════════════════════ --}}
<script>
(function () {
    function initPinModal() {
        const modalEl = document.getElementById('pinModal');
        if (!modalEl || modalEl.dataset.initialized === 'true') {
            return;
        }
        modalEl.dataset.initialized = 'true';

        const pinInput       = document.getElementById('pinInput');
        const toggleBtn      = document.getElementById('togglePinVisibility');
        const toggleIcon     = document.getElementById('togglePinIcon');
        const btnGoToPin     = document.getElementById('btnGoToPin');
        const btnBack        = document.getElementById('btnBackToConfirm');
        const confirmFoot    = document.getElementById('confirmationStep_footer');
        const pinFoot        = document.getElementById('pinStep_footer');
        const confirmStep    = document.getElementById('confirmationStep');
        const pinStep        = document.getElementById('pinStep');

        // Detect if mobile/touch device
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (isMobile && pinInput) {
            pinInput.setAttribute('readonly', 'true');
            pinInput.style.cursor = 'default';
        }

        /* ── Toggle PIN visibility ──────────────────── */
        if (toggleBtn && pinInput) {
            toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const isPassword = pinInput.type === 'password';
                pinInput.type    = isPassword ? 'text' : 'password';
                toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        }

        /* ── Step forward ───────────────────────────── */
        if (btnGoToPin) {
            btnGoToPin.addEventListener('click', () => {
                confirmStep.classList.add('d-none');
                pinStep.classList.remove('d-none');
                confirmFoot.classList.add('d-none');
                pinFoot.classList.remove('d-none');
                
                const mt = document.getElementById('modalTitle');
                const ms = document.getElementById('modalSubtitle');
                if (mt) mt.textContent = 'Authorize Transaction';
                if (ms) ms.textContent = 'Step 2 of 2 — Security PIN';
                
                // Focus PIN input for desktop users
                if (!isMobile) {
                    setTimeout(() => pinInput?.focus(), 80);
                }
            });
        }

        /* ── Step back ──────────────────────────────── */
        if (btnBack) {
            btnBack.addEventListener('click', () => {
                pinStep.classList.add('d-none');
                confirmStep.classList.remove('d-none');
                pinFoot.classList.add('d-none');
                confirmFoot.classList.remove('d-none');
                
                const mt = document.getElementById('modalTitle');
                const ms = document.getElementById('modalSubtitle');
                if (mt) mt.textContent = 'Confirm Transaction';
                if (ms) ms.textContent = 'Please review details carefully';
            });
        }

        /* ── Reset on modal close ───────────────────── */
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', () => {
                // Reset steps
                document.getElementById('confirmationStep')?.classList.remove('d-none');
                document.getElementById('pinStep')?.classList.add('d-none');
                confirmFoot.classList.remove('d-none');
                pinFoot.classList.add('d-none');

                const mt = document.getElementById('modalTitle');
                const ms = document.getElementById('modalSubtitle');
                if (mt) mt.textContent = 'Confirm Transaction';
                if (ms) ms.textContent = 'Please review details carefully';

                // Clear PIN & errors
                if (pinInput) pinInput.value = '';
                const errEl = document.getElementById('pinError');
                if (errEl) errEl.classList.add('d-none');
                const errText = document.getElementById('pinErrorText');
                if (errText) errText.textContent = '';

                // Reset authorize btn
                const btn  = document.getElementById('confirmPinBtn');
                const text = document.getElementById('confirmPinText');
                const ldr  = document.getElementById('pinLoader');
                const ico  = document.getElementById('pinBtnIcon');
                if (btn)  btn.disabled = false;
                if (text) text.textContent = 'Authorize Now';
                if (ldr)  ldr.classList.add('d-none');
                if (ico)  ico.classList.remove('d-none');
            });
        }

        /* ── Keypad Button Taps ─────────────────────── */
        modalEl.querySelectorAll('.keypad-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                // Micro Haptic feedback for touch devices
                if ('vibrate' in navigator) {
                    try { navigator.vibrate(15); } catch(e) {}
                }

                const val = btn.getAttribute('data-val');
                const action = btn.getAttribute('data-action');

                if (val !== null) {
                    if (pinInput.value.length < 5) {
                        pinInput.value += val;
                        pinInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                } else if (action === 'clear') {
                    pinInput.value = '';
                    pinInput.dispatchEvent(new Event('input', { bubbles: true }));
                } else if (action === 'delete') {
                    pinInput.value = pinInput.value.slice(0, -1);
                    pinInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        });

        /* ── Keyboard & Input filters ───────────────── */
        if (pinInput) {
            // Filter non-digit characters (for paste or direct typing)
            pinInput.addEventListener('input', () => {
                pinInput.value = pinInput.value.replace(/\D/g, '');
            });

            // Enter key submits PIN
            pinInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('confirmPinBtn')?.click();
                }
            });
        }

        // Global keydown handler when PIN Step is visible
        if (!window.pinKeydownListenerAdded) {
            window.pinKeydownListenerAdded = true;
            document.addEventListener('keydown', function (e) {
                // Check if pinStep is active
                const activePinStep = document.getElementById('pinStep');
                if (!activePinStep || activePinStep.classList.contains('d-none')) return;

                const activePinInput = document.getElementById('pinInput');
                if (!activePinInput) return;

                // Don't intercept if focus is in another input/textarea
                if (e.target.tagName === 'INPUT' && e.target !== activePinInput) return;
                if (e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') return;

                // If pinInput is focused and editable on desktop, let standard keyboard event pass
                if (document.activeElement === activePinInput && !activePinInput.hasAttribute('readonly')) {
                    return;
                }

                // Otherwise, process manually for high-speed typing
                if (e.key >= '0' && e.key <= '9') {
                    if (activePinInput.value.length < 5) {
                        activePinInput.value += e.key;
                        activePinInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    e.preventDefault();
                } else if (e.key === 'Backspace') {
                    activePinInput.value = activePinInput.value.slice(0, -1);
                    activePinInput.dispatchEvent(new Event('input', { bubbles: true }));
                    e.preventDefault();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('confirmPinBtn')?.click();
                }
            });
        }

        /* ── Auto-Lock on Tab Switch / Device Lock ── */
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                const activeModal = document.getElementById('pinModal');
                if (activeModal && activeModal.classList.contains('show')) {
                    try {
                        const bsModal = bootstrap.Modal.getInstance(activeModal) || new bootstrap.Modal(activeModal);
                        bsModal.hide();
                    } catch (err) {
                        console.warn('Failed auto-closing modal:', err);
                    }
                }
            }
        });

        /* ── Global Centralized Interceptor for 429 Rate Limiting ── */
        // Intercept native fetch requests
        if (!window.pinFetchInterceptorAdded) {
            window.pinFetchInterceptorAdded = true;
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch(...args).then(response => {
                    try {
                        const url = typeof args[0] === 'string' ? args[0] : (args[0] instanceof URL ? args[0].href : '');
                        if (response.status === 429 && url.indexOf('verify-pin') !== -1) {
                            showGlobalRateLimitError();
                        }
                    } catch (err) {
                        console.error('Fetch intercept error:', err);
                    }
                    return response;
                });
            };
        }

        // Intercept jQuery AJAX requests safely
        const bindJQueryInterceptor = () => {
            if (window.jQuery && !window.pinJQueryInterceptorAdded) {
                window.pinJQueryInterceptorAdded = true;
                window.jQuery(document).ajaxError(function(event, jqXHR, ajaxSettings) {
                    if (ajaxSettings.url && ajaxSettings.url.indexOf('verify-pin') !== -1 && jqXHR.status === 429) {
                        showGlobalRateLimitError();
                    }
                });
            }
        };
        bindJQueryInterceptor();
        window.addEventListener('load', bindJQueryInterceptor);

        function showGlobalRateLimitError() {
            const errEl = document.getElementById('pinError');
            if (errEl) errEl.classList.remove('d-none');
            const errText = document.getElementById('pinErrorText');
            if (errText) errText.textContent = 'Too many failed attempts. Please try again in 1 minute.';

            // Reset the authorize button state
            const btn  = document.getElementById('confirmPinBtn');
            const text = document.getElementById('confirmPinText');
            const ldr  = document.getElementById('pinLoader');
            const ico  = document.getElementById('pinBtnIcon');
            if (btn)  btn.disabled = false;
            if (text) text.textContent = 'Authorize Now';
            if (ldr)  ldr.classList.add('d-none');
            if (ico)  ico.classList.remove('d-none');
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPinModal);
    } else {
        initPinModal();
    }
})();
</script>

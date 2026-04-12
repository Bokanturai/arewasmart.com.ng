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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

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
                                   autocomplete="off"
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
    document.addEventListener('DOMContentLoaded', function () {

        const pinInput       = document.getElementById('pinInput');
        const toggleBtn      = document.getElementById('togglePinVisibility');
        const toggleIcon     = document.getElementById('togglePinIcon');
        const btnGoToPin     = document.getElementById('btnGoToPin');
        const btnBack        = document.getElementById('btnBackToConfirm');
        const confirmFoot    = document.getElementById('confirmationStep_footer');
        const pinFoot        = document.getElementById('pinStep_footer');
        const confirmStep    = document.getElementById('confirmationStep');
        const pinStep        = document.getElementById('pinStep');

        /* ── Toggle PIN visibility ──────────────────── */
        if (toggleBtn && pinInput) {
            toggleBtn.addEventListener('click', () => {
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
                setTimeout(() => pinInput?.focus(), 80);
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
        const modalEl = document.getElementById('pinModal');
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

    });
})();
</script>

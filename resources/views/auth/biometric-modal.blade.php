@if(Auth::check() && Auth::user()->webAuthnCredentials()->count() === 0)
    <!-- Biometric Login Modal -->
    <div class="modal fade" id="biometricModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-20px overflow-hidden">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="ti ti-fingerprint fs-22 me-2"></i>
                        <span>Secure Your Account</span>
                    </h5>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-4">
                        <div class="bg-light-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px;">
                            <i class="ti ti-shield-lock-filled text-primary fs-40 animate-pulse"></i>
                        </div>
                    </div>

                    <h4 class="fw-bold mb-2">Enable Biometric Login</h4>
                    <p class="text-muted mb-4 px-3">
                        Experience faster and more secure access using your Fingerprint, Face ID, or Windows Hello. No more
                        typing passwords!
                    </p>

                    <div class="d-grid gap-2">
                        <button type="button" id="enable-biometrics"
                            class="btn btn-primary py-3 rounded-pill fw-bold shadow-sm">
                            <i class="ti ti-fingerprint me-2"></i> Enable Now
                        </button>
                        <button type="button" id="maybe-later" class="btn btn-link text-muted" data-bs-dismiss="modal">
                            Maybe Later
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #F26522 0%, #ff8c52 100%);
        }

        .bg-light-primary {
            background-color: rgba(242, 101, 34, 0.1);
        }

        .animate-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        #biometricModal .modal-content {
            transition: all 0.3s ease-in-out;
        }
    </style>

    @push('scripts')
        @include('auth.passkey-script')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Show modal if not previously dismissed in this session
                if (!sessionStorage.getItem('biometric_prompt_dismissed')) {
                    const modalEl = document.getElementById('biometricModal');
                    if (modalEl) {
                        const myModal = new bootstrap.Modal(modalEl);
                        myModal.show();

                        modalEl.addEventListener('hidden.bs.modal', function () {
                            sessionStorage.setItem('biometric_prompt_dismissed', 'true');
                        });
                    }
                }

                // Dismiss handler for "Maybe Later"
                const maybeLater = document.getElementById('maybe-later');
                if (maybeLater) {
                    maybeLater.addEventListener('click', () => {
                        sessionStorage.setItem('biometric_prompt_dismissed', 'true');
                    });
                }
            });
        </script>
    @endpush
@endif
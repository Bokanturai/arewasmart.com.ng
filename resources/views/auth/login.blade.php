<x-guest-layout>
    <title>Arewa Smart - {{ $title ?? 'Login' }}</title>
    
    <div class="auth-card">
        <div class="auth-logo">
            <a href="/">
                <img src="{{ asset('assets/img/logo/new-logo.png') }}" alt="Arewa Smart Logo">
            </a>
        </div>

        <div class="text-center mb-4">
            <h2 class="fw-bold mb-1">Welcome Back</h2>
            <p class="text-muted small">Please sign in to your account</p>
        </div>

        <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email Field --}}
            <div class="mb-3">
                <label class="form-label fw-semibold" for="email">Email Address</label>
                <div class="input-group">
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        placeholder="Enter your email"
                        autocomplete="username webauthn"
                        class="form-control border-end-0 @error('email') is-invalid @enderror">
                    <span class="input-group-text border-start-0">
                        <i class="ti ti-mail"></i>
                    </span>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Password Field --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label fw-semibold mb-0" for="password">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-primary small fw-bold">Forgot Password?</a>
                    @endif
                </div>
                <div class="pass-group position-relative">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="••••••••"
                        autocomplete="current-password webauthn"
                        class="form-control @error('password') is-invalid @enderror">
                    <span class="ti toggle-password ti-eye-off position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer text-muted fs-18"></span>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Remember Me --}}
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                    <label class="form-check-label text-muted small" for="remember_me">Remember my device</label>
                </div>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn btn-primary w-100 mb-3 py-2">Sign In</button>

            {{-- Biometric Login Button --}}
            <button type="button" id="biometric-login-btn" class="btn btn-outline-primary w-100 mb-4 py-2 d-flex align-items-center justify-content-center">
                <i class="ti ti-fingerprint me-2 fs-20"></i> Sign in with Biometrics
            </button>

            {{-- Register Link --}}
            <div class="text-center">
                <p class="text-muted small mb-0">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-primary fw-bold">Create Account</a>
                </p>
            </div>
        </form>
    </div>

    {{-- Footer Text --}}
    <p class="auth-footer-text">&copy; {{ date('Y') }} Arewa Smart. All rights reserved.</p>

    <!-- SweetAlert2 & Biometric Script -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('auth.passkey-script')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bioLoginBtn = document.getElementById('biometric-login-btn');
            if (bioLoginBtn) {
                bioLoginBtn.addEventListener('click', async function() {
                    const emailInput = document.getElementById('email');
                    const email = emailInput ? emailInput.value : null;

                    // Disable button and show loading
                    const originalContent = bioLoginBtn.innerHTML;
                    bioLoginBtn.disabled = true;
                    bioLoginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Initializing...';

                    try {
                        await webAuthnLogin(email);
                    } catch (error) {
                        // webAuthnLogin already handles logging the error, 
                        // but we need to reset the button state here.
                        bioLoginBtn.disabled = false;
                        bioLoginBtn.innerHTML = originalContent;
                    }
                });
            }
            // Auto-trigger biometrics if enabled previously
            if (localStorage.getItem('arewa_smart_biometrics_enabled') === 'true') {
                setTimeout(async () => {
                    // Only auto-trigger if email is empty (usernameless flow)
                    // or if email is pre-filled (remember me)
                    const emailInput = document.getElementById('email');
                    const email = emailInput ? emailInput.value : null;

                    try {
                        // Disable button and show loading while auto-prompt is active
                        const originalContent = bioLoginBtn.innerHTML;
                        bioLoginBtn.disabled = true;
                        bioLoginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Auto-signing in...';
                        
                        await webAuthnLogin(email);
                    } catch (error) {
                        // If auto-login fails (e.g. user canceled), just reset UI
                        bioLoginBtn.disabled = false;
                        bioLoginBtn.innerHTML = '<i class="ti ti-fingerprint me-2 fs-20"></i> Sign in with Biometrics';
                        console.log('Auto-biometrics cancelled or blocked.');
                    }
                }, 1000); // Small delay to ensure browser readiness
            }
        });
    </script>
</x-guest-layout>

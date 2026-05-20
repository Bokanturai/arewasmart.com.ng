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
                        autocomplete="username"
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
                        autocomplete="current-password"
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

            {{-- Social Login Divider --}}
            <div class="d-flex align-items-center my-4">
                <hr class="flex-grow-1 border-light-subtle m-0">
                <span class="mx-3 text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px; white-space: nowrap;">Or continue with</span>
                <hr class="flex-grow-1 border-light-subtle m-0">
            </div>

            {{-- Social Login Buttons --}}
            <div class="d-flex gap-3 mb-4">
                <a href="#" class="btn btn-outline-light w-50 d-flex align-items-center justify-content-center border py-2 text-dark font-medium shadow-sm hover-elevated" style="background-color: #f8f9fa; border-color: #e2e8f0 !important; border-radius: 8px;">
                    <i class="fab fa-google text-danger me-2 fs-18"></i>
                    <span class="small fw-bold text-secondary">Google</span>
                </a>
                <a href="#" class="btn btn-outline-light w-50 d-flex align-items-center justify-content-center border py-2 text-dark font-medium shadow-sm hover-elevated" style="background-color: #f8f9fa; border-color: #e2e8f0 !important; border-radius: 8px;">
                    <i class="fab fa-facebook-f text-primary me-2 fs-18"></i>
                    <span class="small fw-bold text-secondary">Facebook</span>
                </a>
            </div>


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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passGroups = document.querySelectorAll('.pass-group');
            passGroups.forEach(group => {
                const input = group.querySelector('input');
                const toggle = group.querySelector('.toggle-password');
                if (toggle) {
                    toggle.addEventListener('click', function() {
                        if (input.type === 'password') {
                            input.type = 'text';
                            toggle.classList.remove('ti-eye-off');
                            toggle.classList.add('ti-eye');
                        } else {
                            input.type = 'password';
                            toggle.classList.remove('ti-eye');
                            toggle.classList.add('ti-eye-off');
                        }
                    });
                }
            });
        });
    </script>
</x-guest-layout>

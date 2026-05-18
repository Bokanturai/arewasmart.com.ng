<x-guest-layout>
    <title>Arewa Smart - {{ $title ?? 'Register' }}</title>
    
    <div class="auth-card">
        <div class="auth-logo">
            <a href="/">
                <img src="{{ asset('assets/img/logo/new-logo.png') }}" alt="Arewa Smart Logo">
            </a>
        </div>

        <div class="text-center mb-4">
            <h2 class="fw-bold mb-1">Create Account</h2>
            <p class="text-muted small">Join Arewa Smart today and start your journey</p>
        </div>

        <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register') }}">
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
                        placeholder="name@example.com"
                        class="form-control border-end-0 @error('email') is-invalid @enderror">
                    <span class="input-group-text border-start-0">
                        <i class="ti ti-mail"></i>
                    </span>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <small id="emailError" class="text-danger d-none mt-1">Please enter a valid email address.</small>
            </div>

            {{-- Referral Code Field (Optional) --}}
            <div class="mb-3">
                <label class="form-label fw-semibold" for="referral_code">Referral Code (Optional)</label>
                <div class="input-group">
                    <input 
                        type="text" 
                        id="referral_code" 
                        name="referral_code" 
                        value="{{ old('referral_code', request()->query('ref')) }}" 
                        placeholder="Enter if any"
                        class="form-control border-end-0 @error('referral_code') is-invalid @enderror">
                    <span class="input-group-text border-start-0">
                        <i class="ti ti-user-plus"></i>
                    </span>
                    @error('referral_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Password Field --}}
            <div class="mb-3">
                <label class="form-label fw-semibold" for="password">Password</label>
                <div class="pass-group position-relative">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="••••••••"
                        class="form-control @error('password') is-invalid @enderror">
                    <span class="ti toggle-password ti-eye-off position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer text-muted fs-18"></span>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                {{-- Password Strength --}}
                <div class="mt-2">
                    <div class="progress" style="height: 5px; border-radius: 10px;">
                        <div id="passwordStrengthBar" class="progress-bar" role="progressbar"></div>
                    </div>
                    <small id="passwordStrengthText" class="text-muted mt-1 d-block" style="font-size: 0.75rem;"></small>
                </div>
            </div>

            {{-- Confirm Password Field --}}
            <div class="mb-3">
                <label class="form-label fw-semibold" for="password_confirmation">Confirm Password</label>
                <div class="pass-group position-relative">
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required 
                        placeholder="••••••••"
                        class="form-control @error('password_confirmation') is-invalid @enderror">
                    <span class="ti toggle-password ti-eye-off position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer text-muted fs-18"></span>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <small id="passwordMatchError" class="text-danger d-none mt-1">Passwords do not match.</small>
            </div>

            {{-- Terms & Conditions --}}
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label text-muted small" for="terms">
                        I agree to the <a href="{{ route('terms') }}" target="_blank" class="text-primary fw-bold">Terms &amp; Privacy</a>
                    </label>
                </div>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn btn-primary w-100 mb-4 py-2">Create Account</button>

            {{-- Already have an account --}}
            <div class="auth-container">
                <p class="text-muted small mb-0">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-primary fw-bold">Sign In</a>
                </p>
            </div>
        </form>
    </div>

    {{-- Footer Text --}}
    <p class="auth-footer-text">&copy; {{ date('Y') }} Arewa Smart. All rights reserved.</p>


</x-guest-layout>

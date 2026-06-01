<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
    <script>
        /* Global Safety Net — Silently absorbs unhandled Promise rejections */
        window.addEventListener('unhandledrejection', function(event) {
            console.warn('[Arewa Smart] Unhandled promise rejection suppressed:', event.reason);
            event.preventDefault();
        });
    </script>
    <style>
        :root {
            /* Project Branding Tokens */
            --auth-primary: #d37102; 
            --auth-primary-rgb: 211, 113, 2;
            --auth-bg: #f8f9fa; 
            --auth-card-radius: 20px; 
            --auth-card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); 
            --transition: all 0.3s ease;
        }

        body.auth-layout {
            background-color: var(--auth-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 2rem 1rem;
            font-family: 'Nunito Sans', sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        .auth-container {
            width: 100%;
            max-width: 500px;
            margin: auto;
            position: relative;
            z-index: 1;
            animation: authFadeIn 0.5s ease-out;
            padding: 2rem 0;
        }

        @keyframes authFadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-card {
            background: #ffffff;
            border-radius: var(--auth-card-radius);
            box-shadow: var(--auth-card-shadow);
            padding: 2.5rem;
            border: none;
            position: relative;
        }


        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo img {
            max-width: 110px;
            height: auto;
        }

        /* Mobile Responsiveness Refinements */
        @media (max-width: 768px) {
            body.auth-layout {
                padding: 1.5rem 0.75rem;
            }
            .auth-card {
                padding: 1.5rem;
            }
            .auth-container {
                max-width: 100%;
            }
        }

        /* Project-specific UI overrides */
        .form-control {
            border-radius: 8px;
            padding: 0.6rem 0.9rem;
            border-color: #e2e8f0;
            font-size: 14px;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--auth-primary);
            box-shadow: 0 0 0 3px rgba(var(--auth-primary-rgb), 0.1);
        }

        .btn-primary {
            background-color: var(--auth-primary) !important;
            border-color: var(--auth-primary) !important;
            padding: 0.65rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .text-primary {
            color: var(--auth-primary) !important;
        }

        .auth-footer-text {
            color: #64748b;
            font-size: 13px;
            text-align: center;
            margin-top: 1.5rem;
        }
    </style>
</head>

<body class="auth-layout">
    <div id="global-loader" style="display: none;">
        <div class="page-loader"></div>
    </div>

    <div class="auth-container">
        {{ $slot }}
    </div>

    {{-- Modal stack is here at the root of body --}}
    @stack('modals')

    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle Password
            document.querySelectorAll('.toggle-password').forEach(toggle => {
                toggle.addEventListener('click', function () {
                    const group = this.closest('.pass-group') || this.parentElement;
                    const input = group.querySelector('input');
                    if (input) {
                        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                        input.setAttribute('type', type);
                        this.classList.toggle('ti-eye');
                        this.classList.toggle('ti-eye-off');
                    }
                });
            });

            // Loading state
            document.querySelectorAll('form').forEach(form => {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    form.addEventListener('submit', function () {
                        if (form.checkValidity()) {
                            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...`;
                            submitBtn.disabled = true;
                        }
                    });
                }
            });

            // Strength bar
            const pwdInp = document.getElementById('password');
            const bar = document.getElementById('passwordStrengthBar');
            const txt = document.getElementById('passwordStrengthText');
            if (pwdInp && bar) {
                pwdInp.addEventListener('input', () => {
                    const val = pwdInp.value;
                    let strength = 0;
                    if (val.length >= 8) strength++;
                    if (/[A-Z]/.test(val)) strength++;
                    if (/[0-9]/.test(val)) strength++;
                    if (/[^A-Za-z0-9]/.test(val)) strength++;
                    let w = strength * 25;
                    let c = 'bg-danger';
                    if (strength == 2) c = 'bg-warning';
                    if (strength == 3) c = 'bg-info';
                    if (strength == 4) c = 'bg-success';
                    bar.style.width = w + '%';
                    bar.className = 'progress-bar ' + c;
                });
            }
        });
    </script>

    <script>
        // PWA Implementation & Service Worker (Crucial for TWA / Play Store installation verification)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => {
                        console.log('Service Worker registered', reg);
                        // Check for service worker updates immediately
                        reg.addEventListener('updatefound', () => {
                            const newWorker = reg.installing;
                            if (newWorker) {
                                newWorker.addEventListener('statechange', () => {
                                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                        console.log('New service worker installed. Reloading...');
                                        window.location.reload();
                                    }
                                });
                            }
                        });
                    })
                    .catch(err => console.log('Service Worker registration failed', err));
            });

            // Reload the page when the new active service worker takes control
            let refreshing = false;
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                if (!refreshing) {
                    refreshing = true;
                    console.log('Controller changed. Reloading page...');
                    window.location.reload();
                }
            });
        }
    </script>
</body>
</html>

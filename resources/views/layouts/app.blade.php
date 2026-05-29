<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script>
        // Inline anti-flash script for Dark Mode
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();

        /*
         * Global Safety Net — Prevents unhandled Promise rejections from
         * surfacing as native browser alert() dialogs. Errors are logged
         * to the console for debugging but NEVER shown as popups to users.
         */
        window.addEventListener('unhandledrejection', function(event) {
            // Log to console for debugging, but suppress the native browser dialog
            console.warn('[Arewa Smart] Unhandled promise rejection suppressed:', event.reason);
            event.preventDefault();
        });
    </script>

   

    <meta name="description" content="SmartHR - An advanced Bootstrap 5 admin dashboard template for HRM and CRM. Ideal for managing employee records, payroll, attendance, recruitment, and team performance with an intuitive and responsive design. Perfect for HR teams and business managers looking to streamline workforce management.">
    <meta name="keywords" content="HR dashboard template, HRM admin template, Bootstrap 5 HR dashboard, workforce management dashboard, employee management system, payroll dashboard, HR analytics, admin dashboard, CRM admin template, human resources management, HR admin template, team management dashboard, recruitment dashboard, employee attendance system, performance management, HR CRM, HR dashboard HTML, Bootstrap HR template, employee engagement, HR software, project management dashboard">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/logo/app-logo.png') }}">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffffff">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/logo/app-logo.png') }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ asset('assets/img/logo/app-logo.png') }}" type="image/x-icon" />

    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Feather CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/feather/feather.css') }}">

    <!-- Tabler Icons -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">

    <!-- Datetimepicker -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">

    <!-- Bootstrap Tagsinput -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">

    <!-- Summernote -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote-lite.min.css') }}">

    <!-- Daterangepicker -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Color Picker -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/@simonwep/pickr/themes/nano.min.css') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@200;300;400;500;600;700;800;900;1000&display=swap" rel="stylesheet">

    <!-- Custom App CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bokanturai.css') }}">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- Dark Mode CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dark_mode.css') }}">

    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    @stack('styles')
    
    <style>
        /* Global Modal Fixes — Ensuring all modals work like pin.blade.php */
        .modal {
            z-index: 10001 !important;
        }
        .modal-backdrop {
            z-index: 10000 !important;
        }
        /* Standardizing Premium Modal Look */
        .modal-content {
            border-radius: 20px !important;
            border: none !important;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.2) !important;
            overflow: hidden;
        }
        .modal-header {
            border-bottom: none !important;
            padding: 1.5rem 1.5rem 1rem !important;
        }
        .modal-footer {
            border-top: none !important;
            padding: 1rem 1.5rem 1.5rem !important;
        }
    </style>
</head>

<body>
    <!-- Tap to top -->
    <div class="tap-top"><i class="iconly-Arrow-Up icli"></i></div>

    <!-- Loader -->
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <!-- Page Wrapper -->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        @include('layouts.partials.header')

        <div class="page-body-wrapper">
            @include('layouts.partials.sidebar')

            <div class="page-body">
                <div class="container-fluid">
                    @isset($header)
                        <div class="page-title">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h2>{{ $header }}</h2>
                                </div>
                            </div>
                        </div>
                    @endisset

                    <main>
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </div>
   <!-- ===== Footer Start ===== -->
<footer class="footer bg-primary text-light py-2 mt-4">
  <div class="container-fluid">
    <div class="row align-items-center justify-content-between">

      <!-- Left Side: Copyright -->
      <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
        <p class="mb-0 small">
          © <span id="currentYear"></span> 
          <strong class="text-dark"> Arewa Smart Idea  </strong>. 
          All Rights Reserved.
        </p>
      </div>

      <!-- Right Side: Social & Quick Links -->
      <div class="col-md-6 text-center text-md-end">
        <div class="d-inline-flex align-items-center gap-3">
          <a href="https://play.google.com/store/apps/details?id=com.arewasmart&pcampaignid=web_share" target="_blank" class="text-light text-decoration-none footer-social install-app-btn" title="Install Application">
            <i class="ti ti-download fs-18"></i>
          </a>
          <a href="https://www.facebook.com/arewasmartidea" target="_blank" class="text-light text-decoration-none footer-social">
            <i class="ti ti-brand-facebook fs-18"></i>
          </a>
          <a href="https://www.twitter.com/arewasmartidea" target="_blank" class="text-light text-decoration-none footer-social">
            <i class="ti ti-brand-twitter fs-18"></i>
          </a>
          <a href="https://wa.me/2348064333983" target="_blank" class="text-light text-decoration-none footer-social">
            <i class="ti ti-brand-whatsapp fs-18"></i>
          </a>
          <a href="mailto:arewasmart001@gmail.com" class="text-light text-decoration-none footer-social" title="Send us an email">
            <i class="ti ti-mail fs-18"></i>
          </a>
        </div>
      </div>

    </div>
  </div>
</footer>
<!-- ===== Footer End ===== -->

<div class="row">
            @include('pages.dashboard.kyc')
        </div>

<!-- ===== Footer Style ===== -->
<style>
  .footer {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    font-size: 14px;
    backdrop-filter: blur(8px);
  }
  .footer-social {
    transition: all 0.3s ease;
  }
  .footer-social:hover {
    color: #ffc107 !important;
    transform: translateY(-3px);
  }
</style>

  <!-- Auto Year Script -->
  <script>
    document.getElementById("currentYear").textContent = new Date().getFullYear();
  </script>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>

    <!-- Charts -->
    <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexchart/chart-data.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/chart-data.js') }}"></script>

    <!-- Date & Time -->
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <!-- Editors -->
    <script src="{{ asset('assets/plugins/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Color Picker -->
    <script src="{{ asset('assets/plugins/@simonwep/pickr/pickr.es5.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/todo.js') }}"></script>
    <script src="{{ asset('assets/js/theme-colorpicker.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/bokanturai.js') }}"></script>
    <script src="{{ asset('assets/js/bvn_ux.js') }}"></script>
    <script src="{{ asset('assets/js/data.js') }}"></script>
    <script src="{{ asset('assets/js/airtime.js') }}"></script>
    <script src="{{ asset('assets/js/pin.js') }}"></script>
    <script src="{{ asset('assets/js/bvnservices.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/sweetalert.js') }}"></script>
    <script src="{{ asset('assets/js/theme_toggle.js') }}"></script>


    <script>
        // Auto-dismiss alerts after 4 seconds
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(() => {
                document.querySelectorAll('.alert.alert-dismissible').forEach(alert => new bootstrap.Alert(alert).close());
            }, 4000);

            // Global Form Submission Loading State
            const forms = document.querySelectorAll('form:not(.no-loader):not([target="_blank"])');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (e.defaultPrevented) return;
                    
                    // Don't show loader for specific trivial forms or search
                    if (form.method.toLowerCase() === 'get') return;
                    
                    Swal.fire({
                        title: 'Processing Request',
                        text: 'Please wait while we secure your transaction...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                });
            });
        });
    </script>

    @stack('scripts')

    <script>
        /* 
           Modal Stability & UX Enhancement Script
           - Ensures no stuck backdrops
           - Re-enables scrolling if modals crash
           - Automatically resets forms on close for a clean experience
        */
        document.addEventListener('hidden.bs.modal', function (event) {
            const openModals = document.querySelectorAll('.modal.show');
            
            // 1. Cleanup Backdrops & Scroll
            if (openModals.length === 0) {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.style.overflow = 'auto';
                document.body.classList.remove('modal-open');
            }

            // 2. Form Reset (Optional but recommended for stability)
            const modal = event.target;
            const form = modal.querySelector('form');
            if (form && !form.classList.contains('no-reset')) {
                form.reset();
            }
        });

        // Ensure all modals are centered if they don't have the class
        document.addEventListener('show.bs.modal', function (event) {
            const modalDialog = event.target.querySelector('.modal-dialog');
            if (modalDialog && !modalDialog.classList.contains('modal-dialog-centered')) {
                modalDialog.classList.add('modal-dialog-centered');
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

        // Helper to detect if running in standalone mode (as installed App or TWA)
        function isRunningInApp() {
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches;
            const isSafariStandalone = window.navigator.standalone === true;
            const isAndroidApp = window.document.referrer.includes('android-app://') || 
                                 navigator.userAgent.toLowerCase().includes('wv') || 
                                 navigator.userAgent.toLowerCase().includes('webview');
            
            return isStandalone || isSafariStandalone || isAndroidApp;
        }

        const installBtns = document.querySelectorAll('.install-app-btn');
        const playStoreUrl = 'https://play.google.com/store/apps/details?id=com.arewasmart&pcampaignid=web_share';

        // Configure visibility and click handlers for install buttons
        if (isRunningInApp()) {
            // Hide install buttons if they are already using the Play Store app
            installBtns.forEach(btn => btn.style.display = 'none');
        } else {
            // Show install buttons and link them directly to the Play Store
            installBtns.forEach(btn => {
                btn.style.display = 'inline-flex';
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    window.open(playStoreUrl, '_blank');
                });
            });

            // Prompt mobile browser visitors to install our Play Store app (once every 24 hours)
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            if (isMobile) {
                const lastPlaystorePrompt = localStorage.getItem('playstore_install_prompt_last');
                const now = new Date().getTime();
                const twentyFourHours = 24 * 60 * 60 * 1000;

                if (!lastPlaystorePrompt || (now - lastPlaystorePrompt > twentyFourHours)) {
                    setTimeout(() => {
                        Swal.fire({
                            title: 'Arewa Smart App is on Play Store!',
                            text: 'Get our official app on the Google Play Store for faster access, better security, and a premium experience!',
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#F26522',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="fab fa-google-play me-2"></i> Install App',
                            cancelButtonText: 'Maybe later',
                            customClass: {
                                confirmButton: 'btn btn-primary px-4 py-2',
                                cancelButton: 'btn btn-secondary px-4 py-2'
                            },
                            buttonsStyling: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.open(playStoreUrl, '_blank');
                            }
                            localStorage.setItem('playstore_install_prompt_last', now);
                        });
                    }, 4000); // 4-second delay
                }
        }
    </script>

    @auth
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let globalAudioContext = null;

            // Initialize and unlock audio context on the very first user interaction
            function unlockAudio() {
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (AudioContext && !globalAudioContext) {
                        globalAudioContext = new AudioContext();
                    }
                    if (globalAudioContext && globalAudioContext.state === 'suspended') {
                        globalAudioContext.resume();
                    }
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.resume();
                        // Warm up the voices
                        window.speechSynthesis.getVoices();
                    }
                } catch (e) {
                    console.warn("Audio unlock failed:", e);
                }
            }

            // Bind to first click or touch start
            document.addEventListener('click', unlockAudio, { once: true });
            document.addEventListener('touchstart', unlockAudio, { once: true });

            // Warm up speech voices asynchronously
            if ('speechSynthesis' in window) {
                window.speechSynthesis.onvoiceschanged = function () {
                    window.speechSynthesis.getVoices();
                };
            }

            // Function to check for new credit transactions
            function checkNewCredits() {
                $.ajax({
                    url: "{{ route('wallet.checkVoiceCredits') }}",
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            // Update balance dynamically on every poll in case it changed
                            if (response.balance !== undefined) {
                                const event = new CustomEvent('wallet-balance-updated', {
                                    detail: { balance: response.balance }
                                });
                                document.dispatchEvent(event);
                            }

                            if (response.credits && response.credits.length > 0) {
                                let announced = JSON.parse(localStorage.getItem('announced_credits') || '[]');
                                let newCreditsDetected = false;

                                response.credits.forEach(function (credit) {
                                    const creditId = String(credit.id);
                                    if (!announced.includes(creditId)) {
                                        // Mark as new credit detected
                                        newCreditsDetected = true;
                                        announced.push(creditId);

                                        // 1. Play premium cash chime & TTS Voice announcement
                                        playSuccessChime();
                                        speakCreditNotification();

                                        // 2. Show premium SweetAlert2 Toast
                                        showPremiumCreditToast(credit.amount, credit.description);
                                    }
                                });

                                if (newCreditsDetected) {
                                    // Limit cached IDs to latest 50 to keep localStorage clean
                                    if (announced.length > 50) {
                                        announced = announced.slice(announced.length - 50);
                                    }
                                    localStorage.setItem('announced_credits', JSON.stringify(announced));
                                }
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        console.warn("Silent failure checking credits:", error);
                    }
                });
            }

            // Function to play a premium synthesizer cash-receipt arpeggio chime (Web Audio API)
            function playSuccessChime() {
                try {
                    // Try to unlock or reuse the global context
                    if (!globalAudioContext) {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        if (AudioContext) {
                            globalAudioContext = new AudioContext();
                        }
                    }
                    if (!globalAudioContext) return;

                    // If suspended, try to resume
                    if (globalAudioContext.state === 'suspended') {
                        globalAudioContext.resume();
                    }

                    const ctx = globalAudioContext;
                    
                    // Chime note 1 (E5 - 659.25 Hz)
                    const osc1 = ctx.createOscillator();
                    const gain1 = ctx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(659.25, ctx.currentTime);
                    gain1.gain.setValueAtTime(0.12, ctx.currentTime);
                    gain1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
                    osc1.connect(gain1);
                    gain1.connect(ctx.destination);
                    osc1.start();
                    osc1.stop(ctx.currentTime + 0.4);
                    
                    // Chime note 2 (A5 - 880.00 Hz) - offset for arpeggio
                    setTimeout(function () {
                        if (ctx.state === 'suspended') return;
                        const osc2 = ctx.createOscillator();
                        const gain2 = ctx.createGain();
                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(880.00, ctx.currentTime);
                        gain2.gain.setValueAtTime(0.12, ctx.currentTime);
                        gain2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
                        osc2.connect(gain2);
                        gain2.connect(ctx.destination);
                        osc2.start();
                        osc2.stop(ctx.currentTime + 0.6);
                    }, 120);
                } catch (e) {
                    console.warn("Chime Web Audio failed:", e);
                }
            }

            // Function to announce credit using Speech Synthesis (Web Speech API)
            function speakCreditNotification() {
                if ('speechSynthesis' in window) {
                    try {
                        // Force resume in case browser engine is stuck in suspended mode
                        window.speechSynthesis.resume();
                        window.speechSynthesis.cancel();

                        const text = "Your Arewa Smart Wallet has been credited successfully";
                        const utterance = new SpeechSynthesisUtterance(text);
                        utterance.lang = 'en-US'; // Explicitly target English to ensure speech engine triggers properly
                        
                        // Auto-select best English voice if available
                        const voices = window.speechSynthesis.getVoices();
                        const englishVoice = voices.find(voice => voice.lang.startsWith('en'));
                        if (englishVoice) {
                            utterance.voice = englishVoice;
                        }
                        
                        utterance.rate = 0.95; // Slightly slower for crisp clear pronunciation
                        utterance.pitch = 1.0;
                        
                        window.speechSynthesis.speak(utterance);
                    } catch (err) {
                        console.warn("Speech Synthesis error:", err);
                    }
                } else {
                    console.warn("Speech Synthesis not supported in this browser.");
                }
            }

            // Function to show a premium, jaw-dropping visual toast alert
            function showPremiumCreditToast(amount, description) {
                const formattedAmount = '₦' + parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2});
                
                Swal.fire({
                    title: '<span style="font-family: \'Nunito Sans\', sans-serif; font-weight: 800; color: #fff;">Wallet Credited!</span>',
                    html: `
                        <div class="d-flex align-items-center gap-3" style="text-align: left; font-family: 'Nunito Sans', sans-serif;">
                            <div style="background: rgba(255, 255, 255, 0.15); border-radius: 12px; padding: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                                <i class="ti ti-wallet fs-24 text-warning" style="color: #ffc107 !important;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 text-white" style="font-weight: 800; font-size: 1.15rem; letter-spacing: -0.3px;">+ ${formattedAmount}</h4>
                                <p class="mb-0 text-white-50" style="font-size: 0.75rem; line-height: 1.2; margin-top: 2px;">${description || 'Your wallet has been funded successfully.'}</p>
                            </div>
                        </div>
                    `,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 8000,
                    timerProgressBar: true,
                    background: 'linear-gradient(135deg, #1e293b 0%, #0f172a 100%)',
                    showClass: {
                        popup: 'animate__animated animate__fadeInRight animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutRight animate__faster'
                    },
                    customClass: {
                        popup: 'premium-toast border-0 shadow-lg'
                    },
                    didOpen: (toast) => {
                        toast.style.borderRadius = '16px';
                        toast.style.border = '1px solid rgba(255, 255, 255, 0.08)';
                        toast.style.backdropFilter = 'blur(10px)';
                    }
                });
            }

            // Run initial check 3.5 seconds after page load (gives time for SpeechSynthesis voices to load)
            setTimeout(checkNewCredits, 3500);

            // Poll every 10 seconds for real-time responsiveness
            setInterval(checkNewCredits, 10000);
        });
    </script>
    @endauth

    @include('ai.widget')
</body>
</html>

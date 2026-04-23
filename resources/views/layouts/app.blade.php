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
           <a href="javascript:void(0);" class="text-light text-decoration-none footer-social install-app-btn" title="Install Application">
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
        // PWA Implementation
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered', reg))
                    .catch(err => console.log('Service Worker registration failed', err));
            });
        }

        let deferredPrompt;
        const installBtns = document.querySelectorAll('.install-app-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;
            
            // Show all install buttons
            installBtns.forEach(btn => btn.style.display = 'inline-flex');

            // Check if we should show SweetAlert (once every 24 hours)
            const lastPrompt = localStorage.getItem('pwa_install_prompt_last');
            const now = new Date().getTime();
            const twentyFourHours = 24 * 60 * 60 * 1000;

            if (!lastPrompt || (now - lastPrompt > twentyFourHours)) {
                setTimeout(() => {
                    Swal.fire({
                        title: 'Install Arewa Smart?',
                        text: 'Install our application on your device for a better experience and quick access!',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#F26522',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Install now',
                        cancelButtonText: 'Maybe later'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            triggerInstall();
                        }
                        // Update last prompt time regardless of choice
                        localStorage.setItem('pwa_install_prompt_last', now);
                    });
                }, 3000); // 3-second delay after load
            }
        });

        installBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (deferredPrompt) {
                    triggerInstall();
                } else {
                    Swal.fire({
                        title: 'Installation Ready?',
                        text: 'To install this app, please use Google Chrome or Microsoft Edge. If you are already using them, wait a few seconds or ensure your connection is stable.',
                        icon: 'info',
                        confirmButtonColor: '#F26522'
                    });
                }
            });
        });

        function triggerInstall() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                        installBtns.forEach(btn => btn.style.display = 'none');
                    } else {
                        console.log('User dismissed the install prompt');
                    }
                    deferredPrompt = null;
                });
            }
        }

        window.addEventListener('appinstalled', (evt) => {
            console.log('App was installed');
            installBtns.forEach(btn => btn.style.display = 'none');
            Swal.fire({
                title: 'Installed!',
                text: 'Application has been successfully installed.',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            });
        });
    </script>
    @include('ai.widget')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Arewa Smart - Instant Utility, Data & Identity Services in Nigeria</title>
        <meta name="description" content="Pay electricity bills, buy cheap data, airtime, and access instant identity verification (BVN/NIN) services in Nigeria. Fast, secure, and automated.">
        <meta name="keywords" content="Arewa Smart, data, airtime, electricity bill, BVN search, NIN modification, Nigeria utility, cheap data, gift cards">
        <meta name="author" content="Arewa Smart">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:title" content="Arewa Smart - Instant Utility & Identity Services">
        <meta property="og:description" content="The ultimate platform for automated utility payments and fast identity verification in Nigeria.">
        <meta property="og:image" content="{{ asset('assets/img/logo/app-logo.png') }}">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url('/') }}">
        <meta property="twitter:title" content="Arewa Smart - Instant Utility & Identity Services">
        <meta property="twitter:description" content="Pay bills and verify identity instantly on Arewa Smart.">
        <meta property="twitter:image" content="{{ asset('assets/img/logo/app-logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        
        <!-- FontAwesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Favicons -->
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/logo/app-logo.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/logo/app-logo.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/logo/app-logo.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/logo/app-logo.png') }}">

        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#ffffff">

        <!-- Custom Styles -->
        <link rel="stylesheet" href="{{ asset('assets/css/welcome.css') }}">
    </head>
    <body>
        <!-- Mobile Sidebar Overlay -->
        <div class="overlay" id="mobile-overlay"></div>

        <!-- Header -->
        <header class="header">
            <a href="/" class="logo">
                <img src="{{ asset('assets/img/logo/app-logo.png') }}" alt="Arewa Smart Logo" style="height: 40px;">
            </a>
            
            <nav class="nav-links" id="nav-links">
                <a href="#home">Home</a>
                <a href="#services">Services</a>
                <a href="#about">About</a>
                <a href="#support">Contact Us</a>
                <a href="https://api.arewasmart.com.ng/docs">Api Services</a>
                <a href="#pricing">Pricing</a>


                <!-- Auth Buttons for Mobile Inside Navigation -->
                <div class="auth-buttons mobile-auth">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </nav>

            <div class="header-right">
                <!-- Auth Buttons for Desktop Header -->
                <div class="auth-buttons desktop-auth">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>

                <!-- Hamburger Menu Icon -->
                <div class="menu-toggle" id="mobile-menu">
                    <i class="fa-solid fa-bars"></i>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero" id="home">
            <div class="hero-content">
                <h1>Smart Solutions for <span>Identity & Utility Bills</span></h1>
                <p>Welcome to the ultimate platform for instant BVN searches, NIN modifications, utility bill payments, Spry Gift Cards, and cheap data top-ups. Experience fast, secure, and automated services 24/7.</p>
                <div class="hero-buttons">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary">Get Started Now</a>
                    @endif
                    <a href="{{ route('login') }}" class="btn btn-outline">Login Now</a>
                </div>
            </div>

            <div class="hero-image">
                <div class="chatbot-wrapper">
                    <div class="blob blob-1"></div>
                    <div class="blob blob-2"></div>
                    <div class="chatbot-container">
                        <div class="chat-header">
                            <div class="bot-avatar">
                                <i class="fa-solid fa-robot"></i>
                            </div>
                            <div class="chat-header-info">
                                <h3>Smart AI Assistant</h3>
                                <p>Online & Ready to Help</p>
                            </div>
                        </div>
                        <div class="chat-body" id="chatBody">
                            <div class="chat-message bot-message" style="animation-delay: 0.2s;">
                                Hello there! 👋 Welcome to Arewa Smart. I'm your AI assistant.
                            </div>
                            <div class="chat-message bot-message" style="animation-delay: 0.6s;">
                                I can automate your requests instantly. Check out our services:
                                <div class="chat-services">
                                    <span class="service-pill">BVN Search</span>
                                    <span class="service-pill">NIN Modification</span>
                                    <span class="service-pill">Electricity Bill</span>
                                    <span class="service-pill">Buy Data</span>
                                    <span class="service-pill">Spry Gift Card</span>
                                    <span class="service-pill">Bonus</span>
                                </div>
                            </div>
                            <div class="chat-message bot-message" style="animation-delay: 1s;">
                                How can I assist you today?
                            </div>
                        </div>
                        <div class="chat-input-area">
                            <input type="text" id="chatInput" class="chat-input" placeholder="Ask me about our services...">
                            <button id="sendBtn" class="chat-submit">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Our Services -->
       @include('pages.welcome.services')

        <!-- About Us -->
       @include('pages.welcome.about-us')

        <!-- Contact Us -->
       @include('pages.welcome.support')


       <!-- Testimonials -->
       @include('pages.welcome.testimonials')


       <!-- footer -->
       @include('pages.welcome.footer')


        <!-- Custom Scripts -->
        <script src="{{ asset('assets/js/welcome.js') }}"></script>
    </body>
</html>

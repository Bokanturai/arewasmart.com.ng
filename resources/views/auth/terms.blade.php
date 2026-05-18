<x-guest-layout>
    <title>Arewa Smart - Terms & Conditions</title>

    <style>
        :root {
            --primary-navy: #d37102;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --bg-light: #f8f9fa;
        }

        /* Override guest-layout constraints to allow wider content */
        .auth-container {
            max-width: 900px !important;
            padding: 2rem 0;
        }

        .terms-page-wrapper {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: authFadeIn 0.5s ease-out;
        }

        .terms-header {
            background: var(--primary-navy);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .terms-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .terms-header p {
            opacity: 0.8;
            font-size: 1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .terms-body {
            padding: 40px 60px;
            background: #fff;
        }

        .terms-footer {
            background: var(--bg-light);
            border-top: 1px solid var(--border-color);
            padding: 24px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .terms-header { padding: 30px 20px; }
            .terms-body { padding: 25px 20px; }
            .auth-container { padding: 1rem 0.5rem; }
        }
    </style>

    <div class="terms-page-wrapper">
        <!-- Header Section -->
        <header class="terms-header">
            <div class="mb-3">
                <img src="{{ asset('assets/img/logo/new-logo.png') }}" alt="Arewa Smart Logo"
                    class="img-fluid bg-white p-2 rounded-circle" style="max-width: 60px;">
            </div>
            <h1>Legal & Governance</h1>
            <p>Our commitment to transparency and legal compliance in the digital ecosystem.</p>
        </header>

        <div class="terms-body">
            @include('auth.partials.terms-content')
        </div>

        <!-- Footer Section -->
        <footer class="terms-footer">
            <p class="mb-3 small text-muted">For further clarification, please reach out to our legal department.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-primary px-4">Accept & Join</a>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary px-4">Back to Login</a>
            </div>
        </footer>
    </div>
</x-guest-layout>

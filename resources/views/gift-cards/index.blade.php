<x-app-layout>
    {{-- Meta & Styles --}}
    <title>Arewa Smart - Gift Cards</title>
    
    {{-- External Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    
    {{-- Premium Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Outfit:wght@400;600;700;900&display=swap" rel="stylesheet">

    <style>
        /* ============================================
           PREMIUM GIFT CARD STYLES
           ============================================ */
        
        /* ── Live Card Base ──────────────────────────────── */
        .live-card {
            width: 100%;
            max-width: 500px;
            min-height: 320px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            margin: 0 auto;
        }

        /* Decorative Elements */
        .live-card .card-glow {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 10% 10%, rgba(255, 100, 250, 0.2) 0%, transparent 60%),
                        radial-gradient(circle at 90% 90%, rgba(100, 200, 255, 0.15) 0%, transparent 60%);
            z-index: 5;
            pointer-events: none;
        }

        .live-card .card-particles {
            position: absolute;
            inset: 0;
            z-index: 10;
            pointer-events: none;
            overflow: hidden;
        }

        /* Card Header */
        .card-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem 1.4rem 0;
            position: relative;
            z-index: 2;
        }

        .card-chip {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            padding: 0.25rem 0.6rem;
            font-size: 0.55rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .card-brand {
            font-size: 0.82rem;
            font-weight: 700;
            opacity: 0.92;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            color: #ffd700;
        }

        /* Card Body */
        .card-body-area {
            padding: 0.2rem 1.4rem;
            flex: 1;
            position: relative;
            z-index: 20;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .card-title {
            font-family: 'Dancing Script', cursive;
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1.1;
            color: #fff;
            text-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: -10px;
            z-index: 25;
        }

        .card-message {
            font-size: 0.72rem;
            font-weight: 500;
            opacity: 0.95;
            line-height: 1.3;
            max-width: 85%;
            margin-top: 10px;
            text-align: center;
        }

        /* Amount Hero */
        .card-amount-hero {
            text-align: center;
            padding: 0.2rem 1.4rem;
            position: relative;
            z-index: 25;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex: 1;
        }

        .card-amount-hero .amount-value {
            font-size: 4.2rem;
            font-weight: 900;
            line-height: 1;
            color: #ffffff;
            text-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            letter-spacing: -2px;
        }

        .card-amount-hero .amount-currency {
            font-size: 2.2rem;
            font-weight: 900;
            color: #ffd700;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
            margin-right: -4px;
        }

        /* Bottom Bar */
        .card-bottom-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding: 0.5rem 1.2rem;
            position: relative;
            z-index: 50;
            min-height: 80px;
        }

        .card-token {
            flex: 1;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            opacity: 0.96;
            display: flex;
            align-items: center;
            word-break: break-all;
            color: #fff;
        }

        .card-qr-box {
            width: 64px;
            height: 64px;
            flex-shrink: 0;
            border-radius: 10px;
            background: #fff;
            display: grid;
            place-items: center;
            margin-left: 0.75rem;
            border: 2px solid rgba(255, 255, 255, 0.25);
        }

        /* ── 40+ Theme Gradients ─────────────────────────── */
        .theme-birthday {
            background: linear-gradient(45deg, #4c1d95 0%, #db2777 50%, #f59e0b 100%);
            position: relative;
        }
        .theme-birthday .card-particles::before {
            content: '🎈';
            position: absolute;
            top: -5%;
            left: -3%;
            font-size: 6rem;
            transform: rotate(-20deg);
            opacity: 0.9;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.4));
        }
        .theme-birthday .card-particles::after {
            content: '🎈';
            position: absolute;
            top: 15%;
            right: -5%;
            font-size: 5rem;
            transform: rotate(15deg);
            opacity: 0.75;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.4));
        }
        .theme-birthday .decoration-1,
        .theme-birthday .decoration-2 {
            position: absolute;
            z-index: 15;
        }
        .theme-birthday .decoration-1 {
            content: '🎈';
            bottom: 5%;
            left: 5%;
            font-size: 3.5rem;
            opacity: 0.5;
            transform: rotate(10deg);
            filter: blur(1px);
        }
        .theme-birthday .decoration-2 {
            content: '🎈';
            bottom: 20%;
            right: 15%;
            font-size: 3rem;
            opacity: 0.4;
            transform: rotate(-30deg);
            filter: blur(2px);
        }
        .theme-birthday .confetti {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 10% 20%, #fff 2px, transparent 2px),
                              radial-gradient(circle at 80% 40%, #ff0 2px, transparent 2px);
            background-size: 100px 100px;
            opacity: 0.4;
            pointer-events: none;
        }

        .theme-wedding { background: linear-gradient(135deg, #2c2c54 0%, #706fd3 60%, #c9a84c 100%); }
        .theme-anniversary { background: linear-gradient(135deg, #b24592 0%, #f15f79 100%); }
        .theme-graduation { background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%); }
        .theme-naming { background: linear-gradient(135deg, #74b9ff 0%, #a29bfe 50%, #fd79a8 100%); }
        .theme-housewarming { background: linear-gradient(135deg, #e67e22 0%, #f39c12 50%, #f1c40f 100%); }
        .theme-engagement { background: linear-gradient(135deg, #e84393 0%, #d63031 50%, #e84393 100%); }
        .theme-babyshower { background: linear-gradient(135deg, #74b9ff 0%, #a29bfe 100%); }
        .theme-romantic { background: linear-gradient(135deg, #6d0019 0%, #b71c1c 50%, #e53935 100%); }
        .theme-valentine { background: linear-gradient(135deg, #f953c6 0%, #b91d73 55%, #f953c6 100%); }
        .theme-apology { background: linear-gradient(135deg, #4776e6 0%, #8e54e9 100%); }
        .theme-thankyou { background: linear-gradient(135deg, #f7971e 0%, #e05d5d 50%, #ffd200 100%); }
        .theme-missyou { background: linear-gradient(135deg, #373b44 0%, #1565c0 60%, #4286f4 100%); }
        .theme-friendship { background: linear-gradient(135deg, #f093fb 0%, #f5576c 50%, #4facfe 100%); }
        .theme-fordad { background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #1565c0 100%); }
        .theme-formom { background: linear-gradient(135deg, #e91e63 0%, #f48fb1 50%, #e91e63 100%); }
        .theme-forbrother { background: linear-gradient(135deg, #004d40 0%, #00695c 50%, #26a69a 100%); }
        .theme-forsister { background: linear-gradient(135deg, #6a1b9a 0%, #ab47bc 50%, #ce93d8 100%); }
        .theme-family { background: linear-gradient(135deg, #bf360c 0%, #e64a19 50%, #ff7043 100%); }
        .theme-care { background: linear-gradient(135deg, #1b5e20 0%, #388e3c 50%, #66bb6a 100%); }
        .theme-christmas { background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 40%, #c62828 100%); }
        .theme-newyear { background: linear-gradient(135deg, #0f0c29 0%, #302b63 55%, #715c8e 100%); }
        .theme-eid { background: linear-gradient(135deg, #004d40 0%, #00695c 50%, #c9a84c 100%); }
        .theme-ramadan { background: linear-gradient(135deg, #0c3547 0%, #1a5276 50%, #1f618d 100%); }
        .theme-easter { background: linear-gradient(135deg, #6a1b9a 0%, #8e24aa 50%, #d4e157 100%); }
        .theme-independence { background: linear-gradient(135deg, #006400 0%, #008751 55%, #006400 100%); }
        .theme-reward { background: linear-gradient(135deg, #0f0c29 0%, #302b63 55%, #c9a84c 100%); }
        .theme-bonus { background: linear-gradient(135deg, #003d1a 0%, #005c32 50%, #007a45 100%); }
        .theme-customerapp { background: linear-gradient(135deg, #0d0d1a 0%, #16213e 55%, #0f3460 100%); }
        .theme-promotion { background: linear-gradient(135deg, #1a1a1a 0%, #333 50%, #c9a84c 100%); }
        .theme-salary { background: linear-gradient(135deg, #003366 0%, #004e92 55%, #006bb3 100%); }
        .theme-loyalty { background: linear-gradient(135deg, #1a0030 0%, #4a0072 50%, #0b8793 100%); }
        .theme-gaming { background: linear-gradient(135deg, #0a0a1a 0%, #1a1a3e 50%, #7c3aed 100%); }
        .theme-shopping { background: linear-gradient(135deg, #c2185b 0%, #e91e63 50%, #f06292 100%); }
        .theme-food { background: linear-gradient(135deg, #bf360c 0%, #f57c00 50%, #ffcc02 100%); }
        .theme-travel { background: linear-gradient(135deg, #006064 0%, #00838f 50%, #4dd0e1 100%); }
        .theme-surprise { background: linear-gradient(135deg, #f39c12 0%, #e74c3c 50%, #8e44ad 100%); }
        .theme-getwell { background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 50%, #81c784 100%); }
        .theme-condolence { background: linear-gradient(135deg, #37474f 0%, #546e7a 50%, #78909c 100%); }
        .theme-support { background: linear-gradient(135deg, #4a148c 0%, #6a1b9a 50%, #ab47bc 100%); }
        .theme-general { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #3b82f6 100%); }
        .theme-cash { background: linear-gradient(135deg, #134e5e 0%, #1a6b4a 50%, #71b280 100%); }
        .theme-custom { background: linear-gradient(135deg, #1c1c2e 0%, #2d2d44 50%, #414168 100%); }

        /* ── Responsive Design ───────────────────────────── */
        @media (max-width: 768px) {
            .live-card {
                max-width: 100%;
                border-radius: 20px;
            }
            .card-amount-hero .amount-value { font-size: 3rem; }
            .card-amount-hero .amount-currency { font-size: 1.6rem; }
            .card-title { font-size: 1.8rem; margin-bottom: -5px; }
            .card-message { font-size: 0.65rem; max-width: 90%; }
            .card-bottom-bar { padding: 0.4rem 1rem; min-height: 70px; }
            .card-token .fw-bold { font-size: 0.8rem !important; }
            .card-qr-box { width: 55px; height: 55px; }
        }

        @media (max-width: 480px) {
            .card-amount-hero .amount-value { font-size: 2.4rem; }
            .card-title { font-size: 1.5rem; }
            .card-brand { font-size: 0.65rem; }
            .card-chip { font-size: 0.45rem; }
        }

        /* ── Mobile Card List ────────────────────────────── */
        .mobile-card-item {
            transition: transform 0.2s;
        }
        .mobile-card-item:active {
            transform: scale(0.98);
        }

        .stats-card-gradient-1 {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(79, 70, 229, 0.02) 100%);
        }
        .stats-card-gradient-2 {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.02) 100%);
        }
        .stats-card-gradient-3 {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(6, 182, 212, 0.02) 100%);
        }

        /* ── Redemption Modal Styles ─────────────────────── */
        .redeem-modal-content {
            border-radius: 20px !important;
            border: none !important;
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.25);
        }

        .code-input-group {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .code-input-group:focus-within {
            background: #ffffff;
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08);
            transform: translateY(-2px);
        }

        .redeem-code-input {
            letter-spacing: 0.15em;
            font-size: 1.4rem !important;
            font-weight: 800 !important;
            color: var(--bs-primary) !important;
            font-family: 'Outfit', 'Courier New', Courier, monospace !important;
        }

        #qr-reader {
            border: 2px dashed #cbd5e1 !important;
            background: #f8fafc;
            border-radius: 20px;
            overflow: hidden;
        }
        #qr-reader video {
            border-radius: 20px !important;
        }

        /* ── Mobile Card List Premium Styles ─────────────── */
        .mobile-card-item {
            background: #ffffff;
            border: 1px solid #e2e8f0 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important;
        }
        .mobile-card-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #4f46e5;
        }
        .mobile-card-item.status-redeemed::before {
            background: #10b981;
        }

        .mobile-amount-display {
            font-size: 1.35rem !important;
            font-weight: 900 !important;
            letter-spacing: -0.5px;
        }

        .pagination {
            justify-content: center;
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
            gap: 8px;
        }
        .pagination .page-item .page-link {
            border: none;
            border-radius: 20px !important;
            min-width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0 12px;
        }
        .pagination .page-item.active .page-link {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2);
        }
        .pagination .page-item:not(.active):hover .page-link {
            background: #ffffff;
            color: #4f46e5;
            border-color: #4f46e5;
            transform: translateY(-2px);
        }
        .pagination .page-item.disabled .page-link {
            background: #f1f5f9;
            color: #94a3b8;
            border-color: #e2e8f0;
            opacity: 0.6;
        }
        .pagination-container {
            padding: 1.5rem;
            border-top: 1px solid #f1f5f9;
        }

        /* ── Desktop Filter Styling ───────────────────── */
        .filter-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .filter-input-wrapper {
            background: #f8f9fa;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            display: flex;
            align-items: center;
            padding: 0 12px;
            height: 45px;
            transition: all 0.2s;
        }
        .filter-input-wrapper:focus-within {
            background: #ffffff;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }
        .filter-input-wrapper i {
            color: #64748b;
            font-size: 1rem;
            margin-right: 8px;
        }
        .filter-input-wrapper input, 
        .filter-input-wrapper select {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            font-weight: 500;
            color: #334155;
            width: 100%;
            font-size: 0.9rem;
        }
        .filter-input-wrapper input::placeholder {
            color: #94a3b8;
        }
        .btn-filter-action {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            background: #f76b2c;
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .btn-filter-action:hover {
            background: #e65a1d;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(247, 107, 44, 0.3);
            color: white;
        }
        .btn-filter-reset {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            background: #f1f5f9;
            color: #64748b;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .btn-filter-reset:hover {
            background: #e2e8f0;
            color: #334155;
        }

        /* ── Dark Mode Support ─────────────────────────── */
        [data-theme="dark"] .filter-card,
        [data-theme="dark"] .card,
        [data-theme="dark"] .mobile-card-item,
        [data-theme="dark"] .redeem-modal-content,
        [data-theme="dark"] .modal-content {
            background: #1e1e2d !important;
            color: #e2e8f0;
            border-color: #2b2b40 !important;
        }

        [data-theme="dark"] .filter-input-wrapper,
        [data-theme="dark"] .code-input-group,
        [data-theme="dark"] #qr-reader {
            background: #2b2b40 !important;
            border-color: #3f3f5f !important;
        }

        [data-theme="dark"] .filter-input-wrapper input,
        [data-theme="dark"] .filter-input-wrapper select,
        [data-theme="dark"] .redeem-code-input {
            color: #ffffff !important;
        }

        [data-theme="dark"] .filter-input-wrapper i,
        [data-theme="dark"] .text-muted {
            color: #94a3b8 !important;
        }

        [data-theme="dark"] .card-header,
        [data-theme="dark"] .table-light,
        [data-theme="dark"] .pagination-container {
            background: #1e1e2d !important;
            border-color: #2b2b40 !important;
        }

        [data-theme="dark"] .table thead th {
            background: #2b2b40 !important;
            color: #94a3b8 !important;
        }

        [data-theme="dark"] .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        [data-theme="dark"] .text-dark,
        [data-theme="dark"] .modal-title {
            color: #ffffff !important;
        }

        [data-theme="dark"] .pagination .page-item .page-link {
            background: #2b2b40;
            border-color: #3f3f5f;
            color: #94a3b8;
        }

        [data-theme="dark"] .pagination .page-item.active .page-link {
            background: #4f46e5;
            border-color: #4f46e5;
            color: #ffffff;
        }

        [data-theme="dark"] .btn-light,
        [data-theme="dark"] .btn-filter-reset {
            background: #2b2b40 !important;
            border-color: #3f3f5f !important;
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .btn-filter-reset:hover {
            background: #3f3f5f !important;
            color: #ffffff !important;
        }

        [data-theme="dark"] .avatar.bg-light,
        [data-theme="dark"] .bg-light,
        [data-theme="dark"] .bg-white {
            background: #1e1e2d !important;
        }

        [data-theme="dark"] .translate-middle.bg-white {
            background: #1e1e2d !important;
            color: #94a3b8 !important;
        }

        [data-theme="dark"] .badge.bg-light {
            background: #2b2b40 !important;
            color: #e2e8f0 !important;
            border-color: #3f3f5f !important;
        }

        [data-theme="dark"] hr {
            border-color: rgba(255, 255, 255, 0.1);
        }

        [data-theme="dark"] .avatar-xs.bg-light i {
            color: #94a3b8 !important;
        }
    </style>

    {{-- Main Content --}}
    <div class="container-fluid px-0 px-md-3 mb-4 mt-4">
        
        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                    <div class="avatar flex-shrink-0 bg-primary-transparent rounded-circle" style="width: 45px; height: 45px;">
                        <i class="ti ti-gift text-primary fs-20"></i>
                    </div>
                    Gift Cards Dashboard
                </h4>
                <p class="text-muted small mb-0 ms-md-5 ps-md-2">Create, manage, and redeem personalized digital gift cards instantly.</p>
            </div>
            <div class="d-flex gap-2 w-100 w-md-auto">
                <button type="button" data-bs-toggle="modal" data-bs-target="#redeemGiftCardModal" 
                        class="btn btn-outline-primary fw-bold d-flex align-items-center justify-content-center gap-2 rounded-pill px-4 flex-grow-1 flex-md-grow-0 py-2 py-md-2">
                    <i class="ti ti-qrcode fs-15"></i> Redeem
                </button>
                <a href="{{ route('gift-card.generate') }}" 
                   class="btn btn-primary fw-bold d-flex align-items-center justify-content-center gap-2 rounded-pill px-4 flex-grow-1 flex-md-grow-0 py-2 py-md-2 shadow-sm">
                    <i class="ti ti-wand fs-15"></i> Generate
                </a>
            </div>
        </div>

        {{-- Search & Filter Section --}}
        <div class="mb-4">
            <div class="filter-card p-3 p-md-4">
                <form action="{{ route('gift-card.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <div class="flex-grow-1 flex-basis-100 flex-basis-md-0" style="min-width: 250px;">
                        <div class="filter-input-wrapper">
                            <i class="ti ti-search"></i>
                            <input type="text" name="search" placeholder="Search Title, Amount..." value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    <div class="flex-grow-1 flex-md-grow-0" style="min-width: 140px; width: auto;">
                        <div class="filter-input-wrapper">
                            <i class="ti ti-adjustments-horizontal"></i>
                            <select name="style">
                                <option value="">All Types</option>
                                <option value="birthday" {{ request('style') == 'birthday' ? 'selected' : '' }}>Birthday</option>
                                <option value="wedding" {{ request('style') == 'wedding' ? 'selected' : '' }}>Wedding</option>
                                <option value="anniversary" {{ request('style') == 'anniversary' ? 'selected' : '' }}>Anniversary</option>
                                <option value="general" {{ request('style') == 'general' ? 'selected' : '' }}>General</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex-grow-1 flex-md-grow-0" style="min-width: 140px; width: auto;">
                        <div class="filter-input-wrapper">
                            <i class="ti ti-chart-dots"></i>
                            <select name="status">
                                <option value="">Statuses</option>
                                <option value="unused" {{ request('status') == 'unused' ? 'selected' : '' }}>Active</option>
                                <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Redeemed</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex-grow-1 flex-md-grow-0" style="min-width: 140px; width: auto;">
                        <div class="filter-input-wrapper">
                            <i class="ti ti-calendar"></i>
                            <input type="date" name="created_from" value="{{ request('created_from') }}" title="Created From">
                        </div>
                    </div>

                    <div class="flex-grow-1 flex-md-grow-0" style="min-width: 140px; width: auto;">
                        <div class="filter-input-wrapper">
                            <i class="ti ti-calendar-event"></i>
                            <input type="date" name="created_to" value="{{ request('created_to') }}" title="Created To">
                        </div>
                    </div>

                    <div class="d-flex gap-2 ms-auto pt-2 pt-md-0">
                        <button type="submit" class="btn-filter-action flex-grow-1 flex-md-grow-0" title="Apply Filters">
                            <i class="ti ti-filter fs-14"></i>
                        </button>
                        <a href="{{ route('gift-card.index') }}" class="btn-filter-reset flex-grow-1 flex-md-grow-0" title="Reset Filters">
                            <i class="ti ti-rotate fs-14"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @php
            $isFiltered = request()->anyFilled(['search', 'style', 'status', 'created_from', 'created_to', 'redeemed_from', 'redeemed_to']);
        @endphp

        {{-- Stats Grid --}}
        <div class="mb-4">
            <div class="row g-0 g-md-4">
                <div class="col-md-6 col-xl-4 mb-2 mb-md-0">
                    <div class="card shadow-lg border-0 h-100 overflow-hidden stats-card-gradient-1" style="border-radius: 20px;">
                        <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center text-center">
                            <div class="avatar avatar-lg bg-primary rounded-circle mb-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="ti ti-gift text-white fs-2"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-1 small fw-bold text-uppercase letter-spacing-1">Created Cards</p>
                                <h3 class="mb-0 fw-black text-dark">{{ number_format($createdCards->count()) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4 mb-2 mb-md-0">
                    <div class="card shadow-lg border-0 h-100 overflow-hidden stats-card-gradient-2" style="border-radius: 20px;">
                        <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center text-center">
                            <div class="avatar avatar-lg bg-success rounded-circle mb-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="ti ti-circle-check text-white fs-2"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-1 small fw-bold text-uppercase letter-spacing-1">Redeemed Card Value</p>
                                <h3 class="mb-0 fw-black text-success">₦{{ number_format($redeemedCards->sum('amount'), 0) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xl-4">
                    <div class="card shadow-lg border-0 h-100 overflow-hidden stats-card-gradient-3" style="border-radius: 20px;">
                        <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center text-center">
                            <div class="avatar avatar-lg bg-info rounded-circle mb-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="ti ti-wallet text-white fs-2"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-1 small fw-bold text-uppercase letter-spacing-1">Wallet Balance</p>
                                <h3 class="mb-0 fw-black text-info">₦{{ number_format(auth()->user()->wallet->balance ?? 0, 0) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 20px;">
                <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
                    <h5 class="fw-bold d-flex align-items-center gap-2 mb-0">
                        <i class="ti ti-gift text-primary"></i> Cards I've Generated
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($createdCards->count() > 0)
                        {{-- Desktop Table --}}
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th class="fw-bold text-uppercase px-4" style="width: 50px;">S/N</th>
                                        <th class="fw-bold text-uppercase">Card Title</th>
                                        <th class="fw-bold text-uppercase">Value</th>
                                        <th class="fw-bold text-uppercase">Style</th>
                                        <th class="fw-bold text-uppercase">Status</th>
                                        <th class="fw-bold text-uppercase">Created Date</th>
                                        <th class="fw-bold text-uppercase text-end px-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($createdCards as $card)
                                        <tr>
                                            <td class="px-4 text-muted small fw-bold">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $card->title }}</div>
                                            </td>
                                            <td class="fw-bold text-dark fs-15">₦{{ number_format($card->amount) }}</td>
                                          
                                            <td class="text-capitalize">
                                                <span class="badge bg-light text-dark fw-bold border">{{ $card->style }}</span>
                                            </td>
                                            <td>
                                                @if($card->status === 'unused')
                                                    <span class="badge bg-success-transparent text-success px-3 py-1 rounded-pill">Active</span>
                                                @else
                                                    <span class="badge bg-secondary-transparent text-secondary px-3 py-1 rounded-pill">Redeemed</span>
                                                @endif
                                            </td>
                                            <td class="text-muted">{{ $card->created_at->format('M d, Y') }}</td>
                                            <td class="text-end px-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 download-card-btn"
                                                            data-card="{{ json_encode(['id' => $card->id, 'title' => $card->title, 'message' => $card->message, 'style' => $card->style, 'amount' => $card->amount, 'status' => $card->status, 'text_color' => $card->text_color ?? '#ffffff']) }}">
                                                        <i class="ti ti-download me-1"></i> Preview
                                                    </button>
                                                    <button type="button" class="btn btn-primary-transparent btn-sm rounded-pill px-3 share-card-row-btn"
                                                            data-card="{{ json_encode(['id' => $card->id, 'title' => $card->title, 'message' => $card->message, 'style' => $card->style, 'amount' => $card->amount, 'status' => $card->status, 'text_color' => $card->text_color ?? '#ffffff']) }}">
                                                        <i class="ti ti-share me-1"></i> Share
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-md-none p-3 pt-4">
                            @foreach($createdCards as $card)
                                <div class="card mb-3 mobile-card-item {{ $card->status === 'redeemed' ? 'status-redeemed' : '' }} shadow-none" style="border-radius: 20px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex flex-column">
                                                <span class="text-muted small fw-bold text-uppercase opacity-75 letter-spacing-1" style="font-size: 0.65rem;">Recipient Card</span>
                                                <h6 class="fw-bold mb-0 text-dark fs-16">{{ $card->title }}</h6>
                                            </div>
                                            @if($card->status === 'unused')
                                                <span class="badge bg-success-transparent text-success rounded-pill px-3">Active</span>
                                            @else
                                                <span class="badge bg-secondary-transparent text-secondary rounded-pill px-3">Redeemed</span>
                                            @endif
                                        </div>
                                        
                                        <div class="d-flex align-items-baseline gap-1 my-2">
                                            <span class="text-{{ $card->status === 'unused' ? 'primary' : 'secondary' }} small fw-black">₦</span>
                                            <h4 class="text-{{ $card->status === 'unused' ? 'primary' : 'secondary' }} mobile-amount-display mb-0">
                                                {{ number_format($card->amount) }}
                                            </h4>
                                        </div>

                                        <div class="mb-3">
                                            <span class="text-muted small d-block mb-1">Gift Code:</span>
                                            @if($card->status === 'unused')
                                                <div class="bg-light p-2 rounded border border-dashed d-flex justify-content-between align-items-center">
                                                    <code class="text-primary fw-bold fs-15">{{ $card->safe_token }}</code>
                                                    <button class="btn btn-sm p-0 text-muted" onclick="navigator.clipboard.writeText('{{ $card->safe_token }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'Copied', showConfirmButton:false, timer:1500})">
                                                        <i class="ti ti-copy"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="badge bg-secondary-transparent text-secondary w-100 py-2">ALREADY CLAIMED</span>
                                            @endif
                                        </div>

                                        <div class="text-muted small mb-3">
                                            <i class="ti ti-calendar-event me-1"></i> Generated {{ $card->created_at->format('M d, Y • h:ia') }}
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-6">
                                                <button class="btn btn-light w-100 rounded-pill py-2 download-card-btn shadow-sm fw-bold border"
                                                        data-card="{{ json_encode(['id' => $card->id, 'title' => $card->title, 'message' => $card->message, 'style' => $card->style, 'amount' => $card->amount, 'text_color' => $card->text_color ?? '#ffffff']) }}">
                                                    <i class="ti ti-eye"></i> View
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button class="btn btn-primary w-100 rounded-pill py-2 share-card-row-btn shadow-sm fw-bold"
                                                        data-card="{{ json_encode(['id' => $card->id, 'title' => $card->title, 'message' => $card->message, 'style' => $card->style, 'amount' => $card->amount, 'text_color' => $card->text_color ?? '#ffffff']) }}">
                                                    <i class="ti ti-share"></i> Share
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination for Created Cards --}}
                        @if($createdCards->hasPages())
                            <div class="pagination-container justify-content-center {{ $isFiltered ? 'd-flex' : 'd-none d-md-flex' }}">
                                {{ $createdCards->appends(['redeemed_page' => $redeemedCards->currentPage()])->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl bg-primary-transparent rounded-circle mb-3 mx-auto" style="width: 80px; height: 80px;">
                                <i class="ti ti-box text-primary fs-1"></i>
                            </div>
                            <h5 class="fw-bold text-dark">No Cards Found</h5>
                            <p class="text-muted mb-0 small px-4">You haven't generated any gift cards yet. Start by creating one!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Download/Preview Modal --}}
        <div class="modal fade" id="downloadCardModal" tabindex="-1" aria-labelledby="downloadCardModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content rounded-20px border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <div>
                            <h5 class="modal-title fw-bold" id="downloadCardModalLabel">Gift Card Preview</h5>
                            <p class="small text-muted mb-0">Preview, download, or share the card as PNG.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="downloadCardPreview" class="p-3"></div>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                    
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-outline-primary rounded-pill" id="downloadCardFileButton">
                                <i class="ti ti-download me-1"></i> PNG
                            </button>
                            <button type="button" class="btn btn-primary rounded-pill" id="shareCardFileButton">
                                <i class="ti ti-share me-1"></i> Share
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Redeem Gift Card Modal --}}
        <div class="modal fade" id="redeemGiftCardModal" tabindex="-1" aria-labelledby="redeemGiftCardModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content redeem-modal-content">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 pt-0 text-center">
                        <div class="avatar avatar-xl bg-primary-transparent rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px;">
                            <i class="ti ti-gift text-primary fs-1"></i>
                        </div>
                        <h4 class="fw-black text-dark mb-1">Redeem Gift Card</h4>
                        <p class="text-muted small mb-4">Enter your 15-character secure code to credit your wallet.</p>

                        <form action="{{ route('gift-card.processRedeem') }}" method="POST" id="redeemForm">
                            @csrf
                            <div class="mb-4 text-start">
                                <label for="redeem_code" class="form-label fw-bold text-muted text-uppercase small letter-spacing-1 mb-2">Secure Gift Code</label>
                                <div class="input-group input-group-lg code-input-group rounded-4 overflow-hidden shadow-sm">
                                    <span class="input-group-text bg-transparent border-0 text-primary pe-0">
                                        <i class="ti ti-lock-square-rounded fs-3"></i>
                                    </span>
                                    <input type="text" name="code" id="redeem_code"
                                           class="form-control bg-transparent border-0 ps-3 text-center redeem-code-input font-monospace"
                                           autocomplete="off" spellcheck="false" required placeholder="XXXXX-XXXXX-XXXXX" maxlength="17">
                                </div>
                                <div class="form-text text-center mt-3 small opacity-75">
                                    <i class="ti ti-info-circle-filled me-1"></i> Format: 5-5-5 letters/numbers
                                </div>
                            </div>

                            <button type="submit" id="redeemSubmitBtn" 
                                    class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-black shadow-lg d-flex justify-content-center align-items-center gap-2 text-uppercase letter-spacing-1">
                                <i class="ti ti-circle-check fs-20"></i> Redeem Now
                            </button>
                        </form>

                        <div class="position-relative my-4 py-2">
                            <hr class="text-muted opacity-25">
                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small fw-bold text-uppercase border rounded-pill">OR</span>
                        </div>

                        {{-- Camera Scanner --}}
                        <div id="modalScannerSection">
                            <button type="button" class="btn btn-light w-100 py-3 rounded-pill fw-bold d-flex justify-content-center align-items-center gap-2 mb-3 border shadow-none" id="modalStartScanBtn">
                                <i class="ti ti-camera fs-20 text-primary"></i> Scan QR Code
                            </button>

                            <div id="qr-reader" class="rounded-4 overflow-hidden mb-3 shadow-none border-2" style="display: none; height: 280px;"></div>

                            <button type="button" class="btn btn-danger w-100 py-2 rounded-pill fw-bold d-flex justify-content-center align-items-center gap-2 mb-3 shadow-sm" id="modalStopScanBtn" style="display:none;">
                                <i class="ti ti-x fs-15"></i> Cancel Scanning
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-2 mt-md-0">
            <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 20px;">
                <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
                    <h5 class="fw-bold d-flex align-items-center gap-2 mb-0">
                        <i class="ti ti-wallet text-success"></i> Cards I've Redeemed
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($redeemedCards->count() > 0)
                        {{-- Desktop Table --}}
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th class="fw-bold text-uppercase px-4" style="width: 50px;">S/N</th>
                                        <th class="fw-bold text-uppercase">Card Title</th>
                                        <th class="fw-bold text-uppercase">Card Value</th>
                                        <th class="fw-bold text-uppercase">Generated By</th>
                                        <th class="fw-bold text-uppercase pe-4">Redeemed Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($redeemedCards as $card)
                                        <tr>
                                            <td class="px-4 text-muted small fw-bold">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $card->title }}</div>
                                            </td>
                                            <td class="fw-bold text-success">+₦{{ number_format($card->amount) }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-xs bg-light rounded-circle flex-shrink-0">
                                                        <i class="ti ti-user text-muted fs-15"></i>
                                                    </div>
                                                    <span class="fw-medium text-dark">{{ $card->creator->first_name ?? 'Someone' }} {{ $card->creator->last_name ?? '' }}</span>
                                                </div>
                                            </td>
                                            <td class="text-muted pe-4">
                                                {{ $card->used_at ? $card->used_at->format('M d, h:ia') : 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-md-none p-3 pt-4">
                            @foreach($redeemedCards as $card)
                                <div class="card mb-3 mobile-card-item status-redeemed shadow-none border-0" style="border-radius: 20px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex flex-column text-start">
                                                <span class="text-muted small fw-bold text-uppercase opacity-75 letter-spacing-1" style="font-size: 0.65rem;">Gift Claimed</span>
                                                <h6 class="fw-bold mb-0 text-dark">{{ $card->title }}</h6>
                                            </div>
                                            <span class="badge bg-success-transparent text-success rounded-pill px-3">Redeemed</span>
                                        </div>
                                        
                                        <div class="d-flex align-items-baseline gap-1 my-2">
                                            <span class="text-success small fw-black">₦</span>
                                            <h4 class="text-success mobile-amount-display mb-0">
                                                {{ number_format($card->amount) }}
                                            </h4>
                                        </div>

                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="avatar avatar-xs bg-light rounded-circle flex-shrink-0" style="width:20px; height:20px;">
                                                <i class="ti ti-user text-muted" style="font-size: 0.6rem;"></i>
                                            </div>
                                            <span class="text-muted small">Gift from <strong>{{ $card->creator->first_name ?? 'Unknown' }}</strong></span>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="ti ti-calendar-event me-1"></i> Claimed {{ $card->used_at ? $card->used_at->format('M d, Y • h:ia') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination for Redeemed Cards --}}
                        @if($redeemedCards->hasPages())
                            <div class="pagination-container justify-content-center {{ $isFiltered ? 'd-flex' : 'd-none d-md-flex' }}">
                                {{ $redeemedCards->appends(['created_page' => $createdCards->currentPage()])->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl bg-success-transparent rounded-circle mb-3 mx-auto" style="width: 80px; height: 80px;">
                                <i class="ti ti-ticket text-success fs-1"></i>
                            </div>
                            <h5 class="fw-bold text-dark">No Redeemed Cards</h5>
                            <p class="text-muted mb-0 small px-4">You haven't claimed any gift cards yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        // ── Flash Messages via SweetAlert2 ───────────────────────────────────
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ addslashes(session('success')) }}',
                    confirmButtonColor: '#4f46e5',
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    toast: false,
                    customClass: { popup: 'rounded-4' }
                });
            });
        @endif

        @if(session('error'))
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: '{{ addslashes(session('error')) }}',
                    confirmButtonColor: '#d63031',
                    timer: 8000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    customClass: { popup: 'rounded-4' }
                });
            });
        @endif

        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonColor: '#e17055',
                    customClass: { popup: 'rounded-4' }
                });
            });
        @endif

        document.addEventListener('DOMContentLoaded', function () {
            let currentCard = null;
            const downloadCardModal = document.getElementById('downloadCardModal');
            const downloadCardPreview = document.getElementById('downloadCardPreview');
            const downloadButton = document.getElementById('downloadCardFileButton');
            const shareButton = document.getElementById('shareCardFileButton');
            const modalInstance = new bootstrap.Modal(downloadCardModal);

            // Render Card Preview
            function renderCardPreview(card) {
                downloadCardPreview.innerHTML = `
                    <div class="live-card theme-${card.style || 'general'}" id="currentPreviewCard" 
                         style="--gc-text: ${card.text_color || '#ffffff'};">
                        <div class="card-glow"></div>
                        <div class="card-particles"></div>
                        <div class="decoration-1">🎈</div>
                        <div class="decoration-2">🎈</div>
                        <div class="confetti"></div>

                        <div class="card-header-bar justify-content-center pt-4">
                            <div class="card-title text-center m-0">${escapeHtml(card.title)}</div>
                        </div>

                        <div class="card-body-area text-center">
                            <div class="card-amount-hero">
                                <span class="amount-currency">₦</span>
                                <span class="amount-value">${parseFloat(card.amount).toLocaleString('en-US')}</span>
                            </div>
                            <p class="card-message m-0">
                                ${escapeHtml(card.message || 'Wishing you all the best and a fantastic day!')}
                            </p>
                        </div>

                        <div class="card-bottom-bar">
                            <div class="d-flex flex-column gap-1">
                                <div class="card-brand opacity-90" style="font-size: 0.75rem;"><i class="ti ti-wallet"></i> Arewa Smart Idea Ltd</div>
                                <div class="card-brand opacity-75" style="font-size: 0.65rem; letter-spacing: 0.3px;"><i class="ti ti-world"></i> arewasmart.com.ng</div>
                                <div class="card-token">
                                    <span class="getTokenDisplay" style="font-size: 0.95rem;">
                                        ${card.status === 'used' 
                                            ? '<span class="badge bg-secondary-transparent text-secondary px-3 py-1 rounded-pill fw-bold">CLAIMED</span>' 
                                            : `<code class="bg-light p-1 px-2 rounded text-primary fw-bold" style="letter-spacing: 0.5px;">${card.token || 'Loading...'}</code>`}
                                    </span>
                                </div>
                            </div>
                            <div class="card-qr-box" id="downloadModalQR" style="${card.status === 'used' ? 'display:none' : ''}"></div>
                        </div>
                    </div>
                `;

                if (card.token) {
                    new QRCode(document.getElementById('downloadModalQR'), {
                        text: card.token,
                        width: 60,
                        height: 60,
                        colorDark: '#000000',
                        colorLight: '#ffffff',
                        correctLevel: QRCode.CorrectLevel.H
                    });
                }
            }

            // Escape HTML to prevent XSS
            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, function(m) {
                    if (m === '&') return '&amp;';
                    if (m === '<') return '&lt;';
                    if (m === '>') return '&gt;';
                    return m;
                });
            }

            // Fetch Card Token
            async function fetchCardToken(cardId) {
                if (currentCard && currentCard.status === 'used') {
                    return 'CLAIMED';
                }
                try {
                    const response = await fetch(`/gift-cards/code/${cardId}`);
                    const data = await response.json();
                    if (data.token) {
                        return data.token;
                    }
                    if (data.error === 'CLAIMED') {
                        return 'CLAIMED';
                    }
                    throw new Error(data.error || 'Failed to fetch code');
                } catch (error) {
                    console.error('Error fetching gift card code:', error);
                    return 'UNAVAILABLE';
                }
            }

            // Download Card
            function downloadCurrentCard() {
                if (!currentCard) return;
                const cardEl = document.getElementById('currentPreviewCard');
                Swal.fire({
                    title: 'Preparing Download',
                    text: 'Generating your high-quality card image...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                html2canvas(cardEl, {
                    scale: 3,
                    useCORS: true,
                    backgroundColor: null,
                    logging: false,
                }).then(canvas => {
                    const link = document.createElement('a');
                    link.download = `ArewaGift-${currentCard.title.replace(/\s+/g, '-')}-${Date.now()}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                    Swal.close();
                }).catch(() => {
                    Swal.fire('Error', 'Failed to generate image', 'error');
                });
            }

            // Share Card
            function shareCurrentCard() {
                if (!currentCard) return;
                const cardEl = document.getElementById('currentPreviewCard');
                const text = `🎁 Gift for you: ${currentCard.title}\n💰 Value: ₦${parseFloat(currentCard.amount).toLocaleString()}\n🔑 Code: ${currentCard.token || 'Check card image'}`;

                if (navigator.share) {
                    Swal.fire({
                        title: 'Preparing Share',
                        text: 'Generating card image...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    html2canvas(cardEl, { scale: 2, useCORS: true, backgroundColor: null })
                        .then(canvas => {
                            canvas.toBlob(blob => {
                                const file = new File([blob], 'gift-card.png', { type: 'image/png' });
                                navigator.share({
                                    title: 'Arewa Smart Gift Card',
                                    text: text,
                                    files: [file]
                                }).then(() => Swal.close())
                                  .catch(() => {
                                      Swal.close();
                                      navigator.clipboard.writeText(text);
                                      Swal.fire('Copied', 'Share failed, but card details copied to clipboard.', 'success');
                                  });
                            });
                        });
                } else {
                    navigator.clipboard.writeText(text);
                    Swal.fire('Copied', 'Share not supported. Card details copied to clipboard.', 'info');
                }
            }

            // Event Listeners for Download Buttons
            document.querySelectorAll('.download-card-btn').forEach(function (button) {
                button.addEventListener('click', async function () {
                    const cardData = JSON.parse(this.dataset.card);
                    currentCard = cardData;
                    renderCardPreview(currentCard);
                    modalInstance.show();

                    if (!currentCard.token) {
                        const token = await fetchCardToken(currentCard.id);
                        currentCard.token = token;
                        renderCardPreview(currentCard);
                    }
                });
            });

            // Event Listeners for Share Buttons
            document.querySelectorAll('.share-card-row-btn').forEach(function (button) {
                button.addEventListener('click', async function () {
                    const cardData = JSON.parse(this.dataset.card);
                    currentCard = cardData;

                    if (!currentCard.token) {
                        Swal.fire({
                            title: 'Authenticating',
                            text: 'Retrieving secure gift card code...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        currentCard.token = await fetchCardToken(currentCard.id);
                        Swal.close();
                    }

                    renderCardPreview(currentCard);
                    shareCurrentCard();
                });
            });

            downloadButton.addEventListener('click', downloadCurrentCard);
            shareButton.addEventListener('click', shareCurrentCard);

            // ── Redemption Modal Logic ───────────────────────────────────────
            const redeemModal = document.getElementById('redeemGiftCardModal');
            const redeemCodeInput = document.getElementById('redeem_code');
            const modalStartScanBtn = document.getElementById('modalStartScanBtn');
            const modalStopScanBtn = document.getElementById('modalStopScanBtn');
            const qrReader = document.getElementById('qr-reader');
            let html5QrcodeScanner = null;

            function formatCode(raw) {
                raw = raw.replace(/-/g, '').toUpperCase().slice(0, 15);
                let parts = [];
                if (raw.length > 0) parts.push(raw.slice(0, 5));
                if (raw.length > 5) parts.push(raw.slice(5, 10));
                if (raw.length > 10) parts.push(raw.slice(10, 15));
                return parts.join('-');
            }

            redeemCodeInput.addEventListener('input', function () {
                const pos = this.selectionStart;
                const raw = this.value.replace(/-/g, '').toUpperCase();
                const formatted = formatCode(raw);
                this.value = formatted;
                const newPos = Math.min(pos + (formatted.length - this.value.length + 1), formatted.length);
                try { this.setSelectionRange(newPos, newPos); } catch (e) {}
            });

            redeemCodeInput.addEventListener('keydown', function (e) {
                if (e.key.length === 1 && e.key !== '-' && !/[a-zA-Z0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });

            function startModalScanner() {
                modalStartScanBtn.style.display = 'none';
                qrReader.style.display = 'block';
                modalStopScanBtn.style.display = 'inline-flex';

                html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-reader",
                    { fps: 15, qrbox: { width: 220, height: 220 } },
                    false
                );

                html5QrcodeScanner.render((decodedText) => {
                    redeemCodeInput.value = formatCode(decodedText);
                    stopModalScanner();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Code scanned successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }, (error) => {});
            }

            function stopModalScanner() {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear().then(() => {
                        qrReader.style.display = 'none';
                        modalStopScanBtn.style.display = 'none';
                        modalStartScanBtn.style.display = 'inline-flex';
                    });
                }
            }

            modalStartScanBtn.addEventListener('click', startModalScanner);
            modalStopScanBtn.addEventListener('click', stopModalScanner);

            redeemModal.addEventListener('hidden.bs.modal', function () {
                stopModalScanner();
                redeemCodeInput.value = '';
            });

            document.getElementById('redeemForm').addEventListener('submit', function () {
                const btn = document.getElementById('redeemSubmitBtn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
            });
        });
    </script>

    @if((session('showRedeemModal') ?? false) || (isset($showRedeemModal) && $showRedeemModal))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const redeemModal = new bootstrap.Modal(document.getElementById('redeemGiftCardModal'));
                redeemModal.show();
            });
        </script>
    @endif
</x-app-layout>
<!-- Financial Metrics Section -->
<div class="container-fluid mb-2">
    <div class="row g-2 mt-2">
        <!-- Total Transactions (Debit) -->
        <div class="col-6 col-md-3 fade-in-up" style="animation-delay: 0.1s;">
            <div class="financial-card-sm shadow-sm h-100 p-2 d-flex align-items-center" style="background: #fff; border-left: 4px solid #e74a3b;">
                <div class="icon-box-sm bg-danger-subtle text-danger rounded-3 me-2 d-flex align-items-center justify-content-center">
                    <i class="bi bi-cart-dash fs-15"></i>
                </div>
                <div>
                    <p class="stats-label-sm mb-0 text-muted">Transactions</p>
                    <h6 class="stats-value-sm mb-0 text-dark fw-bold">₦{{ number_format($totalTransactionAmount ?? 0, 0) }}</h6>
                </div>
            </div>
        </div>

        <!-- Total Funded (Credit) -->
        <div class="col-6 col-md-3 fade-in-up" style="animation-delay: 0.2s;">
            <div class="financial-card-sm shadow-sm h-100 p-2 d-flex align-items-center" style="background: #fff; border-left: 4px solid #1cc88a;">
                <div class="icon-box-sm bg-success-subtle text-success rounded-3 me-2 d-flex align-items-center justify-content-center">
                    <i class="bi bi-wallet2 fs-15"></i>
                </div>
                <div>
                    <p class="stats-label-sm mb-0 text-muted">Funded</p>
                    <h6 class="stats-value-sm mb-0 text-dark fw-bold">₦{{ number_format($totalFundedAmount ?? 0, 0) }}</h6>
                </div>
            </div>
        </div>

        <!-- Agency Requests -->
        <div class="col-6 col-md-3 fade-in-up" style="animation-delay: 0.3s;">
            <div class="financial-card-sm shadow-sm h-100 p-2 d-flex align-items-center" style="background: #fff; border-left: 4px solid #4e73df;">
                <div class="icon-box-sm bg-primary-subtle text-primary rounded-3 me-2 d-flex align-items-center justify-content-center">
                    <i class="bi bi-bank fs-15"></i>
                </div>
                <div>
                    <p class="stats-label-sm mb-0 text-muted">Agency</p>
                    <h6 class="stats-value-sm mb-0 text-dark fw-bold">{{ number_format($totalAgencyRequests ?? 0) }}</h6>
                </div>
            </div>
        </div>

        <!-- Referral Earnings -->
        <div class="col-6 col-md-3 fade-in-up" style="animation-delay: 0.4s;">
            <div class="financial-card-sm shadow-sm h-100 p-2 d-flex align-items-center" style="background: #fff; border-left: 4px solid #36b9cc;">
                <div class="icon-box-sm bg-info-subtle text-info rounded-3 me-2 d-flex align-items-center justify-content-center">
                    <i class="bi bi-people fs-15"></i>
                </div>
                <div>
                    <p class="stats-label-sm mb-0 text-muted">Referrals</p>
                    <h6 class="stats-value-sm mb-0 text-dark fw-bold">₦{{ number_format($totalReferralEarnings ?? 0, 0) }}</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .financial-card-sm {
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .financial-card-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
    }

    .icon-box-sm {
        width: 38px;
        height: 38px;
        min-width: 38px;
    }

    .stats-label-sm {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        line-height: 1;
        margin-bottom: 2px !important;
    }

    .stats-value-sm {
        font-size: 0.95rem;
        letter-spacing: -0.3px;
    }

    .bg-primary-subtle { background-color: rgba(78, 115, 223, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(28, 200, 138, 0.1) !important; }
    .bg-danger-subtle { background-color: rgba(231, 74, 59, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(54, 185, 204, 0.1) !important; }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.4s ease forwards;
        opacity: 0;
    }
    
    @media (max-width: 576px) {
        .stats-value-sm {
            font-size: 0.85rem;
        }
    }
</style>

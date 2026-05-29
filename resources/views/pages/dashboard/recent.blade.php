{{-- Recent Transactions (Last 3) --}}
<div class="card recent-tx-card mb-3">

    {{-- Header --}}
    <div class="card-header recent-tx-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="recent-tx-icon-wrap">
                <i class="ti ti-clock-hour-4"></i>
            </div>
            <h6 class="mb-0 fw-bold text-dark">Recent Transactions</h6>
        </div>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 small fw-semibold">
            Last 3
        </span>
    </div>

    {{-- Body --}}
    <div class="card-body p-0">

        @php $latest = $recentTransactions->take(3); @endphp

        @if($latest->isEmpty())
            {{-- Empty State --}}
            <div class="recent-tx-empty py-5 text-center">
                <div class="recent-tx-empty-icon mb-3">
                    <i class="ti ti-receipt-off"></i>
                </div>
                <p class="text-muted mb-0 small fw-medium">No transactions yet.</p>
                <p class="text-muted small">Your recent activity will appear here.</p>
            </div>
        @else
            <ul class="list-unstyled mb-0 recent-tx-list">
                @foreach($latest as $tx)
                    @php
                        $isCredit = in_array($tx->type, ['credit', 'refund', 'bonus', 'manual_credit']);
                        $isDebit  = in_array($tx->type, ['debit', 'manual_debit']);
                        $amountPrefix = $isCredit ? '+' : ($isDebit ? '-' : '');
                        $amountClass  = $isCredit ? 'text-success' : ($isDebit ? 'text-danger' : 'text-info');
                        $iconClass    = $isCredit ? 'ti-arrow-down-left text-success' : ($isDebit ? 'ti-arrow-up-right text-danger' : 'ti-transfer-in text-info');
                        $bgClass      = $isCredit ? 'bg-success' : ($isDebit ? 'bg-danger' : 'bg-info');
                        $badgeBg      = $tx->status == 'completed' || $tx->status == 'successful'
                                            ? 'bg-success-subtle text-success'
                                            : ($tx->status == 'pending' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger');
                        $badgeLabel   = $tx->status == 'completed' || $tx->status == 'successful'
                                            ? 'Success'
                                            : ($tx->status == 'pending' ? 'Pending' : 'Failed');

                        $typeLabel    = match($tx->type) {
                            'manual_credit' => 'Credit',
                            'manual_debit'  => 'Debit',
                            default         => ucfirst($tx->type),
                        };
                    @endphp

                    <li class="recent-tx-item d-flex align-items-center gap-3 px-4 py-3">

                        {{-- Type Icon --}}
                        <div class="recent-tx-type-icon {{ $bgClass }} bg-opacity-10 flex-shrink-0">
                            <i class="ti {{ $iconClass }}"></i>
                        </div>

                        {{-- Description --}}
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="mb-0 fw-semibold text-dark small text-truncate">
                                {{ $typeLabel }}
                                <span class="text-muted fw-normal">&middot; #{{ substr($tx->transaction_ref, 0, 10) }}...</span>
                            </p>
                            <span class="text-muted" style="font-size: 11px;">
                                {{ $tx->created_at->format('d M Y, h:i A') }}
                            </span>
                        </div>

                        {{-- Amount & Status --}}
                        <div class="text-end flex-shrink-0">
                            <p class="mb-1 fw-bold small {{ $amountClass }}">
                                {{ $amountPrefix }}₦{{ number_format($tx->amount, 2) }}
                            </p>
                            <span class="badge rounded-pill px-2 py-1 {{ $badgeBg }}" style="font-size: 10px;">
                                {{ $badgeLabel }}
                            </span>
                        </div>

                    </li>
                @endforeach
            </ul>
        @endif

    </div>

    {{-- Footer — View All Button --}}
    <div class="card-footer recent-tx-footer text-center">
        <a href="{{ route('transactions') }}" class="btn btn-primary w-100">
            <i class="ti ti-list-details me-2"></i>View All Transactions
            <i class="ti ti-arrow-right ms-2"></i>
        </a>
    </div>

</div>

@push('styles')
<style>
    /* ─── Recent Transactions Card ─── */
    .recent-tx-card {
        border-radius: 22px !important;
        border: 1px solid rgba(0,0,0,0.06) !important;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06) !important;
        overflow: hidden;
        background: #fff;
    }

    .recent-tx-header {
        background: #fff !important;
        border-bottom: 1px solid #f3f4f6 !important;
        padding: 16px 20px !important;
    }

    .recent-tx-icon-wrap {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(99,102,241,0.12), rgba(99,102,241,0.06));
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6366f1;
        font-size: 1rem;
    }

    /* ─── List Items ─── */
    .recent-tx-list li.recent-tx-item {
        border-bottom: 1px solid #f9fafb;
        transition: background 0.18s ease;
    }
    .recent-tx-list li.recent-tx-item:last-child {
        border-bottom: none;
    }
    .recent-tx-list li.recent-tx-item:hover {
        background: #f9fafb;
    }

    /* ─── Type Icon ─── */
    .recent-tx-type-icon {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    /* ─── Empty State ─── */
    .recent-tx-empty-icon {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: #9ca3af;
        margin: 0 auto;
    }

    /* ─── Footer ─── */
    .recent-tx-footer {
        background: #fafafa !important;
        border-top: 1px solid #f3f4f6 !important;
        padding: 14px 20px !important;
    }

    /* ─── View All Button ─── */
    .btn-view-all {
        background: linear-gradient(135deg, #6366f1, #818cf8);
        color: #fff !important;
        border: none;
        border-radius: 14px;
        padding: 10px 20px;
        font-weight: 600;
        font-size: 13px;
        letter-spacing: 0.2px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px rgba(99,102,241,0.25);
    }
    .btn-view-all:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99,102,241,0.35);
        background: linear-gradient(135deg, #4f52e0, #6366f1);
        color: #fff !important;
    }
    .btn-view-all:active {
        transform: translateY(0);
    }

    /* ─── Dark Mode Overrides ─── */
    [data-theme="dark"] .recent-tx-card {
        background: #0f172a !important;
        border: 1px solid rgba(255, 255, 255, 0.05) !important;
        box-shadow: none !important;
    }
    [data-theme="dark"] .recent-tx-header {
        background: #0f172a !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
    }
    [data-theme="dark"] .recent-tx-header h6,
    [data-theme="dark"] .recent-tx-card .text-dark {
        color: #f1f5f9 !important;
    }
    [data-theme="dark"] .recent-tx-list li.recent-tx-item {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
    }
    [data-theme="dark"] .recent-tx-list li.recent-tx-item:hover {
        background: rgba(255, 255, 255, 0.03) !important;
    }
    [data-theme="dark"] .recent-tx-footer {
        background: #0f172a !important;
        border-top: 1px solid rgba(255, 255, 255, 0.05) !important;
    }
    [data-theme="dark"] .recent-tx-empty-icon {
        background: rgba(255, 255, 255, 0.05) !important;
    }
</style>
@endpush

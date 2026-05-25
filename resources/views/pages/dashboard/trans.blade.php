<!-- Recent Transactions -->
<div class="row g-4">
    <div class="col-12 col-xl-7 d-flex">
        <div class="card flex-fill border-0 shadow-lg overflow-hidden dashboard-card" style="border-radius: 30px !important;">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap border-bottom-0">
                <h5 class="mb-0 fw-bold text-dark">Recent Transactions</h5>
                <div class="d-flex align-items-center">
                     <a href="{{ route('transactions') }}" class="btn btn-sm btn-light text-primary fw-medium">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">  
                    <table class="table table-hover table-nowrap mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-secondary small fw-semibold ps-4">#</th>
                                <th class="text-secondary small fw-semibold">Ref ID</th>
                                <th class="text-secondary small fw-semibold">Type</th>
                                <th class="text-secondary small fw-semibold">Amount</th>
                                <th class="text-secondary small fw-semibold">Date</th>
                                <th class="text-secondary small fw-semibold pe-4 text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                            <tr>
                                <td class="ps-4">
                                    <span class="text-muted small">{{ $loop->iteration }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark">#{{ substr($transaction->transaction_ref, 0, 8) }}...</span>
                                </td>
                                <td>
                                    @if(in_array($transaction->type, ['credit', 'refund', 'bonus', 'manual_credit']))
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                            <i class="ti ti-arrow-down-left me-1"></i>{{ $transaction->type == 'manual_credit' ? 'Credit' : ucfirst($transaction->type) }}
                                        </span>
                                    @elseif(in_array($transaction->type, ['debit', 'manual_debit']))
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1">
                                            <i class="ti ti-arrow-up-right me-1"></i>{{ $transaction->type == 'manual_debit' ? 'Debit' : 'Debit' }}
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    @endif
                                </td>
                               
                                <td>
                                    <span class="fw-bold {{ in_array($transaction->type, ['credit', 'refund', 'bonus', 'manual_credit']) ? 'text-success' : 'text-danger' }}">
                                        {{ in_array($transaction->type, ['credit', 'refund', 'bonus', 'manual_credit']) ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $transaction->created_at->format('d M Y') }}</span>
                                </td>
                                <td class="pe-4 text-end">
                                    @if($transaction->status == 'completed' || $transaction->status == 'successful')
                                        <span class="badge bg-success text-white rounded-pill px-3">Success</span>
                                    @elseif($transaction->status == 'pending')
                                        <span class="badge bg-warning text-white rounded-pill px-3">Pending</span>
                                    @else
                                        <span class="badge bg-danger text-white rounded-pill px-3">Failed</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ti ti-receipt-off fs-1 text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No recent transactions found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Statistics -->
    <div class="col-12 col-xl-5 d-none d-lg-flex">
        <div class="card flex-fill border-0 shadow-lg overflow-hidden dashboard-card" style="border-radius: 30px !important;">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h5 class="mb-0 fw-bold text-primary">Transaction Statistics</h5>
            </div>

            <div class="card-body">
                <div class="position-relative mb-4 d-flex justify-content-center">
                    <div style="height: 200px; width: 200px;">
                        <canvas id="transactionChart"></canvas>
                    </div>
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                        <p class="fs-12 text-muted mb-0">Total</p>
                        <h3 class="fw-bold text-dark mb-0">{{ $totalTransactions }}</h3>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-4">
                        <div class="p-3 rounded-3 bg-success-subtle text-center h-100">
                            <i class="ti ti-circle-check-filled fs-4 text-success mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">{{ $completedPercentage }}%</h6>
                            <span class="fs-11 text-muted text-uppercase fw-semibold">Success</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-3 bg-warning-subtle text-center h-100">
                            <i class="ti ti-clock-filled fs-4 text-warning mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">{{ $pendingPercentage }}%</h6>
                            <span class="fs-11 text-muted text-uppercase fw-semibold">Pending</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-3 bg-danger-subtle text-center h-100">
                            <i class="ti ti-circle-x-filled fs-4 text-danger mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">{{ $failedPercentage }}%</h6>
                            <span class="fs-11 text-muted text-uppercase fw-semibold">Failed</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold text-primary mb-1">₦{{ number_format($totalTransactionAmount, 2) }}</h5>
                        <p class="fs-12 text-muted mb-0">Total Spent This Month</p>
                    </div>
                    <a href="{{ route('transactions') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                        View Report <i class="ti ti-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('transactionChart').getContext('2d');
        var transactionChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Success', 'Pending', 'Failed'],
                datasets: [{
                    data: [{{ $completedTransactions }}, {{ $pendingTransactions }}, {{ $failedTransactions }}],
                    backgroundColor: [
                        '#28a745', // Success - Green
                        '#ffc107', // Pending - Yellow
                        '#dc3545'  // Failed - Red
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                cutout: '75%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                }
            }
        });
    });
</script>

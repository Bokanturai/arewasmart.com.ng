<div class="ai-receipt-card bg-white rounded-3 shadow-sm border overflow-hidden mt-2" style="max-width: 300px; font-family: 'Nunito Sans', sans-serif;">
    <div class="card-header bg-primary text-white text-center py-2" style="border-radius: 0;">
        <div class="mb-1">
            <i class="bi bi-check-circle-fill fs-20"></i>
        </div>
        <div class="fw-bold small">Transaction Successful</div>
    </div>
    
    <div class="card-body p-3">
        <div class="text-center mb-3">
            <div class="text-muted small text-uppercase fw-bold" style="font-size: 10px;">Amount Paid</div>
            <div class="fs-14 fw-extrabold text-dark">₦{{ number_format($paid, 2) }}</div>
            @if(isset($amount) && $amount > $paid)
                <div class="badge bg-success-subtle text-success extra-small rounded-pill px-2 py-1 mt-1" style="font-size: 9px;">
                    Saved ₦{{ number_format($amount - $paid, 2) }}
                </div>
            @endif
        </div>

        <div class="receipt-details border-top pt-2">
            <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                <span class="text-muted">Service:</span>
                <span class="fw-bold text-dark">{{ $serviceName }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                <span class="text-muted">Recipient:</span>
                <span class="fw-bold text-dark text-end">
                    {{ $mobile }}
                    @if($receiverName)
                        <div style="font-size: 9px; font-weight: normal; color: #6c757d;">{{ $receiverName }}</div>
                    @endif
                </span>
            </div>

            <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                <span class="text-muted">Date:</span>
                <span class="text-dark">{{ \Carbon\Carbon::parse($date)->format('d M, h:i A') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1" style="font-size: 11px;">
                <span class="text-muted">Ref:</span>
                <span class="text-dark">#{{ substr($ref, -8) }}</span>
            </div>
        </div>

        <div class="d-grid gap-2 mt-3">
            <button onclick="downloadChatReceipt('{{ $ref }}')" class="btn btn-sm btn-primary py-2 rounded-3 shadow-sm fw-bold" style="font-size: 11px;">
                <i class="bi bi-download me-1"></i> Download PDF
            </button>
            <button onclick="shareChatReceipt('{{ $ref }}')" class="btn btn-sm btn-outline-dark py-2 rounded-3 fw-bold" style="font-size: 11px;">
                <i class="bi bi-share me-1"></i> Share Slip
            </button>
        </div>
    </div>
    <div class="bg-primary py-1 w-100"></div>
</div>

<style>
    .fw-extrabold { font-weight: 800; }
    .extra-small { font-size: 0.75rem; }
    .ai-receipt-card { animation: fadeInScale 0.3s ease-out; }
    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

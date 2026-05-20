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

        {{-- Educational PIN & Serial block --}}
        @if(!empty($token) && $token !== 'Check History')
        <div class="mt-2 rounded-3 overflow-hidden border" style="border-color: #4f46e5 !important;">
            <div class="px-2 py-1 d-flex align-items-center gap-1" style="background: linear-gradient(90deg,#4f46e5,#7c3aed);">
                <i class="bi bi-shield-lock-fill text-white" style="font-size: 0.7rem;"></i>
                <span class="text-white fw-bold" style="font-size: 9px; letter-spacing: 0.05em;">{{ strtoupper($network ?? 'EXAM') }} PIN</span>
            </div>
            <div class="px-2 py-2" style="background: #f0f0ff;">
                <div style="font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">PIN / Access Code</div>
                <div class="font-monospace fw-bold text-dark" style="font-size: 13px; letter-spacing: 1.5px; word-break: break-all;">{{ $token }}</div>
                @if(!empty($serial))
                <div class="mt-1 pt-1 border-top" style="border-color: #d1d5db !important;">
                    <div style="font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Serial Number</div>
                    <div class="font-monospace text-secondary fw-semibold" style="font-size: 11px; letter-spacing: 1px; word-break: break-all;">{{ $serial }}</div>
                </div>
                @else
                <div class="mt-1" style="font-size: 9px; color: #9ca3af; font-style: italic;">Serial not provided by provider.</div>
                @endif
            </div>
        </div>
        @endif

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

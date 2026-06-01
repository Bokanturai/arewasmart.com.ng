<x-app-layout>
    <title>Arewa Smart - Transaction Receipt</title>

    @push('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        /* Minimal custom styling for specialized receipt features */
        .receipt-card {
            max-width: 420px;
            width: 100%;
        }
        @media print {
            .no-print { display: none !important; }
            .receipt-card { 
                box-shadow: none !important; 
                border: 1px solid #dee2e6 !important;
                margin: 0 auto;
            }
        }
        .success-icon-bg {
            width: 80px;
            height: 80px;
            background-color: rgba(25, 135, 84, 0.1);
        }
    </style>
    @endpush

    @php
        $discount = $amount - $paid;

        // Detect educational PIN purchase (WAEC, NECO, NABTED, JAMB)
        $eduKeywords = ['waec', 'neco', 'nabted', 'nabteb', 'jamb'];
        $networkLower = strtolower($network ?? '');
        $serviceNameLower = strtolower($serviceName ?? '');
        $isEduPin = $token && (
            collect($eduKeywords)->contains(fn($k) => str_contains($networkLower, $k) || str_contains($serviceNameLower, $k))
        );

        // $serial is passed from session or DB metadata (may be null)
        $serial = $serial ?? null;
    @endphp

    <div class="container-fluid px-0 px-md-3 py-3 py-sm-5 d-flex flex-column align-items-center bg-light min-vh-100">
        <!-- Navigation -->
        <div class="receipt-card mb-3 no-print">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show text-center py-2 mb-3 shadow-sm border-0" role="alert">
                    <small><i class="bi bi-check-circle-fill me-2"></i>{!! session('success') !!}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show text-center py-2 mb-3 shadow-sm border-0" role="alert">
                    <small><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <a href="{{ route('dashboard') }}" class="text-secondary text-decoration-none small d-flex align-items-center fw-semibold transition-all hover-translate-x">
                <i class="bi bi-arrow-left me-2"></i>Dashboard
            </a>
        </div>

        <!-- Receipt Slip -->
        <div class="card border-0 shadow-lg rounded-0 rounded-md-4 overflow-hidden receipt-card mx-auto" id="receipt-capture">
            <!-- Header Section -->
            <div class="card-header bg-white border-0 text-center pt-4 pt-sm-5 pb-3 pb-sm-4 rounded-0 rounded-top-md-4">
                <div class="success-icon-bg rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 70px; height: 70px;">
                    <i class="bi bi-check2-circle text-success fs-1"></i>
                </div>
                <h6 class="text-success fw-bold text-uppercase ls-wide mb-1" style="font-size: 0.7rem;">Transaction Successful</h6>
                <h4 class="fw-extrabold text-primary mb-1 fs-15 fs-sm-4">Arewa Smart Idea</h4>
                <p class="text-muted small mb-0 font-monospace" style="font-size: 0.75rem;">Ref: #{{ $ref }}</p>
            </div>

            <!-- Body Section -->
            <div class="card-body px-3 px-sm-4 pt-0">
                <div class="list-group list-group-flush border-top border-bottom py-3 mb-4">
                    <div class="list-group-item d-flex justify-content-between border-0 px-0 py-2">
                        <span class="text-secondary small fw-medium">Date & Time</span>
                        <span class="text-dark small fw-bold">{{ \Carbon\Carbon::parse($date)->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between border-0 px-0 py-2">
                        <span class="text-secondary small fw-medium">Customer</span>
                        <span class="text-dark small fw-bold text-end">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between border-0 px-0 py-2">
                        <span class="text-secondary small fw-medium">Service Type</span>
                        <span class="text-dark small fw-bold">{{ $serviceName }}</span>
                    </div>
                    @if($receiverName)
                    <div class="list-group-item d-flex justify-content-between border-0 px-0 py-2">
                        <span class="text-secondary small fw-medium">Beneficiary</span>
                        <span class="text-dark small fw-bold text-end">{{ $receiverName }}</span>
                    </div>
                    @endif
                    <div class="list-group-item d-flex justify-content-between border-0 px-0 py-2">
                        <span class="text-secondary small fw-medium">{{ str_contains(strtolower($serviceName), 'withdrawal') ? 'Recipient Bank' : 'Network/Provider' }}</span>
                        <span class="badge bg-primary-subtle text-primary border-0 rounded-pill px-3">{{ strtoupper($network) }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between border-0 px-0 py-2">
                        <span class="text-secondary small fw-medium">{{ str_contains(strtolower($serviceName), 'withdrawal') ? 'Account Number' : 'Phone/Account' }}</span>
                        <span class="text-dark small fw-bold">{{ $mobile }}</span>
                    </div>
                    @if(str_contains(strtolower($serviceName), 'withdrawal') && (($fee ?? 0) > 0 || ($tax ?? 0) > 0))
                    <div class="list-group-item d-flex justify-content-between border-0 px-0 py-2">
                        <span class="text-secondary small fw-medium">Payout Amount</span>
                        <span class="text-dark small fw-bold">₦{{ number_format($amount - $fee, 2) }}</span>
                    </div>
                    @if(($fee ?? 0) > 0)
                    <div class="list-group-item d-flex justify-content-between border-0 px-0 py-2">
                        <span class="text-secondary small fw-medium">Service Fee</span>
                        <span class="text-dark small fw-bold">₦{{ number_format($fee, 2) }}</span>
                    </div>
                    @endif
                    @if(($tax ?? 0) > 0)
                    <div class="list-group-item d-flex justify-content-between border-0 px-0 py-2">
                        <span class="text-secondary small fw-medium">Government Tax</span>
                        <span class="text-dark small fw-bold">₦{{ number_format($tax, 2) }}</span>
                    </div>
                    @endif
                    @endif
                </div>

                {{-- ── Educational PIN Section (WAEC / NECO / NABTED / JAMB) ── --}}
                @if($isEduPin)
                <div class="mb-4">
                    {{-- PIN Card --}}
                    <div class="edu-pin-card rounded-4 p-0 overflow-hidden border border-2" style="border-color: #4f46e5 !important; background: linear-gradient(135deg, #eef2ff 0%, #f0fdf4 100%);">
                        
                        {{-- Card Header --}}
                        <div class="d-flex align-items-center gap-2 px-3 py-2" style="background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);">
                            <i class="bi bi-shield-lock-fill text-white" style="font-size: 1rem;"></i>
                            <span class="text-white fw-bold small text-uppercase ls-wider">{{ strtoupper($network) }} Examination PIN</span>
                        </div>

                        {{-- PIN Row --}}
                        <div class="px-3 py-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted extra-small fw-semibold text-uppercase" style="letter-spacing: 0.06em;">
                                    <i class="bi bi-key-fill me-1 text-indigo"></i>PIN / Access Code
                                </span>
                                <button 
                                    onclick="copyToClipboard('{{ $token }}', 'pin-copied-badge')" 
                                    class="btn btn-sm px-2 py-0 rounded-2 border-0 no-print"
                                    id="copy-pin-btn"
                                    style="background: rgba(79,70,229,0.1); color: #4f46e5; font-size: 0.7rem;"
                                    title="Copy PIN">
                                    <i class="bi bi-clipboard me-1"></i>Copy
                                </button>
                            </div>
                            <div class="pin-display d-flex align-items-center gap-2 rounded-3 p-2" style="background: rgba(79,70,229,0.07);">
                                <span class="font-monospace fw-extrabold text-dark flex-grow-1" style="font-size: 1.18rem; letter-spacing: 2px; word-break: break-all;" id="edu-pin-value">{{ $token }}</span>
                                <span class="badge text-success border border-success-subtle rounded-pill extra-small d-none" id="pin-copied-badge" style="background: rgba(22,163,74,0.1);">
                                    <i class="bi bi-check2 me-1"></i>Copied!
                                </span>
                            </div>
                        </div>

                        {{-- Serial Number Row (shown only if serial exists) --}}
                        @if($serial)
                        <div class="px-3 pb-3 pt-0">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted extra-small fw-semibold text-uppercase" style="letter-spacing: 0.06em;">
                                    <i class="bi bi-hash me-1 text-secondary"></i>Serial Number
                                </span>
                                <button 
                                    onclick="copyToClipboard('{{ $serial }}', 'serial-copied-badge')" 
                                    class="btn btn-sm px-2 py-0 rounded-2 border-0 no-print"
                                    id="copy-serial-btn"
                                    style="background: rgba(100,116,139,0.12); color: #475569; font-size: 0.7rem;"
                                    title="Copy Serial Number">
                                    <i class="bi bi-clipboard me-1"></i>Copy
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-2 rounded-3 p-2" style="background: rgba(100,116,139,0.07);">
                                <span class="font-monospace fw-bold text-secondary flex-grow-1" style="font-size: 0.95rem; letter-spacing: 1.5px; word-break: break-all;" id="edu-serial-value">{{ $serial }}</span>
                                <span class="badge text-success border border-success-subtle rounded-pill extra-small d-none" id="serial-copied-badge" style="background: rgba(22,163,74,0.1);">
                                    <i class="bi bi-check2 me-1"></i>Copied!
                                </span>
                            </div>
                        </div>

                        {{-- Combined Copy Row --}}
                        <div class="px-3 pb-3">
                            <button 
                                onclick="copyBoth('{{ $token }}', '{{ $serial }}')" 
                                class="btn w-100 py-2 rounded-3 fw-bold extra-small border-0 no-print d-flex align-items-center justify-content-center gap-2"
                                style="background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%); color: #fff;">
                                <i class="bi bi-clipboard2-check-fill"></i>Copy PIN + Serial
                            </button>
                        </div>
                        @else
                        {{-- No serial: show note and single copy button --}}
                        <div class="px-3 pb-3">
                            <p class="text-muted extra-small mb-2 text-center" style="font-size: 0.72rem;">
                                <i class="bi bi-info-circle me-1"></i>Serial number was not provided by the provider for this transaction.
                            </p>
                            <button 
                                onclick="copyToClipboard('{{ $token }}', 'pin-copied-badge')" 
                                class="btn w-100 py-2 rounded-3 fw-bold extra-small border-0 no-print d-flex align-items-center justify-content-center gap-2"
                                style="background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%); color: #fff;">
                                <i class="bi bi-clipboard-check-fill"></i>Copy PIN
                            </button>
                        </div>
                        @endif
                    </div>

                    {{-- Usage instruction --}}
                    <p class="text-muted text-center mt-2 mb-0" style="font-size: 0.72rem;">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                        Keep your PIN safe. Do not share it with anyone.
                    </p>
                </div>

                {{-- ── Generic Token Section (non-educational) ── --}}
                @elseif($token)
                <div class="bg-primary bg-opacity-10 rounded-4 p-3 text-center mb-4 border border-primary border-opacity-10 shadow-sm">
                    <span class="text-primary small fw-bold text-uppercase ls-wider d-block mb-1">Examination PIN / Token</span>
                    <div class="text-primary fs-12 fw-extrabold font-monospace letter-spacing-1">{{ $token }}</div>
                </div>
                @endif

                <!-- Amount Section -->
                <div class="bg-light rounded-4 p-3 p-sm-4 text-center border shadow-sm mb-4">
                    <span class="text-secondary small fw-semibold text-uppercase mb-1 d-block" style="font-size: 0.75rem;">Amount Charged</span>
                    <h2 class="fw-extrabold text-dark mb-0 fs-3 fs-sm-2">₦{{ number_format($paid, 2) }}</h2>
                    @if($discount > 0)
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 mt-2 extra-small border border-success border-opacity-10">
                            <i class="bi bi-lightning-fill me-1"></i>You saved ₦{{ number_format($discount, 2) }}
                        </span>
                    @endif
                </div>

                <!-- Footer Section & Actions -->
                <div class="no-print pb-3 pb-sm-4">
                    <!-- Action Buttons -->
                    <div class="d-grid gap-3 mt-4 no-print">
                        <button onclick="window.print()" class="btn btn-primary btn-lg rounded-pill shadow-sm fw-bold"> <i class="bi bi-printer me-2"></i> Print Receipt</button>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-house-door me-1"></i> Dashboard
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ url()->previous() }}" class="btn btn-outline-primary w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-plus-circle me-1"></i> Buy Another
                                </a>
                            </div>
                        </div>
                    </div>

                    <p class="text-center text-muted mt-4 small mb-0 no-print">
                        Thank you for choosing <strong>Arewa Smart</strong>.
                    </p>
                    
                    <div class="row g-2 mt-3">
                        <div class="col-6">
                            <button onclick="downloadPDF()" class="btn btn-outline-primary w-100 rounded-3 py-2 fw-bold extra-small shadow-sm d-flex align-items-center justify-content-center">
                                <i class="bi bi-download me-2"></i>Download PDF
                            </button>
                        </div>
                        <div class="col-6">
                            <button onclick="shareAsPDF()" class="btn btn-outline-dark w-100 rounded-3 py-2 fw-bold extra-small shadow-sm d-flex align-items-center justify-content-center">
                                <i class="bi bi-share me-2"></i>Share PDF
                            </button>
                        </div>
                        @if($token && !$isEduPin)
                        <div class="col-12 mt-2">
                            <button onclick="copyToClipboard('{{ $token }}', null)" class="btn btn-primary bg-gradient w-100 rounded-3 py-2 fw-bold extra-small shadow-sm mb-2 d-flex align-items-center justify-content-center border-0">
                                <i class="bi bi-clipboard-check me-2"></i>Copy PIN
                            </button>
                        </div>
                        @endif
                    </div>
                    <p class="text-muted text-center extra-small mb-0 px-2" style="line-height: 1.4; font-size: 0.7rem;">
                        Transaction complete. Thanks for choosing <span class="fw-bold text-dark">Arewa Smart Idea</span>.
                    </p>
                </div>
            </div>
            
            <!-- Bottom Design Element -->
            <div class="bg-primary py-1 w-100"></div>
        </div>
    </div>

    @push('scripts')
    <script>
        // AI Voice Notification
        window.addEventListener('load', () => {
            if ('speechSynthesis' in window) {
                const message = "Your purchase is successful and delivered. Thank you for using Arewa Smart Idea.";
                const utterance = new SpeechSynthesisUtterance(message);
                utterance.rate = 1.0;
                utterance.pitch = 1.1;
                window.speechSynthesis.speak(utterance);
            }
        });

        // Private helper to generate PDF using html2canvas & jsPDF
        async function generateReceiptPDF() {
            const { jsPDF } = window.jspdf;
            const receipt = document.getElementById('receipt-capture');
            const noPrintElements = receipt.querySelectorAll('.no-print');
            
            // Hide elements that shouldn't be in the snapshot
            noPrintElements.forEach(el => el.style.display = 'none');
            
            try {
                const canvas = await html2canvas(receipt, {
                    backgroundColor: '#f8f9fa',
                    scale: 2, 
                    logging: false,
                    useCORS: true,
                    onclone: (clonedDoc) => {
                        const clonedReceipt = clonedDoc.getElementById('receipt-capture');
                        clonedReceipt.classList.remove('shadow-lg');
                        clonedReceipt.style.border = '1px solid #eee';
                    }
                });
                
                const imgData = canvas.toDataURL('image/jpeg', 0.95);
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'px',
                    format: [canvas.width / 2, canvas.height / 2]
                });
                
                pdf.addImage(imgData, 'JPEG', 0, 0, canvas.width / 2, canvas.height / 2);
                return pdf;
            } finally {
                // Show elements back
                noPrintElements.forEach(el => el.style.display = 'block');
            }
        }

        // Direct PDF Download functionality
        async function downloadPDF() {
            try {
                const pdf = await generateReceiptPDF();
                const fileName = `ArewaSmart_Receipt_{{ $ref }}.pdf`;
                pdf.save(fileName);
            } catch (err) {
                console.error('Download failed:', err);
                alert('Download failed. Please try the Print button.');
            }
        }

        // Share as PDF functionality
        async function shareAsPDF() {
            try {
                const pdf = await generateReceiptPDF();
                const pdfBlob = pdf.output('blob');
                const fileName = `ArewaSmart_Receipt_{{ $ref }}.pdf`;
                const file = new File([pdfBlob], fileName, { type: 'application/pdf' });

                // Check if sharing is supported
                if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
                    await navigator.share({
                        files: [file],
                        title: 'Transaction Receipt',
                        text: 'Receipt for my transaction on Arewa Smart Idea.'
                    });
                } else {
                    // Fallback to download
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(pdfBlob);
                    link.download = fileName;
                    link.click();
                }
            } catch (err) {
                console.error('Sharing failed:', err);
                alert('Sharing failed. Please try the Print button.');
            }
        }

        /**
         * Copy text to clipboard and show a badge feedback element.
         * @param {string} text - Text to copy
         * @param {string|null} badgeId - ID of badge element to show temporarily, or null for alert
         */
        function copyToClipboard(text, badgeId) {
            const doCopy = () => {
                if (badgeId) {
                    const badge = document.getElementById(badgeId);
                    if (badge) {
                        badge.classList.remove('d-none');
                        setTimeout(() => badge.classList.add('d-none'), 2500);
                    }
                } else {
                    alert('PIN copied to clipboard successfully!');
                }
            };

            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(doCopy).catch(() => {
                    fallbackCopy(text);
                    doCopy();
                });
            } else {
                fallbackCopy(text);
                doCopy();
            }
        }

        /**
         * Copy both PIN and Serial Number together
         */
        function copyBoth(pin, serial) {
            const combined = `PIN: ${pin}\nSerial: ${serial}`;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(combined).then(() => {
                    alert('PIN and Serial Number copied to clipboard!');
                }).catch(() => {
                    fallbackCopy(combined);
                    alert('PIN and Serial Number copied to clipboard!');
                });
            } else {
                fallbackCopy(combined);
                alert('PIN and Serial Number copied to clipboard!');
            }
        }

        function fallbackCopy(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.opacity = '0';
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
            } catch (err) {
                console.warn('Fallback copy failed:', err);
            }
            document.body.removeChild(textArea);
        }
    </script>
    <style>
        .ls-wide { letter-spacing: 0.05em; }
        .ls-wider { letter-spacing: 0.1em; }
        .fw-extrabold { font-weight: 800; }
        .fs-extra-small { font-size: 0.75rem; }
        .extra-small { font-size: 0.8rem; }
        .transition-all { transition: all 0.2s ease; }
        .hover-translate-x:hover { transform: translateX(-4px); }
        .letter-spacing-1 { letter-spacing: 1px; }
        .text-indigo { color: #4f46e5; }

        /* Educational PIN card hover effect */
        .edu-pin-card {
            transition: box-shadow 0.2s ease;
        }
        .edu-pin-card:hover {
            box-shadow: 0 4px 24px rgba(79,70,229,0.13) !important;
        }

        /* PIN display pulse animation on load */
        @keyframes pinReveal {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .pin-display {
            animation: pinReveal 0.5s ease both;
        }
    </style>
    @endpush
</x-app-layout>

<x-app-layout>
    <title>Arewa Smart - NECO & NABTED PIN</title>

    @php
        // Statistics calculation
        $groupedHistory = $history->groupBy('network');
        $chartLabels = $groupedHistory->keys()->toArray();
        $chartValues = $groupedHistory->map(function($items) {
            return $items->sum('amount');
        })->values()->toArray();
    @endphp


    @push('styles')
    <style>
        .network-selection .network-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #eee;
            background: #fff;
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px 5px;
        }

        .network-selection .network-card:hover {
            transform: translateY(-3px);
            border-color: #0d6efd;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .network-selection .network-card.active {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }

        .network-selection .network-card.active::after {
            content: '✓';
            position: absolute;
            top: 5px;
            right: 5px;
            background: #0d6efd;
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .network-selection .network-card img {
            width: 45px;
            height: 45px;
            object-fit: contain;
            border-radius: 5px;
            margin-bottom: 8px;
        }

        /* Typography & Utilities */
        .fs-8 { font-size: 0.7rem; }
        .fs-9 { font-size: 0.75rem; }
        .fs-10 { font-size: 0.8rem; }
        .fs-11 { font-size: 0.85rem; }
        .fs-12 { font-size: 0.9rem; }
        .fs-13 { font-size: 0.95rem; }
        .fs-14 { font-size: 14px; }
        .fs-15 { font-size: 15px; }

        .spin { animation: fa-spin 2s infinite linear; }
        @keyframes fa-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(359deg); } }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .shadow-hover:hover { box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important; }
        .transition-all { transition: all 0.3s ease; }
        .btn-xs { padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 600; }
        .focus-within-shadow:focus-within { box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important; border-color: #0d6efd !important; }
        
        .animate-fade-in { animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8f9fa; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #d47508ff; border-radius: 10px; }
    </style>
    @endpush

    <div class="container-fluid px-0 px-md-3 mt-3">
        <div class="row g-0 g-md-4 justify-content-center">

            {{-- Left Column: Purchase Form --}}
            <div class="col-12 col-xl-5 mb-4">
                <div class="card custom-card shadow-lg border-0 rounded-20px d-flex flex-column">
                    <div class="card-header justify-content-between bg-primary text-white rounded-20px rounded-bottom-0">
                        <div class="card-title fw-semibold">
                            <i class="bi bi-mortarboard-fill me-2"></i> NECO & NABTED
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="text-center text-muted mb-4 small">
                            Select your exam, enter your phone number, and complete your pin purchase securely.
                        </p>

                        {{-- Alert Messages --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show text-center py-2 mb-3" role="alert">
                                <small>{!! session('success') !!}</small>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show text-center py-2 mb-3" role="alert">
                                <small>{{ session('error') }}</small>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form id="buy-pin-form" method="POST" action="{{ route('buy-neco-nabted') }}">
                            @csrf
                            
                            {{-- Exam Selection --}}
                            <div class="mb-4 network-selection">
                                <label class="form-label fw-semibold small">Select Exam Type</label>
                                <div class="row g-2 justify-content-center text-center">
                                    @foreach($variations as $variation)
                                        <div class="col-6">
                                            <div class="network-card rounded-3 shadow-sm border p-3 h-100 d-flex flex-column align-items-center justify-content-center transition-all bg-white" 
                                                 id="pkg-{{ $variation->field_code }}"
                                                 onclick="selectPackage('{{ $variation->field_code }}', '{{ $variation->field_name }}', 'pkg-{{ $variation->field_code }}', '{{ $fieldPrices[$variation->field_code] ?? 0 }}')">
                                                <img src="{{ asset('assets/img/apps/' . (strtolower($variation->field_name) == 'neco' ? 'neco.png' : 'waec.png')) }}" class="img-fluid mb-2" style="width: 40px; height: 40px;">
                                                <div class="fw-bold fs-9 text-dark lh-1">{{ $variation->field_name }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="service" id="service_id" required>
                            </div>

                            {{-- Phone Number --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Phone Number</label>
                                <input type="text" id="mobileno" name="mobileno" maxlength="11"
                                       class="form-control text-center shadow-sm" placeholder="08012345678" required>
                            </div>

                            {{-- Amount & Wallet --}}
                            <div class="mb-5">
                                <label class="form-label fw-semibold small d-flex justify-content-between">
                                    <span>Amount (₦)</span>
                                    <small class="text-muted d-flex align-items-center gap-1">Balance: 
                                        <strong class="text-primary" id="walletBalance">
                                            ₦{{ number_format($wallet->balance ?? 0, 2) }}
                                        </strong>
                                        <i class="bi bi-eye-slash-fill ms-1" id="toggleBalance" style="cursor: pointer;" onclick="toggleBalanceVisibility()"></i>
                                    </small>
                                </label>
                                <input type="text" id="amountToPay" name="amount" readonly class="form-control text-center shadow-sm" value="0.00" />
                            </div>

                            <input type="hidden" name="pin" id="service_pin" required>

                            <div class="d-grid shadow-sm">
                                <button type="button" class="btn btn-primary btn-lg fw-bold rounded-pill py-2"
                                        id="proceed-btn" disabled onclick="openPinModal()">
                                    Proceed to Buy <i class="bi bi-chevron-right ms-1"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right Column: AI Guide & History --}}
            <div class="col-12 col-xl-7">
                <div class="card shadow-lg border-0 rounded-20px overflow-hidden d-flex flex-column shadow-hover transition-all" style="height: 750px;">
                    <div class="card-header bg-white text-dark p-3 p-md-4 d-flex align-items-center justify-content-between rounded-20px rounded-bottom-0 flex-shrink-0">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary rounded-circle p-2 shadow-sm" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-robot fs-15 text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Arewa Smart AI Guide</h6>
                                <small class="text-primary small fw-bold"><i class="bi bi-circle-fill fs-8 me-1"></i> Online Assistant</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary border-0 rounded-circle" data-bs-toggle="collapse" data-bs-target="#historyCollapse" title="Toggle History">
                                <i class="bi bi-clock-history"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="clearChat()" title="Clear Chat"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>

                    <div class="flex-grow-1 overflow-auto custom-scrollbar" id="aiScrollContainer">
                        
                        <div class="collapse show" id="historyCollapse">
                            <div class="bg-white border-bottom shadow-sm">
                                {{-- Spending Statistics --}}
                                <div class="px-3 py-4 border-bottom bg-light-subtle">
                                    <h6 class="fs-11 fw-bold text-uppercase mb-3 d-flex align-items-center">
                                        <i class="bi bi-pie-chart-fill me-2 text-primary"></i> Spending Distribution
                                    </h6>
                                    @if($history->count() > 0)
                                        <div id="spendingChart" style="min-height: 200px;"></div>
                                    @else
                                        <div class="text-center py-4 text-muted small">No data for statistics.</div>
                                    @endif
                                </div>

                                <div class="px-3 py-3 pb-3">
                                    <small class="text-muted fw-bold text-uppercase fs-9 d-block mb-2">Recent NECO & NABTED Purchases</small>

                                    <div class="transaction-list">
                                        @forelse($history->take(15) as $data)
                                            <a href="{{ route('education.receipt', $data->ref) }}" class="text-decoration-none d-block mb-3">
                                                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light-subtle shadow-sm border-start border-3 border-primary" 
                                                     style="transition: all 0.2s ease;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                            <i class="bi bi-book fs-12"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fs-12 fw-bold text-dark">{{ $data->phone_number }}</h6>
                                                            @php
                                                                $tokenVal = $data->token;
                                                                if (!$tokenVal) {
                                                                    preg_match('/PIN: (.*)/', $data->description, $matches);
                                                                    $tokenVal = $matches[1] ?? 'Check Receipt';
                                                                }
                                                            @endphp
                                                            <div class="d-flex align-items-center mt-1">
                                                                <small class="badge bg-primary-subtle text-primary border-0 fs-8 fw-bold px-2 py-1">
                                                                    PIN: {{ $tokenVal }}
                                                                </small>
                                                                <small class="text-muted fs-9 ms-2">{{ strtoupper($data->network ?? 'PIN') }} • {{ $data->created_at->diffForHumans() }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="d-block fs-12 fw-bold text-dark">₦{{ number_format($data->amount, 0) }}</span>
                                                        <span class="badge bg-success p-1" style="font-size: 8px;">SUCCESSFUL</span>
                                                    </div>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="text-center py-4 text-muted small">No recent purchases found.</div>
                                        @endforelse
                                    </div>
                                    @if($history->count() > 0)
                                        <div class="d-flex justify-content-center mt-2">
                                            {{ $history->links('vendor.pagination.bootstrap-4') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- AI Chat Window --}}
                        <div class="card-body bg-light-subtle p-3 p-md-4" id="aiChatWindow" style="min-height: 300px;">
                            <div class="d-flex gap-2 mb-4 animate-fade-in">
                                <div class="bg-primary text-white rounded-circle p-1 align-self-start shadow-sm" style="width:28px;height:28px;font-size:12px;display:flex;align-items:center;justify-content:center; flex-shrink:0;">AS</div>
                                <div class="bg-white p-3 rounded-4 rounded-start-0 shadow-sm border small shadow-hover" style="max-width: 85%;">
                                    <p class="mb-0 text-dark">Hello! I'm your Exam Pin assistant. Need help with NECO or NABTED pins? Just ask!</p>
                                </div>
                            </div>
                        </div>

                        <div id="aiTypingIndicator" class="px-4 py-2 d-none">
                            <small class="text-muted"><i class="bi bi-stars spin me-1"></i> Analyzing details...</small>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-3 p-md-4 mt-auto rounded-20px rounded-top-0 shadow-sm">
                        <div class="input-group bg-light rounded-pill p-1 border shadow-sm focus-within-shadow">
                            <input type="text" id="aiInput" class="form-control border-0 bg-transparent ps-3" placeholder="Ask about NECO/NABTED pins...">
                            <button class="btn btn-primary rounded-circle p-2 mx-1 shadow-sm d-flex align-items-center justify-content-center" id="sendAiBtn" style="width:38px;height:38px;">
                                <i class="bi bi-send-fill fs-14"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- PIN Modal --}}
    @include('pages.pin')

    @push('scripts')
    <script>
        let convHistory = [];

        $(document).ready(function () {
            const serviceIdInput = $('#service_id');
            const proceedBtn = $('#proceed-btn');
            const amountInput = $('#amountToPay');

            // Package Selection Helper
            window.selectPackage = (code, name, elementId, price) => {
                $('.network-card').removeClass('active border-primary').addClass('bg-white');
                $('#' + elementId).addClass('active border-primary').removeClass('bg-white');
                serviceIdInput.val(code);
                amountInput.val(price);
                proceedBtn.prop('disabled', false);
            };

            // AI Chatbot Logic
            const aiWin = document.getElementById('aiChatWindow');
            const aiIn = document.getElementById('aiInput');
            const typeInd = document.getElementById('aiTypingIndicator');
            const scrollCont = document.getElementById('aiScrollContainer');

            const addBubble = (txt, role = 'user') => {
                const wrap = document.createElement('div');
                wrap.className = `d-flex mb-4 animate-fade-in ${role === 'user' ? 'justify-content-end' : ''}`;
                
                const html = role === 'user'
                    ? `<div class="bg-primary text-white p-3 rounded-4 rounded-top-end-0 shadow-sm border-0 small shadow-hover" style="max-width: 85%;">${txt}</div>`
                    : `<div class="d-flex gap-2">
                        <div class="bg-primary text-white rounded-circle p-1 align-self-start shadow-sm" style="width:28px;height:28px;font-size:12px;display:flex;align-items:center;justify-content:center; flex-shrink:0;">AS</div>
                        <div class="bg-white p-3 rounded-4 rounded-start-0 shadow-sm border small shadow-hover text-dark" style="max-width: 85%;">${txt}</div>
                       </div>`;
                
                wrap.innerHTML = html;
                aiWin.appendChild(wrap);
                scrollCont.scrollTop = scrollCont.scrollHeight;
                convHistory.push({ role, content: txt });
            };

            window.askAi = (txt) => {
                if(!txt.trim()) return;
                const currentHistory = [...convHistory]; // Capture history before adding new bubble
                addBubble(txt, 'user');
                aiIn.value = '';
                typeInd.classList.remove('d-none');

                fetch("{{ route('ai.ask') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    body: JSON.stringify({
                        comment: `User is on the NECO & NABTED PIN purchase page. Selected: ${$('#service_id').val() || 'None'}.`,
                        question: txt,
                        history: currentHistory
                    })
                })
                .then(r => r.json())
                .then(data => {
                    typeInd.classList.add('d-none');
                    if(data.success) addBubble(data.answer, 'assistant');
                    else addBubble("I'm sorry, I'm having trouble connecting right now.", 'assistant');
                })
                .catch(() => {
                    typeInd.classList.add('d-none');
                });
            };

            $('#sendAiBtn').on('click', () => askAi(aiIn.value));
            $('#aiInput').on('keypress', (e) => { if(e.key === 'Enter') askAi(aiIn.value); });
        });

        // Global Helpers
        window.toggleBalanceVisibility = function() {
            const balanceSpan = document.getElementById('walletBalance');
            const toggleIcon = document.getElementById('toggleBalance');
            const actualBalance = "₦{{ number_format($wallet->balance ?? 0, 2) }}";
            
            if (balanceSpan.textContent.includes('***')) {
                balanceSpan.textContent = actualBalance;
                toggleIcon.className = 'bi bi-eye-slash-fill ms-1';
                localStorage.setItem('balanceVisible', 'true');
            } else {
                balanceSpan.textContent = '₦****.**';
                toggleIcon.className = 'bi bi-eye-fill ms-1';
                localStorage.setItem('balanceVisible', 'false');
            }
        };

        window.clearChat = function() {
            document.getElementById('aiChatWindow').innerHTML = '';
            convHistory = [];
        };

        window.openPinModal = function() {
            const serviceId = $('#service_id').val();
            const examName = $('#pkg-' + serviceId + ' .fw-bold').text();
            const amount = document.getElementById('amountToPay').value;
            const mobile = document.getElementById('mobileno').value;

            document.getElementById('confirmAccountName').textContent = 'Exam PIN Purchase';
            document.getElementById('confirmBankName').textContent = examName;
            document.getElementById('confirmAccountNo').textContent = 'Receiver: ' + mobile;
            const cleanAmount = String(amount).replace(/[^0-9.]/g, '');
            const parsedAmount = parseFloat(cleanAmount);
            document.getElementById('confirmAmount').textContent = '₦' + (isNaN(parsedAmount) ? '0.00' : parsedAmount.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));

            const pinModal = new bootstrap.Modal(document.getElementById('pinModal'));
            pinModal.show();
        };

        // PIN Verification
        $('#confirmPinBtn').on('click', function() {
            const btn = $(this);
            if(btn.prop('disabled')) return;

            const pin = $('#pinInput').val().trim();
            if(!pin) return;
            
            btn.prop('disabled', true);
            $('#pinLoader').removeClass('d-none');
            
            $.ajax({
                type: "POST",
                url: "{{ route('verify.pin') }}",
                data: { pin: pin, _token: "{{ csrf_token() }}" },
                success: function(data) {
                    if (data.valid) {
                        $('#service_pin').val(pin);
                        $('#confirmPinBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
                        $('#buy-pin-form').submit();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Invalid PIN', text: 'Please check your transaction PIN.' });
                        btn.prop('disabled', false);
                        $('#pinLoader').addClass('d-none');
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Connection failed. Please try again.' });
                    btn.prop('disabled', false);
                    $('#pinLoader').addClass('d-none');
                }
            });
        });

        // Persist preferences
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('balanceVisible') === 'false') {
                const balanceSpan = document.getElementById('walletBalance');
                const toggleIcon = document.getElementById('toggleBalance');
                if (balanceSpan && toggleIcon) {
                    balanceSpan.textContent = '₦****.**';
                    toggleIcon.className = 'bi bi-eye-fill ms-1';
                }
            }
        });

        // History Chart
        document.addEventListener('DOMContentLoaded', function() {
            const options = {
                series: @json($chartValues),
                chart: { type: 'donut', height: 200, sparkline: { enabled: true } },
                labels: @json($chartLabels),
                colors: ['#0d6efd', '#d47508ff', '#6610f2', '#6f42c1', '#d63384', '#dc3545', '#fd7e14', '#ffc107'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                name: { show: true, fontSize: '10px', fontWeight: 600, offsetY: -10 },
                                value: { 
                                    show: true, 
                                    fontSize: '14px', 
                                    fontWeight: 700, 
                                    offsetY: 5,
                                    formatter: function(val) { return '₦' + parseInt(val).toLocaleString(); }
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '10px',
                                    fontWeight: 600,
                                    formatter: function(w) {
                                        return '₦' + w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false },
                legend: { show: true, position: 'bottom', fontSize: '10px' },
                tooltip: { y: { formatter: function(val) { return '₦' + val.toLocaleString(); } } }
            };

            if (document.querySelector("#spendingChart")) {
                const chart = new ApexCharts(document.querySelector("#spendingChart"), options);
                chart.render();
            }
        });
    </script>

    @endpush
</x-app-layout>

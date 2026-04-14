<x-app-layout>
    <title>Arewa Smart - Buy Educational Pin</title>

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
                            <i class="bi bi-credit-card me-2"></i> Buy Educational Pin
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="text-center text-muted mb-4 small">
                            Select your educational pin service, choose the type, and complete your purchase securely.
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

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-3 py-2">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form id="buy-pin-form" method="POST" action="{{ route('buypin') }}">
                            @csrf
                            
                            {{-- Service Selection --}}
                            <div class="network-selection mb-4">
                                <label class="form-label fw-semibold text-dark small mb-3 text-center d-block w-100">Select Educational Service</label>
                                <div class="row text-center g-2 g-sm-3 justify-content-center">
                                    <div class="col-6">
                                        <div class="network-card p-2 border rounded-3 text-center" 
                                             id="service-waec"
                                             onclick="selectService('waec', 'service-waec')">
                                            <img src="{{ asset('assets/img/apps/waec.png') }}" alt="WAEC" class="mb-1" onerror="this.src='{{ asset('assets/img/apps/pin.png') }}'">
                                            <span class="d-block small fw-bold" style="font-size: 10px;">WAEC Result Checker</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="network-card p-2 border rounded-3 text-center" 
                                             id="service-waec-registration"
                                             onclick="selectService('waec-registration', 'service-waec-registration')">
                                            <img src="{{ asset('assets/img/apps/waec.png') }}" alt="WAEC Registration" class="mb-1" onerror="this.src='{{ asset('assets/img/apps/pin.png') }}'">
                                            <span class="d-block small fw-bold" style="font-size: 10px;">WAEC Registration</span>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="service" id="service_id" required>
                                <input type="hidden" name="pin" id="service_pin" required>
                            </div>

                            {{-- Pin Type Selection --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Select Type</label>
                                <select name="type" id="type" class="form-select text-center shadow-sm" required>
                                    <option value="">-- Choose Type --</option>
                                    @foreach ($pins as $p)
                                        <option value="{{ $p->variation_code }}" data-amount="{{ $p->variation_amount }}">
                                            {{ strtoupper($p->name ?? $p->variation_code) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Phone Number --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Phone Number</label>
                                <input type="text" id="mobileno" name="mobileno" maxlength="11"
                                       class="form-control text-center shadow-sm" placeholder="Enter 11-digit number" required>
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
                                <input type="text" id="amountToPay" name="amount" readonly class="form-control text-center shadow-sm" placeholder="0.00" />
                            </div>

                            <div class="d-grid shadow-sm">
                                <button type="button" class="btn btn-primary btn-lg fw-bold rounded-pill py-2"
                                        id="proceed-btn" onclick="openPinModal()">
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

                    {{-- Unified Scrollable Section --}}
                    <div class="flex-grow-1 overflow-auto custom-scrollbar" id="aiScrollContainer">
                        
                        {{-- Collapsible History Section --}}
                        @php
                            $historyItems = $history ?? collect();
                            $eduData = $historyItems->groupBy(function($item) {
                                return strtoupper($item->network ?? 'UNKNOWN');
                            })->map(fn($group) => $group->sum('amount'));

                            $chartLabels = $eduData->keys()->toArray();
                            $chartValues = $eduData->values()->toArray();
                        @endphp

                        <div class="collapse show" id="historyCollapse">
                            <div class="bg-white border-bottom shadow-sm">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light-subtle">
                                    <div>
                                        <small class="fw-bold text-muted text-uppercase mb-0 fs-11 px-2">
                                            <i class="bi bi-bar-chart-fill me-2"></i>Spending Distribution
                                        </small>
                                    </div>
                                    <div class="text-end px-2">
                                        <h6 class="mb-0 fw-bold text-primary fs-13">₦{{ number_format($historyItems->sum('amount'), 0) }}</h6>
                                        <small class="text-muted fs-9">Total History</small>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <div id="spendingChart" style="min-height: 180px;"></div>
                                </div>

                                <div class="px-3 pb-3">
                                    <small class="text-muted fw-bold text-uppercase fs-9 d-block mb-2">Recent PIN Purchases</small>
                                    <div class="transaction-list">
                                        @forelse($historyItems->take(15) as $data)
                                            @php
                                                preg_match('/PIN: (.*)/', $data->description, $matches);
                                                $token = $matches[1] ?? 'N/A';
                                            @endphp
                                            <a href="{{ route('education.receipt', $data->ref) }}" class="text-decoration-none d-block mb-3">
                                                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light-subtle shadow-sm border-start border-3 border-primary" 
                                                     style="transition: all 0.2s ease;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                            <i class="bi bi-mortarboard fs-12"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fs-12 fw-bold text-dark">{{ $token }}</h6>
                                                            <small class="text-muted fs-10 text-uppercase">{{ strtoupper($data->network ?? 'UNKNOWN') }} • {{ $data->created_at->diffForHumans() }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="d-block fs-12 fw-bold text-dark">₦{{ number_format($data->amount, 0) }}</span>
                                                        <span class="badge bg-success p-1" style="font-size: 8px;">SUCCESSFUL</span>
                                                    </div>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="text-center py-4 text-muted small">No recent educational pin purchases.</div>
                                        @endforelse
                                    </div>
                                    @if($historyItems->count() > 0)
                                        <div class="d-flex justify-content-center mt-2">
                                            {{ $historyItems->links('vendor.pagination.bootstrap-4') }}
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
                                    <p class="mb-0 text-dark">Hello! I'm your Educational Services assistant. Need help with WAEC or NECO pins? Just ask!</p>
                                </div>
                            </div>
                        </div>

                        <div id="aiTypingIndicator" class="px-4 py-2 d-none">
                            <small class="text-muted"><i class="bi bi-stars spin me-1"></i> Analyzing educational options...</small>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-3 p-md-4 mt-auto rounded-20px rounded-top-0 shadow-sm">
                        <div class="input-group bg-light rounded-pill p-1 border shadow-sm focus-within-shadow">
                            <input type="text" id="aiInput" class="form-control border-0 bg-transparent ps-3" placeholder="Ask about educational pins...">
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
            // Service Selection Logic
            window.selectService = function(val, elementId) {
                $('.network-card').removeClass('active');
                $('#' + elementId).addClass('active');
                $("#service_id").val(val);
            };

            // Handle Type Selection - Update Amount
            $('#type').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const amount = selectedOption.data('amount');
                if(amount) {
                    $('#amountToPay').val(amount);
                } else {
                    $('#amountToPay').val('');
                }
            });

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
                const currentHistory = [...convHistory];
                addBubble(txt, 'user');
                aiIn.value = '';
                typeInd.classList.remove('d-none');

                fetch("{{ route('ai.ask') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    body: JSON.stringify({
                        comment: `User is on the Educational PIN purchase page. Selected Service: ${$('#service_id').val() || 'None'}.`,
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
                    addBubble("Network error. Please try again.", 'assistant');
                });
            };

            $('#sendAiBtn').on('click', () => askAi(aiIn.value));
            $('#aiInput').on('keypress', (e) => { if(e.key === 'Enter') askAi(aiIn.value); });

            // History Chart
            const options = {
                series: @json($chartValues),
                chart: { type: 'donut', height: 200, sparkline: { enabled: true } },
                labels: @json($chartLabels),
                colors: ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545', '#fd7e14', '#ffc107'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                name: { show: true, fontSize: '12px', fontWeight: 600, offsetY: -10 },
                                value: { 
                                    show: true, 
                                    fontSize: '16px', 
                                    fontWeight: 700, 
                                    offsetY: 5,
                                    formatter: function(val) { return '₦' + parseInt(val).toLocaleString(); }
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '12px',
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
            const service = document.getElementById('service_id').value;
            const type = document.getElementById('type').value;
            const typeText = $('#type option:selected').text();
            const phone = document.getElementById('mobileno').value;
            const amount = document.getElementById('amountToPay').value;

            if (!service || !type || !phone || phone.length < 11 || !amount) {
                Swal.fire({ icon: 'warning', title: 'Check Form', text: 'Please complete the form correctly.' });
                return;
            }

            document.getElementById('confirmAccountName').textContent = 'Educational PIN Purchase';
            document.getElementById('confirmBankName').textContent = typeText.trim();
            document.getElementById('confirmAccountNo').textContent = 'Phone: ' + phone;
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
            if (!pin) return;

            btn.prop('disabled', true);
            $('#pinLoader').removeClass('d-none');
            
            fetch("{{ route('verify.pin') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ pin })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    $('#service_pin').val(pin);
                    document.getElementById('buy-pin-form').submit();
                } else {
                    Swal.fire({ icon: 'error', title: 'Invalid PIN', text: 'Please check your transaction PIN.' });
                    btn.prop('disabled', false);
                    $('#pinLoader').addClass('d-none');
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Connection failed. Please try again.' });
                btn.prop('disabled', false);
                $('#pinLoader').addClass('d-none');
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
    </script>
    @endpush
</x-app-layout>

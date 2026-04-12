<x-app-layout>
    <title>Arewa Smart - Pay Electricity Bill</title>

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
            padding: 10px 5px;
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
            width: 35px;
            height: 35px;
            object-fit: contain;
            border-radius: 5px;
            margin-bottom: 5px;
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

            {{-- Left Column: Electricity Form --}}
            <div class="col-12 col-xl-5 mb-4">
                <div class="card custom-card shadow-lg border-0 rounded-0 rounded-md-4 d-flex flex-column">
                    <div class="card-header justify-content-between bg-primary text-white rounded-0 rounded-top-md-4">
                        <div class="card-title fw-semibold">
                            <i class="bi bi-lightning-charge-fill me-2"></i> Pay Electricity Bill
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="text-center text-muted mb-4 small">
                            Enter meter number, select provider, and pay instantly.
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

                        <form id="buy-electricity-form" method="POST" action="{{ route('buy.electricity') }}">
                            @csrf
                            
                            {{-- Disco Selection --}}
                            <div class="network-selection mb-4">
                                <label class="form-label fw-semibold text-dark small mb-3 text-center d-block w-100">Select Electricity Provider (Disco)</label>
                                <div class="row text-center g-2 g-sm-3 justify-content-center">
                                    @php
                                        $discos = [
                                            'ikeja-electric' => 'Ikeja',
                                            'eko-electric' => 'Eko',
                                            'kano-electric' => 'Kano',
                                            'portharcourt-electric' => 'PHED',
                                            'jos-electric' => 'Jos',
                                            'ibadan-electric' => 'Ibadan',
                                            'kaduna-electric' => 'Kaduna',
                                            'abuja-electric' => 'AEDC',
                                            'enugu-electric' => 'Enugu',
                                            'benin-electric' => 'Benin',
                                            'aba-electric-payment' => 'Aba',
                                            'yola-electric' => 'Yola',
                                        ];
                                    @endphp
                                    @foreach($discos as $val => $name)
                                        <div class="col-3 col-sm-2">
                                            <div class="network-card p-2 border rounded-3 text-center" 
                                                 id="disco-{{ $val }}"
                                                 onclick="selectDisco('{{ $val }}', 'disco-{{ $val }}')">
                                                <img src="{{ asset('assets/img/apps/electric.png') }}" alt="{{ $name }}" class="mb-1">
                                                <span class="d-block small fw-bold" style="font-size: 9px;">{{ $name }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="service_id" id="service_id" required>
                                <input type="hidden" name="pin" id="service_pin" required>
                            </div>

                            {{-- Meter Type --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Meter Type</label>
                                <select name="meter_type" id="meter_type" class="form-select text-center shadow-sm" required>
                                    <option value="">Select Type</option>
                                    <option value="prepaid">Prepaid</option>
                                    <option value="postpaid">Postpaid</option>
                                </select>
                            </div>

                            {{-- Meter Number --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Meter Number</label>
                                <div class="input-group shadow-sm focus-within-shadow rounded-2">
                                    <input type="text" id="meter_number" name="meter_number" class="form-control text-center border-0 small" placeholder="Enter Meter No." required>
                                    <button class="btn btn-primary btn-sm px-3" type="button" id="verify-btn">Verify</button>
                                </div>
                                <small id="verify-status" class="d-block mt-1 fw-bold fs-9 text-center"></small>
                            </div>

                            <div id="customer-info" class="alert alert-info py-2 px-3 border-0 shadow-sm d-none animate-fade-in mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-check-fill fs-13 text-primary"></i>
                                    <div>
                                        <div class="fw-bold fs-12">Name: <span id="customer-name" class="text-primary"></span></div>
                                        <div class="small text-muted fs-11">Address: <span id="customer-address"></span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Phone Number</label>
                                    <input type="text" id="phone" name="phone" maxlength="11"
                                           class="form-control text-center shadow-sm" placeholder="08012345678" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small d-flex justify-content-between">
                                        <span>Amount (₦)</span>
                                        <small class="text-muted d-flex align-items-center gap-1">Balance: 
                                            <strong class="text-primary" id="walletBalance">
                                                ₦{{ number_format($wallet->balance ?? 0, 2) }}
                                            </strong>
                                            <i class="bi bi-eye-slash-fill ms-1" id="toggleBalance" style="cursor: pointer;" onclick="toggleBalanceVisibility()"></i>
                                        </small>
                                    </label>
                                    <input type="number" id="amount" name="amount" class="form-control text-center shadow-sm" placeholder="Min 100" min="100" required>
                                </div>
                            </div>

                            <div class="d-grid shadow-sm">
                                <button type="button" class="btn btn-primary btn-lg fw-bold rounded-pill py-2"
                                        id="proceed-btn" disabled onclick="openPinModal()">
                                    Proceed to Pay <i class="bi bi-chevron-right ms-1"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right Column: AI Guide & History --}}
            <div class="col-12 col-xl-7">
                <div class="card shadow-lg border-0 rounded-0 rounded-md-4 overflow-hidden d-flex flex-column shadow-hover transition-all" style="height: 750px;">
                    <div class="card-header bg-white text-dark p-3 p-md-4 d-flex align-items-center justify-content-between rounded-0 rounded-top-md-4 flex-shrink-0">
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
                            $discosData = $history->groupBy(function($item) {
                                return strtoupper(str_replace('-', ' ', $item->network));
                            })->map(fn($group) => $group->sum('amount'));

                            $chartLabels = $discosData->keys()->toArray();
                            $chartValues = $discosData->values()->toArray();
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
                                        <h6 class="mb-0 fw-bold text-primary fs-13">₦{{ number_format($history->sum('amount'), 0) }}</h6>
                                        <small class="text-muted fs-9">Total History</small>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <div id="spendingChart" style="min-height: 180px;"></div>
                                </div>

                                <div class="px-3 pb-3">
                                    <small class="text-muted fw-bold text-uppercase fs-9 d-block mb-2">Recent Bill Activity</small>
                                    <div class="transaction-list">
                                        @forelse($history->take(15) as $data)
                                            @php
                                                preg_match('/Token: (.*)/', $data->description, $matches);
                                                $token = $matches[1] ?? null;
                                                $ref = $data->ref;
                                                $providerName = strtoupper(str_replace('-', ' ', $data->network));
                                            @endphp
                                            <div class="d-flex align-items-center justify-content-between p-2 mb-2 rounded-3 bg-light-subtle shadow-sm border-start border-3 border-primary" 
                                                 style="transition: all 0.2s ease;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        <i class="bi bi-lightning-charge fs-12"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fs-12 fw-bold text-dark">{{ $data->phone_number }}</h6>
                                                        <small class="text-muted fs-10 text-uppercase">{{ $providerName }} • {{ $data->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <span class="d-block fs-12 fw-bold text-dark">₦{{ number_format($data->amount, 0) }}</span>
                                                    <span class="badge bg-success p-1" style="font-size: 8px;">SUCCESSFUL</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-4 text-muted small">No recent electricity payments.</div>
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
                                    <p class="mb-0 text-dark">Hello! I'm your Electricity Bill specialist. Need help with Ikeja, Eko, or any other provider? Just ask!</p>
                                </div>
                            </div>
                        </div>

                        <div id="aiTypingIndicator" class="px-4 py-2 d-none">
                            <small class="text-muted"><i class="bi bi-stars spin me-1"></i> Analyzing bill options...</small>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-3 p-md-4 mt-auto rounded-0 rounded-bottom-md-4 shadow-sm">
                        <div class="input-group bg-light rounded-pill p-1 border shadow-sm focus-within-shadow">
                            <input type="text" id="aiInput" class="form-control border-0 bg-transparent ps-3" placeholder="Ask about electricity bills...">
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
            // Disco Selection Logic
            window.selectDisco = function(val, elementId) {
                $('.network-card').removeClass('active');
                $('#' + elementId).addClass('active');
                $("#service_id").val(val);
                
                // Clear verification if disco changes
                resetVerification();
            };

            // Clear verification if meter type or number changes
            $('#meter_type, #meter_number').on('change input', function() {
                resetVerification();
            });

            function resetVerification() {
                $('#verify-status').text('');
                $('#customer-info').addClass('d-none');
                $('#proceed-btn').prop('disabled', true);
            }

            // Meter Verification Logic
            $('#verify-btn').on('click', function() {
                const service = $('#service_id').val();
                const type = $('#meter_type').val();
                const meter = $('#meter_number').val();

                if (!service) {
                    Swal.fire({ icon: 'warning', title: 'Provider Required', text: 'Please select an electricity provider from the options above.' });
                    return;
                }
                if (!type) {
                    Swal.fire({ icon: 'warning', title: 'Meter Type Required', text: 'Please select your meter type (Prepaid or Postpaid).' });
                    return;
                }
                if (!meter) {
                    Swal.fire({ icon: 'warning', title: 'Meter Number Required', text: 'Please enter your meter number.' });
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).text('Verifying...');
                $('#verify-status').text('');
                $('#customer-info').addClass('d-none');

                $.ajax({
                    type: "POST",
                    url: "{{ route('verify.electricity') }}",
                    data: { service_id: service, meter_type: type, meter_number: meter, _token: "{{ csrf_token() }}" },
                    success: function (data) {
                        btn.prop('disabled', false).text('Verify');

                        if (data.success) {
                            $('#verify-status').attr('class', 'd-block mt-1 fw-bold text-success').text('Verification Successful!');
                            $('#customer-name').text(data.customer_name);
                            $('#customer-address').text(data.address);
                            $('#customer-info').removeClass('d-none');
                            $('#proceed-btn').prop('disabled', false);
                        } else {
                            $('#verify-status').attr('class', 'd-block mt-1 fw-bold text-danger').text(data.message || 'Verification failed.');
                        }
                    },
                    error: function () {
                        btn.prop('disabled', false).text('Verify');
                        $('#verify-status').attr('class', 'd-block mt-1 fw-bold text-danger').text('Network error. Please try again.');
                    }
                });
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
                        comment: "User is on the Electricity Bill page.",
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

            // History Chart Initialization
            const options = {
                series: @json($chartValues),
                chart: {
                    type: 'donut',
                    height: 200,
                    sparkline: { enabled: true }
                },
                labels: @json($chartLabels),
                colors: ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545', '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0', '#adb5bd', '#212529'],
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
            const discoId = document.getElementById('service_id').value;
            const discoName = $('#disco-' + discoId + ' span').text();
            const meterType = document.getElementById('meter_type').value;
            const meterNo = document.getElementById('meter_number').value;
            const phone = document.getElementById('phone').value;
            const amount = document.getElementById('amount').value;
            const name = document.getElementById('customer-name').textContent;

            if (!discoId || !meterType || !meterNo || !phone || phone.length < 11 || !amount || amount < 100) {
                Swal.fire({ icon: 'warning', title: 'Check Form', text: 'Please complete the form correctly (min amount ₦100).' });
                return;
            }

            document.getElementById('confirmAccountName').textContent = name || 'Electricity Customer';
            document.getElementById('confirmBankName').textContent = discoName + ' (' + meterType.toUpperCase() + ')';
            document.getElementById('confirmAccountNo').textContent = 'Meter: ' + meterNo;
            const cleanAmount = String(amount).replace(/[^0-9.]/g, '');
            const parsedAmount = parseFloat(cleanAmount);
            document.getElementById('confirmAmount').textContent = '₦' + (isNaN(parsedAmount) ? '0.00' : parsedAmount.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));

            const pinModal = new bootstrap.Modal(document.getElementById('pinModal'));
            pinModal.show();
        };

        // PIN Verification logic
        $('#confirmPinBtn').on('click', function() {
            const pin = $('#pinInput').val().trim();
            if(!pin) return;
            const self = $(this);
            self.prop('disabled', true);
            $('#pinLoader').removeClass('d-none');
            
            $.ajax({
                type: "POST",
                url: "{{ route('verify.pin') }}",
                data: { pin: pin, _token: "{{ csrf_token() }}" },
                success: function(data) {
                    if (data.valid) {
                        $('#service_pin').val(pin);
                        $('#buy-electricity-form').submit();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Invalid PIN', text: 'Please check your transaction PIN.' });
                        self.prop('disabled', false);
                        $('#pinLoader').addClass('d-none');
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Connection failed.' });
                    self.prop('disabled', false);
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
    </script>
    @endpush
</x-app-layout>

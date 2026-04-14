<x-app-layout>
    <title>Arewa Smart - Pay Cable TV</title>

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
            border-radius: 12px;
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

            {{-- Left Column: Cable TV Form --}}
            <div class="col-12 col-xl-5 mb-4">
                <div class="card custom-card shadow-lg border-0 rounded-20px d-flex flex-column">
                    <div class="card-header justify-content-between bg-primary text-white rounded-20px rounded-bottom-0">
                        <div class="card-title fw-semibold">
                            <i class="bi bi-tv me-2"></i> Pay Cable TV
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="text-center text-muted mb-4 small">
                            Subscribe to DSTV, GOTV, Startimes, or Showmax instantly.
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

                        <form id="buy-cable-form" method="POST" action="{{ route('buy.cable') }}">
                            @csrf
                            
                            {{-- Provider Selection --}}
                            <div class="network-selection mb-4">
                                <label class="form-label fw-semibold text-dark small mb-3 text-center d-block w-100">Select Cable Provider</label>
                                <div class="row text-center g-2 g-sm-3 justify-content-center">
                                    @php
                                        $providers = [
                                            'dstv'      => 'DSTV',
                                            'gotv'      => 'GOTV',
                                            'startimes' => 'StarTimes',
                                            'showmax'   => 'Showmax'
                                        ];
                                    @endphp
                                    @foreach($providers as $val => $name)
                                        <div class="col-3">
                                            <div class="network-card p-2 border rounded-20px text-center" 
                                                 id="provider-{{ $val }}"
                                                 onclick="selectProvider('{{ $val }}', 'provider-{{ $val }}')">
                                                <img src="{{ asset('assets/img/apps/tv.png') }}" alt="{{ $name }}" class="mb-1" onerror="this.src='{{ asset('assets/img/apps/tv.jpg') }}'">
                                                <span class="d-block small fw-bold" style="font-size: 10px;">{{ $name }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="service_id" id="service_id" required>
                                <input type="hidden" name="pin" id="service_pin" required>
                            </div>

                            {{-- IUC Number --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Smartcard / IUC Number</label>
                                <div class="input-group shadow-sm focus-within-shadow rounded-2">
                                    <input type="text" id="billersCode" name="billersCode" class="form-control text-center border-0 small" placeholder="Enter IUC Number" required>
                                    <button class="btn btn-primary btn-sm px-3" type="button" id="verify-btn">Verify</button>
                                </div>
                                <small id="verify-status" class="d-block mt-1 fw-bold fs-9 text-center"></small>
                            </div>

                            {{-- Verification Info --}}
                            <div id="customer-info" class="alert alert-info py-2 px-3 border-0 shadow-sm d-none animate-fade-in mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-check-fill fs-13 text-primary"></i>
                                    <div>
                                        <div class="fw-bold fs-12">Name: <span id="customer-name" class="text-primary"></span></div>
                                        <div class="small text-muted fs-11">Status: <span id="customer-status" class="fw-bold"></span> | Due: <span id="due-date"></span></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Subscription Type Selector --}}
                            <div id="sub-type-section" class="mb-4 d-none animate-fade-in">
                                <label class="form-label fw-bold small d-block text-center text-muted mb-3">Subscription Type</label>
                                <div class="nav nav-pills justify-content-center bg-light p-1 rounded-pill shadow-sm mx-auto" style="max-width: fit-content;">
                                    <div class="nav-item">
                                        <input type="radio" class="btn-check" name="subscription_type" id="sub_renew" value="renew" checked>
                                        <label class="btn btn-sm btn-outline-primary border-0 rounded-pill px-4 fw-bold" for="sub_renew" style="font-size: 11px;">
                                            <i class="bi bi-arrow-repeat me-1"></i> Renew Current
                                        </label>
                                    </div>
                                    <div class="nav-item">
                                        <input type="radio" class="btn-check" name="subscription_type" id="sub_change" value="change">
                                        <label class="btn btn-sm btn-outline-primary border-0 rounded-pill px-4 fw-bold" for="sub_change" style="font-size: 11px;">
                                            <i class="bi bi-plus-circle me-1"></i> Change Package
                                        </label>
                                    </div>
                                </div>
                            </div>


                            {{-- Package Selection --}}
                            <div class="mb-4 d-none" id="package-section">
                                <label class="form-label fw-semibold small">Select Package</label>
                                <select name="variation_code" id="variation_code" class="form-select text-center shadow-sm">
                                    <option value="">-- Choose Package --</option>
                                </select>
                            </div>

                            {{-- Amount & Wallet --}}
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
                                    <input type="number" id="amount" name="amount" class="form-control text-center shadow-sm" readonly required>
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
                            $cableData = $history->groupBy(function($item) {
                                return strtoupper($item->network);
                            })->map(fn($group) => $group->sum('amount'));

                            $chartLabels = $cableData->keys()->toArray();
                            $chartValues = $cableData->values()->toArray();
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
                                    <small class="text-muted fw-bold text-uppercase fs-9 d-block mb-2">Recent Subscription Activity</small>
                                    <div class="transaction-list">
                                        @forelse($history->take(15) as $data)
                                            @php
                                                $providerName = strtoupper($data->network);
                                            @endphp
                                            <div class="d-flex align-items-center justify-content-between p-2 mb-2 rounded-3 bg-light-subtle shadow-sm border-start border-3 border-primary" 
                                                 style="transition: all 0.2s ease;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        <i class="bi bi-tv fs-12"></i>
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
                                            <div class="text-center py-4 text-muted small">No recent cable subscriptions.</div>
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
                                    <p class="mb-0 text-dark">Hello! I'm your Cable TV specialist. Need help with DSTV, GOTV, or StarTimes? Just ask!</p>
                                </div>
                            </div>
                        </div>

                        <div id="aiTypingIndicator" class="px-4 py-2 d-none">
                            <small class="text-muted"><i class="bi bi-stars spin me-1"></i> Analyzing subscription options...</small>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-3 p-md-4 mt-auto rounded-20px rounded-top-0 shadow-sm">
                        <div class="input-group bg-light rounded-pill p-1 border shadow-sm focus-within-shadow">
                            <input type="text" id="aiInput" class="form-control border-0 bg-transparent ps-3" placeholder="Ask about cable subscriptions...">
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
        let renewalAmount = 0;
        let variations = [];

        $(document).ready(function () {
            // Provider Selection Logic
            window.selectProvider = function(val, elementId) {
                $('.network-card').removeClass('active');
                $('#' + elementId).addClass('active');
                $("#service_id").val(val);
                
                // Fetch variations for the selected service
                fetchVariations(val);
                
                // Reset verification
                resetVerification();
            };

            function fetchVariations(service_id) {
                $.getJSON("{{ route('cable.variations') }}?service_id=" + service_id, function(data) {
                    if(data.success) {
                        variations = data.variations;
                        populateVariations(variations);
                    }
                });
            }

            function populateVariations(list) {
                const select = $('#variation_code');
                select.html('<option value="">-- Choose Package --</option>');
                list.forEach(v => {
                    select.append(`<option value="${v.code}" data-amount="${v.amount}">${v.name}</option>`);
                });
            }

            // Verification Logic
            $('#verify-btn').on('click', function() {
                const service = $('#service_id').val();
                const iuc = $('#billersCode').val();

                if (!service) {
                    Swal.fire({ icon: 'warning', title: 'Provider Required', text: 'Please select a cable provider first.' });
                    return;
                }
                if (!iuc) {
                    Swal.fire({ icon: 'warning', title: 'IUC Required', text: 'Please enter your Smartcard or IUC number.' });
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).text('Verifying...');
                $('#verify-status').text('');
                $('#customer-info').addClass('d-none');
                $('#sub-type-section').addClass('d-none');
                $('#proceed-btn').prop('disabled', true);

                $.ajax({
                    type: "POST",
                    url: "{{ route('verify.cable') }}",
                    data: { service_id: service, billersCode: iuc, _token: "{{ csrf_token() }}" },
                    success: function (data) {
                        btn.prop('disabled', false).text('Verify');

                        if (data.success) {
                            $('#verify-status').attr('class', 'd-block mt-1 fw-bold text-success').text('Verification Successful!');
                            $('#customer-name').text(data.customer_name);
                            $('#customer-status').text(data.status);
                            $('#due-date').text(data.due_date);
                            
                            renewalAmount = data.renewal_amount || 0;
                            
                            $('#customer-info').removeClass('d-none');
                            $('#sub-type-section').removeClass('d-none');
                            $('#proceed-btn').prop('disabled', false);

                            // Handle subtype default
                            handleSubTypeChange();
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

            // Handle SubType Change
            $('input[name="subscription_type"]').on('change', handleSubTypeChange);

            function handleSubTypeChange() {
                const type = $('input[name="subscription_type"]:checked').val();
                if (type === 'renew') {
                    $('#package-section').addClass('d-none');
                    $('#amount').val(renewalAmount);
                    $('#variation_code').prop('required', false);
                } else {
                    $('#package-section').removeClass('d-none');
                    $('#amount').val(''); // Clear amount, wait for selection
                    $('#variation_code').prop('required', true);
                }
            }

            // Handle Package Selection
            $('#variation_code').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const amount = selectedOption.data('amount');
                if(amount) {
                    $('#amount').val(amount);
                }
            });

            function resetVerification() {
                $('#verify-status').text('');
                $('#customer-info').addClass('d-none');
                $('#sub-type-section').addClass('d-none');
                $('#package-section').addClass('d-none');
                $('#proceed-btn').prop('disabled', true);
            }

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
                        comment: `User is on the Cable TV Subscription page. Selected Provider: ${$('#service_id').val() || 'None'}. Customer: ${$('#customer-name').text() || 'Unknown'}.`,
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
            const providerId = document.getElementById('service_id').value;
            const providerName = $('#provider-' + providerId + ' span').text();
            const iuc = document.getElementById('billersCode').value;
            const phone = document.getElementById('phone').value;
            const amount = document.getElementById('amount').value;
            const name = document.getElementById('customer-name').textContent;
            const subType = $('input[name="subscription_type"]:checked').val();

            if (!providerId || !iuc || !phone || phone.length < 11 || !amount) {
                Swal.fire({ icon: 'warning', title: 'Check Form', text: 'Please complete the form correctly.' });
                return;
            }

            document.getElementById('confirmAccountName').textContent = name || 'Cable Customer';
            document.getElementById('confirmBankName').textContent = providerName + ' (' + subType.toUpperCase() + ')';
            document.getElementById('confirmAccountNo').textContent = 'IUC: ' + iuc;
            const cleanAmount = String(amount).replace(/[^0-9.]/g, '');
            const parsedAmount = parseFloat(cleanAmount);
            document.getElementById('confirmAmount').textContent = '₦' + (isNaN(parsedAmount) ? '0.00' : parsedAmount.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));

            const pinModal = new bootstrap.Modal(document.getElementById('pinModal'));
            pinModal.show();
        };

        // PIN Verification
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
                        $('#buy-cable-form').submit();
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

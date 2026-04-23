<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'SME Data' }}</title>

    @push('styles')
    <style>
        .network-selection .network-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #eee;
            background: #fff;
            position: relative;
            overflow: hidden;
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
            border-radius: 12px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .network-selection .network-card img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 12px;
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

            {{-- Left Column: SME Data Form --}}
            <div class="col-12 col-xl-5 mb-4">
                <div class="card custom-card shadow-lg border-0 rounded-20px d-flex flex-column">
                    <div class="card-header justify-content-between bg-primary text-white rounded-20px rounded-bottom-0 flex-shrink-0">
                        <div class="card-title fw-semibold">
                            <i class="bi bi-globe2 me-2"></i> SME Data Service
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="text-center text-muted mb-4 small">
                            Affordable SME data bundles. Select network, data type, and plan to proceed.
                        </p>

                        {{-- Flash Messages --}}
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
                            <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert">
                                <ul class="mb-0 px-3">
                                    @foreach ($errors->all() as $error)
                                        <li><small>{{ $error }}</small></li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form id="buySmeDataForm" method="POST" action="{{ route('buy-sme-data.submit') }}">
                            @csrf

                            {{-- Phone Number --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Phone Number</label>
                                <input type="text" id="sme_mobile" name="mobileno"
                                       oninput="validateNumber()"
                                       class="form-control text-center shadow-sm"
                                       placeholder="08012345678"
                                       maxlength="11" required>
                                <small id="networkResult" class="text-muted d-block mt-1 fs-9"></small>
                            </div>

                            {{-- Network Selection --}}
                            <div class="network-selection mb-4">
                                <label class="form-label fw-semibold text-dark small mb-3 text-center d-block w-100">Select network operator</label>
                                <div class="row text-center g-2 g-sm-3 justify-content-center">
                                    @php
                                        $displayNetworks = [
                                            'MTN'      => ['name' => 'MTN',    'img' => 'mtn.jpg', 'val' => 'MTN'],
                                            'GLO'      => ['name' => 'Glo',    'img' => 'glo.jpg', 'val' => 'GLO'],
                                            '9MOBILE'  => ['name' => '9Mobile','img' => '9Mobile.jpg', 'val' => '9MOBILE'],
                                            'AIRTEL'   => ['name' => 'Airtel', 'img' => 'Airtel.png', 'val' => 'AIRTEL'],
                                        ];
                                    @endphp
                                    @foreach($displayNetworks as $id => $net)
                                        <div class="col-3">
                                            <div class="network-card p-2 border rounded-4 text-center" 
                                                 id="net-{{ strtolower($id) }}"
                                                 onclick="selectNetwork('{{ $net['val'] }}', 'net-{{ strtolower($id) }}')">
                                                <img src="{{ asset('assets/img/apps/' . $net['img']) }}" alt="{{ $net['name'] }}" class="mb-1">
                                                <span class="d-block small fw-bold" style="font-size: 10px;">{{ $net['name'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="network" id="sme_network" required>
                                <input type="hidden" name="pin" id="sme_pin" required>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Data Type</label>
                                    <select name="type" id="sme_type" class="form-select text-center shadow-sm" required>
                                        <option value="">Select Type</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Select Plan</label>
                                    <select name="plan" id="sme_plan" class="form-select text-center shadow-sm" required>
                                        <option value="">Choose Plan</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Amount --}}
                            <div class="mb-4 text-start">
                                <label for="sme_amount" class="form-label fw-semibold d-flex justify-content-between small">
                                    <span>Payable Amount</span>
                                    <small class="text-muted d-flex align-items-center gap-1">Balance: 
                                        <strong class="text-primary" id="walletBalance">
                                            ₦{{ number_format($wallet->balance ?? 0, 2) }}
                                        </strong>
                                        <i class="bi bi-eye-slash-fill ms-1" id="toggleBalance" style="cursor: pointer;" onclick="toggleBalanceVisibility()"></i>
                                    </small>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 shadow-sm small">₦</span>
                                    <input type="text" id="sme_amount" name="amount" readonly class="form-control text-center bg-light shadow-sm fw-bold text-primary" placeholder="0.00" />
                                </div>
                            </div>

                            <div class="d-grid shadow-sm">
                                <button type="button" class="btn btn-primary btn-lg fw-bold rounded-pill py-2"
                                        onclick="openPinModal()">
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
                            <button class="btn btn-sm btn-outline-primary border-0 rounded-circle" data-bs-toggle="collapse" data-bs-target="#smeHistoryCollapse" title="Toggle History">
                                <i class="bi bi-clock-history"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="clearChat()" title="Clear Chat"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>

                    {{-- Unified Scrollable Section --}}
                    <div class="flex-grow-1 overflow-auto custom-scrollbar" id="aiScrollContainer">
                        @php
                            $networksData = $recentPurchases->groupBy(function($item) {
                                $meta = $item->metadata;
                                return strtoupper($meta['network'] ?? 'DATA');
                            })->map(fn($group) => $group->sum('amount'));

                            $chartLabels = $networksData->keys()->toArray();
                            $chartValues = $networksData->values()->toArray();
                            
                            $baseColors = [
                                'MTN' => '#FFCC00',
                                'AIRTEL' => '#ED1C24',
                                'GLO' => '#008D41',
                                '9MOBILE' => '#006633'
                            ];
                            $chartColors = [];
                            foreach($chartLabels as $label) {
                                $chartColors[] = $baseColors[$label] ?? '#0d6efd';
                            }
                        @endphp

                        <div class="collapse show" id="smeHistoryCollapse">
                            <div class="bg-white border-bottom shadow-sm">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light-subtle">
                                    <div>
                                        <small class="fw-bold text-muted text-uppercase mb-0 fs-11 px-2">
                                            <i class="bi bi-bar-chart-fill me-2"></i>Usage Distribution
                                        </small>
                                    </div>
                                    <div class="text-end px-2">
                                        <h6 class="mb-0 fw-bold text-primary fs-13">₦{{ number_format($recentPurchases->sum('amount'), 0) }}</h6>
                                        <small class="text-muted fs-9">Total Last 15</small>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <div id="usageChart" style="min-height: 180px;"></div>
                                </div>

                                <div class="px-3 pb-3">
                                    <small class="text-muted fw-bold text-uppercase fs-9 d-block mb-2">Recent SME Activity</small>
                                    <div class="transaction-list">
                                        @forelse($recentPurchases->take(15) as $history)
                                            @php
                                                $meta = $history->metadata;
                                                $phone = $meta['phone'] ?? substr($history->description, -11);
                                                $networkLabel = strtoupper($meta['network'] ?? 'DATA');
                                                $networkColors = [
                                                    'MTN' => 'warning',
                                                    'AIRTEL' => 'danger',
                                                    'GLO' => 'success',
                                                    '9MOBILE' => 'dark'
                                                ];
                                                $nColor = $networkColors[$networkLabel] ?? 'primary';
                                            @endphp
                                            <div class="d-flex align-items-center justify-content-between p-2 mb-2 rounded-3 bg-light-subtle shadow-sm border-start border-3 border-{{ $nColor }}" 
                                                 onclick="repeatSme('{{ $networkLabel }}', '{{ $phone }}')" 
                                                 style="cursor: pointer; transition: all 0.2s ease;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-{{ $nColor }} bg-opacity-10 text-{{ $nColor }} rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        <i class="bi bi-reception-4 fs-12"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fs-12 fw-bold text-dark">{{ $phone }}</h6>
                                                        <small class="text-muted fs-10 text-uppercase">{{ $networkLabel }} • {{ $history->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <span class="d-block fs-12 fw-bold text-dark">₦{{ number_format($history->amount, 0) }}</span>
                                                    <span class="badge {{ $history->status == 'completed' || $history->status == 'successful' ? 'bg-success' : 'bg-danger' }} p-1" style="font-size: 8px;">{{ strtoupper($history->status) }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-4 text-muted small">No recent SME purchases.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- AI Chat Window --}}
                        <div class="card-body bg-light-subtle p-3 p-md-4" id="aiChatWindow" style="min-height: 300px;">
                            <div class="d-flex gap-2 mb-4 animate-fade-in">
                                <div class="bg-primary text-white rounded-circle p-1 align-self-start shadow-sm" style="width:28px;height:28px;font-size:12px;display:flex;align-items:center;justify-content:center; flex-shrink:0;">AS</div>
                                <div class="bg-white p-3 rounded-4 rounded-start-0 shadow-sm border small shadow-hover" style="max-width: 85%;">
                                    <p class="mb-0 text-dark">Hello! I'm your SME Data specialist. Need recommendations for MTN or Airtel?</p>
                                </div>
                            </div>
                        </div>

                        <div id="aiTypingIndicator" class="px-4 py-2 d-none">
                            <small class="text-muted"><i class="bi bi-stars spin me-1"></i> Analyzing SME options...</small>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-3 p-md-4 mt-auto rounded-20px rounded-top-0 shadow-sm">
                        <div class="input-group bg-light rounded-pill p-1 border shadow-sm focus-within-shadow">
                            <input type="text" id="aiInput" class="form-control border-0 bg-transparent ps-3" placeholder="Ask about SME data...">
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
            // SME AJAX Selection Logic
            window.selectNetwork = function(val, elementId) {
                $('.network-card').removeClass('active');
                $('#' + elementId).addClass('active');
                $("#sme_network").val(val).trigger('change');
            };

            $("#sme_network").change(function () {
                let service_id = $(this).val();
                if(!service_id) return;
                $("#sme_type").empty().append("<option value=''>Loading...</option>");
                
                $.ajax({
                    type: "get",
                    url: "{{ url('sme-data/fetch-data-type') }}",
                    data: { id: service_id },
                    success: function (response) {
                        $("#sme_type").empty().append("<option value=''>Data Type</option>");
                        response.forEach(item => {
                            $("#sme_type").append(`<option value="${item.plan_type}">${item.plan_type}</option>`);
                        });
                        $("#sme_plan").empty().append("<option value=''>Select Plan</option>");
                        $("#sme_amount").val("");
                    }
                });
            });

            $("#sme_type").change(function () {
                let service_id = $("#sme_network").val();
                let type = $(this).val();
                if(!service_id || !type) return;
                $("#sme_plan").empty().append("<option value=''>Fetching plans...</option>");

                $.ajax({
                    type: "get",
                    url: "{{ url('sme-data/fetch-data-plan') }}",
                    data: { id: service_id, type: type },
                    success: function (response) {
                        $("#sme_plan").empty().append("<option value=''>Data Plan</option>");
                        response.forEach(item => {
                            let text = item.size + " - " + item.validity;
                            $("#sme_plan").append(`<option value="${item.data_id}">${text}</option>`);
                        });
                        $("#sme_amount").val("");
                    }
                });
            });

            $("#sme_plan").change(function () {
                let plan_id = $(this).val();
                if(!plan_id) { $("#sme_amount").val(""); return; }

                $.ajax({
                    type: "get",
                    url: "{{ url('sme-data/fetch-sme-data-bundles-price') }}",
                    data: { id: plan_id },
                    success: function (response) {
                        $("#sme_amount").val(response);
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
                        comment: "User is on the SME Data page inquiring about bundles.",
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
                colors: @json($chartColors),
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

            if (document.querySelector("#usageChart")) {
                const chart = new ApexCharts(document.querySelector("#usageChart"), options);
                chart.render();
            }
        });

        // Global Helpers
        window.validateNumber = function() {
            const phone = document.getElementById('sme_mobile').value;
            const result = document.getElementById('networkResult');
            const selectedNetwork = document.getElementById('sme_network').value;

            if (phone.length >= 4) {
                const patterns = {
                    'mtn': ['0803','0806','0703','0706','0813','0816','0810','0814','0815','0903','0906','0913','0916','07025','07026','0704'],
                    'airtel': ['0802','0808','0701','0708','0812','0902','0907','0901','0904','0912','0917'],
                    'glo': ['0805','0807','0705','0815','0811','0905','0915'],
                    '9mobile': ['0809','0817','0818','0909','0908']
                };

                let detectedVal = null;
                let detectedId = null;

                for (const [key, prefixes] of Object.entries(patterns)) {
                    if (prefixes.some(p => phone.startsWith(p))) {
                        detectedId = key;
                        detectedVal = key === 'mtn' ? 'MTN' : (key === 'airtel' ? 'AIRTEL' : (key === 'glo' ? 'GLO' : '9MOBILE'));
                        break;
                    }
                }

                if (detectedVal) {
                    result.textContent = 'Detected: ' + detectedVal;
                    result.className = 'text-primary fs-9 fw-bold animate-fade-in';
                    if (selectedNetwork !== detectedVal) {
                        selectNetwork(detectedVal, 'net-' + detectedId);
                    }
                } else {
                    result.textContent = 'Unknown prefix';
                    result.className = 'text-muted fs-9';
                }
            } else {
                result.textContent = '';
            }
        };

        window.repeatSme = function(net, phone) {
            const netId = net.toLowerCase() === '9mobile' ? '9mobile' : net.toLowerCase();
            selectNetwork(net, 'net-' + netId);
            $("#sme_mobile").val(phone).addClass('border-primary shadow-sm');
            setTimeout(() => $("#sme_mobile").removeClass('border-primary shadow-sm'), 1500);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };

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

        window.openPinModal = function() {
            const network = document.getElementById('sme_network').value;
            const type = document.getElementById('sme_type').value;
            const plan = document.getElementById('sme_plan');
            const planText = plan.options[plan.selectedIndex]?.text;
            const phone = document.getElementById('sme_mobile').value;
            const amount = document.getElementById('sme_amount').value;

            if (!network || !type || !plan.value || !phone || phone.length < 11) {
                Swal.fire({ icon: 'warning', title: 'Action Required', text: 'Please complete the form correctly.' });
                return;
            }

            document.getElementById('confirmAccountName').textContent = network + ' (' + type + ')';
            document.getElementById('confirmBankName').textContent = planText;
            document.getElementById('confirmAccountNo').textContent = phone;
            const cleanAmount = String(amount).replace(/[^0-9.]/g, '');
            const parsedAmount = parseFloat(cleanAmount);
            document.getElementById('confirmAmount').textContent = '₦' + (isNaN(parsedAmount) ? '0.00' : parsedAmount.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));

            const pinModal = new bootstrap.Modal(document.getElementById('pinModal'));
            pinModal.show();
        };

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
                        $('#sme_pin').val(pin);
                        $('#buySmeDataForm').submit();
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

        window.clearChat = function() {
            document.getElementById('aiChatWindow').innerHTML = '';
            convHistory = [];
        };
    </script>
    @endpush
</x-app-layout>

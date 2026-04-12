<x-app-layout>
    <title>Arewa Smart - Transactions</title>

    <div class="page-body">
        <div class="container-fluid px-0 px-md-3">
            <!-- Page Header -->
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <h3 class="fw-bold text-primary">Transaction History</h3>
                        <p class="text-muted small mb-0">
                            View and filter your wallet transactions and service history.
                        </p>
                    </div>
                </div>
            </div>


            <!-- Loading Indicator Overlay (Removed in favor of internal modal loading) -->
            <div id="globalAiInsightsArea" class="d-none"></div>

            <!-- Global AI Modal (Dedicated for whole list summary) -->
            <div class="modal fade" id="globalAiModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header py-3 text-white bg-primary">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="bi bi-stars fs-15"></i>
                                </div>
                                <h5 class="modal-title fw-bold mb-0">AI Assistant</h5>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 bg-light" id="globalAiModalBody"
                            style="max-height: 450px; overflow-y: auto;">
                            <!-- AI Content Injected Here -->
                            <div id="globalChatContent"></div>
                        </div>
                        <div class="modal-footer bg-white border-top flex-column align-items-stretch p-3">
                            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                <input type="text" id="globalAiQuestion" class="form-control border-0 px-3 py-2"
                                    placeholder="Ask about these transactions..."
                                    onkeydown="if(event.key === 'Enter') submitGlobalQuery()">
                                <button onclick="submitGlobalQuery()" class="btn btn-primary px-3 border-0">
                                    <i class="bi bi-send-fill text-white"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Statistics Cards -->
            <div class="row g-0 g-md-4 mb-3 mb-md-4">
                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <div class="card border-0 shadow-sm rounded-0 rounded-md-4 bg-primary text-white overflow-hidden position-relative hover-scale transition-all h-100 shadow-hover">
                        <div class="card-body p-4 position-relative" style="z-index: 2;">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-white bg-opacity-25 rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="bi bi-wallet2 fs-15"></i>
                                </div>
                                <h6 class="mb-0 fw-bold">Total Credits</h6>
                            </div>
                            <h3 class="fw-bold mb-1">₦{{ number_format($transactions->whereIn('type', ['credit', 'refund', 'bonus', 'manual_credit'])->sum('amount'), 2) }}</h3>
                            <p class="mb-0 small opacity-75">All successful inflows</p>
                        </div>
                        <div class="position-absolute bottom-0 end-0 opacity-10" style="transform: translate(20%, 20%);">
                            <i class="bi bi-graph-up-arrow fs-1" style="font-size: 8rem !important;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <div class="card border-0 shadow-sm rounded-0 rounded-md-4 bg-white overflow-hidden position-relative hover-scale transition-all h-100 shadow-hover border-top border-4 border-danger">
                        <div class="card-body p-4 position-relative" style="z-index: 2;">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-danger-subtle text-danger rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="bi bi-cart-dash fs-15"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-muted">Total Debits</h6>
                            </div>
                            <h3 class="fw-bold mb-1 text-danger">₦{{ number_format($transactions->whereIn('type', ['debit', 'manual_debit'])->sum('amount'), 2) }}</h3>
                            <p class="mb-0 small text-muted">Total spending volume</p>
                        </div>
                        <div class="position-absolute bottom-0 end-0 opacity-10" style="transform: translate(20%, 20%);">
                            <i class="bi bi-graph-down-arrow fs-1" style="font-size: 8rem !important;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <div class="card border-0 shadow-sm rounded-0 rounded-md-4 bg-white overflow-hidden position-relative hover-scale transition-all border-bottom border-4 border-warning h-100 shadow-hover">
                        <div class="card-body p-4 position-relative" style="z-index: 2;">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-warning-subtle text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="bi bi-clock-history fs-15"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-muted">Recent Activity</h6>
                            </div>
                            <h3 class="fw-bold mb-1 text-dark">{{ $transactions->total() }}</h3>
                            <p class="mb-0 small text-muted">Total movements recorded</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unified Search & Filter Bar Section -->
            <div class="row g-0 g-md-4 mb-3 mb-md-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-0 rounded-md-4 overflow-hidden search-bar-container">
                        <div class="card-body p-2 px-3">
                            <form method="GET" action="{{ route('transactions') }}" class="row g-2 align-items-center" onsubmit="document.getElementById('filterOverlay').classList.remove('d-none')">
                                <div class="col-12 col-md-4">
                                    <div class="input-group input-group-modern">
                                        <span class="input-group-text bg-transparent border-0 pe-0">
                                            <i class="bi bi-search text-muted"></i>
                                        </span>
                                        <input type="text" name="search" class="form-control border-0 shadow-none ps-2" 
                                            placeholder="Search Title, Amount..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                
                                <div class="col-6 col-md-2">
                                    <div class="d-flex align-items-center border-start ps-3 h-100">
                                        <i class="bi bi-sliders2-vertical text-muted me-2"></i>
                                        <select class="form-select border-0 shadow-none bg-transparent py-0" name="type">
                                            <option value="">All Types</option>
                                            <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
                                            <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Debit</option>
                                            <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-6 col-md-2">
                                    <div class="d-flex align-items-center border-start ps-3 h-100">
                                        <i class="bi bi-grid-fill text-muted me-2"></i>
                                        <select class="form-select border-0 shadow-none bg-transparent py-0" name="status">
                                            <option value="">Statuses</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 d-flex align-items-center justify-content-md-end gap-2">
                                    <button type="button" class="btn btn-light rounded-pill px-3 shadow-none border d-flex align-items-center gap-2 transition-all hover-translate-y" 
                                        id="searchAiBtn" title="Ask AI about Date">
                                        <i class="bi bi-stars text-primary"></i>
                                        <span class="small fw-semibold text-muted d-none d-lg-inline">Ask AI</span>
                                    </button>

                                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background-color: #F26522; border-color: #F26522;">
                                        <i class="bi bi-funnel"></i>
                                    </button>

                                    <a href="{{ route('transactions') }}" class="btn btn-outline-light text-muted border border-secondary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History Table Card -->
            <div class="row g-0 g-md-4 position-relative">
                <!-- Loading Overlay -->
                <div id="filterOverlay" class="d-none position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center border-0 rounded-4" style="z-index: 10; background: rgba(255,255,255,0.7); backdrop-filter: blur(2px);">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-2"></div>
                        <p class="small fw-bold text-primary mb-0">Updating Records...</p>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-0 rounded-md-4 overflow-hidden">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3 rounded-0 rounded-top-md-4">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-receipt me-2"></i>Transaction Details</h5>
                            <span class="badge bg-white bg-opacity-25 text-white fw-semibold rounded-pill px-3">Arewa Smart</span>
                        </div>
                        <div class="card-body">

                            <!-- Transactions Table -->
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Description</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transactions as $index => $transaction)
                                            <tr style="cursor: pointer;" data-bs-toggle="modal"
                                                data-bs-target="#txModal{{ $transaction->id }}" class="transaction-row">
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span
                                                            class="fw-semibold">{{ $transaction->created_at->format('d M Y') }}</span>
                                                    </div>
                                                </td>
                                                <td><span
                                                        class="font-monospace small text-muted">{{ Str::limit($transaction->transaction_ref, 15) }}</span>
                                                </td>
                                                <td>
                                                    <span title="{{ $transaction->description }}" class="fw-medium">
                                                        {{ Str::limit($transaction->description, 30) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if(in_array($transaction->type, ['credit', 'refund', 'bonus', 'manual_credit']))
                                                        <span class="badge bg-success-subtle text-success fw-semibold">
                                                            {{ $transaction->type == 'manual_credit' ? 'Credit' : ucfirst($transaction->type) }}
                                                        </span>
                                                    @elseif(in_array($transaction->type, ['debit', 'manual_debit']))
                                                        <span class="badge bg-danger-subtle text-danger fw-semibold">
                                                            {{ $transaction->type == 'manual_debit' ? 'Debit' : 'Debit' }}
                                                        </span>
                                                    @else
                                                        <span
                                                            class="badge bg-info-subtle text-info fw-semibold">{{ ucfirst($transaction->type) }}</span>
                                                    @endif
                                                </td>
                                                <td
                                                    class="text-end fw-bold {{ in_array($transaction->type, ['credit', 'refund', 'bonus', 'manual_credit']) ? 'text-success' : 'text-danger' }}">
                                                    {{ in_array($transaction->type, ['credit', 'refund', 'bonus', 'manual_credit']) ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                                </td>
                                                <td class="text-center">
                                                    @if(in_array($transaction->status, ['completed', 'successful']))
                                                        <span class="badge bg-success-subtle text-success fw-bold px-3 py-2 rounded-pill badge-glass">
                                                            <i class="bi bi-check-circle-fill me-1"></i> Success
                                                        </span>
                                                    @elseif($transaction->status == 'failed')
                                                        <span class="badge bg-danger-subtle text-danger fw-bold px-3 py-2 rounded-pill badge-glass">
                                                            <i class="bi bi-x-circle-fill me-1"></i> Failed
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning fw-bold px-3 py-2 rounded-pill badge-glass">
                                                            <i class="bi bi-clock-history me-1"></i> {{ ucfirst($transaction->status) }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="bi bi-inbox text-muted fs-15 mb-3"></i>
                                                        <h6 class="fw-bold text-muted">No transactions found</h6>
                                                        <p class="text-muted small">Try adjusting your filters.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 d-flex justify-content-center">
                                {{ $transactions->withQueryString()->links('vendor.pagination.custom') }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Detail Modals (Placed outside the table to prevent shaking) -->
    @foreach ($transactions as $transaction)
        <div class="modal fade" id="txModal{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-primary text-white py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-receipt fs-15"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0">Transaction Detail</h5>
                                <small class="text-white-50">Ref: {{ $transaction->transaction_ref }}</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0 bg-white">
                        <div class="p-4">
                            <!-- Detail List -->
                            <div class="row g-3 mb-4">
                                <div class="col-6 col-md-3">
                                    <label class="small text-muted d-block mb-1">Amount</label>
                                    <div
                                        class="fw-bold fs-15 {{ in_array($transaction->type, ['credit', 'refund', 'bonus', 'manual_credit']) ? 'text-success' : 'text-danger' }}">
                                        {{ in_array($transaction->type, ['credit', 'refund', 'bonus', 'manual_credit']) ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="small text-muted d-block mb-1">Status</label>
                                    <span
                                        class="badge bg-{{ $transaction->status == 'completed' || $transaction->status == 'successful' ? 'success' : ($transaction->status == 'failed' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="small text-muted d-block mb-1">Date & Time</label>
                                    <div class="fw-semibold text-dark">
                                        {{ $transaction->created_at->format('d M Y, h:i A') }}
                                    </div>
                                </div>
                            </div>

                            <div
                                class="admin-comment-card p-3 rounded-3 bg-light border-start border-4 border-primary mb-4">
                                <label class="small text-uppercase text-muted fw-bold mb-2 d-block">Description</label>
                                <p class="text-dark mb-0 fw-medium">{{ $transaction->description }}</p>

                                @php
                                    $metadata = json_decode($transaction->metadata, true);
                                    $purchasedPin = $metadata['purchased_code'] ?? $metadata['purchased_pin'] ?? $metadata['pin'] ?? null;
                                @endphp

                                @if($purchasedPin)
                                    <div class="mt-3 p-2 bg-primary bg-opacity-10 border border-primary border-opacity-10 rounded">
                                        <label class="small text-uppercase text-primary fw-bold mb-1 d-block">Purchased PIN/Token</label>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="font-monospace fw-bold text-dark fs-12">{{ $purchasedPin }}</span>
                                            <button class="btn btn-sm btn-link text-primary p-0" onclick="navigator.clipboard.writeText('{{ $purchasedPin }}').then(() => Swal.fire({title: 'Copied!', text: 'PIN copied to clipboard', icon: 'success', timer: 1500, showConfirmButton: false}))">
                                                <i class="bi bi-clipboard"></i> Copy
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- AI Unified Section (Using standard card styling) -->
                            <div id="aiUnifiedSection{{ $transaction->id }}" class="mt-3">
                                <!-- AI Content injected here -->
                            </div>

                            <!-- AI Input (Using project standard look) -->
                            <div id="aiInputWrapper{{ $transaction->id }}" class="d-none mt-3">
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <input type="text" id="aiQuestion{{ $transaction->id }}"
                                        class="form-control border-0 px-3 py-2" placeholder="Ask follow-up details..."
                                        onkeydown="if(event.key === 'Enter') submitUserQuery('{{ $transaction->id }}', '{{ $transaction->description }}', '{{ $transaction->transaction_ref }}')">
                                    <button
                                        onclick="submitUserQuery('{{ $transaction->id }}', '{{ $transaction->description }}', '{{ $transaction->transaction_ref }}')"
                                        class="btn btn-primary px-3 border-0">
                                        <i class="bi bi-send-fill text-white"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="modal-footer bg-light border-top d-flex justify-content-between align-items-center py-3 px-4">
                        <div class="d-flex gap-2 w-100 justify-content-between align-items-center">
                            <button type="button" id="summarizeBtn{{ $transaction->id }}"
                                onclick="performSummarization('{{ $transaction->id }}', '{{ $transaction->description }}', '{{ $transaction->transaction_ref }}')"
                                class="btn btn-primary rounded-pill px-4 shadow-sm border-0 transition-all hover-translate-y"
                                style="background: linear-gradient(135deg, #F26522 0%, #ff8c52 100%);">
                                <i class="bi bi-stars me-2 text-white"></i> Summarize with AI
                            </button>
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                                data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        let chatHistories = {};
        let globalSummaryText = "";

        function performSummarization(id, description, reference) {
            const btn = document.getElementById(`summarizeBtn${id}`);
            const aiSection = document.getElementById(`aiUnifiedSection${id}`);
            const inputWrapper = document.getElementById(`aiInputWrapper${id}`);

            btn.classList.add('disabled');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>AI';

            addBubble(id, 'AI', '✨ *Workking*');

            fetch("{{ route('ai.summarize') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ comment: description, reference: reference })
            })
                .then(res => res.json())
                .then(data => {
                    aiSection.lastElementChild.remove();
                    if (data.success) {
                        addBubble(id, 'ai', data.answer);
                        chatHistories[id] = [{ role: 'assistant', content: data.answer }];
                        btn.classList.add('d-none');
                        inputWrapper.classList.remove('d-none');
                    } else {
                        addBubble(id, 'ai', '⚠️ ' + (data.message || 'AI service error.'));
                    }
                })
                .catch(err => {
                    if (aiSection.lastElementChild) aiSection.lastElementChild.remove();
                    addBubble(id, 'ai', '❌ Connection error.');
                })
                .finally(() => {
                    btn.classList.remove('disabled');
                    btn.innerHTML = '<i class="bi bi-stars me-2 text-white"></i> Summarize with AI';
                });
        }

        function submitUserQuery(id, description, reference) {
            const input = document.getElementById(`aiQuestion${id}`);
            const q = input.value.trim();
            if (!q) return;

            input.value = '';
            addBubble(id, 'user', q);
            addBubble(id, 'ai', '<div class="spinner-border spinner-border-sm"></div>');

            fetch("{{ route('ai.ask') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    comment: description,
                    question: q,
                    history: chatHistories[id] || [],
                    reference: reference
                })
            })
                .then(res => res.json())
                .then(data => {
                    const aiSection = document.getElementById(`aiUnifiedSection${id}`);
                    aiSection.lastElementChild.remove();
                    if (data.success) {
                        addBubble(id, 'ai', data.answer);
                        if (!chatHistories[id]) chatHistories[id] = [];
                        chatHistories[id].push({ role: 'user', content: q }, { role: 'assistant', content: data.answer });
                    } else {
                        addBubble(id, 'ai', 'Please try again.');
                    }
                })
                .catch(err => {
                    const aiSection = document.getElementById(`aiUnifiedSection${id}`);
                    if (aiSection.lastElementChild) aiSection.lastElementChild.remove();
                    addBubble(id, 'ai', 'Connection lost.');
                });
        }

        // --- GLOBAL AI ANALYZER ---
        const triggerGlobalAi = function () {
            const globalModal = new bootstrap.Modal(document.getElementById('globalAiModal'));
            const globalContent = document.getElementById('globalChatContent');

            // Open modal instantly
            globalModal.show();
            globalContent.innerHTML = ''; // Start fresh
            addGlobalBubble('ai', '<div class="d-flex align-items-center gap-2 text-primary fw-bold"><div class="spinner-border spinner-border-sm"></div> Analysing Activity...</div>');

            // Extract visible stats and context
            const totalCredits = document.querySelector('.bg-primary h3')?.innerText || '₦0.00';
            const totalDebits = document.querySelector('.text-danger h3')?.innerText || '₦0.00';
            const activityCount = document.querySelector('.text-dark h3')?.innerText || '0';

            let txSummary = `My current dashboard shows:\n- Total Credits: ${totalCredits}\n- Total Debits: ${totalDebits}\n- Total Movements: ${activityCount} record(s)\n\nSummarize my activity from these specific transactions:\n`;
            
            document.querySelectorAll('.transaction-row').forEach(row => {
                const desc = row.querySelector('.fw-medium').innerText;
                const amt = row.querySelector('.fw-bold').innerText;
                txSummary += `- ${desc}: ${amt}\n`;
            });
            globalSummaryText = txSummary;

            fetch("{{ route('ai.summarize') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ comment: txSummary })
            })
                .then(res => res.json())
                .then(data => {
                    globalContent.innerHTML = ''; // Clear loading
                    if (data.success) {
                        addGlobalBubble('ai', data.answer);
                        chatHistories['global'] = [{ role: 'assistant', content: data.answer }];
                    } else {
                        addGlobalBubble('ai', '⚠️ Could not generate summary. Please try again.');
                    }
                })
                .catch(() => {
                    globalContent.innerHTML = '';
                    addGlobalBubble('ai', '❌ Network error.');
                });
        };

        document.getElementById('globalAiSummarize')?.addEventListener('click', triggerGlobalAi);
        document.getElementById('searchAiBtn')?.addEventListener('click', triggerGlobalAi);

        function submitGlobalQuery() {
            const input = document.getElementById('globalAiQuestion');
            const q = input.value.trim();
            if (!q) return;

            input.value = '';
            addGlobalBubble('user', q);
            addGlobalBubble('ai', '<div class="spinner-border spinner-border-sm"></div>');

            fetch("{{ route('ai.ask') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    comment: globalSummaryText,
                    question: q,
                    history: chatHistories['global'] || []
                })
            })
                .then(res => res.json())
                .then(data => {
                    const contentArea = document.getElementById('globalChatContent');
                    contentArea.lastElementChild.remove();
                    if (data.success) {
                        addGlobalBubble('ai', data.answer);
                        if (!chatHistories['global']) chatHistories['global'] = [];
                        chatHistories['global'].push({ role: 'user', content: q }, { role: 'assistant', content: data.answer });
                    }
                });
        }

        function addBubble(id, type, text) {
            const aiSection = document.getElementById(`aiUnifiedSection${id}`);
            const b = document.createElement('div');
            // Use standard Bootstrap alert classes for professional look without custom CSS
            b.className = `alert ${type === 'ai' ? 'alert-primary' : 'bg-secondary-subtle'} mb-3 border-0 shadow-sm transition-all`;
            b.style.fontSize = '0.9rem';
            b.innerHTML = text.replace(/\n/g, '<br>');
            aiSection.appendChild(b);

            const modalBody = aiSection.closest('.modal-body');
            modalBody.scrollTo({ top: modalBody.scrollHeight, behavior: 'smooth' });
        }

        function addGlobalBubble(type, text) {
            const content = document.getElementById('globalChatContent');
            const b = document.createElement('div');
            b.className = `card mb-3 border-0 shadow-sm ${type === 'ai' ? 'bg-white border-start border-4 border-primary' : 'bg-light ms-auto'}`;
            b.style.maxWidth = type === 'ai' ? '100%' : '85%';
            b.innerHTML = `<div class="card-body p-3">${text.replace(/\n/g, '<br>')}</div>`;
            content.appendChild(b);

            const body = document.getElementById('globalAiModalBody');
            body.scrollTo({ top: body.scrollHeight, behavior: 'smooth' });
        }
    </script>
</x-app-layout>
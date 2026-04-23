<!-- Arewa Smart Global AI Assistant Widget -->
<style>
    :root {
        --ai-primary: #F26522;
        --ai-secondary: #ff8c52;
        --ai-bg: rgba(255, 255, 255, 0.9);
        --ai-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    #ai-assistant-toggle {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--ai-primary) 0%, var(--ai-secondary) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        cursor: pointer;
        box-shadow: var(--ai-shadow);
        z-index: 9999;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    #ai-assistant-toggle:hover {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 15px 35px rgba(242, 101, 34, 0.3);
    }

    #ai-chat-window {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 380px;
        height: 550px;
        background: var(--ai-bg);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 24px;
        box-shadow: var(--ai-shadow);
        z-index: 9998;
        display: none;
        flex-direction: column;
        overflow: hidden;
        animation: slideInUp 0.4s ease-out;
    }

    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .ai-chat-header {
        background: linear-gradient(135deg, var(--ai-primary) 0%, var(--ai-secondary) 100%);
        color: white;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ai-chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
        background: rgba(248, 249, 250, 0.5);
    }

    .ai-msg {
        max-width: 85%;
        padding: 12px 16px;
        border-radius: 18px;
        font-size: 0.9rem;
        line-height: 1.5;
        position: relative;
    }

    .ai-msg-ai {
        background: white;
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .ai-msg-user {
        background: var(--ai-primary);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
        box-shadow: 0 2px 5px rgba(242, 101, 34, 0.2);
    }

    .ai-chat-input-area {
        padding: 15px;
        background: white;
        border-top: 1px solid #eee;
    }

    .ai-input-group {
        display: flex;
        background: #f8f9fa;
        border-radius: 30px;
        padding: 5px 5px 5px 15px;
        border: 1px solid #eee;
    }

    .ai-input-group input {
        border: none;
        background: transparent;
        flex: 1;
        padding: 8px 0;
        font-size: 0.9rem;
        outline: none;
    }

    .ai-send-btn {
        width: 35px;
        height: 35px;
        background: var(--ai-primary);
        border: none;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .ai-send-btn:hover { background: var(--ai-secondary); }

    .loader-dots span { width: 6px; height: 6px; margin: 0 2px; background-color: var(--ai-primary); border-radius: 50%; display: inline-block; animation: bounce 0.6s infinite alternate; }
    @keyframes bounce { from { transform: translateY(0); } to { transform: translateY(-6px); } }

    @media (max-width: 480px) {
        #ai-chat-window { width: calc(100% - 40px); right: 20px; left: 20px; height: 70vh; bottom: 80px; }
        #ai-assistant-toggle { bottom: 20px; right: 20px; width: 50px; height: 50px; }
    }
</style>

<div id="ai-assistant-toggle" title="Ask Arewa Smart AI">
    <i class="bi bi-stars"></i>
</div>

<div id="ai-chat-window">
    <div class="ai-chat-header">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                <i class="bi bi-robot"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Smart AI Guide</h6>
                <small class="opacity-75" style="font-size: 0.7rem;">Official Assistant</small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" id="close-ai-chat" style="font-size: 0.7rem;"></button>
    </div>

    <div class="ai-chat-messages" id="ai-messages">
        <div class="ai-msg ai-msg-ai">
            Hello! I'm your **Arewa Smart AI Guide**. How can I help you manage your account or services today?
        </div>
    </div>

    <div class="ai-chat-input-area">
        <div class="ai-input-group">
            <input type="text" id="ai-global-input" placeholder="Type your question...">
            <button class="ai-send-btn" id="ai-global-send">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>
</div>

{{-- Existing PIN Modal --}}
@include('pages.pin')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('ai-assistant-toggle');
        const windowEl = document.getElementById('ai-chat-window');
        const closeBtn = document.getElementById('close-ai-chat');
        const input = document.getElementById('ai-global-input');
        const sendBtn = document.getElementById('ai-global-send');
        const messagesArea = document.getElementById('ai-messages');

        toggle.addEventListener('click', () => {
            windowEl.style.display = windowEl.style.display === 'flex' ? 'none' : 'flex';
            if (windowEl.style.display === 'flex') {
                messagesArea.scrollTop = messagesArea.scrollHeight;
                input.focus();
            }
        });

        closeBtn.addEventListener('click', () => {
            windowEl.style.display = 'none';
        });

        async function sendMessage() {
            const msg = input.value.trim();
            if (!msg) return;

            input.value = '';
            appendMessage('user', msg);
            
            const loader = appendLoader();
            
            try {
                const res = await fetch("{{ route('ai.chat') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: msg })
                });

                const data = await res.json();
                loader.remove();

                if (data.success) {
                    appendMessage('ai', data.answer);
                } else {
                    appendMessage('ai', '⚠️ ' + (data.message || 'Error occurred.'));
                }
            } catch (err) {
                loader.remove();
                appendMessage('ai', '❌ Connection problem.');
            }
        }

        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keydown', (e) => { if (e.key === 'Enter') sendMessage(); });

        function appendMessage(role, text) {
            const div = document.createElement('div');
            div.className = `ai-msg ai-msg-${role}`;
            
            let actionData = null;
            // Match JSON block at end of message
            const jsonMatch = text.match(/\{"action":.*?\}(?=\s*$)/s);
            if (role === 'ai' && jsonMatch) {
                try {
                    actionData = JSON.parse(jsonMatch[0]);
                    text = text.replace(jsonMatch[0], '').trim();
                } catch (e) { console.warn("Failed to parse AI action JSON"); }
            }

            // Simple markdown-ish bolding
            let content = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            content = content.replace(/\n/g, '<br>');
            div.innerHTML = `<div>${content}</div>`;

            if (actionData) {
                const btn = document.createElement('button');
                btn.className = 'btn btn-sm btn-light border mt-2 w-100 fw-bold text-primary py-2 rounded-3';
                btn.innerHTML = `<i class="bi bi-shield-lock-fill me-1"></i> Authorize & Pay`;
                btn.onclick = () => confirmAction(actionData);
                div.appendChild(btn);
            }

            messagesArea.appendChild(div);
            messagesArea.scrollTo({ top: messagesArea.scrollHeight, behavior: 'smooth' });
        }

        async function confirmAction(data) {
            // 1. Populate pinModal fields
            const amount = parseFloat(data.params.amount);
            document.getElementById('confirmAmount').textContent = '₦' + amount.toLocaleString(undefined, {minimumFractionDigits: 2});
            
            if (data.action === 'airtime') {
                document.getElementById('confirmAccountName').textContent = data.params.phone_number;
                document.getElementById('confirmBankName').textContent = data.params.network.toUpperCase() + ' Airtime';
                document.getElementById('confirmAccountNo').textContent = data.params.phone_number;
                document.getElementById('modalTitle').textContent = 'Confirm Airtime';
            } else if (data.action === 'p2p_transfer') {
                document.getElementById('confirmAccountName').textContent = data.params.description || 'P2P Transfer';
                document.getElementById('confirmBankName').textContent = 'Arewa Smart User';
                document.getElementById('confirmAccountNo').textContent = data.params.wallet_id;
                document.getElementById('modalTitle').textContent = 'Confirm Transfer';
            }

            // 2. Show Modal
            const pinModalEl = document.getElementById('pinModal');
            const pinModal = bootstrap.Modal.getOrCreateInstance(pinModalEl);
            pinModal.show();

            // 3. Handle PIN Authorization
            const confirmBtn = document.getElementById('confirmPinBtn');
            confirmBtn.onclick = async function() {
                const pin = document.getElementById('pinInput').value;
                if (!pin || pin.length !== 5) {
                    const errEl = document.getElementById('pinError');
                    const errText = document.getElementById('pinErrorText');
                    if (errEl) errEl.classList.remove('d-none');
                    if (errText) errText.textContent = 'Please enter your 5-digit PIN.';
                    return;
                }

                // Show loading on btn
                this.disabled = true;
                const ldr = document.getElementById('pinLoader');
                const ico = document.getElementById('pinBtnIcon');
                const txt = document.getElementById('confirmPinText');
                if (ldr) ldr.classList.remove('d-none');
                if (ico) ico.classList.add('d-none');
                if (txt) txt.textContent = 'Verifying...';

                try {
                    // Step A: Verify PIN
                    const verifyRes = await fetch("{{ route('verify.pin') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ pin })
                    });
                    const verifyData = await verifyRes.json();

                    if (!verifyData.valid) {
                        const errEl = document.getElementById('pinError');
                        const errText = document.getElementById('pinErrorText');
                        if (errEl) errEl.classList.remove('d-none');
                        if (errText) errText.textContent = 'Incorrect Transaction PIN.';
                        this.disabled = false;
                        if (ldr) ldr.classList.add('d-none');
                        if (ico) ico.classList.remove('d-none');
                        if (txt) txt.textContent = 'Authorize Now';
                        return;
                    }

                    // Step B: Execute Transaction
                    pinModal.hide();
                    
                    let route, params;
                    if (data.action === 'airtime') {
                        route = "{{ route('buyairtime') }}";
                        params = { 
                            network: data.params.network, 
                            mobileno: data.params.phone_number, 
                            amount: data.params.amount 
                        };
                    } else {
                        route = "{{ route('transfer.process') }}";
                        params = { 
                            wallet_id: data.params.wallet_id, 
                            amount: data.params.amount, 
                            description: data.params.description,
                            pin: pin
                        };
                    }


                    executeAction(route, params);

                } catch (err) {
                    console.error("Auth Error", err);
                    alert("Authorization failed. Please try again.");
                    this.disabled = false;
                }
            };
        }

        async function executeAction(route, params) {
            const loader = appendLoader();
            try {
                const res = await fetch(route, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(params)
                });

                const result = await res.json();
                loader.remove();

                if (result.status === 'success' || result.success === true) {
                    const ref = result.data.ref || (result.data.transaction ? result.data.transaction.transaction_ref : null);
                    appendMessage('ai', `✅ **Transaction Successful**\n\n${result.message || 'Payment completed.'}`);
                    
                    if (ref) {
                        const lastMsg = messagesArea.lastElementChild;
                        if (lastMsg) {
                            const btnContainer = document.createElement('div');
                            btnContainer.className = 'mt-3 d-grid gap-2';
                            
                            const viewBtn = document.createElement('button');
                            viewBtn.className = 'btn btn-sm btn-primary fw-bold rounded-pill shadow-sm py-2';
                            viewBtn.innerHTML = '<i class="bi bi-receipt me-1"></i> View Receipt';
                            viewBtn.onclick = () => showReceipt(ref);
                            
                            const fullReceiptBtn = document.createElement('a');
                            fullReceiptBtn.href = `{{ route('thankyou') }}?ref=${ref}`;
                            fullReceiptBtn.target = '_blank';
                            fullReceiptBtn.className = 'btn btn-sm btn-outline-primary fw-bold rounded-pill py-2';
                            fullReceiptBtn.innerHTML = '<i class="bi bi-box-arrow-up-right me-1"></i> Full Receipt';

                            btnContainer.appendChild(viewBtn);
                            btnContainer.appendChild(fullReceiptBtn);
                            lastMsg.appendChild(btnContainer);
                        }
                    }

                    Swal.fire({ icon: 'success', title: 'Payment Confirmed', text: result.message, timer: 4000 });
                } else {
                    appendMessage('ai', `❌ **Transaction Failed**\n\n${result.message || 'Error occurred.'}`);
                    Swal.fire({ icon: 'error', title: 'Payment Failed', text: result.message });
                }
            } catch (err) {
                loader.remove();
                appendMessage('ai', '❌ System error during execution. Please check your transaction history.');
            }
        }

        async function showReceipt(ref) {
            const loader = appendLoader();
            try {
                const res = await fetch("{{ route('ai.receipt') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ref })
                });
                const data = await res.json();
                loader.remove();

                if (data.success) {
                    appendMessage('ai', 'Here is your receipt detail:');
                    const lastMsg = messagesArea.lastElementChild;
                    const container = document.createElement('div');
                    container.id = 'chat-receipt-' + ref;
                    container.innerHTML = data.html;
                    lastMsg.appendChild(container);
                    messagesArea.scrollTo({ top: messagesArea.scrollHeight, behavior: 'smooth' });
                }
            } catch (err) {
                loader.remove();
                console.error("Receipt error", err);
            }
        }

        function appendLoader() {
            const div = document.createElement('div');
            div.className = 'ai-msg ai-msg-ai';
            div.innerHTML = '<div class="loader-dots"><span></span><span></span><span></span></div>';
            messagesArea.appendChild(div);
            messagesArea.scrollTo({ top: messagesArea.scrollHeight, behavior: 'smooth' });
            return div;
        }

    });

    // Helper scripts for Chat Receipt (PDF/Share)
    // Load libraries if not already present
    if (!window.html2canvas) {
        const s1 = document.createElement('script');
        s1.src = "https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js";
        document.head.appendChild(s1);
    }
    if (!window.jspdf) {
        const s2 = document.createElement('script');
        s2.src = "https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js";
        document.head.appendChild(s2);
    }

    async function downloadChatReceipt(ref) {
        await processChatReceipt(ref, 'download');
    }

    async function shareChatReceipt(ref) {
        await processChatReceipt(ref, 'share');
    }

    async function processChatReceipt(ref, mode) {
        const element = document.getElementById('chat-receipt-' + ref).querySelector('.ai-receipt-card');
        if (!element) return;

        // Temporarily hide buttons for capture
        const btns = element.querySelector('.d-grid');
        btns.style.display = 'none';

        try {
            const canvas = await html2canvas(element, { scale: 2, useCORS: true, backgroundColor: '#ffffff' });
            const imgData = canvas.toDataURL('image/jpeg', 0.95);
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'px',
                format: [canvas.width / 2, canvas.height / 2]
            });

            pdf.addImage(imgData, 'JPEG', 0, 0, canvas.width / 2, canvas.height / 2);
            const pdfBlob = pdf.output('blob');
            const fileName = `Receipt_${ref}.pdf`;

            if (mode === 'share' && navigator.share) {
                const file = new File([pdfBlob], fileName, { type: 'application/pdf' });
                await navigator.share({ files: [file], title: 'Receipt', text: 'My Arewa Smart Receipt' });
            } else {
                const link = document.createElement('a');
                link.href = URL.createObjectURL(pdfBlob);
                link.download = fileName;
                link.click();
            }
        } catch (err) {
            console.error("PDF Error", err);
            alert("Failed to generate PDF. You can use 'Full Receipt' to download from the main page.");
        } finally {
            btns.style.display = 'grid';
        }
    }
</script>



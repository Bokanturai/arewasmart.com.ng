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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('ai-assistant-toggle');
        const window = document.getElementById('ai-chat-window');
        const closeBtn = document.getElementById('close-ai-chat');
        const input = document.getElementById('ai-global-input');
        const sendBtn = document.getElementById('ai-global-send');
        const messagesArea = document.getElementById('ai-messages');

        toggle.addEventListener('click', () => {
            window.style.display = window.style.display === 'flex' ? 'none' : 'flex';
            if (window.style.display === 'flex') {
                messagesArea.scrollTop = messagesArea.scrollHeight;
                input.focus();
            }
        });

        closeBtn.addEventListener('click', () => {
            window.style.display = 'none';
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
            // Simple markdown-ish bolding
            let content = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            content = content.replace(/\n/g, '<br>');
            div.innerHTML = content;
            messagesArea.appendChild(div);
            messagesArea.scrollTo({ top: messagesArea.scrollHeight, behavior: 'smooth' });
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
</script>

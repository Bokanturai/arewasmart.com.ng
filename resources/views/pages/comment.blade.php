<!-- AREWA SMART: Comment Response Modal -->
<style>
    .message-bubble {
        padding: 12px 16px;
        margin-bottom: 12px;
        border-radius: 18px;
        font-size: 0.95rem;
        line-height: 1.5;
        max-width: 90%;
        position: relative;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        animation: fadeIn 0.3s ease-out;
    }
  
    .message-user {
        background: linear-gradient(135deg, #F26522 0%, #ff8c52 100%);
        color: white;
        border-bottom-right-radius: 4px;
        align-self: flex-end;
        margin-left: auto;
    }
   
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .loader-dots span {
        width: 8px;
        height: 8px;
        margin: 0 2px;
        background-color: #F26522;
        border-radius: 50%;
        display: inline-block;
        animation: bounce 0.6s infinite alternate;
    }
    .loader-dots span:nth-child(2) { animation-delay: 0.2s; }
    .loader-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes bounce {
        from { transform: translateY(0); }
        to { transform: translateY(-8px); }
    }
</style>

<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden position-relative">

            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;">
                        <i class="bi bi-chat-left-dots fs-15"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white mb-0 fw-bold" id="commentModalLabel">Administrator Feedback
                        </h5>
                        <small class="text-white-50">Official Response from Arewa Smart</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-0 bg-white custom-scrollbar" style="max-height: 80vh; overflow-y: auto;">
                <div id="mainCommentView" class="p-4">
                    <!-- Unified Content Card -->
                    <div
                        class="admin-comment-card p-4 rounded-4 bg-light border-start border-4 border-primary shadow-sm mb-4">
                        <label class="small text-uppercase text-muted fw-bold mb-2 d-block">Official Feedback</label>
                        <div id="commentModalBody" class="text-dark lh-base mb-0"
                            style="font-size: 1.05rem; white-space: pre-wrap;">
                            <!-- Official comment injected here -->
                        </div>

                        <!-- AI Analysis & Chat injected here -->
                        <div id="aiUnifiedSection" class="chat-container mt-4"></div>
                    </div>

                    <!-- AI Input Wrapper (Initially hidden) -->
                    <div id="aiInputWrapper" class="d-none mt-2 animate-fade-in">
                        <div
                            class="input-group shadow-sm rounded-pill overflow-hidden border border-light bg-white p-1">
                            <input type="text" id="aiQuestion" class="form-control border-0 px-4 py-2"
                                placeholder="Ask AI to clarify or explain more..." style="font-size: 0.95rem;">
                            <button id="sendAiQuestion"
                                class="btn btn-primary px-4 border-0 rounded-pill d-flex align-items-center"
                                style="background: #F26522;">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center py-3 px-4">
                <div class="d-flex gap-2">
                    <button type="button" id="summarizeWithAi"
                        class="btn btn-primary rounded-pill px-4 shadow-sm border-0 transition-all hover-translate-y"
                        style="background: linear-gradient(135deg, #F26522 0%, #ff8c52 100%);">
                        <i class="bi bi-stars me-2"></i> Summarize with AI
                    </button>
                    <a href="#" id="downloadBtn"
                        class="btn btn-success rounded-pill px-4 shadow-sm d-none transition-all hover-translate-y">
                        <i class="bi bi-download me-2"></i> Download File
                    </a>
                </div>
                <div id="encouragement" class="text-muted small fw-medium fst-italic"></div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ENCOURAGEMENT_MESSAGES = [
            "Protecting your growth at Arewa Smart.",
            "Believe in your progress, stay persistent.",
            "Excellence is standard at Arewa Smart Idea Ltd.",
            "Great things take time — stay patient.",
            "Your satisfaction is our primary priority.",
            "Stay focused — progress is happening.",
            "Serving Northern Excellence with Smart Tech."
        ];

        let currentComment = '';
        let currentReferenceId = '';

        const ui = {
            modal: document.getElementById("commentModal"),
            modalBody: document.getElementById("commentModalBody"),
            encouragement: document.getElementById("encouragement"),
            downloadBtn: document.getElementById("downloadBtn"),
            summarizeBtn: document.getElementById("summarizeWithAi"),
            aiSection: document.getElementById("aiUnifiedSection"),
            aiInputWrapper: document.getElementById("aiInputWrapper"),
            aiInput: document.getElementById("aiQuestion"),
            aiSend: document.getElementById("sendAiQuestion")
        };

        ui.modal.addEventListener("show.bs.modal", handleOpen);
        ui.modal.addEventListener("hidden.bs.modal", handleReset);
        ui.summarizeBtn.addEventListener("click", performSummarization);
        ui.aiSend.addEventListener("click", submitUserQuery);
        ui.aiInput.addEventListener("keydown", (e) => { if (e.key === 'Enter') submitUserQuery(); });

        async function handleOpen(event) {
            const btn = event.relatedTarget;
            currentComment = btn.getAttribute('data-comment');
            currentReferenceId = btn.getAttribute('data-reference') || btn.getAttribute('data-ref') || '';
            const fileUrl = btn.getAttribute('data-file-url');
            const approvedBy = btn.getAttribute('data-approved-by');

            ui.modalBody.innerHTML = `<div>${currentComment || 'No feedback provided yet.'}</div> ${approvedBy ? `<div class="mt-3 text-muted small border-top pt-2 opacity-75"><i class="bi bi-patch-check-fill text-primary"></i> Verified by ${approvedBy}</div>` : ''}`;

            ui.aiSection.innerHTML = '<div class="text-center py-2"><span class="spinner-border spinner-border-sm text-primary opacity-50"></span></div>';
            ui.summarizeBtn.classList.add('d-none');
            ui.aiInputWrapper.classList.add('d-none');

            if (fileUrl && fileUrl !== 'null' && fileUrl.trim()) {
                ui.downloadBtn.href = fileUrl;
                ui.downloadBtn.classList.remove('d-none');
            } else {
                ui.downloadBtn.classList.add('d-none');
            }

            ui.encouragement.innerText = ENCOURAGEMENT_MESSAGES[Math.floor(Math.random() * ENCOURAGEMENT_MESSAGES.length)];

            // Fetch History
            try {
                const res = await fetch(`{{ route('ai.history') }}?reference=${currentReferenceId}`);
                const data = await res.json();
                ui.aiSection.innerHTML = '';
                
                if (data.success && data.history.length > 0) {
                    data.history.forEach(chat => {
                        addBubble(chat.role === 'assistant' ? 'ai' : 'user', chat.content);
                    });
                    ui.aiInputWrapper.classList.remove('d-none');
                } else {
                    ui.summarizeBtn.classList.remove('d-none');
                }
            } catch (err) {
                ui.aiSection.innerHTML = '';
                ui.summarizeBtn.classList.remove('d-none');
            }
        }

        function handleReset() {
            ui.aiSection.innerHTML = '';
            ui.aiInput.value = '';
            ui.aiInputWrapper.classList.add('d-none');
        }

        async function performSummarization() {
            if (!currentComment) return;

            ui.summarizeBtn.classList.add('disabled');
            ui.summarizeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Analyzing...';

            addBubble('ai', '<div class="loader-dots"><span></span><span></span><span></span></div>');

            try {
                const res = await fetch("{{ route('ai.summarize') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    body: JSON.stringify({ comment: currentComment, reference: currentReferenceId })
                });

                const data = await res.json();
                ui.aiSection.lastElementChild.remove();

                if (data.success) {
                    addBubble('ai', data.answer);
                    ui.summarizeBtn.classList.add('d-none');
                    ui.aiInputWrapper.classList.remove('d-none');
                } else {
                    addBubble('ai', '⚠️ ' + (data.message || 'AI service error.'));
                }
            } catch (err) {
                if (ui.aiSection.lastElementChild) ui.aiSection.lastElementChild.remove();
                addBubble('ai', '❌ Connection error.');
            } finally {
                ui.summarizeBtn.classList.remove('disabled');
                ui.summarizeBtn.innerHTML = '<i class="bi bi-stars me-2"></i> Summarize with AI';
            }
        }

        async function submitUserQuery() {
            const q = ui.aiInput.value.trim();
            if (!q) return;

            ui.aiInput.value = '';
            addBubble('user', q);
            addBubble('ai', '<div class="loader-dots"><span></span><span></span><span></span></div>');

            try {
                const res = await fetch("{{ route('ai.ask') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    body: JSON.stringify({ comment: currentComment, question: q, reference: currentReferenceId })
                });

                const data = await res.json();
                ui.aiSection.lastElementChild.remove();

                if (data.success) {
                    addBubble('ai', data.answer);
                } else {
                    addBubble('ai', 'Please try again in a few seconds.');
                }
            } catch (err) {
                if (ui.aiSection.lastElementChild) ui.aiSection.lastElementChild.remove();
                addBubble('ai', 'Connection lost.');
            }
        }

        function addBubble(type, text) {
            const b = document.createElement('div');
            b.className = `message-bubble message-${type}`;
            b.innerHTML = text.replace(/\n/g, '<br>');
            ui.aiSection.appendChild(b);
            
            const modalBody = document.querySelector('.modal-body');
            modalBody.scrollTo({ top: modalBody.scrollHeight, behavior: 'smooth' });
        }
    });
</script>
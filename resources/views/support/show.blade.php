<x-app-layout>
    <title>Arewa Smart - Ticket #{{ $ticket->ticket_reference }}</title>
    <style>
        .chat-wrapper {
            height: calc(100vh - 180px);
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            background: #fff;
        }

        .glass-footer {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-top: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        .message-bubble {
            max-width: 85%;
            border-radius: 18px;
            position: relative;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03) !important;
        }

        .message-admin {
            background-color: #ffffff;
            border-bottom-left-radius: 4px !important;
            animation: bubble-in-left 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .message-user {
            background-color: #e3f2fd;
            border-bottom-right-radius: 4px !important;
            animation: bubble-in-right 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes bubble-in-left {
            from { opacity: 0; transform: translateX(-20px) scale(0.95); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }

        @keyframes bubble-in-right {
            from { opacity: 0; transform: translateX(20px) scale(0.95); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }

        @media (max-width: 768px) {
            .chat-wrapper {
                height: calc(100vh - 120px);
            }
            .message-bubble {
                max-width: 92% !important;
            }
            .page-title {
                display: none !important;
            }
        }

        .bg-chat {
            background-color: #f8f9fa;
            background-image: radial-gradient(#dee2e6 0.5px, transparent 0.5px);
            background-size: 20px 20px;
        }

        .typing-indicator {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
        }

        .typing-dot {
            width: 6px;
            height: 6px;
            background: #0d6efd;
            border-radius: 50%;
            margin: 0 2px;
            animation: typing 1.4s infinite;
            opacity: 0.4;
        }

        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        .focus-within-primary {
            transition: all 0.3s ease;
            border: 1px solid #eee;
        }

        .focus-within-primary:focus-within {
            border-color: var(--bs-primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15) !important;
            background: #fff !important;
        }

        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
            30% { transform: translateY(-4px); opacity: 1; }
        }
    </style>

    <div class="page-body">
        <div class="container-fluid px-0 px-md-3">
            <div class="page-title mb-3">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 mb-2 mb-md-0">
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('support.index') }}"
                                class="btn btn-sm btn-light border rounded-circle d-md-none">
                                <i class="ti ti-arrow-left"></i>
                            </a>
                            <div>
                                <h4 class="fw-bold text-primary mb-0">Ticket #{{ $ticket->ticket_reference }}</h4>
                                <p class="text-muted small mb-0 d-none d-md-block">
                                    {{ Str::limit($ticket->subject, 60) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="col-12 col-md-6 text-md-end d-flex justify-content-between justify-content-md-end align-items-center gap-2">
                        <span id="ticket-status-badge" class="badge rounded-pill bg-{{ match ($ticket->status) {
    'open' => 'success',
    'answered' => 'primary',
    'customer_reply' => 'warning',
    'closed' => 'secondary',
    default => 'info'
} }} px-3 py-2">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>

                        <div class="d-flex gap-2">
                            @if($ticket->status !== 'closed')
                                <button onclick="closeTicket('{{ $ticket->ticket_reference }}')"
                                    class="btn btn-outline-danger btn-sm rounded-pill d-inline-flex align-items-center">
                                    <i class="ti ti-circle-x me-1"></i>
                                    <span class="d-none d-sm-inline">Close Ticket</span>
                                    <span class="d-sm-none">Close</span>
                                </button>
                            @endif

                            <a href="{{ route('support.index') }}"
                                class="btn btn-outline-secondary btn-sm rounded-pill d-none d-md-inline-flex align-items-center">
                                <i class="ti ti-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-0 px-md-3">
            <div class="row g-0 g-md-4">
                <div class="col-12 col-lg-12">
                    <div class="card shadow-lg border-0 chat-wrapper" style="border-radius: 20px; overflow: hidden;">
                        <!-- Chat Header -->
                        <div class="card-header bg-white border-bottom py-3" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 45px; height: 45px; background: rgba(13, 110, 253, 0.1);">
                                        <i class="ti ti-robot fs-15"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Support Assistant <span class="badge bg-soft-info text-info fw-normal ms-1" style="font-size: 0.6rem; letter-spacing: 0.5px;">AI POWERED</span></h6>
                                        <small class="text-success"><i class="ti ti-circle-filled fs-xs me-1"></i>
                                            Automated Helper & Ready</small>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li><a class="dropdown-item" href="#"><i
                                                    class="ti ti-info-circle me-2"></i>Ticket Details</a></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="javascript:void(0)"
                                                onclick="closeTicket('{{ $ticket->ticket_reference }}')">
                                                <i class="ti ti-circle-x me-2"></i>Close Ticket
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Messages Area -->
                        <div class="card-body bg-chat overflow-auto p-3 p-md-4" id="messages-area" style="flex: 1;">
                            @foreach($ticket->messages as $message)
                                <div class="d-flex mb-4 {{ $message->is_admin_reply ? 'justify-content-start' : 'justify-content-end' }}"
                                    data-id="{{ $message->id }}">
                                    @if($message->is_admin_reply)
                                        <div class="me-2 d-none d-md-block">
                                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 35px; height: 35px;">
                                                <i class="ti ti-headset"></i>
                                            </div>
                                        </div>
                                    @endif

                                    <div
                                        class="card border-0 message-bubble {{ $message->is_admin_reply ? 'message-admin' : 'message-user' }}">
                                        <div class="card-body p-3">
                                            <p class="mb-1 text-dark" style="line-height: 1.5;">{{ $message->message }}</p>

                                            @if($message->attachment_url)
                                                <div class="mt-2 bg-light p-2 rounded border-start border-primary border-4">
                                                    @php
                                                        $extension = strtolower(pathinfo($message->attachment, PATHINFO_EXTENSION));
                                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    @endphp

                                                    @if($isImage)
                                                        <div class="mb-1">
                                                            <a href="{{ $message->attachment_url }}" target="_blank">
                                                                <img src="{{ $message->attachment_url }}" alt="Attachment"
                                                                    class="img-fluid rounded border shadow-sm"
                                                                    style="max-width: 100%; max-height: 250px;">
                                                            </a>
                                                        </div>
                                                    @else
                                                        <a href="{{ $message->attachment_url }}" target="_blank"
                                                            class="d-flex align-items-center text-decoration-none">
                                                            <i class="ti ti-file-text fs-15 me-2"></i>
                                                            <span class="small fw-semibold">View Attachment</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="text-end mt-1">
                                                <small class="text-muted" style="font-size: 0.65rem; opacity: 0.8;">
                                                    {{ $message->created_at->format('h:i A') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    @if(!$message->is_admin_reply)
                                        <div class="ms-2 d-none d-md-block">
                                            <div class="avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 35px; height: 35px;">
                                                <i class="ti ti-user"></i>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <!-- Typing Indicator -->
                            <div id="typing-indicator" class="d-none">
                                <div class="d-flex mb-4 justify-content-start">
                                    <div class="me-2 d-none d-md-block">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                            style="width: 35px; height: 35px;">
                                            <i class="ti ti-headset"></i>
                                        </div>
                                    </div>
                                    <div class="typing-indicator">
                                        <span class="small text-muted me-2">AI is typing</span>
                                        <div class="typing-dot"></div>
                                        <div class="typing-dot"></div>
                                        <div class="typing-dot"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reply Area -->
                        <div class="card-footer p-3 p-md-4 glass-footer" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                            @if($ticket->status !== 'closed')
                                <form id="reply-form" action="{{ route('support.reply', $ticket->ticket_reference) }}"
                                    method="POST" enctype="multipart/form-data" class="w-100">
                                    @csrf
                                    <div class="mb-2 text-center">
                                        <span class="badge bg-soft-warning text-dark border-0 small px-3 py-1" style="font-size: 0.75rem;">
                                            <i class="ti ti-shield-lock me-1"></i> Safety Tip: Never share your Password or PIN here.
                                        </span>
                                    </div>
                                    <div
                                        class="d-flex align-items-end gap-2 bg-light p-2 rounded-4 border shadow-sm transition-all focus-within-primary w-100">
                                        <input type="file" name="attachment" id="replyAttachment" class="d-none"
                                            accept=".jpg,.jpeg,.png,.pdf">

                                        <button type="button"
                                            class="btn btn-white border rounded-circle flex-shrink-0 shadow-sm"
                                            style="width: 45px; height: 45px;"
                                            onclick="document.getElementById('replyAttachment').click()"
                                            title="Attach File">
                                            <i class="ti ti-paperclip fs-18 text-muted"></i>
                                        </button>

                                        <div class="flex-grow-1 position-relative">
                                            <textarea name="message" id="chatMessage"
                                                class="form-control border-0 bg-transparent shadow-none px-2"
                                                placeholder="Type your message here..." rows="1" required
                                                style="resize: none; overflow-y: hidden; min-height: 45px; border-radius: 12px; padding-top: 10px;"></textarea>
                                            <small class="text-primary mt-1 d-block fw-semibold" style="font-size: 0.75rem;"
                                                id="fileNameDisplay"></small>
                                        </div>

                                        <button type="submit" id="send-btn"
                                            class="btn btn-primary rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center shadow-sm"
                                            style="width: 45px; height: 45px;">
                                            <i class="ti ti-send-2 fs-15"></i>
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="bg-light p-4 rounded-4 text-center border-dashed">
                                    <i class="ti ti-lock fs-1 text-muted mb-2"></i>
                                    <h6 class="fw-bold mb-0">Conversation Closed</h6>
                                    <p class="text-muted small mb-0">This ticket has been resolved and is no longer
                                        accepting replies.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // State management
            let lastMessageId = {{ $ticket->messages->last()?->id ?? 0 }};
            const messagesArea = document.getElementById('messages-area');
            const replyForm = document.getElementById('reply-form');
            const chatMessage = document.getElementById('chatMessage');
            const typingIndicator = document.getElementById('typing-indicator');

            // Date formatter utility
            function formatTime(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            // Scroll to bottom
            function scrollToBottom() {
                messagesArea.scrollTo({
                    top: messagesArea.scrollHeight,
                    behavior: 'smooth'
                });
            }

            // Append a new message to the chat
            function appendMessage(msg) {
                // Prevent duplicate appending
                if (document.querySelector(`[data-id="${msg.id}"]`)) return;

                const isHost = !msg.is_admin_reply;
                const msgHtml = `
                    <div class="d-flex mb-4 ${msg.is_admin_reply ? 'justify-content-start' : 'justify-content-end'}" data-id="${msg.id}" style="animation: fadeIn 0.3s ease-out;">
                        ${msg.is_admin_reply ? `
                            <div class="me-2 d-none d-md-block">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px;">
                                    <i class="ti ti-headset"></i>
                                </div>
                            </div>
                        ` : ''}

                        <div class="card border-0 message-bubble ${msg.is_admin_reply ? 'message-admin' : 'message-user'}" style="background-color: ${msg.is_admin_reply ? '#ffffff' : '#e3f2fd'}">
                            <div class="card-body p-3">
                                <p class="mb-1 text-dark" style="line-height: 1.5;">${msg.message}</p>
                                
                                ${msg.attachment_url ? `
                                    <div class="mt-2 bg-light p-2 rounded border-start border-primary border-4">
                                        ${msg.attachment_url.match(/\.(jpg|jpeg|png|gif|webp)$/i) ? `
                                            <div class="mb-1">
                                                <a href="${msg.attachment_url}" target="_blank">
                                                    <img src="${msg.attachment_url}" alt="Attachment" class="img-fluid rounded border shadow-sm" style="max-width: 100%; max-height: 250px;">
                                                </a>
                                            </div>
                                        ` : `
                                            <a href="${msg.attachment_url}" target="_blank" class="d-flex align-items-center text-decoration-none">
                                                <i class="ti ti-file-text fs-15 me-2"></i>
                                                <span class="small fw-semibold">View Attachment</span>
                                            </a>
                                        `}
                                    </div>
                                ` : ''}
                                
                                <div class="text-end mt-1">
                                    <small class="text-muted" style="font-size: 0.65rem; opacity: 0.8;">
                                        ${formatTime(msg.created_at)}
                                    </small>
                                </div>
                            </div>
                        </div>

                        ${!msg.is_admin_reply ? `
                            <div class="ms-2 d-none d-md-block">
                                <div class="avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px;">
                                    <i class="ti ti-user"></i>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                `;

                // Insert before the typing indicator
                typingIndicator.insertAdjacentHTML('beforebegin', msgHtml);
                scrollToBottom();
            }

            // Polling for updates
            function pollUpdates() {
                fetch(`{{ route('support.updates', $ticket->ticket_reference) }}?last_message_id=${lastMessageId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Show/Hide typing indicator
                        if (data.is_typing) {
                            typingIndicator.classList.remove('d-none');
                            // If appending a message, we might want to auto-scroll if user is near bottom
                            if (messagesArea.scrollTop + messagesArea.clientHeight >= messagesArea.scrollHeight - 100) {
                                scrollToBottom();
                            }
                        } else {
                            typingIndicator.classList.add('d-none');
                        }

                        // Process new messages
                        if (data.messages && data.messages.length > 0) {
                            data.messages.forEach(msg => {
                                if (msg.id > lastMessageId) {
                                    lastMessageId = msg.id;
                                    appendMessage(msg);
                                }
                            });
                        }
                    })
                    .catch(console.error);
            }

            // Optimized Polling with Page Visibility API
            let pollInterval;
            function startPolling() {
                if (!pollInterval) {
                    pollInterval = setInterval(pollUpdates, 4000);
                }
            }

            function stopPolling() {
                clearInterval(pollInterval);
                pollInterval = null;
            }

            // Handle visibility change
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopPolling();
                } else {
                    startPolling();
                    pollUpdates(); // Update immediately upon return
                }
            });

            // Initial start
            startPolling();

            // Handle reply form via AJAX
            if (replyForm) {
                replyForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const form = this;
                    const sendBtn = document.getElementById('send-btn');
                    const originalBtnContent = sendBtn.innerHTML;
                    const formData = new FormData(form);

                    // Pre-validation local append (optional, but let's stick to server response for accuracy)
                    sendBtn.disabled = true;
                    sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                form.reset();
                                document.getElementById('fileNameDisplay').textContent = '';
                                chatMessage.style.height = '45px';
                                // Status badge update
                                if (data.ticket_status) {
                                    const badge = document.getElementById('ticket-status-badge');
                                    badge.textContent = data.ticket_status.replace('_', ' ').charAt(0).toUpperCase() + data.ticket_status.replace('_', ' ').slice(1);
                                }
                                // Manually trigger a poll or just append the message if returned
                                if (data.message) {
                                    lastMessageId = data.message.id;
                                    appendMessage(data.message);
                                }
                                // Append AI reply immediately since it was processed synchronously
                                if (data.ai_reply) {
                                    setTimeout(() => {
                                        lastMessageId = data.ai_reply.id;
                                        appendMessage(data.ai_reply);
                                    }, 500); // Slight delay for natural feel
                                }
                            } else {
                                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Failed to send message.' });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.' });
                        })
                        .finally(() => {
                            sendBtn.disabled = false;
                            sendBtn.innerHTML = originalBtnContent;
                        });
                });
            }

            // Auto-expand textarea & Shift+Enter logic
            if (chatMessage) {
                chatMessage.addEventListener('input', function () {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 150) + 'px';
                    this.style.overflowY = this.scrollHeight > 150 ? 'auto' : 'hidden';
                });

                chatMessage.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' && !e.shiftKey && window.innerWidth > 768) {
                        e.preventDefault();
                        if (this.value.trim() !== '') {
                            replyForm.dispatchEvent(new Event('submit'));
                        }
                    }
                });
            }

            // File attachment display
            const fileInput = document.getElementById('replyAttachment');
            if (fileInput) {
                fileInput.addEventListener('change', function (e) {
                    const fileName = e.target.files[0]?.name;
                    document.getElementById('fileNameDisplay').textContent = fileName ? '📎 ' + fileName : '';
                });
            }

            // Close Ticket functionality
            function closeTicket(reference) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to close this support ticket? You won't be able to reply anymore.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, close it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/support/${reference}/close`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Closed!',
                                        text: 'Your ticket has been closed.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Failed to close the ticket.' });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.' });
                            });
                    }
                });
            }

            // Initial scroll
            document.addEventListener('DOMContentLoaded', scrollToBottom);

            // Clean up interval on leave
            window.addEventListener('beforeunload', () => clearInterval(pollInterval));
        </script>

        <style>
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .focus-within-primary:focus-within {
                border-color: var(--bs-primary) !important;
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
            }

            #messages-area::-webkit-scrollbar {
                width: 6px;
            }

            #messages-area::-webkit-scrollbar-track {
                background: transparent;
            }

            #messages-area::-webkit-scrollbar-thumb {
                background: #dee2e6;
                border-radius: 10px;
            }

            #messages-area::-webkit-scrollbar-thumb:hover {
                background: #adb5bd;
            }
        </style>
    </div>
</x-app-layout>
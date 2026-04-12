<x-app-layout>
    <title>Arewa Smart - Support Dashboard</title>
    @push('styles')
        <style>
            /* Premium Banner & Animations */
            .bg-mesh-primary {
                background: linear-gradient(135deg, var(--bs-primary) 0%, #0a58ca 100%);
                position: relative;
                overflow: hidden;
            }
            .bg-mesh-primary::before {
                content: '';
                position: absolute;
                top: -50%; left: -50%; width: 200%; height: 200%;
                background: radial-gradient(circle at center, rgba(255,255,255,0.15) 0%, transparent 60%);
                animation: mesh-rotate 20s linear infinite;
            }
            @keyframes mesh-rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

            .online-indicator {
                width: 12px; height: 12px;
                animation: status-pulse 2s infinite;
            }
            @keyframes status-pulse {
                0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
                70% { box-shadow: 0 0 0 8px rgba(25, 135, 84, 0); }
                100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
            }

            /* Table Enhancements */
            .table-hover-custom tbody tr { transition: all 0.2s ease; cursor: pointer; }
            .table-hover-custom tbody tr:hover { background-color: rgba(var(--bs-primary-rgb), 0.03) !important; transform: translateY(-1px); }
            
            /* Soft Badges */
            .badge-soft-success { background: rgba(25, 135, 84, 0.1); color: #198754; border: 1px solid rgba(25, 135, 84, 0.1); }
            .badge-soft-primary { background: rgba(13, 110, 253, 0.1); color: #0d6efd; border: 1px solid rgba(13, 110, 253, 0.1); }
            .badge-soft-warning { background: rgba(255, 193, 7, 0.1); color: #997404; border: 1px solid rgba(255, 193, 7, 0.1); }
            .badge-soft-secondary { background: rgba(108, 117, 125, 0.1); color: #6c757d; border: 1px solid rgba(108, 117, 125, 0.1); }

            .animate-float { animation: float 3s ease-in-out infinite; }
            @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

            .focus-within-primary { transition: all 0.3s ease; border: 1px solid #eee; }
            .focus-within-primary:focus-within {
                border-color: var(--bs-primary) !important;
                box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15) !important;
                background: #fff !important;
            }

            @media (max-width: 768px) {
                .responsive-table thead {
                    display: none;
                }

                .responsive-table,
                .responsive-table tbody,
                .responsive-table tr,
                .responsive-table td {
                    display: block;
                    width: 100%;
                }

                .responsive-table tr {
                    margin-bottom: 15px;
                    border: 1px solid #eee;
                    border-radius: 0; /* Match edge-to-edge card */
                    padding: 10px;
                    background: #fdfdfd;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
                }

                .responsive-table td {
                    text-align: right;
                    padding-left: 50%;
                    position: relative;
                    border-bottom: 1px solid #eee;
                    min-height: 40px;
                    display: flex;
                    align-items: center;
                    justify-content: flex-end;
                }

                .responsive-table td:last-child {
                    border-bottom: 0;
                }

                .responsive-table td::before {
                    content: attr(data-label);
                    position: absolute;
                    left: 10px;
                    width: 45%;
                    font-weight: 600;
                    text-align: left;
                    font-size: 0.85rem;
                    color: #666;
                }

                .d-mobile-none {
                    display: none !important;
                }
            }
        </style>
    @endpush

    <div class="page-body">
        <div class="container-fluid px-0 px-md-3">
            <!-- Header -->
            <div class="page-title mb-4">
                <div class="row align-items-center">
                    <div class="col-sm-6 col-12">
                        <h3 class="fw-semibold text-dark mb-1">AI Support Dashboard</h3>
                        <p class="text-muted small mb-0">Experience lightning-fast 24/7 support with our intelligent AI
                            Assistant.</p>
                    </div>
                </div>
            </div>

            <!-- AI Assistant Banner -->
            <div class="row g-0 g-md-4 mb-4">
                <div class="col-12">
                    <div class="card bg-mesh-primary text-white shadow-lg border-0 rounded-0 rounded-md-4 overflow-hidden">
                        <div
                            class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between p-4"  style="position: relative; z-index: 1;">
                            <div class="d-flex align-items-center mb-3 mb-md-0">
                                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3 position-relative shadow-sm"
                                    style="width: 60px; height: 60px; min-width: 60px;">
                                    <i class="ti ti-robot fs-1"></i>
                                    <span
                                        class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle online-indicator"
                                        style="width: 14px; height: 14px; transform: translate(5%, 5%);"
                                        title="AI Online"></span>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold text-white text-uppercase"
                                        style="letter-spacing: 1px; font-size: 1.1rem;">Premium AI Support Assistant
                                    </h5>
                                    <p class="mb-0 text-white-50 small">Powered by advanced AI to solve your issues
                                        instantly, 24/7. No waiting in line.</p>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#createTicketModal"
                                    class="btn btn-white text-primary fw-bold rounded-pill px-4 shadow-sm d-flex align-items-center transition-all hover-translate-y">
                                    <i class="ti ti-messages me-2"></i> Chat with AI
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="row g-0 g-md-4">
                <div class="col-12">
                    <div class="card shadow-lg border-0 rounded-0 rounded-md-4 overflow-hidden">
                        <div
                            class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between rounded-0 rounded-top-md-4">
                            <h5 class="mb-0 fw-bold text-dark"><i class="ti ti-list me-2 text-primary"></i>Your Support
                                Tickets</h5>
                            <span class="badge badge-soft-primary px-3 py-2 rounded-pill">{{ $tickets->total() }}
                                Total</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 responsive-table table-hover-custom">
                                    <thead class="bg-light">
                                        <tr class="text-uppercase text-muted small fw-bold"
                                            style="letter-spacing: 0.5px;">
                                            <th class="ps-4 d-mobile-none" style="width: 5%;">S/N</th>
                                            <th style="width: 15%;">Ticket ID</th>
                                            <th style="width: 35%;">Subject</th>
                                            <th style="width: 15%;">Status</th>
                                            <th class="d-mobile-none" style="width: 15%;">Last Update</th>
                                            <th class="text-end pe-4" style="width: 15%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tickets as $ticket)
                                                                                <tr onclick="window.location='{{ route('support.show', $ticket->ticket_reference) }}'" class="position-relative">
                                                                                    <td class="ps-4 text-muted fw-bold d-mobile-none" data-label="S/N">
                                                                                        {{ $tickets->firstItem() + $loop->index }}
                                                                                    </td>
                                                                                    <td data-label="Ticket ID">
                                                                                        <span
                                                                                            class="badge bg-light text-dark border fw-bold text-uppercase px-2 py-1">
                                                                                            {{ $ticket->ticket_reference }}
                                                                                        </span>
                                                                                    </td>
                                                                                    <td data-label="Subject">
                                                                                        <span
                                                                                            class="fw-medium text-dark">{{ Str::limit($ticket->subject, 40) }}</span>
                                                                                    </td>
                                                                                    <td data-label="Status">
                                                                                        <span class="badge rounded-pill px-3 py-2 badge-soft-{{ match ($ticket->status) {
                                                'open' => 'success',
                                                'answered' => 'primary',
                                                'customer_reply' => 'warning',
                                                'closed' => 'secondary',
                                                default => 'info'
                                            } }}">
                                                                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                                                                        </span>
                                                                                    </td>
                                                                                    <td class="text-muted small d-mobile-none" data-label="Last Update">
                                                                                        <i
                                                                                            class="ti ti-clock me-1"></i>{{ $ticket->updated_at->diffForHumans() }}
                                                                                    </td>
                                                                                    <td class="text-end pe-4" data-label="Action">
                                                                                        <a href="{{ route('support.show', $ticket->ticket_reference) }}"
                                                                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 stretched-link">
                                                                                            View <i class="ti ti-arrow-right ms-1"></i>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="empty-state py-4">
                                                        <div class="mb-4">
                                                            <div class="avatar bg-soft-primary text-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm animate-float"
                                                                style="width: 100px; height: 100px; background: rgba(var(--bs-primary-rgb), 0.1);">
                                                                <i class="ti ti-ticket fs-1"></i>
                                                            </div>
                                                        </div>
                                                        <h4 class="fw-bold text-dark mb-2">No Tickets Found</h4>
                                                        <p class="text-muted mb-4 mx-auto" style="max-width: 300px;">You
                                                            haven't created any support tickets yet. Our AI is ready to help
                                                            whenever you need it.</p>
                                                        <button type="button" data-bs-toggle="modal"
                                                            data-bs-target="#createTicketModal"
                                                            class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                                                            <i class="ti ti-plus me-1"></i> Create Your First Ticket
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($tickets->hasPages())
                            <div class="card-footer bg-white border-top py-3 rounded-0 rounded-bottom-md-4">
                                {{ $tickets->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Ticket Modal -->
    <div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white border-bottom-0 py-3">
                    <h5 class="modal-title fw-bold" id="createTicketModalLabel">
                        <i class="ti ti-message-dots me-2"></i>Open New Support Ticket
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="createTicketForm" action="{{ route('support.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div style="display: none;">
                        <input type="text" name="honeypot_field" autocomplete="off">
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Subject <span
                                    class="text-danger">*</span></label>
                            <div class="input-group focus-within-primary rounded-3 overflow-hidden">
                                <span class="input-group-text bg-light border-0"><i
                                        class="ti ti-edit text-primary"></i></span>
                                <input type="text" name="subject" class="form-control border-0 bg-transparent ps-0"
                                    placeholder="e.g., Payment Issue, Technical Error" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Message <span
                                    class="text-danger">*</span></label>
                            <textarea name="message" class="form-control focus-within-primary" rows="5"
                                placeholder="Please describe your issue in detail..." required
                                style="border-radius: 12px;"></textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold text-dark">Attachment <span
                                    class="text-muted small fw-normal">(Optional)</span></label>
                            <input type="file" name="attachment" class="form-control border-dashed"
                                accept=".jpg,.jpeg,.png,.pdf" style="border-style: dashed;">
                            <small class="text-muted d-block mt-2">Max size: 2MB. Only JPG, PNG, or PDF allowed.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm"
                            id="submitTicketBtn">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"
                                aria-hidden="true"></span>
                            Submit Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('createTicketForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const form = this;
                const submitBtn = document.getElementById('submitTicketBtn');
                const spinner = submitBtn.querySelector('.spinner-border');
                const formData = new FormData(form);

                // Client-side file size validation (2MB)
                const attachment = form.querySelector('input[name="attachment"]');
                if (attachment.files.length > 0 && attachment.files[0].size > 2 * 1024 * 1024) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Attachment size exceeds 2MB limit.',
                        icon: 'error',
                        confirmButtonColor: '#F26522'
                    });
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                spinner.classList.remove('d-none');

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#F26522',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = data.redirect_url;
                            });
                        } else {
                            // Handle validation errors if any
                            let errorMsg = 'Something went wrong. Please try again.';
                            if (data.errors) {
                                errorMsg = Object.values(data.errors).flat().join('\n');
                            }
                            Swal.fire({
                                title: 'Error!',
                                text: errorMsg,
                                icon: 'error',
                                confirmButtonColor: '#F26522'
                            });
                            submitBtn.disabled = false;
                            spinner.classList.add('d-none');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An unexpected error occurred. Please try again later.',
                            icon: 'error',
                            confirmButtonColor: '#F26522'
                        });
                        submitBtn.disabled = false;
                        spinner.classList.add('d-none');
                    });
            });
        </script>
    @endpush
</x-app-layout>
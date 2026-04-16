<x-app-layout>
    <title>Arewa Smart - Open Support Ticket</title>
    @push('styles')
        <style>
            .focus-within-primary { transition: all 0.3s ease; border: 1px solid #eee; }
            .focus-within-primary:focus-within {
                border-color: var(--bs-primary) !important;
                box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15) !important;
                background: #fff !important;
            }
            .form-control:focus {
                border-color: var(--bs-primary);
                box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
            }
        </style>
    @endpush
    <div class="page-body">
        <div class="container-fluid px-0 px-md-3">
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <h3 class="fw-bold text-primary">Open New Ticket</h3>
                        <p class="text-muted small mb-0">Submit your complaint or inquiry.</p>
                    </div>
                    <div class="col-sm-6 col-12 text-end">
                        <a href="{{ route('support.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-0 px-md-3">
            <div class="row justify-content-center g-0 g-md-4">
                <div class="col-12 col-lg-8">
                    <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                        <div class="card-header bg-primary text-white" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                            <h5 class="mb-0">Ticket Information</h5>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                                    <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data" class="no-loader" id="create-ticket-form">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="form-label fw-bold text-dark">Subject <span class="text-danger">*</span></label>
                                            <div class="input-group focus-within-primary rounded-3 overflow-hidden">
                                                <span class="input-group-text bg-light border-0"><i class="ti ti-edit text-primary"></i></span>
                                                <input type="text" name="subject" class="form-control border-0 bg-transparent ps-0" placeholder="Briefly describe the issue" required value="{{ old('subject') }}">
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-bold text-dark">Message <span class="text-danger">*</span></label>
                                            <textarea name="message" class="form-control focus-within-primary" rows="6" placeholder="Detailed explanation of your issue..." required style="border-radius: 20px;">{{ old('message') }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Attachment (Optional)</label>
                                            <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                            <small class="text-muted">Max size: 2MB. Supported formats: JPG, PNG, PDF.</small>
                                        </div>

                                        <div class="d-grid mt-4">
                                            <button type="submit" id="submit-btn" class="btn btn-primary btn-lg rounded-pill shadow-sm fw-bold">
                                                Submit Support Ticket <i class="ti ti-send ms-2"></i>
                                            </button>
                                        </div>
                                    </form>

                                    <script>
                                        document.getElementById('create-ticket-form').addEventListener('submit', function() {
                                            const btn = document.getElementById('submit-btn');
                                            btn.disabled = true;
                                            btn.innerHTML = 'Sending... <span class="spinner-border spinner-border-sm ms-2"></span>';
                                        });
                                    </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

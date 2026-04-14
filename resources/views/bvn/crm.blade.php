<x-app-layout>
 <title>Arewa Smart - {{ $title ?? 'CRM Request Form' }}</title>
    <div class="page-body">
        <div class="container-fluid px-0 px-md-3">
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-sm-6 col-12">
                     <h3 class="fw-bold text-primary">CRM on failed Enrolment Request Form</h3>
                        <p class="text-muted small mb-0">Submit your request accurately to ensure smooth processing.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3 g-0 g-md-4">

            <!-- BVN CRM Form -->
            <div class="col-12 col-xl-6 mb-4">
                <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-gear me-2"></i>BVN CRM Request</h5>
                        <span class="badge bg-light text-primary fw-semibold">Arewa Smart</span>
                    </div>

                    <div class="card-body">
                        <div class="text-center mb-3">
                            <p class="text-muted small mb-0">
                                Submit your BVN CRM request below. Ensure all details are correct before submission.
                            </p>
                        </div>

                        {{-- Alerts --}}
                        @if (session('status'))
                            <div class="alert alert-{{ session('status') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul class="mb-0 small text-start">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- BVN CRM Request Form --}}
                        <form method="POST" action="{{ route('crm.store') }}" class="needs-validation" novalidate>
                            @csrf
                            <div class="row g-3">

                                <!-- CRM Type -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">CRM Type <span class="text-danger">*</span></label>
                                    <select class="form-select text-center" name="field_code" id="service_field" required>
                                        <option value="">-- Select CRM Type --</option>
                                        @foreach ($fieldname as $field)
                                            @php
                                                $price = $field->prices
                                                    ->where('user_type', auth()->user()->role)
                                                    ->first()?->price ?? $field->base_price;
                                            @endphp
                                            <option value="{{ $field->id }}"
                                                    data-price="{{ $price }}"
                                                    data-description="{{ $field->description }}"
                                                    {{ old('field_code') == $field->id ? 'selected' : '' }}>
                                                {{ $field->field_name }} - ₦{{ number_format($price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted" id="field-description"></small>
                                </div>

                                <!-- Batch ID -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold d-flex justify-content-between" for="batch_id">
                                        <span>Batch ID <span class="text-danger">*</span></span>
                                        <button type="button" class="btn btn-outline-primary btn-sm py-0" data-bs-toggle="modal" data-bs-target="#sampleInfoModal">
                                            <i class="bi bi-info-circle"></i> Guide
                                        </button>
                                    </label>
                                    <input class="form-control text-center numeric-only" name="batch_id" id="batch_id" type="text" required
                                           placeholder="Enter 7-digit Batch ID"
                                           value="{{ old('batch_id') }}" maxlength="7" minlength="7"
                                           inputmode="numeric" pattern="\d{7}" title="7-digit Batch ID" aria-label="7-digit Batch ID">
                                    <div class="invalid-feedback">Please provide a valid 7-digit Batch ID.</div>
                                </div>

                                <!-- Ticket ID -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="ticket_id">Ticket ID <span class="text-danger">*</span></label>
                                    <input class="form-control text-center numeric-only" name="ticket_id" id="ticket_id" type="text" required
                                           placeholder="Enter 8-digit Ticket ID"
                                           value="{{ old('ticket_id') }}" maxlength="8" minlength="8"
                                           inputmode="numeric" pattern="\d{8}" title="8-digit Ticket ID" aria-label="8-digit Ticket ID">
                                    <div class="invalid-feedback">Please provide a valid 8-digit Ticket ID.</div>
                                </div>

                                <!-- Service Fee -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Service Fee</label>
                                    <div class="alert alert-info py-2 mb-0 text-center">
                                        <strong id="field-price">₦0.00</strong>
                                    </div>
                                    <small class="text-muted">
                                        Balance:
                                        <strong class="text-success">
                                            ₦{{ number_format($wallet->balance ?? 0, 2) }}
                                        </strong>
                                    </small>
                                </div>

                                <!-- Terms -->
                                <div class="col-md-12">
                                    <div class="p-3 bg-light rounded-3 border mt-2 shadow-sm">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" id="termsCheckbox" type="checkbox" required>
                                            <label class="form-check-label fw-semibold small text-dark" for="termsCheckbox">
                                                <i class="bi bi-shield-check text-primary me-2"></i>
                                                I confirm that the provided information is accurate and agree to the CRM policy.
                                            </label>
                                            <div class="invalid-feedback">You must agree before submitting.</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="col-md-12 d-grid mt-3">
                                    <button type="submit" class="btn btn-primary btn-lg fw-semibold" data-loading-text="Submitting Request...">
                                        <i class="bi bi-send-fill me-2"></i> Submit Request
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

    <!-- Submission History -->
<div class="col-12 col-xl-6 mt-2 mt-md-0">
    <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
        <div class="card-header bg-primary d-flex justify-content-between align-items-center" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-clock-history me-2"></i> CRM Submission History
            </h5>
        </div>

        <div class="card-body">

            <!-- Filter Form -->
            <form method="GET" class="row g-3 mb-3">

                <div class="col-md-6">
                    <input class="form-control"
                           name="search"
                           type="text"
                           placeholder="Search by Ticket/Batch ID"
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-4">
                    <select class="form-control" name="status">
                        <option value="">All Status</option>
                        @foreach(['pending','processing','successful','query','resolved','rejected','remark'] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">Filter</button>
                </div>

            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Reference</th>
                        <th>Ticket ID</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse ($submissions as $submission)
                        <tr>
                            <td>{{ $loop->iteration + $submissions->firstItem() - 1 }}</td>

                            <td>{{ $submission->reference }}</td>

                            <td>{{ $submission->ticket_id ?? $submission->batch_id ?? 'N/A' }}</td>

                            <td>
                                <span class="badge bg-{{ match($submission->status) {
                                    'resolved', 'successful' => 'success',
                                    'processing'             => 'primary',
                                    'rejected'               => 'danger',
                                    'query'                  => 'info',
                                    'remark'                 => 'secondary',
                                    default                  => 'warning'
                                } }}">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </td>

                            <td>
                                @php
                                    $fileUrl = '';
                                    if (!empty($submission->file_url)) {
                                        $f = $submission->file_url;
                                        if (preg_match('/^https?:\/\//', $f)) {
                                            $fileUrl = $f;
                                        } elseif (str_starts_with($f, '/storage') || str_starts_with($f, 'storage')) {
                                            $fileUrl = asset(ltrim($f, '/'));
                                        } else {
                                            $fileUrl = \Illuminate\Support\Facades\Storage::url($f);
                                        }
                                    }
                                @endphp

                                <td>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#commentModal"
                                            data-comment="{{ $submission->comment ?? 'No comment yet.' }}"
                                            data-reference="{{ $submission->reference }}"
                                            data-file-url="{{ $fileUrl }}">
                                        <i class="bi bi-chat-left-text"></i> View
                                    </button>
                                </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                No submissions found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $submissions->withQueryString()->links('vendor.pagination.custom') }}
            </div>

        </div>
    </div>
</div>


    <!-- BVN CRM Guidelines Modal -->
    <div class="modal fade" id="sampleInfoModal" tabindex="-1" aria-labelledby="sampleInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-primary bg-gradient text-white py-4 px-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                            <i class="bi bi-info-circle-fill fs-3 text-white"></i>
                        </div>
                        <div>
                            <h4 class="modal-title fw-bold mb-0" id="sampleInfoModalLabel">CRM Submission Guide</h4>
                            <p class="mb-0 small opacity-75">Learn how to retrieve your Batch and Ticket IDs</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 p-md-5">
                    <div class="row align-items-center mb-5">
                        <div class="col-lg-7">
                            <h5 class="fw-bold text-dark mb-3">Where to find your IDs?</h5>
                            <p class="text-muted">IDs are generated automatically by NIBSS when an enrollment fails. You can find them in the error response on your enrollment portal.</p>
                        </div>
                        <div class="col-lg-5 text-center d-none d-lg-block">
                            <i class="bi bi-file-earmark-text text-primary opacity-25" style="font-size: 5rem;"></i>
                        </div>
                    </div>

                    <div class="instructions-timeline">
                        <!-- Step 1 -->
                        <div class="d-flex mb-4">
                            <div class="me-4 text-center" style="width: 40px;">
                                <div class="bg-primary text-white rounded-circle fw-bold d-flex align-items-center justify-content-center mx-auto" style="width: 40px; height: 40px;">1</div>
                                <div class="vr mt-2 opacity-25" style="height: 30px; width: 2px;"></div>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Locate Enrollment</h6>
                                <p class="small text-muted mb-0">Navigate to the <strong>failed BVN enrollment</strong> record on your system or bank portal.</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="d-flex mb-4">
                            <div class="me-4 text-center" style="width: 40px;">
                                <div class="bg-primary text-white rounded-circle fw-bold d-flex align-items-center justify-content-center mx-auto" style="width: 40px; height: 40px;">2</div>
                                <div class="vr mt-2 opacity-25" style="height: 30px; width: 2px;"></div>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Check Error Details</h6>
                                <p class="small text-muted mb-0">Open the response or error details. Look for lines labeled <strong>Batch ID</strong> or <strong>Ticket ID</strong>.</p>
                                <div class="mt-2 p-2 bg-light border rounded-3 d-flex gap-2">
                                    <code class="badge bg-white border text-primary px-2 py-1">Batch: 1234567</code>
                                    <code class="badge bg-white border text-primary px-2 py-1">Ticket: 87654321</code>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="d-flex">
                            <div class="me-4 text-center" style="width: 40px;">
                                <div class="bg-primary text-white rounded-circle fw-bold d-flex align-items-center justify-content-center mx-auto" style="width: 40px; height: 40px;">3</div>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Submit CRM</h6>
                                <p class="small text-muted mb-0">Copy these exactly into the form and submit. Ensure they are <strong>7 and 8 digits</strong> respectively.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 p-4 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-25">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-lightbulb-fill text-warning fs-3 me-3"></i>
                            <div>
                                <h6 class="fw-bold text-primary mb-1">Pro Tip</h6>
                                <p class="small text-dark mb-0 opacity-75">All CRM requests are subject to NIBSS verification. Double-check IDs to prevent rejection.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 py-4 px-5 justify-content-center">
                    <button type="button" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow-sm" data-bs-dismiss="modal">
                        <i class="bi bi-check2-circle me-2"></i> I've got it
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Comment Modal --}}
    @include('pages.comment')

    {{-- JS for dynamic fee & description --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const serviceField = document.getElementById('service_field');
            const fieldPrice = document.getElementById('field-price');
            const fieldDescription = document.getElementById('field-description');

            serviceField.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                const description = selectedOption.getAttribute('data-description');

                if (price) {
                    fieldPrice.textContent = '₦' + new Intl.NumberFormat().format(price);
                } else {
                    fieldPrice.textContent = '₦0.00';
                }

                if (description) {
                    fieldDescription.textContent = description;
                } else {
                    fieldDescription.textContent = '';
                }
            });
        });
    </script>
  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</x-app-layout>

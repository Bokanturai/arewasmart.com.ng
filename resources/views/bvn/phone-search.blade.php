<x-app-layout>
     <title>Arewa Smart - {{ $title ?? 'Search Bvn Using Phone Number' }}</title>

    <div class="page-body">
        <div class="container-fluid px-0 px-md-3">
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <h3 class="fw-bold text-primary">Search BVN using Phone Number</h3>
                        <p class="text-muted small mb-0">
                            Submit your phone validation request accurately to ensure smooth processing.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3 g-0 g-md-4">
            <!-- Phone Validation Form -->
            <div class="col-12 col-xl-6 mb-4">
                <div class="card shadow-lg border-0 rounded-0 rounded-md-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-0 rounded-top-md-4">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-phone me-2"></i>Phone Validation Request</h5>
                        <span class="badge bg-light text-primary fw-semibold">Arewa Smart</span>
                    </div>

                    <div class="card-body">
                        {{-- Alert Messages --}}
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

                        {{-- Request Form --}}
                        <form method="POST" action="{{ route('phone.search.store') }}" class="needs-validation" novalidate>
                            @csrf
                            <div class="row g-3">

                                <!-- Service Field -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Service Field <span class="text-danger">*</span></label>
                                    <select class="form-select text-center" name="service_field_id" id="service_field" required>
                                        <option value="">-- Select Service Field --</option>
                                        @foreach ($serviceFields as $field)
                                            @php
                                                $price = $field->getPriceForUserType(auth()->user()->role);
                                            @endphp
                                            <option value="{{ $field->id }}"
                                                data-price="{{ $price }}"
                                                data-description="{{ $field->description }}"
                                                {{ old('service_field_id') == $field->id ? 'selected' : '' }}>
                                                {{ $field->field_name }} - ₦{{ number_format($price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-1" id="field-description"></small>
                                </div>

                                <!-- Phone Number -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold d-flex justify-content-between" for="number">
                                        <span>Phone Number <span class="text-danger">*</span></span>
                                        <button type="button" class="btn btn-outline-primary btn-sm py-0"
                                            data-bs-toggle="modal" data-bs-target="#sampleInfoModal">
                                            <i class="bi bi-info-circle"></i> Guide
                                        </button>
                                    </label>
                                    <input class="form-control text-center numeric-only" name="number" id="number" type="text" required
                                        placeholder="Enter 11-digit Phone Number"
                                        value="{{ old('number') }}" maxlength="11" minlength="11"
                                        inputmode="tel" pattern="\d{11}" title="11-digit Phone Number" aria-label="11-digit Phone Number">
                                    <div class="invalid-feedback">Please provide a valid 11-digit phone number.</div>
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
                                                I confirm that the provided information is accurate and agree to the phone validation policy.
                                            </label>
                                            <div class="invalid-feedback">You must agree before submitting.</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="col-md-12 d-grid mt-3">
                                    <button type="submit" class="btn btn-primary btn-lg fw-semibold" data-loading-text="Processing Validation...">
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
                <div class="card shadow-lg border-0 rounded-0 rounded-md-4">
                    <div class="card-header bg-primary d-flex justify-content-between align-items-center text-white rounded-0 rounded-top-md-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Submission History</h5>
                    </div>

                    <div class="card-body">
                        <form class="row g-3 mb-3" method="GET" action="{{ route('phone.search.index') }}">
                            <div class="col-md-6">
                                <input class="form-control" name="search" type="text" placeholder="Search by Phone Number" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" name="status">
                                    <option value="">All Status</option>
                                    @foreach(['pending', 'processing', 'verified', 'resolved', 'rejected'] as $status)
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

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Reference</th>
                                        <th>Phone Number</th>
                                        <th>BVN</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($crmSubmissions as $submission)
                                        <tr>
                                            <td>{{ $loop->iteration + $crmSubmissions->firstItem() - 1 }}</td>
                                            <td>{{ $submission->reference }}</td>
                                            <td>{{ $submission->number }}</td>
                                            <td>{{ $submission->bvn ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ match($submission->status) {
                                                    'resolved' => 'success',
                                                    'successful' => 'success',
                                                    'processing' => 'primary',
                                                    'verified' => 'info',
                                                    'rejected' => 'danger',
                                                    default => 'warning'
                                                } }}">{{ ucfirst($submission->status) }}</span>
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
                                            <td colspan="5" class="text-center text-muted">No submissions found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $crmSubmissions->withQueryString()->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Comment Modal --}}
    @include('pages.comment')

    {{-- Guidelines Modal --}}
    <div class="modal fade" id="sampleInfoModal" tabindex="-1" aria-labelledby="sampleInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white py-3 rounded-top-4">
                    <h4 class="modal-title fw-bold" id="sampleInfoModalLabel">
                        <i class="bi bi-phone-fill me-2"></i> Phone Validation Guidelines
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="bg-light p-4 rounded-3 mb-4 border-start border-4 border-primary">
                        <h6 class="fw-semibold mb-3 text-primary">
                            <i class="bi bi-list-check me-2"></i> Important Guidelines
                        </h6>
                        <ul class="fw-semibold mb-3 text-primary">
                            <li><i class="bi bi-arrow-return-right text-success me-2"></i> Ensure the number is 11 digits.</li>
                            <li><i class="bi bi-chat-dots-fill text-warning me-2"></i> Validation may take up to 24 hours.</li>
                            <li><i class="bi bi-clipboard-check-fill text-info me-2"></i> 20% fee applies on failed validations.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="bi bi-check-circle me-2"></i> Understood
                    </button>
                </div>
            </div>
        </div>
    </div>
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
                    fieldPrice.textContent = '₦' + new Intl.NumberFormat('en-NG').format(price);
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

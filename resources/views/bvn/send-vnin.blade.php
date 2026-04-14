<x-app-layout> 
    <title>Arewa Smart - {{ $title ?? 'SEND VNIN TO NIBSS Request Form' }}</title>
    <div class="page-body">
        <div class="container-fluid px-0 px-md-3">
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <h3 class="fw-bold text-primary">SEND VNIN TO NIBSS Request</h3>
                        <p class="text-muted small mb-0">Submit your request accurately to ensure smooth processing.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3 g-0 g-md-4">
            <!-- SEND VNIN TO NIBSS Request Form -->
            <div class="col-12 col-xl-6 mb-4">
                <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-send-fill me-2"></i> SEND VNIN TO NIBSS Request
                        </h5>
                        <span class="badge bg-light text-primary fw-semibold">Arewa Smart</span>
                    </div>

                    <div class="card-body">
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

                        {{-- SEND VNIN Form --}}
                        <form method="POST" action="{{ route('send-vnin.store') }}" class="needs-validation" novalidate>
                            @csrf
                            <div class="row g-3">

                                <!-- Verification Type -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Verification Type <span class="text-danger">*</span></label>
                                    <select class="form-select text-center" name="field_code" id="service_field" required>
                                        <option value="">-- Select Verification Type --</option>
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

                                <!-- Request ID -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="request_id">Request ID <span class="text-danger">*</span></label>
                                    <input class="form-control text-center numeric-only" name="request_id" id="request_id" type="text" required
                                           placeholder="Enter 7-digit Request ID"
                                           value="{{ old('request_id') }}" maxlength="7" minlength="7"
                                           inputmode="numeric" pattern="\d{7}" title="7-digit Request ID" aria-label="7-digit Request ID">
                                    <div class="invalid-feedback">Please provide a valid 7-digit Request ID.</div>
                                </div>

                                <!-- BVN -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="bvn">BVN <span class="text-danger">*</span></label>
                                    <input class="form-control text-center numeric-only" name="bvn" id="bvn" type="text" required
                                           placeholder="Enter 11-digit BVN"
                                           value="{{ old('bvn') }}" maxlength="11" minlength="11"
                                           inputmode="numeric" pattern="\d{11}" title="11-digit BVN" aria-label="11-digit BVN">
                                    <div class="invalid-feedback">Please provide a valid 11-digit BVN.</div>
                                </div>

                                <!-- NIN -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="nin">NIN <span class="text-danger">*</span></label>
                                    <input class="form-control text-center numeric-only" name="nin" id="nin" type="text" required
                                           placeholder="Enter 11-digit NIN"
                                           value="{{ old('nin') }}" maxlength="11" minlength="11"
                                           inputmode="numeric" pattern="\d{11}" title="11-digit NIN" aria-label="11-digit NIN">
                                    <div class="invalid-feedback">Please provide a valid 11-digit NIN.</div>
                                </div>

                                <!-- Modification Field -->
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Modification Field <span class="text-danger">*</span></label>
                                    <input class="form-control text-center" name="field" type="text" required
                                           placeholder="Enter field to modify"
                                           value="{{ old('field') }}" maxlength="50">
                                </div>

                                <!-- Service Fee -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Service Fee</label>
                                    <div class="alert alert-info py-2 mb-0 text-center shadow-sm">
                                        <strong id="field-price">₦0.00</strong>
                                    </div>
                                    <small class="text-muted">
                                        Wallet Balance: 
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
                                                I confirm that the provided information is accurate and agree to the NIBSS policy.
                                            </label>
                                            <div class="invalid-feedback">You must agree before submitting.</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="col-md-12 d-grid mt-3">
                                    <button type="submit" class="btn btn-primary btn-lg fw-semibold" data-loading-text="Sending to NIBSS...">
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
                        <h5 class="mb-0 fw-bold text-black">Submission History</h5>
                    </div>
                    <div class="card-body">
                        <form class="row g-3 mb-3" method="GET" action="{{ route('send-vnin') }}">
                            <div class="col-md-6">
                                <input class="form-control" name="search" type="text" placeholder="Search by Request ID" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" name="status">
                                    <option value="">All Status</option>
                                    @foreach(['pending', 'processing', 'successful', 'query', 'resolved', 'rejected', 'remark'] as $status)
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
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Reference</th>
                                        <th>Request ID</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($submissions as $submission)
                                        <tr>
                                            <td>{{ $loop->iteration + $submissions->firstItem() - 1 }}</td>
                                            <td>{{ $submission->reference }}</td>
                                            <td>{{ $submission->request_id }}</td>
                                            <td>
                                                <span class="badge bg-{{ match($submission->status) {
                                                    'resolved', 'successful' => 'success',
                                                    'processing' => 'primary',
                                                    'query' => 'info',
                                                    'rejected' => 'danger',
                                                    'remark' => 'secondary',
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
                            {{ $submissions->withQueryString()->links('vendor.pagination.custom') }}
                        </div>
                    </div>
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

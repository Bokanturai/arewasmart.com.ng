<x-app-layout>
    <title>Arewa Smart - NIN Validation</title>
   <div class="page-body">
        <div class="container-fluid">
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <h3 class="fw-bold text-primary">NIN Validation Request Form</h3>
                        <p class="text-muted small mb-0">
                            Submit your request accurately to ensure smooth processing.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-0 px-md-3">
            <div class="row g-0 g-md-4">
                <!-- Form Section -->
                <div class="col-12 col-xl-5 mb-4">
                    <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                        <div class="card-header bg-primary text-white p-3 p-md-4 border-0 text-center text-sm-start" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                            <h5 class="mb-0 fw-bold fs-15">New Request</h5>
                            <p class="mb-0 small text-white-50 mt-1">Please ensure all details are correct. Fees are non-refundable.</p>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('nin-validation.store') }}" class="row g-4">
                                @csrf

                                <!-- Service Field Selection -->
                                <div class="col-12">
                                    <label for="service_field" class="form-label fw-semibold text-dark">Select Service Field <span class="text-danger">*</span></label>
                                    <select name="service_field" id="service_field" class="form-select form-select-lg bg-light border-0 shadow-sm" required>
                                        <option value="">-- Choose a Field --</option>
                                        @foreach($services as $field)
                                            <option value="{{ $field['id'] }}" data-price="{{ $field['price'] }}">
                                                {{ $field['name'] }} - ₦{{ number_format($field['price'], 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2">
                                        <small class="text-muted" id="field-description"></small>
                                    </div>
                                </div>

                                <!-- NIN Input -->
                                <div class="col-12" id="nin_wrapper">
                                    <label class="form-label fw-semibold text-dark">NIN <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm">
                                        <span class="input-group-text bg-white border-0"><i class="bi bi-person-badge text-primary"></i></span>
                                        <input type="text" name="nin" class="form-control bg-light border-0 ps-0" placeholder="Enter 11-digit NIN" maxlength="11" pattern="\d{11}" required>
                                    </div>
                                </div>

                                <!-- Price Display -->
                                <div class="col-12">
                                    <div class="alert alert-info d-flex justify-content-between align-items-center mb-0 rounded-3 shadow-sm border-0">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-wallet2 fs-15 me-2 text-info"></i>
                                            <span class="fw-medium">Service Fee:</span>
                                        </div>
                                        <strong id="price_display" class="fs-15 text-info">₦0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-end mt-2">
                                        <small class="text-muted">
                                            Wallet Balance: <span class="text-success fw-bold">₦{{ number_format($wallet->balance ?? 0, 2) }}</span>
                                        </small>
                                    </div>
                                </div>

                                <!-- Warning -->
                                <div class="col-12">
                                    <div class="alert alert-warning py-3 rounded-3 shadow-sm border-0 d-flex align-items-center">
                                        <i class="bi bi-exclamation-circle text-warning fs-15 me-3"></i>
                                        <div class="small">
                                            <strong>Non-refundable Service</strong><br>
                                            Please verify all details carefully before submission.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center gap-2">
                                        Submit Request <i class="bi bi-arrow-right-circle"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- History Section -->
                <div class="col-12 col-xl-7 mt-2 mt-md-0">
                    <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                        <!-- Header -->
                        <div class="card-header bg-white p-3 p-md-4 border-bottom d-flex justify-content-center justify-content-sm-between align-items-center" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                            <h5 class="mb-0 fw-bold text-dark fs-15"><i class="bi bi-clock-history text-primary me-2"></i>Request History</h5>
                        </div>

                        <div class="card-body p-3 p-md-4">
                            <!-- Filters -->
                            <form class="row g-3 mb-4" method="GET">
                                <div class="col-12 col-md-5">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                                        <input class="form-control bg-light border-0 ps-0" name="search" type="text" placeholder="Search NIN" value="{{ request('search') }}">
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <select class="form-select bg-light border-0 shadow-sm" name="status">
                                        <option value="">All Statuses</option>
                                        @foreach(['pending', 'processing', 'successful', 'failed', 'rejected'] as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-3">
                                    <button class="btn btn-primary w-100 rounded-pill shadow-sm fw-medium" type="submit">Filter Status</button>
                                </div>
                            </form>

                            <!-- Table -->
                            <div class="table-responsive rounded-3 border">
                                <table class="table table-hover table-borderless align-middle mb-0 text-nowrap">
                                    <thead class="table-light border-bottom">
                                        <tr>
                                            <th class="ps-4">#</th>
                                            <th>Reference</th>
                                            <th>NIN</th>
                                            <th>Status</th>
                                            <th class="text-end pe-4">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($submissions as $submission)
                                            <tr class="border-bottom">
                                                <!-- Serial Number -->
                                                <td class="ps-4 text-muted">{{ $loop->iteration + $submissions->firstItem() - 1 }}</td>

                                                <!-- Reference -->
                                                <td class="fw-medium text-dark">{{ $submission->reference }}</td>

                                                <!-- NIN -->
                                                <td>
                                                    <span class="fw-bold d-block">{{ $submission->nin }}</span>
                                                    <small class="text-primary fw-medium">
                                                        {{ $submission->service_field_name }}
                                                    </small>
                                                </td>
                                                <!-- Status Badge -->
                                                <td>
                                                    @php
                                                        $statusLower = strtolower($submission->status);
                                                        $badgeInfo = match($statusLower) {
                                                            'successful', 'success', 'resolved' => ['color' => 'success', 'icon' => 'check-circle'],
                                                            'processing', 'in-progress' => ['color' => 'info', 'icon' => 'arrow-repeat'],
                                                            'pending' => ['color' => 'warning', 'icon' => 'hourglass-split'],
                                                            'failed', 'rejected', 'error' => ['color' => 'danger', 'icon' => 'x-circle'],
                                                            default => ['color' => 'secondary', 'icon' => 'dash-circle']
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $badgeInfo['color'] }}-subtle text-{{ $badgeInfo['color'] }} px-3 py-2 rounded-pill fw-semibold border border-{{ $badgeInfo['color'] }}-subtle">
                                                        <i class="bi bi-{{ $badgeInfo['icon'] }} me-1"></i> {{ ucfirst($submission->status) }}
                                                    </span>
                                                </td>

                                                <!-- Action Buttons -->
                                                <td class="text-end pe-4">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <!-- Check Status -->
                                                        @if(in_array($statusLower, ['pending', 'processing', 'in-progress']))
                                                            <a href="{{ route('nin-validation.check', $submission->id) }}" class="btn btn-sm btn-light text-primary shadow-sm rounded-circle d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Check Status" style="width: 35px; height: 35px;">
                                                                <i class="bi bi-arrow-repeat fs-15"></i>
                                                            </a>
                                                        @endif

                                                        <!-- View Comment -->
                                                        <button type="button" class="btn btn-sm btn-light text-secondary shadow-sm rounded-circle d-flex align-items-center justify-content-center" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#commentModal" 
                                                            data-comment="{{ $submission->comment ?? 'No comment yet.' }}"
                                                            data-reference="{{ $submission->reference }}"
                                                            data-file-url="{{ $submission->file_url ?? '' }}"
                                                            data-approved-by="{{ $submission->approved_by ?? '' }}"
                                                            data-bs-toggle="tooltip" title="View Response" style="width: 35px; height: 35px;">
                                                            <i class="bi bi-chat-left-text fs-16"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-5">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                                            <i class="bi bi-inbox fs-15 text-muted"></i>
                                                        </div>
                                                        <h6 class="fw-semibold">No requests found</h6>
                                                        <p class="small text-muted mb-0">Your submitted NIN validation requests will appear here.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4 d-flex justify-content-end">
                                {{ $submissions->withQueryString()->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End Row -->

               {{-- Comment Modal --}}
            @include('pages.comment')

          




            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const serviceFieldSelect = document.getElementById('service_field');
                    const priceDisplay = document.getElementById('price_display');

                    // Initialize tooltips
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    })

                    // When selecting specific service field
                    if (serviceFieldSelect) {
                        serviceFieldSelect.addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            const price = selectedOption.getAttribute('data-price');

                            if (price) {
                                priceDisplay.textContent = '₦' + parseFloat(price).toLocaleString('en-NG', {
                                    minimumFractionDigits: 2
                                });
                            } else {
                                priceDisplay.textContent = '₦0.00';
                            }
                        });
                    }


                }); // DOM Loaded END
            </script>
        </div>
    </div>
</x-app-layout>

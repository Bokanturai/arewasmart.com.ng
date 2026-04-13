<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'BVN Modification Request' }}</title>

    <div class="page-body">
        <!-- Page Title -->
        <div class="container-fluid">
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-sm-6 col-12 text-center text-sm-start">
                        <h3 class="fw-bold text-primary">BVN Modification Request</h3>
                        <p class="text-muted small mb-0">Submit your request accurately following NIBSS standards.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-0 px-md-3">
            <div class="row mt-3 g-0 g-md-4">

                {{-- MODIFICATION REQUEST FORM --}}
                <div class="col-12 col-xl-5 mb-4">
                    <div class="card shadow-lg border-0 rounded-0 rounded-md-4">
                        <div class="card-header bg-primary text-white p-3 p-md-4 border-0 rounded-0 rounded-top-md-4 text-center text-sm-start">
                            <h5 class="mb-0 fw-bold fs-15"><i class="bi bi-shield-lock-fill me-2"></i>New Modification Request</h5>
                            <p class="mb-0 small text-white-50 mt-1">Ensure all details match your verification slip.</p>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            {{-- Alerts --}}
                            @if (session('status'))
                                <div class="alert alert-{{ session('status') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                                    <i class="bi bi-{{ session('status') === 'success' ? 'check-circle' : 'exclamation-triangle' }}-fill me-2"></i>
                                    {{ session('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                                    <ul class="mb-0 small text-start">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                             <!-- Verification Advice -->
                            <div class="alert alert-primary-subtle border-0 d-flex align-items-center mb-4 py-3 rounded-3 shadow-sm">
                                <i class="bi bi-info-circle-fill text-primary fs-14 me-3"></i>
                                <div class="small">
                                    <strong class="text-primary d-block mb-1">Important: Verify first</strong>
                                    Verify your current BVN details via <strong>*565*0#</strong> to ensure accuracy.
                                    <a href="{{ route('bvn.verification.index') }}" class="fw-bold text-primary text-decoration-none ms-1">Verify Here <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('modification.store') }}" enctype="multipart/form-data" class="row g-4">
                                @csrf

                                <!-- Bank Selection -->
                                <div class="col-12">
                                    <label for="enrolment_bank" class="form-label fw-semibold text-dark">Select Bank <span class="text-danger">*</span></label>
                                    <select name="enrolment_bank" id="enrolment_bank" class="form-select form-select-lg bg-light border-0 shadow-sm" required>
                                        <option value="">-- Select Bank --</option>
                                        @foreach($bankServices as $service)
                                            <option value="{{ $service->id }}" {{ old('enrolment_bank') == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Modification Field Selection -->
                                <div class="col-12">
                                    <label for="service_field" class="form-label fw-semibold text-dark">Modification Field <span class="text-danger">*</span></label>
                                    <select name="service_field" id="service_field" class="form-select form-select-lg bg-light border-0 shadow-sm" required>
                                        <option value="">-- Select Field --</option>
                                    </select>
                                    <div class="mt-2">
                                        <small class="text-muted" id="field-description"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">BVN ID <span class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-0"><i class="bi bi-person-vcard text-primary"></i></span>
                                        <input type="text" name="bvn" class="form-control bg-light border-0 ps-0 text-center" placeholder="BVN Num" value="{{ old('bvn') }}" maxlength="11" minlength="11" pattern="[0-9]{11}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">NIN ID <span class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-0"><i class="bi bi-person-badge text-primary"></i></span>
                                        <input type="text" name="nin" class="form-control bg-light border-0 ps-0 text-center" placeholder="NIN Num" value="{{ old('nin') }}" maxlength="11" minlength="11" pattern="[0-9]{11}" required>
                                    </div>
                                </div>

                                <!-- Data Description -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold text-dark mb-0">New Data Information <span class="text-danger">*</span></label>
                                        <button type="button" class="btn btn-outline-primary btn-sm py-0 border-0" data-bs-toggle="modal" data-bs-target="#sampleInfoModal">
                                            <i class="bi bi-info-circle"></i> View Samples
                                        </button>
                                    </div>
                                    <textarea name="description" id="description-field" class="form-control bg-light border-0 shadow-sm" rows="3" placeholder="Describe the modification needed..." required>{{ old('description') }}</textarea>
                                </div>

                                <!-- Affidavit Selection -->
                                <div class="col-12">
                                    <label for="affidavit" class="form-label fw-semibold text-dark">Do you have an Affidavit? <span class="text-danger">*</span></label>
                                    <select name="affidavit" id="affidavit" class="form-select bg-light border-0 shadow-sm" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="available" {{ old('affidavit') === 'available' ? 'selected' : '' }}>Yes, I will upload mine</option>
                                        <option value="not_available" {{ old('affidavit') === 'not_available' ? 'selected' : '' }}>No, please provide one for me</option>
                                    </select>
                                    <div id="affidavit-hint" class="mt-2 d-none">
                                        <div class="alert alert-warning-subtle border-0 py-2 small mb-0 rounded-3">
                                            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> A fee of <strong>₦{{ number_format($affidavitFee, 2) }}</strong> will be charged.
                                        </div>
                                    </div>
                                </div>

                                <!-- Affidavit Upload -->
                                <div class="col-12 d-none" id="affidavit_upload_wrapper">
                                    <label class="form-label fw-semibold text-dark">Upload Affidavit (PDF)</label>
                                    <input type="file" name="affidavit_file" accept="application/pdf" class="form-control bg-light border-0 shadow-sm">
                                </div>

                                <!-- Fee & Balance Section -->
                                <div class="col-12">
                                    <div class="card border-0 bg-light rounded-4 p-3 shadow-sm">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small">Modification Fee</span>
                                            <strong id="price-mod" class="text-dark">₦0.00</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 d-none" id="price-aff-wrapper">
                                            <span class="text-muted small">Affidavit Fee</span>
                                            <strong id="price-aff" class="text-dark">₦0.00</strong>
                                        </div>
                                        <hr class="my-2 opacity-10">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-dark">Total Payable</span>
                                            <h5 id="price-total" class="fw-bold text-primary mb-0">₦0.00</h5>
                                        </div>
                                        <div class="mt-3 text-end">
                                            <small class="text-muted">Available: <span class="text-success fw-bold">₦{{ number_format($wallet->balance ?? 0, 2) }}</span></small>
                                        </div>
                                    </div>
                                </div>

                                 <!-- Action Button -->
                                <div class="col-12">
                                    <button type="submit" id="submit-btn" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center gap-2">
                                        <span id="btn-text">Submit Request</span>
                                        <i class="bi bi-arrow-right-circle" id="btn-icon"></i>
                                        <div id="btn-spinner" class="spinner-border spinner-border-sm d-none" role="status"></div>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- SUBMISSION HISTORY -->
                <div class="col-12 col-xl-7 mt-2 mt-md-0">
                    <div class="card shadow-lg border-0 rounded-0 rounded-md-4">
                        <div class="card-header bg-white p-3 p-md-4 border-bottom d-flex justify-content-center justify-content-sm-between align-items-center rounded-0 rounded-top-md-4">
                            <h5 class="mb-0 fw-bold text-dark fs-15"><i class="bi bi-clock-history text-primary me-2"></i>Submission History</h5>
                        </div>

                        <div class="card-body p-3 p-md-4">
                            {{-- Filters --}}
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-5">
                                    <form method="GET" action="{{ route('modification') }}">
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                                            <input type="text" name="search" class="form-control bg-light border-0 ps-0" placeholder="Search by BVN..." value="{{ request('search') }}">
                                        </div>
                                    </form>
                                </div>

                                <div class="col-12 col-md-4">
                                    <form method="GET" action="{{ route('modification') }}">
                                        <select name="status" class="form-select bg-light border-0 shadow-sm" onchange="this.form.submit()">
                                            <option value="">All Statuses</option>
                                            @foreach (['pending', 'query', 'processing', 'resolved', 'successful', 'rejected'] as $status)
                                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                                <div class="col-12 col-md-3">
                                    <a href="{{ route('modification') }}" class="btn btn-light w-100 rounded-pill border-0 shadow-sm fw-medium text-muted">Reset</a>
                                </div>
                            </div>

                            {{-- History Table --}}
                            <div class="table-responsive rounded-3 border">
                                <table class="table table-hover table-borderless align-middle mb-0 text-nowrap">
                                    <thead class="table-light border-bottom">
                                        <tr>
                                            <th class="ps-4">S/N</th>
                                            <th>Reference / BVN</th>
                                            <th>Bank</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($crmSubmissions as $submission)
                                            <tr class="border-bottom">
                                                <td class="ps-4 text-muted small">
                                                    {{ $loop->iteration + ($crmSubmissions->currentPage() - 1) * $crmSubmissions->perPage() }}
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-dark d-block mb-1">{{ $submission->bvn }}</span>
                                                    <small class="text-muted" style="font-size: 0.7rem;">Ref: {{ $submission->reference }}</small>
                                                </td>
                                                <td>
                                                    <span class="small text-muted">{{ $submission->bank }}</span>
                                                    <br>
                                                    <small class="text-primary fw-medium">{{ $submission->submission_date->format('M d, Y') }}</small>
                                                </td>

                                                {{-- Status Badge --}}
                                                <td>
                                                    @php
                                                        $stat = strtolower($submission->status);
                                                        $badge = match ($stat) {
                                                            'resolved', 'successful' => ['c' => 'success', 'i' => 'check-circle'],
                                                            'processing' => ['c' => 'info', 'i' => 'arrow-repeat'],
                                                            'rejected' => ['c' => 'danger', 'i' => 'x-circle'],
                                                            'query', 'remark', 'pending' => ['c' => 'warning', 'i' => 'exclamation-circle'],
                                                            default => ['c' => 'secondary', 'i' => 'hourglass-split'],
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $badge['c'] }}-subtle text-{{ $badge['c'] }} px-3 py-2 rounded-pill fw-semibold border border-{{ $badge['c'] }}-subtle">
                                                        <i class="bi bi-{{ $badge['i'] }} me-1"></i>
                                                        {{ ucfirst($submission->status) }}
                                                    </span>
                                                </td>

                                                <td>
                                                    @php
                                                        $fileUrl = '';
                                                        if (!empty($submission->file_url)) {
                                                            $f = $submission->file_url;
                                                            $fileUrl = preg_match('/^https?:\/\//', $f) ? $f : asset(ltrim($f, '/'));
                                                        }
                                                    @endphp

                                                    <button type="button"
                                                        class="btn btn-sm btn-light text-primary shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                                                        data-bs-toggle="modal" data-bs-target="#commentModal"
                                                        data-comment="{{ $submission->comment ?? 'No comment yet.' }}"
                                                        data-reference="{{ $submission->reference }}"
                                                        data-file-url="{{ $fileUrl }}"
                                                        title="View Response" style="width: 35px; height: 35px;">
                                                        <i class="bi bi-chat-left-text-fill fs-15"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-5">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                                            <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                                                        </div>
                                                        <h6 class="fw-semibold">No requests found</h6>
                                                        <p class="small text-muted mb-0">Your submitted BVN requests will appear here.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 d-flex justify-content-end">
                                {{ $crmSubmissions->withQueryString()->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Comment Modal --}}
        @include('pages.comment')

        <!-- Sample Info Modal -->
        <div class="modal fade" id="sampleInfoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-header bg-primary text-white py-3 rounded-top-4 border-0">
                        <h5 class="modal-title fw-bold fs-18">
                            <i class="bi bi-info-square-fill me-2"></i> Modification Guidelines
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light rounded-4 p-3 shadow-sm">
                                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Name Correction</h6>
                                    <div class="p-2 small">
                                        <p class="mb-1"><strong>New Data:</strong>Firstname: ADEBAYO, lastname: ADEKUNLE, Middlename: BOLA</p>
                                        <p class="mb-0 text-muted fst-italic">Requires Court Affidavit.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light rounded-4 p-3 shadow-sm">
                                    <h6 class="fw-bold text-info mb-3"><i class="bi bi-calendar-event me-2"></i>Date of Birth</h6>
                                    <div class="p-2 small">
                                        <p class="mb-1"><strong>New Data:</strong> 15-05-1992</p>
                                        <p class="mb-0 text-muted fst-italic">Requires Birth Certificate.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card border-0 bg-light rounded-4 p-3 shadow-sm">
                                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bank2 me-2"></i>Enrollment Banks & Codes</h6>
                                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                        <table class="table table-hover table-sm align-middle small bg-white rounded-3 overflow-hidden">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="ps-2">Code</th>
                                                    <th>Bank Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><td class="ps-2">044</td><td>Access Bank Nigeria Plc</td></tr>
                                                <tr><td class="ps-2">063</td><td>Diamond Bank Plc</td></tr>
                                                <tr><td class="ps-2">050</td><td>Ecobank Nigeria</td></tr>
                                                <tr><td class="ps-2">084</td><td>Enterprise Bank Plc</td></tr>
                                                <tr><td class="ps-2">070</td><td>Fidelity Bank Plc</td></tr>
                                                <tr><td class="ps-2">011</td><td>First Bank of Nigeria Plc</td></tr>
                                                <tr><td class="ps-2">214</td><td>First City Monument Bank</td></tr>
                                                <tr><td class="ps-2">058</td><td>Guaranty Trust Bank Plc</td></tr>
                                                <tr><td class="ps-2">030</td><td>Heritage Banking Company Ltd</td></tr>
                                                <tr><td class="ps-2">301</td><td>Jaiz Bank</td></tr>
                                                <tr><td class="ps-2">082</td><td>Keystone Bank Ltd</td></tr>
                                                <tr><td class="ps-2">014</td><td>Mainstreet Bank Plc</td></tr>
                                                <tr><td class="ps-2">076</td><td>Skye Bank Plc</td></tr>
                                                <tr><td class="ps-2">039</td><td>Stanbic IBTC Plc</td></tr>
                                                <tr><td class="ps-2">232</td><td>Sterling Bank Plc</td></tr>
                                                <tr><td class="ps-2">032</td><td>Union Bank Nigeria Plc</td></tr>
                                                <tr><td class="ps-2">033</td><td>United Bank for Africa Plc</td></tr>
                                                <tr><td class="ps-2">215</td><td>Unity Bank Plc</td></tr>
                                                <tr><td class="ps-2">035</td><td>WEMA Bank Plc</td></tr>
                                                <tr><td class="ps-2">057</td><td>Zenith Bank International</td></tr>
                                                <tr><td class="ps-2">000</td><td>Agency Banking/NIBSS</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4">
                        <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold" data-bs-dismiss="modal">I Understood</button>
                    </div>
                </div>
            </div>
        </div>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bankSelect = document.getElementById('enrolment_bank');
            const fieldSelect = document.getElementById('service_field');
            const fieldDescription = document.getElementById('field-description');
            const affidavitSelect = document.getElementById('affidavit');
            const affidavitHint = document.getElementById('affidavit-hint');
            const affidavitUploadWrapper = document.getElementById('affidavit_upload_wrapper');
            
            const priceModDisplay = document.getElementById('price-mod');
            const priceAffDisplay = document.getElementById('price-aff');
            const priceAffWrapper = document.getElementById('price-aff-wrapper');
            const priceTotalDisplay = document.getElementById('price-total');

            const dbAffidavitFee = {{ (float)$affidavitFee }};
            let currentModFee = 0;
            let currentAffFee = 0;

            const form = bankSelect.closest('form');
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnIcon = document.getElementById('btn-icon');
            const btnSpinner = document.getElementById('btn-spinner');

            form.addEventListener('submit', function (e) {
                // Determine if it's an AJAX submission or standard
                // Based on previous code, this project uses standard form submission with alerts, 
                // but let's lock the UI regardless.
                
                submitBtn.disabled = true;
                btnText.textContent = 'Processing...';
                btnIcon.classList.add('d-none');
                btnSpinner.classList.remove('d-none');
            });

            const formatMoney = (amount) => '₦' + new Intl.NumberFormat('en-NG').format(amount);

            bankSelect.addEventListener('change', function () {
                const bankId = this.value;
                fieldSelect.innerHTML = '<option value="">Loading...</option>';
                
                if (bankId) {
                    fetch("{{ route('modification.fields', ['serviceId' => ':id']) }}".replace(':id', bankId))
                        .then(response => response.json())
                        .then(data => {
                            fieldSelect.innerHTML = '<option value="">-- Select Field --</option>';
                            data.forEach(field => {
                                const option = document.createElement('option');
                                option.value = field.id;
                                option.textContent = `${field.field_name}`;
                                option.dataset.price = field.price;
                                option.dataset.description = field.description;
                                fieldSelect.appendChild(option);
                            });
                        })
                        .catch(() => {
                            fieldSelect.innerHTML = '<option value="">Error loading fields</option>';
                        });
                } else {
                    fieldSelect.innerHTML = '<option value="">-- Select Field --</option>';
                    resetTotal();
                }
            });

            fieldSelect.addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];
                if (selected.value) {
                    currentModFee = parseFloat(selected.dataset.price);
                    fieldDescription.textContent = selected.dataset.description || '';
                    priceModDisplay.textContent = formatMoney(currentModFee);
                } else {
                    currentModFee = 0;
                    fieldDescription.textContent = '';
                    priceModDisplay.textContent = formatMoney(0);
                }
                calculateTotal();
            });

            affidavitSelect.addEventListener('change', function () {
                if (this.value === 'not_available') {
                    affidavitUploadWrapper.classList.add('d-none');
                    affidavitHint.classList.remove('d-none');
                    priceAffWrapper.classList.remove('d-none');
                    currentAffFee = dbAffidavitFee;
                } else if (this.value === 'available') {
                    affidavitUploadWrapper.classList.remove('d-none');
                    affidavitHint.classList.add('d-none');
                    priceAffWrapper.classList.add('d-none');
                    currentAffFee = 0;
                } else {
                    affidavitUploadWrapper.classList.add('d-none');
                    affidavitHint.classList.add('d-none');
                    priceAffWrapper.classList.add('d-none');
                    currentAffFee = 0;
                }
                priceAffDisplay.textContent = formatMoney(currentAffFee);
                calculateTotal();
            });

            function calculateTotal() {
                const total = currentModFee + currentAffFee;
                priceTotalDisplay.textContent = formatMoney(total);
            }

            function resetTotal() {
                currentModFee = 0;
                currentAffFee = 0;
                priceModDisplay.textContent = formatMoney(0);
                priceAffDisplay.textContent = formatMoney(0);
                priceTotalDisplay.textContent = formatMoney(0);
                priceAffWrapper.classList.add('d-none');
                affidavitHint.classList.add('d-none');
                affidavitUploadWrapper.classList.add('d-none');
            }
        });
    </script>
</x-app-layout>
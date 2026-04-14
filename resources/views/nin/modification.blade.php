<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'NIN Modification Request Form' }}</title>

    <div class="page-body">
        <!-- Page Title & Greeting -->
        <div class="container-fluid">
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-sm-6 col-12 text-center text-sm-start">
                        <h3 class="fw-bold text-primary">NIN Modification Request Form</h3>
                        <p class="text-muted small mb-0">Submit your request accurately to ensure smooth processing.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-0 px-md-3">
            <div class="row mt-3 g-0 g-md-4">

                {{-- MODIFICATION REQUEST FORM --}}
                <div class="col-12 col-xl-5 mb-4">
                    <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                        <div
                            class="card-header bg-primary text-white p-3 p-md-4 border-0 text-center text-sm-start" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                            <h5 class="mb-0 fw-bold fs-15"><i class="bi bi-gear-fill me-2"></i>New Modification Request
                            </h5>
                            <p class="mb-0 small text-white-50 mt-1">Fees are non-refundable. Please verify all details.
                            </p>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            {{-- Alerts --}}
                            @if (session('status'))
                                <div class="alert alert-{{ session('status') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show rounded-3 shadow-sm border-0"
                                    role="alert">
                                    <i
                                        class="bi bi-{{ session('status') === 'success' ? 'check-circle' : 'exclamation-triangle' }}-fill me-2"></i>
                                    {{ session('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0"
                                    role="alert">
                                    <ul class="mb-0 small text-start">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('nin-modification.store') }}" class="row g-4">
                                @csrf

                                <!-- Modification Field Selection -->
                                <div class="col-12">
                                    <label for="service_field" class="form-label fw-semibold text-dark">Select
                                        Modification Field <span class="text-danger">*</span></label>
                                    <select name="service_field_id" id="service_field"
                                        class="form-select form-select-lg bg-light border-0 shadow-sm" required>
                                        <option value="">-- Select Modification Field --</option>
                                        @foreach ($serviceFields as $field)
                                            <option value="{{ $field->id }}"
                                                data-price="{{ $field->getPriceForUserType(auth()->user()->role) }}"
                                                data-description="{{ $field->description ?? 'No description available.' }}"
                                                {{ old('service_field_id') == $field->id ? 'selected' : '' }}>
                                                {{ $field->field_name }} -
                                                ₦{{ number_format($field->getPriceForUserType(auth()->user()->role), 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2">
                                        <small class="text-muted" id="field-description"></small>
                                    </div>
                                </div>

                                <!-- NIN ID Input -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark d-flex justify-content-between">
                                        <span>NIN ID <span class="text-danger">*</span></span>
                                        <button type="button" class="btn btn-outline-primary btn-sm py-0 border-0"
                                            data-bs-toggle="modal" data-bs-target="#sampleInfoModal">
                                            <i class="bi bi-info-circle"></i> Guide
                                        </button>
                                    </label>
                                    <div class="input-group input-group-lg shadow-sm">
                                        <span class="input-group-text bg-white border-0"><i
                                                class="bi bi-person-badge text-primary"></i></span>
                                        <input type="text" name="nin"
                                            class="form-control bg-light border-0 ps-0 text-center fw-bold"
                                            placeholder="11-digit NIN Number" value="{{ old('nin') }}" maxlength="11"
                                            minlength="11" pattern="[0-9]{11}" required>
                                    </div>
                                </div>

                                <!-- Data Description Section (Default) -->
                                <div class="col-12" id="generic-data-info">
                                    <label class="form-label fw-semibold text-dark">Data Information <span
                                            class="text-danger">*</span></label>
                                    <textarea name="description" id="description-field"
                                        class="form-control bg-light border-0 shadow-sm" rows="4"
                                        placeholder="Describe the modification needed..."
                                        required>{{ old('description') }}</textarea>
                                </div>

                                <!-- DOB Modification Wizard -->
                                <div class="col-12 d-none" id="dob-wizard">
                                    <div class="card bg-white border shadow-sm overflow-hidden mt-2" style="border-radius: 20px;">
                                        <div class="card-header bg-light border-bottom py-3 px-4">
                                            <h6 class="fw-bold text-primary mb-0"><i
                                                    class="bi bi-person-lines-fill me-2"></i>Attestation for DOB
                                                Modification</h6>
                                        </div>
                                        <div class="card-body p-4">
                                            {{-- Wizard Steps --}}
                                            <div class="wizard-step" id="step-1">
                                                <h6
                                                    class="text-muted border-bottom pb-2 mb-3 px-1 small fw-bold text-uppercase">
                                                    1/8: Personal Details</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6"><label class="small fw-semibold">First Name
                                                            <span class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[first_name]"
                                                            placeholder="First Name" disabled></div>
                                                    <div class="col-md-6"><label class="small fw-semibold">Surname <span
                                                                class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[surname]" placeholder="Surname"
                                                            disabled></div>
                                                    <div class="col-md-12"><label class="small fw-semibold">Middle
                                                            Name</label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[middle_name]"
                                                            placeholder="Middle Name (Optional)" disabled></div>
                                                    <div class="col-md-6"><label class="small fw-semibold">Gender <span
                                                                class="text-danger">*</span></label><select
                                                            class="form-select form-select-sm bg-light border-0 dob-input"
                                                            name="modification_data[gender]" disabled>
                                                            <option value="">Select</option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                        </select></div>
                                                    <div class="col-md-6"><label class="small fw-semibold">Marital
                                                            Status <span class="text-danger">*</span></label><select
                                                            class="form-select form-select-sm bg-light border-0 dob-input"
                                                            name="modification_data[marital_status]" disabled>
                                                            <option value="">Select</option>
                                                            <option value="Single">Single</option>
                                                            <option value="Married">Married</option>
                                                            <option value="Divorced">Divorced</option>
                                                            <option value="Widowed">Widowed</option>
                                                        </select></div>
                                                    <div class="col-12 text-end mt-4"><button type="button"
                                                            class="btn btn-primary rounded-pill px-4 btn-sm next-step"
                                                            data-next="step-2">Next <i
                                                                class="bi bi-arrow-right"></i></button></div>
                                                </div>
                                            </div>

                                            {{-- Other steps (Step 2 to 8) modernized similarly --}}
                                            <div class="wizard-step d-none" id="step-2">
                                                <h6
                                                    class="text-muted border-bottom pb-2 mb-3 px-1 small fw-bold text-uppercase">
                                                    2/8: DOB & Origin</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6"><label class="small fw-semibold">New DOB <span
                                                                class="text-danger">*</span></label><input type="date"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[new_dob]" disabled></div>
                                                    <div class="col-md-6"><label class="small fw-semibold">Nationality
                                                            <span class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[nationality]" value="Nigeria"
                                                            disabled></div>
                                                    <div class="col-md-6"><label class="small fw-semibold">State of
                                                            Origin <span class="text-danger">*</span></label><input
                                                            type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[state_of_origin]" disabled></div>
                                                    <div class="col-md-6"><label class="small fw-semibold">LGA of Origin
                                                            <span class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[lga_of_origin]" disabled></div>
                                                    <div class="col-md-12"><label class="small fw-semibold">Town of
                                                            Origin <span class="text-danger">*</span></label><input
                                                            type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[town_of_origin]" disabled></div>
                                                    <div class="col-12 text-end mt-4 d-flex justify-content-between">
                                                        <button type="button"
                                                            class="btn btn-light rounded-pill px-4 btn-sm prev-step"
                                                            data-prev="step-1"><i class="bi bi-arrow-left"></i>
                                                            Back</button><button type="button"
                                                            class="btn btn-primary rounded-pill px-4 btn-sm next-step"
                                                            data-next="step-3">Next <i
                                                                class="bi bi-arrow-right"></i></button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="wizard-step d-none" id="step-3">
                                                <h6
                                                    class="text-muted border-bottom pb-2 mb-3 px-1 small fw-bold text-uppercase">
                                                    3/8: Residence</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6"><label class="small fw-semibold">State <span
                                                                class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[residence_state]" disabled></div>
                                                    <div class="col-md-6"><label class="small fw-semibold">LGA <span
                                                                class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[residence_lga]" disabled></div>
                                                    <div class="col-md-12"><label class="small fw-semibold">Address
                                                            <span class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[residence_address]" disabled></div>
                                                    <div class="col-md-12"><label class="small fw-semibold">Phone <span
                                                                class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[phone_number]" disabled></div>
                                                    <div class="col-12 text-end mt-4 d-flex justify-content-between">
                                                        <button type="button"
                                                            class="btn btn-light rounded-pill px-4 btn-sm prev-step"
                                                            data-prev="step-2"><i class="bi bi-arrow-left"></i>
                                                            Back</button><button type="button"
                                                            class="btn btn-primary rounded-pill px-4 btn-sm next-step"
                                                            data-next="step-4">Next <i
                                                                class="bi bi-arrow-right"></i></button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="wizard-step d-none" id="step-4">
                                                <h6
                                                    class="text-muted border-bottom pb-2 mb-3 px-1 small fw-bold text-uppercase">
                                                    4/8: Birth Information</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-12"><label class="small fw-semibold">Place of
                                                            Birth <span class="text-danger">*</span></label><input
                                                            type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[place_of_birth]" disabled></div>
                                                    <div class="col-md-6"><label class="small fw-semibold">State of
                                                            Birth <span class="text-danger">*</span></label><input
                                                            type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[state_of_birth]" disabled></div>
                                                    <div class="col-md-6"><label class="small fw-semibold">LGA of Birth
                                                            <span class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[lga_of_birth]" disabled></div>
                                                    <div class="col-12 text-end mt-4 d-flex justify-content-between">
                                                        <button type="button"
                                                            class="btn btn-light rounded-pill px-4 btn-sm prev-step"
                                                            data-prev="step-3"><i class="bi bi-arrow-left"></i>
                                                            Back</button><button type="button"
                                                            class="btn btn-primary rounded-pill px-4 btn-sm next-step"
                                                            data-next="step-5">Next <i
                                                                class="bi bi-arrow-right"></i></button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="wizard-step d-none" id="step-5">
                                                <h6
                                                    class="text-muted border-bottom pb-2 mb-3 px-1 small fw-bold text-uppercase">
                                                    5/8: Socio-Economic</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6"><label class="small fw-semibold">Occupation
                                                            <span class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[occupation]" disabled></div>
                                                    <div class="col-md-6"><label class="small fw-semibold">Education
                                                            <span class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[education_level]" disabled></div>
                                                    <div class="col-12 text-end mt-4 d-flex justify-content-between">
                                                        <button type="button"
                                                            class="btn btn-light rounded-pill px-4 btn-sm prev-step"
                                                            data-prev="step-4"><i class="bi bi-arrow-left"></i>
                                                            Back</button><button type="button"
                                                            class="btn btn-primary rounded-pill px-4 btn-sm next-step"
                                                            data-next="step-6">Next <i
                                                                class="bi bi-arrow-right"></i></button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="wizard-step d-none" id="step-6">
                                                <h6
                                                    class="text-muted border-bottom pb-2 mb-3 px-1 small fw-bold text-uppercase">
                                                    6/8: Father's Details</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6"><label class="small fw-semibold">Surname <span
                                                                class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[father_surname]" disabled></div>
                                                    <div class="col-md-6"><label class="small fw-semibold">First Name
                                                            <span class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[father_firstname]" disabled></div>
                                                    <div class="col-12 text-end mt-4 d-flex justify-content-between">
                                                        <button type="button"
                                                            class="btn btn-light rounded-pill px-4 btn-sm prev-step"
                                                            data-prev="step-5"><i class="bi bi-arrow-left"></i>
                                                            Back</button><button type="button"
                                                            class="btn btn-primary rounded-pill px-4 btn-sm next-step"
                                                            data-next="step-7">Next <i
                                                                class="bi bi-arrow-right"></i></button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="wizard-step d-none" id="step-7">
                                                <h6
                                                    class="text-muted border-bottom pb-2 mb-3 px-1 small fw-bold text-uppercase">
                                                    7/8: Mother's Details</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6"><label class="small fw-semibold">Surname <span
                                                                class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[mother_surname]" disabled></div>
                                                    <div class="col-md-6"><label class="small fw-semibold">First Name
                                                            <span class="text-danger">*</span></label><input type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[mother_firstname]" disabled></div>
                                                    <div class="col-12 text-end mt-4 d-flex justify-content-between">
                                                        <button type="button"
                                                            class="btn btn-light rounded-pill px-4 btn-sm prev-step"
                                                            data-prev="step-6"><i class="bi bi-arrow-left"></i>
                                                            Back</button><button type="button"
                                                            class="btn btn-primary rounded-pill px-4 btn-sm next-step"
                                                            data-next="step-8">Next <i
                                                                class="bi bi-arrow-right"></i></button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="wizard-step d-none" id="step-8">
                                                <h6
                                                    class="text-muted border-bottom pb-2 mb-3 px-1 small fw-bold text-uppercase">
                                                    8/8: Registration Centre</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-12"><label class="small fw-semibold">Centre
                                                            Address <span class="text-danger">*</span></label><input
                                                            type="text"
                                                            class="form-control form-control-sm bg-light border-0 dob-input"
                                                            name="modification_data[reg_centre]" disabled></div>
                                                    <div
                                                        class="col-12 text-end mt-4 d-flex justify-content-between align-items-center">
                                                        <button type="button"
                                                            class="btn btn-light rounded-pill px-4 btn-sm prev-step"
                                                            data-prev="step-7"><i class="bi bi-arrow-left"></i>
                                                            Back</button>
                                                        <button type="submit"
                                                            class="btn btn-success rounded-pill px-4 btn-sm fw-bold shadow-sm"><i
                                                                class="bi bi-send-fill me-1"></i> Confirm &
                                                            Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fee & Balance Section -->
                                <div class="col-12">
                                    <div
                                        class="alert alert-info d-flex justify-content-between align-items-center mb-0 rounded-3 shadow-sm border-0">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-wallet2 fs-15 me-2 text-info"></i>
                                            <span class="fw-medium">Modification Fee:</span>
                                        </div>
                                        <strong id="field-price" class="fs-15 text-info">₦0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-end mt-2">
                                        <small class="text-muted">
                                            Wallet Balance: <span
                                                class="text-success fw-bold">₦{{ number_format($wallet->balance ?? 0, 2) }}</span>
                                        </small>
                                    </div>
                                </div>

                                {{-- Warning --}}
                                <div class="col-12">
                                    <div class="alert alert-success py-3 rounded-3 shadow-sm border-0 d-flex align-items-center">
                                        <i class="bi bi-exclamation-circle text-warning fs-15 me-3"></i>
                                        <div class="small">
                                            <strong>Is- refundable Service</strong><br>
                                            Please verify all details carefully before submission.
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-12 mt-2" id="generic-submit-btn">
                                    <button type="submit"
                                        class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center gap-2">
                                        Submit Modification <i class="bi bi-arrow-right-circle"></i>
                                    </button>
                                </div>

                                <div class="col-12 mt-2 d-none" id="dob-proceed-btn">
                                    <button type="button" id="proceed-attestation-btn"
                                        class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center gap-2">
                                        Proceed with Attestation <i class="bi bi-arrow-right-circle"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- SUBMISSION HISTORY -->
                <div class="col-12 col-xl-7 mt-2 mt-md-0">
                    <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                        <div
                            class="card-header bg-white p-3 p-md-4 border-bottom d-flex justify-content-center justify-content-sm-between align-items-center" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                            <h5 class="mb-0 fw-bold text-dark fs-15"><i
                                    class="bi bi-clock-history text-primary me-2"></i>Submission History</h5>
                        </div>

                        <div class="card-body p-3 p-md-4">
                            {{-- Filters --}}
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-5">
                                    <form method="GET" action="{{ route('nin-modification') }}">
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text bg-white border-0"><i
                                                    class="bi bi-search text-muted"></i></span>
                                            <input type="text" name="search" class="form-control bg-light border-0 ps-0"
                                                placeholder="Search by NIN..." value="{{ request('search') }}">
                                        </div>
                                    </form>
                                </div>

                                <div class="col-12 col-md-4">
                                    <form method="GET" action="{{ route('nin-modification') }}">
                                        <select name="status" class="form-select bg-light border-0 shadow-sm"
                                            onchange="this.form.submit()">
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
                                    <a href="{{ route('nin-modification') }}"
                                        class="btn btn-light w-100 rounded-pill border-0 shadow-sm fw-medium text-muted">Reset</a>
                                </div>
                            </div>

                            {{-- History Table --}}
                            <div class="table-responsive rounded-3 border">
                                <table class="table table-hover table-borderless align-middle mb-0 text-nowrap">
                                    <thead class="table-light border-bottom">
                                        <tr>
                                            <th class="ps-4">#</th>
                                            <th>NIN</th>
                                            <th>Field</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($crmSubmissions as $submission)
                                            <tr class="border-bottom">
                                                <td class="ps-4 text-muted">
                                                    {{ $loop->iteration + ($crmSubmissions->currentPage() - 1) * $crmSubmissions->perPage() }}
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-dark d-block">{{ $submission->nin }}</span>
                                                    <small class="text-muted" style="font-size: 0.7rem;">Ref:
                                                        {{ $submission->reference }}</small>
                                                </td>
                                                <td>
                                                    <span
                                                        class="small text-muted">{{ $submission->service_field_name ?? 'N/A' }}</span>
                                                    <br>
                                                    <small
                                                        class="text-primary fw-medium">{{ $submission->submission_date->format('M d, Y') }}</small>
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
                                                    <span
                                                        class="badge bg-{{ $badge['c'] }}-subtle text-{{ $badge['c'] }} px-3 py-2 rounded-pill fw-semibold border border-{{ $badge['c'] }}-subtle">
                                                        <i class="bi bi-{{ $badge['i'] }} me-1"></i>
                                                        {{ ucfirst($submission->status) }}
                                                    </span>
                                                </td>

                                                <td class="pe-4">
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
                                                        class="btn btn-sm btn-light text-primary shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                                                        data-bs-toggle="modal" data-bs-target="#commentModal"
                                                        data-comment="{{ $submission->comment ?? 'No comment yet.' }}"
                                                        data-reference="{{ $submission->reference }}"
                                                        data-file-url="{{ $fileUrl }}"
                                                        data-approved-by="{{ $submission->approved_by }}"
                                                        title="View Response" style="width: 35px; height: 35px;">
                                                        <i class="bi bi-chat-left-text-fill fs-15"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-5">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3"
                                                            style="width: 60px; height: 60px;">
                                                            <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                                                        </div>
                                                        <h6 class="fw-semibold">No requests found</h6>
                                                        <p class="small text-muted mb-0">Your submitted NIN modification
                                                            requests will appear here.</p>
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

        <!-- Sample/Guide Info Modal -->
        <div class="modal fade" id="sampleInfoModal" tabindex="-1" aria-labelledby="sampleInfoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <div class="modal-header bg-primary text-white py-3 border-0" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                        <h4 class="modal-title fw-bold fs-18">
                            <i class="bi bi-info-square-fill me-2"></i> NIN Modification Guidelines
                        </h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="text-center mb-4">
                            <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 70px; height: 70px;">
                                <i class="bi bi-journal-text fs-1"></i>
                            </div>
                            <h5 class="fw-bold">How to submit a modification request?</h5>
                            <p class="text-muted small">Follow these steps to ensure your request is processed without
                                delay.</p>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light p-3 shadow-sm" style="border-radius: 20px;">
                                    <h6 class="fw-bold text-primary mb-3">
                                        <i class="bi bi-1-circle-fill me-2"></i> Initial Verification
                                    </h6>
                                    <ul class="list-unstyled small mb-0 d-grid gap-2">
                                        <li class="d-flex align-items-start">
                                            <i class="bi bi-check2-circle text-success me-2 mt-1"></i>
                                            <span>Ensure your <strong>11-digit NIN</strong> is typed correctly.</span>
                                        </li>
                                        <li class="d-flex align-items-start">
                                            <i class="bi bi-check2-circle text-success me-2 mt-1"></i>
                                            <span>Double-check that you have chosen the <strong>correct Modification
                                                    Field</strong>.</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light p-3 shadow-sm" style="border-radius: 20px;">
                                    <h6 class="fw-bold text-primary mb-3">
                                        <i class="bi bi-2-circle-fill me-2"></i> Documentation
                                    </h6>
                                    <ul class="list-unstyled small mb-0 d-grid gap-2">
                                        <li class="d-flex align-items-start">
                                            <i class="bi bi-check2-circle text-success me-2 mt-1"></i>
                                            <span>For <strong>Name Change</strong>, specify the Full NEW Name.</span>
                                        </li>
                                        <li class="d-flex align-items-start">
                                            <i class="bi bi-check2-circle text-success me-2 mt-1"></i>
                                            <span>For <strong>DOB</strong>, you must complete the 8-step attestation
                                                wizard carefully.</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-white border shadow-sm" style="border-radius: 20px;">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-lightbulb-fill text-warning me-2"></i>
                                Correct Submission Example:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mb-0 small">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted fw-medium" style="width: 120px;">Field:</td>
                                            <td class="fw-bold text-primary">NAME CORRECTION</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-medium">Description:</td>
                                            <td class="text-dark">Please correct my first name from 'Jhon' to 'JOHN' and
                                                surname to 'DOE'.</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-medium">Reason:</td>
                                            <td class="text-dark italic">Typing error during initial registration at
                                                NIMC centre.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div
                            class="alert alert-warning border-0 mt-4 mb-0 d-flex align-items-center p-3 shadow-sm" style="border-radius: 20px;">
                            <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-warning"></i>
                            <div class="small">
                                <strong class="d-block mb-1">Important Notice:</strong>
                                Fees are deducted instantly and are <strong>non-refundable</strong> if the request was
                                succesfully sent to NIMC portal. Even if the request delay to effect on the NIN record
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4">
                        <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm"
                            data-bs-dismiss="modal">
                            I Understood, Proceed <i class="bi bi-check-circle-fill ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    </div>

    {{-- SCRIPTS (Keep functional logic unchanged) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const serviceField = document.getElementById('service_field');
            const fieldPrice = document.getElementById('field-price');
            const fieldDescription = document.getElementById('field-description');

            const genericDataInfo = document.getElementById('generic-data-info');
            const genericInput = document.getElementById('description-field');
            const dobWizard = document.getElementById('dob-wizard');

            const genericSubmitBtn = document.getElementById('generic-submit-btn');
            const dobProceedBtn = document.getElementById('dob-proceed-btn');
            const proceedAttestationBtn = document.getElementById('proceed-attestation-btn');

            // Handle Field Change
            serviceField.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                const description = selectedOption.getAttribute('data-description');
                const fieldName = selectedOption.textContent.toLowerCase();

                // Update Price
                if (price) {
                    fieldPrice.textContent = '₦' + new Intl.NumberFormat('en-NG').format(price);
                } else {
                    fieldPrice.textContent = '₦0.00';
                }

                // Update Description
                if (description) {
                    fieldDescription.textContent = description;
                } else {
                    fieldDescription.textContent = '';
                }

                // Toggle Form Mode
                if (fieldName.includes('date of birth') || fieldName.includes('dob')) {
                    genericDataInfo.classList.add('d-none');
                    genericInput.removeAttribute('required');

                    genericSubmitBtn.classList.add('d-none');
                    dobProceedBtn.classList.remove('d-none');
                    dobWizard.classList.add('d-none');
                } else {
                    genericDataInfo.classList.remove('d-none');
                    genericInput.setAttribute('required', 'required');

                    genericSubmitBtn.classList.remove('d-none');
                    dobProceedBtn.classList.add('d-none');
                    dobWizard.classList.add('d-none');
                    disableWizardInputs();
                }
            });

            // Proceed Button Click
            proceedAttestationBtn.addEventListener('click', function () {
                const ninInput = document.querySelector('input[name="nin"]');
                if (!ninInput.value || ninInput.value.length !== 11) {
                    alert('Please enter a valid 11-digit NIN first.');
                    ninInput.focus();
                    return;
                }

                dobWizard.classList.remove('d-none');
                enableWizardInputs();
                dobProceedBtn.classList.add('d-none');

                document.querySelectorAll('.wizard-step').forEach(el => el.classList.add('d-none'));
                document.getElementById('step-1').classList.remove('d-none');
            });

            function enableWizardInputs() {
                document.querySelectorAll('.dob-input').forEach(input => {
                    if (!input.name.includes('middle_name') && !input.name.includes('middlename')) {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                    }
                    input.removeAttribute('disabled');
                });
            }

            function disableWizardInputs() {
                document.querySelectorAll('.dob-input').forEach(input => {
                    input.removeAttribute('required');
                    input.setAttribute('disabled', 'disabled');
                });
            }

            // Wizard Navigation
            document.querySelectorAll('.next-step').forEach(button => {
                button.addEventListener('click', function () {
                    const currentStep = this.closest('.wizard-step');
                    const nextStepId = this.getAttribute('data-next');

                    const inputs = currentStep.querySelectorAll('input, select');
                    let valid = true;
                    inputs.forEach(input => {
                        if (input.hasAttribute('required') && !input.value) {
                            valid = false;
                            input.classList.add('is-invalid');
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });

                    if (valid) {
                        currentStep.classList.add('d-none');
                        document.getElementById(nextStepId).classList.remove('d-none');
                    } else {
                        alert('Please fill all required fields in this step.');
                    }
                });
            });

            document.querySelectorAll('.prev-step').forEach(button => {
                button.addEventListener('click', function () {
                    const currentStep = this.closest('.wizard-step');
                    const prevStepId = this.getAttribute('data-prev');

                    currentStep.classList.add('d-none');
                    document.getElementById(prevStepId).classList.remove('d-none');
                });
            });
        });
    </script>
</x-app-layout>
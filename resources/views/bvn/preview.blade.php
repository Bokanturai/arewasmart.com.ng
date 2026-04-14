<x-app-layout>
    <div class="container-fluid px-0 px-md-3 py-4">

        <!-- Page Title -->
        <div class="mb-4 text-center">
            <h3 class="fw-bold text-primary">
                <i class="fa-solid fa-file-circle-check me-2"></i> Enrollment Preview
            </h3>
            <p class="text-muted mb-0">
                Review the details of this enrollment record below.
            </p>
        </div>

        <!-- Card Preview -->
        <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
            <div class="card-body p-4">
                <div class="row g-0 g-md-4">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong class="text-secondary">Ticket:</strong>  
                            <span class="fw-bold">{{ $record->ticket_number }}</span>
                        </p>
                        <p class="mb-2">
                            <strong class="text-secondary">BVN:</strong>  
                            <span class="fw-bold text-success">{{ $record->bvn }}</span>
                        </p>
                        <p class="mb-2">
                            <strong class="text-secondary">BMS ID:</strong>  
                            <span>{{ $record->bms_import_id }}</span>
                        </p>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong class="text-secondary">Status:</strong>  
                            @php
                                $statusClass = match($record->validation_status) {
                                    'SUCCESSFUL' => 'bg-success',
                                    'FAILED', 'REJECTED' => 'bg-danger',
                                    'PENDING' => 'bg-warning text-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }} px-3 py-2">
                                {{ $record->validation_status }}
                            </span>
                        </p>
                        <p class="mb-2">
                            <strong class="text-secondary">Message:</strong>  
                            <span>{{ $record->validation_message }}</span>
                        </p>
                        <p class="mb-0">
                            <strong class="text-secondary">Validation Date:</strong>  
                            <span>{{ $record->validation_date }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CRM Report Section -->
        <div class="alert alert-warning shadow-sm border-start border-5 border-warning mt-4 rounded-3 p-4">
            <div class="d-flex align-items-start">
                <i class="fa-solid fa-triangle-exclamation text-warning me-3 fs-3"></i>
                <div>
                    <h5 class="fw-bold text-dark mb-1">Failed Enrollment?</h5>
                    <p class="mb-2 text-dark">
                        If this enrollment failed, you can report a CRM for immediate follow-up.
                    </p>
                    <a href="{{ route('bvn-crm') }}" class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm">
                        Apply CRM Now <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-4">
            <a href="{{ route('modification') }}" class="btn btn-success me-2 rounded-pill px-4">
                Modification
            </a>
            <a href="{{ route('phone.search.index') }}" class="btn btn-primary me-2 rounded-pill px-4">
                BVN Search
            </a>
        </div>

    </div>
</x-app-layout>

<x-app-layout>
    <title>Arewa Smart - {{ $title ?? 'Enrolment Report' }}</title>
    <div class="page-body">
        <div class="container-fluid px-0 px-md-3">
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <h3 class="fw-bold text-primary">Enrolment Report</h3>
                        <p class="text-muted small mb-0">View and filter enrolment records.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-0 px-md-3">
            <div class="row g-0 g-md-4">
                <!-- Filter Section -->
                <div class="col-12 mb-4">
                    <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                        <div class="card-header bg-primary text-white" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                            <h5 class="mb-0">Filter Enrolments</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('enrolment.report') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Agent Code</label>
                                    <input type="text" name="agent_code" class="form-control" value="{{ request('agent_code') }}" placeholder="Enter Agent Code">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Search Ticket ID</label>
                                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Ticket ID">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Filter Report</button>
                                    <a href="{{ route('enrolment.report') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Stats Section -->
                @if(request('agent_code') && $stats['agent_name'])
                <div class="col-12 mt-2 mt-md-0 mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white h-100" style="border-radius: 20px;">
                                <div class="card-body">
                                    <h6 class="card-title text-white-50">Total Enrolment</h6>
                                    <h2 class="mb-0">{{ $stats['total'] }}</h2>
                                    <small class="text-white-50">{{ $stats['agent_name'] }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white h-100" style="border-radius: 20px;">
                                <div class="card-body">
                                    <h6 class="card-title text-white-50">Successful</h6>
                                    <h2 class="mb-0">{{ $stats['successful'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white h-100" style="border-radius: 20px;">
                                <div class="card-body">
                                    <h6 class="card-title text-white-50">Failed</h6>
                                    <h2 class="mb-0">{{ $stats['failed'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark h-100" style="border-radius: 20px;">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Ongoing</h6>
                                    <h2 class="mb-0">{{ $stats['ongoing'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Table Section -->
                <div class="col-12 mt-2 mt-md-0">
                    <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                        <div class="card-header bg-white border-bottom-0" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                             <h5 class="mb-0 fw-bold text-primary">Enrolment List</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0 align-middle">
                                    <thead class="bg-light text-primary">
                                        <tr>
                                            <th>#</th>
                                            <th>Ticket Number</th>
                                            <th>BVN</th>
                                            <th>Agent Name</th>
                                            <th>Agent Code</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($enrollments as $enrollment)
                                            <tr>
                                                <td>{{ $loop->iteration + $enrollments->firstItem() - 1 }}</td>
                                                <td>{{ $enrollment->ticket_number }}</td>
                                                <td>{{ $enrollment->bvn }}</td>
                                                <td>{{ $enrollment->agent_name }}</td>
                                                <td>{{ $enrollment->agent_code }}</td>
                                                <td>
                                                    <span class="badge bg-{{ match(strtolower((string)$enrollment->validation_status)) {
                                                        'successful' => 'success',
                                                        'failed' => 'danger',
                                                        'rejected' => 'danger',
                                                        'pending', 'processing' => 'warning',
                                                        default => 'info'
                                                    } }}">
                                                        {{ ucfirst($enrollment->validation_status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $enrollment->created_at->format('d M Y, h:i A') }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-primary view-details-btn"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#enrolmentDetailsModal"
                                                        data-details='@json($enrollment)'>
                                                        <i class="ti ti-eye"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    @if(request('agent_code') || request('search'))
                                                        No records found.
                                                    @else
                                                        Enter Agent Code or Ticket ID to view enrolments.
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                         <div class="card-footer bg-white" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                            {{ $enrollments->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrolment Details Modal -->
        <div class="modal fade" id="enrolmentDetailsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">Enrolment Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3" id="modalContent">
                            <!-- Content will be populated by JS -->
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('enrolmentDetailsModal');
                const modalContent = document.getElementById('modalContent');

                modal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const details = JSON.parse(button.getAttribute('data-details'));

                    let html = '';
                    const fields = {
                        'Ticket Number': details.ticket_number,
                        'BVN': details.bvn,
                        'Agent Name': details.agent_name,
                        'Agent Code': details.agent_code,
                        'Enroller Code': details.enroller_code,
                        'Institution Name': details.agt_mgt_inst_name,
                        'Institution Code': details.agt_mgt_inst_code,
                        'Validation Status': details.validation_status,
                        'Validation Message': details.validation_message,
                        'Amount': details.amount,
                        'Capture Date': details.capture_date,
                        'Sync Date': details.sync_date,
                        'Validation Date': details.validation_date,
                        'Latitude': details.latitude,
                        'Longitude': details.longitude,
                        'BMS Import ID': details.bms_import_id
                    };

                    for (const [label, value] of Object.entries(fields)) {
                        if (value) {
                            html += `
                                <div class="col-md-6">
                                    <div class="p-2 border rounded bg-light h-100">
                                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">${label}</small>
                                        <span class="fw-medium text-dark">${value}</span>
                                    </div>
                                </div>
                            `;
                        }
                    }

                    modalContent.innerHTML = html;
                });
            });
        </script>
    </div>
</x-app-layout>

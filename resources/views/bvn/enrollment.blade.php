<x-app-layout>
    <x-slot name="title">BVN Enrolment Report</x-slot>
      <div class="page-body">
    <div class="container-fluid px-0 px-md-3">
      <div class="page-title">
        <div class="row g-0 g-md-4">
          <div class="col-sm-6 col-12">
          </div>
        </div>
      </div>
    </div>
    <div class="container py-4">
        <!-- Logo -->
        <div class="text-center mb-4">
            <a href="/">
                <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Zepa Logo" class="img-fluid" style="max-width: 60px;">
            </a>
        </div>

        <!-- Title -->
        <h2 class="text-center text-primary fw-bold mb-4">
            <i class="fa-solid fa-user-shield me-2"></i> Agent Enrollment Records
        </h2>

        <!-- Action Buttons -->
        <div class="d-flex flex-wrap gap-3 mb-4 justify-content-center">
            <a href="{{ route('bvn-crm') }}" class="btn btn-warning flex-grow-1 fw-bold shadow-sm">
                <i class="fa-solid fa-file-circle-exclamation me-2"></i> Report CRM
            </a>
            <a href="{{ route('modification') }}" class="btn btn-primary flex-grow-1 fw-bold shadow-sm">
                <i class="fa-solid fa-pen-to-square me-2"></i> Modify BVN
            </a>
            <a href="{{ route('bvn.index') }}" class="btn btn-success flex-grow-1 fw-bold shadow-sm">
                <i class="fa-solid fa-magnifying-glass me-2"></i> Become an Agent
            </a>
        </div>

        <!-- Search Card -->
        <div class="card shadow-lg border-0 mb-4" style="border-radius: 20px; overflow: hidden;">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('enrollments.index') }}" class="row g-3 justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">
                                <i class="fa-solid fa-user-tag"></i>
                            </span>
                            <input 
                                type="text" 
                                name="agent_code" 
                                placeholder="Enter Agent Code" 
                                value="{{ $agentCode }}" 
                                required 
                                class="form-control form-control-lg"
                            >
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Agent Info -->
        @if ($agentCode && $agentInfo)
            <div class="card shadow-lg border-0 mt-2 mt-md-0 mb-4" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body bg-light-blue border-start border-4 border-primary p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-2 text-primary fw-bold">
                                <i class="fa-solid fa-id-card me-2"></i> Agent Information
                            </h5>
                            <p class="mb-1"><strong class="text-dark">Agent Name:</strong> {{ $agentInfo->agent_name }}</p>
                            <p class="mb-0"><strong class="text-dark">Agent Code:</strong> {{ $agentInfo->agent_code }}</p>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <span class="badge bg-primary-subtle text-primary fs-6">
                                <i class="fa-solid fa-database me-1"></i> 
                                {{ $enrollmentCount }} Records
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card shadow-lg border-0 mt-2 mt-md-0" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-white border-0 p-4 pb-2" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 text-primary fw-bold">
                                <i class="fa-solid fa-table-list me-2"></i> Enrollment Records
                            </h5>
                            <p class="text-muted mb-0">
                                Any enrollment that does not <code>appear here</code> means it was not processed through our system.
                            </p>
                        </div>
                        <div class="mt-2">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm active" id="showAllColumns">
                                    Full View
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="showEssentialColumns">
                                    Compact View
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="recordsTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-bold">Ticket #</th>
                                    <th class="fw-bold">BVN</th>
                                    <th class="fw-bold">Validation Status</th>
                                    <th class="all fw-bold">BMS ID</th>
                                    <th class="all fw-bold text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

        @elseif($agentCode)
            <div class="alert alert-warning text-center shadow-sm py-3">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                No records found for this Agent Code.
            </div>
        @endif
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <style>
        /* Custom color for the table header */
        .table-light {
            background-color: #f8fafc;
        }
        
        /* Card styling */
        .bg-light-blue {
            background-color: #f0f7ff;
        }
        
        
        /* DataTable custom styling */
        .dataTables_wrapper {
            padding: 0 1rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            transition: border-color 0.15s ease-in-out;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 0.375rem;
            padding: 0.375rem 1.75rem 0.375rem 0.75rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.375rem;
            margin: 0 0.125rem;
            border: 1px solid transparent;
            transition: all 0.15s ease-in-out;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #0d6efd !important;
            color: white !important;
            border-color: #0d6efd;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #0b5ed7 !important;
            color: white !important;
            border-color: #0a58ca;
        }
        table.dataTable tbody tr {
            transition: background-color 0.15s ease-in-out;
        }
        table.dataTable tbody tr:hover {
            background-color: #f8f9fa !important;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_length {
                text-align: center;
            }
            .dataTables_wrapper .dataTables_filter {
                text-align: center !important;
                margin-top: 1rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
    @if ($agentCode && $agentInfo)
    $(document).ready(function() {
        var table = $('#recordsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('enrollments.data') }}",
                data: {
                    agent_code: "{{ $agentCode }}"
                }
            },
            columns: [
                { 
                    data: 'ticket_number',
                    className: 'fw-bold'
                },
                { 
                    data: 'bvn',
                    render: function(data, type, row) {
                        return '<span class="text-monospace">' + data + '</span>';
                    }
                },
               {
    data: 'validation_status',
    render: function(data, type, row) {
        let badgeClass = 'bg-secondary'; // default if status doesn't match

        if (data === 'SUCCESSFUL') {
            badgeClass = 'bg-success';
        } else if (data === 'FAILED' || data === 'REJECTED') {
            badgeClass = 'bg-danger';
        } else if (data === 'PENDING') {
            badgeClass = 'bg-warning text-dark';
        }

        return '<span class="badge ' + badgeClass + ' px-2 py-1">' + data + '</span>';
    }
},

                { 
                    data: 'bms_import_id',
                    className: 'text-muted'
                },
              
                { 
                    data: 'action', 
                    orderable: false, 
                    searchable: false,
                    className: 'text-center'
                }
            ],
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No records available",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            },
            dom: '<"top"lf>rt<"bottom"ip>',
            initComplete: function() {
            }
        });

        // Toggle between full and compact view
        $('#showEssentialColumns').click(function() {
            $('.all').hide();
            table.columns.adjust();
            $(this).addClass('active');
            $('#showAllColumns').removeClass('active');
        });

        $('#showAllColumns').click(function() {
            $('.all').show();
            table.columns.adjust();
            $(this).addClass('active');
            $('#showEssentialColumns').removeClass('active');
        });
    });
    @endif
    </script>
    @endpush
</x-app-layout>
{{-- Flash Messages Component --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 text-center py-2 mb-3" role="alert">
        <small class="fw-bold"><i class="ti ti-circle-check me-2"></i> {{ session('success') }}</small>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 text-center py-2 mb-3" role="alert">
        <small class="fw-bold"><i class="ti ti-alert-circle me-2"></i> {{ session('error') }}</small>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 py-3 mb-3" role="alert">
        <div class="d-flex align-items-center mb-2">
            <i class="ti ti-alert-triangle me-2 fs-5"></i>
            <strong class="small">Please fix the following:</strong>
        </div>
        <ul class="mb-0 text-start small ps-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

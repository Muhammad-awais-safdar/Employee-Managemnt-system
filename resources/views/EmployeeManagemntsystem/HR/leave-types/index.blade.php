@extends('EmployeeManagemntsystem.Layout.App')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <!-- Breadcrumb Card -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-divide p-0 mb-0">
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a href="{{ route('HR.dashboard') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item fw-medium active" aria-current="page">
                                Leave Types
                            </li>
                        </ol>
                    </nav>
                    <h5 class="fw-bold mb-0">Leave Types Overview</h5>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Search -->
                        <div class="search-wrapper">
                            <input type="text" class="form-control" placeholder="Search leave types..." id="searchInput">
                        </div>
                        
                        <!-- Filters -->
                        <div class="filter-wrapper d-flex gap-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <div class="alert alert-info mb-0 py-2 px-3">
                            <small><i class="ti ti-info-circle me-1"></i>Read-only view for HR role</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Types Table -->
        <div class="card leave-types-card">
            <div class="card-header bg-light border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar avtar-md bg-primary bg-opacity-10 text-primary me-3">
                            <i class="ti ti-settings"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Leave Type Configurations</h5>
                            <small class="text-muted">{{ $leaveTypes->total() }} leave types configured</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($leaveTypes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 fw-semibold">Leave Type</th>
                                <th class="border-0 fw-semibold">Code</th>
                                <th class="border-0 fw-semibold">Max Days/Year</th>
                                <th class="border-0 fw-semibold">Configuration</th>
                                <th class="border-0 fw-semibold">Applicable Roles</th>
                                <th class="border-0 fw-semibold">Status</th>
                                <th class="border-0 fw-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveTypes as $leaveType)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avtar-sm {{ $leaveType->is_active ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 text-{{ $leaveType->is_active ? 'success' : 'secondary' }} me-2">
                                            <i class="ti ti-calendar"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $leaveType->name }}</div>
                                            <small class="text-muted">{{ Str::limit($leaveType->description, 40) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary fs-6">{{ $leaveType->code }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-primary">{{ $leaveType->max_days_per_year }}</div>
                                    @if($leaveType->carry_forward_limit)
                                        <small class="text-muted">Carry: {{ $leaveType->carry_forward_limit }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if($leaveType->is_paid)
                                            <span class="badge bg-success badge-sm">Paid</span>
                                        @else
                                            <span class="badge bg-warning badge-sm">Unpaid</span>
                                        @endif
                                        
                                        @if($leaveType->requires_medical_certificate)
                                            <span class="badge bg-info badge-sm">Medical Cert</span>
                                        @endif
                                        
                                        @if($leaveType->min_notice_days > 0)
                                            <span class="badge bg-secondary badge-sm">{{ $leaveType->min_notice_days }}d Notice</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($leaveType->applicable_roles)
                                        <div class="role-tags">
                                            @foreach($leaveType->applicable_roles as $role)
                                                <span class="badge bg-light text-dark badge-sm">{{ $role }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <small class="text-muted">All Roles</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $leaveType->status_badge_class }}">
                                        {{ $leaveType->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('HR.leave-types.show', $leaveType) }}">
                                        <i class="ti ti-eye me-1"></i>View Details
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-light border-0">
                    {{ $leaveTypes->withQueryString()->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <div class="empty-state">
                        <div class="empty-icon mb-3">
                            <i class="ti ti-settings-off text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-1">No leave types configured</h6>
                        <p class="text-muted small mb-3">Contact your administrator to set up leave types</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.leave-types-card {
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.6);
}

.table {
    border-radius: 12px;
    overflow: hidden;
    border: none;
}

.table-light th {
    background-color: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 600;
    color: #475569;
    padding: 1rem 0.75rem;
}

.table-hover tbody tr {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: rgba(99, 102, 241, 0.04);
}

.table td {
    padding: 1rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.table tbody tr:last-child td {
    border-bottom: none;
}

.role-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.search-wrapper {
    min-width: 300px;
}

.filter-wrapper select {
    min-width: 120px;
}

.empty-state {
    padding: 3rem 1rem;
}

.empty-icon {
    opacity: 0.6;
}

@media (max-width: 768px) {
    .search-wrapper {
        min-width: 200px;
    }
    
    .filter-wrapper {
        flex-direction: column;
    }
    
    .filter-wrapper select {
        min-width: 100px;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .role-tags .badge {
        font-size: 0.65rem;
    }
}
</style>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    debounce(performSearch, 300)();
});

document.getElementById('statusFilter').addEventListener('change', performSearch);

function performSearch() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    
    const url = new URL(window.location);
    url.searchParams.delete('page'); // Reset pagination
    
    if (search) {
        url.searchParams.set('search', search);
    } else {
        url.searchParams.delete('search');
    }
    
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    window.location = url;
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Show success/error messages
@if(session('success'))
    toastr.success("{{ session('success') }}", 'Success');
@endif

@if(session('error'))
    toastr.error("{{ session('error') }}", 'Error');
@endif
</script>
@endsection
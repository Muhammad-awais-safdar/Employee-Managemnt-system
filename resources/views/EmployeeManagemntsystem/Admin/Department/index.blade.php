@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'Departments')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<style>
    .stats-card {
        border-left: 4px solid #3E007C;
        transition: all 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    .btn-purple {
        background-color: #3E007C;
        border-color: #3E007C;
        color: white;
    }
    .btn-purple:hover {
        background-color: #7100E2;
        border-color: #7100E2;
        color: white;
    }
    .status-toggle {
        cursor: pointer;
    }
    .table-actions .btn {
        margin: 0 2px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const departmentName = this.dataset.name;
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Delete department "${departmentName}"? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Status toggle
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const departmentId = this.dataset.id;
            const status = this.checked;
            
            fetch(`/admin/departments/${departmentId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const badge = document.querySelector(`#status-badge-${departmentId}`);
                    badge.className = `badge ${status ? 'bg-success' : 'bg-danger'}`;
                    badge.textContent = data.status_label;
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    this.checked = !status;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                this.checked = !status;
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while updating status.'
                });
            });
        });
    });
});
</script>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-purple mb-1">Departments</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('Admin.dashboard') }}" class="text-purple">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Departments</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    @can('assignUsers', App\Models\Department::class)
                    <a href="{{ route('Admin.departments.assignments') }}" class="btn btn-outline-purple">
                        <i class="ti ti-users-group me-2"></i>Manage Assignments
                    </a>
                    @endcan
                    @can('create', App\Models\Department::class)
                    <a href="{{ route('Admin.departments.create') }}" class="btn btn-purple">
                        <i class="ti ti-circle-plus me-2"></i>Add Department
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2 fw-normal">Total Departments</h6>
                            <h3 class="fw-bold text-purple mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-purple-subtle text-purple rounded">
                                <i class="ti ti-building-community fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card h-100" style="border-left-color: #28a745;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2 fw-normal">Active</h6>
                            <h3 class="fw-bold text-success mb-0">{{ $stats['active'] }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-success-subtle text-success rounded">
                                <i class="ti ti-check-circle fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card h-100" style="border-left-color: #dc3545;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2 fw-normal">Inactive</h6>
                            <h3 class="fw-bold text-danger mb-0">{{ $stats['inactive'] }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-danger-subtle text-danger rounded">
                                <i class="ti ti-x-circle fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card h-100" style="border-left-color: #17a2b8;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2 fw-normal">With Users</h6>
                            <h3 class="fw-bold text-info mb-0">{{ $stats['with_users'] }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-info-subtle text-info rounded">
                                <i class="ti ti-users fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Search departments...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="sort_by" class="form-label">Sort By</label>
                            <select class="form-select" id="sort_by" name="sort_by">
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                                <option value="users_count" {{ request('sort_by') == 'users_count' ? 'selected' : '' }}>User Count</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-purple">
                                    <i class="ti ti-search"></i>
                                </button>
                                <a href="{{ route('Admin.departments.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-building-community me-2"></i>Departments List
                        </h5>
                        <small class="text-muted">{{ $departments->total() }} total departments</small>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($departments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">Department</th>
                                    @if(auth()->user()->hasRole('superAdmin'))
                                    <th class="fw-semibold">Company</th>
                                    @endif
                                    <th class="fw-semibold">Contact Info</th>
                                    <th class="fw-semibold">Users</th>
                                    <th class="fw-semibold">Status</th>
                                    <th class="fw-semibold">Created</th>
                                    <th class="fw-semibold text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departments as $department)
                                <tr>
                                    <td>
                                        <div>
                                            <h6 class="mb-1 fw-semibold text-dark">{{ $department->name }}</h6>
                                            <p class="text-muted mb-0 fs-14">
                                                {{ $department->description ? Str::limit($department->description, 60) : 'No description' }}
                                            </p>
                                            @if($department->location)
                                            <small class="text-muted">
                                                <i class="ti ti-map-pin me-1"></i>{{ $department->location }}
                                            </small>
                                            @endif
                                        </div>
                                    </td>
                                    @if(auth()->user()->hasRole('superAdmin'))
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <div class="avatar-title bg-primary-subtle text-primary rounded">
                                                    <i class="ti ti-building fs-14"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fs-14">{{ $department->company->company_name ?? 'N/A' }}</h6>
                                                <small class="text-muted">Company</small>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                    <td>
                                        @if($department->phone || $department->email)
                                        <div class="fs-14">
                                            @if($department->phone)
                                            <div class="mb-1">
                                                <i class="ti ti-phone me-2 text-muted"></i>{{ $department->phone }}
                                            </div>
                                            @endif
                                            @if($department->email)
                                            <div>
                                                <i class="ti ti-mail me-2 text-muted"></i>{{ $department->email }}
                                            </div>
                                            @endif
                                        </div>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-info-subtle text-info me-2">{{ $department->users_count }}</span>
                                            <small class="text-muted">users</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span id="status-badge-{{ $department->id }}" 
                                                  class="badge {{ $department->status_badge_class }}">
                                                {{ $department->status_label }}
                                            </span>
                                            @can('update', $department)
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle" type="checkbox" 
                                                       data-id="{{ $department->id }}" 
                                                       {{ $department->status ? 'checked' : '' }}>
                                            </div>
                                            @endcan
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fs-14">
                                            <div>{{ $department->formatted_created_at }}</div>
                                            <small class="text-muted">{{ $department->created_at->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end table-actions">
                                            <a href="{{ route('Admin.departments.show', $department) }}" 
                                               class="btn btn-sm btn-outline-info" title="View">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            @can('update', $department)
                                            <a href="{{ route('Admin.departments.edit', $department) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            @endcan
                                            @can('delete', $department)
                                            <form action="{{ route('Admin.departments.destroy', $department) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn" 
                                                        data-name="{{ $department->name }}" title="Delete">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="ti ti-building-community text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted">No departments found</h5>
                        <p class="text-muted mb-4">Get started by creating your first department.</p>
                        @can('create', App\Models\Department::class)
                        <a href="{{ route('Admin.departments.create') }}" class="btn btn-purple">
                            <i class="ti ti-circle-plus me-2"></i>Create Department
                        </a>
                        @endcan
                    </div>
                    @endif
                </div>
                @if($departments->hasPages())
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted fs-14">
                            Showing {{ $departments->firstItem() }} to {{ $departments->lastItem() }} of {{ $departments->total() }} departments
                        </div>
                        {{ $departments->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}'
    });
});
</script>
@endif
@endsection
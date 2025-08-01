@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'Department Details')

@push('styles')
<style>
.text-purple {
    color: #3E007C !important;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: #6c757d;
}

.breadcrumb-item a {
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #7100E2 !important;
}

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

.info-item {
    padding: 12px 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.user-avatar {
    width: 32px;
    height: 32px;
    background: linear-gradient(45deg, #3E007C, #7100E2);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-purple mb-1">{{ $department->name }}</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('Admin.dashboard') }}" class="text-purple">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('Admin.departments.index') }}" class="text-purple">Departments</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $department->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    @can('update', $department)
                    <a href="{{ route('Admin.departments.edit', $department) }}" class="btn btn-outline-primary">
                        <i class="ti ti-edit me-2"></i>Edit Department
                    </a>
                    @endcan
                    @can('delete', $department)
                    <form action="{{ route('Admin.departments.destroy', $department) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-outline-danger delete-btn" 
                                data-name="{{ $department->name }}">
                            <i class="ti ti-trash me-2"></i>Delete
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2 fw-normal">Total Users</h6>
                            <h3 class="fw-bold text-purple mb-0">{{ $stats['total_users'] }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-purple-subtle text-purple rounded">
                                <i class="ti ti-users fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6 mb-3">
            <div class="card stats-card h-100" style="border-left-color: #28a745;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2 fw-normal">Active Users</h6>
                            <h3 class="fw-bold text-success mb-0">{{ $stats['active_users'] }}</h3>
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
    </div>

    <div class="row">
        <!-- Department Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-info-circle me-2"></i>Department Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <span class="fw-semibold text-muted">Name:</span>
                            </div>
                            <div class="col-sm-8">
                                <span class="text-dark">{{ $department->name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="row align-items-start">
                            <div class="col-sm-4">
                                <span class="fw-semibold text-muted">Description:</span>
                            </div>
                            <div class="col-sm-8">
                                <span class="text-dark">
                                    {{ $department->description ?: 'No description provided.' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($department->location)
                    <div class="info-item">
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <span class="fw-semibold text-muted">Location:</span>
                            </div>
                            <div class="col-sm-8">
                                <span class="text-dark">
                                    <i class="ti ti-map-pin me-2 text-muted"></i>{{ $department->location }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($department->phone)
                    <div class="info-item">
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <span class="fw-semibold text-muted">Phone:</span>
                            </div>
                            <div class="col-sm-8">
                                <span class="text-dark">
                                    <i class="ti ti-phone me-2 text-muted"></i>{{ $department->phone }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($department->email)
                    <div class="info-item">
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <span class="fw-semibold text-muted">Email:</span>
                            </div>
                            <div class="col-sm-8">
                                <span class="text-dark">
                                    <i class="ti ti-mail me-2 text-muted"></i>{{ $department->email }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <span class="fw-semibold text-muted">Status:</span>
                            </div>
                            <div class="col-sm-8">
                                <span class="badge {{ $department->status_badge_class }}">
                                    {{ $department->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <span class="fw-semibold text-muted">Created:</span>
                            </div>
                            <div class="col-sm-8">
                                <span class="text-dark">{{ $department->formatted_created_at }}</span>
                                <small class="text-muted ms-2">({{ $department->created_at->diffForHumans() }})</small>
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <span class="fw-semibold text-muted">Last Updated:</span>
                            </div>
                            <div class="col-sm-8">
                                <span class="text-dark">{{ $department->formatted_updated_at }}</span>
                                <small class="text-muted ms-2">({{ $department->updated_at->diffForHumans() }})</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Department Users -->
            @if($department->users->count() > 0)
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-users me-2"></i>Department Users
                        </h5>
                        <span class="badge bg-info-subtle text-info">{{ $department->users->count() }} users</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">User</th>
                                    <th class="fw-semibold">Email</th>
                                    <th class="fw-semibold">Role</th>
                                    <th class="fw-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                                                <small class="text-muted">ID: {{ $user->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ $user->email }}</span>
                                    </td>
                                    <td>
                                        @if($user->roles->count() > 0)
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-primary-subtle text-primary me-1">{{ $role->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No roles</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $user->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('update', $department)
                        <a href="{{ route('Admin.departments.edit', $department) }}" class="btn btn-purple">
                            <i class="ti ti-edit me-2"></i>Edit Department
                        </a>
                        @endcan

                        <a href="{{ route('Admin.departments.assignments') }}" class="btn btn-outline-purple">
                            <i class="ti ti-users-group me-2"></i>Manage User Assignments
                        </a>

                        <a href="{{ route('Admin.departments.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-2"></i>Back to Departments
                        </a>

                        @can('delete', $department)
                        @if($department->canBeDeleted())
                        <form action="{{ route('Admin.departments.destroy', $department) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-outline-danger w-100 delete-btn" 
                                    data-name="{{ $department->name }}">
                                <i class="ti ti-trash me-2"></i>Delete Department
                            </button>
                        </form>
                        @else
                        <button class="btn btn-outline-danger w-100" disabled title="Cannot delete department with users">
                            <i class="ti ti-trash me-2"></i>Delete Department
                        </button>
                        <small class="text-muted text-center">Remove all users first to delete this department.</small>
                        @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
});
</script>
@endpush

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
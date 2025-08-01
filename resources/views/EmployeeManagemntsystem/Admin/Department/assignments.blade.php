@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'Department Assignments')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
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

.user-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(45deg, #3E007C, #7100E2);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
}

.department-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.department-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.assignment-select {
    min-width: 200px;
}

.assignment-modal .modal-dialog {
    max-width: 600px;
}

.user-row {
    transition: background-color 0.2s ease;
}

.user-row:hover {
    background-color: #f8f9fa;
}

.assignment-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Assignment change handler
    document.querySelectorAll('.assignment-select').forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            const departmentId = this.value;
            const departmentName = this.options[this.selectedIndex].text;
            
            // Store original value for rollback
            const originalValue = this.dataset.originalValue;
            
            Swal.fire({
                title: 'Confirm Assignment',
                text: `Assign ${userName} to ${departmentName}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3E007C',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, assign!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Make AJAX request
                    fetch('{{ route('Admin.departments.assign-user') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            department_id: departmentId || null
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update original value
                            this.dataset.originalValue = departmentId;
                            
                            // Update badge
                            const badge = document.querySelector(`#badge-${userId}`);
                            if (departmentId) {
                                badge.className = 'badge bg-success-subtle text-success assignment-badge';
                                badge.textContent = departmentName;
                            } else {
                                badge.className = 'badge bg-secondary-subtle text-secondary assignment-badge';
                                badge.textContent = 'Unassigned';
                            }
                            
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            // Update department cards counts
                            updateDepartmentCounts();
                        } else {
                            // Rollback select value
                            this.value = originalValue;
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        // Rollback select value
                        this.value = originalValue;
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while updating assignment.'
                        });
                    });
                } else {
                    // Rollback select value
                    this.value = originalValue;
                }
            });
        });
    });
    
    // Bulk assignment modal
    document.getElementById('bulkAssignBtn').addEventListener('click', function() {
        const checkedUsers = document.querySelectorAll('.user-checkbox:checked');
        if (checkedUsers.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Users Selected',
                text: 'Please select at least one user for bulk assignment.'
            });
            return;
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('bulkAssignModal'));
        modal.show();
    });
    
    // Bulk assignment form submission
    document.getElementById('bulkAssignForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const checkedUsers = document.querySelectorAll('.user-checkbox:checked');
        const departmentId = document.getElementById('bulkDepartmentSelect').value;
        const departmentName = document.getElementById('bulkDepartmentSelect').options[document.getElementById('bulkDepartmentSelect').selectedIndex].text;
        
        const userIds = Array.from(checkedUsers).map(cb => cb.value);
        
        // Close modal first
        const modal = bootstrap.Modal.getInstance(document.getElementById('bulkAssignModal'));
        modal.hide();
        
        // Show loading
        Swal.fire({
            title: 'Processing...',
            text: 'Updating user assignments',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Process assignments one by one
        let processed = 0;
        const total = userIds.length;
        
        userIds.forEach((userId, index) => {
            fetch('{{ route('Admin.departments.assign-user') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_id: userId,
                    department_id: departmentId || null
                })
            })
            .then(response => response.json())
            .then(data => {
                processed++;
                
                if (data.success) {
                    // Update UI elements for this user
                    const select = document.querySelector(`[data-user-id="${userId}"]`);
                    const badge = document.querySelector(`#badge-${userId}`);
                    const checkbox = document.querySelector(`[value="${userId}"]`);
                    
                    if (select) {
                        select.value = departmentId || '';
                        select.dataset.originalValue = departmentId || '';
                    }
                    
                    if (badge) {
                        if (departmentId) {
                            badge.className = 'badge bg-success-subtle text-success assignment-badge';
                            badge.textContent = departmentName;
                        } else {
                            badge.className = 'badge bg-secondary-subtle text-secondary assignment-badge';
                            badge.textContent = 'Unassigned';
                        }
                    }
                    
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                }
                
                // Check if all are processed
                if (processed === total) {
                    updateDepartmentCounts();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Bulk Assignment Complete!',
                        text: `Successfully updated assignments for ${total} users.`,
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                processed++;
                
                if (processed === total) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Bulk Assignment Complete!',
                        text: 'Some assignments may have failed. Please review the results.'
                    });
                }
            });
        });
    });
    
    // Select all functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
    
    // Update department counts
    function updateDepartmentCounts() {
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
    
    // Search functionality
    document.getElementById('userSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.user-row');
        
        rows.forEach(row => {
            const userName = row.querySelector('.user-name').textContent.toLowerCase();
            const userEmail = row.querySelector('.user-email').textContent.toLowerCase();
            
            if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Department filter
    document.getElementById('departmentFilter').addEventListener('change', function() {
        const selectedDepartment = this.value;
        const rows = document.querySelectorAll('.user-row');
        
        rows.forEach(row => {
            const select = row.querySelector('.assignment-select');
            const currentDepartment = select.value;
            
            if (selectedDepartment === '' || currentDepartment === selectedDepartment) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
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
                    <h4 class="fw-bold text-purple mb-1">Department Assignments</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            @php
                                $role = auth()->user()->getRoleNames()->first();
                                $dashboardRoute = $role && Route::has($role . '.dashboard') ? route($role . '.dashboard') : route('Admin.dashboard');
                            @endphp
                            <li class="breadcrumb-item"><a href="{{ $dashboardRoute }}" class="text-purple">Dashboard</a></li>
                            @if(!auth()->user()->hasRole('HR'))
                            <li class="breadcrumb-item"><a href="{{ route('Admin.departments.index') }}" class="text-purple">Departments</a></li>
                            @endif
                            <li class="breadcrumb-item active" aria-current="page">{{ auth()->user()->hasRole('HR') ? 'Department Assignments' : 'Assignments' }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @if(!auth()->user()->hasRole('HR'))
                    <a href="{{ route('Admin.departments.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i>Back to Departments
                    </a>
                    @else
                    @php
                        $role = auth()->user()->getRoleNames()->first();
                        $dashboardRoute = $role && Route::has($role . '.dashboard') ? route($role . '.dashboard') : route('Admin.dashboard');
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Department Overview Cards -->
    <div class="row mb-4">
        @foreach($departments as $department)
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card department-card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="fw-semibold text-dark mb-1">{{ $department->name }}</h6>
                            <p class="text-muted mb-2 fs-14">
                                {{ $department->users_count }} {{ Str::plural('user', $department->users_count) }}
                            </p>
                            <span class="badge {{ $department->status_badge_class }}">
                                {{ $department->status_label }}
                            </span>
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
        @endforeach
        
        <!-- Unassigned Users Card -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card department-card h-100" style="border-left: 4px solid #6c757d;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="fw-semibold text-dark mb-1">Unassigned</h6>
                            <p class="text-muted mb-2 fs-14">
                                {{ $users->where('department_id', null)->count() }} {{ Str::plural('user', $users->where('department_id', null)->count()) }}
                            </p>
                            <span class="badge bg-secondary">
                                No Department
                            </span>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-secondary-subtle text-secondary rounded">
                                <i class="ti ti-users-off fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Assignment Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-users-group me-2"></i>User Department Assignments
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-purple btn-sm" id="bulkAssignBtn">
                                <i class="ti ti-users-plus me-2"></i>Bulk Assign
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" id="userSearch" placeholder="Search users...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="departmentFilter">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                                <option value="">Unassigned</option>
                            </select>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th class="fw-semibold">User</th>
                                    <th class="fw-semibold">Current Assignment</th>
                                    <th class="fw-semibold">Assign to Department</th>
                                    <th class="fw-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr class="user-row">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold user-name">{{ $user->name }}</h6>
                                                <small class="text-muted user-email">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span id="badge-{{ $user->id }}" class="badge assignment-badge {{ $user->department ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                            {{ $user->department ? $user->department->name : 'Unassigned' }}
                                        </span>
                                    </td>
                                    <td>
                                        <select class="form-select assignment-select" 
                                                data-user-id="{{ $user->id }}" 
                                                data-user-name="{{ $user->name }}"
                                                data-original-value="{{ $user->department_id }}">
                                            <option value="">Select Department</option>
                                            @foreach($departments->where('status', true) as $department)
                                            <option value="{{ $department->id }}" 
                                                    {{ $user->department_id == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                            @endforeach
                                        </select>
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

                    @if($users->count() === 0)
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="ti ti-users-off text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted">No users found</h5>
                        <p class="text-muted mb-0">Create users first to assign them to departments.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Assignment Modal -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1" aria-labelledby="bulkAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog assignment-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkAssignModalLabel">
                    <i class="ti ti-users-plus me-2"></i>Bulk Department Assignment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkAssignForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulkDepartmentSelect" class="form-label fw-semibold">Select Department</label>
                        <select class="form-select" id="bulkDepartmentSelect" required>
                            <option value="">Remove from all departments</option>
                            @foreach($departments->where('status', true) as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Selected users will be assigned to the chosen department.</div>
                    </div>
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Note:</strong> This will update department assignments for all selected users.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple">
                        <i class="ti ti-check me-2"></i>Assign Users
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
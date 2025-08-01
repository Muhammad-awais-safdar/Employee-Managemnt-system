@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'Attendance Management')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h3 class="page-title">Attendance Management</h3>
            </div>
            <div class="col-md-8 text-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#markAttendanceModal">
                        <i class="fa fa-plus me-2"></i>Mark Attendance
                    </button>
                    <a href="{{ route(auth()->user()->getRoleNames()->first() . '.attendance.reports') }}" class="btn btn-info">
                        <i class="fa fa-chart-bar me-2"></i>Reports
                    </a>
                    <button type="button" class="btn btn-success" onclick="exportAttendance()">
                        <i class="fa fa-download me-2"></i>Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Summary Cards -->
    <div class="row">
        <div class="col-md-2">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $companySummary['total_employees'] }}</h4>
                            <p class="text-muted mb-0">Total Employees</p>
                        </div>
                        <div class="stat-icon bg-primary">
                            <i class="fa fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $companySummary['present_today'] }}</h4>
                            <p class="text-muted mb-0">Present Today</p>
                        </div>
                        <div class="stat-icon bg-success">
                            <i class="fa fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $companySummary['absent_today'] }}</h4>
                            <p class="text-muted mb-0">Absent Today</p>
                        </div>
                        <div class="stat-icon bg-danger">
                            <i class="fa fa-times"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $companySummary['late_today'] }}</h4>
                            <p class="text-muted mb-0">Late Today</p>
                        </div>
                        <div class="stat-icon bg-warning">
                            <i class="fa fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $companySummary['half_day_today'] }}</h4>
                            <p class="text-muted mb-0">Half Day</p>
                        </div>
                        <div class="stat-icon bg-info">
                            <i class="fa fa-adjust"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $companySummary['attendance_rate'] }}%</h4>
                            <p class="text-muted mb-0">Attendance Rate</p>
                        </div>
                        <div class="stat-icon bg-purple">
                            <i class="fa fa-percentage"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Filter Attendance Records</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route(auth()->user()->getRoleNames()->first() . '.attendance.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="">All Status</option>
                                        <option value="present" {{ $status == 'present' ? 'selected' : '' }}>Present</option>
                                        <option value="absent" {{ $status == 'absent' ? 'selected' : '' }}>Absent</option>
                                        <option value="late" {{ $status == 'late' ? 'selected' : '' }}>Late</option>
                                        <option value="half_day" {{ $status == 'half_day' ? 'selected' : '' }}>Half Day</option>
                                        <option value="early_leave" {{ $status == 'early_leave' ? 'selected' : '' }}>Early Leave</option>
                                        <option value="unpaid_leave" {{ $status == 'unpaid_leave' ? 'selected' : '' }}>Unpaid Leave</option>
                                        <option value="without_notice" {{ $status == 'without_notice' ? 'selected' : '' }}>Without Notice</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Department</label>
                                    <select class="form-control" name="department_id">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if(auth()->user()->hasRole('superAdmin') && $companies)
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Company</label>
                                    <select class="form-control" name="company_id">
                                        <option value="">All Companies</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ $companyId == $company->id ? 'selected' : '' }}>
                                                {{ $company->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search me-2"></i>Filter
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                                    <i class="fa fa-times me-2"></i>Clear
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Attendance Records</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Employee</th>
                                    <th class="d-none d-md-table-cell">Department</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th class="d-none d-lg-table-cell">Working Hours</th>
                                    <th class="d-none d-lg-table-cell">Break Time</th>
                                    <th>Status</th>
                                    <th class="d-none d-xl-table-cell">Overtime</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('M d, Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-title bg-primary rounded-circle">
                                                    {{ substr($attendance->user->name, 0, 2) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $attendance->user->name }}</h6>
                                                <small class="text-muted">{{ $attendance->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        {{ $attendance->user->department->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="time-badge {{ $attendance->check_in_time ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $attendance->check_in_time ?? '--:--' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="time-badge {{ $attendance->check_out_time ? 'bg-danger' : 'bg-secondary' }}">
                                            {{ $attendance->check_out_time ?? '--:--' }}
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ $attendance->formatted_total_hours }}</td>
                                    <td class="d-none d-lg-table-cell">{{ $attendance->formatted_break_duration }}</td>
                                    <td>
                                        <span class="badge {{ $attendance->status_badge_class }}">
                                            {{ $attendance->status_label }}
                                        </span>
                                    </td>
                                    <td class="d-none d-xl-table-cell">
                                        @if($attendance->overtime_hours > 0)
                                            <span class="text-success font-weight-bold">+{{ $attendance->overtime_hours }}h</span>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="editAttendance({{ $attendance->id }})">
                                                        <i class="fa fa-edit me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="viewDetails({{ $attendance->id }})">
                                                        <i class="fa fa-eye me-2"></i>View Details
                                                    </a>
                                                </li>
                                                @if(auth()->user()->hasRole('Admin'))
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteAttendance({{ $attendance->id }})">
                                                        <i class="fa fa-trash me-2"></i>Delete
                                                    </a>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fa fa-calendar-times fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No attendance records found</h5>
                                            <p class="text-muted">Try adjusting your filters or date range</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($attendances->hasPages())
                    <div class="row">
                        <div class="col-md-6">
                            <div class="dataTables_info">
                                Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} of {{ $attendances->total() }} entries
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dataTables_paginate">
                                {{ $attendances->appends(request()->input())->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mark Attendance Modal -->
<div class="modal fade" id="markAttendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="markAttendanceForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label>Employee *</label>
                                <select class="form-control" name="user_id" required>
                                    <option value="">Select Employee</option>
                                    <!-- This will be populated via AJAX based on role -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Date *</label>
                                <input type="date" class="form-control" name="date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Status *</label>
                                <select class="form-control" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                    <option value="half_day">Half Day</option>
                                    <option value="early_leave">Early Leave</option>
                                    <option value="unpaid_leave">Unpaid Leave</option>
                                    <option value="without_notice">Without Notice</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Check In Time</label>
                                <input type="time" class="form-control" name="check_in_time">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Check Out Time</label>
                                <input type="time" class="form-control" name="check_out_time">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label>Notes</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="Additional notes or comments"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Mark Attendance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.stat-card {
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    margin-bottom: 20px;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.bg-purple {
    background-color: #6f42c1 !important;
}

.time-badge {
    padding: 4px 8px;
    border-radius: 4px;
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
}

.avatar {
    width: 40px;
    height: 40px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
}

.empty-state {
    padding: 40px 20px;
}

@media (max-width: 768px) {
    .stat-card .card-body {
        padding: 15px;
    }
    
    .stat-card h4 {
        font-size: 1.5rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }
}
</style>

<script>
// Set default date to today
document.querySelector('input[name="date"]').value = new Date().toISOString().split('T')[0];

// Load employees when modal opens
document.getElementById('markAttendanceModal').addEventListener('show.bs.modal', function() {
    loadEmployees();
});

function loadEmployees() {
    const select = document.querySelector('select[name="user_id"]');
    select.innerHTML = '<option value="">Loading employees...</option>';
    
    // Fetch employees from server
    fetch("{{ route(auth()->user()->getRoleNames()->first() . '.attendance.employees') }}", {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.employees) {
            let options = '<option value="">Select Employee</option>';
            data.employees.forEach(employee => {
                options += `<option value="${employee.id}">${employee.name} (${employee.department}) - ${employee.roles.join(', ')}</option>`;
            });
            select.innerHTML = options;
        } else {
            select.innerHTML = '<option value="">No employees found</option>';
            toastr.warning('No employees available for attendance marking', 'No Employees');
        }
    })
    .catch(error => {
        console.error('Error loading employees:', error);
        select.innerHTML = '<option value="">Error loading employees</option>';
        toastr.error('Failed to load employees list', 'Loading Error');
    });
}

function clearFilters() {
    const form = document.getElementById('filterForm');
    form.reset();
    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    form.querySelector('input[name="date_from"]').value = today;
    form.querySelector('input[name="date_to"]').value = today;
    form.submit();
}

// Mark attendance form submission
document.getElementById('markAttendanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    fetch("{{ route(auth()->user()->getRoleNames()->first() . '.attendance.mark') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message, 'Attendance Marked');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            toastr.error(data.message, 'Error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('An error occurred while marking attendance', 'Network Error');
    });
});

function editAttendance(id) {
    // Implementation for editing attendance
    toastr.info('Edit attendance functionality will be implemented here', 'Coming Soon');
}

function viewDetails(id) {
    // Implementation for viewing attendance details
    toastr.info('Detailed view functionality will be implemented here', 'Coming Soon');
}

function deleteAttendance(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implementation for deleting attendance
            Swal.fire(
                'Deleted!',
                'Attendance record has been deleted.',
                'success'
            );
        }
    });
}

function exportAttendance() {
    const params = new URLSearchParams(window.location.search);
    const exportUrl = "{{ route(auth()->user()->getRoleNames()->first() . '.attendance.export') }}?" + params.toString();
    
    toastr.info('Your attendance report is being prepared...', 'Exporting Data');
    
    // In real implementation, this would trigger a download
    console.log('Export URL:', exportUrl);
}

// Auto-submit form when filters change
document.querySelectorAll('#filterForm input, #filterForm select').forEach(element => {
    element.addEventListener('change', function() {
        // Optional: Auto-submit form on change
        // document.getElementById('filterForm').submit();
    });
});
</script>

<!-- Include Attendance JavaScript -->
<script src="{{ asset('js/attendance.js') }}"></script>
@endsection
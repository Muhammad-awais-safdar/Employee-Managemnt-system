@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'Team Leave Calendar')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Team Leave Calendar</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('Admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Leave Calendar</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Filters & Controls</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Company Filter (SuperAdmin only) -->
                    @if($companies)
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Company</label>
                            <select class="form-control" id="companyFilter">
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ $company->id == $companyId ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    <!-- Department Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Department</label>
                            <select class="form-control" id="departmentFilter">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Leave Type Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Leave Type</label>
                            <select class="form-control" id="leaveTypeFilter">
                                <option value="">All Leave Types</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Employee Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Employee</label>
                            <select class="form-control" id="employeeFilter">
                                <option value="">All Employees</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary" onclick="applyFilters()">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fa fa-times"></i> Clear Filters
                        </button>
                        <button type="button" class="btn btn-info" onclick="showTeamAvailability()">
                            <i class="fa fa-users"></i> Team Availability
                        </button>
                        <button type="button" class="btn btn-warning" onclick="showConflicts()">
                            <i class="fa fa-exclamation-triangle"></i> View Conflicts
                        </button>
                        <button type="button" class="btn btn-success" onclick="showDepartmentStats()">
                            <i class="fa fa-chart-bar"></i> Department Stats
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Leave Calendar</h4>
                <div class="card-options">
                    <div class="legend">
                        <span class="legend-item"><span class="color-box" style="background-color: #28a745;"></span> Annual Leave</span>
                        <span class="legend-item"><span class="color-box" style="background-color: #dc3545;"></span> Sick Leave</span>
                        <span class="legend-item"><span class="color-box" style="background-color: #007bff;"></span> Casual Leave</span>
                        <span class="legend-item"><span class="color-box" style="background-color: #6f42c1;"></span> Personal Leave</span>
                        <span class="legend-item"><span class="color-box" style="background-color: #fd7e14;"></span> Maternity/Paternity</span>
                        <span class="legend-item"><span class="color-box" style="background-color: #17a2b8;"></span> Other</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Leave Detail Modal -->
<div class="modal fade" id="leaveDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Leave Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="leaveDetailContent">
                <!-- Leave details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Team Availability Modal -->
<div class="modal fade" id="teamAvailabilityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Team Availability Overview</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="date" class="form-control" id="availabilityDate" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" id="availabilityDepartment">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary" onclick="loadTeamAvailability()">
                            <i class="fa fa-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
                <div id="availabilityContent">
                    <!-- Availability data will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Conflicts Modal -->
<div class="modal fade" id="conflictsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Leave Conflicts & High Impact Dates</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="conflictStartDate" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="conflictEndDate" value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="conflictDepartment">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary" onclick="loadConflicts()">
                            <i class="fa fa-search"></i> Check Conflicts
                        </button>
                    </div>
                </div>
                <div id="conflictsContent">
                    <!-- Conflicts data will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Department Stats Modal -->
<div class="modal fade" id="departmentStatsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Department-wise Leave Statistics</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="date" class="form-control" id="statsStartDate" value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-md-4">
                        <input type="date" class="form-control" id="statsEndDate" value="{{ date('Y-m-t') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary" onclick="loadDepartmentStats()">
                            <i class="fa fa-chart-bar"></i> Generate Stats
                        </button>
                    </div>
                </div>
                <div id="departmentStatsContent">
                    <!-- Department stats will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
.legend {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
}
.color-box {
    width: 12px;
    height: 12px;
    border-radius: 2px;
    display: inline-block;
}
.fc-event {
    cursor: pointer;
}
.availability-card {
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
}
.status-available { border-left: 4px solid #28a745; }
.status-on-leave { border-left: 4px solid #dc3545; }
.status-half-day { border-left: 4px solid #ffc107; }
.conflict-card {
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 15px;
}
.severity-critical { border-left: 4px solid #dc3545; }
.severity-high { border-left: 4px solid #fd7e14; }
.department-stat-card {
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 15px;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
let calendar;
let currentCompanyId = {{ $companyId }};

document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
    loadEmployees();
    
    // Event listeners for filters
    $('#departmentFilter').change(function() {
        loadEmployees();
    });
    
    @if($companies)
    $('#companyFilter').change(function() {
        currentCompanyId = $(this).val();
        loadDepartments();
        loadLeaveTypes();
        loadEmployees();
        applyFilters();
    });
    @endif
});

function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        height: 'auto',
        events: function(info, successCallback, failureCallback) {
            loadCalendarEvents(info.startStr, info.endStr, successCallback, failureCallback);
        },
        eventClick: function(info) {
            showLeaveDetails(info.event);
        },
        eventDidMount: function(info) {
            // Add tooltip
            info.el.setAttribute('title', 
                info.event.extendedProps.employee_name + ' - ' + 
                info.event.extendedProps.leave_type + ' (' + 
                info.event.extendedProps.duration + ')'
            );
        }
    });
    calendar.render();
}

function loadCalendarEvents(start, end, successCallback, failureCallback) {
    const filters = getFilters();
    
    $.ajax({
        url: '{{ route("calendar.events") }}',
        method: 'GET',
        data: {
            start: start,
            end: end,
            company_id: currentCompanyId,
            ...filters
        },
        success: function(events) {
            successCallback(events);
        },
        error: function(xhr) {
            console.error('Failed to load calendar events:', xhr);
            failureCallback(xhr);
        }
    });
}

function getFilters() {
    return {
        department_id: $('#departmentFilter').val(),
        leave_type_id: $('#leaveTypeFilter').val(),
        employee_id: $('#employeeFilter').val()
    };
}

function applyFilters() {
    calendar.refetchEvents();
}

function clearFilters() {
    $('#departmentFilter').val('');
    $('#leaveTypeFilter').val('');
    $('#employeeFilter').val('');
    loadEmployees();
    calendar.refetchEvents();
}

function loadEmployees() {
    const departmentId = $('#departmentFilter').val();
    
    $.ajax({
        url: '{{ route("calendar.employees") }}',
        method: 'GET',
        data: {
            company_id: currentCompanyId,
            department_id: departmentId
        },
        success: function(response) {
            const select = $('#employeeFilter');
            select.empty().append('<option value="">All Employees</option>');
            
            response.employees.forEach(function(employee) {
                select.append(`<option value="${employee.id}">${employee.name} (${employee.department})</option>`);
            });
        },
        error: function(xhr) {
            console.error('Failed to load employees:', xhr);
        }
    });
}

function showLeaveDetails(event) {
    const props = event.extendedProps;
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6>Employee Information</h6>
                <p><strong>Name:</strong> ${props.employee_name}</p>
                <p><strong>Department:</strong> ${props.department}</p>
            </div>
            <div class="col-md-6">
                <h6>Leave Information</h6>
                <p><strong>Application ID:</strong> ${props.application_id}</p>
                <p><strong>Leave Type:</strong> ${props.leave_type}</p>
                <p><strong>Duration:</strong> ${props.duration}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Start Date:</strong> ${new Date(props.start_date).toLocaleDateString()}</p>
                <p><strong>End Date:</strong> ${new Date(props.end_date).toLocaleDateString()}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Total Days:</strong> ${props.total_days}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h6>Reason</h6>
                <p>${props.reason || 'No reason provided'}</p>
            </div>
        </div>
    `;
    
    $('#leaveDetailContent').html(html);
    $('#leaveDetailModal').modal('show');
}

function showTeamAvailability() {
    $('#teamAvailabilityModal').modal('show');
    loadTeamAvailability();
}

function loadTeamAvailability() {
    const date = $('#availabilityDate').val();
    const departmentId = $('#availabilityDepartment').val();
    
    $.ajax({
        url: '{{ route("calendar.availability") }}',
        method: 'GET',
        data: {
            company_id: currentCompanyId,
            date: date,
            department_id: departmentId
        },
        success: function(response) {
            let html = `
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>Summary for ${new Date(response.date).toLocaleDateString()}:</strong>
                            Total: ${response.summary.total} | 
                            Available: ${response.summary.available} | 
                            On Leave: ${response.summary.on_leave} | 
                            Half Day: ${response.summary.half_day}
                        </div>
                    </div>
                </div>
            `;
            
            // Group by department
            Object.keys(response.department_wise).forEach(function(dept) {
                html += `<div class="row"><div class="col-md-12"><h6>${dept}</h6></div></div>`;
                html += '<div class="row">';
                
                response.department_wise[dept].forEach(function(employee) {
                    const statusClass = `status-${employee.status.replace('_', '-')}`;
                    const statusText = employee.status === 'available' ? 'Available' : 
                                     employee.status === 'on_leave' ? 'On Leave' : 'Half Day Leave';
                    
                    html += `
                        <div class="col-md-4 mb-2">
                            <div class="availability-card ${statusClass}">
                                <strong>${employee.name}</strong><br>
                                <small class="text-muted">${statusText}</small>
                                ${employee.leave_info ? `<br><small>${employee.leave_info.type}</small>` : ''}
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
            });
            
            $('#availabilityContent').html(html);
        },
        error: function(xhr) {
            $('#availabilityContent').html('<div class="alert alert-danger">Failed to load availability data</div>');
        }
    });
}

function showConflicts() {
    $('#conflictsModal').modal('show');
    loadConflicts();
}

function loadConflicts() {
    const startDate = $('#conflictStartDate').val();
    const endDate = $('#conflictEndDate').val();
    const departmentId = $('#conflictDepartment').val();
    
    $.ajax({
        url: '{{ route("calendar.conflicts") }}',
        method: 'GET',
        data: {
            company_id: currentCompanyId,
            start_date: startDate,
            end_date: endDate,
            department_id: departmentId
        },
        success: function(response) {
            let html = `
                <div class="alert alert-info">
                    <strong>Conflict Analysis (${new Date(response.period.start).toLocaleDateString()} - ${new Date(response.period.end).toLocaleDateString()}):</strong>
                    Total Conflicts: ${response.summary.total_conflicts} | 
                    Critical: ${response.summary.critical} | 
                    High: ${response.summary.high}
                </div>
            `;
            
            if (response.conflicts.length === 0) {
                html += '<div class="alert alert-success">No significant conflicts detected in this period.</div>';
            } else {
                response.conflicts.forEach(function(conflict) {
                    const severityClass = `severity-${conflict.severity}`;
                    html += `
                        <div class="conflict-card ${severityClass}">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6>${conflict.department} - ${new Date(conflict.date).toLocaleDateString()}</h6>
                                    <p><strong>Impact:</strong> ${conflict.employees_on_leave}/${conflict.total_employees} employees on leave (${conflict.coverage_percentage}%)</p>
                                    <p><strong>Severity:</strong> <span class="badge badge-${conflict.severity === 'critical' ? 'danger' : 'warning'}">${conflict.severity.toUpperCase()}</span></p>
                                </div>
                                <div class="col-md-4">
                                    <strong>Employees on Leave:</strong>
                                    <ul class="list-unstyled">
                    `;
                    
                    conflict.employees.forEach(function(emp) {
                        html += `<li><small>${emp.name} (${emp.leave_type})</small></li>`;
                    });
                    
                    html += `
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            
            $('#conflictsContent').html(html);
        },
        error: function(xhr) {
            $('#conflictsContent').html('<div class="alert alert-danger">Failed to load conflicts data</div>');
        }
    });
}

function showDepartmentStats() {
    $('#departmentStatsModal').modal('show');
    loadDepartmentStats();
}

function loadDepartmentStats() {
    const startDate = $('#statsStartDate').val();
    const endDate = $('#statsEndDate').val();
    
    $.ajax({
        url: '{{ route("calendar.department-stats") }}',
        method: 'GET',
        data: {
            company_id: currentCompanyId,
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            let html = `
                <div class="alert alert-info">
                    <strong>Department Statistics (${new Date(response.period.start).toLocaleDateString()} - ${new Date(response.period.end).toLocaleDateString()})</strong>
                </div>
            `;
            
            response.departments.forEach(function(dept) {
                html += `
                    <div class="department-stat-card">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>${dept.department_name}</h6>
                                <p><strong>Total Employees:</strong> ${dept.total_employees}</p>
                                <p><strong>Total Leaves:</strong> ${dept.total_leaves}</p>
                                <p><strong>Total Leave Days:</strong> ${dept.total_leave_days}</p>
                                <p><strong>Avg Days/Employee:</strong> ${dept.average_days_per_employee}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Leave Type Breakdown</h6>
                `;
                
                Object.keys(dept.leave_type_breakdown).forEach(function(type) {
                    const breakdown = dept.leave_type_breakdown[type];
                    html += `<p><small>${type}: ${breakdown.count} applications (${breakdown.days} days)</small></p>`;
                });
                
                html += `
                            </div>
                        </div>
                    </div>
                `;
            });
            
            $('#departmentStatsContent').html(html);
        },
        error: function(xhr) {
            $('#departmentStatsContent').html('<div class="alert alert-danger">Failed to load department statistics</div>');
        }
    });
}

@if($companies)
function loadDepartments() {
    // Reload departments when company changes
    $.ajax({
        url: '{{ route("Admin.departments.index") }}',
        method: 'GET',
        data: { company_id: currentCompanyId },
        success: function(response) {
            // Update department filter options
        }
    });
}

function loadLeaveTypes() {
    // Reload leave types when company changes
    $.ajax({
        url: '{{ route("Admin.leave-types.index") }}',
        method: 'GET', 
        data: { company_id: currentCompanyId },
        success: function(response) {
            // Update leave type filter options
        }
    });
}
@endif
</script>
@endsection
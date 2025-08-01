@extends('EmployeeManagemntsystem.Layout.employee')

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
                        <li class="breadcrumb-item"><a href="{{ route('Employee.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Leave Calendar</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Filters (simplified for employees) -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Team Leave View</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                <i class="fa fa-filter"></i> Apply Filters
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                                <i class="fa fa-times"></i> Clear
                            </button>
                        </div>
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
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
let calendar;
let currentCompanyId = {{ $companyId }};

document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
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
        leave_type_id: $('#leaveTypeFilter').val()
    };
}

function applyFilters() {
    calendar.refetchEvents();
}

function clearFilters() {
    $('#departmentFilter').val('');
    $('#leaveTypeFilter').val('');
    calendar.refetchEvents();
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
    `;
    
    $('#leaveDetailContent').html(html);
    $('#leaveDetailModal').modal('show');
}
</script>
@endsection
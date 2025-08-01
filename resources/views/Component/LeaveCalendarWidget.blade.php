{{-- Team Leave Calendar Widget --}}
<div class="card">
    <div class="card-header">
        <h4 class="card-title">
            <i class="fa fa-calendar-alt"></i> Team Leave Calendar
        </h4>
        <div class="card-options">
            <a href="{{ route('calendar.leave') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-expand"></i> Full Calendar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div id="widget-calendar" style="height: 400px;"></div>
        
        {{-- Quick stats --}}
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="text-center">
                    <h6 class="text-muted">Today</h6>
                    <h4 class="text-primary" id="todayLeaveCount">-</h4>
                    <small>On Leave</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <h6 class="text-muted">This Week</h6>
                    <h4 class="text-warning" id="weekLeaveCount">-</h4>
                    <small>Total Leaves</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <h6 class="text-muted">This Month</h6>
                    <h4 class="text-success" id="monthLeaveCount">-</h4>
                    <small>Total Leaves</small>
                </div>
            </div>
        </div>
        
        {{-- Upcoming leaves --}}
        <div class="mt-3">
            <h6>Upcoming Leaves (Next 7 Days)</h6>
            <div id="upcomingLeaves">
                <div class="text-center text-muted">
                    <i class="fa fa-spinner fa-spin"></i> Loading...
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
.leave-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    margin-bottom: 5px;
    border-radius: 4px;
    background-color: #f8f9fa;
    border-left: 3px solid #007bff;
}
.leave-item small {
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeWidgetCalendar();
    loadQuickStats();
    loadUpcomingLeaves();
});

function initializeWidgetCalendar() {
    const calendarEl = document.getElementById('widget-calendar');
    const widgetCalendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'today'
        },
        height: 350,
        events: function(info, successCallback, failureCallback) {
            $.ajax({
                url: '{{ route("calendar.events") }}',
                method: 'GET',
                data: {
                    start: info.startStr,
                    end: info.endStr
                },
                success: function(events) {
                    // Limit to 50 events for widget performance
                    successCallback(events.slice(0, 50));
                },
                error: function(xhr) {
                    console.error('Failed to load widget calendar events:', xhr);
                    failureCallback(xhr);
                }
            });
        },
        eventClick: function(info) {
            // Show simple tooltip or navigate to full calendar
            window.location.href = '{{ route("calendar.leave") }}';
        },
        eventDidMount: function(info) {
            // Add tooltip
            info.el.setAttribute('title', 
                info.event.extendedProps.employee_name + ' - ' + 
                info.event.extendedProps.leave_type
            );
        }
    });
    widgetCalendar.render();
}

function loadQuickStats() {
    const today = new Date();
    const startOfWeek = new Date(today.getFullYear(), today.getMonth(), today.getDate() - today.getDay());
    const endOfWeek = new Date(today.getFullYear(), today.getMonth(), today.getDate() - today.getDay() + 6);
    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    // Load today's leaves
    $.ajax({
        url: '{{ route("calendar.events") }}',
        method: 'GET',
        data: {
            start: today.toISOString().split('T')[0],
            end: today.toISOString().split('T')[0]
        },
        success: function(events) {
            $('#todayLeaveCount').text(events.length);
        }
    });

    // Load week's leaves
    $.ajax({
        url: '{{ route("calendar.events") }}',
        method: 'GET',
        data: {
            start: startOfWeek.toISOString().split('T')[0],
            end: endOfWeek.toISOString().split('T')[0]
        },
        success: function(events) {
            $('#weekLeaveCount').text(events.length);
        }
    });

    // Load month's leaves
    $.ajax({
        url: '{{ route("calendar.events") }}',
        method: 'GET',
        data: {
            start: startOfMonth.toISOString().split('T')[0],
            end: endOfMonth.toISOString().split('T')[0]
        },
        success: function(events) {
            $('#monthLeaveCount').text(events.length);
        }
    });
}

function loadUpcomingLeaves() {
    const today = new Date();
    const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);

    $.ajax({
        url: '{{ route("calendar.events") }}',
        method: 'GET',
        data: {
            start: today.toISOString().split('T')[0],
            end: nextWeek.toISOString().split('T')[0]
        },
        success: function(events) {
            let html = '';
            
            if (events.length === 0) {
                html = '<div class="text-center text-muted">No upcoming leaves</div>';
            } else {
                // Group events by date and limit to 5
                const upcomingEvents = events.slice(0, 5);
                upcomingEvents.forEach(function(event) {
                    const props = event.extendedProps;
                    const startDate = new Date(event.start);
                    
                    html += `
                        <div class="leave-item">
                            <div>
                                <strong>${props.employee_name}</strong>
                                <br>
                                <small>${props.leave_type} - ${props.department}</small>
                            </div>
                            <div class="text-right">
                                <small>${startDate.toLocaleDateString()}</small>
                                <br>
                                <small class="badge badge-info">${props.duration}</small>
                            </div>
                        </div>
                    `;
                });
            }
            
            $('#upcomingLeaves').html(html);
        },
        error: function(xhr) {
            $('#upcomingLeaves').html('<div class="text-center text-danger">Failed to load upcoming leaves</div>');
        }
    });
}
</script>
@endpush
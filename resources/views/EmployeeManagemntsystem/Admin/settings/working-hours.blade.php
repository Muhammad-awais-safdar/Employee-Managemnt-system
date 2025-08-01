@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'Working Hours Settings')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="page-title">Working Hours Settings</h3>
                <p class="text-muted">Configure company working hours policy and attendance rules</p>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" onclick="openSettingsForm()">
                        <i class="fa fa-edit me-2"></i>Edit Settings
                    </button>
                    <button type="button" class="btn btn-success" onclick="createNewSettings()">
                        <i class="fa fa-plus me-2"></i>New Policy
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Settings Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card settings-overview-card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 text-white">Current Working Hours Policy</h5>
                            <small class="text-white-50">{{ $company->company_name ?? 'Company' }}</small>
                        </div>
                        <div class="settings-status">
                            <span class="badge bg-success">
                                <i class="fa fa-check me-1"></i>Active
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Basic Hours -->
                        <div class="col-lg-6">
                            <div class="settings-section">
                                <h6 class="section-title">
                                    <i class="fa fa-clock text-primary me-2"></i>Basic Working Hours
                                </h6>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="setting-item">
                                            <label class="setting-label">Check-in Time</label>
                                            <p class="setting-value">{{ $workingHours->check_in_time }}</p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="setting-item">
                                            <label class="setting-label">Check-out Time</label>
                                            <p class="setting-value">{{ $workingHours->check_out_time }}</p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="setting-item">
                                            <label class="setting-label">Daily Hours</label>
                                            <p class="setting-value">{{ $workingHours->formatted_standard_hours }}</p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="setting-item">
                                            <label class="setting-label">Break Duration</label>
                                            <p class="setting-value">{{ $workingHours->formatted_break_duration }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Rules -->
                        <div class="col-lg-6">
                            <div class="settings-section">
                                <h6 class="section-title">
                                    <i class="fa fa-user-clock text-warning me-2"></i>Attendance Rules
                                </h6>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="setting-item">
                                            <label class="setting-label">Late Threshold</label>
                                            <p class="setting-value">{{ $workingHours->late_threshold }} minutes</p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="setting-item">
                                            <label class="setting-label">Grace Period</label>
                                            <p class="setting-value">{{ $workingHours->grace_period }} minutes</p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="setting-item">
                                            <label class="setting-label">Early Leave</label>
                                            <p class="setting-value">{{ $workingHours->early_leave_threshold }} minutes</p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="setting-item">
                                            <label class="setting-label">Auto Break</label>
                                            <p class="setting-value">
                                                <span class="badge {{ $workingHours->auto_break_deduction ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $workingHours->auto_break_deduction ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Overtime Settings -->
                        <div class="col-lg-6">
                            <div class="settings-section">
                                <h6 class="section-title">
                                    <i class="fa fa-money-bill text-success me-2"></i>Overtime Rates
                                </h6>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="setting-item">
                                            <label class="setting-label">Regular OT</label>
                                            <p class="setting-value">{{ $workingHours->overtime_rate }}x</p>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="setting-item">
                                            <label class="setting-label">Weekend OT</label>
                                            <p class="setting-value">{{ $workingHours->weekend_overtime_rate }}x</p>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="setting-item">
                                            <label class="setting-label">Holiday OT</label>
                                            <p class="setting-value">{{ $workingHours->holiday_overtime_rate }}x</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Working Days -->
                        <div class="col-lg-6">
                            <div class="settings-section">
                                <h6 class="section-title">
                                    <i class="fa fa-calendar text-info me-2"></i>Working Days & Flexibility
                                </h6>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="setting-item">
                                            <label class="setting-label">Working Days</label>
                                            <div class="working-days-display">
                                                @php
                                                    $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                                    $workingDays = $workingHours->working_days ?? [1,2,3,4,5];
                                                @endphp
                                                @foreach($dayNames as $index => $day)
                                                    <span class="badge {{ in_array($index, $workingDays) ? 'bg-primary' : 'bg-light text-dark' }} me-1">
                                                        {{ $day }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="setting-item">
                                            <label class="setting-label">Flexible Hours</label>
                                            <p class="setting-value">
                                                <span class="badge {{ $workingHours->flexible_hours ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $workingHours->flexible_hours ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="setting-item">
                                            <label class="setting-label">Location Tracking</label>
                                            <p class="setting-value">
                                                <span class="badge {{ $workingHours->track_location ? 'bg-warning' : 'bg-secondary' }}">
                                                    {{ $workingHours->track_location ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($workingHours->flexible_hours)
                        <!-- Flexible Hours Details -->
                        <div class="col-12">
                            <div class="settings-section">
                                <h6 class="section-title">
                                    <i class="fa fa-clock-rotate-left text-purple me-2"></i>Flexible Hours Configuration
                                </h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="setting-item">
                                            <label class="setting-label">Core Hours Start</label>
                                            <p class="setting-value">{{ $workingHours->core_hours_start ?? 'Not Set' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="setting-item">
                                            <label class="setting-label">Core Hours End</label>
                                            <p class="setting-value">{{ $workingHours->core_hours_end ?? 'Not Set' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="setting-item">
                                            <label class="setting-label">Min Daily Hours</label>
                                            <p class="setting-value">{{ floor($workingHours->min_daily_hours / 60) }}h {{ $workingHours->min_daily_hours % 60 }}m</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="setting-item">
                                            <label class="setting-label">Max Daily Hours</label>
                                            <p class="setting-value">{{ floor($workingHours->max_daily_hours / 60) }}h {{ $workingHours->max_daily_hours % 60 }}m</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <div class="stat-icon bg-primary mb-3">
                        <i class="fa fa-users"></i>
                    </div>
                    <h4 class="mb-1">{{ $company->users_count ?? 0 }}</h4>
                    <p class="text-muted mb-0">Employees Affected</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <div class="stat-icon bg-success mb-3">
                        <i class="fa fa-clock"></i>
                    </div>
                    <h4 class="mb-1">{{ $workingHours->formatted_standard_hours }}</h4>
                    <p class="text-muted mb-0">Daily Working Hours</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <div class="stat-icon bg-warning mb-3">
                        <i class="fa fa-calendar-week"></i>
                    </div>
                    <h4 class="mb-1">{{ count($workingHours->working_days ?? []) }}</h4>
                    <p class="text-muted mb-0">Working Days/Week</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <div class="stat-icon bg-info mb-3">
                        <i class="fa fa-percentage"></i>
                    </div>
                    <h4 class="mb-1">{{ $workingHours->overtime_rate }}x</h4>
                    <p class="text-muted mb-0">Overtime Rate</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-overview-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    overflow: hidden;
}

.settings-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    height: 100%;
}

.section-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}

.setting-item {
    margin-bottom: 15px;
}

.setting-label {
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 500;
    margin-bottom: 5px;
    display: block;
}

.setting-value {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.working-days-display .badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
}

.stat-card {
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    border-radius: 10px;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin: 0 auto;
}

.settings-status .badge {
    padding: 0.5rem 1rem;
}

.text-purple {
    color: #6f42c1 !important;
}

.bg-purple {
    background-color: #6f42c1 !important;
}

@media (max-width: 768px) {
    .settings-section {
        margin-bottom: 20px;
    }
    
    .stat-card .card-body {
        padding: 15px;
    }
}
</style>

<script>
function openSettingsForm() {
    window.location.href = "{{ route(auth()->user()->getRoleNames()->first() . '.working-hours.edit', $workingHours->id) }}";
}

function createNewSettings() {
    window.location.href = "{{ route(auth()->user()->getRoleNames()->first() . '.working-hours.create') }}";
}

// Show success message if settings were updated
@if(session('success'))
    toastr.success("{{ session('success') }}", 'Settings Updated');
@endif

@if(session('error'))
    toastr.error("{{ session('error') }}", 'Error');
@endif
</script>
@endsection
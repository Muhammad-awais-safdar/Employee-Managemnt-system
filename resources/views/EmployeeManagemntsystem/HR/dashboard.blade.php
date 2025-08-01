@extends('EmployeeManagemntsystem.Layout.App')

@push('styles')
<style>
    .stats-card {
        border-left: 4px solid #3E007C;
        transition: all 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    .progress-sm {
        height: 6px;
    }
    .metric-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
    }
    .activity-timeline {
        position: relative;
        padding-left: 20px;
    }
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e3e6f0;
    }
    .activity-item {
        position: relative;
        margin-bottom: 20px;
    }
    .activity-item::before {
        content: '';
        position: absolute;
        left: -16px;
        top: 8px;
        width: 8px;
        height: 8px;
        background: #3E007C;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 0 2px #e3e6f0;
    }
    .quick-action-card {
        transition: all 0.3s ease;
        border: 1px solid #e3e6f0 !important;
        background: #fff;
    }
    .quick-action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(62, 0, 124, 0.15);
        border-color: #3E007C !important;
    }
    .quick-action-card i {
        transition: transform 0.3s ease;
    }
    .quick-action-card:hover i {
        transform: scale(1.1);
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
                    <h4 class="fw-bold text-purple mb-1">HR Dashboard</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('HR.dashboard') }}" class="text-purple">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('HR.departments.assignments') }}" class="btn btn-outline-purple">
                        <i class="ti ti-users-group me-2"></i>Manage Assignments
                    </a>
                    <a href="{{ route('HR.users.create') }}" class="btn btn-purple">
                        <i class="ti ti-user-plus me-2"></i>Add Employee
                    </a>
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
                            <h6 class="text-muted mb-2 fw-normal">Total Employees</h6>
                            <h3 class="fw-bold text-purple mb-1">{{ $stats['total_employees'] }}</h3>
                            <small class="text-muted">
                                <span class="text-{{ $stats['monthly_growth'] >= 0 ? 'success' : 'danger' }}">
                                    <i class="ti ti-trending-{{ $stats['monthly_growth'] >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($stats['monthly_growth']) }}%
                                </span>
                                this month
                            </small>
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
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card h-100" style="border-left-color: #28a745;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2 fw-normal">Active Employees</h6>
                            <h3 class="fw-bold text-success mb-1">{{ $stats['active_employees'] }}</h3>
                            <div class="progress progress-sm mt-2">
                                <div class="progress-bar bg-success" style="width: {{ $stats['total_employees'] > 0 ? ($stats['active_employees'] / $stats['total_employees']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-success-subtle text-success rounded">
                                <i class="ti ti-user-check fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card h-100" style="border-left-color: #ffc107;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2 fw-normal">Unassigned</h6>
                            <h3 class="fw-bold text-warning mb-1">{{ $stats['unassigned_employees'] }}</h3>
                            <small class="text-muted">{{ $stats['assignment_completion_rate'] }}% assigned</small>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-warning-subtle text-warning rounded">
                                <i class="ti ti-user-question fs-18"></i>
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
                            <h6 class="text-muted mb-2 fw-normal">New Hires</h6>
                            <h3 class="fw-bold text-info mb-1">{{ $stats['new_hires_this_month'] }}</h3>
                            <small class="text-muted">This month</small>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-info-subtle text-info rounded">
                                <i class="ti ti-user-plus fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Overview and Recent Activities -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-building-community me-2"></i>Department Overview
                        </h5>
                        <a href="{{ route('HR.departments.assignments') }}" class="btn btn-sm btn-outline-purple">
                            Manage Assignments
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($departmentOverview as $department)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-semibold mb-0">{{ $department->name }}</h6>
                                    <span class="badge bg-primary-subtle text-primary">{{ $department->users_count }}</span>
                                </div>
                                <p class="text-muted small mb-2">{{ Str::limit($department->description, 50) ?: 'No description' }}</p>
                                <div class="progress progress-sm">
                                    <div class="progress-bar" style="width: {{ $department->users_count > 0 ? min(($department->users_count / 10) * 100, 100) : 0 }}%"></div>
                                </div>
                                <small class="text-muted">{{ $department->users_count }} employees</small>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-4">
                            <i class="ti ti-building-community text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No departments found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-activity me-2"></i>Recent Activities
                    </h5>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        @forelse($recentActivities as $activity)
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="fs-14 mb-1">{{ $activity['user']->name }}</h6>
                                    <p class="text-muted fs-13 mb-1">{{ $activity['action'] }}</p>
                                    <small class="text-muted">{{ $activity['date'] ?? $activity['time'] }}</small>
                                </div>
                                <span class="badge badge-soft-{{ $activity['type'] === 'new_hire' ? 'success' : 'info' }}">
                                    {{ $activity['time'] }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-3">
                            <i class="ti ti-activity text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">No recent activities</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Hires and Pending Assignments -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-user-plus me-2"></i>Recent New Hires
                        </h5>
                        <a href="{{ route('HR.users.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($newHires as $hire)
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm bg-success text-white rounded-circle me-3">
                                {{ strtoupper(substr($hire->name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="fs-14 mb-0">{{ $hire->name }}</h6>
                                <small class="text-muted">{{ $hire->email }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <small class="text-success">{{ $hire->created_at->format('M d') }}</small>
                            @if($hire->department)
                                <div><span class="badge badge-sm bg-primary-subtle text-primary">{{ $hire->department->name }}</span></div>
                            @else
                                <div><span class="badge badge-sm bg-warning-subtle text-warning">Unassigned</span></div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3">
                        <i class="ti ti-user-plus text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">No new hires this month</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-user-question me-2"></i>Pending Assignments
                        </h5>
                        <a href="{{ route('HR.departments.assignments') }}" class="btn btn-sm btn-outline-warning">Assign Now</a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($pendingAssignments as $user)
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm bg-warning text-white rounded-circle me-3">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="fs-14 mb-0">{{ $user->name }}</h6>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                            <div>
                                <a href="{{ route('HR.departments.assignments') }}" class="btn btn-xs btn-outline-primary">
                                    Assign
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3">
                        <i class="ti ti-check-circle text-success" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">All employees are assigned!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('HR.users.index') }}" class="text-decoration-none">
                                <div class="quick-action-card p-3 text-center border rounded h-100">
                                    <i class="ti ti-users fs-24 text-primary mb-2"></i>
                                    <h6 class="mb-1">Manage Employees</h6>
                                    <small class="text-muted">View and manage all employees</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('HR.departments.assignments') }}" class="text-decoration-none">
                                <div class="quick-action-card p-3 text-center border rounded h-100">
                                    <i class="ti ti-building-community fs-24 text-success mb-2"></i>
                                    <h6 class="mb-1">Department Assignments</h6>
                                    <small class="text-muted">Assign employees to departments</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('HR.attendance.index') }}" class="text-decoration-none">
                                <div class="quick-action-card p-3 text-center border rounded h-100">
                                    <i class="ti ti-clock fs-24 text-info mb-2"></i>
                                    <h6 class="mb-1">Attendance Reports</h6>
                                    <small class="text-muted">Track employee attendance</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('HR.leave.index') }}" class="text-decoration-none">
                                <div class="quick-action-card p-3 text-center border rounded h-100">
                                    <i class="ti ti-calendar-event fs-24 text-warning mb-2"></i>
                                    <h6 class="mb-1">Leave Management</h6>
                                    <small class="text-muted">Review leave applications</small>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('HR.users.create') }}" class="text-decoration-none">
                                <div class="quick-action-card p-3 text-center border rounded h-100">
                                    <i class="ti ti-user-plus fs-24 text-purple mb-2"></i>
                                    <h6 class="mb-1">Add New Employee</h6>
                                    <small class="text-muted">Onboard new employees</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('HR.departments.index') }}" class="text-decoration-none">
                                <div class="quick-action-card p-3 text-center border rounded h-100">
                                    <i class="ti ti-building fs-24 text-secondary mb-2"></i>
                                    <h6 class="mb-1">Departments</h6>
                                    <small class="text-muted">View all departments</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('HR.leave-types.index') }}" class="text-decoration-none">
                                <div class="quick-action-card p-3 text-center border rounded h-100">
                                    <i class="ti ti-list fs-24 text-primary mb-2"></i>
                                    <h6 class="mb-1">Leave Types</h6>
                                    <small class="text-muted">View leave type policies</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('HR.working-hours.index') }}" class="text-decoration-none">
                                <div class="quick-action-card p-3 text-center border rounded h-100">
                                    <i class="ti ti-clock-hour-9 fs-24 text-success mb-2"></i>
                                    <h6 class="mb-1">Working Hours</h6>
                                    <small class="text-muted">View working hours settings</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HR Metrics Summary -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-chart-bar me-2"></i>HR Metrics Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="metric-card p-3 mb-3">
                                <h4 class="fw-bold mb-1">{{ $hrMetrics['assignment_rate'] }}%</h4>
                                <p class="mb-0 opacity-75">Assignment Rate</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="metric-card p-3 mb-3" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                                <h4 class="fw-bold mb-1">{{ $hrMetrics['new_hires_this_week'] }}</h4>
                                <p class="mb-0 opacity-75">This Week Hires</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="metric-card p-3 mb-3" style="background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);">
                                <h4 class="fw-bold mb-1">{{ $hrMetrics['retention_rate'] }}%</h4>
                                <p class="mb-0 opacity-75">Retention Rate</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="metric-card p-3 mb-3" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                                <h4 class="fw-bold mb-1 text-dark">{{ $stats['total_departments'] }}</h4>
                                <p class="mb-0 opacity-75 text-dark">Active Departments</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any interactive functionality here
    console.log('HR Dashboard loaded successfully');
});
</script>
@endpush
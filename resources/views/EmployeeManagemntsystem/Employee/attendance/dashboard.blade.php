@extends('EmployeeManagemntsystem.Layout.employee')

@section('title', 'Attendance Dashboard')

@section('content')
    <div class="col-lg-9">
        <!-- Breadcrumb -->
        <div class="card mb-4">
            <div class="card-body">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-divide p-0 mb-2">
                        <li class="breadcrumb-item d-flex align-items-center fw-medium">
                            <a href="{{ route('Employee.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active fw-medium" aria-current="page">Attendance</li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1">Attendance Dashboard</h4>
                        <p class="text-muted mb-0">Manage your daily attendance and track your work hours</p>
                    </div>
                    <div class="attendance-actions">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="check-in-btn" onclick="checkIn()"
                                {{ $todayAttendance && $todayAttendance->check_in_time ? 'disabled' : '' }}>
                                <i class="ti ti-login me-1"></i>Check In
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="check-out-btn" onclick="checkOut()"
                                {{ !$todayAttendance || !$todayAttendance->check_in_time || $todayAttendance->check_out_time ? 'disabled' : '' }}>
                                <i class="ti ti-logout me-1"></i>Check Out
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" id="break-btn" onclick="toggleBreak()"
                                {{ !$todayAttendance || !$todayAttendance->check_in_time || $todayAttendance->check_out_time ? 'disabled' : '' }}>
                                <i class="ti ti-clock-pause me-1"></i><span id="break-text">Break</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Status Dashboard -->
        <div class="row">
            <div class="col-12">
                <div class="card today-status-card border-0 shadow-sm">
                    <div class="card-header bg-gradient-primary text-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 text-white">Today's Attendance</h5>
                                <p class="mb-0 text-white-50">{{ now()->format('l, F j, Y') }}</p>
                            </div>
                            <div class="live-clock">
                                <h3 class="mb-0 text-white font-weight-bold">{{ now()->format('H:i:s') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-xl-2 col-lg-4 col-md-6">
                                <div class="stat-item text-center">
                                    <div class="stat-icon bg-success mb-3">
                                        <i class="ti ti-login"></i>
                                    </div>
                                    <h6 class="stat-label text-muted mb-1">Check In</h6>
                                    <h4 class="stat-value mb-0" id="check-in-time">{{ $todayAttendance->check_in_time ?? '--:--' }}</h4>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-4 col-md-6">
                                <div class="stat-item text-center">
                                    <div class="stat-icon bg-danger mb-3">
                                        <i class="ti ti-logout"></i>
                                    </div>
                                    <h6 class="stat-label text-muted mb-1">Check Out</h6>
                                    <h4 class="stat-value mb-0" id="check-out-time">{{ $todayAttendance->check_out_time ?? '--:--' }}</h4>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-4 col-md-6">
                                <div class="stat-item text-center">
                                    <div class="stat-icon bg-primary mb-3">
                                        <i class="ti ti-clock"></i>
                                    </div>
                                    <h6 class="stat-label text-muted mb-1">Working Hours</h6>
                                    <h4 class="stat-value mb-0" id="working-hours">{{ $todayAttendance ? $todayAttendance->formatted_total_hours : '00:00' }}</h4>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-4 col-md-6">
                                <div class="stat-item text-center">
                                    <div class="stat-icon bg-warning mb-3">
                                        <i class="ti ti-clock-pause"></i>
                                    </div>
                                    <h6 class="stat-label text-muted mb-1">Break Time</h6>
                                    <h4 class="stat-value mb-0" id="break-time">{{ $todayAttendance ? $todayAttendance->formatted_break_duration : '00:00' }}</h4>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-4 col-md-6">
                                <div class="stat-item text-center">
                                    <div class="stat-icon bg-info mb-3">
                                        <i class="ti ti-user-check"></i>
                                    </div>
                                    <h6 class="stat-label text-muted mb-1">Status</h6>
                                    <span id="attendance-status" class="badge fs-6 {{ $todayAttendance ? $todayAttendance->status_badge_class : 'bg-secondary' }}">
                                        {{ $todayAttendance ? $todayAttendance->status_label : 'Not Marked' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-4 col-md-6">
                                <div class="stat-item text-center">
                                    <div class="stat-icon bg-dark mb-3">
                                        <i class="ti ti-clock-plus"></i>
                                    </div>
                                    <h6 class="stat-label text-muted mb-1">Overtime</h6>
                                    <h4 class="stat-value mb-0" id="overtime-hours">{{ $todayAttendance && $todayAttendance->overtime_hours > 0 ? $todayAttendance->overtime_hours . 'h' : '0h' }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Statistics -->
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stats-card hover-lift border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="ti ti-check fs-24"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="stats-number mb-1">{{ $monthSummary['present_days'] }}</h3>
                                <p class="stats-label text-muted mb-0">Present Days</p>
                                <small class="text-success"><i class="ti ti-trending-up me-1"></i>This Month</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stats-card hover-lift border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                                <i class="ti ti-clock fs-24"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="stats-number mb-1">{{ $monthSummary['late_days'] }}</h3>
                                <p class="stats-label text-muted mb-0">Late Days</p>
                                <small class="text-warning"><i class="ti ti-clock me-1"></i>This Month</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stats-card hover-lift border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-danger bg-opacity-10 text-danger me-3">
                                <i class="ti ti-x fs-24"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="stats-number mb-1">{{ $monthSummary['absent_days'] }}</h3>
                                <p class="stats-label text-muted mb-0">Absent Days</p>
                                <small class="text-danger"><i class="ti ti-trending-down me-1"></i>This Month</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stats-card hover-lift border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-info bg-opacity-10 text-info me-3">
                                <i class="ti ti-percentage fs-24"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="stats-number mb-1">{{ $monthSummary['attendance_percentage'] }}%</h3>
                                <p class="stats-label text-muted mb-0">Attendance Rate</p>
                                <small class="text-info"><i class="ti ti-chart-line me-1"></i>Monthly Avg</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Policy & Summary Cards -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card policy-card border-0 shadow-sm h-100">
                    <div class="card-header bg-light border-0">
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <div class="icon-wrapper bg-primary bg-opacity-10 text-primary">
                                    <i class="ti ti-clock-cog"></i>
                                </div>
                            </div>
                            <h5 class="mb-0">Working Hours Policy</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="policy-item">
                                    <div class="policy-icon text-success mb-2">
                                        <i class="ti ti-sun"></i>
                                    </div>
                                    <h6 class="policy-label mb-1">Check-in Time</h6>
                                    <p class="policy-value mb-0">{{ $workingHours['check_in_time'] }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="policy-item">
                                    <div class="policy-icon text-danger mb-2">
                                        <i class="ti ti-moon"></i>
                                    </div>
                                    <h6 class="policy-label mb-1">Check-out Time</h6>
                                    <p class="policy-value mb-0">{{ $workingHours['check_out_time'] }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="policy-item">
                                    <div class="policy-icon text-info mb-2">
                                        <i class="ti ti-hourglass"></i>
                                    </div>
                                    <h6 class="policy-label mb-1">Daily Hours</h6>
                                    <p class="policy-value mb-0">{{ floor($workingHours['standard_hours'] / 60) }} hours</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="policy-item">
                                    <div class="policy-icon text-warning mb-2">
                                        <i class="ti ti-clock-plus"></i>
                                    </div>
                                    <h6 class="policy-label mb-1">Overtime Rate</h6>
                                    <p class="policy-value mb-0">{{ $workingHours['overtime_rate'] }}x</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card summary-card border-0 shadow-sm h-100">
                    <div class="card-header bg-light border-0">
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <div class="icon-wrapper bg-success bg-opacity-10 text-success">
                                    <i class="ti ti-chart-bar"></i>
                                </div>
                            </div>
                            <h5 class="mb-0">Monthly Summary</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="summary-grid">
                            <div class="summary-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-clock text-primary me-2"></i>
                                        <span class="summary-label">Total Working Hours</span>
                                    </div>
                                    <span class="summary-value text-primary fw-bold">{{ floor($monthSummary['total_hours'] / 60) }}h {{ $monthSummary['total_hours'] % 60 }}m</span>
                                </div>
                            </div>
                            <div class="summary-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-clock-plus text-warning me-2"></i>
                                        <span class="summary-label">Overtime Hours</span>
                                    </div>
                                    <span class="summary-value text-warning fw-bold">{{ $monthSummary['total_overtime_hours'] }}h</span>
                                </div>
                            </div>
                            <div class="summary-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-clock-hour-12 text-info me-2"></i>
                                        <span class="summary-label">Half Days</span>
                                    </div>
                                    <span class="summary-value text-info fw-bold">{{ $monthSummary['half_days'] }}</span>
                                </div>
                            </div>
                            <div class="summary-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-calendar-off text-danger me-2"></i>
                                        <span class="summary-label">Unpaid Leave</span>
                                    </div>
                                    <span class="summary-value text-danger fw-bold">{{ $monthSummary['unpaid_leave_days'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance History -->
        <div class="row">
            <div class="col-12">
                <div class="card history-card border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <div class="icon-wrapper bg-secondary bg-opacity-10 text-secondary">
                                        <i class="ti ti-history"></i>
                                    </div>
                                </div>
                                <h5 class="mb-0">Recent Attendance History</h5>
                            </div>
                            <small class="text-muted">Last 10 records</small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-semibold">Date</th>
                                        <th class="border-0 fw-semibold">Check In</th>
                                        <th class="border-0 fw-semibold">Check Out</th>
                                        <th class="border-0 fw-semibold d-none d-md-table-cell">Working Hours</th>
                                        <th class="border-0 fw-semibold d-none d-lg-table-cell">Break Time</th>
                                        <th class="border-0 fw-semibold">Status</th>
                                        <th class="border-0 fw-semibold d-none d-xl-table-cell">Overtime</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentAttendance as $attendance)
                                        <tr>
                                            <td class="align-middle">
                                                <div class="fw-semibold">{{ $attendance->date->format('M d') }}</div>
                                                <small class="text-muted">{{ $attendance->date->format('Y') }}</small>
                                            </td>
                                            <td class="align-middle">
                                                <span class="time-badge {{ $attendance->check_in_time ? 'bg-success-subtle text-success' : 'bg-light text-muted' }}">
                                                    {{ $attendance->check_in_time ?? '--:--' }}
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="time-badge {{ $attendance->check_out_time ? 'bg-danger-subtle text-danger' : 'bg-light text-muted' }}">
                                                    {{ $attendance->check_out_time ?? '--:--' }}
                                                </span>
                                            </td>
                                            <td class="align-middle d-none d-md-table-cell">
                                                <span class="fw-semibold">{{ $attendance->formatted_total_hours }}</span>
                                            </td>
                                            <td class="align-middle d-none d-lg-table-cell">
                                                <span class="text-muted">{{ $attendance->formatted_break_duration }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge {{ $attendance->status_badge_class }} fs-7">
                                                    {{ $attendance->status_label }}
                                                </span>
                                            </td>
                                            <td class="align-middle d-none d-xl-table-cell">
                                                @if ($attendance->overtime_hours > 0)
                                                    <span class="text-success fw-semibold">+{{ $attendance->overtime_hours }}h</span>
                                                @else
                                                    <span class="text-muted">--</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="empty-state">
                                                    <div class="empty-icon mb-3">
                                                        <i class="ti ti-calendar-off text-muted" style="font-size: 3rem;"></i>
                                                    </div>
                                                    <h6 class="text-muted mb-1">No attendance records found</h6>
                                                    <p class="text-muted small mb-0">Your attendance history will appear here</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <style>
        /* Modern Card Styles */
        .today-status-card {
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .today-status-card .card-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            position: relative;
        }

        .today-status-card .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="80" cy="80" r="1" fill="%23ffffff" opacity="0.05"/><circle cx="40" cy="70" r="1" fill="%23ffffff" opacity="0.08"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            opacity: 0.3;
        }

        .live-clock {
            font-family: 'Courier New', monospace;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .stat-item {
            padding: 1rem;
            border-radius: 12px;
            background: rgba(248, 250, 252, 0.8);
            border: 1px solid rgba(226, 232, 240, 0.5);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            background: white;
            border-color: rgba(99, 102, 241, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 1.5rem;
        }

        .stat-label {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }

        /* Statistics Cards */
        .stats-card {
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .stats-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1;
        }

        .stats-label {
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Policy & Summary Cards */
        .policy-card,
        .summary-card {
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        .icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .policy-item {
            text-align: center;
            padding: 1rem 0.5rem;
        }

        .policy-icon {
            font-size: 1.5rem;
        }

        .policy-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
        }

        .policy-value {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
        }

        .summary-grid {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .summary-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
        }

        .summary-value {
            font-size: 1rem;
            font-weight: 700;
        }

        /* History Card */
        .history-card {
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        .time-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid currentColor;
        }

        .empty-state {
            padding: 3rem 1rem;
        }

        .empty-icon {
            opacity: 0.6;
        }

        /* Enhanced Toast Styles */
        .toast-top-right {
            top: 100px !important;
            right: 20px !important;
            z-index: 9999;
        }

        #toast-container > div {
            opacity: 0.96;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            border: none;
            backdrop-filter: blur(10px);
        }

        #toast-container .toast-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-left: 4px solid #065f46;
        }

        #toast-container .toast-error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-left: 4px solid #991b1b;
        }

        #toast-container .toast-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-left: 4px solid #92400e;
            color: #1f2937 !important;
        }

        #toast-container .toast-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-left: 4px solid #1e40af;
        }

        /* Enhanced Button Styles */
        .btn-group .btn {
            border-radius: 10px;
            font-weight: 600;
            padding: 12px 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 0 4px;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .btn-group .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-group .btn:hover::before {
            left: 100%;
        }

        .btn-group .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-outline-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: 2px solid transparent;
            color: white;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            border-color: transparent;
            color: white;
        }

        .btn-outline-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: 2px solid transparent;
            color: white;
        }

        .btn-outline-danger:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border-color: transparent;
            color: white;
        }

        .btn-outline-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: 2px solid transparent;
            color: white;
        }

        .btn-outline-warning:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            border-color: transparent;
            color: white;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Enhanced Table Styles */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-group .btn {
                margin: 0;
                padding: 14px 20px;
            }

            .stats-number {
                font-size: 1.75rem;
            }

            .stat-value {
                font-size: 1.25rem;
            }

            .live-clock h3 {
                font-size: 1.75rem;
            }

            .summary-grid {
                gap: 0.75rem;
            }

            .toast-top-right {
                top: 80px !important;
                right: 15px !important;
                left: 15px !important;
                width: auto !important;
            }
        }

        @media (max-width: 576px) {
            .today-status-card .card-body {
                padding: 1.5rem;
            }

            .stat-item {
                padding: 0.75rem 0.5rem;
            }

            .policy-item {
                padding: 0.75rem 0.25rem;
            }

            .stats-card .card-body {
                padding: 1rem;
            }

            .table-responsive {
                font-size: 0.875rem;
            }
        }

        /* Background gradient for the main content area */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
        }

        /* Utility classes */
        .bg-success-subtle {
            background-color: rgba(34, 197, 94, 0.1) !important;
        }

        .bg-danger-subtle {
            background-color: rgba(239, 68, 68, 0.1) !important;
        }

        .text-success {
            color: #16a34a !important;
        }

        .text-danger {
            color: #dc2626 !important;
        }

        .fs-7 {
            font-size: 0.875rem !important;
        }
    </style>

    <script>
        let isOnBreak = false;
        let breakStartTime = null;

        // Initialize break status after DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            @if ($todayAttendance && $todayAttendance->break_times)
                const breakTimes = @json($todayAttendance->break_times);
                
                if (breakTimes && Array.isArray(breakTimes)) {
                    breakTimes.forEach(function(breakTime) {
                        if (breakTime.start && !breakTime.end) {
                            isOnBreak = true;
                            breakStartTime = breakTime.start;
                            updateBreakButton();
                        }
                    });
                }
            @endif
            
            // Show welcome messages
            @if ($todayAttendance)
                @if ($todayAttendance->check_in_time && !$todayAttendance->check_out_time)
                    toastr.info('Welcome back! You checked in at {{ $todayAttendance->check_in_time }}', 'Status Update');
                @elseif ($todayAttendance->check_out_time)
                    toastr.success('You have completed your work day. Total hours: {{ $todayAttendance->formatted_total_hours }}', 'Day Complete');
                @endif
            @else
                toastr.info('Good day! Please check in to start your work day.', 'Welcome');
            @endif
            
            // Final sync check - ensure JavaScript variable matches button state
            const breakBtn = document.getElementById('break-btn');
            if (breakBtn) {
                const buttonBasedStatus = getCurrentBreakStatus();
                if (buttonBasedStatus !== isOnBreak) {
                    isOnBreak = buttonBasedStatus;
                }
                updateBreakButton();
            }
        });

        // Helper function for better error messages
        function showAttendanceError(message) {
            if (message.includes('already checked in')) {
                toastr.warning(message, 'Already Checked In');
            } else if (message.includes('already checked out')) {
                toastr.warning(message, 'Already Checked Out');
            } else if (message.includes('must check in')) {
                toastr.warning(message, 'Check In Required');
            } else if (message.includes('already on break')) {
                toastr.info(message, 'Break Status');
            } else if (message.includes('No active break')) {
                toastr.info(message, 'Break Status');
            } else {
                toastr.error(message, 'Error');
            }
        }

        // Update time every second with enhanced styling
        setInterval(function() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour12: false
            });
            const clockElement = document.querySelector('.live-clock h3');
            if (clockElement) {
                clockElement.textContent = timeString;
            }
        }, 1000);

        function checkIn() {
            const checkInBtn = document.getElementById('check-in-btn');
            if (checkInBtn.disabled) {
                toastr.warning('You have already checked in today or check-in is not available.', 'Check-in Unavailable');
                return;
            }

            // Show loading message
            toastr.info('Processing check-in...', 'Please Wait');

            // Disable button during request and show loading state
            checkInBtn.disabled = true;
            const originalText = checkInBtn.innerHTML;
            checkInBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Checking In...';

            fetch("{{ route('Employee.attendance.check-in') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message, 'Success');

                        // Update UI
                        const now = new Date();
                        const timeString = now.toLocaleTimeString('en-US', {
                            hour12: false,
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        document.getElementById('check-in-time').textContent = timeString;
                        document.getElementById('check-in-btn').disabled = true;
                        document.getElementById('check-out-btn').disabled = false;
                        document.getElementById('break-btn').disabled = false;

                        // Update status
                        const statusBadge = document.getElementById('attendance-status');
                        statusBadge.className = 'badge bg-success';
                        statusBadge.textContent = 'Present';

                        // Show additional success info
                        toastr.success('You are now marked as present for today!', 'Welcome');

                    } else {
                        showAttendanceError(data.message);
                        // Re-enable button on error and restore text
                        checkInBtn.disabled = false;
                        checkInBtn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while checking in', 'Network Error');
                    // Re-enable button on error and restore text
                    checkInBtn.disabled = false;
                    checkInBtn.innerHTML = originalText;
                });
        }

        function checkOut() {
            const checkOutBtn = document.getElementById('check-out-btn');
            if (checkOutBtn.disabled) {
                toastr.warning('You must check in first or have already checked out.', 'Check-out Unavailable');
                return;
            }

            // Show loading message
            toastr.info('Processing check-out...', 'Please Wait');

            // Disable button during request and show loading state
            checkOutBtn.disabled = true;
            const originalText = checkOutBtn.innerHTML;
            checkOutBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Checking Out...';

            fetch("{{ route('Employee.attendance.check-out') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message, 'Success');

                        // Update UI
                        const now = new Date();
                        const timeString = now.toLocaleTimeString('en-US', {
                            hour12: false,
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        document.getElementById('check-out-time').textContent = timeString;
                        document.getElementById('check-out-btn').disabled = true;
                        document.getElementById('break-btn').disabled = true;

                        // Reset break status
                        isOnBreak = false;
                        updateBreakButton();

                        // Update working hours
                        if (data.total_hours) {
                            document.getElementById('working-hours').textContent = data.total_hours;
                        }

                        // Update overtime if present
                        if (data.attendance && data.attendance.overtime_hours) {
                            document.getElementById('overtime-hours').textContent = data.attendance.overtime_hours +
                            'h';
                        }

                        // Show additional success info with work summary
                        const workingHours = document.getElementById('working-hours').textContent;
                        toastr.success(`Work day completed! Total hours: ${workingHours}`, 'Good Work!');

                    } else {
                        showAttendanceError(data.message);
                        // Re-enable button on error and restore text
                        checkOutBtn.disabled = false;
                        checkOutBtn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while checking out', 'Network Error');
                    // Re-enable button on error and restore text
                    checkOutBtn.disabled = false;
                    checkOutBtn.innerHTML = originalText;
                });
        }

        function toggleBreak() {
            const breakBtn = document.getElementById('break-btn');
            if (breakBtn.disabled) {
                toastr.warning('Break management is not available. Please check your attendance status.',
                    'Break Unavailable');
                return;
            }

            // Determine break status by checking button text as source of truth
            const isCurrentlyOnBreak = getCurrentBreakStatus();

            // Use the button text to determine the correct route
            const url = isCurrentlyOnBreak ? "{{ route('Employee.attendance.end-break') }}" :
                "{{ route('Employee.attendance.start-break') }}";
            const action = isCurrentlyOnBreak ? 'Ending break...' : 'Starting break...';

            // Show loading message
            toastr.info(action, 'Please Wait');

            // Disable button during request and show loading state
            breakBtn.disabled = true;
            const originalText = breakBtn.innerHTML;
            const loadingIcon = '<i class="fa fa-spinner fa-spin me-2"></i>';
            breakBtn.innerHTML = loadingIcon + (isCurrentlyOnBreak ? 'Ending Break...' : 'Starting Break...');

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message, 'Success');

                        // Update the JavaScript variable to match the new state
                        isOnBreak = !isCurrentlyOnBreak;
                        updateBreakButton();

                        // Show additional info for break actions
                        if (!isOnBreak) {
                            toastr.info('Break ended. You can now continue working.', 'Break Status');
                        } else {
                            toastr.info('You are now on break.', 'Break Status');
                        }

                        // Update break time display
                        if (data.total_break_duration) {
                            document.getElementById('break-time').textContent = data.total_break_duration;
                        } else if (data.attendance && data.attendance.formatted_break_duration) {
                            document.getElementById('break-time').textContent = data.attendance
                            .formatted_break_duration;
                        }

                    } else {
                        showAttendanceError(data.message);
                        // Restore original text on error
                        breakBtn.innerHTML = originalText;
                    }

                    // Re-enable button
                    breakBtn.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while managing break', 'Network Error');

                    // Re-enable button and restore text
                    breakBtn.disabled = false;
                    breakBtn.innerHTML = originalText;
                });
        }

        // Utility function to get current break status from button
        function getCurrentBreakStatus() {
            const breakBtn = document.getElementById('break-btn');
            const buttonText = breakBtn.textContent.trim();
            return buttonText.includes('End Break');
        }

        function updateBreakButton() {
            const breakBtn = document.getElementById('break-btn');

            if (isOnBreak) {
                breakBtn.className = 'btn btn-success';
                breakBtn.innerHTML = '<i class="fa fa-play me-2"></i><span id="break-text">End Break</span>';
            } else {
                breakBtn.className = 'btn btn-warning';
                breakBtn.innerHTML = '<i class="fa fa-pause me-2"></i><span id="break-text">Start Break</span>';
            }
        }
    </script>

    <!-- Include Attendance JavaScript -->
    {{-- <script src="{{ asset('js/attendance.js') }}"></script> --}}
@endsection

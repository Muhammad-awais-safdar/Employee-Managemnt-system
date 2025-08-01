@extends('EmployeeManagemntsystem.Layout.App')

@section('content')
<div class="row">
    <div class="col-lg-8 col-xl-9">
        <!-- Breadcrumb Card -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-divide p-0 mb-0">
                            @php
                                $role = auth()->check() ? Auth::user()->getRoleNames()->first() : null;
                                $dashboardRoute = $role && Route::has($role . '.dashboard') ? route($role . '.dashboard') : route('login');
                            @endphp
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a href="{{ $dashboardRoute }}">Home</a>
                            </li>
                            <li class="breadcrumb-item fw-medium active" aria-current="page">
                                Leave Management
                            </li>
                        </ol>
                    </nav>
                    <h5 class="fw-bold mb-0">HR Leave Management</h5>
                </div>
            </div>
        </div>

        <!-- Leave Statistics Cards -->
        <div class="row">
            <div class="col-md-6 col-xl-3 d-flex">
                <div class="card flex-fill stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="avatar avtar-lg bg-warning mb-2">
                                    <i class="ti ti-clock text-white fs-3"></i>
                                </div>
                                <h6 class="fs-14 fw-semibold mb-2">Pending Reviews</h6>
                                <h4 class="fw-bold text-warning mb-1">{{ $leaveStats['pending_applications'] }}</h4>
                                <small class="text-muted">Require your attention</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 d-flex">
                <div class="card flex-fill stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="avatar avtar-lg bg-success mb-2">
                                    <i class="ti ti-check text-white fs-3"></i>
                                </div>
                                <h6 class="fs-14 fw-semibold mb-2">Approved This Month</h6>
                                <h4 class="fw-bold text-success mb-1">{{ $leaveStats['approved_this_month'] }}</h4>
                                <small class="text-muted">{{ date('M Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 d-flex">
                <div class="card flex-fill stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="avatar avtar-lg bg-info mb-2">
                                    <i class="ti ti-calendar-stats text-white fs-3"></i>
                                </div>
                                <h6 class="fs-14 fw-semibold mb-2">Leave Days Used</h6>
                                <h4 class="fw-bold text-info mb-1">{{ $leaveStats['total_leave_days_used'] }}</h4>
                                <small class="text-muted">This year</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 d-flex">
                <div class="card flex-fill stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="avatar avtar-lg bg-primary mb-2">
                                    <i class="ti ti-users text-white fs-3"></i>
                                </div>
                                <h6 class="fs-14 fw-semibold mb-2">On Leave Today</h6>
                                <h4 class="fw-bold text-primary mb-1">{{ $leaveStats['employees_on_leave_today'] }}</h4>
                                <small class="text-muted">Team members</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Priority Actions for HR -->
        @if($pendingLeaves->count() > 0)
        <div class="card mb-4 priority-card">
            <div class="card-header bg-gradient-warning text-white border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar avtar-md bg-white bg-opacity-20 text-white me-3">
                            <i class="ti ti-alert-triangle"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 text-white">Priority: Pending Leave Applications</h5>
                            <small class="text-white-50">{{ $pendingLeaves->count() }} applications need HR review</small>
                        </div>
                    </div>
                    <div class="badge bg-white text-warning fs-6">
                        {{ $pendingLeaves->count() }}
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 fw-semibold">Employee</th>
                                <th class="border-0 fw-semibold">Department</th>
                                <th class="border-0 fw-semibold">Leave Type</th>
                                <th class="border-0 fw-semibold">Period</th>
                                <th class="border-0 fw-semibold">Days</th>
                                <th class="border-0 fw-semibold">Applied</th>
                                <th class="border-0 fw-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingLeaves as $leave)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avtar-sm bg-primary bg-opacity-10 text-primary me-2">
                                            <i class="ti ti-user"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $leave->user->name }}</div>
                                            <small class="text-muted">ID: {{ $leave->user->employee_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($leave->user->department)
                                        <span class="badge bg-secondary">{{ $leave->user->department->name }}</span>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $leave->leaveType->code }}</span>
                                    <div class="small text-muted">{{ $leave->leaveType->name }}</div>
                                </td>
                                <td>
                                    <div>{{ $leave->date_range }}</div>
                                    @if($leave->emergency_leave)
                                        <span class="badge bg-danger badge-sm">Emergency</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-primary">{{ $leave->total_days }}</strong>
                                    <small class="text-muted">{{ $leave->total_days == 1 ? 'day' : 'days' }}</small>
                                </td>
                                <td>
                                    <div>{{ $leave->applied_at->format('M d') }}</div>
                                    <small class="text-muted">{{ $leave->applied_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-success" onclick="quickApprove('{{ $leave->id }}')">
                                            <i class="ti ti-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="quickReject('{{ $leave->id }}')">
                                            <i class="ti ti-x"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('{{ $leave->id }}')">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Search and Filter Section -->
        <div class="card mb-4">
            <div class="card-header bg-light border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Search & Filter Leave Applications</h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="resetFilters()">
                        <i class="ti ti-refresh me-1"></i>Reset
                    </button>    
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('HR.leave.index') }}" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Leave Type</label>
                        <select name="leave_type_id" class="form-select">
                            <option value="">All Types</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ $leaveTypeId == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }} ({{ $type->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>  
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="ti ti-search me-1"></i>Search
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="exportData()">
                                <i class="ti ti-download"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- All Leave Applications -->
        <div class="card leave-applications-card">
            <div class="card-header bg-light border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar avtar-md bg-secondary bg-opacity-10 text-secondary me-3">
                            <i class="ti ti-list"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">All Leave Applications</h5>
                            <small class="text-muted">{{ $allLeaves->total() }} total records found</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($allLeaves->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 fw-semibold">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th class="border-0 fw-semibold">Employee</th>
                                <th class="border-0 fw-semibold">Leave Details</th>
                                <th class="border-0 fw-semibold">Period & Duration</th>
                                <th class="border-0 fw-semibold">Status</th>
                                <th class="border-0 fw-semibold">Applied On</th>
                                <th class="border-0 fw-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allLeaves as $leave)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input leave-checkbox" type="checkbox" value="{{ $leave->id }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avtar-sm bg-primary bg-opacity-10 text-primary me-2">
                                            <i class="ti ti-user"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $leave->user->name }}</div>
                                            <small class="text-muted">
                                                ID: {{ $leave->user->employee_id }}
                                                @if($leave->user->department)
                                                    • {{ $leave->user->department->name }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-start">
                                        <span class="badge bg-primary me-2">{{ $leave->leaveType->code }}</span>
                                        <div>
                                            <div class="fw-semibold">{{ $leave->leaveType->name }}</div>
                                            <small class="text-muted">App ID: {{ $leave->application_id }}</small>
                                            @if($leave->emergency_leave)
                                                <span class="badge bg-danger badge-sm">Emergency</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $leave->date_range }}</div>
                                    <small class="text-muted">
                                        <strong>{{ $leave->total_days }}</strong> {{ $leave->total_days == 1 ? 'day' : 'days' }}
                                        • {{ $leave->duration_label ?? 'Full day' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge {{ $leave->status_badge_class }}">
                                        {{ $leave->status_label }}
                                    </span>
                                    @if($leave->approver && in_array($leave->status, ['approved', 'rejected']))
                                        <div class="small text-muted mt-1">by {{ $leave->approver->name }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $leave->applied_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $leave->applied_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="viewDetails('{{ $leave->id }}')">
                                                    <i class="ti ti-eye me-2"></i>View Details
                                                </a>
                                            </li>
                                            @if($leave->status === 'pending')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-success" href="#" onclick="quickApprove('{{ $leave->id }}')">
                                                    <i class="ti ti-check me-2"></i>Quick Approve
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="quickReject('{{ $leave->id }}')">
                                                    <i class="ti ti-x me-2"></i>Reject
                                                </a>
                                            </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="sendMessage('{{ $leave->user->id }}')">
                                                    <i class="ti ti-message me-2"></i>Message Employee
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-light border-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-outline-success" onclick="bulkApprove()" disabled id="bulkApproveBtn">
                            <i class="ti ti-check me-1"></i>Bulk Approve
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="bulkReject()" disabled id="bulkRejectBtn">
                            <i class="ti ti-x me-1"></i>Bulk Reject
                        </button>
                    </div>
                    <div>
                        {{ $allLeaves->withQueryString()->links() }}
                    </div>
                </div>
                @else
                <div class="text-center py-5">
                    <div class="empty-state">
                        <div class="empty-icon mb-3">
                            <i class="ti ti-calendar-off text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-1">No leave applications found</h6>
                        <p class="text-muted small mb-0">Applications will appear here when employees submit them</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-lg-4 col-xl-3">
        <!-- Quick HR Actions -->
        <div class="card mb-4">
            <div class="card-header bg-light border-0">
                <h6 class="mb-0">HR Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" onclick="exportEmployeeReport()">
                        <i class="ti ti-download me-2"></i>Employee Leave Report
                    </button>
                    <button class="btn btn-outline-primary" onclick="viewTeamCalendar()">
                        <i class="ti ti-calendar me-2"></i>Team Leave Calendar
                    </button>
                    <button class="btn btn-outline-info" onclick="leaveBalanceReport()">
                        <i class="ti ti-chart-bar me-2"></i>Balance Report
                    </button>
                    <button class="btn btn-outline-secondary" onclick="leavePolicy()">
                        <i class="ti ti-file-text me-2"></i>Leave Policies
                    </button>
                </div>
            </div>
        </div>

        <!-- Leave Insights -->
        <div class="card mb-4">
            <div class="card-header bg-light border-0">
                <h6 class="mb-0">Leave Insights</h6>
            </div>
            <div class="card-body">
                <div class="insight-item mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Peak Leave Period</small>
                        <small class="fw-semibold">December</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-info" style="width: 78%"></div>
                    </div>
                </div>
                <div class="insight-item mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Most Used Leave</small>
                        <small class="fw-semibold">Annual Leave</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success" style="width: 65%"></div>
                    </div>
                </div>
                <div class="insight-item">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Approval Rate</small>
                        <small class="fw-semibold text-success">94%</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success" style="width: 94%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent HR Activity -->
        <div class="card">
            <div class="card-header bg-light border-0">
                <h6 class="mb-0">Recent HR Activity</h6>
            </div>
            <div class="card-body">
                <div class="activity-timeline">
                    @if($pendingLeaves->count() > 0)
                        @foreach($pendingLeaves->take(3) as $leave)
                        <div class="activity-item d-flex align-items-start mb-3">
                            <div class="activity-dot bg-warning"></div>
                            <div class="activity-content ms-3">
                                <div class="activity-title">{{ $leave->user->name }}</div>
                                <div class="activity-desc">Requested {{ $leave->leaveType->name }}</div>
                                <div class="activity-time">{{ $leave->applied_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="ti ti-clock" style="font-size: 2rem; opacity: 0.5;"></i>
                            <p class="mt-2 mb-0 small">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-card {
    transition: all 0.3s ease;
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.6);
    border-left: 4px solid #3E007C;
}

.stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.priority-card {
    border-radius: 16px;
    border: 1px solid rgba(245, 158, 11, 0.3);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.1);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.leave-applications-card {
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.6);
}

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
    background: #e5e7eb;
}

.activity-item {
    position: relative;
}

.activity-dot {
    position: absolute;
    left: -24px;
    top: 4px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.activity-title {
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
}

.activity-desc {
    color: #6b7280;
    font-size: 0.8125rem;
}

.activity-time {
    color: #9ca3af;
    font-size: 0.75rem;
}

.insight-item .progress-sm {
    height: 4px;
}

.empty-state {
    padding: 3rem 1rem;
}

.empty-icon {
    opacity: 0.6;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .d-flex.gap-1 {
        flex-direction: column;
        gap: 0.25rem !important;
    }
    
    .d-flex.gap-1 .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>

<script>
// Checkbox management
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.leave-checkbox');
    const bulkButtons = document.querySelectorAll('#bulkApproveBtn, #bulkRejectBtn');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    
    bulkButtons.forEach(btn => {
        btn.disabled = !this.checked;
    });
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('leave-checkbox')) {
        const checkedBoxes = document.querySelectorAll('.leave-checkbox:checked');
        const bulkButtons = document.querySelectorAll('#bulkApproveBtn, #bulkRejectBtn');
        
        bulkButtons.forEach(btn => {
            btn.disabled = checkedBoxes.length === 0;
        });
    }
});

// Quick approve function
async function quickApprove(leaveId) {
    try {
        const response = await fetch(`{{ route('HR.leave.index') }}/${leaveId}/review`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                action: 'approve',
                admin_notes: 'Quick approved by HR'
            })
        });

        const data = await response.json();

        if (data.success) {
            toastr.success(data.message, 'Approved');
            setTimeout(() => location.reload(), 1500);
        } else {
            toastr.error(data.message, 'Error');
        }
    } catch (error) {
        toastr.error('An error occurred', 'Error');
    }
}

// Quick reject function
async function quickReject(leaveId) {
    const { value: reason } = await Swal.fire({
        title: 'Reject Leave Application',
        input: 'textarea',
        inputLabel: 'Rejection reason (required)',
        inputPlaceholder: 'Please provide a reason for rejection...',
        showCancelButton: true,
        confirmButtonText: 'Reject',
        confirmButtonColor: '#dc3545',
        inputValidator: (value) => {
            if (!value) {
                return 'Please provide a reason for rejection';
            }
        }
    });

    if (reason) {
        try {
            const response = await fetch(`{{ route('HR.leave.index') }}/${leaveId}/review`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    action: 'reject',
                    admin_notes: reason
                })
            });

            const data = await response.json();

            if (data.success) {
                toastr.success(data.message, 'Rejected');
                setTimeout(() => location.reload(), 1500);
            } else {
                toastr.error(data.message, 'Error');
            }
        } catch (error) {
            toastr.error('An error occurred', 'Error');
        }
    }
}

// View details function
async function viewDetails(leaveId) {
    try {
        const response = await fetch(`{{ route('HR.leave.index') }}/${leaveId}`);
        
        if (!response.ok) {
            throw new Error('Failed to fetch leave details');
        }
        
        // For now, show a detailed modal with leave information
        // In a real implementation, this would fetch actual leave details
        Swal.fire({
            title: 'Leave Application Details',
            html: `
                <div class="text-start">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <small><i class="ti ti-info-circle me-1"></i>Loading leave details for ID: ${leaveId}</small>
                            </div>
                            <p>This modal would display:</p>
                            <ul class="list-unstyled">
                                <li><i class="ti ti-user me-2"></i>Employee information</li>
                                <li><i class="ti ti-calendar me-2"></i>Leave dates and duration</li>
                                <li><i class="ti ti-file-text me-2"></i>Application reason</li>
                                <li><i class="ti ti-clock me-2"></i>Application timeline</li>
                                <li><i class="ti ti-message me-2"></i>Admin notes and comments</li>
                                <li><i class="ti ti-chart-line me-2"></i>Leave balance impact</li>
                            </ul>
                        </div>
                    </div>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Close',
            width: '600px'
        });
        
    } catch (error) {
        Swal.fire({
            title: 'Error',
            text: 'Failed to load leave details',
            icon: 'error'
        });
    }
}

// Bulk operations
async function bulkApprove() {
    const selected = document.querySelectorAll('.leave-checkbox:checked');
    if (selected.length === 0) return;
    
    const result = await Swal.fire({
        title: `Approve ${selected.length} applications?`,
        text: "This will approve all selected leave applications",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Yes, approve all'
    });
    
    if (result.isConfirmed) {
        const leaveIds = Array.from(selected).map(checkbox => checkbox.value);
        
        try {
            const response = await fetch('{{ route("HR.leave.bulk-approve") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    leave_ids: leaveIds,
                    admin_notes: 'Bulk approved by HR'
                })
            });

            const data = await response.json();

            if (data.success) {
                toastr.success(data.message, 'Success');
                setTimeout(() => location.reload(), 1500);
            } else {
                toastr.error(data.message, 'Error');
            }
        } catch (error) {
            toastr.error('An error occurred during bulk approval', 'Error');
        }
    }
}

async function bulkReject() {
    const selected = document.querySelectorAll('.leave-checkbox:checked');
    if (selected.length === 0) return;
    
    const { value: reason } = await Swal.fire({
        title: `Reject ${selected.length} applications?`,
        input: 'textarea',
        inputLabel: 'Rejection reason (required)',
        inputPlaceholder: 'Please provide a reason for bulk rejection...',
        showCancelButton: true,
        confirmButtonText: 'Reject All',
        confirmButtonColor: '#dc3545',
        inputValidator: (value) => {
            if (!value) {
                return 'Please provide a reason for rejection';
            }
        }
    });

    if (reason) {
        const leaveIds = Array.from(selected).map(checkbox => checkbox.value);
        
        try {
            const response = await fetch('{{ route("HR.leave.bulk-reject") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    leave_ids: leaveIds,
                    admin_notes: reason
                })
            });

            const data = await response.json();

            if (data.success) {
                toastr.success(data.message, 'Success');
                setTimeout(() => location.reload(), 1500);
            } else {
                toastr.error(data.message, 'Error');
            }
        } catch (error) {
            toastr.error('An error occurred during bulk rejection', 'Error');
        }
    }
}

// Other functions
function resetFilters() {
    window.location.href = '{{ route("HR.leave.index") }}';
}

function exportData() {
    const params = new URLSearchParams(window.location.search);
    const exportUrl = '{{ route("HR.leave.export") }}?' + params.toString();
    window.open(exportUrl, '_blank');
    toastr.success('Export started successfully', 'Success');
}

function exportEmployeeReport() {
    const exportUrl = '{{ route("HR.leave.export.employee") }}';
    window.open(exportUrl, '_blank');
    toastr.success('Employee report export started', 'Success');
}

function viewTeamCalendar() {
    // Navigate to the Team Leave Calendar page
    window.location.href = '{{ route("HR.leave.calendar") }}';
}

function leaveBalanceReport() {
    const exportUrl = '{{ route("HR.leave.export.balance") }}';
    window.open(exportUrl, '_blank');
    toastr.success('Balance report export started', 'Success');
}

function leavePolicy() {
    window.open('{{ route("HR.leave-types.index") }}', '_blank');
    toastr.info('Opening leave policies management', 'Info');
}

function sendMessage(userId) {
    Swal.fire({
        title: 'Send Message to Employee',
        html: `
            <div class="text-start">
                <label class="form-label">Message:</label>
                <textarea id="messageText" class="form-control" rows="4" placeholder="Type your message here..."></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Send Message',
        preConfirm: () => {
            const message = document.getElementById('messageText').value;
            if (!message) {
                Swal.showValidationMessage('Please enter a message');
                return false;
            }
            return message;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // In a real implementation, this would send via notification system
            toastr.success('Message functionality would integrate with notification system', 'Info');
        }
    });
}

// Show success/error messages
@if(session('success'))
    toastr.success("{{ session('success') }}", 'Success');
@endif

@if(session('error'))
    toastr.error("{{ session('error') }}", 'Error');
@endif
</script>
@endsection
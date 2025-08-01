@extends('EmployeeManagemntsystem.Layout.employee')

@section('title', 'Leave Management')

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
                        <li class="breadcrumb-item active fw-medium" aria-current="page">Leave Management</li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1">Leave Management</h4>
                        <p class="text-muted mb-0">Apply for leave and track your leave balance</p>
                    </div>
                    <div class="leave-actions">
                        <a href="{{ route('Employee.leave.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Apply for Leave
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Balance Cards -->
        <div class="row">
            @forelse($leaveBalances as $balance)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card balance-card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="balance-icon bg-primary bg-opacity-10 text-primary">
                                <i class="ti ti-calendar-time"></i>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">{{ $balance['leave_type_code'] }}</span>
                            </div>
                        </div>
                        <h6 class="balance-title mb-2">{{ $balance['leave_type'] }}</h6>
                        <div class="balance-stats">
                            <div class="row g-2 text-center">
                                <div class="col-6">
                                    <div class="stat-box">
                                        <h4 class="stat-number text-success mb-0">{{ $balance['available'] }}</h4>
                                        <small class="stat-label text-muted">Available</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box">
                                        <h4 class="stat-number text-primary mb-0">{{ $balance['used'] }}</h4>
                                        <small class="stat-label text-muted">Used</small>
                                    </div>
                                </div>
                            </div>
                            @if($balance['pending'] > 0)
                            <div class="pending-info mt-2">
                                <small class="text-warning">
                                    <i class="ti ti-clock me-1"></i>{{ $balance['pending'] }} days pending approval
                                </small>
                            </div>
                            @endif
                        </div>
                        <div class="progress mt-3" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ $balance['usage_percentage'] }}%">
                            </div>
                        </div>
                        <small class="text-muted mt-1">{{ $balance['usage_percentage'] }}% used</small>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    No leave types available. Please contact your HR department.
                </div>
            </div>
            @endforelse
        </div>

        <!-- Upcoming Leaves -->
        @if($upcomingLeaves->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card upcoming-leaves-card border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <div class="icon-wrapper bg-info bg-opacity-10 text-info">
                                    <i class="ti ti-calendar-event"></i>
                                </div>
                            </div>
                            <h5 class="mb-0">Upcoming Approved Leaves</h5>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-semibold">Leave Type</th>
                                        <th class="border-0 fw-semibold">Dates</th>
                                        <th class="border-0 fw-semibold">Days</th>
                                        <th class="border-0 fw-semibold">Starts In</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingLeaves as $leave)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $leave->leaveType->code }}</span>
                                            {{ $leave->leaveType->name }}
                                        </td>
                                        <td>{{ $leave->date_range }}</td>
                                        <td>{{ $leave->total_days }} {{ $leave->total_days == 1 ? 'day' : 'days' }}</td>
                                        <td>
                                            <span class="text-success fw-semibold">
                                                {{ $leave->days_until_start }} {{ $leave->days_until_start == 1 ? 'day' : 'days' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Leave Applications -->
        <div class="row">
            <div class="col-12">
                <div class="card recent-leaves-card border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <div class="icon-wrapper bg-secondary bg-opacity-10 text-secondary">
                                        <i class="ti ti-history"></i>
                                    </div>
                                </div>
                                <h5 class="mb-0">Recent Leave Applications</h5>
                            </div>
                            <small class="text-muted">Last 10 applications</small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-semibold">Application ID</th>
                                        <th class="border-0 fw-semibold">Leave Type</th>
                                        <th class="border-0 fw-semibold">Dates</th>
                                        <th class="border-0 fw-semibold">Days</th>
                                        <th class="border-0 fw-semibold">Status</th>
                                        <th class="border-0 fw-semibold">Applied On</th>
                                        <th class="border-0 fw-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($userLeaves as $leave)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $leave->application_id }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $leave->leaveType->code }}</span>
                                            {{ $leave->leaveType->name }}
                                        </td>
                                        <td>{{ $leave->date_range }}</td>
                                        <td>{{ $leave->total_days }} {{ $leave->total_days == 1 ? 'day' : 'days' }}</td>
                                        <td>
                                            <span class="badge {{ $leave->status_badge_class }}">
                                                {{ $leave->status_label }}
                                            </span>
                                        </td>
                                        <td>{{ $leave->applied_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="ti ti-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('Employee.leave.show', $leave) }}">
                                                            <i class="ti ti-eye me-2"></i>View Details
                                                        </a>
                                                    </li>
                                                    @if($leave->canBeEdited())
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('Employee.leave.edit', $leave) }}">
                                                            <i class="ti ti-edit me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    @endif
                                                    @if($leave->canBeCancelled())
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="cancelLeave('{{ $leave->id }}')">
                                                            <i class="ti ti-x me-2"></i>Cancel
                                                        </a>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="empty-state">
                                                <div class="empty-icon mb-3">
                                                    <i class="ti ti-calendar-off text-muted" style="font-size: 3rem;"></i>
                                                </div>
                                                <h6 class="text-muted mb-1">No leave applications found</h6>
                                                <p class="text-muted small mb-0">Start by applying for your first leave</p>
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

    <style>
        .balance-card {
            border-radius: 16px;
            transition: all 0.3s ease;
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        .balance-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .balance-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .balance-title {
            color: #2c3e50;
            font-weight: 600;
        }

        .stat-box {
            padding: 0.5rem;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .upcoming-leaves-card,
        .recent-leaves-card {
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

        .empty-state {
            padding: 3rem 1rem;
        }

        .empty-icon {
            opacity: 0.6;
        }

        .pending-info {
            padding: 0.5rem;
            background: rgba(255, 193, 7, 0.1);
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .balance-card .card-body {
                padding: 1rem;
            }
            
            .stat-number {
                font-size: 1.25rem;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
        }
    </style>

    <script>
        function cancelLeave(leaveId) {
            Swal.fire({
                title: 'Cancel Leave Application?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ route('Employee.leave.index') }}/${leaveId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message, 'Success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            toastr.error(data.message, 'Error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('An error occurred while cancelling the leave application', 'Network Error');
                    });
                }
            });
        }

        // Show success message if leave was created/updated
        @if(session('success'))
            toastr.success("{{ session('success') }}", 'Success');
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}", 'Error');
        @endif
    </script>
@endsection
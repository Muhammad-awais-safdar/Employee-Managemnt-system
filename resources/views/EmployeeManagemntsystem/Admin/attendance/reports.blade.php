@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'Attendance Reports')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="page-title">Attendance Reports</h3>
                <p class="text-muted">Comprehensive attendance analytics and insights</p>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-success" onclick="exportReport()">
                        <i class="fa fa-download me-2"></i>Export Excel
                    </button>
                    <button type="button" class="btn btn-info" onclick="printReport()">
                        <i class="fa fa-print me-2"></i>Print Report
                    </button>
                    <a href="{{ route(auth()->user()->getRoleNames()->first() . '.attendance.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Report Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route(auth()->user()->getRoleNames()->first() . '.attendance.reports') }}" id="reportFilterForm">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Department</label>
                                    <select class="form-control" name="department_id">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if(auth()->user()->hasRole('superAdmin') && $companies)
                            <div class="col-md-3">
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
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search me-2"></i>Generate Report
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                                <i class="fa fa-refresh me-2"></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row">
        <div class="col-md-2">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="mb-1">{{ $summaryData['total_employees'] }}</h3>
                        <p class="mb-0">Total Employees</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="mb-1">{{ $summaryData['total_present_days'] }}</h3>
                        <p class="mb-0">Present Days</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card bg-danger text-white">
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="mb-1">{{ $summaryData['total_absent_days'] }}</h3>
                        <p class="mb-0">Absent Days</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="mb-1">{{ $summaryData['total_late_days'] }}</h3>
                        <p class="mb-0">Late Days</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="mb-1">{{ floor($summaryData['total_working_hours'] / 60) }}</h3>
                        <p class="mb-0">Total Hours</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card bg-purple text-white">
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="mb-1">{{ $summaryData['average_attendance_rate'] }}%</h3>
                        <p class="mb-0">Avg. Attendance</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Report Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Employee-wise Attendance Report</h5>
                    <small class="text-muted">Period: {{ $dateFrom }} to {{ $dateTo }}</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="reportTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Total Days</th>
                                    <th>Present</th>
                                    <th>Late</th>
                                    <th>Half Days</th>
                                    <th>Absent</th>
                                    <th>Unpaid Leave</th>
                                    <th>Working Hours</th>
                                    <th>Overtime</th>
                                    <th>Attendance %</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceData as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-title bg-primary rounded-circle">
                                                    {{ substr($data['employee']->name, 0, 2) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $data['employee']->name }}</h6>
                                                <small class="text-muted">{{ $data['employee']->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $data['employee']->department->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $data['total_days'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $data['present_days'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $data['late_days'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $data['half_days'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $data['absent_days'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-dark">{{ $data['unpaid_leave_days'] }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ floor($data['total_hours'] / 60) }}h {{ $data['total_hours'] % 60 }}m</strong>
                                    </td>
                                    <td>
                                        @if($data['total_overtime_hours'] > 0)
                                            <span class="text-success font-weight-bold">+{{ $data['total_overtime_hours'] }}h</span>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $data['attendance_percentage'] >= 90 ? 'bg-success' : ($data['attendance_percentage'] >= 75 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $data['attendance_percentage'] }}%">
                                                {{ $data['attendance_percentage'] }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewEmployeeDetail({{ $data['employee']->id }})">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="exportEmployeeReport({{ $data['employee']->id }})">
                                                <i class="fa fa-download"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="13" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fa fa-chart-bar fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No attendance data found</h5>
                                            <p class="text-muted">Try adjusting your filters or date range</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($attendanceData) > 0)
                            <tfoot class="table-light">
                                <tr class="font-weight-bold">
                                    <td colspan="3"><strong>TOTALS</strong></td>
                                    <td><span class="badge bg-secondary">{{ array_sum(array_column($attendanceData, 'total_days')) }}</span></td>
                                    <td><span class="badge bg-success">{{ $summaryData['total_present_days'] }}</span></td>
                                    <td><span class="badge bg-warning">{{ $summaryData['total_late_days'] }}</span></td>
                                    <td><span class="badge bg-info">{{ $summaryData['total_half_days'] }}</span></td>
                                    <td><span class="badge bg-danger">{{ $summaryData['total_absent_days'] }}</span></td>
                                    <td><span class="badge bg-dark">{{ array_sum(array_column($attendanceData, 'unpaid_leave_days')) }}</span></td>
                                    <td><strong>{{ floor($summaryData['total_working_hours'] / 60) }}h {{ $summaryData['total_working_hours'] % 60 }}m</strong></td>
                                    <td><strong class="text-success">+{{ $summaryData['total_overtime_hours'] }}h</strong></td>
                                    <td><strong>{{ $summaryData['average_attendance_rate'] }}%</strong></td>
                                    <td>--</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Attendance Status Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Department-wise Attendance Rate</h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" height="300"></canvas>
                </div>
            </div>
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
    transform: translateY(-2px);
}

.bg-purple {
    background-color: #6f42c1 !important;
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

.progress {
    border-radius: 10px;
}

.table thead th {
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.075);
}

@media print {
    .btn-group, .page-header .col-md-6:last-child, .card-header {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 12px;
    }
}

@media (max-width: 768px) {
    .stat-card h3 {
        font-size: 1.5rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Reset filters function
function resetFilters() {
    const form = document.getElementById('reportFilterForm');
    form.reset();
    
    // Set default dates (current month)
    const now = new Date();
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
    const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    
    form.querySelector('input[name="date_from"]').value = startOfMonth.toISOString().split('T')[0];
    form.querySelector('input[name="date_to"]').value = endOfMonth.toISOString().split('T')[0];
}

// Export report function
function exportReport() {
    const params = new URLSearchParams(window.location.search);
    const exportUrl = "{{ route(auth()->user()->getRoleNames()->first() . '.attendance.export') }}?" + params.toString();
    
    toastr.info('Your attendance report is being prepared for download...', 'Preparing Export');
    
    // In real implementation, this would trigger a download
    window.location.href = exportUrl;
}

// Print report function
function printReport() {
    window.print();
}

// View employee detail function
function viewEmployeeDetail(employeeId) {
    toastr.info('Detailed view for employee ID: ' + employeeId + ' will be implemented here', 'Employee Details');
}

// Export individual employee report
function exportEmployeeReport(employeeId) {
    toastr.info('Individual report for employee ID: ' + employeeId + ' is being prepared...', 'Exporting Employee Report');
}

// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    // Attendance Status Distribution Chart
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(attendanceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Late', 'Half Day', 'Absent', 'Unpaid Leave'],
            datasets: [{
                data: [
                    {{ $summaryData['total_present_days'] }},
                    {{ $summaryData['total_late_days'] }},
                    {{ $summaryData['total_half_days'] }},
                    {{ $summaryData['total_absent_days'] }},
                    {{ array_sum(array_column($attendanceData, 'unpaid_leave_days')) }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#17a2b8',
                    '#dc3545',
                    '#6c757d'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Department-wise Attendance Rate Chart
    const departmentCtx = document.getElementById('departmentChart').getContext('2d');
    
    // Calculate department-wise data
    const departmentData = {};
    @foreach($attendanceData as $data)
        const deptName = "{{ $data['employee']->department->name ?? 'N/A' }}";
        if (!departmentData[deptName]) {
            departmentData[deptName] = { total: 0, percentage: 0, count: 0 };
        }
        departmentData[deptName].total += {{ $data['attendance_percentage'] }};
        departmentData[deptName].count += 1;
    @endforeach
    
    const deptLabels = Object.keys(departmentData);
    const deptPercentages = deptLabels.map(dept => departmentData[dept].count > 0 ? (departmentData[dept].total / departmentData[dept].count).toFixed(1) : 0);
    
    new Chart(departmentCtx, {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Attendance Rate (%)',
                data: deptPercentages,
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});

// Auto-refresh data every 5 minutes
setInterval(function() {
    if (confirm('Refresh attendance data?')) {
        location.reload();
    }
}, 300000); // 5 minutes
</script>
@endsection
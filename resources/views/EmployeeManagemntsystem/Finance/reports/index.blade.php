@extends('EmployeeManagemntsystem.Layout.App')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-divide p-0 mb-0">
                                @php
                                    $role = auth()->check() ? Auth::user()->getRoleNames()->first() : null;
                                    $dashboardRoute = $role && Route::has($role . '.dashboard') ? route($role . '.dashboard') : route('login');
                                @endphp
                                <li class="breadcrumb-item d-flex align-items-center"><a href="{{ $dashboardRoute }}">Home</a></li>
                                <li class="breadcrumb-item d-flex align-items-center"><a href="{{ route('Finance.dashboard') }}">Finance</a></li>
                                <li class="breadcrumb-item fw-medium active" aria-current="page">Financial Reports</li>
                            </ol>
                        </nav>
                        <h5 class="fw-bold mb-0">Financial Reports & Analytics</h5>
                    </div>
                </div>
            </div>

            <!-- Report Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0">Report Actions</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                                <i class="ti ti-file-plus me-1"></i>Generate Custom Report
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown">
                                    <i class="ti ti-download me-1"></i>Quick Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('Finance.reports.export', ['report_type' => 'monthly']) }}">Monthly Report</a></li>
                                    <li><a class="dropdown-item" href="{{ route('Finance.reports.export', ['report_type' => 'quarterly']) }}">Quarterly Report</a></li>
                                    <li><a class="dropdown-item" href="{{ route('Finance.reports.export', ['report_type' => 'yearly']) }}">Yearly Report</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="avatar avtar-lg bg-success mb-2">
                                        <i class="ti ti-trending-up fs-24"></i>
                                    </div>
                                    <h6 class="fs-14 fw-semibold mb-1">Monthly Revenue</h6>
                                    <h4 class="fw-bold mb-0">₹{{ number_format(750000) }}</h4>
                                    <small class="text-success"><i class="ti ti-arrow-up"></i> +12.5% from last month</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="avatar avtar-lg bg-warning mb-2">
                                        <i class="ti ti-trending-down fs-24"></i>
                                    </div>
                                    <h6 class="fs-14 fw-semibold mb-1">Monthly Expenses</h6>
                                    <h4 class="fw-bold mb-0">₹{{ number_format(450000) }}</h4>
                                    <small class="text-danger"><i class="ti ti-arrow-down"></i> -5.2% from last month</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="avatar avtar-lg bg-primary mb-2">
                                        <i class="ti ti-calculator fs-24"></i>
                                    </div>
                                    <h6 class="fs-14 fw-semibold mb-1">Net Profit</h6>
                                    <h4 class="fw-bold mb-0">₹{{ number_format(300000) }}</h4>
                                    <small class="text-success"><i class="ti ti-arrow-up"></i> +18.7% from last month</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Financial Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Monthly Financial Summary</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Payroll</th>
                                    <th>Expenses</th>
                                    <th>Net</th>
                                    <th>Variance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData['monthly_summary'] as $month)
                                    <tr>
                                        <td class="fw-semibold">{{ $month['month'] }}</td>
                                        <td>₹{{ number_format($month['payroll']) }}</td>
                                        <td>₹{{ number_format($month['expenses']) }}</td>
                                        <td class="fw-bold">₹{{ number_format($month['net']) }}</td>
                                        <td>
                                            @php
                                                $variance = rand(-15, 25);
                                            @endphp
                                            <span class="badge {{ $variance >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $variance >= 0 ? '+' : '' }}{{ $variance }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Quarterly Summary -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="fw-bold mb-0">Quarterly Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Quarter</th>
                                            <th>Payroll</th>
                                            <th>Expenses</th>
                                            <th>Net</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData['quarterly_summary'] as $quarter)
                                            <tr>
                                                <td class="fw-semibold">{{ $quarter['quarter'] }}</td>
                                                <td>₹{{ number_format($quarter['payroll']) }}</td>
                                                <td>₹{{ number_format($quarter['expenses']) }}</td>
                                                <td class="fw-bold">₹{{ number_format($quarter['net']) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Yearly Summary -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="fw-bold mb-0">Yearly Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Year</th>
                                            <th>Payroll</th>
                                            <th>Expenses</th>
                                            <th>Net</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData['yearly_summary'] as $year)
                                            <tr>
                                                <td class="fw-semibold">{{ $year['year'] }}</td>
                                                <td>₹{{ number_format($year['payroll']) }}</td>
                                                <td>₹{{ number_format($year['expenses']) }}</td>
                                                <td class="fw-bold">₹{{ number_format($year['net']) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="fw-bold mb-0">Financial Trends</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="financialTrendsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="fw-bold mb-0">Expense Breakdown</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="expenseBreakdownChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Report Activities -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Recent Report Activities</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Monthly Financial Report - July 2025</h6>
                                <small class="text-muted">Generated by {{ Auth::user()->name }}</small>
                            </div>
                            <div>
                                <span class="badge bg-success">Completed</span>
                                <small class="text-muted ms-2">2 hours ago</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Payroll Summary Report - Q2 2025</h6>
                                <small class="text-muted">Generated by Finance Team</small>
                            </div>
                            <div>
                                <span class="badge bg-success">Completed</span>
                                <small class="text-muted ms-2">1 day ago</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Expense Analysis Report</h6>
                                <small class="text-muted">Generated by {{ Auth::user()->name }}</small>
                            </div>
                            <div>
                                <span class="badge bg-warning">Processing</span>
                                <small class="text-muted ms-2">3 days ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Custom Report Modal -->
    <div class="modal fade" id="generateReportModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Custom Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="generateReportForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Report Type</label>
                                <select class="form-select" name="report_type" required>
                                    <option value="">Select Report Type</option>
                                    <option value="financial_summary">Financial Summary</option>
                                    <option value="payroll_details">Payroll Details</option>
                                    <option value="expense_analysis">Expense Analysis</option>
                                    <option value="profit_loss">Profit & Loss</option>
                                    <option value="cash_flow">Cash Flow</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Period</label>
                                <select class="form-select" name="period" required>
                                    <option value="">Select Period</option>
                                    <option value="current_month">Current Month</option>
                                    <option value="last_month">Last Month</option>
                                    <option value="current_quarter">Current Quarter</option>
                                    <option value="last_quarter">Last Quarter</option>
                                    <option value="current_year">Current Year</option>
                                    <option value="last_year">Last Year</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="customDateFields" style="display: none;">
                                <label class="form-label">From Date</label>
                                <input type="date" class="form-control" name="date_from">
                            </div>
                            <div class="col-md-6" id="customDateFieldsTo" style="display: none;">
                                <label class="form-label">To Date</label>
                                <input type="date" class="form-control" name="date_to">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Format</label>
                                <select class="form-select" name="format" required>
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                    <option value="csv">CSV</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Include</label>
                                <div class="mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="include_charts" id="includeCharts" checked>
                                        <label class="form-check-label" for="includeCharts">Charts & Graphs</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="include_details" id="includeDetails" checked>
                                        <label class="form-check-label" for="includeDetails">Detailed Breakdown</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Additional Notes</label>
                                <textarea class="form-control" name="notes" rows="3" 
                                          placeholder="Any additional notes or specific requirements..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Period selection change handler
            document.querySelector('select[name="period"]').addEventListener('change', function() {
                const customFields = document.getElementById('customDateFields');
                const customFieldsTo = document.getElementById('customDateFieldsTo');
                
                if (this.value === 'custom') {
                    customFields.style.display = 'block';
                    customFieldsTo.style.display = 'block';
                } else {
                    customFields.style.display = 'none';
                    customFieldsTo.style.display = 'none';
                }
            });

            // Generate report form submission
            document.getElementById('generateReportForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                // Simulate report generation
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>Generating...';
                
                setTimeout(() => {
                    alert('Report generated successfully! It will be downloaded shortly.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Generate Report';
                    
                    const modal = bootstrap.Modal.getInstance(document.getElementById('generateReportModal'));
                    modal.hide();
                }, 2000);
            });

            // Initialize Charts
            initializeCharts();
        });

        function initializeCharts() {
            // Financial Trends Chart
            const trendsCtx = document.getElementById('financialTrendsChart').getContext('2d');
            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: @json(collect($reportData['monthly_summary'])->pluck('month')),
                    datasets: [{
                        label: 'Payroll',
                        data: @json(collect($reportData['monthly_summary'])->pluck('payroll')),
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.1
                    }, {
                        label: 'Expenses',
                        data: @json(collect($reportData['monthly_summary'])->pluck('expenses')),
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        tension: 0.1
                    }, {
                        label: 'Net',
                        data: @json(collect($reportData['monthly_summary'])->pluck('net')),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Expense Breakdown Chart
            const expenseCtx = document.getElementById('expenseBreakdownChart').getContext('2d');
            new Chart(expenseCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Payroll', 'Office Supplies', 'Travel', 'Equipment', 'Utilities'],
                    datasets: [{
                        data: [60, 15, 10, 10, 5],
                        backgroundColor: [
                            'rgb(54, 162, 235)',
                            'rgb(255, 99, 132)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(153, 102, 255)'
                        ]
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
        }
    </script>
@endsection
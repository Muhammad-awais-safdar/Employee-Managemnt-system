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
                                <li class="breadcrumb-item fw-medium active" aria-current="page">Payroll Management</li>
                            </ol>
                        </nav>
                        <h5 class="fw-bold mb-0">Payroll Management</h5>
                    </div>
                </div>
            </div>

            <!-- Payroll Statistics -->
            <div class="row mb-4">
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="avatar avtar-lg bg-primary mb-2">
                                        <i class="ti ti-users fs-24"></i>
                                    </div>
                                    <h6 class="fs-14 fw-semibold mb-1">Total Employees</h6>
                                    <h4 class="fw-bold mb-0">{{ count($payrollData) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="avatar avtar-lg bg-success mb-2">
                                        <i class="ti ti-currency-dollar fs-24"></i>
                                    </div>
                                    <h6 class="fs-14 fw-semibold mb-1">Total Payroll</h6>
                                    <h4 class="fw-bold mb-0">₹{{ number_format($payrollData->sum('total_salary')) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="avatar avtar-lg bg-warning mb-2">
                                        <i class="ti ti-clock fs-24"></i>
                                    </div>
                                    <h6 class="fs-14 fw-semibold mb-1">Total Hours</h6>
                                    <h4 class="fw-bold mb-0">{{ $payrollData->sum('total_hours') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="avatar avtar-lg bg-info mb-2">
                                        <i class="ti ti-clock-plus fs-24"></i>
                                    </div>
                                    <h6 class="fs-14 fw-semibold mb-1">Overtime Hours</h6>
                                    <h4 class="fw-bold mb-0">{{ $payrollData->sum('overtime_hours') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payroll Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0">Payroll Actions</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" id="processPayrollBtn">
                                <i class="ti ti-calculator me-1"></i>Process Payroll
                            </button>
                            <a href="{{ route('Finance.payroll.export') }}" class="btn btn-outline-primary">
                                <i class="ti ti-download me-1"></i>Export Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payroll Data Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Employee Payroll Data</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Total Hours</th>
                                    <th>Overtime Hours</th>
                                    <th>Basic Salary</th>
                                    <th>Overtime Pay</th>
                                    <th>Total Salary</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payrollData as $record)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input employee-checkbox" 
                                                   value="{{ $record['employee']->id }}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <img src="{{ $record['employee']->profile_image ? asset('storage/' . $record['employee']->profile_image) : asset('assets/img/users/user-05.jpg') }}" 
                                                         alt="Employee" class="rounded-circle">
                                                </div>
                                                <div>
                                                    <h6 class="fs-14 mb-0">{{ $record['employee']->name }}</h6>
                                                    <small class="text-muted">{{ $record['employee']->employee_id ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-primary">
                                                {{ $record['employee']->department->name ?? 'No Department' }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($record['total_hours'], 2) }}</td>
                                        <td>{{ number_format($record['overtime_hours'], 2) }}</td>
                                        <td>
                                            <div>
                                                <span class="fw-bold">₹{{ number_format($record['basic_salary']) }}</span>
                                                @if(!$record['employee']->salary)
                                                    <small class="text-warning d-block">
                                                        <i class="ti ti-alert-triangle"></i> No salary set
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>₹{{ number_format($record['overtime_pay']) }}</td>
                                        <td class="fw-bold">₹{{ number_format($record['total_salary']) }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                                        data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="#" 
                                                           onclick="viewPayrollDetails({{ $record['employee']->id }})">
                                                            <i class="ti ti-eye me-1"></i>View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" 
                                                           onclick="generatePayslip({{ $record['employee']->id }})">
                                                            <i class="ti ti-file-text me-1"></i>Generate Payslip
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ti ti-inbox fs-48 mb-2 d-block"></i>
                                                No payroll data available
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

    <!-- Process Payroll Modal -->
    <div class="modal fade" id="processPayrollModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Process Payroll</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="processPayrollForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pay Period</label>
                            <select class="form-select" name="pay_period" required>
                                <option value="">Select Pay Period</option>
                                <option value="current_month">Current Month</option>
                                <option value="previous_month">Previous Month</option>
                                <option value="custom">Custom Period</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Selected Employees</label>
                            <div id="selectedEmployees" class="border rounded p-2 bg-light">
                                <small class="text-muted">No employees selected</small>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle me-1"></i>
                            Processing payroll will calculate salaries based on attendance and working hours.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Process Payroll</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all checkbox functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const employeeCheckboxes = document.querySelectorAll('.employee-checkbox');

            selectAllCheckbox.addEventListener('change', function() {
                employeeCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedEmployees();
            });

            employeeCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedEmployees);
            });

            // Process payroll button
            document.getElementById('processPayrollBtn').addEventListener('click', function() {
                const selectedEmployees = document.querySelectorAll('.employee-checkbox:checked');
                if (selectedEmployees.length === 0) {
                    alert('Please select at least one employee to process payroll.');
                    return;
                }
                
                const modal = new bootstrap.Modal(document.getElementById('processPayrollModal'));
                modal.show();
            });

            // Process payroll form submission
            document.getElementById('processPayrollForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const selectedEmployees = Array.from(document.querySelectorAll('.employee-checkbox:checked'))
                    .map(checkbox => checkbox.value);
                
                const formData = new FormData(this);
                formData.append('employee_ids', JSON.stringify(selectedEmployees));

                fetch('{{ route("Finance.payroll.process") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing payroll.');
                });
            });

            function updateSelectedEmployees() {
                const selectedCheckboxes = document.querySelectorAll('.employee-checkbox:checked');
                const selectedEmployeesDiv = document.getElementById('selectedEmployees');
                
                if (selectedCheckboxes.length === 0) {
                    selectedEmployeesDiv.innerHTML = '<small class="text-muted">No employees selected</small>';
                } else {
                    const employeeNames = Array.from(selectedCheckboxes).map(checkbox => {
                        const row = checkbox.closest('tr');
                        const nameCell = row.querySelector('td:nth-child(2) h6');
                        return nameCell.textContent;
                    });
                    
                    selectedEmployeesDiv.innerHTML = employeeNames.map(name => 
                        `<span class="badge badge-soft-primary me-1">${name}</span>`
                    ).join('');
                }
            }
        });

        function viewPayrollDetails(employeeId) {
            // Implement view payroll details functionality
            console.log('View payroll details for employee:', employeeId);
        }

        function generatePayslip(employeeId) {
            // Implement generate payslip functionality
            console.log('Generate payslip for employee:', employeeId);
        }
    </script>
@endsection
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
                                <li class="breadcrumb-item fw-medium active" aria-current="page">Salary Management</li>
                            </ol>
                        </nav>
                        <h5 class="fw-bold mb-0">Salary Management</h5>
                    </div>
                </div>
            </div>

            <!-- Salary Statistics -->
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
                                    <h4 class="fw-bold mb-0">{{ $employees->count() }}</h4>
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
                                    <h6 class="fs-14 fw-semibold mb-1">Average Salary</h6>
                                    <h4 class="fw-bold mb-0">₹{{ number_format($employees->whereNotNull('salary')->avg('salary') ?? 0) }}</h4>
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
                                        <i class="ti ti-trending-up fs-24"></i>
                                    </div>
                                    <h6 class="fs-14 fw-semibold mb-1">Highest Salary</h6>
                                    <h4 class="fw-bold mb-0">₹{{ number_format($employees->max('salary') ?? 0) }}</h4>
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
                                        <i class="ti ti-user-x fs-24"></i>
                                    </div>
                                    <h6 class="fs-14 fw-semibold mb-1">No Salary Set</h6>
                                    <h4 class="fw-bold mb-0">{{ $employees->whereNull('salary')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salary Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0">Salary Actions</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" id="bulkUpdateBtn">
                                <i class="ti ti-edit me-1"></i>Bulk Update Salaries
                            </button>
                            <a href="{{ route('Finance.salaries.export') }}" class="btn btn-outline-primary">
                                <i class="ti ti-download me-1"></i>Export Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salary Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form class="row g-3" id="salaryFilterForm">
                        <div class="col-md-3">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department">
                                <option value="">All Departments</option>
                                @foreach($employees->pluck('department.name')->unique()->filter() as $department)
                                    <option value="{{ $department }}">{{ $department }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role">
                                <option value="">All Roles</option>
                                <option value="Employee">Employee</option>
                                <option value="TeamLead">Team Lead</option>
                                <option value="HR">HR</option>
                                <option value="Finance">Finance</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Min Salary</label>
                            <input type="number" class="form-control" name="min_salary" placeholder="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Max Salary</label>
                            <input type="number" class="form-control" name="max_salary" placeholder="999999">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Salary Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Employee Salaries</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="salaryTable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Role</th>
                                    <th>Current Salary</th>
                                    <th>Date of Joining</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    <tr data-department="{{ $employee->department->name ?? '' }}" 
                                        data-role="{{ $employee->getRoleNames()->first() }}"
                                        data-salary="{{ $employee->salary ?? 0 }}">
                                        <td>
                                            <input type="checkbox" class="form-check-input employee-checkbox" 
                                                   value="{{ $employee->id }}" data-name="{{ $employee->name }}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <img src="{{ $employee->profile_image ? asset('storage/' . $employee->profile_image) : asset('assets/img/users/user-05.jpg') }}" 
                                                         alt="Employee" class="rounded-circle">
                                                </div>
                                                <div>
                                                    <h6 class="fs-14 mb-0">{{ $employee->name }}</h6>
                                                    <small class="text-muted">{{ $employee->employee_id ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-primary">
                                                {{ $employee->department->name ?? 'No Department' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-info">
                                                {{ $employee->getRoleNames()->first() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-bold" id="salary-{{ $employee->id }}">
                                                    @if($employee->salary)
                                                        ₹{{ number_format($employee->salary) }}
                                                    @else
                                                        <span class="text-muted">Not Set</span>
                                                    @endif
                                                </span>
                                                <button class="btn btn-sm btn-link p-0 ms-2" 
                                                        onclick="editSalary({{ $employee->id }}, '{{ $employee->name }}', {{ $employee->salary ?? 0 }})">
                                                    <i class="ti ti-edit fs-14"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>{{ $employee->date_of_joining ? $employee->date_of_joining->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                                        data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="#" 
                                                           onclick="editSalary({{ $employee->id }}, '{{ $employee->name }}', {{ $employee->salary ?? 0 }})">
                                                            <i class="ti ti-edit me-1"></i>Edit Salary
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" 
                                                           onclick="generatePayroll({{ $employee->id }}, '{{ $employee->name }}')">
                                                            <i class="ti ti-calculator me-1"></i>Generate Payroll
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" 
                                                           onclick="viewSalaryHistory({{ $employee->id }})">
                                                            <i class="ti ti-history me-1"></i>Salary History
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ti ti-inbox fs-48 mb-2 d-block"></i>
                                                No employees found
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

    <!-- Edit Salary Modal -->
    <div class="modal fade" id="editSalaryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Salary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editSalaryForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Employee Name</label>
                            <input type="text" class="form-control" id="employeeName" readonly>
                            <input type="hidden" id="employeeId" name="employee_id">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" name="salary" id="salaryInput" 
                                       step="0.01" min="0" max="99999999.99" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Effective Date</label>
                            <input type="date" class="form-control" name="effective_date" 
                                   value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" 
                                      placeholder="Reason for salary change..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Salary</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Update Modal -->
    <div class="modal fade" id="bulkUpdateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Update Salaries</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="bulkUpdateForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Effective Date</label>
                                <input type="date" class="form-control" name="effective_date" 
                                       value="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Update Type</label>
                                <select class="form-select" id="updateType">
                                    <option value="individual">Individual Updates</option>
                                    <option value="percentage">Percentage Increase</option>
                                    <option value="fixed">Fixed Amount Increase</option>
                                </select>
                            </div>
                        </div>

                        <div id="percentageIncrease" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Percentage Increase (%)</label>
                                <input type="number" class="form-control" id="percentageValue" 
                                       step="0.1" min="0" max="100" placeholder="e.g., 10 for 10%">
                            </div>
                        </div>

                        <div id="fixedIncrease" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Fixed Amount Increase</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" id="fixedValue" 
                                           step="0.01" min="0" placeholder="e.g., 5000">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Selected Employees</label>
                            <div id="selectedEmployeesList" class="border rounded p-3 bg-light">
                                <small class="text-muted">No employees selected</small>
                            </div>
                        </div>

                        <div id="individualUpdates">
                            <label class="form-label">Individual Salary Updates</label>
                            <div id="salaryUpdatesList">
                                <!-- Individual salary inputs will be populated here -->
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" 
                                      placeholder="Reason for salary changes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Salaries</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Generate Payroll Modal -->
    <div class="modal fade" id="generatePayrollModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Individual Payroll</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="generatePayrollForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Employee</label>
                            <input type="text" class="form-control" id="payrollEmployeeName" readonly>
                            <input type="hidden" id="payrollEmployeeId" name="employee_id">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pay Period Start</label>
                                <input type="date" class="form-control" name="pay_period_start" 
                                       value="{{ now()->startOfMonth()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pay Period End</label>
                                <input type="date" class="form-control" name="pay_period_end" 
                                       value="{{ now()->endOfMonth()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Bonus</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" name="bonus" 
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Deductions</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" name="deductions" 
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" 
                                      placeholder="Additional notes for this payroll..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Generate Payroll</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let selectedEmployees = [];

        document.addEventListener('DOMContentLoaded', function() {
            // Select all functionality
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

            // Bulk update functionality
            document.getElementById('bulkUpdateBtn').addEventListener('click', function() {
                const selected = document.querySelectorAll('.employee-checkbox:checked');
                if (selected.length === 0) {
                    alert('Please select at least one employee to update salaries.');
                    return;
                }
                
                updateSelectedEmployees();
                const modal = new bootstrap.Modal(document.getElementById('bulkUpdateModal'));
                modal.show();
            });

            // Update type change handler
            document.getElementById('updateType').addEventListener('change', function() {
                const percentageDiv = document.getElementById('percentageIncrease');
                const fixedDiv = document.getElementById('fixedIncrease');
                const individualDiv = document.getElementById('individualUpdates');
                
                percentageDiv.style.display = 'none';
                fixedDiv.style.display = 'none';
                individualDiv.style.display = 'block';
                
                if (this.value === 'percentage') {
                    percentageDiv.style.display = 'block';
                } else if (this.value === 'fixed') {
                    fixedDiv.style.display = 'block';
                }
            });

            // Filter functionality
            document.getElementById('salaryFilterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                filterTable();
            });
        });

        function updateSelectedEmployees() {
            const checkboxes = document.querySelectorAll('.employee-checkbox:checked');
            const employeesList = document.getElementById('selectedEmployeesList');
            const salaryUpdatesList = document.getElementById('salaryUpdatesList');
            
            selectedEmployees = [];
            
            if (checkboxes.length === 0) {
                employeesList.innerHTML = '<small class="text-muted">No employees selected</small>';
                salaryUpdatesList.innerHTML = '';
                return;
            }
            
            let employeeNames = [];
            let salaryInputs = '';
            
            checkboxes.forEach(checkbox => {
                const name = checkbox.dataset.name;
                const id = checkbox.value;
                const currentSalary = checkbox.closest('tr').querySelector('[id^="salary-"]').textContent.replace(/[^\d]/g, '') || '0';
                
                selectedEmployees.push({id, name, currentSalary});
                employeeNames.push(name);
                
                salaryInputs += `
                    <div class="row mb-2 salary-input-row" data-employee-id="${id}">
                        <div class="col-md-6">
                            <label class="form-label">${name}</label>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control salary-input" 
                                       data-employee-id="${id}" step="0.01" min="0" 
                                       value="${currentSalary}" required>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            employeesList.innerHTML = employeeNames.map(name => 
                `<span class="badge badge-soft-primary me-1">${name}</span>`
            ).join('');
            
            salaryUpdatesList.innerHTML = salaryInputs;
        }

        function editSalary(employeeId, employeeName, currentSalary) {
            document.getElementById('employeeId').value = employeeId;
            document.getElementById('employeeName').value = employeeName;
            document.getElementById('salaryInput').value = currentSalary;
            
            const modal = new bootstrap.Modal(document.getElementById('editSalaryModal'));
            modal.show();
        }

        function generatePayroll(employeeId, employeeName) {
            document.getElementById('payrollEmployeeId').value = employeeId;
            document.getElementById('payrollEmployeeName').value = employeeName;
            
            const modal = new bootstrap.Modal(document.getElementById('generatePayrollModal'));
            modal.show();
        }

        function viewSalaryHistory(employeeId) {
            // Implement salary history view
            alert('Salary history for employee ' + employeeId + ' - Feature coming soon!');
        }

        // Edit salary form submission
        document.getElementById('editSalaryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const employeeId = document.getElementById('employeeId').value;
            const formData = new FormData(this);
            
            fetch(`{{ route('Finance.salaries.update', ':id') }}`.replace(':id', employeeId), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-HTTP-Method-Override': 'PUT'
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
                alert('An error occurred while updating salary.');
            });
        });

        // Bulk update form submission
        document.getElementById('bulkUpdateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const updateType = document.getElementById('updateType').value;
            let updates = [];
            
            if (updateType === 'percentage') {
                const percentage = parseFloat(document.getElementById('percentageValue').value);
                if (!percentage || percentage <= 0) {
                    alert('Please enter a valid percentage.');
                    return;
                }
                
                selectedEmployees.forEach(emp => {
                    const currentSalary = parseFloat(emp.currentSalary) || 0;
                    const newSalary = currentSalary * (1 + percentage / 100);
                    updates.push({user_id: emp.id, salary: newSalary.toFixed(2)});
                });
            } else if (updateType === 'fixed') {
                const fixedAmount = parseFloat(document.getElementById('fixedValue').value);
                if (!fixedAmount || fixedAmount <= 0) {
                    alert('Please enter a valid fixed amount.');
                    return;
                }
                
                selectedEmployees.forEach(emp => {
                    const currentSalary = parseFloat(emp.currentSalary) || 0;
                    const newSalary = currentSalary + fixedAmount;
                    updates.push({user_id: emp.id, salary: newSalary.toFixed(2)});
                });
            } else {
                // Individual updates
                const salaryInputs = document.querySelectorAll('.salary-input');
                salaryInputs.forEach(input => {
                    const salary = parseFloat(input.value);
                    if (salary && salary >= 0) {
                        updates.push({user_id: input.dataset.employeeId, salary: salary});
                    }
                });
            }
            
            if (updates.length === 0) {
                alert('No valid salary updates to process.');
                return;
            }
            
            const formData = new FormData(this);
            formData.append('updates', JSON.stringify(updates));
            
            fetch('{{ route("Finance.salaries.bulk-update") }}', {
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
                alert('An error occurred while updating salaries.');
            });
        });

        // Generate payroll form submission
        document.getElementById('generatePayrollForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const employeeId = document.getElementById('payrollEmployeeId').value;
            const formData = new FormData(this);
            
            fetch(`{{ route('Finance.payroll.generate-individual', ':id') }}`.replace(':id', employeeId), {
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
                    const modal = bootstrap.Modal.getInstance(document.getElementById('generatePayrollModal'));
                    modal.hide();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while generating payroll.');
            });
        });

        function filterTable() {
            const department = document.querySelector('[name="department"]').value.toLowerCase();
            const role = document.querySelector('[name="role"]').value.toLowerCase();
            const minSalary = parseFloat(document.querySelector('[name="min_salary"]').value) || 0;
            const maxSalary = parseFloat(document.querySelector('[name="max_salary"]').value) || 999999999;
            
            const rows = document.querySelectorAll('#salaryTable tbody tr');
            
            rows.forEach(row => {
                const rowDepartment = row.dataset.department.toLowerCase();
                const rowRole = row.dataset.role.toLowerCase();
                const rowSalary = parseFloat(row.dataset.salary) || 0;
                
                let show = true;
                
                if (department && !rowDepartment.includes(department)) show = false;
                if (role && !rowRole.includes(role)) show = false;
                if (rowSalary < minSalary || rowSalary > maxSalary) show = false;
                
                row.style.display = show ? 'table-row' : 'none';
            });
        }
    </script>
@endsection
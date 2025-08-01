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
                            <li class="breadcrumb-item fw-medium active" aria-current="page">Salary Increment Requests</li>
                        </ol>
                    </nav>
                    <h5 class="fw-bold mb-0">Salary Increment Requests</h5>
                </div>
            </div>
        </div>

        <!-- Create New Request Section -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="fw-bold mb-0">
                    <i class="ti ti-plus me-2"></i>Submit New Increment Request
                </h6>
            </div>
            <div class="card-body">
                <form id="incrementRequestForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Select Employee <span class="text-danger">*</span></label>
                                <select class="form-select" id="employee_id" name="employee_id" required>
                                    <option value="">Choose Employee...</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" data-salary="{{ $employee->salary ?? 0 }}">
                                            {{ $employee->name }} - {{ $employee->department->name ?? 'No Department' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Current Salary</label>
                                <input type="text" class="form-control" id="current_salary_display" readonly placeholder="Select employee first">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Requested Salary <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="requested_salary" name="requested_salary" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Increment Amount</label>
                                <input type="text" class="form-control" id="increment_display" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Increment <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="4" required placeholder="Please provide detailed justification for the salary increment request..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="reset" class="btn btn-secondary me-2">Reset</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-send me-1"></i>Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- My Requests Section -->
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="ti ti-list me-2"></i>My Increment Requests
                </h6>
                <button class="btn btn-sm btn-outline-primary" onclick="refreshRequests()">
                    <i class="ti ti-refresh"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="requestsTable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Current Salary</th>
                                <th>Requested Salary</th>
                                <th>Increment</th>
                                <th>Status</th>
                                <th>Submitted Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="requestsTableBody">
                            <!-- Dynamic content will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Request Details Modal -->
<div class="modal fade" id="requestDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Increment Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Employee</label>
                            <p id="detail_employee_name" class="mb-0"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p id="detail_status" class="mb-0"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Salary</label>
                            <p id="detail_current_salary" class="mb-0"></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Requested Salary</label>
                            <p id="detail_requested_salary" class="mb-0"></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Increment</label>
                            <p id="detail_increment" class="mb-0"></p>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Reason</label>
                    <p id="detail_reason" class="mb-0"></p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Submitted Date</label>
                            <p id="detail_submitted_date" class="mb-0"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Approved Date</label>
                            <p id="detail_approved_date" class="mb-0"></p>
                        </div>
                    </div>
                </div>
                <div id="admin_notes_section" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Admin Notes</label>
                        <p id="detail_admin_notes" class="mb-0"></p>
                    </div>
                </div>
                <div id="approved_by_section" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Approved By</label>
                        <p id="detail_approved_by" class="mb-0"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load requests on page load
    loadMyRequests();
    
    // Employee selection change handler
    document.getElementById('employee_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const currentSalary = selectedOption.getAttribute('data-salary') || 0;
        
        document.getElementById('current_salary_display').value = currentSalary > 0 
            ? '$' + parseFloat(currentSalary).toLocaleString('en-US', {minimumFractionDigits: 2})
            : '$0.00';
        
        calculateIncrement();
    });
    
    // Requested salary change handler
    document.getElementById('requested_salary').addEventListener('input', calculateIncrement);
    
    // Form submission
    document.getElementById('incrementRequestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitIncrementRequest();
    });
});

function calculateIncrement() {
    const employeeSelect = document.getElementById('employee_id');
    const requestedSalaryInput = document.getElementById('requested_salary');
    const incrementDisplay = document.getElementById('increment_display');
    
    if (employeeSelect.value && requestedSalaryInput.value) {
        const currentSalary = parseFloat(employeeSelect.options[employeeSelect.selectedIndex].getAttribute('data-salary') || 0);
        const requestedSalary = parseFloat(requestedSalaryInput.value);
        
        if (requestedSalary > currentSalary) {
            const increment = requestedSalary - currentSalary;
            const percentage = currentSalary > 0 ? ((increment / currentSalary) * 100) : 0;
            
            incrementDisplay.value = '+$' + increment.toLocaleString('en-US', {minimumFractionDigits: 2}) + 
                                   ' (' + percentage.toFixed(2) + '%)';
            incrementDisplay.className = 'form-control text-success';
        } else {
            incrementDisplay.value = 'Invalid - must be higher than current salary';
            incrementDisplay.className = 'form-control text-danger';
        }
    } else {
        incrementDisplay.value = '';
        incrementDisplay.className = 'form-control';
    }
}

function submitIncrementRequest() {
    const formData = new FormData(document.getElementById('incrementRequestForm'));
    
    fetch('/hr/increment-requests', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            document.getElementById('incrementRequestForm').reset();
            document.getElementById('current_salary_display').value = '';
            document.getElementById('increment_display').value = '';
            loadMyRequests(); // Refresh the requests table
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error submitting request', 'danger');
    });
}

function loadMyRequests() {
    fetch('/hr/increment-requests/my-requests')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('requestsTableBody');
                tbody.innerHTML = '';
                
                if (data.requests.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="ti ti-inbox text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2">No increment requests submitted yet</p>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                data.requests.forEach(request => {
                    const statusBadge = request.status === 'pending' ? 'badge-soft-warning' : 
                                      request.status === 'approved' ? 'badge-soft-success' : 'badge-soft-danger';
                    
                    const row = `
                        <tr>
                            <td>
                                <div>
                                    <h6 class="mb-0">${request.employee.name}</h6>
                                    <small class="text-muted">${request.employee.email}</small>
                                </div>
                            </td>
                            <td><strong>$${parseFloat(request.current_salary).toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                            <td><strong>$${parseFloat(request.requested_salary).toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                            <td>
                                <span class="text-success">+$${parseFloat(request.increment_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</span><br>
                                <small class="text-muted">(${request.increment_percentage}%)</small>
                            </td>
                            <td><span class="badge ${statusBadge}">${request.status.charAt(0).toUpperCase() + request.status.slice(1)}</span></td>
                            <td>${request.created_at}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="showRequestDetails(${JSON.stringify(request).replace(/"/g, '&quot;')})">
                                    <i class="ti ti-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            } else {
                showAlert('Error loading requests', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading requests', 'danger');
        });
}

function showRequestDetails(request) {
    document.getElementById('detail_employee_name').textContent = request.employee.name;
    
    const statusBadge = request.status === 'pending' ? 'badge badge-soft-warning' : 
                       request.status === 'approved' ? 'badge badge-soft-success' : 'badge badge-soft-danger';
    document.getElementById('detail_status').innerHTML = `<span class="${statusBadge}">${request.status.charAt(0).toUpperCase() + request.status.slice(1)}</span>`;
    
    document.getElementById('detail_current_salary').textContent = '$' + parseFloat(request.current_salary).toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('detail_requested_salary').textContent = '$' + parseFloat(request.requested_salary).toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('detail_increment').innerHTML = `<span class="text-success">+$${parseFloat(request.increment_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</span> <small class="text-muted">(${request.increment_percentage}%)</small>`;
    document.getElementById('detail_reason').textContent = request.reason;
    document.getElementById('detail_submitted_date').textContent = request.created_at;
    document.getElementById('detail_approved_date').textContent = request.approved_at || 'Not yet approved';
    
    // Show/hide admin notes and approved by sections
    if (request.admin_notes) {
        document.getElementById('detail_admin_notes').textContent = request.admin_notes;
        document.getElementById('admin_notes_section').style.display = 'block';
    } else {
        document.getElementById('admin_notes_section').style.display = 'none';
    }
    
    if (request.approved_by) {
        document.getElementById('detail_approved_by').textContent = request.approved_by;
        document.getElementById('approved_by_section').style.display = 'block';
    } else {
        document.getElementById('approved_by_section').style.display = 'none';
    }
    
    $('#requestDetailsModal').modal('show');
}

function refreshRequests() {
    loadMyRequests();
    showAlert('Requests refreshed', 'info');
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endsection
@extends('EmployeeManagemntsystem.Layout.employee')

@section('title', 'Apply for Leave')

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
                    <li class="breadcrumb-item">
                        <a href="{{ route('Employee.leave.index') }}">Leave Management</a>
                    </li>
                    <li class="breadcrumb-item active fw-medium" aria-current="page">Apply for Leave</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Apply for Leave</h4>
                    <p class="text-muted mb-0">Submit your leave application for approval</p>
                </div>
                <div class="leave-actions">
                    <a href="{{ route('Employee.leave.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i>Back to Leaves
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Application Form -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card leave-form-card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Leave Application Form</h6>
                </div>
                <div class="card-body">
                    <form id="leaveApplicationForm" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Leave Type Selection -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                                    <select class="form-select" name="leave_type_id" id="leaveTypeSelect" required>
                                        <option value="">Select Leave Type</option>
                                        @foreach($leaveTypes as $leaveType)
                                            <option value="{{ $leaveType->id }}" 
                                                    data-max-days="{{ $leaveType->max_days_per_year }}"
                                                    data-max-consecutive="{{ $leaveType->max_consecutive_days }}"
                                                    data-notice-days="{{ $leaveType->min_notice_days }}"
                                                    data-medical-required="{{ $leaveType->requires_medical_certificate ? 1 : 0 }}">
                                                {{ $leaveType->name }} ({{ $leaveType->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted" id="leaveTypeInfo"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Duration <span class="text-danger">*</span></label>
                                    <select class="form-select" name="duration" id="durationSelect" required>
                                        <option value="full_day">Full Day</option>
                                        <option value="first_half">First Half (Morning)</option>
                                        <option value="second_half">Second Half (Afternoon)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Date Selection -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="start_date" id="startDate" 
                                           min="{{ date('Y-m-d') }}" required>
                                    <small class="form-text text-muted" id="startDateInfo"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="end_date" id="endDate" 
                                           min="{{ date('Y-m-d') }}" required>
                                    <small class="form-text text-muted" id="endDateInfo"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Calculated Days Display -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info" id="calculatedDays" style="display: none;">
                                    <i class="ti ti-info-circle me-2"></i>
                                    <span id="daysCalculation"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="mb-3">
                            <label class="form-label">Reason for Leave <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="reason" rows="3" 
                                      placeholder="Please provide a detailed reason for your leave..." required></textarea>
                        </div>

                        <!-- Contact Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" name="contact_number" 
                                           value="{{ auth()->user()->phone }}"
                                           placeholder="Your contact number during leave">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Emergency Leave</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="emergencyLeave" 
                                               name="emergency_leave" value="1">
                                        <label class="form-check-label" for="emergencyLeave">
                                            This is an emergency leave
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Emergency leaves may bypass notice period requirements</small>
                                </div>
                            </div>
                        </div>

                        <!-- Handover Notes -->
                        <div class="mb-3">
                            <label class="form-label">Handover Notes</label>
                            <textarea class="form-control" name="handover_notes" rows="3" 
                                      placeholder="Any work handover instructions or notes for your colleagues..."></textarea>
                        </div>

                        <!-- Comments -->
                        <div class="mb-3">
                            <label class="form-label">Additional Comments</label>
                            <textarea class="form-control" name="comments" rows="2" 
                                      placeholder="Any additional information or comments..."></textarea>
                        </div>

                        <!-- File Attachments -->
                        <div class="mb-3">
                            <label class="form-label">Attachments</label>
                            <input type="file" class="form-control" name="attachments[]" multiple 
                                   accept=".jpg,.jpeg,.png,.pdf" id="attachmentInput">
                            <small class="form-text text-muted">
                                Upload medical certificates, supporting documents, etc. (Max: 2MB per file, Formats: JPG, PNG, PDF)
                            </small>
                            <div id="medicalCertificateRequired" class="alert alert-warning mt-2" style="display: none;">
                                <i class="ti ti-alert-triangle me-2"></i>
                                Medical certificate is required for this leave type if duration is 3 or more days.
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('Employee.leave.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-x me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="ti ti-send me-1"></i>Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <!-- Leave Balance Card -->
            <div class="card balance-summary-card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Your Leave Balance</h6>
                </div>
                <div class="card-body">
                    <div id="balanceDisplay">
                        <div class="text-center text-muted">
                            <i class="ti ti-calendar-stats" style="font-size: 2rem; opacity: 0.5;"></i>
                            <p class="mt-2 mb-0">Select a leave type to view balance</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Guidelines -->
            <div class="card guidelines-card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Leave Application Guidelines</h6>
                </div>
                <div class="card-body">
                    <div class="guideline-item mb-3">
                        <i class="ti ti-clock text-primary me-2"></i>
                        <small><strong>Notice Period:</strong> Submit applications in advance as per leave type requirements</small>
                    </div>
                    <div class="guideline-item mb-3">
                        <i class="ti ti-calendar-check text-success me-2"></i>
                        <small><strong>Planning:</strong> Check team calendar and avoid conflicts with important dates</small>
                    </div>
                    <div class="guideline-item mb-3">
                        <i class="ti ti-file-text text-info me-2"></i>
                        <small><strong>Documentation:</strong> Attach medical certificates for sick leave (3+ days)</small>
                    </div>
                    <div class="guideline-item mb-3">
                        <i class="ti ti-users text-warning me-2"></i>
                        <small><strong>Handover:</strong> Ensure proper work handover before extended leaves</small>
                    </div>
                    <div class="guideline-item">
                        <i class="ti ti-phone text-secondary me-2"></i>
                        <small><strong>Contact:</strong> Provide reachable contact information during leave</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.leave-form-card {
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.6);
}

.balance-summary-card,
.guidelines-card {
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.6);
}

.form-control:focus,
.form-select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.1);
}

.balance-item {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(168, 85, 247, 0.05) 100%);
}

.balance-number {
    font-size: 1.5rem;
    font-weight: 700;
}

.guideline-item {
    display: flex;
    align-items: flex-start;
    padding: 0.5rem 0;
}

.alert-info {
    background-color: rgba(99, 102, 241, 0.1);
    border-color: rgba(99, 102, 241, 0.2);
    color: #4c51bf;
}

.alert-warning {
    background-color: rgba(245, 158, 11, 0.1);
    border-color: rgba(245, 158, 11, 0.2);
    color: #d97706;
}

.card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

@media (max-width: 768px) {
    .guideline-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .guideline-item i {
        margin-bottom: 0.25rem;
    }
    
    .balance-number {
        font-size: 1.25rem;
    }
}
</style>

<script>
let leaveBalances = @json($leaveBalances);

// Leave type selection handler
document.getElementById('leaveTypeSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const leaveTypeId = this.value;
    
    if (leaveTypeId) {
        // Update leave type info
        updateLeaveTypeInfo(selectedOption);
        
        // Load and display balance
        loadLeaveBalance(leaveTypeId);
        
        // Update minimum start date based on notice period
        updateMinimumStartDate(selectedOption);
        
        // Check medical certificate requirement
        checkMedicalCertificateRequirement();
    } else {
        clearLeaveTypeInfo();
        clearBalanceDisplay();
    }
});

// Date change handlers
document.getElementById('startDate').addEventListener('change', calculateDays);
document.getElementById('endDate').addEventListener('change', calculateDays);
document.getElementById('durationSelect').addEventListener('change', calculateDays);

// Update leave type information
function updateLeaveTypeInfo(option) {
    const maxDays = option.dataset.maxDays;
    const maxConsecutive = option.dataset.maxConsecutive;
    const noticeDays = option.dataset.noticeDays;
    
    let info = [];
    if (maxDays) info.push(`Max ${maxDays} days/year`);
    if (maxConsecutive) info.push(`Max ${maxConsecutive} consecutive days`);
    if (noticeDays > 0) info.push(`${noticeDays} days notice required`);
    
    document.getElementById('leaveTypeInfo').textContent = info.join(' • ');
}

// Clear leave type info
function clearLeaveTypeInfo() {
    document.getElementById('leaveTypeInfo').textContent = '';
    document.getElementById('startDate').min = new Date().toISOString().split('T')[0];
}

// Update minimum start date based on notice period
function updateMinimumStartDate(option) {
    const noticeDays = parseInt(option.dataset.noticeDays) || 0;
    const emergencyLeave = document.getElementById('emergencyLeave').checked;
    
    if (!emergencyLeave && noticeDays > 0) {
        const minDate = new Date();
        minDate.setDate(minDate.getDate() + noticeDays);
        document.getElementById('startDate').min = minDate.toISOString().split('T')[0];
        
        document.getElementById('startDateInfo').textContent = 
            `Minimum start date: ${minDate.toLocaleDateString()} (${noticeDays} days notice)`;
    } else {
        document.getElementById('startDate').min = new Date().toISOString().split('T')[0];
        document.getElementById('startDateInfo').textContent = '';
    }
}

// Emergency leave toggle handler
document.getElementById('emergencyLeave').addEventListener('change', function() {
    const selectedOption = document.getElementById('leaveTypeSelect').options[document.getElementById('leaveTypeSelect').selectedIndex];
    if (selectedOption.value) {
        updateMinimumStartDate(selectedOption);
    }
});

// Load leave balance
async function loadLeaveBalance(leaveTypeId) {
    try {
        const response = await fetch(`{{ route('Employee.leave.balance') }}/${leaveTypeId}`);
        const data = await response.json();
        
        if (data.success) {
            displayBalance(data.balance);
        }
    } catch (error) {
        console.error('Error loading balance:', error);
    }
}

// Display balance information
function displayBalance(balance) {
    const balanceHtml = `
        <div class="balance-item mb-2">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Available Days</small>
                <span class="balance-number text-success">${balance.available || 0}</span>
            </div>
        </div>
        <div class="balance-item mb-2">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Used Days</small>
                <span class="balance-number text-primary">${balance.used || 0}</span>
            </div>
        </div>
        <div class="balance-item mb-2">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Pending Days</small>
                <span class="balance-number text-warning">${balance.pending || 0}</span>
            </div>
        </div>
        <div class="balance-item">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Total Entitled</small>
                <span class="balance-number text-info">${balance.total_entitled || 0}</span>
            </div>
        </div>
    `;
    
    document.getElementById('balanceDisplay').innerHTML = balanceHtml;
}

// Clear balance display
function clearBalanceDisplay() {
    document.getElementById('balanceDisplay').innerHTML = `
        <div class="text-center text-muted">
            <i class="ti ti-calendar-stats" style="font-size: 2rem; opacity: 0.5;"></i>
            <p class="mt-2 mb-0">Select a leave type to view balance</p>
        </div>
    `;
}

// Calculate days between dates
function calculateDays() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const duration = document.getElementById('durationSelect').value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        if (end >= start) {
            let days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
            
            // Adjust for half days
            if (duration !== 'full_day') {
                if (start.getTime() === end.getTime()) {
                    days = 0.5;
                } else {
                    // For multi-day ranges with half days, adjust accordingly
                    days = days - 0.5;
                }
            }
            
            document.getElementById('calculatedDays').style.display = 'block';
            document.getElementById('daysCalculation').textContent = 
                `Total leave days: ${days} ${days === 1 ? 'day' : 'days'}`;
                
            // Update end date info
            document.getElementById('endDateInfo').textContent = 
                `Duration: ${days} ${days === 1 ? 'day' : 'days'}`;
                
            // Check medical certificate requirement
            checkMedicalCertificateRequirement();
        } else {
            document.getElementById('calculatedDays').style.display = 'none';
            document.getElementById('endDateInfo').textContent = 'End date must be after start date';
        }
    } else {
        document.getElementById('calculatedDays').style.display = 'none';
        document.getElementById('endDateInfo').textContent = '';
    }
}

// Check medical certificate requirement
function checkMedicalCertificateRequirement() {
    const selectedOption = document.getElementById('leaveTypeSelect').options[document.getElementById('leaveTypeSelect').selectedIndex];
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (selectedOption.value && startDate && endDate) {
        const medicalRequired = selectedOption.dataset.medicalRequired === '1';
        const start = new Date(startDate);
        const end = new Date(endDate);
        const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        
        if (medicalRequired && days >= 3) {
            document.getElementById('medicalCertificateRequired').style.display = 'block';
        } else {
            document.getElementById('medicalCertificateRequired').style.display = 'none';
        }
    }
}

// Form submission
document.getElementById('leaveApplicationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i>Submitting...';
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("Employee.leave.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            toastr.success(data.message, 'Success');
            setTimeout(() => {
                window.location.href = '{{ route("Employee.leave.index") }}';
            }, 1500);
        } else {
            toastr.error(data.message, 'Error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        toastr.error('An error occurred while submitting your application', 'Error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});

// Auto-set end date when start date changes (for single day leaves)
document.getElementById('startDate').addEventListener('change', function() {
    const endDateField = document.getElementById('endDate');
    if (!endDateField.value) {
        endDateField.value = this.value;
        calculateDays();
    }
});
</script>
@endsection
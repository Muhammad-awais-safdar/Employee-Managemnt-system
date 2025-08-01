@extends('EmployeeManagemntsystem.Layout.employee')

@section('title', 'Edit Leave Application')

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
                    <li class="breadcrumb-item">
                        <a href="{{ route('Employee.leave.show', $leave) }}">{{ $leave->application_id }}</a>
                    </li>
                    <li class="breadcrumb-item active fw-medium" aria-current="page">Edit</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Edit Leave Application</h4>
                    <p class="text-muted mb-0">Application ID: {{ $leave->application_id }}</p>
                </div>
                <div class="leave-actions">
                    <a href="{{ route('Employee.leave.show', $leave) }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i>Back to Details
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Warning -->
    <div class="alert alert-warning mb-4">
        <i class="ti ti-alert-triangle me-2"></i>
        <strong>Note:</strong> You can only edit leave applications that are still pending approval. 
        Changes will reset the approval process.
    </div>

    <!-- Leave Application Edit Form -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card leave-form-card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Edit Leave Application</h6>
                        <span class="badge {{ $leave->status_badge_class }}">{{ $leave->status_label }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <form id="leaveEditForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
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
                                                    data-medical-required="{{ $leaveType->requires_medical_certificate ? 1 : 0 }}"
                                                    {{ $leave->leave_type_id == $leaveType->id ? 'selected' : '' }}>
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
                                        <option value="full_day" {{ $leave->duration === 'full_day' ? 'selected' : '' }}>Full Day</option>
                                        <option value="first_half" {{ $leave->duration === 'first_half' ? 'selected' : '' }}>First Half (Morning)</option>
                                        <option value="second_half" {{ $leave->duration === 'second_half' ? 'selected' : '' }}>Second Half (Afternoon)</option>
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
                                           value="{{ $leave->start_date->format('Y-m-d') }}"
                                           min="{{ date('Y-m-d') }}" required>
                                    <small class="form-text text-muted" id="startDateInfo"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="end_date" id="endDate" 
                                           value="{{ $leave->end_date->format('Y-m-d') }}"
                                           min="{{ date('Y-m-d') }}" required>
                                    <small class="form-text text-muted" id="endDateInfo"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Calculated Days Display -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info" id="calculatedDays">
                                    <i class="ti ti-info-circle me-2"></i>
                                    <span id="daysCalculation">Total leave days: {{ $leave->total_days }} {{ $leave->total_days == 1 ? 'day' : 'days' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="mb-3">
                            <label class="form-label">Reason for Leave <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="reason" rows="3" 
                                      placeholder="Please provide a detailed reason for your leave..." required>{{ $leave->reason }}</textarea>
                        </div>

                        <!-- Contact Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" name="contact_number" 
                                           value="{{ $leave->contact_number }}"
                                           placeholder="Your contact number during leave">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Emergency Leave</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="emergencyLeave" 
                                               name="emergency_leave" value="1" {{ $leave->emergency_leave ? 'checked' : '' }}>
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
                                      placeholder="Any work handover instructions or notes for your colleagues...">{{ $leave->handover_notes }}</textarea>
                        </div>

                        <!-- Comments -->
                        <div class="mb-3">
                            <label class="form-label">Additional Comments</label>
                            <textarea class="form-control" name="comments" rows="2" 
                                      placeholder="Any additional information or comments...">{{ $leave->comments }}</textarea>
                        </div>

                        <!-- Current Attachments -->
                        @if($leave->attachments && count($leave->attachments) > 0)
                            <div class="mb-3">
                                <label class="form-label">Current Attachments</label>
                                <div class="current-attachments">
                                    @foreach($leave->attachments as $index => $attachment)
                                        <div class="attachment-item d-flex align-items-center justify-content-between p-2 border rounded mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-file text-primary me-2"></i>
                                                <span>{{ $attachment['name'] }}</span>
                                                <small class="text-muted ms-2">({{ number_format($attachment['size'] / 1024, 1) }} KB)</small>
                                            </div>
                                            <div>
                                                <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" 
                                                   class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="removeAttachment({{ $index }})">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- New File Attachments -->
                        <div class="mb-3">
                            <label class="form-label">Add New Attachments</label>
                            <input type="file" class="form-control" name="attachments[]" multiple 
                                   accept=".jpg,.jpeg,.png,.pdf" id="attachmentInput">
                            <small class="form-text text-muted">
                                Upload additional files (Max: 2MB per file, Formats: JPG, PNG, PDF)
                            </small>
                            <div id="medicalCertificateRequired" class="alert alert-warning mt-2" style="display: none;">
                                <i class="ti ti-alert-triangle me-2"></i>
                                Medical certificate is required for this leave type if duration is 3 or more days.
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('Employee.leave.show', $leave) }}" class="btn btn-outline-secondary">
                                <i class="ti ti-x me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="ti ti-check me-1"></i>Update Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <!-- Original Application Info -->
            <div class="card original-info-card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Original Application</h6>
                </div>
                <div class="card-body">
                    <div class="info-item mb-2">
                        <small class="text-muted">Applied On:</small>
                        <div>{{ $leave->applied_at->format('M d, Y h:i A') }}</div>
                    </div>
                    <div class="info-item mb-2">
                        <small class="text-muted">Original Dates:</small>
                        <div>{{ $leave->date_range }}</div>
                    </div>
                    <div class="info-item mb-2">
                        <small class="text-muted">Original Duration:</small>
                        <div>{{ $leave->total_days }} {{ $leave->total_days == 1 ? 'day' : 'days' }}</div>
                    </div>
                    <div class="info-item">
                        <small class="text-muted">Status:</small>
                        <div><span class="badge {{ $leave->status_badge_class }}">{{ $leave->status_label }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Leave Balance Card -->
            <div class="card balance-summary-card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Leave Balance</h6>
                </div>
                <div class="card-body">
                    <div id="balanceDisplay">
                        <!-- Balance will be loaded dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.leave-form-card,
.original-info-card,
.balance-summary-card {
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.6);
}

.form-control:focus,
.form-select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.1);
}

.attachment-item {
    background: #f9fafb;
    transition: all 0.2s ease;
}

.attachment-item:hover {
    background: #f3f4f6;
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

.info-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-item:last-child {
    border-bottom: none;
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
    .balance-number {
        font-size: 1.25rem;
    }
}
</style>

<script>
let leaveBalances = @json($leaveBalances);
let removedAttachments = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load initial balance
    const initialLeaveType = document.getElementById('leaveTypeSelect').value;
    if (initialLeaveType) {
        loadLeaveBalance(initialLeaveType);
        updateLeaveTypeInfo();
    }
    
    // Calculate initial days
    calculateDays();
});

// Leave type selection handler
document.getElementById('leaveTypeSelect').addEventListener('change', function() {
    const leaveTypeId = this.value;
    
    if (leaveTypeId) {
        updateLeaveTypeInfo();
        loadLeaveBalance(leaveTypeId);
        updateMinimumStartDate();
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
function updateLeaveTypeInfo() {
    const selectedOption = document.getElementById('leaveTypeSelect').options[document.getElementById('leaveTypeSelect').selectedIndex];
    
    if (selectedOption.value) {
        const maxDays = selectedOption.dataset.maxDays;
        const maxConsecutive = selectedOption.dataset.maxConsecutive;
        const noticeDays = selectedOption.dataset.noticeDays;
        
        let info = [];
        if (maxDays) info.push(`Max ${maxDays} days/year`);
        if (maxConsecutive) info.push(`Max ${maxConsecutive} consecutive days`);
        if (noticeDays > 0) info.push(`${noticeDays} days notice required`);
        
        document.getElementById('leaveTypeInfo').textContent = info.join(' • ');
    }
}

// Clear leave type info
function clearLeaveTypeInfo() {
    document.getElementById('leaveTypeInfo').textContent = '';
}

// Update minimum start date based on notice period
function updateMinimumStartDate() {
    const selectedOption = document.getElementById('leaveTypeSelect').options[document.getElementById('leaveTypeSelect').selectedIndex];
    const noticeDays = parseInt(selectedOption.dataset.noticeDays) || 0;
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
document.getElementById('emergencyLeave').addEventListener('change', updateMinimumStartDate);

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
                    days = days - 0.5;
                }
            }
            
            document.getElementById('daysCalculation').textContent = 
                `Total leave days: ${days} ${days === 1 ? 'day' : 'days'}`;
                
            document.getElementById('endDateInfo').textContent = 
                `Duration: ${days} ${days === 1 ? 'day' : 'days'}`;
                
            checkMedicalCertificateRequirement();
        } else {
            document.getElementById('endDateInfo').textContent = 'End date must be after start date';
        }
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

// Remove attachment function
function removeAttachment(index) {
    if (confirm('Are you sure you want to remove this attachment?')) {
        removedAttachments.push(index);
        document.querySelector(`[onclick="removeAttachment(${index})"]`).closest('.attachment-item').remove();
    }
}

// Form submission
document.getElementById('leaveEditForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i>Updating...';
    
    const formData = new FormData(this);
    
    // Add removed attachments
    if (removedAttachments.length > 0) {
        formData.append('removed_attachments', JSON.stringify(removedAttachments));
    }
    
    try {
        const response = await fetch('{{ route("Employee.leave.update", $leave) }}', {
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
                window.location.href = '{{ route("Employee.leave.show", $leave) }}';
            }, 1500);
        } else {
            toastr.error(data.message, 'Error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        toastr.error('An error occurred while updating your application', 'Error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
</script>
@endsection
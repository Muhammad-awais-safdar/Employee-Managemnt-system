@extends('EmployeeManagemntsystem.Layout.employee')

@section('title', 'Leave Details')

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
                    <li class="breadcrumb-item active fw-medium" aria-current="page">Leave Details</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Leave Application Details</h4>
                    <p class="text-muted mb-0">Application ID: {{ $leave->application_id }}</p>
                </div>
                <div class="leave-actions d-flex gap-2">
                    @if($leave->canBeEdited())
                        <a href="{{ route('Employee.leave.edit', $leave) }}" class="btn btn-outline-warning btn-sm">
                            <i class="ti ti-edit me-1"></i>Edit
                        </a>
                    @endif
                    @if($leave->canBeCancelled())
                        <button class="btn btn-outline-danger btn-sm" onclick="cancelLeave('{{ $leave->id }}')">
                            <i class="ti ti-x me-1"></i>Cancel
                        </button>
                    @endif
                    <a href="{{ route('Employee.leave.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Details -->
    <div class="row">
        <div class="col-xl-8">
            <!-- Main Leave Information -->
            <div class="card leave-details-card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Leave Information</h6>
                        <span class="badge {{ $leave->status_badge_class }} fs-6">
                            {{ $leave->status_label }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="info-label">Leave Type</label>
                                <div class="info-value">
                                    <span class="badge bg-primary me-2">{{ $leave->leaveType->code }}</span>
                                    {{ $leave->leaveType->name }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="info-label">Duration</label>
                                <div class="info-value">{{ $leave->duration_label }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="info-label">Start Date</label>
                                <div class="info-value">{{ $leave->start_date->format('M d, Y') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="info-label">End Date</label>
                                <div class="info-value">{{ $leave->end_date->format('M d, Y') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="info-label">Total Days</label>
                                <div class="info-value">
                                    <span class="text-primary fw-bold">{{ $leave->total_days }}</span> 
                                    {{ $leave->total_days == 1 ? 'day' : 'days' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="info-label">Applied On</label>
                                <div class="info-value">{{ $leave->applied_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                    </div>

                    @if($leave->emergency_leave)
                        <div class="alert alert-warning">
                            <i class="ti ti-alert-triangle me-2"></i>
                            <strong>Emergency Leave:</strong> This application was marked as emergency leave.
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            <div class="info-item mb-3">
                                <label class="info-label">Reason for Leave</label>
                                <div class="info-value">{{ $leave->reason }}</div>
                            </div>
                        </div>
                    </div>

                    @if($leave->contact_number)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="info-label">Contact Number</label>
                                    <div class="info-value">{{ $leave->contact_number }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($leave->handover_notes)
                        <div class="row">
                            <div class="col-12">
                                <div class="info-item mb-3">
                                    <label class="info-label">Handover Notes</label>
                                    <div class="info-value">{{ $leave->handover_notes }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($leave->comments)
                        <div class="row">
                            <div class="col-12">
                                <div class="info-item">
                                    <label class="info-label">Additional Comments</label>
                                    <div class="info-value">{{ $leave->comments }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Attachments -->
            @if($leave->attachments && count($leave->attachments) > 0)
                <div class="card attachments-card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <h6 class="mb-0">Attachments</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($leave->attachments as $attachment)
                                <div class="col-md-6 mb-3">
                                    <div class="attachment-item">
                                        <div class="d-flex align-items-center">
                                            <div class="attachment-icon me-3">
                                                @if(in_array(strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                                    <i class="ti ti-photo text-success"></i>
                                                @elseif(strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION)) === 'pdf')
                                                    <i class="ti ti-file-text text-danger"></i>
                                                @else
                                                    <i class="ti ti-file text-primary"></i>
                                                @endif
                                            </div>
                                            <div class="attachment-details">
                                                <div class="attachment-name">{{ $attachment['name'] }}</div>
                                                <small class="text-muted">
                                                    {{ number_format($attachment['size'] / 1024, 1) }} KB
                                                </small>
                                            </div>
                                            <div class="attachment-actions ms-auto">
                                                <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Approval Information -->
            @if(in_array($leave->status, ['approved', 'rejected']) && $leave->approver)
                <div class="card approval-card border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h6 class="mb-0">
                            @if($leave->status === 'approved')
                                <i class="ti ti-check-circle text-success me-2"></i>Approval Details
                            @else
                                <i class="ti ti-x-circle text-danger me-2"></i>Rejection Details
                            @endif
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="info-label">
                                        {{ $leave->status === 'approved' ? 'Approved By' : 'Rejected By' }}
                                    </label>
                                    <div class="info-value">{{ $leave->approver->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="info-label">
                                        {{ $leave->status === 'approved' ? 'Approved On' : 'Rejected On' }}
                                    </label>
                                    <div class="info-value">{{ $leave->reviewed_at->format('M d, Y h:i A') }}</div>
                                </div>
                            </div>
                        </div>
                        @if($leave->admin_notes)
                            <div class="row">
                                <div class="col-12">
                                    <div class="info-item">
                                        <label class="info-label">
                                            {{ $leave->status === 'approved' ? 'Approval Notes' : 'Rejection Reason' }}
                                        </label>
                                        <div class="info-value">
                                            <div class="alert {{ $leave->status === 'approved' ? 'alert-success' : 'alert-danger' }} mb-0">
                                                {{ $leave->admin_notes }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-xl-4">
            <!-- Status Timeline -->
            <div class="card timeline-card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Application Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Applied -->
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-primary">
                                <i class="ti ti-send"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Application Submitted</h6>
                                <p class="timeline-date">{{ $leave->applied_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        <!-- Under Review -->
                        @if(in_array($leave->status, ['pending']))
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-warning">
                                    <i class="ti ti-clock"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Under Review</h6>
                                    <p class="timeline-date">Waiting for approval</p>
                                </div>
                            </div>
                        @endif

                        <!-- Decision -->
                        @if(in_array($leave->status, ['approved', 'rejected']))
                            <div class="timeline-item active">
                                <div class="timeline-marker {{ $leave->status === 'approved' ? 'bg-success' : 'bg-danger' }}">
                                    <i class="ti {{ $leave->status === 'approved' ? 'ti-check' : 'ti-x' }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">
                                        {{ $leave->status === 'approved' ? 'Approved' : 'Rejected' }}
                                    </h6>
                                    <p class="timeline-date">{{ $leave->reviewed_at->format('M d, Y h:i A') }}</p>
                                    <p class="timeline-user">by {{ $leave->approver->name }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Leave Period (for approved leaves) -->
                        @if($leave->status === 'approved')
                            <div class="timeline-item {{ $leave->start_date <= now() ? 'active' : '' }}">
                                <div class="timeline-marker bg-info">
                                    <i class="ti ti-calendar"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Leave Period</h6>
                                    <p class="timeline-date">{{ $leave->date_range }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card actions-card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($leave->canBeEdited())
                            <a href="{{ route('Employee.leave.edit', $leave) }}" class="btn btn-warning">
                                <i class="ti ti-edit me-2"></i>Edit Application
                            </a>
                        @endif
                        
                        @if($leave->canBeCancelled())
                            <button class="btn btn-danger" onclick="cancelLeave('{{ $leave->id }}')">
                                <i class="ti ti-x me-2"></i>Cancel Application
                            </button>
                        @endif
                        
                        <a href="{{ route('Employee.leave.create') }}" class="btn btn-outline-primary">
                            <i class="ti ti-plus me-2"></i>Apply for New Leave
                        </a>
                        
                        <a href="{{ route('Employee.leave.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-list me-2"></i>View All Leaves
                        </a>
                        
                        <button class="btn btn-outline-info" onclick="printLeave()">
                            <i class="ti ti-printer me-2"></i>Print Application
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.leave-details-card,
.attachments-card,
.approval-card,
.timeline-card,
.actions-card {
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.6);
}

.info-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 0.25rem;
    display: block;
}

.info-value {
    font-size: 0.9375rem;
    color: #374151;
    line-height: 1.5;
}

.attachment-item {
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
    transition: all 0.2s ease;
}

.attachment-item:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.attachment-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.attachment-name {
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.25rem;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-item.active .timeline-marker {
    background: var(--bs-primary) !important;
    color: white;
}

.timeline-item.active ~ .timeline-item .timeline-marker {
    background: #e5e7eb;
    color: #9ca3af;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    background: #e5e7eb;
    color: #9ca3af;
    z-index: 1;
}

.timeline-content {
    padding-left: 1rem;
}

.timeline-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.25rem;
}

.timeline-date {
    font-size: 0.8125rem;
    color: #6b7280;
    margin-bottom: 0;
}

.timeline-user {
    font-size: 0.8125rem;
    color: #9ca3af;
    margin-bottom: 0;
}

.card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

@media (max-width: 768px) {
    .leave-actions {
        flex-direction: column;
    }
    
    .leave-actions .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .timeline {
        padding-left: 1.5rem;
    }
    
    .timeline-marker {
        left: -1.5rem;
        width: 1.5rem;
        height: 1.5rem;
        font-size: 0.75rem;
    }
    
    .timeline::before {
        left: 0.75rem;
    }
}

@media print {
    .leave-actions,
    .actions-card,
    .breadcrumb {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>

<script>
// Cancel leave function
async function cancelLeave(leaveId) {
    const result = await Swal.fire({
        title: 'Cancel Leave Application?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, cancel it!'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`{{ route('Employee.leave.index') }}/${leaveId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
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
            }
        } catch (error) {
            console.error('Error:', error);
            toastr.error('An error occurred while cancelling the leave application', 'Network Error');
        }
    }
}

// Print leave function
function printLeave() {
    window.print();
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
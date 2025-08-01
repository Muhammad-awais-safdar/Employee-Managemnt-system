@if($notificationItems && $notificationItems->count() > 0)
    <div class="notification-list">
        @foreach($notificationItems as $notification)
            @php
                $data = $notification->data;
                $isUnread = !$notification->read_at;
                $timeAgo = $notification->created_at->diffForHumans();
            @endphp
            
            <div class="notification-item {{ $isUnread ? 'unread' : '' }} position-relative" 
                 data-notification-id="{{ $notification->id }}"
                 data-action="{{ $data['action'] ?? '' }}">
                <div class="d-flex">
                    <!-- Notification Icon -->
                    <div class="notification-icon bg-{{ $data['color'] ?? 'primary' }}">
                        <i class="{{ $data['icon'] ?? 'ti ti-bell' }}"></i>
                    </div>
                    
                    <!-- Notification Content -->
                    <div class="notification-content">
                        <div class="notification-title">{{ $data['title'] ?? 'Notification' }}</div>
                        <div class="notification-message">{{ $data['message'] ?? 'No message' }}</div>
                        
                        <!-- Leave Application Details for Employee -->
                        @if(($data['type'] ?? '') === 'leave_application')
                            <div class="notification-details mt-2">
                                <div class="row g-2">
                                    <div class="col-auto">
                                        <span class="badge bg-info">{{ $data['leave_type'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <small class="text-muted">
                                            <i class="ti ti-calendar me-1"></i>
                                            {{ $data['start_date'] ?? 'N/A' }} - {{ $data['end_date'] ?? 'N/A' }}
                                        </small>
                                    </div>
                                    <div class="col-auto">
                                        <small class="text-muted">
                                            <i class="ti ti-clock me-1"></i>
                                            {{ $data['total_days'] ?? 0 }} {{ ($data['total_days'] ?? 0) == 1 ? 'day' : 'days' }}
                                        </small>
                    </div>
                                    @if(($data['status'] ?? '') === 'approved')
                                        <div class="col-auto">
                                            <span class="badge bg-success">
                                                <i class="ti ti-check me-1"></i>Approved
                                            </span>
                                        </div>
                                    @elseif(($data['status'] ?? '') === 'rejected')
                                        <div class="col-auto">
                                            <span class="badge bg-danger">
                                                <i class="ti ti-x me-1"></i>Rejected
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                @if(!empty($data['reason']))
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <strong>Your Reason:</strong> {{ Str::limit($data['reason'], 150) }}
                                        </small>
                                    </div>
                                @endif
                                
                                @if(in_array($data['action'] ?? '', ['approved', 'rejected']) && !empty($data['admin_notes']))
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small class="text-muted">
                                            <strong>{{ $data['action'] === 'approved' ? 'Approval' : 'Rejection' }} Note:</strong><br>
                                            {{ $data['admin_notes'] }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Notification Meta -->
                        <div class="notification-meta mt-2">
                            <span class="notification-time">
                                <i class="ti ti-clock me-1"></i>{{ $timeAgo }}
                            </span>
                            <div class="notification-actions">
                                @if($isUnread)
                                    <button class="btn btn-sm btn-outline-primary mark-read-btn" 
                                            onclick="markAsRead('{{ $notification->id }}')" 
                                            title="Mark as read">
                                        <i class="ti ti-check"></i>
                                    </button>
                                @endif
                                
                                @if(($data['action_url'] ?? false))
                                    <a href="{{ $data['action_url'] }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="View details">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                @endif
                                
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteNotification('{{ $notification->id }}')" 
                                        title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Application ID for Leave Notifications -->
                @if(($data['type'] ?? '') === 'leave_application' && !empty($data['application_id']))
                    <div class="position-absolute top-0 end-0 mt-2 me-2">
                        <small class="text-muted fw-medium">{{ $data['application_id'] }}</small>
                    </div>
                @endif
                
                <!-- Status Badge for Leave Notifications -->
                @if(($data['type'] ?? '') === 'leave_application')
                    <div class="position-absolute top-0 start-0 mt-1">
                        @switch($data['action'] ?? '')
                            @case('approved')
                                <div class="status-indicator bg-success"></div>
                                @break
                            @case('rejected')
                                <div class="status-indicator bg-danger"></div>
                                @break
                            @case('applied')
                                <div class="status-indicator bg-warning"></div>
                                @break
                            @default
                                <div class="status-indicator bg-info"></div>
                        @endswitch
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <i class="ti ti-bell-off"></i>
        <h5 class="text-muted">No notifications found</h5>
        <p class="text-muted mb-0">
            @if(request()->routeIs('Employee.notifications.index'))
                You don't have any notifications yet. When your leave applications are reviewed, you'll see updates here.
            @else
                No notifications match the current filter.
            @endif
        </p>
        @if(request()->routeIs('Employee.notifications.index'))
            <div class="mt-3">
                <a href="{{ route('Employee.leave.create') }}" class="btn btn-primary btn-sm">
                    <i class="ti ti-plus me-1"></i>Apply for Leave
                </a>
            </div>
        @endif
    </div>
@endif

<style>
.notification-details {
    padding: 0.75rem;
    background: rgba(99, 102, 241, 0.05);
    border-radius: 8px;
    margin-top: 0.5rem;
}

.notification-details .badge {
    font-size: 0.75rem;
}

.notification-item .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.notification-item .btn i {
    font-size: 0.875rem;
}

.status-indicator {
    width: 4px;
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    border-radius: 0 2px 2px 0;
}

.notification-item.unread .status-indicator {
    box-shadow: 0 0 8px rgba(99, 102, 241, 0.4);
}

/* Enhanced styling for employee notifications */
.notification-item[data-action="approved"] {
    border-left-color: #22c55e !important;
}

.notification-item[data-action="approved"]:hover {
    background-color: rgba(34, 197, 94, 0.05) !important;
}

.notification-item[data-action="rejected"] {
    border-left-color: #ef4444 !important;
}

.notification-item[data-action="rejected"]:hover {
    background-color: rgba(239, 68, 68, 0.05) !important;
}

.notification-item[data-action="applied"] {
    border-left-color: #f59e0b !important;
}

.notification-item[data-action="applied"]:hover {
    background-color: rgba(245, 158, 11, 0.05) !important;
}

.bg-light.rounded {
    border: 1px solid rgba(0, 0, 0, 0.05);
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .notification-details .row {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .notification-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .notification-actions {
        justify-content: flex-start;
    }
}
</style>
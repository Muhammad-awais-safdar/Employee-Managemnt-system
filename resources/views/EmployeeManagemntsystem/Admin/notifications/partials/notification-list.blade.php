@if($notificationItems && $notificationItems->count() > 0)
    <div class="notification-list">
        @foreach($notificationItems as $notification)
            @php
                $data = $notification->data;
                $isUnread = !$notification->read_at;
                $timeAgo = $notification->created_at->diffForHumans();
            @endphp
            
            <div class="notification-item {{ $isUnread ? 'unread' : '' }} position-relative" 
                 data-notification-id="{{ $notification->id }}">
                <div class="d-flex">
                    <!-- Notification Icon -->
                    <div class="notification-icon bg-{{ $data['color'] ?? 'primary' }}">
                        <i class="{{ $data['icon'] ?? 'ti ti-bell' }}"></i>
                    </div>
                    
                    <!-- Notification Content -->
                    <div class="notification-content">
                        <div class="notification-title">{{ $data['title'] ?? 'Notification' }}</div>
                        <div class="notification-message">{{ $data['message'] ?? 'No message' }}</div>
                        
                        <!-- Additional Info for Leave Applications -->
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
                                    @if(($data['emergency_leave'] ?? false))
                                        <div class="col-auto">
                                            <span class="badge bg-warning">Emergency</span>
                                        </div>
                                    @endif
                                </div>
                                
                                @if(!empty($data['reason']))
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <strong>Reason:</strong> {{ Str::limit($data['reason'], 100) }}
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
                        <small class="text-muted">{{ $data['application_id'] }}</small>
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
            @if(request()->routeIs('*.notifications.index'))
                You don't have any notifications yet.
            @else
                No notifications match the current filter.
            @endif
        </p>
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
</style>
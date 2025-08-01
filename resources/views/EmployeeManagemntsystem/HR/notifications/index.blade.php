@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'Notifications - HR Dashboard')

@section('content')
<div class="page-wrapper">

    <!-- Page Content -->
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h3 class="page-title">Notifications</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('HR.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Notifications</li>
                    </ul>
                </div>
                <div class="col-md-8 text-end">
                    <div class="btn-group">
                        <button class="btn btn-primary" onclick="markAllAsRead()">
                            <i class="ti ti-check-all me-1"></i>Mark All as Read
                        </button>
                        <button class="btn btn-outline-danger" onclick="clearAllNotifications()">
                            <i class="ti ti-trash me-1"></i>Clear All
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- HR Specific Stats -->
        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-primary border-primary">
                                <i class="ti ti-calendar-plus"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{ $notifications->where('data.action', 'applied')->count() }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Pending Leave Applications</h6>
                            <p class="text-muted mb-0">Requires your review</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-warning border-warning">
                                <i class="ti ti-bell-ringing"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{ $unreadCount }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Unread Notifications</h6>
                            <p class="text-muted mb-0">New alerts for you</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-success border-success">
                                <i class="ti ti-urgent"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{ $notifications->where('data.emergency_leave', true)->count() }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Emergency Leaves</h6>
                            <p class="text-muted mb-0">Urgent attention needed</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-info border-info">
                                <i class="ti ti-clock"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{ $notifications->where('created_at', '>=', now()->today())->count() }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Today's Requests</h6>
                            <p class="text-muted mb-0">New applications today</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions for HR -->
        <div class="row">
            <div class="col-md-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-white mb-1">HR Quick Actions</h5>
                                <p class="text-white-50 mb-0">Manage leave applications efficiently</p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('HR.leave.index', ['filter' => 'pending']) }}" class="btn btn-light">
                                    <i class="ti ti-calendar-check me-1"></i>Review Leave Applications
                                </a>
                                <a href="{{ route('HR.departments.assignments') }}" class="btn btn-outline-light">
                                    <i class="ti ti-users-group me-1"></i>Manage Departments
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Leave Application Notifications</h4>
                        <div class="card-options">
                            <ul class="nav nav-tabs" id="notificationTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" 
                                            type="button" role="tab" aria-controls="pending" aria-selected="true">
                                        Pending Review <span class="badge bg-warning ms-1">{{ $notifications->where('data.action', 'applied')->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="emergency-tab" data-bs-toggle="tab" data-bs-target="#emergency" 
                                            type="button" role="tab" aria-controls="emergency" aria-selected="false">
                                        Emergency <span class="badge bg-danger ms-1">{{ $notifications->where('data.emergency_leave', true)->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="all-hr-tab" data-bs-toggle="tab" data-bs-target="#all-hr" 
                                            type="button" role="tab" aria-controls="all-hr" aria-selected="false">
                                        All Notifications <span class="badge bg-secondary ms-1">{{ $notifications->total() }}</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="notificationTabContent">
                            <!-- Pending Review Tab -->
                            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                                @include('EmployeeManagemntsystem.Admin.notifications.partials.notification-list', ['notificationItems' => $notifications->where('data.action', 'applied')])
                            </div>
                            
                            <!-- Emergency Leave Tab -->
                            <div class="tab-pane fade" id="emergency" role="tabpanel" aria-labelledby="emergency-tab">
                                @include('EmployeeManagemntsystem.Admin.notifications.partials.notification-list', ['notificationItems' => $notifications->where('data.emergency_leave', true)])
                            </div>
                            
                            <!-- All Notifications Tab -->
                            <div class="tab-pane fade" id="all-hr" role="tabpanel" aria-labelledby="all-hr-tab">
                                @include('EmployeeManagemntsystem.Admin.notifications.partials.notification-list', ['notificationItems' => $notifications])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-center">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

<style>
.notification-item {
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
}

.notification-item:hover {
    background-color: rgba(99, 102, 241, 0.05);
    border-left-color: #6366f1;
}

.notification-item.unread {
    background-color: rgba(99, 102, 241, 0.02);
    border-left-color: #6366f1;
}

.notification-item.unread::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 8px;
    height: 8px;
    background: #6366f1;
    border-radius: 50%;
}

.notification-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

.dash-widget-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.dash-widget-icon {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 1rem;
    border: 2px solid;
    background: rgba(255, 255, 255, 0.1);
}

.dash-count h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
}

.card.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

<script>
// Mark all notifications as read
async function markAllAsRead() {
    if (!confirm('Mark all notifications as read?')) return;
    
    try {
        const response = await fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            toastr.success('All notifications marked as read', 'Success');
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error('Failed to mark notifications as read', 'Error');
        }
    } catch (error) {
        console.error('Error:', error);
        toastr.error('Network error occurred', 'Error');
    }
}

// Clear all notifications
async function clearAllNotifications() {
    if (!confirm('Are you sure you want to delete all notifications? This action cannot be undone.')) return;
    
    try {
        const response = await fetch('/notifications/clear-all', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            toastr.success('All notifications cleared', 'Success');
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error('Failed to clear notifications', 'Error');
        }
    } catch (error) {
        console.error('Error:', error);
        toastr.error('Network error occurred', 'Error');
    }
}

// Mark single notification as read
async function markAsRead(notificationId) {
    try {
        const response = await fetch(`/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const element = document.querySelector(`[data-notification-id="${notificationId}"]`);
            element.classList.remove('unread');
            element.querySelector('.mark-read-btn')?.remove();
            toastr.success('Notification marked as read', 'Success');
        }
    } catch (error) {
        console.error('Error:', error);
        toastr.error('Failed to mark notification as read', 'Error');
    }
}

// Delete single notification
async function deleteNotification(notificationId) {
    if (!confirm('Delete this notification?')) return;
    
    try {
        const response = await fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const element = document.querySelector(`[data-notification-id="${notificationId}"]`);
            element.style.opacity = '0';
            element.style.transition = 'opacity 0.3s ease';
            setTimeout(() => element.remove(), 300);
            toastr.success('Notification deleted', 'Success');
        }
    } catch (error) {
        console.error('Error:', error);
        toastr.error('Failed to delete notification', 'Error');
    }
}
</script>
@endsection
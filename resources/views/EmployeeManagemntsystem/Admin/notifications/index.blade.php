@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'Notifications - Admin Dashboard')

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
                            <a href="{{ route('Admin.dashboard') }}">Dashboard</a>
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

        <!-- Notification Stats -->
        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-primary border-primary">
                                <i class="ti ti-bell"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{ $notifications->total() }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Total Notifications</h6>
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
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-success border-success">
                                <i class="ti ti-calendar-plus"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{ $notifications->where('data.action', 'applied')->count() }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Leave Applications</h6>
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
                            <h6 class="text-muted">Today's Notifications</h6>
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
                        <h4 class="card-title">All Notifications</h4>
                        <div class="card-options">
                            <ul class="nav nav-tabs" id="notificationTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" 
                                            type="button" role="tab" aria-controls="all" aria-selected="true">
                                        All <span class="badge bg-secondary ms-1">{{ $notifications->total() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="unread-tab" data-bs-toggle="tab" data-bs-target="#unread" 
                                            type="button" role="tab" aria-controls="unread" aria-selected="false">
                                        Unread <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="leave-tab" data-bs-toggle="tab" data-bs-target="#leave" 
                                            type="button" role="tab" aria-controls="leave" aria-selected="false">
                                        Leave Applications <span class="badge bg-primary ms-1">{{ $notifications->where('data.action', 'applied')->count() }}</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="notificationTabContent">
                            <!-- All Notifications Tab -->
                            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                                @include('EmployeeManagemntsystem.Admin.notifications.partials.notification-list', ['notificationItems' => $notifications])
                            </div>
                            
                            <!-- Unread Notifications Tab -->
                            <div class="tab-pane fade" id="unread" role="tabpanel" aria-labelledby="unread-tab">
                                @include('EmployeeManagemntsystem.Admin.notifications.partials.notification-list', ['notificationItems' => $notifications->where('read_at', null)])
                            </div>
                            
                            <!-- Leave Applications Tab -->
                            <div class="tab-pane fade" id="leave" role="tabpanel" aria-labelledby="leave-tab">
                                @include('EmployeeManagemntsystem.Admin.notifications.partials.notification-list', ['notificationItems' => $notifications->where('data.action', 'applied')])
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

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.notification-message {
    color: #6b7280;
    font-size: 0.875rem;
    line-height: 1.4;
    margin-bottom: 0.5rem;
}

.notification-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.75rem;
    color: #9ca3af;
}

.notification-actions {
    display: flex;
    gap: 0.5rem;
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

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state i {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
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
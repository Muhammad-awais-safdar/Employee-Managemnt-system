@extends('EmployeeManagemntsystem.Layout.employee')

@section('title', 'My Notifications')

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
                    <li class="breadcrumb-item active fw-medium" aria-current="page">My Notifications</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">My Notifications</h4>
                    <p class="text-muted mb-0">Stay updated with your leave applications and company announcements</p>
                </div>
                <div class="notification-actions">
                    <button class="btn btn-primary btn-sm" onclick="markAllAsRead()">
                        <i class="ti ti-check-all me-1"></i>Mark All Read
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Notification Stats -->
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                            <i class="ti ti-bell"></i>
                        </div>
                        <span class="badge bg-primary">Total</span>
                    </div>
                    <h3 class="stats-number text-primary mb-1">{{ $notifications->total() }}</h3>
                    <p class="stats-label text-muted mb-0">All Notifications</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                            <i class="ti ti-bell-ringing"></i>
                        </div>
                        <span class="badge bg-warning">New</span>
                    </div>
                    <h3 class="stats-number text-warning mb-1">{{ $unreadCount }}</h3>
                    <p class="stats-label text-muted mb-0">Unread Notifications</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stats-icon bg-success bg-opacity-10 text-success">
                            <i class="ti ti-calendar-check"></i>
                        </div>
                        <span class="badge bg-success">Leave</span>
                    </div>
                    <h3 class="stats-number text-success mb-1">{{ $notifications->whereIn('data.action', ['approved', 'rejected'])->count() }}</h3>
                    <p class="stats-label text-muted mb-0">Leave Updates</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Leave Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card quick-actions-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="quick-action-icon bg-info bg-opacity-10 text-info">
                                    <i class="ti ti-calendar-plus"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">Need to apply for leave?</h6>
                                <p class="text-muted mb-0">Submit your leave application quickly and track its status</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('Employee.leave.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>Apply for Leave
                            </a>
                            <a href="{{ route('Employee.leave.index') }}" class="btn btn-outline-primary">
                                <i class="ti ti-list me-1"></i>View My Leaves
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card notifications-card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Notification History</h5>
                        <ul class="nav nav-pills nav-sm" id="notificationTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-employee-tab" data-bs-toggle="pill" data-bs-target="#all-employee" 
                                        type="button" role="tab" aria-controls="all-employee" aria-selected="true">
                                    All <span class="badge bg-secondary ms-1">{{ $notifications->total() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="unread-employee-tab" data-bs-toggle="pill" data-bs-target="#unread-employee" 
                                        type="button" role="tab" aria-controls="unread-employee" aria-selected="false">
                                    Unread <span class="badge bg-warning ms-1">{{ $unreadCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="leave-employee-tab" data-bs-toggle="pill" data-bs-target="#leave-employee" 
                                        type="button" role="tab" aria-controls="leave-employee" aria-selected="false">
                                    Leave Updates <span class="badge bg-primary ms-1">{{ $notifications->whereIn('data.action', ['approved', 'rejected'])->count() }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="tab-content" id="notificationTabContent">
                        <!-- All Notifications Tab -->
                        <div class="tab-pane fade show active" id="all-employee" role="tabpanel" aria-labelledby="all-employee-tab">
                            @include('EmployeeManagemntsystem.Employee.notifications.partials.notification-list', ['notificationItems' => $notifications])
                        </div>
                        
                        <!-- Unread Notifications Tab -->
                        <div class="tab-pane fade" id="unread-employee" role="tabpanel" aria-labelledby="unread-employee-tab">
                            @include('EmployeeManagemntsystem.Employee.notifications.partials.notification-list', ['notificationItems' => $notifications->where('read_at', null)])
                        </div>
                        
                        <!-- Leave Updates Tab -->
                        <div class="tab-pane fade" id="leave-employee" role="tabpanel" aria-labelledby="leave-employee-tab">
                            @include('EmployeeManagemntsystem.Employee.notifications.partials.notification-list', ['notificationItems' => $notifications->whereIn('data.action', ['approved', 'rejected'])])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.stats-card {
    border-radius: 16px;
    transition: all 0.3s ease;
    border: 1px solid rgba(226, 232, 240, 0.6);
}

.stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

.stats-label {
    font-size: 0.875rem;
    font-weight: 500;
}

.quick-actions-card {
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.6);
}

.quick-action-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
}

.notifications-card {
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.6);
}

.nav-pills .nav-link {
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.nav-pills .nav-link.active {
    background-color: #6366f1;
}

.notification-item {
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
    padding: 1.25rem;
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

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state i {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .stats-card .card-body {
        padding: 1rem;
    }
    
    .stats-number {
        font-size: 1.5rem;
    }
    
    .quick-actions-card .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
    
    .notification-item {
        padding: 1rem;
    }
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
            
            // Update badge counts
            updateBadgeCounts();
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
            setTimeout(() => {
                element.remove();
                updateBadgeCounts();
                
                // Check if no notifications left in current tab
                const activeTab = document.querySelector('.tab-pane.active');
                const remainingNotifications = activeTab.querySelectorAll('.notification-item').length;
                if (remainingNotifications === 0) {
                    activeTab.innerHTML = `
                        <div class="empty-state">
                            <i class="ti ti-bell-off"></i>
                            <h5 class="text-muted">No notifications found</h5>
                            <p class="text-muted mb-0">You're all caught up!</p>
                        </div>
                    `;
                }
            }, 300);
            toastr.success('Notification deleted', 'Success');
        }
    } catch (error) {
        console.error('Error:', error);
        toastr.error('Failed to delete notification', 'Error');
    }
}

// Update badge counts dynamically
function updateBadgeCounts() {
    const totalCount = document.querySelectorAll('.notification-item').length;
    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
    const leaveCount = document.querySelectorAll('.notification-item[data-action="approved"], .notification-item[data-action="rejected"]').length;
    
    // Update tab badges
    document.querySelector('#all-employee-tab .badge').textContent = totalCount;
    document.querySelector('#unread-employee-tab .badge').textContent = unreadCount;
    document.querySelector('#leave-employee-tab .badge').textContent = leaveCount;
    
    // Update stats cards
    document.querySelector('.stats-number.text-primary').textContent = totalCount;
    document.querySelector('.stats-number.text-warning').textContent = unreadCount;
    document.querySelector('.stats-number.text-success').textContent = leaveCount;
}
</script>
@endsection
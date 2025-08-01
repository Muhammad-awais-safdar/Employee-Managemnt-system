<!-- Notification Dropdown Component -->
<div class="header-item">
    <div class="dropdown me-2">
        <button class="topbar-link btn btn-icon topbar-link dropdown-toggle drop-arrow-none" 
                data-bs-toggle="dropdown" 
                data-bs-offset="0,24" 
                type="button" 
                aria-haspopup="false" 
                aria-expanded="false">
            <i class="ti ti-bell fs-16 animate-ring"></i>
            <span class="notification-badge" id="notificationBadge" style="display: none;"></span>
        </button>
        
        <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg" style="min-height: 300px; max-width: 400px;">
            <!-- Header -->
            <div class="p-3 border-bottom bg-light">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 fs-16 fw-semibold">Notifications</h6>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">
                            Mark All Read
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Notification Body -->
            <div class="notification-body position-relative z-2 rounded-0" 
                 data-simplebar 
                 style="max-height: 400px;"
                 id="notificationContainer">
                
                <!-- Loading State -->
                <div class="text-center py-4" id="notificationLoading">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2 mb-0">Loading notifications...</p>
                </div>
                
                <!-- Empty State -->
                <div class="text-center py-5" id="notificationEmpty" style="display: none;">
                    <div class="empty-icon mb-3">
                        <i class="ti ti-bell-off text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                    <h6 class="text-muted mb-1">No notifications</h6>
                    <p class="text-muted small mb-0">You're all caught up!</p>
                </div>
                
                <!-- Dynamic Notifications will be loaded here -->
            </div>
            
            <!-- Footer -->
            <div class="p-2 border-top text-center">
                @php
                    $role = auth()->check() ? Auth::user()->getRoleNames()->first() : null;
                    $notificationRoute = $role && Route::has($role . '.notifications.index') 
                        ? route($role . '.notifications.index') 
                        : route('notifications.index');
                @endphp
                <a href="{{ $notificationRoute }}" 
                   class="btn btn-sm btn-link text-primary">
                    View All Notifications
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Notification Badge Styles */
.notification-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 10px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    animation: pulse-badge 2s infinite;
}

@keyframes pulse-badge {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Notification Item Styles */
.notification-item {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    position: relative;
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

.notification-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
}

.notification-time {
    color: #6b7280;
    font-size: 0.75rem;
}

.notification-actions {
    display: flex;
    gap: 0.25rem;
}

.notification-action-btn {
    background: none;
    border: none;
    color: #6b7280;
    font-size: 0.875rem;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.notification-action-btn:hover {
    background-color: #f3f4f6;
    color: #374151;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.notification-icon.bg-primary {
    background-color: rgba(99, 102, 241, 0.1);
    color: #6366f1;
}

.notification-icon.bg-success {
    background-color: rgba(34, 197, 94, 0.1);
    color: #22c55e;
}

.notification-icon.bg-danger {
    background-color: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.notification-icon.bg-warning {
    background-color: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
}

.notification-message {
    color: #6b7280;
    font-size: 0.8125rem;
    line-height: 1.4;
    margin-bottom: 0;
}

/* Animation for new notifications */
@keyframes slideInNotification {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-item.new {
    animation: slideInNotification 0.3s ease-out;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dropdown-menu-lg {
        max-width: 90vw;
        left: -200px !important;
    }
    
    .notification-item {
        padding: 0.75rem !important;
    }
    
    .notification-actions {
        margin-top: 0.5rem;
    }
}
</style>

<script>
// Notification management functionality
let notificationPollingInterval;
let lastNotificationCheck = Date.now();

// Initialize notifications when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    startNotificationPolling();
});

// Load notifications from server
async function loadNotifications() {
    try {
        const response = await fetch('{{ route("notifications.index") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (!response.ok) throw new Error('Failed to load notifications');
        
        const data = await response.json();
        renderNotifications(data.notifications);
        updateNotificationBadge(data.unread_count);
        
    } catch (error) {
        console.error('Error loading notifications:', error);
        showNotificationError();
    }
}

// Render notifications in the dropdown
function renderNotifications(notifications) {
    const container = document.getElementById('notificationContainer');
    const loading = document.getElementById('notificationLoading');
    const empty = document.getElementById('notificationEmpty');
    
    // Hide loading
    loading.style.display = 'none';
    
    if (notifications.length === 0) {
        empty.style.display = 'block';
        return;
    }
    
    empty.style.display = 'none';
    
    const notificationsHtml = notifications.map(notification => {
        const data = notification.data;
        const isUnread = !notification.read_at;
        const timeAgo = getTimeAgo(notification.created_at);
        
        return `
            <div class="dropdown-item notification-item py-3 text-wrap border-bottom ${isUnread ? 'unread' : ''}" 
                 data-notification-id="${notification.id}">
                <div class="d-flex">
                    <div class="notification-icon bg-${data.color || 'primary'}">
                        <i class="${data.icon || 'ti ti-bell'}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${data.title}</div>
                        <div class="notification-message">${data.message}</div>
                        <div class="notification-meta">
                            <span class="notification-time">
                                <i class="ti ti-clock me-1"></i>${timeAgo}
                            </span>
                            <div class="notification-actions">
                                ${isUnread ? `
                                    <button class="notification-action-btn" onclick="markAsRead('${notification.id}')" 
                                            title="Mark as read">
                                        <i class="ti ti-check"></i>
                                    </button>
                                ` : ''}
                                <button class="notification-action-btn" onclick="deleteNotification('${notification.id}')" 
                                        title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    container.innerHTML = `
        <div id="notificationLoading" style="display: none;">
            <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-2 mb-0">Loading notifications...</p>
            </div>
        </div>
        <div id="notificationEmpty" style="display: none;">
            <div class="text-center py-5">
                <div class="empty-icon mb-3">
                    <i class="ti ti-bell-off text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
                <h6 class="text-muted mb-1">No notifications</h6>
                <p class="text-muted small mb-0">You're all caught up!</p>
            </div>
        </div>
        ${notificationsHtml}
    `;
}

// Update notification badge
function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationBadge');
    
    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.style.display = 'flex';
    } else {
        badge.style.display = 'none';
    }
}

// Mark notification as read
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
            const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
            notificationElement.classList.remove('unread');
            
            // Remove the mark as read button
            const markReadBtn = notificationElement.querySelector('.notification-action-btn[onclick*="markAsRead"]');
            if (markReadBtn) markReadBtn.remove();
            
            // Update badge count
            const currentBadge = document.getElementById('notificationBadge');
            const currentCount = parseInt(currentBadge.textContent) || 0;
            updateNotificationBadge(Math.max(0, currentCount - 1));
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

// Mark all notifications as read
async function markAllAsRead() {
    try {
        const response = await fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            // Remove unread class from all notifications
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
                const markReadBtn = item.querySelector('.notification-action-btn[onclick*="markAsRead"]');
                if (markReadBtn) markReadBtn.remove();
            });
            
            // Update badge
            updateNotificationBadge(0);
        }
    } catch (error) {
        console.error('Error marking all notifications as read:', error);
    }
}

// Delete notification
async function deleteNotification(notificationId) {
    if (!confirm('Are you sure you want to delete this notification?')) return;
    
    try {
        const response = await fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
            
            // Check if it was unread before removing
            const wasUnread = notificationElement.classList.contains('unread');
            
            // Remove element with animation
            notificationElement.style.opacity = '0';
            notificationElement.style.transform = 'translateX(100%)';
            
            setTimeout(() => {
                notificationElement.remove();
                
                // Update badge count if notification was unread
                if (wasUnread) {
                    const currentBadge = document.getElementById('notificationBadge');
                    const currentCount = parseInt(currentBadge.textContent) || 0;
                    updateNotificationBadge(Math.max(0, currentCount - 1));
                }
                
                // Check if no notifications left
                const remainingNotifications = document.querySelectorAll('.notification-item').length;
                if (remainingNotifications === 0) {
                    document.getElementById('notificationEmpty').style.display = 'block';
                }
            }, 300);
        }
    } catch (error) {
        console.error('Error deleting notification:', error);
    }
}

// Start polling for new notifications every 30 seconds
function startNotificationPolling() {
    notificationPollingInterval = setInterval(loadNotifications, 30000);
}

// Stop polling (cleanup)
function stopNotificationPolling() {
    if (notificationPollingInterval) {
        clearInterval(notificationPollingInterval);
    }
}

// Helper function to get time ago
function getTimeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;
    
    return date.toLocaleDateString();
}

// Show error state
function showNotificationError() {
    const container = document.getElementById('notificationContainer');
    container.innerHTML = `
        <div class="text-center py-5">
            <div class="empty-icon mb-3">
                <i class="ti ti-alert-circle text-danger" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
            <h6 class="text-muted mb-1">Error loading notifications</h6>
            <p class="text-muted small mb-2">Please try refreshing the page</p>
            <button class="btn btn-sm btn-outline-primary" onclick="loadNotifications()">
                <i class="ti ti-refresh me-1"></i>Retry
            </button>
        </div>
    `;
}

// Cleanup when page unloads
window.addEventListener('beforeunload', stopNotificationPolling);
</script>
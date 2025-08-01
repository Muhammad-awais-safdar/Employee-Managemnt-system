<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Get notifications for AJAX requests.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
            
        $unreadCount = $user->unreadNotifications()->count();
        
        if ($request->expectsJson()) {
            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        }
        
        return view('EmployeeManagemntsystem.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Admin notifications page.
     */
    public function adminIndex()
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $unreadCount = $user->unreadNotifications()->count();
        
        return view('EmployeeManagemntsystem.Admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * HR notifications page.
     */
    public function hrIndex()
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $unreadCount = $user->unreadNotifications()->count();
        
        return view('EmployeeManagemntsystem.HR.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Employee notifications page.
     */
    public function employeeIndex()
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $unreadCount = $user->unreadNotifications()->count();
        
        return view('EmployeeManagemntsystem.Employee.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Delete a specific notification.
     */
    public function destroy(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Clear all notifications for the authenticated user.
     */
    public function clearAll()
    {
        Auth::user()->notifications()->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count for badge.
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        
        return response()->json(['count' => $count]);
    }
}
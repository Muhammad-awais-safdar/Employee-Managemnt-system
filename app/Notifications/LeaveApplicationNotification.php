<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Leave;

class LeaveApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $leave;
    protected $action;

    /**
     * Create a new notification instance.
     */
    public function __construct(Leave $leave, $action = 'applied')
    {
        $this->leave = $leave;
        $this->action = $action; // 'applied', 'approved', 'rejected', 'cancelled'
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Only database notifications, no email
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->getMailSubject();
        $greeting = $this->getMailGreeting();
        $line = $this->getMailLine();
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($line)
            ->line('Leave Details:')
            ->line('• Type: ' . $this->leave->leaveType->name)
            ->line('• Duration: ' . $this->leave->date_range)
            ->line('• Days: ' . $this->leave->total_days)
            ->line('• Reason: ' . $this->leave->reason)
            ->action('View Leave Application', $this->getActionUrl())
            ->line('Thank you for using our application!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'leave_application',
            'action' => $this->action,
            'leave_id' => $this->leave->id,
            'application_id' => $this->leave->application_id,
            'employee_name' => $this->leave->user->name,
            'employee_id' => $this->leave->user_id,
            'leave_type' => $this->leave->leaveType->name,
            'leave_type_code' => $this->leave->leaveType->code,
            'start_date' => $this->leave->start_date->format('M d, Y'),
            'end_date' => $this->leave->end_date->format('M d, Y'),
            'total_days' => $this->leave->total_days,
            'duration' => $this->leave->duration_label,
            'status' => $this->leave->status,
            'reason' => $this->leave->reason,
            'emergency_leave' => $this->leave->emergency_leave,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'icon' => $this->getIcon(),
            'color' => $this->getColor(),
            'action_url' => $this->getActionUrl(),
            'company_id' => $this->leave->company_id,
        ];
    }

    /**
     * Get notification title based on action.
     */
    private function getTitle(): string
    {
        return match($this->action) {
            'applied' => 'New Leave Application',
            'approved' => 'Leave Application Approved',
            'rejected' => 'Leave Application Rejected',
            'cancelled' => 'Leave Application Cancelled',
            'withdrawn' => 'Leave Application Withdrawn',
            default => 'Leave Application Update'
        };
    }

    /**
     * Get notification message based on action.
     */
    private function getMessage(): string
    {
        $employeeName = $this->leave->user->name;
        $leaveType = $this->leave->leaveType->name;
        $days = $this->leave->total_days;
        $duration = $days == 1 ? 'day' : 'days';

        return match($this->action) {
            'applied' => "{$employeeName} has applied for {$days} {$duration} of {$leaveType}",
            'approved' => "Your {$leaveType} application for {$days} {$duration} has been approved",
            'rejected' => "Your {$leaveType} application for {$days} {$duration} has been rejected",
            'cancelled' => "{$employeeName} has cancelled their {$leaveType} application",
            'withdrawn' => "{$employeeName} has withdrawn their {$leaveType} application",
            default => "{$employeeName} has updated their leave application"
        };
    }

    /**
     * Get notification icon based on action.
     */
    private function getIcon(): string
    {
        return match($this->action) {
            'applied' => 'ti-calendar-plus',
            'approved' => 'ti-check-circle',
            'rejected' => 'ti-x-circle',
            'cancelled' => 'ti-calendar-x',
            'withdrawn' => 'ti-calendar-minus',
            default => 'ti-calendar'
        };
    }

    /**
     * Get notification color based on action.
     */
    private function getColor(): string
    {
        return match($this->action) {
            'applied' => 'primary',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'warning',
            'withdrawn' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Get action URL for the notification.
     */
    private function getActionUrl(): string
    {
        // Return different URLs based on user role
        if ($this->action === 'applied') {
            // For admins/HR - go to leave management
            return route('Admin.leave.index', ['filter' => 'pending']);
        } else {
            // For employees - go to their leave details
            return route('Employee.leave.show', $this->leave);
        }
    }

    /**
     * Get mail subject based on action.
     */
    private function getMailSubject(): string
    {
        $appName = config('app.name');
        return match($this->action) {
            'applied' => "[{$appName}] New Leave Application Requires Review",
            'approved' => "[{$appName}] Your Leave Application Has Been Approved",
            'rejected' => "[{$appName}] Your Leave Application Has Been Rejected",
            'cancelled' => "[{$appName}] Leave Application Cancelled",
            'withdrawn' => "[{$appName}] Leave Application Withdrawn",
            default => "[{$appName}] Leave Application Update"
        };
    }

    /**
     * Get mail greeting based on action.
     */
    private function getMailGreeting(): string
    {
        return match($this->action) {
            'applied' => 'Hello,',
            'approved' => 'Good news!',
            'rejected' => 'Hello,',
            'cancelled' => 'Hello,',
            'withdrawn' => 'Hello,',
            default => 'Hello,'
        };
    }

    /**
     * Get mail line based on action.
     */
    private function getMailLine(): string
    {
        $employeeName = $this->leave->user->name;
        
        return match($this->action) {
            'applied' => "A new leave application from {$employeeName} requires your review and approval.",
            'approved' => "Your leave application has been approved and is now confirmed.",
            'rejected' => "We're sorry to inform you that your leave application has been rejected.",
            'cancelled' => "{$employeeName} has cancelled their leave application.",
            'withdrawn' => "{$employeeName} has withdrawn their leave application.",
            default => "There has been an update to a leave application."
        };
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
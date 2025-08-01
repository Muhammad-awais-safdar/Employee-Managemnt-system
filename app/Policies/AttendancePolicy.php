<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class AttendancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // HR, Admin, and SuperAdmin can view attendance records
        return $user->hasAnyRole(['superAdmin', 'Admin', 'HR']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        // SuperAdmin can view all attendance records
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Users can view their own attendance
        if ($user->id === $attendance->user_id) {
            return true;
        }
        
        // Admin and HR can view attendance in their company
        if ($user->hasAnyRole(['Admin', 'HR'])) {
            return $attendance->company_id === $user->company_id;
        }
        
        // Team lead can view their team members' attendance
        if ($user->hasRole('TeamLead')) {
            return $attendance->user->team_lead_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only HR, Admin, and SuperAdmin can manually create/mark attendance
        return $user->hasAnyRole(['superAdmin', 'Admin', 'HR']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        // SuperAdmin can update any attendance record
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Employees can only update their own attendance (check-in/check-out)
        if ($user->id === $attendance->user_id) {
            return true;
        }
        
        // Admin and HR can update attendance in their company
        if ($user->hasAnyRole(['Admin', 'HR'])) {
            return $attendance->company_id === $user->company_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        // SuperAdmin can delete any attendance record
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin can delete attendance records in their company
        if ($user->hasRole('Admin')) {
            return $attendance->company_id === $user->company_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attendance $attendance): bool
    {
        return $this->delete($user, $attendance);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attendance $attendance): bool
    {
        // Only SuperAdmin can permanently delete attendance records
        return $user->hasRole('superAdmin');
    }

    /**
     * Determine whether the user can check in/out.
     */
    public function checkInOut(User $user, ?Attendance $attendance = null): bool
    {
        // All authenticated users can check in/out for themselves
        return true;
    }

    /**
     * Determine whether the user can manage breaks.
     */
    public function manageBreaks(User $user, Attendance $attendance): bool
    {
        // Users can manage breaks for their own attendance
        if ($user->id === $attendance->user_id) {
            return true;
        }
        
        // HR and Admin can manage breaks for their company employees
        if ($user->hasAnyRole(['Admin', 'HR'])) {
            return $attendance->company_id === $user->company_id;
        }
        
        // SuperAdmin can manage breaks for any employee
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can view attendance reports.
     */
    public function viewReports(User $user): bool
    {
        // HR, Admin, and SuperAdmin can view attendance reports
        return $user->hasAnyRole(['superAdmin', 'Admin', 'HR']);
    }

    /**
     * Determine whether the user can export attendance data.
     */
    public function export(User $user): bool
    {
        // HR, Admin, and SuperAdmin can export attendance data
        return $user->hasAnyRole(['superAdmin', 'Admin', 'HR']);
    }

    /**
     * Determine whether the user can mark attendance for others.
     */
    public function markForOthers(User $user, User $targetUser): bool
    {
        // SuperAdmin can mark attendance for anyone
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Users must be from the same company
        if ($targetUser->company_id !== $user->company_id) {
            return false;
        }
        
        // Admin can mark attendance for non-admin users in their company
        if ($user->hasRole('Admin')) {
            return !$targetUser->hasAnyRole(['superAdmin', 'Admin']);
        }
        
        // HR can mark attendance for employees and team leads in their company
        // Allow HR to mark attendance for anyone except SuperAdmin and Admin
        if ($user->hasRole('HR')) {
            return !$targetUser->hasAnyRole(['superAdmin', 'Admin']);
        }
        
        return false;
    }

    /**
     * Determine whether the user can set working hours.
     */
    public function setWorkingHours(User $user): bool
    {
        // Only Admin and SuperAdmin can set working hours
        return $user->hasAnyRole(['superAdmin', 'Admin']);
    }

    /**
     * Determine whether the user can view company attendance summary.
     */
    public function viewCompanySummary(User $user, $companyId = null): bool
    {
        // SuperAdmin can view any company summary
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin and HR can view their own company summary
        if ($user->hasAnyRole(['Admin', 'HR'])) {
            return $companyId ? $companyId === $user->company_id : true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can approve overtime.
     */
    public function approveOvertime(User $user, Attendance $attendance): bool
    {
        // SuperAdmin can approve any overtime
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin and HR can approve overtime in their company
        if ($user->hasAnyRole(['Admin', 'HR'])) {
            return $attendance->company_id === $user->company_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can manage leave requests.
     */
    public function manageLeave(User $user, Attendance $attendance): bool
    {
        // SuperAdmin can manage any leave
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin and HR can manage leave in their company
        if ($user->hasAnyRole(['Admin', 'HR'])) {
            return $attendance->company_id === $user->company_id;
        }
        
        // Team lead can manage leave for their team members
        if ($user->hasRole('TeamLead')) {
            return $attendance->user->team_lead_id === $user->id;
        }
        
        return false;
    }
}
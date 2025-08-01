<?php

namespace App\Policies;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeavePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view leave applications (filtered by role)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Leave $leave): bool
    {
        // SuperAdmin can view all leaves
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Users can view their own leaves
        if ($user->id === $leave->user_id) {
            return true;
        }
        
        // Admin and HR can view leaves in their company
        if ($user->hasAnyRole(['Admin', 'HR'])) {
            return $leave->company_id === $user->company_id;
        }
        
        // Team lead can view their team members' leaves
        if ($user->hasRole('TeamLead')) {
            return $leave->user->team_lead_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All employees can apply for leave
        return $user->hasAnyRole(['Employee', 'TeamLead', 'HR', 'Finance', 'Admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Leave $leave): bool
    {
        // Only the leave applicant can update their own leave
        if ($user->id !== $leave->user_id) {
            return false;
        }
        
        // Can only update pending leaves
        return $leave->status === 'pending' && $leave->canBeEdited();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Leave $leave): bool
    {
        // Users can cancel/withdraw their own leaves
        if ($user->id === $leave->user_id) {
            return $leave->canBeCancelled();
        }
        
        // SuperAdmin can delete any leave
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin can delete leaves in their company
        if ($user->hasRole('Admin')) {
            return $leave->company_id === $user->company_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Leave $leave): bool
    {
        return $this->delete($user, $leave);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Leave $leave): bool
    {
        // Only SuperAdmin can permanently delete leaves
        return $user->hasRole('superAdmin');
    }

    /**
     * Determine whether the user can review (approve/reject) leave applications.
     */
    public function review(User $user, Leave $leave): bool
    {
        // SuperAdmin can review any leave
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Users cannot review their own leaves
        if ($user->id === $leave->user_id) {
            return false;
        }
        
        // Must be from the same company
        if ($leave->company_id !== $user->company_id) {
            return false;
        }
        
        // Admin can review all leaves in their company
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // HR can review leaves except for Admin and SuperAdmin leaves
        if ($user->hasRole('HR')) {
            return !$leave->user->hasAnyRole(['Admin', 'superAdmin']);
        }
        
        // Team lead can review their team members' leaves
        if ($user->hasRole('TeamLead')) {
            return $leave->user->team_lead_id === $user->id 
                && !$leave->user->hasAnyRole(['Admin', 'superAdmin', 'HR']);
        }
        
        return false;
    }

    /**
     * Determine whether the user can manage leave balances.
     */
    public function manageBalances(User $user, ?User $targetUser = null): bool
    {
        // SuperAdmin can manage any balance
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin can manage balances in their company
        if ($user->hasRole('Admin')) {
            return $targetUser ? $targetUser->company_id === $user->company_id : true;
        }
        
        // HR can manage balances for non-admin employees in their company
        if ($user->hasRole('HR')) {
            if (!$targetUser) {
                return true;
            }
            return $targetUser->company_id === $user->company_id 
                && !$targetUser->hasAnyRole(['Admin', 'superAdmin']);
        }
        
        return false;
    }

    /**
     * Determine whether the user can view leave reports.
     */
    public function viewReports(User $user): bool
    {
        // HR, Admin, and SuperAdmin can view leave reports
        return $user->hasAnyRole(['superAdmin', 'Admin', 'HR']);
    }

    /**
     * Determine whether the user can export leave data.
     */
    public function export(User $user): bool
    {
        // HR, Admin, and SuperAdmin can export leave data
        return $user->hasAnyRole(['superAdmin', 'Admin', 'HR']);
    }

    /**
     * Determine whether the user can manage leave types.
     */
    public function manageLeaveTypes(User $user): bool
    {
        // Only Admin and SuperAdmin can manage leave types
        return $user->hasAnyRole(['superAdmin', 'Admin']);
    }

    /**
     * Determine whether the user can view company leave summary.
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
        
        // Team lead can view summary for their team
        if ($user->hasRole('TeamLead')) {
            return $companyId ? $companyId === $user->company_id : true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can approve emergency leaves.
     */
    public function approveEmergencyLeave(User $user, Leave $leave): bool
    {
        // Emergency leaves can be approved by Admin and HR
        if (!$leave->emergency_leave) {
            return $this->review($user, $leave);
        }
        
        // For emergency leaves, more flexible approval
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        if ($user->hasAnyRole(['Admin', 'HR']) && $leave->company_id === $user->company_id) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can bulk approve leaves.
     */
    public function bulkApprove(User $user): bool
    {
        // Admin, HR, and SuperAdmin can bulk approve
        return $user->hasAnyRole(['superAdmin', 'Admin', 'HR']);
    }

    /**
     * Determine whether the user can override leave policies.
     */
    public function overridePolicies(User $user): bool
    {
        // Only SuperAdmin can override leave policies
        return $user->hasRole('superAdmin');
    }

    /**
     * Determine whether the user can view leave calendar.
     */
    public function viewCalendar(User $user): bool
    {
        // All authenticated users can view leave calendar (filtered by permissions)
        return true;
    }

    /**
     * Determine whether the user can add leave on behalf of others.
     */
    public function createForOthers(User $user, User $targetUser): bool
    {
        // SuperAdmin can create leave for anyone
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Users must be from the same company
        if ($targetUser->company_id !== $user->company_id) {
            return false;
        }
        
        // Admin can create leave for non-admin users in their company
        if ($user->hasRole('Admin')) {
            return !$targetUser->hasAnyRole(['superAdmin', 'Admin']);
        }
        
        // HR can create leave for employees in their company
        if ($user->hasRole('HR')) {
            return $targetUser->hasAnyRole(['Employee', 'TeamLead', 'Finance']);
        }
        
        return false;
    }

    /**
     * Determine whether the user can cancel approved leaves.
     */
    public function cancelApproved(User $user, Leave $leave): bool
    {
        // SuperAdmin can cancel any approved leave
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin can cancel approved leaves in their company
        if ($user->hasRole('Admin') && $leave->company_id === $user->company_id) {
            return true;
        }
        
        // Users can cancel their own approved leaves if they haven't started
        if ($user->id === $leave->user_id) {
            return $leave->canBeCancelled();
        }
        
        return false;
    }
}
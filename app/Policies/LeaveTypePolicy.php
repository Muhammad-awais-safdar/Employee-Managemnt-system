<?php

namespace App\Policies;

use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeaveTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only Admin, HR, and SuperAdmin can manage leave types
        return $user->hasAnyRole(['superAdmin', 'Admin', 'HR']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveType $leaveType): bool
    {
        // SuperAdmin can view all leave types
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin and HR can view leave types from their company
        if ($user->hasAnyRole(['Admin', 'HR'])) {
            return $user->company_id === $leaveType->company_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Admin and SuperAdmin can create leave types
        return $user->hasAnyRole(['superAdmin', 'Admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeaveType $leaveType): bool
    {
        // SuperAdmin can update all leave types
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin can update leave types from their company
        if ($user->hasRole('Admin')) {
            return $user->company_id === $leaveType->company_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveType $leaveType): bool
    {
        // SuperAdmin can delete all leave types
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin can delete leave types from their company
        if ($user->hasRole('Admin')) {
            return $user->company_id === $leaveType->company_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LeaveType $leaveType): bool
    {
        return $this->delete($user, $leaveType);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LeaveType $leaveType): bool
    {
        return $this->delete($user, $leaveType);
    }
}
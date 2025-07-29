<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['superAdmin', 'Admin', 'HR', 'TeamLead']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Super admin can view all users
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Users can view themselves
        if ($user->id === $model->id) {
            return true;
        }
        
        // Admin can view users in their company (except super admins)
        if ($user->hasRole('Admin')) {
            return $model->company_id === $user->company_id && 
                   !$model->hasRole('superAdmin');
        }
        
        // HR can view team leads and employees in their company
        if ($user->hasRole('HR')) {
            return $model->company_id === $user->company_id && 
                   $model->hasAnyRole(['TeamLead', 'Employee']);
        }
        
        // Team lead can view their team members
        if ($user->hasRole('TeamLead')) {
            return $model->team_lead_id === $user->id && 
                   $model->hasRole('Employee');
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['superAdmin', 'Admin', 'HR']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Super admin can update all users except they can't demote themselves
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Users can update themselves (basic info only)
        if ($user->id === $model->id) {
            return true;
        }
        
        // Admin can update users in their company (except super admins and other admins)
        if ($user->hasRole('Admin')) {
            return $model->company_id === $user->company_id && 
                   !$model->hasAnyRole(['superAdmin', 'Admin']);
        }
        
        // HR can update team leads and employees in their company
        if ($user->hasRole('HR')) {
            return $model->company_id === $user->company_id && 
                   $model->hasAnyRole(['TeamLead', 'Employee']);
        }
        
        // Team lead can update their team members
        if ($user->hasRole('TeamLead')) {
            return $model->team_lead_id === $user->id && 
                   $model->hasRole('Employee');
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Can't delete yourself
        if ($user->id === $model->id) {
            return false;
        }
        
        // Super admin can delete all users
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin can delete users in their company (except super admins and other admins)
        if ($user->hasRole('Admin')) {
            return $model->company_id === $user->company_id && 
                   !$model->hasAnyRole(['superAdmin', 'Admin']);
        }
        
        // HR can delete team leads and employees in their company
        if ($user->hasRole('HR')) {
            return $model->company_id === $user->company_id && 
                   $model->hasAnyRole(['TeamLead', 'Employee']);
        }
        
        // Team lead can soft delete their team members
        if ($user->hasRole('TeamLead')) {
            return $model->team_lead_id === $user->id && 
                   $model->hasRole('Employee');
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Only super admin can permanently delete users
        return $user->hasRole('superAdmin') && $user->id !== $model->id;
    }
}
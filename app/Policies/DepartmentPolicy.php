<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['HR', 'Admin', 'superAdmin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Department $department): bool
    {
        if (!$user->hasAnyRole(['HR', 'Admin', 'superAdmin'])) {
            return false;
        }
        
        // SuperAdmin can view all departments
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // HR and Admin can only view departments from their company
        return $department->company_id === $user->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only SuperAdmin and Admin can create departments
        return $user->hasAnyRole(['Admin', 'superAdmin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Department $department): bool
    {
        if (!$user->hasAnyRole(['Admin', 'superAdmin'])) {
            return false;
        }
        
        // SuperAdmin can update all departments
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin can only update departments from their company
        return $department->company_id === $user->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Department $department): bool
    {
        if (!$user->hasAnyRole(['Admin', 'superAdmin'])) {
            return false;
        }
        
        // SuperAdmin can delete all departments
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin can only delete departments from their company
        return $department->company_id === $user->company_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Department $department): bool
    {
        return $user->hasRole('superAdmin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Department $department): bool
    {
        return $user->hasRole('superAdmin');
    }
    
    /**
     * Determine whether the user can assign users to departments.
     */
    public function assignUsers(User $user): bool
    {
        return $user->hasAnyRole(['HR', 'Admin', 'superAdmin']);
    }
    
    /**
     * Determine whether the user can assign a specific user to a department.
     */
    public function assignUser(User $user, $targetUser, Department $department = null): bool
    {
        if (!$user->hasAnyRole(['HR', 'Admin', 'superAdmin'])) {
            return false;
        }
        
        // SuperAdmin can assign anyone to any department
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Users must be from the same company
        if ($targetUser->company_id !== $user->company_id) {
            return false;
        }
        
        // If department is specified, it must be from the same company
        if ($department && $department->company_id !== $user->company_id) {
            return false;
        }
        
        // HR cannot assign Admin or SuperAdmin users
        if ($user->hasRole('HR') && $targetUser->hasAnyRole(['Admin', 'superAdmin', 'HR'])) {
            return false;
        }
        
        return true;
    }
}

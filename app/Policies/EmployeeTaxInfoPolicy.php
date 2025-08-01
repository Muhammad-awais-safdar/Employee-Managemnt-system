<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EmployeeTaxInfo;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeeTaxInfoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any employee tax information.
     */
    public function viewAny(User $user)
    {
        // SuperAdmin, Admin, Finance, and HR can view employee tax info
        return $user->hasAnyRole(['superAdmin', 'Admin', 'Finance', 'HR']);
    }

    /**
     * Determine whether the user can view the employee tax information.
     */
    public function view(User $user, EmployeeTaxInfo $employeeTaxInfo)
    {
        // SuperAdmin can view any employee tax info
        if ($user->hasRole('superAdmin')) {
            return true;
        }

        // Admin, Finance, and HR can view tax info for employees in their company
        if ($user->hasAnyRole(['Admin', 'Finance', 'HR'])) {
            return $user->company_id === $employeeTaxInfo->company_id;
        }

        // Employees can view their own tax information
        if ($user->hasRole('Employee')) {
            return $user->id === $employeeTaxInfo->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create employee tax information.
     */
    public function create(User $user)
    {
        // SuperAdmin, Admin, HR, and Finance can create employee tax info
        return $user->hasAnyRole(['superAdmin', 'Admin', 'HR', 'Finance']);
    }

    /**
     * Determine whether the user can update the employee tax information.
     */
    public function update(User $user, EmployeeTaxInfo $employeeTaxInfo)
    {
        // SuperAdmin can update any employee tax info
        if ($user->hasRole('superAdmin')) {
            return true;
        }

        // Admin, Finance, and HR can update tax info for employees in their company
        if ($user->hasAnyRole(['Admin', 'Finance', 'HR'])) {
            return $user->company_id === $employeeTaxInfo->company_id;
        }

        // Employees can update their own tax information (limited fields)
        if ($user->hasRole('Employee')) {
            return $user->id === $employeeTaxInfo->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the employee tax information.
     */
    public function delete(User $user, EmployeeTaxInfo $employeeTaxInfo)
    {
        // SuperAdmin can delete any employee tax info
        if ($user->hasRole('superAdmin')) {
            return true;
        }

        // Admin and Finance can delete tax info for employees in their company
        if ($user->hasAnyRole(['Admin', 'Finance'])) {
            return $user->company_id === $employeeTaxInfo->company_id;
        }

        // HR typically shouldn't delete tax info
        return false;
    }

    /**
     * Determine whether the user can restore the employee tax information.
     */
    public function restore(User $user, EmployeeTaxInfo $employeeTaxInfo)
    {
        return $this->update($user, $employeeTaxInfo);
    }

    /**
     * Determine whether the user can permanently delete the employee tax information.
     */
    public function forceDelete(User $user, EmployeeTaxInfo $employeeTaxInfo)
    {
        // Only SuperAdmin can permanently delete employee tax info
        return $user->hasRole('superAdmin');
    }
}
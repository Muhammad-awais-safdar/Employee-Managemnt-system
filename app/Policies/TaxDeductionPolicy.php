<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TaxDeduction;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxDeductionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tax deductions.
     */
    public function viewAny(User $user)
    {
        // SuperAdmin, Admin, Finance, and HR can view tax deductions
        return $user->hasAnyRole(['superAdmin', 'Admin', 'Finance', 'HR']);
    }

    /**
     * Determine whether the user can view the tax deduction.
     */
    public function view(User $user, TaxDeduction $taxDeduction)
    {
        // SuperAdmin can view any tax deduction
        if ($user->hasRole('superAdmin')) {
            return true;
        }

        // Admin, Finance, and HR can view tax deductions for their company
        if ($user->hasAnyRole(['Admin', 'Finance', 'HR'])) {
            return $user->company_id === $taxDeduction->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create tax deductions.
     */
    public function create(User $user)
    {
        // SuperAdmin, Admin, and Finance can create tax deductions
        return $user->hasAnyRole(['superAdmin', 'Admin', 'Finance']);
    }

    /**
     * Determine whether the user can update the tax deduction.
     */
    public function update(User $user, TaxDeduction $taxDeduction)
    {
        // SuperAdmin can update any tax deduction
        if ($user->hasRole('superAdmin')) {
            return true;
        }

        // Admin and Finance can update tax deductions for their company
        if ($user->hasAnyRole(['Admin', 'Finance'])) {
            return $user->company_id === $taxDeduction->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the tax deduction.
     */
    public function delete(User $user, TaxDeduction $taxDeduction)
    {
        // SuperAdmin can delete any tax deduction
        if ($user->hasRole('superAdmin')) {
            return true;
        }

        // Admin and Finance can delete tax deductions for their company
        if ($user->hasAnyRole(['Admin', 'Finance'])) {
            return $user->company_id === $taxDeduction->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the tax deduction.
     */
    public function restore(User $user, TaxDeduction $taxDeduction)
    {
        return $this->update($user, $taxDeduction);
    }

    /**
     * Determine whether the user can permanently delete the tax deduction.
     */
    public function forceDelete(User $user, TaxDeduction $taxDeduction)
    {
        // Only SuperAdmin can permanently delete tax deductions
        return $user->hasRole('superAdmin');
    }
}
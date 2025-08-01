<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TaxRate;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxRatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tax rates.
     */
    public function viewAny(User $user)
    {
        // SuperAdmin, Admin, and Finance can view tax rates
        return $user->hasAnyRole(['superAdmin', 'Admin', 'Finance']);
    }

    /**
     * Determine whether the user can view the tax rate.
     */
    public function view(User $user, TaxRate $taxRate)
    {
        // SuperAdmin can view any tax rate
        if ($user->hasRole('superAdmin')) {
            return true;
        }

        // Admin and Finance can view tax rates for their company
        if ($user->hasAnyRole(['Admin', 'Finance'])) {
            return $user->company_id === $taxRate->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create tax rates.
     */
    public function create(User $user)
    {
        // SuperAdmin, Admin, and Finance can create tax rates
        return $user->hasAnyRole(['superAdmin', 'Admin', 'Finance']);
    }

    /**
     * Determine whether the user can update the tax rate.
     */
    public function update(User $user, TaxRate $taxRate)
    {
        // SuperAdmin can update any tax rate
        if ($user->hasRole('superAdmin')) {
            return true;
        }

        // Admin and Finance can update tax rates for their company
        if ($user->hasAnyRole(['Admin', 'Finance'])) {
            return $user->company_id === $taxRate->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the tax rate.
     */
    public function delete(User $user, TaxRate $taxRate)
    {
        // SuperAdmin can delete any tax rate
        if ($user->hasRole('superAdmin')) {
            return true;
        }

        // Admin and Finance can delete tax rates for their company
        if ($user->hasAnyRole(['Admin', 'Finance'])) {
            return $user->company_id === $taxRate->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the tax rate.
     */
    public function restore(User $user, TaxRate $taxRate)
    {
        return $this->update($user, $taxRate);
    }

    /**
     * Determine whether the user can permanently delete the tax rate.
     */
    public function forceDelete(User $user, TaxRate $taxRate)
    {
        // Only SuperAdmin can permanently delete tax rates
        return $user->hasRole('superAdmin');
    }
}
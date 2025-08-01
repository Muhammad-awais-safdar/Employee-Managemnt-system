<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TaxBracket;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxBracketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tax brackets.
     */
    public function viewAny(User $user)
    {
        // SuperAdmin, Admin, and Finance can view tax brackets
        return $user->hasAnyRole(['superAdmin', 'Admin', 'Finance']);
    }

    /**
     * Determine whether the user can view the tax bracket.
     */
    public function view(User $user, TaxBracket $taxBracket)
    {
        // SuperAdmin can view any tax bracket
        if ($user->hasRole('superAdmin')) {
            return true;
        }

        // Admin and Finance can view tax brackets for their company
        if ($user->hasAnyRole(['Admin', 'Finance'])) {
            return $user->company_id === $taxBracket->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create tax brackets.
     */
    public function create(User $user)
    {
        // SuperAdmin, Admin, and Finance can create tax brackets
        return $user->hasAnyRole(['superAdmin', 'Admin', 'Finance']);
    }

    /**
     * Determine whether the user can update the tax bracket.
     */
    public function update(User $user, TaxBracket $taxBracket)
    {
        // SuperAdmin can update any tax bracket
        if ($user->hasRole('superAdmin')) {
            return true;
        }

        // Admin and Finance can update tax brackets for their company
        if ($user->hasAnyRole(['Admin', 'Finance'])) {
            return $user->company_id === $taxBracket->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the tax bracket.
     */
    public function delete(User $user, TaxBracket $taxBracket)
    {
        // SuperAdmin can delete any tax bracket
        if ($user->hasRole('superAdmin')) {
            return true;
        }

        // Admin and Finance can delete tax brackets for their company
        if ($user->hasAnyRole(['Admin', 'Finance'])) {
            return $user->company_id === $taxBracket->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the tax bracket.
     */
    public function restore(User $user, TaxBracket $taxBracket)
    {
        return $this->update($user, $taxBracket);
    }

    /**
     * Determine whether the user can permanently delete the tax bracket.
     */
    public function forceDelete(User $user, TaxBracket $taxBracket)
    {
        // Only SuperAdmin can permanently delete tax brackets
        return $user->hasRole('superAdmin');
    }
}
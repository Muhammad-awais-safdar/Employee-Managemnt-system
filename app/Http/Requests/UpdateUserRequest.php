<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        $authUser = $this->user();
        $targetUser = $this->route('user');
        
        \Log::info('UpdateUserRequest authorization check', [
            'auth_user_id' => $authUser->id,
            'auth_user_roles' => $authUser->getRoleNames()->toArray(),
            'target_user_id' => $targetUser->id,
            'target_user_roles' => $targetUser->getRoleNames()->toArray(),
            'request_data' => $this->except(['password', '_token'])
        ]);
        
        // Super admin can update any user
        if ($authUser->hasRole('superAdmin')) {
            \Log::info('UpdateUserRequest: SuperAdmin authorized');
            return true;
        }
        
        // Admin can update users in their company (except super admins and other admins)
        if ($authUser->hasRole('Admin')) {
            if ($targetUser->company_id !== $authUser->company_id) {
                return false;
            }
            if ($targetUser->hasRole('superAdmin') || $targetUser->hasRole('Admin')) {
                return false;
            }
            
            // Check if trying to assign forbidden role
            if ($this->role) {
                $requestedRole = Role::find($this->role);
                if (!$requestedRole) {
                    return false;
                }
                $allowedRoles = ['HR', 'Finance', 'TeamLead', 'Employee'];
                return in_array($requestedRole->name, $allowedRoles);
            }
            return true;
        }
        
        // HR can update team leads and employees in their company
        if ($authUser->hasRole('HR')) {
            \Log::info('UpdateUserRequest: HR authorization check', [
                'target_company' => $targetUser->company_id,
                'auth_company' => $authUser->company_id,
                'target_roles' => $targetUser->getRoleNames()->toArray(),
                'requested_role_id' => $this->role
            ]);
            
            if ($targetUser->company_id !== $authUser->company_id) {
                \Log::warning('UpdateUserRequest: HR authorization failed - different company');
                return false;
            }
            if (!$targetUser->hasRole('TeamLead') && !$targetUser->hasRole('Employee')) {
                \Log::warning('UpdateUserRequest: HR authorization failed - invalid target role');
                return false;
            }
            
            if ($this->role) {
                $requestedRole = Role::find($this->role);
                if (!$requestedRole) {
                    \Log::warning('UpdateUserRequest: HR authorization failed - invalid role ID');
                    return false;
                }
                $allowedRoles = ['TeamLead', 'Employee'];
                $authorized = in_array($requestedRole->name, $allowedRoles);
                \Log::info('UpdateUserRequest: HR role check result', [
                    'requested_role' => $requestedRole->name,
                    'allowed_roles' => $allowedRoles,
                    'authorized' => $authorized
                ]);
                return $authorized;
            }
            \Log::info('UpdateUserRequest: HR authorized - no role change');
            return true;
        }
        
        // Team lead can update their team members
        if ($authUser->hasRole('TeamLead')) {
            return $targetUser->team_lead_id === $authUser->id && 
                   $targetUser->hasRole('Employee');
        }
        
        return false;
    }

    public function rules()
    {
        $user = $this->user();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->route('user')->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'team_lead_id' => ['nullable', 'exists:users,id'],
        ];
        
        // Team leads can't change roles
        if (!$user->hasRole('TeamLead')) {
            $rules['role'] = ['required', 'exists:roles,id'];
        }
        
        // Super admin can assign any company
        if ($user->hasRole('superAdmin')) {
            $rules['company_id'] = ['required', 'exists:companies,id'];
        } elseif (!$user->hasRole('TeamLead')) {
            // Other roles (except team lead) are restricted to their own company
            $rules['company_id'] = ['required', Rule::in([$user->company_id])];
        }
        
        return $rules;
    }
}

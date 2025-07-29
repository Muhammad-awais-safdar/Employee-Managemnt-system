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
        
        // Super admin can update any user
        if ($authUser->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin can update users in their company (except super admins and other admins)
        if ($authUser->hasRole('admin')) {
            if ($targetUser->company_id !== $authUser->company_id) {
                return false;
            }
            if ($targetUser->hasRole('superAdmin') || $targetUser->hasRole('admin')) {
                return false;
            }
            
            // Check if trying to assign forbidden roles  
            $requestedRoles = Role::whereIn('id', $this->roles ?? [])->pluck('name');
            $allowedRoles = ['hr', 'finance', 'team_lead', 'employee'];
            return $requestedRoles->every(fn($role) => in_array($role, $allowedRoles));
        }
        
        // HR can update team leads and employees in their company
        if ($authUser->hasRole('hr')) {
            if ($targetUser->company_id !== $authUser->company_id) {
                return false;
            }
            if (!$targetUser->hasRole('team_lead') && !$targetUser->hasRole('employee')) {
                return false;
            }
            
            $requestedRoles = Role::whereIn('id', $this->roles ?? [])->pluck('name');
            $allowedRoles = ['team_lead', 'employee'];
            return $requestedRoles->every(fn($role) => in_array($role, $allowedRoles));
        }
        
        // Team lead can update their team members
        if ($authUser->hasRole('team_lead')) {
            return $targetUser->team_lead_id === $authUser->id && 
                   $targetUser->hasRole('employee');
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
        if (!$user->hasRole('team_lead')) {
            $rules['roles'] = ['required', 'array'];
            $rules['roles.*'] = ['exists:roles,name']; // ✅ expects role names like "Employee"

        }
        
        // Super admin can assign any company
        if ($user->hasRole('superAdmin')) {
            $rules['company_id'] = ['required', 'exists:companies,id'];
        } elseif (!$user->hasRole('team_lead')) {
            // Other roles (except team lead) are restricted to their own company
            $rules['company_id'] = ['required', Rule::in([$user->company_id])];
        }
        
        return $rules;
    }
}

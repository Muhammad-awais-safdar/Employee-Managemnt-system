<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->user();
        
        // Super admin can create any user
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        // Admin can create HR, Finance, Team Lead, Employee
        if ($user->hasRole('admin')) {
            $requestedRoles = Role::whereIn('id', $this->roles ?? [])->pluck('name');
            $allowedRoles = ['hr', 'finance', 'team_lead', 'employee'];
            return $requestedRoles->every(fn($role) => in_array($role, $allowedRoles));
        }
        
        // HR can create Team Lead and Employee
        if ($user->hasRole('hr')) {
            $requestedRoles = Role::whereIn('id', $this->roles ?? [])->pluck('name');
            $allowedRoles = ['team_lead', 'employee'];
            return $requestedRoles->every(fn($role) => in_array($role, $allowedRoles));
        }
        
        return false;
    }

    public function rules()
    {
        $user = $this->user();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
            'team_lead_id' => ['nullable', 'exists:users,id'],
        ];
        
        // Super admin can assign any company
        if ($user->hasRole('superAdmin')) {
            $rules['company_id'] = ['required', 'exists:companies,id'];
        } else {
            // Other roles are restricted to their own company
            $rules['company_id'] = ['required', Rule::in([$user->company_id])];
        }
        
        return $rules;
    }
}

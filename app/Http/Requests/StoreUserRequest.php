<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;

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
        if ($user->hasRole('Admin')) {
            if (!$this->role) {
                return false;
            }
            $requestedRole = Role::find($this->role);
            if (!$requestedRole) {
                return false;
            }
            $allowedRoles = ['HR', 'Finance', 'TeamLead', 'Employee'];
            return in_array($requestedRole->name, $allowedRoles);
        }
        
        // HR can create Team Lead and Employee
        if ($user->hasRole('HR')) {
            if (!$this->role) {
                return false;
            }
            $requestedRole = Role::find($this->role);
            if (!$requestedRole) {
                return false;
            }
            $allowedRoles = ['TeamLead', 'Employee'];
            return in_array($requestedRole->name, $allowedRoles);
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
            'role' => ['required', 'exists:roles,id'],
            'team_lead_id' => ['nullable', 'exists:users,id'],
        ];
        
        // Super admin can assign any company
        if ($user->hasRole('superAdmin')) {
            $rules['company_id'] = ['required', 'exists:companies,id'];
        } else {
            // For Admin and HR, company_id is set programmatically in controller
            // Only validate if it's present in the request
            if ($this->has('company_id')) {
                $rules['company_id'] = ['required', Rule::in([$user->company_id])];
            }
        }
        
        return $rules;
    }
}

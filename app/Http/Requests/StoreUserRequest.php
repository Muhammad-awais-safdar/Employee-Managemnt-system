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
            
            // Employee Information
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:users'],
            'salary' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'date_of_joining' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'marital_status' => ['nullable', 'in:single,married,divorced,widowed'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'skills' => ['nullable', 'string'],
            'department_id' => ['nullable', 'exists:departments,id'],
            
            // Emergency Contact
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            
            // Tax Information
            'filing_status' => ['required', 'in:single,married_jointly,married_separately,head_of_household'],
            'allowances' => ['nullable', 'integer', 'min:0', 'max:20'],
            'additional_withholding' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'exempt_from_federal' => ['nullable', 'boolean'],
            'exempt_from_state' => ['nullable', 'boolean'],
            'exempt_from_local' => ['nullable', 'boolean'],
            'health_insurance_premium' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'retirement_contribution_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
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

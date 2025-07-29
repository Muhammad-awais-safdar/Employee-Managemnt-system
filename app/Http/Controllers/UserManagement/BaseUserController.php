<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

abstract class BaseUserController extends Controller
{
    protected $user;
    protected $allowedRoles = [];
    protected $viewPrefix = '';
    
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    
    /**
     * Get roles that the current user is allowed to assign
     */
    protected function getAllowedRoles()
    {
        if ($this->user->hasRole('superAdmin')) {
            return Role::all();
        }
        
        if ($this->user->hasRole('Admin')) {
            return Role::whereIn('name', ['HR', 'Finance', 'TeamLead', 'Employee'])->get();
        }
        
        if ($this->user->hasRole('HR')) {
            return Role::whereIn('name', ['TeamLead', 'Employee'])->get();
        }
        
        return collect();
    }
    
    /**
     * Get company ID for the current user
     */
    protected function getCompanyId()
    {
        if ($this->user->hasRole('superAdmin')) {
            return null; // Super admin can see all companies
        }
        
        return $this->user->company_id;
    }
    
    /**
     * Authorize the user action
     */
    protected function authorizeAction($action, $user = null)
    {
        if ($this->user->hasRole('superAdmin')) {
            return true;
        }
        
        if ($this->user->hasRole('Admin')) {
            // Admin can only manage users in their company, except super admins
            return $user && $user->company_id === $this->user->company_id && !$user->hasRole('superAdmin|admin');
        }
        
        if ($this->user->hasRole('HR')) {
            // HR can only manage team leads and employees in their company
            return $user && 
                   $user->company_id === $this->user->company_id && 
                   $user->hasRole('TeamLead|Employee');
        }
        
        if ($this->user->hasRole('TeamLead')) {
            // Team leads can only manage their team members
            return $user && 
                   $user->company_id === $this->user->company_id && 
                   $user->hasRole('Employee') && 
                   $user->team_lead_id === $this->user->id;
        }
        
        return false;
    }
    
    /**
     * Get the base query for users
     */
    protected function getBaseQuery()
    {
        $query = User::with(['roles', 'company']);
        
        if ($this->user->hasRole('superAdmin')) {
            return $query;
        }
        
        $query->where('company_id', $this->user->company_id);
        
        if ($this->user->hasRole('Admin')) {
            return $query->whereHas('roles', function($q) {
                $q->whereIn('name', ['HR', 'Finance', 'TeamLead', 'Employee']);
            });
        }
        
        if ($this->user->hasRole('HR')) {
            return $query->whereHas('roles', function($q) {
                $q->whereIn('name', ['TeamLead', 'Employee']);
            });
        }
        
        if ($this->user->hasRole('TeamLead')) {
            return $query->where('team_lead_id', $this->user->id);
        }
        
        return $query->where('id', $this->user->id);
    }
}

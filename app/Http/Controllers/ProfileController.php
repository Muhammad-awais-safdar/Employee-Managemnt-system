<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the profile for the authenticated user based on their role.
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();
        
        switch ($role) {
            case 'superAdmin':
                return $this->showSuperAdminProfile();
            case 'Admin':
                return $this->showAdminProfile();
            case 'HR':
                return $this->showHRProfile();
            case 'Employee':
                return $this->showEmployeeProfile();
            default:
                return $this->showEmployeeProfile();
        }
    }

    /**
     * Show SuperAdmin profile.
     */
    private function showSuperAdminProfile()
    {
        $user = Auth::user();
        $companies = Company::all();
        $totalUsers = User::count();
        $totalCompanies = Company::count();
        
        return view('EmployeeManagemntsystem.SuperAdmin.profile.index', compact(
            'user', 'companies', 'totalUsers', 'totalCompanies'
        ));
    }

    /**
     * Show Admin profile.
     */
    private function showAdminProfile()
    {
        $user = Auth::user();
        $company = $user->company;
        $departments = Department::where('company_id', $user->company_id)->get();
        $companyUsers = User::where('company_id', $user->company_id)->count();
        
        return view('EmployeeManagemntsystem.Admin.profile.index', compact(
            'user', 'company', 'departments', 'companyUsers'
        ));
    }

    /**
     * Show HR profile.
     */
    private function showHRProfile()
    {
        $user = Auth::user();
        $company = $user->company;
        $department = $user->department;
        $departments = Department::where('company_id', $user->company_id)->get();
        $managedEmployees = User::where('company_id', $user->company_id)
            ->whereHas('roles', function($query) {
                $query->where('name', 'Employee');
            })->count();
        
        return view('EmployeeManagemntsystem.HR.profile.index', compact(
            'user', 'company', 'department', 'departments', 'managedEmployees'
        ));
    }

    /**
     * Show Employee profile.
     */
    private function showEmployeeProfile()
    {
        $user = Auth::user();
        $company = $user->company;
        $department = $user->department;
        $departments = Department::where('company_id', $user->company_id)->get();
        
        return view('EmployeeManagemntsystem.Employee.profile.index', compact(
            'user', 'company', 'department', 'departments'
        ));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();
        
        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];

        // Role-specific validation
        switch ($role) {
            case 'superAdmin':
                $rules = array_merge($rules, [
                    'bio' => 'nullable|string|max:1000',
                    'linkedin_url' => 'nullable|url|max:255',
                    'twitter_url' => 'nullable|url|max:255'
                ]);
                break;
                
            case 'Admin':
                $rules = array_merge($rules, [
                    'bio' => 'nullable|string|max:1000',
                    'linkedin_url' => 'nullable|url|max:255',
                    'emergency_contact_name' => 'nullable|string|max:255',
                    'emergency_contact_phone' => 'nullable|string|max:20',
                    'employee_id' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)]
                ]);
                break;
                
            case 'HR':
                $rules = array_merge($rules, [
                    'department_id' => 'nullable|exists:departments,id',
                    'bio' => 'nullable|string|max:500',
                    'emergency_contact_name' => 'required|string|max:255',
                    'emergency_contact_phone' => 'required|string|max:20',
                    'employee_id' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
                    'date_of_joining' => 'nullable|date|before_or_equal:today',
                    'salary' => 'nullable|numeric|min:0',
                    'qualification' => 'nullable|string|max:255',
                    'experience_years' => 'nullable|integer|min:0|max:50'
                ]);
                break;
                
            case 'Employee':
                $rules = array_merge($rules, [
                    'department_id' => 'nullable|exists:departments,id',
                    'emergency_contact_name' => 'required|string|max:255',
                    'emergency_contact_phone' => 'required|string|max:20',
                    'employee_id' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
                    'date_of_joining' => 'nullable|date|before_or_equal:today',
                    'salary' => 'nullable|numeric|min:0',
                    'qualification' => 'nullable|string|max:255',
                    'experience_years' => 'nullable|integer|min:0|max:50',
                    'skills' => 'nullable|string|max:1000',
                    'marital_status' => 'nullable|in:single,married,divorced,widowed',
                    'gender' => 'nullable|in:male,female,other'
                ]);
                break;
        }

        $request->validate($rules);

        try {
            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                
                $path = $request->file('profile_image')->store('profile-images', 'public');
                $user->profile_image = $path;
            }

            // Update basic fields
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->date_of_birth = $request->date_of_birth;

            // Update role-specific fields
            switch ($role) {
                case 'superAdmin':
                    $user->bio = $request->bio;
                    $user->linkedin_url = $request->linkedin_url;
                    $user->twitter_url = $request->twitter_url;
                    break;
                    
                case 'Admin':
                    $user->bio = $request->bio;
                    $user->linkedin_url = $request->linkedin_url;
                    $user->emergency_contact_name = $request->emergency_contact_name;
                    $user->emergency_contact_phone = $request->emergency_contact_phone;
                    $user->employee_id = $request->employee_id;
                    break;
                    
                case 'HR':
                case 'Employee':
                    $user->department_id = $request->department_id;
                    $user->bio = $request->bio;
                    $user->emergency_contact_name = $request->emergency_contact_name;
                    $user->emergency_contact_phone = $request->emergency_contact_phone;
                    $user->employee_id = $request->employee_id;
                    $user->date_of_joining = $request->date_of_joining;
                    $user->salary = $request->salary;
                    $user->qualification = $request->qualification;
                    $user->experience_years = $request->experience_years;
                    
                    if ($role === 'Employee') {
                        $user->skills = $request->skills;
                        $user->marital_status = $request->marital_status;
                        $user->gender = $request->gender;
                    }
                    break;
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'user' => $user->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 422);
        }

        try {
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password.'
            ], 500);
        }
    }

    /**
     * Delete profile image.
     */
    public function deleteProfileImage()
    {
        $user = Auth::user();

        try {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $user->profile_image = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile image deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete profile image.'
            ], 500);
        }
    }

    /**
     * Get user profile data for API.
     */
    public function getProfile()
    {
        $user = Auth::user();
        $user->load(['company', 'department', 'roles']);
        
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    /**
     * Update user settings/preferences.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            'email_settings' => 'boolean',
            'sms_settings' => 'boolean',
            'theme_preference' => 'nullable|in:light,dark,system'
        ]);

        $user = Auth::user();

        try {
            $settings = $user->settings ?? [];
            
            $settings['timezone'] = $request->timezone ?? 'UTC';
            $settings['language'] = $request->language ?? 'en';
            $settings['email_settings'] = $request->boolean('email_settings', true);
            $settings['sms_settings'] = $request->boolean('sms_settings', false);
            $settings['theme_preference'] = $request->theme_preference ?? 'system';

            $user->settings = $settings;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully!',
                'settings' => $settings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings.'
            ], 500);
        }
    }
}
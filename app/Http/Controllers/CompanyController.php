<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::latest()->paginate(10);
        return view('EmployeeManagemntsystem.SuperAdmin.Company.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all users with admin role who don't already have a company
        $adminUsers = \App\Models\User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->whereDoesntHave('ownedCompany')->get(['id', 'name', 'email']);
        
        return view('EmployeeManagemntsystem.SuperAdmin.Company.create', compact('adminUsers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'admin_user_id' => 'required|exists:users,id|unique:companies,user_id',
                'company_name' => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'required|email|unique:companies,email|max:255',
                'phone' => 'nullable|string|max:20',
                'website' => 'nullable|url|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'status' => 'required|in:active,inactive',
                'notes' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            // Verify the selected user has admin role
            $adminUser = \App\Models\User::where('id', $validated['admin_user_id'])
                ->whereHas('roles', function($query) {
                    $query->where('name', 'admin');
                })
                ->firstOrFail();

            // Handle logo upload with company name
            if ($request->hasFile('logo')) {
                $companySlug = Str::slug($validated['company_name']);
                $logoExtension = $request->file('logo')->getClientOriginalExtension();
                $logoFileName = $companySlug . '.' . $logoExtension;
                $logoPath = $request->file('logo')->storeAs('company-logos', $logoFileName, 'public');
                $validated['logo'] = $logoPath;
            }

            // Set the admin user as the company owner
            $validated['user_id'] = $adminUser->id;
            unset($validated['admin_user_id']); // Remove from data as it's not a column

            $company = Company::create($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company created and assigned to admin user successfully.',
                    'data' => $company->load('user')
                ]);
            }

            return redirect()->route('superAdmin.company.index')
                ->with('success', 'Company created and assigned to admin user successfully.');

        } catch (\Exception $e) {
            // Delete uploaded file if there was an error
            if (isset($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating company: ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating company: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::findOrFail($id);
        return view('EmployeeManagemntsystem.SuperAdmin.Company.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $company = Company::findOrFail($id);
        
        // Get all users with admin role who don't have a company, plus the current company's admin
        $adminUsers = \App\Models\User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->where(function($query) use ($company) {
            $query->whereDoesntHave('ownedCompany')
                  ->orWhere('id', $company->user_id);
        })->get(['id', 'name', 'email']);
        
        return view('EmployeeManagemntsystem.SuperAdmin.Company.edit', compact('company', 'adminUsers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $company = Company::findOrFail($id);
        
        try {
            $validated = $request->validate([
                'admin_user_id' => [
                    'nullable',
                    'exists:users,id',
                    'unique:companies,user_id,' . $id
                ],
                'company_name' => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'required|email|max:255|unique:companies,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'website' => 'nullable|url|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'status' => 'required|in:active,inactive',
                'notes' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            // Handle logo upload if new file is provided
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($company->logo) {
                    Storage::disk('public')->delete($company->logo);
                }
                
                $companySlug = Str::slug($validated['company_name']);
                $logoExtension = $request->file('logo')->getClientOriginalExtension();
                $logoFileName = $companySlug . '.' . $logoExtension;
                $logoPath = $request->file('logo')->storeAs('company-logos', $logoFileName, 'public');
                $validated['logo'] = $logoPath;
            } else if ($request->has('remove_logo') && $company->logo) {
                // Handle logo removal
                Storage::disk('public')->delete($company->logo);
                $validated['logo'] = null;
            }

            // Update admin user if provided
            if (isset($validated['admin_user_id'])) {
                $adminUser = \App\Models\User::where('id', $validated['admin_user_id'])
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'admin');
                    })
                    ->firstOrFail();
                
                $validated['user_id'] = $adminUser->id;
                unset($validated['admin_user_id']);
            }
            
            $company->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company updated successfully.',
                    'data' => $company->load('user')
                ]);
            }

            return redirect()->route('superAdmin.company.index')
                ->with('success', 'Company updated successfully.');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating company: ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating company: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $company = Company::findOrFail($id);
            
            // Delete logo if exists
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            
            $company->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company deleted successfully.'
                ]);
            }

            return redirect()->route('superAdmin.company.index')
                ->with('success', 'Company deleted successfully.');
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting company: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error deleting company: ' . $e->getMessage());
        }
    }

    /**
     * Toggle company status via AJAX
     */
    public function toggleStatus(Request $request, string $id)
    {
        try {
            $company = Company::findOrFail($id);
            
            $validated = $request->validate([
                'status' => 'required|in:active,inactive'
            ]);
            
            $company->update([
                'status' => $validated['status']
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company status updated successfully.',
                    'status' => $company->status
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Company status updated successfully.');
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating status: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    /**
     * Validate company field in real-time
     */
    public function validateField(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $companyId = $request->input('company_id');

        $rules = [
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => $companyId ? 'required|email|max:255|unique:companies,email,' . $companyId : 'required|email|unique:companies,email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'admin_user_id' => $companyId ? 'required|exists:users,id|unique:companies,user_id,' . $companyId : 'required|exists:users,id|unique:companies,user_id',
        ];

        if (!isset($rules[$field])) {
            return response()->json(['valid' => true]);
        }

        try {
            $validator = validator([$field => $value], [$field => $rules[$field]]);
            
            if ($validator->fails()) {
                return response()->json([
                    'valid' => false,
                    'message' => $validator->errors()->first($field)
                ]);
            }

            return response()->json(['valid' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Validation error occurred'
            ]);
        }
    }

    /**
     * Show edit form for admin's own company
     */
    public function editOwn()
    {
        $user = auth()->user();
        
        // Check if user has admin role
        if (!$user->hasRole('admin')) {
            abort(403, 'Only admin users can edit companies.');
        }
        
        // Get the admin's company
        $company = $user->ownedCompany;
        
        if (!$company) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have a company assigned to you.');
        }
        
        // For admin edit, we don't show admin reassignment options
        $adminUsers = collect(); // Empty collection
        
        return view('EmployeeManagemntsystem.SuperAdmin.Company.edit', compact('company', 'adminUsers'));
    }

    /**
     * Update admin's own company
     */
    public function updateOwn(Request $request)
    {
        $user = auth()->user();
        
        // Check if user has admin role
        if (!$user->hasRole('admin')) {
            abort(403, 'Only admin users can edit companies.');
        }
        
        // Get the admin's company
        $company = $user->ownedCompany;
        
        if (!$company) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have a company assigned to you.'
                ], 404);
            }
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have a company assigned to you.');
        }

        try {
            $validated = $request->validate([
                // Admin cannot reassign the company to another admin
                'company_name' => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'required|email|max:255|unique:companies,email,' . $company->id,
                'phone' => 'nullable|string|max:20',
                'website' => 'nullable|url|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'status' => 'required|in:active,inactive',
                'notes' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            // Handle logo upload if new file is provided
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($company->logo) {
                    Storage::disk('public')->delete($company->logo);
                }
                
                $companySlug = Str::slug($validated['company_name']);
                $logoExtension = $request->file('logo')->getClientOriginalExtension();
                $logoFileName = $companySlug . '.' . $logoExtension;
                $logoPath = $request->file('logo')->storeAs('company-logos', $logoFileName, 'public');
                $validated['logo'] = $logoPath;
            } else if ($request->has('remove_logo') && $company->logo) {
                // Handle logo removal
                Storage::disk('public')->delete($company->logo);
                $validated['logo'] = null;
            }
            
            $company->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company updated successfully.',
                    'data' => $company->load('user')
                ]);
            }

            return redirect()->route('admin.dashboard')
                ->with('success', 'Company updated successfully.');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating company: ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating company: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:HR|Admin|superAdmin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Department::class);
        
        $user = auth()->user();
        
        // Company scoping based on user role
        $query = Department::withCount('users');
        
        if ($user->hasRole('superAdmin')) {
            // SuperAdmin can see all departments across all companies
            // No additional scoping needed
        } else {
            // HR and Admin can only see departments from their company
            $query->where('company_id', $user->company_id);
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
        }

        // Status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if (in_array($sortBy, ['name', 'created_at', 'users_count'])) {
            if ($sortBy === 'users_count') {
                $query->orderBy('users_count', $sortDirection);
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
        }

        $departments = $query->paginate(
            $request->get('per_page', 10)
        )->withQueryString();

        // Get statistics for dashboard cards with company scoping
        $statsQuery = Department::query();
        if (!$user->hasRole('superAdmin')) {
            $statsQuery->where('company_id', $user->company_id);
        }
        
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->where('status', 1)->count(),
            'inactive' => (clone $statsQuery)->where('status', 0)->count(),
            'with_users' => (clone $statsQuery)->has('users')->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'departments' => $departments,
                'stats' => $stats,
                'filters' => [
                    'search' => $request->search,
                    'status' => $request->status,
                    'sort_by' => $sortBy,
                    'sort_direction' => $sortDirection,
                ]
            ]);
        }

        return view('EmployeeManagemntsystem.Admin.Department.index', compact(
            'departments', 
            'stats'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Department::class);
        
        $companies = [];
        if (auth()->user()->hasRole('superAdmin')) {
            $companies = \App\Models\Company::active()->orderBy('company_name')->get();
        }
        
        return view('EmployeeManagemntsystem.Admin.Department.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', Department::class);
        
        $user = auth()->user();
        
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ];
        
        // SuperAdmin can select company, others use their own
        if ($user->hasRole('superAdmin')) {
            $rules['company_id'] = 'required|exists:companies,id';
        }
        
        $validated = $request->validate($rules);
        
        // Add unique validation with company scope
        $uniqueRule = Rule::unique('departments', 'name');
        if ($user->hasRole('superAdmin') && isset($validated['company_id'])) {
            $uniqueRule->where('company_id', $validated['company_id']);
        } else {
            $uniqueRule->where('company_id', $user->company_id);
        }
        
        $request->validate([
            'name' => ['required', 'string', 'max:255', $uniqueRule]
        ]);

        // Set default status if not provided
        $validated['status'] = $validated['status'] ?? true;
        
        // Set company_id based on user role
        if (!$user->hasRole('superAdmin')) {
            $validated['company_id'] = $user->company_id;
        }

        try {
            $department = Department::create($validated);

            $message = 'Department "' . $department->name . '" created successfully.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'department' => $department
                ]);
            }

            return redirect()
                ->route('Admin.departments.index')
                ->with('success', $message);

        } catch (\Exception) {
            $errorMessage = 'Failed to create department. Please try again.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['general' => [$errorMessage]]
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        $this->authorize('view', $department);
        
        // Ensure user can only view departments from their company (unless superAdmin)
        $user = auth()->user();
        if (!$user->hasRole('superAdmin') && $department->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
        
        $department->load([
            'users' => function ($query) {
                $query->where('status', 1)->orderBy('name');
            }
        ]);

        // Get department statistics
        $stats = [
            'total_users' => $department->users()->count(),
            'active_users' => $department->users()->where('status', 1)->count(),
        ];

        if (request()->expectsJson()) {
            return response()->json([
                'department' => $department,
                'stats' => $stats
            ]);
        }

        return view('EmployeeManagemntsystem.Admin.Department.show', compact('department', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        $this->authorize('update', $department);
        
        // Ensure user can only edit departments from their company (unless superAdmin)
        $user = auth()->user();
        if (!$user->hasRole('superAdmin') && $department->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
        
        $companies = [];
        if ($user->hasRole('superAdmin')) {
            $companies = \App\Models\Company::active()->orderBy('company_name')->get();
        }
        
        return view('EmployeeManagemntsystem.Admin.Department.edit', compact('department', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Department $department)
    {
        $this->authorize('update', $department);
        
        $user = auth()->user();
        
        // Ensure user can only update departments from their company (unless superAdmin)
        if (!$user->hasRole('superAdmin') && $department->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
        
        $rules = [
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ];
        
        // Handle company selection for SuperAdmin
        if ($user->hasRole('superAdmin')) {
            $rules['company_id'] = 'required|exists:companies,id';
        }
        
        // Name validation with company scoping
        $uniqueRule = Rule::unique('departments', 'name')->ignore($department);
        if ($user->hasRole('superAdmin') && $request->has('company_id')) {
            $uniqueRule->where('company_id', $request->company_id);
        } else {
            $uniqueRule->where('company_id', $user->company_id);
        }
        
        $rules['name'] = ['required', 'string', 'max:255', $uniqueRule];
        
        $validated = $request->validate($rules);
        
        // Prevent company change for non-superAdmin users
        if (!$user->hasRole('superAdmin')) {
            unset($validated['company_id']);
        }

        try {
            $department->update($validated);

            $message = 'Department "' . $department->name . '" updated successfully.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'department' => $department
                ]);
            }

            return redirect()
                ->route('Admin.departments.index')
                ->with('success', $message);

        } catch (\Exception) {
            $errorMessage = 'Failed to update department. Please try again.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['general' => [$errorMessage]]
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Department $department)
    {
        $this->authorize('delete', $department);
        
        // Ensure user can only delete departments from their company (unless superAdmin)
        $user = auth()->user();
        if (!$user->hasRole('superAdmin') && $department->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
        
        // Check if department has users
        if ($department->users()->count() > 0) {
            $message = 'Cannot delete department. It has assigned users.';
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }

            return redirect()
                ->route('Admin.departments.index')
                ->with('error', $message);
        }

        try {
            $departmentName = $department->name;
            $department->delete();

            $message = 'Department "' . $departmentName . '" deleted successfully.';

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()
                ->route('Admin.departments.index')
                ->with('success', $message);

        } catch (\Exception) {
            $errorMessage = 'Failed to delete department. Please try again.';

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()
                ->route('Admin.departments.index')
                ->with('error', $errorMessage);
        }
    }

    /**
     * Show user-department assignment page.
     *
     * @return \Illuminate\Http\Response
     */
    public function assignments()
    {
        $this->authorize('viewAny', Department::class);

        $user = auth()->user();
        $authCompanyId = $user->company_id;

        // Company scoping for departments
        $departmentsQuery = Department::where('status', 1)
            ->withCount(['users' => function ($query) use ($authCompanyId) {
                $query->where('company_id', $authCompanyId);
            }]);
            
        if (!$user->hasRole('superAdmin')) {
            $departmentsQuery->where('company_id', $authCompanyId);
        }
        
        $departments = $departmentsQuery->orderBy('name')->get();

        // User scoping - only show users from same company
        $usersQuery = User::where('status', 1)
            ->where('company_id', $authCompanyId)
            ->with('department');
            
        // HR can only assign users, not admin/superadmin
        if ($user->hasRole('HR')) {
            $usersQuery->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'superAdmin', 'HR']);
            });
        } else {
            $usersQuery->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'superAdmin']);
            });
        }
        
        $users = $usersQuery->orderBy('name')->get();

        return view('EmployeeManagemntsystem.Admin.Department.assignments', compact('departments', 'users'));
    }

    /**
     * Assign user to department.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignUser(Request $request): JsonResponse
    {
        $this->authorize('assignUsers', Department::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        try {
            $currentUser = auth()->user();
            $authCompanyId = $currentUser->company_id;
            
            $targetUser = User::where('id', $validated['user_id'])
                       ->where('company_id', $authCompanyId)
                       ->firstOrFail();
                       
            $department = null;
            if ($validated['department_id']) {
                $department = Department::where('id', $validated['department_id']);
                
                // Ensure department belongs to the same company
                if (!$currentUser->hasRole('superAdmin')) {
                    $department->where('company_id', $authCompanyId);
                }
                
                $department = $department->firstOrFail();
            }
            
            // Check if current user can assign this specific user
            if (!$this->authorize('assignUser', [Department::class, $targetUser, $department])) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to assign this user.'
                ], 403);
            }

            $targetUser->update(['department_id' => $validated['department_id']]);

            $departmentName = $validated['department_id'] 
                ? Department::find($validated['department_id'])->name 
                : 'None';

            return response()->json([
                'success' => true,
                'message' => "User {$targetUser->name} assigned to {$departmentName} successfully.",
                'user' => $targetUser->load('department')
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'User or department not found in your company.'
            ], 404);
        } catch (\Exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign user to department.'
            ], 500);
        }
    }

    /**
     * Toggle department status.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Department $department): JsonResponse
    {
        $this->authorize('update', $department);
        
        // Ensure user can only toggle status for departments from their company (unless superAdmin)
        $user = auth()->user();
        if (!$user->hasRole('superAdmin') && $department->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        try {
            $department->update(['status' => !$department->status]);
            
            return response()->json([
                'success' => true,
                'message' => 'Department status updated successfully.',
                'status' => $department->status,
                'status_label' => $department->status ? 'Active' : 'Inactive'
            ]);
        } catch (\Exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update department status.'
            ], 500);
        }
    }
}
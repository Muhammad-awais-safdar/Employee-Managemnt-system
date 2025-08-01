<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HRDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:HR|Admin|superAdmin');
    }

    public function index()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // Get HR-specific statistics
        $stats = $this->getHRStatistics($companyId);
        
        // Get recent HR activities
        $recentActivities = $this->getRecentHRActivities($companyId);
        
        // Get department assignments overview
        $departmentOverview = $this->getDepartmentAssignmentOverview($companyId);
        
        // Get new hires this month
        $newHires = $this->getNewHires($companyId);
        
        // Get pending assignments
        $pendingAssignments = $this->getPendingAssignments($companyId);
        
        // Get HR metrics
        $hrMetrics = $this->getHRMetrics($companyId);
        
        // Get employee distribution by department
        $employeeDistribution = $this->getEmployeeDistribution($companyId);

        return view('EmployeeManagemntsystem.HR.dashboard', compact(
            'stats',
            'recentActivities',
            'departmentOverview',
            'newHires',
            'pendingAssignments',
            'hrMetrics',
            'employeeDistribution'
        ));
    }

    private function getHRStatistics($companyId)
    {
        // Total employees in company
        $totalEmployees = User::where('company_id', $companyId)
            ->whereHas('roles', function($q) {
                $q->where('name', 'Employee');
            })
            ->count();

        // Active employees
        $activeEmployees = User::where('company_id', $companyId)
            ->where('status', 1)
            ->whereHas('roles', function($q) {
                $q->where('name', 'Employee');
            })
            ->count();

        // Employees without department assignment
        $unassignedEmployees = User::where('company_id', $companyId)
            ->whereNull('department_id')
            ->whereHas('roles', function($q) {
                $q->where('name', 'Employee');
            })
            ->count();

        // New hires this month
        $newHiresThisMonth = User::where('company_id', $companyId)
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->whereHas('roles', function($q) {
                $q->where('name', 'Employee');
            })
            ->count();

        // Total departments
        $totalDepartments = Department::where('company_id', $companyId)
            ->count();

        // Active departments
        $activeDepartments = Department::where('company_id', $companyId)
            ->where('status', 1)
            ->count();

        // Departments with employees
        $departmentsWithEmployees = Department::where('company_id', $companyId)
            ->has('users')
            ->count();

        return [
            'total_employees' => $totalEmployees,
            'active_employees' => $activeEmployees,
            'unassigned_employees' => $unassignedEmployees,
            'new_hires_this_month' => $newHiresThisMonth,
            'total_departments' => $totalDepartments,
            'active_departments' => $activeDepartments,
            'departments_with_employees' => $departmentsWithEmployees,
            'assignment_completion_rate' => $totalEmployees > 0 ? 
                round((($totalEmployees - $unassignedEmployees) / $totalEmployees) * 100, 1) : 0,
            'monthly_growth' => $this->calculateMonthlyGrowth($companyId),
        ];
    }

    private function calculateMonthlyGrowth($companyId)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        $currentCount = User::where('company_id', $companyId)
            ->where('created_at', '>=', $currentMonth)
            ->whereHas('roles', function($q) {
                $q->where('name', 'Employee');
            })
            ->count();

        $lastMonthCount = User::where('company_id', $companyId)
            ->whereBetween('created_at', [$lastMonth, $currentMonth])
            ->whereHas('roles', function($q) {
                $q->where('name', 'Employee');
            })
            ->count();

        if ($lastMonthCount == 0) return $currentCount > 0 ? 100 : 0;
        
        return round((($currentCount - $lastMonthCount) / $lastMonthCount) * 100, 1);
    }

    private function getRecentHRActivities($companyId)
    {
        $activities = collect();

        // Recent new hires
        $newHires = User::where('company_id', $companyId)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->whereHas('roles', function($q) {
                $q->where('name', 'Employee');
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($newHires as $hire) {
            $activities->push([
                'user' => $hire,
                'action' => 'New employee onboarded',
                'time' => $hire->created_at->format('h:i A'),
                'date' => $hire->created_at->format('M d'),
                'type' => 'new_hire'
            ]);
        }

        // Recent department assignments
        $recentAssignments = User::where('company_id', $companyId)
            ->whereNotNull('department_id')
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->with('department')
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentAssignments as $assignment) {
            if ($assignment->department) {
                $activities->push([
                    'user' => $assignment,
                    'action' => 'Assigned to ' . $assignment->department->name,
                    'time' => $assignment->updated_at->format('h:i A'),
                    'date' => $assignment->updated_at->format('M d'),
                    'type' => 'assignment'
                ]);
            }
        }

        return $activities->sortByDesc('time')->take(6);
    }

    private function getDepartmentAssignmentOverview($companyId)
    {
        return Department::where('company_id', $companyId)
            ->where('status', 1)
            ->withCount(['users' => function($query) use ($companyId) {
                $query->where('company_id', $companyId)
                      ->where('status', 1);
            }])
            ->orderBy('users_count', 'desc')
            ->limit(5)
            ->get();
    }

    private function getNewHires($companyId)
    {
        return User::where('company_id', $companyId)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->whereHas('roles', function($q) {
                $q->where('name', 'Employee');
            })
            ->with('department')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getPendingAssignments($companyId)
    {
        return User::where('company_id', $companyId)
            ->whereNull('department_id')
            ->where('status', 1)
            ->whereHas('roles', function($q) {
                $q->where('name', 'Employee');
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getHRMetrics($companyId)
    {
        $totalEmployees = User::where('company_id', $companyId)
            ->whereHas('roles', function($q) {
                $q->where('name', 'Employee');
            })
            ->count();

        $assignedEmployees = User::where('company_id', $companyId)
            ->whereNotNull('department_id')
            ->whereHas('roles', function($q) {
                $q->where('name', 'Employee');
            })
            ->count();

        $newHiresThisWeek = User::where('company_id', $companyId)
            ->where('created_at', '>=', Carbon::now()->startOfWeek())
            ->whereHas('roles', function($q) {
                $q->where('name', 'Employee');
            })
            ->count();

        $assignmentRate = $totalEmployees > 0 ? 
            round(($assignedEmployees / $totalEmployees) * 100, 1) : 0;

        return [
            'total_employees' => $totalEmployees,
            'assigned_employees' => $assignedEmployees,
            'unassigned_employees' => $totalEmployees - $assignedEmployees,
            'assignment_rate' => $assignmentRate,
            'new_hires_this_week' => $newHiresThisWeek,
            'retention_rate' => 95.5, // Placeholder - would come from actual leave/termination data
        ];
    }

    private function getEmployeeDistribution($companyId)
    {
        return Department::where('company_id', $companyId)
            ->withCount(['users' => function($query) use ($companyId) {
                $query->where('company_id', $companyId)
                      ->where('status', 1)
                      ->whereHas('roles', function($q) {
                          $q->where('name', 'Employee');
                      });
            }])
            ->where('status', 1)
            ->orderBy('users_count', 'desc')
            ->get()
            ->map(function ($dept) {
                return [
                    'name' => $dept->name,
                    'count' => $dept->users_count,
                    'color' => $this->getDepartmentColor($dept->name)
                ];
            });
    }

    private function getDepartmentColor($departmentName)
    {
        $colors = [
            '#3E007C', '#7100E2', '#28a745', '#dc3545', 
            '#ffc107', '#17a2b8', '#6f42c1', '#e83e8c'
        ];
        
        return $colors[crc32($departmentName) % count($colors)];
    }
}
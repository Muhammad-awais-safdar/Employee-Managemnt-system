<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Company;
use App\Models\SalaryIncrementRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Admin|superAdmin');
    }

    public function index()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // Get company-scoped statistics
        $stats = $this->getStatistics($user, $companyId);
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($companyId);
        
        // Get team leads
        $teamLeads = $this->getTeamLeads($companyId);
        
        // Get upcoming leaves (placeholder for now)
        $upcomingLeaves = $this->getUpcomingLeaves($companyId);
        
        // Get today's events
        $todayEvents = $this->getTodayEvents($companyId);
        
        // Get department statistics for charts
        $departmentStats = $this->getDepartmentStatistics($companyId);
        
        // Get user growth data for charts
        $userGrowthData = $this->getUserGrowthData($companyId);
        
        // Get pending increment requests count
        $pendingIncrements = SalaryIncrementRequest::where('company_id', $companyId)
            ->where('status', 'pending')
            ->count();

        return view('EmployeeManagemntsystem.Admin.dashboard', compact(
            'stats',
            'recentActivities', 
            'teamLeads',
            'upcomingLeaves',
            'todayEvents',
            'departmentStats',
            'userGrowthData',
            'pendingIncrements'
        ));
    }

    private function getStatistics($user, $companyId)
    {
        $query = User::where('company_id', $companyId);
        
        // For SuperAdmin, get all users; for Admin, get company users
        if (!$user->hasRole('superAdmin')) {
            $query->where('company_id', $companyId);
        } else {
            $query = User::query(); // SuperAdmin sees all
        }

        $totalUsers = (clone $query)->count();
        $activeUsers = (clone $query)->where('status', 1)->count();
        $inactiveUsers = (clone $query)->where('status', 0)->count();
        
        // Department statistics
        $departmentQuery = Department::query();
        if (!$user->hasRole('superAdmin')) {
            $departmentQuery->where('company_id', $companyId);
        }
        
        $totalDepartments = (clone $departmentQuery)->count();
        $activeDepartments = (clone $departmentQuery)->where('status', 1)->count();
        
        // Role-based statistics
        $adminUsers = (clone $query)->whereHas('roles', function($q) {
            $q->where('name', 'Admin');
        })->count();
        
        $hrUsers = (clone $query)->whereHas('roles', function($q) {
            $q->where('name', 'HR');
        })->count();
        
        $employees = (clone $query)->whereHas('roles', function($q) {
            $q->where('name', 'Employee');
        })->count();

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'total_departments' => $totalDepartments,
            'active_departments' => $activeDepartments,
            'admin_users' => $adminUsers,
            'hr_users' => $hrUsers,
            'employees' => $employees,
            'user_growth_percentage' => $this->calculateGrowthPercentage($companyId, 'users'),
            'department_growth_percentage' => $this->calculateGrowthPercentage($companyId, 'departments'),
        ];
    }

    private function calculateGrowthPercentage($companyId, $type = 'users')
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        if ($type === 'users') {
            $currentCount = User::where('company_id', $companyId)
                ->where('created_at', '>=', $currentMonth)
                ->count();
            $lastMonthCount = User::where('company_id', $companyId)
                ->whereBetween('created_at', [$lastMonth, $currentMonth])
                ->count();
        } else {
            $currentCount = Department::where('company_id', $companyId)
                ->where('created_at', '>=', $currentMonth)
                ->count();
            $lastMonthCount = Department::where('company_id', $companyId)
                ->whereBetween('created_at', [$lastMonth, $currentMonth])
                ->count();
        }
        
        if ($lastMonthCount == 0) return $currentCount > 0 ? 100 : 0;
        
        return round((($currentCount - $lastMonthCount) / $lastMonthCount) * 100, 1);
    }

    private function getRecentActivities($companyId)
    {
        // Get recently created users as activities
        return User::where('company_id', $companyId)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($user) {
                return [
                    'user' => $user,
                    'action' => 'Joined the company',
                    'time' => $user->created_at->format('h:i A'),
                    'type' => 'user_joined'
                ];
            });
    }

    private function getTeamLeads($companyId)
    {
        return User::where('company_id', $companyId)
            ->whereHas('roles', function($query) {
                $query->where('name', 'TeamLead');
            })
            ->with(['department', 'roles'])
            ->where('status', 1)
            ->limit(5)
            ->get();
    }

    private function getUpcomingLeaves($companyId)
    {
        // Placeholder for leave management system
        // Return sample data for now
        return collect([
            [
                'user_name' => 'Sample Employee',
                'date' => Carbon::now()->addDays(3)->format('d M Y'),
                'type' => 'Annual Leave',
                'avatar' => 'assets/img/employees/employee-01.jpg'
            ],
            [
                'user_name' => 'Another Employee', 
                'date' => Carbon::now()->addDays(5)->format('d M Y'),
                'type' => 'Sick Leave',
                'avatar' => 'assets/img/employees/employee-02.jpg'
            ]
        ]);
    }

    private function getTodayEvents($companyId)
    {
        $today = Carbon::now()->format('Y-m-d');
        
        // Get users with birthdays today (assuming birthday field exists)
        $birthdays = User::where('company_id', $companyId)
            ->where('status', 1)
            ->whereRaw('DATE_FORMAT(created_at, "%m-%d") = ?', [Carbon::now()->format('m-d')])
            ->limit(3)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'birthday',
                    'user' => $user,  
                    'message' => $user->name . "'s Work Anniversary"
                ];
            });

        return $birthdays;
    }

    private function getDepartmentStatistics($companyId)
    {
        return Department::where('company_id', $companyId)
            ->withCount('users')
            ->where('status', 1)
            ->get()
            ->map(function ($dept) {
                return [
                    'name' => $dept->name,
                    'users_count' => $dept->users_count,
                    'percentage' => 0 // Will be calculated in frontend
                ];
            });
    }

    private function getUserGrowthData($companyId)
    {
        $months = [];
        $userCounts = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');
            
            $count = User::where('company_id', $companyId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $userCounts[] = $count;
        }
        
        return [
            'months' => $months,
            'counts' => $userCounts
        ];
    }
}
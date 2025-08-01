<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Leave;
use App\Models\Department;
use App\Models\LeaveType;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeamLeaveCalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display the team leave calendar page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check permissions - allow all authenticated users to view calendar
        if (!$user->hasAnyRole(['Admin', 'superAdmin', 'HR', 'TeamLead', 'Employee'])) {
            abort(403, 'Unauthorized to view team calendar');
        }

        $companyId = $user->hasRole('superAdmin') ? 
            $request->get('company_id', $user->company_id) : $user->company_id;

        // Get departments for filtering
        $departments = Department::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get leave types for filtering
        $leaveTypes = LeaveType::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get companies for SuperAdmin
        $companies = $user->hasRole('superAdmin') ? Company::active()->get() : null;

        // Determine which view to return based on user role
        $viewPath = 'EmployeeManagemntsystem.Admin.leave.calendar';
        
        if ($user->hasRole('Employee') && !$user->hasAnyRole(['Admin', 'superAdmin', 'HR', 'TeamLead'])) {
            $viewPath = 'EmployeeManagemntsystem.Employee.leave.calendar';
        } elseif ($user->hasRole('HR') && !$user->hasAnyRole(['Admin', 'superAdmin'])) {
            $viewPath = 'EmployeeManagemntsystem.HR.leave.calendar';
        }

        return view($viewPath, compact(
            'departments',
            'leaveTypes', 
            'companies',
            'companyId'
        ));
    }

    /**
     * Get calendar events for approved leaves.
     */
    public function getCalendarEvents(Request $request)
    {
        try {
            $user = Auth::user();
            $companyId = $user->hasRole('superAdmin') ? 
                $request->get('company_id', $user->company_id) : $user->company_id;

            $start = Carbon::parse($request->get('start'));
            $end = Carbon::parse($request->get('end'));
            $departmentId = $request->get('department_id');
            $leaveTypeId = $request->get('leave_type_id');
            $employeeId = $request->get('employee_id');

            $query = Leave::with(['user', 'leaveType', 'user.department'])
                ->where('company_id', $companyId)
                ->where('status', 'approved')
                ->where(function($q) use ($start, $end) {
                    $q->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end])
                      ->orWhere(function($q2) use ($start, $end) {
                          $q2->where('start_date', '<=', $start)
                             ->where('end_date', '>=', $end);
                      });
                });

            // Apply filters
            if ($departmentId) {
                $query->whereHas('user', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }

            if ($leaveTypeId) {
                $query->where('leave_type_id', $leaveTypeId);
            }

            if ($employeeId) {
                $query->where('user_id', $employeeId);
            }

            // TeamLead can only see their team's leaves
            if ($user->hasRole('TeamLead') && !$user->hasAnyRole(['Admin', 'superAdmin', 'HR'])) {
                $query->whereHas('user', function($q) use ($user) {
                    $q->where('team_lead_id', $user->id)
                      ->orWhere('id', $user->id); // Include their own leaves
                });
            }

            $leaves = $query->get();

            $events = [];
            
            foreach ($leaves as $leave) {
                $startDate = Carbon::parse($leave->start_date);
                $endDate = Carbon::parse($leave->end_date);
                
                // Handle single day leaves
                if ($startDate->isSameDay($endDate)) {
                    $events[] = $this->createCalendarEvent($leave, $startDate, $startDate);
                } else {
                    // Multi-day leaves - create event for each day
                    $current = $startDate->copy();
                    while ($current->lte($endDate)) {
                        $events[] = $this->createCalendarEvent($leave, $current, $current);
                        $current->addDay();
                    }
                }
            }

            return response()->json($events);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load calendar events: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a calendar event object.
     */
    private function createCalendarEvent($leave, $date, $endDate)
    {
        $isHalfDay = $leave->duration === 'first_half' || $leave->duration === 'second_half';
        
        return [
            'id' => $leave->id . '_' . $date->format('Y-m-d'),
            'title' => $leave->user->name . ($isHalfDay ? ' (Half Day)' : ''),
            'start' => $date->format('Y-m-d'),
            'end' => $endDate->addDay()->format('Y-m-d'), // FullCalendar end is exclusive
            'allDay' => true,
            'backgroundColor' => $this->getLeaveTypeColor($leave->leaveType),
            'borderColor' => $this->getLeaveTypeColor($leave->leaveType),
            'textColor' => '#ffffff',
            'extendedProps' => [
                'leave_id' => $leave->id,
                'employee_name' => $leave->user->name,
                'employee_id' => $leave->user->id,
                'department' => $leave->user->department?->name ?? 'N/A',
                'leave_type' => $leave->leaveType?->name ?? 'Leave',
                'duration' => $leave->duration,
                'application_id' => $leave->application_id,
                'reason' => $leave->reason,
                'total_days' => $leave->total_days,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date
            ]
        ];
    }

    /**
     * Get color for leave type.
     */
    private function getLeaveTypeColor($leaveType): string
    {
        if (!$leaveType) return '#6c757d';

        return match(strtolower($leaveType->name)) {
            'annual leave', 'vacation' => '#28a745',
            'sick leave' => '#dc3545', 
            'casual leave' => '#007bff',
            'personal leave' => '#6f42c1',
            'maternity leave', 'paternity leave' => '#fd7e14',
            'emergency leave' => '#e83e8c',
            'unpaid leave' => '#6c757d',
            default => '#17a2b8'
        };
    }

    /**
     * Get team availability overview.
     */
    public function getTeamAvailability(Request $request)
    {
        try {
            $user = Auth::user();
            $companyId = $user->hasRole('superAdmin') ? 
                $request->get('company_id', $user->company_id) : $user->company_id;

            $date = Carbon::parse($request->get('date', now()));
            $departmentId = $request->get('department_id');

            $query = User::with(['department', 'roles'])
                ->where('company_id', $companyId)
                ->where('status', true)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['Employee', 'TeamLead', 'HR', 'Finance']);
                });

            if ($departmentId) {
                $query->where('department_id', $departmentId);
            }

            // TeamLead can only see their team
            if ($user->hasRole('TeamLead') && !$user->hasAnyRole(['Admin', 'superAdmin', 'HR'])) {
                $query->where(function($q) use ($user) {
                    $q->where('team_lead_id', $user->id)
                      ->orWhere('id', $user->id);
                });
            }

            $employees = $query->get();

            $availability = [];
            
            foreach ($employees as $employee) {
                // Check if employee has approved leave on this date
                $leaveOnDate = Leave::where('user_id', $employee->id)
                    ->where('status', 'approved')
                    ->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->with('leaveType')
                    ->first();

                $status = 'available';
                $leaveInfo = null;

                if ($leaveOnDate) {
                    $status = match($leaveOnDate->duration) {
                        'first_half', 'second_half' => 'half_day_leave',
                        default => 'on_leave'
                    };
                    
                    $leaveInfo = [
                        'type' => $leaveOnDate->leaveType?->name ?? 'Leave',
                        'duration' => $leaveOnDate->duration,
                        'application_id' => $leaveOnDate->application_id
                    ];
                }

                $availability[] = [
                    'employee_id' => $employee->id,
                    'name' => $employee->name,
                    'department' => $employee->department?->name ?? 'N/A',
                    'status' => $status,
                    'leave_info' => $leaveInfo
                ];
            }

            // Group by department
            $departmentWise = collect($availability)->groupBy('department');

            return response()->json([
                'date' => $date->format('Y-m-d'),
                'employees' => $availability,
                'department_wise' => $departmentWise,
                'summary' => [
                    'total' => count($availability),
                    'available' => collect($availability)->where('status', 'available')->count(),
                    'on_leave' => collect($availability)->where('status', 'on_leave')->count(),
                    'half_day' => collect($availability)->where('status', 'half_day_leave')->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get team availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get department-wise leave statistics.
     */
    public function getDepartmentStats(Request $request)
    {
        try {
            $user = Auth::user();
            $companyId = $user->hasRole('superAdmin') ? 
                $request->get('company_id', $user->company_id) : $user->company_id;

            $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
            $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));

            $departments = Department::where('company_id', $companyId)
                ->where('status', 'active')
                ->withCount(['users' => function($q) {
                    $q->where('status', true)
                      ->whereHas('roles', function($r) {
                          $r->whereIn('name', ['Employee', 'TeamLead', 'HR', 'Finance']);
                      });
                }])
                ->get();

            $stats = [];

            foreach ($departments as $department) {
                // Get approved leaves for this department in date range
                $leaves = Leave::whereHas('user', function($q) use ($department) {
                        $q->where('department_id', $department->id);
                    })
                    ->where('status', 'approved')
                    ->where(function($q) use ($startDate, $endDate) {
                        $q->whereBetween('start_date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhere(function($q2) use ($startDate, $endDate) {
                              $q2->where('start_date', '<=', $startDate)
                                 ->where('end_date', '>=', $endDate);
                          });
                    })
                    ->with(['leaveType'])
                    ->get();

                $totalDays = $leaves->sum('total_days');
                $leaveTypeBreakdown = $leaves->groupBy('leaveType.name')
                    ->map(function($typeLeaves) {
                        return [
                            'count' => $typeLeaves->count(),
                            'days' => $typeLeaves->sum('total_days')
                        ];
                    });

                $stats[] = [
                    'department_id' => $department->id,
                    'department_name' => $department->name,
                    'total_employees' => $department->users_count,
                    'total_leaves' => $leaves->count(),
                    'total_leave_days' => $totalDays,
                    'average_days_per_employee' => $department->users_count > 0 ? 
                        round($totalDays / $department->users_count, 2) : 0,
                    'leave_type_breakdown' => $leaveTypeBreakdown
                ];
            }

            return response()->json([
                'period' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d')
                ],
                'departments' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get department statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detect leave conflicts and overlaps.
     */
    public function getLeaveConflicts(Request $request)
    {
        try {
            $user = Auth::user();
            $companyId = $user->hasRole('superAdmin') ? 
                $request->get('company_id', $user->company_id) : $user->company_id;

            $startDate = Carbon::parse($request->get('start_date', now()));
            $endDate = Carbon::parse($request->get('end_date', now()->addDays(30)));
            $departmentId = $request->get('department_id');

            // Get all approved leaves in the period
            $query = Leave::with(['user.department', 'leaveType'])
                ->where('company_id', $companyId)
                ->where('status', 'approved')
                ->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q2) use ($startDate, $endDate) {
                          $q2->where('start_date', '<=', $startDate)
                             ->where('end_date', '>=', $endDate);
                      });
                });

            if ($departmentId) {
                $query->whereHas('user', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }

            $leaves = $query->get();

            $conflicts = [];
            $departmentOverlaps = [];

            // Group leaves by date to find conflicts
            $leavesByDate = [];
            foreach ($leaves as $leave) {
                $current = Carbon::parse($leave->start_date);
                $end = Carbon::parse($leave->end_date);
                
                while ($current->lte($end)) {
                    $dateKey = $current->format('Y-m-d');
                    if (!isset($leavesByDate[$dateKey])) {
                        $leavesByDate[$dateKey] = [];
                    }
                    $leavesByDate[$dateKey][] = $leave;
                    $current->addDay();
                }
            }

            // Analyze each date for conflicts
            foreach ($leavesByDate as $date => $dayLeaves) {
                if (count($dayLeaves) > 1) {
                    // Group by department to check department coverage
                    $deptGroups = collect($dayLeaves)->groupBy('user.department_id');
                    
                    foreach ($deptGroups as $deptId => $deptLeaves) {
                        if (count($deptLeaves) > 1) {
                            $department = $deptLeaves->first()->user->department;
                            $totalEmployees = User::where('department_id', $deptId)
                                ->where('status', true)
                                ->whereHas('roles', function($q) {
                                    $q->whereIn('name', ['Employee', 'TeamLead', 'HR', 'Finance']);
                                })
                                ->count();

                            $leaveCount = count($deptLeaves);
                            $coveragePercentage = $totalEmployees > 0 ? 
                                round(($leaveCount / $totalEmployees) * 100, 2) : 0;

                            if ($coveragePercentage >= 50) { // High impact threshold
                                $conflicts[] = [
                                    'date' => $date,
                                    'type' => 'department_coverage',
                                    'severity' => $coveragePercentage >= 75 ? 'critical' : 'high',
                                    'department' => $department?->name ?? 'Unknown',
                                    'employees_on_leave' => $leaveCount,
                                    'total_employees' => $totalEmployees,
                                    'coverage_percentage' => $coveragePercentage,
                                    'employees' => $deptLeaves->map(function($leave) {
                                        return [
                                            'name' => $leave->user->name,
                                            'leave_type' => $leave->leaveType?->name ?? 'Leave',
                                            'application_id' => $leave->application_id
                                        ];
                                    })->toArray()
                                ];
                            }
                        }
                    }
                }
            }

            return response()->json([
                'period' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d')
                ],
                'conflicts' => $conflicts,
                'summary' => [
                    'total_conflicts' => count($conflicts),
                    'critical' => collect($conflicts)->where('severity', 'critical')->count(),
                    'high' => collect($conflicts)->where('severity', 'high')->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to detect conflicts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employees for a company (for filtering).
     */
    public function getEmployees(Request $request)
    {
        try {
            $user = Auth::user();
            $companyId = $user->hasRole('superAdmin') ? 
                $request->get('company_id', $user->company_id) : $user->company_id;
            $departmentId = $request->get('department_id');

            $query = User::with(['department'])
                ->where('company_id', $companyId)
                ->where('status', true)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['Employee', 'TeamLead', 'HR', 'Finance']);
                });

            if ($departmentId) {
                $query->where('department_id', $departmentId);
            }

            // TeamLead can only see their team
            if ($user->hasRole('TeamLead') && !$user->hasAnyRole(['Admin', 'superAdmin', 'HR'])) {
                $query->where(function($q) use ($user) {
                    $q->where('team_lead_id', $user->id)
                      ->orWhere('id', $user->id);
                });
            }

            $employees = $query->orderBy('name')
                ->get(['id', 'name', 'department_id'])
                ->map(function($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'department' => $employee->department?->name ?? 'N/A'
                    ];
                });

            return response()->json([
                'employees' => $employees
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load employees: ' . $e->getMessage()
            ], 500);
        }
    }
}
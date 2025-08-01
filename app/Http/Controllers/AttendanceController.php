<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Display attendance dashboard based on user role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->hasRole('Employee')) {
            return $this->employeeDashboard($request);
        }
        
        return $this->managementDashboard($request);
    }

    /**
     * Employee attendance dashboard.
     */
    private function employeeDashboard(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Get today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        
        // Get current month summary
        $monthSummary = Attendance::getUserSummary(
            $user->id,
            $today->copy()->startOfMonth(),
            $today->copy()->endOfMonth()
        );
        
        // Get recent attendance (last 10 days)
        $recentAttendance = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
        
        // Get working hours settings
        $workingHours = $this->getWorkingHoursSettings($user->company_id);
        
        return view('EmployeeManagemntsystem.Employee.attendance.dashboard', compact(
            'todayAttendance',
            'monthSummary',
            'recentAttendance',
            'workingHours'
        ));
    }

    /**
     * Management attendance dashboard (HR, Admin, SuperAdmin).
     */
    private function managementDashboard(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Get company scope
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        // Get today's company summary
        $companySummary = Attendance::getCompanySummary($companyId, $today);
        
        // Get attendance filters
        $dateFrom = $request->get('date_from', $today->format('Y-m-d'));
        $dateTo = $request->get('date_to', $today->format('Y-m-d'));
        $status = $request->get('status');
        $departmentId = $request->get('department_id');
        
        // Build query for attendance list
        $query = Attendance::with(['user', 'user.department'])
            ->when($companyId, function($q) use ($companyId) {
                return $q->forCompany($companyId);
            })
            ->dateRange($dateFrom, $dateTo)
            ->when($status, function($q) use ($status) {
                return $q->byStatus($status);
            })
            ->when($departmentId, function($q) use ($departmentId) {
                return $q->whereHas('user', function($userQuery) use ($departmentId) {
                    $userQuery->where('department_id', $departmentId);
                });
            })
            ->orderBy('date', 'desc')
            ->orderBy('check_in_time', 'desc');
        
        $attendances = $query->paginate(20);
        
        // Get departments for filter
        $departments = \App\Models\Department::when($companyId, function($q) use ($companyId) {
            return $q->forCompany($companyId);
        })->active()->get();
        
        // Get companies for SuperAdmin
        $companies = $user->hasRole('superAdmin') ? \App\Models\Company::active()->get() : null;
        
        return view('EmployeeManagemntsystem.Admin.attendance.dashboard', compact(
            'companySummary',
            'attendances',
            'departments',
            'companies',
            'dateFrom',
            'dateTo',
            'status',
            'departmentId',
            'companyId'
        ));
    }

    /**
     * Employee check-in.
     */
    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();
        
        // Check if already checked in today
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        
        if ($attendance && $attendance->check_in_time) {
            return response()->json([
                'success' => false,
                'message' => 'You have already checked in today at ' . $attendance->check_in_time
            ]);
        }
        
        // Create or update attendance record
        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => $today
            ],
            [
                'company_id' => $user->company_id,
                'check_in_time' => $now->format('H:i:s'),
                'ip_address' => $request->ip(),
                'is_weekend' => $now->isWeekend(),
            ]
        );
        
        // Update status based on timing
        $attendance->updateStatus();
        
        return response()->json([
            'success' => true,
            'message' => 'Checked in successfully at ' . $now->format('h:i A'),
            'attendance' => $attendance
        ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
    }

    /**
     * Employee check-out.
     */
    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();
        
        // Get today's attendance
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        
        if (!$attendance || !$attendance->check_in_time) {
            return response()->json([
                'success' => false,
                'message' => 'You must check in first before checking out'
            ]);
        }
        
        if ($attendance->check_out_time) {
            return response()->json([
                'success' => false,
                'message' => 'You have already checked out today at ' . $attendance->check_out_time
            ]);
        }
        
        // Update check-out time
        $attendance->update([
            'check_out_time' => $now->format('H:i:s')
        ]);
        
        // Update status and calculate hours
        $attendance->updateStatus();
        
        return response()->json([
            'success' => true,
            'message' => 'Checked out successfully at ' . $now->format('h:i A'),
            'attendance' => $attendance,
            'total_hours' => $attendance->formatted_total_hours
        ]);
    }

    /**
     * Start break.
     */
    public function startBreak(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();
        
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        
        if (!$attendance || !$attendance->check_in_time || $attendance->check_out_time) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid attendance status for break'
            ]);
        }
        
        $breakTimes = $attendance->break_times ?? [];
        
        // Check if already on break
        $onBreak = false;
        foreach ($breakTimes as $break) {
            if (isset($break['start']) && !isset($break['end'])) {
                $onBreak = true;
                break;
            }
        }
        
        if ($onBreak) {
            return response()->json([
                'success' => false,
                'message' => 'You are already on break'
            ]);
        }
        
        // Add new break start
        $breakTimes[] = ['start' => $now->format('H:i:s')];
        
        $attendance->update(['break_times' => $breakTimes]);
        
        return response()->json([
            'success' => true,
            'message' => 'Break started at ' . $now->format('h:i A')
        ]);
    }

    /**
     * End break.
     */
    public function endBreak(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();
        
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        
        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No attendance record found'
            ]);
        }
        
        $breakTimes = $attendance->break_times ?? [];
        
        // Find active break
        $activeBreakIndex = null;
        foreach ($breakTimes as $index => $break) {
            if (isset($break['start']) && !isset($break['end'])) {
                $activeBreakIndex = $index;
                break;
            }
        }
        
        if ($activeBreakIndex === null) {
            return response()->json([
                'success' => false,
                'message' => 'No active break found'
            ]);
        }
        
        // End the break
        $breakTimes[$activeBreakIndex]['end'] = $now->format('H:i:s');
        
        // Calculate total break duration
        $totalBreakMinutes = 0;
        foreach ($breakTimes as $break) {
            if (isset($break['start']) && isset($break['end'])) {
                try {
                    $start = Carbon::createFromFormat('H:i:s', $break['start']);
                    $end = Carbon::createFromFormat('H:i:s', $break['end']);
                    $totalBreakMinutes += $end->diffInMinutes($start);
                } catch (\Exception $e) {
                    \Log::error('Error calculating break duration: ' . $e->getMessage());
                    continue;
                }
            }
        }
        
        $attendance->update([
            'break_times' => $breakTimes,
            'break_duration' => $totalBreakMinutes
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Break ended at ' . $now->format('h:i A'),
            'total_break_duration' => $attendance->formatted_break_duration
        ]);
    }

    /**
     * Get attendance reports.
     */
    public function reports(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);
        
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        // Date range
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Get detailed attendance report
        $attendanceReport = $this->generateAttendanceReport($companyId, $dateFrom, $dateTo, $request);
        
        return view('EmployeeManagemntsystem.Admin.attendance.reports', $attendanceReport);
    }

    /**
     * Generate comprehensive attendance report.
     */
    private function generateAttendanceReport($companyId, $dateFrom, $dateTo, $request)
    {
        // Get all employees in company
        $employees = User::with(['department'])
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('company_id', $companyId);
            })
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['Employee', 'TeamLead', 'HR', 'Finance']);
            })
            ->when($request->get('department_id'), function($q) use ($request) {
                return $q->where('department_id', $request->get('department_id'));
            })
            ->get();
        
        // Get attendance data for the period
        $attendanceData = [];
        $summaryData = [
            'total_employees' => $employees->count(),
            'total_present_days' => 0,
            'total_absent_days' => 0,
            'total_late_days' => 0,
            'total_half_days' => 0,
            'total_working_hours' => 0,
            'total_overtime_hours' => 0,
            'average_attendance_rate' => 0
        ];
        
        foreach ($employees as $employee) {
            $userSummary = Attendance::getUserSummary($employee->id, $dateFrom, $dateTo);
            $userSummary['employee'] = $employee;
            $attendanceData[] = $userSummary;
            
            // Add to summary
            $summaryData['total_present_days'] += $userSummary['present_days'];
            $summaryData['total_absent_days'] += $userSummary['absent_days'];
            $summaryData['total_late_days'] += $userSummary['late_days'];
            $summaryData['total_half_days'] += $userSummary['half_days'];
            $summaryData['total_working_hours'] += $userSummary['total_hours'];
            $summaryData['total_overtime_hours'] += $userSummary['total_overtime_hours'];
        }
        
        // Calculate average attendance rate
        if ($employees->count() > 0) {
            $totalAttendancePercentage = array_sum(array_column($attendanceData, 'attendance_percentage'));
            $summaryData['average_attendance_rate'] = round($totalAttendancePercentage / $employees->count(), 2);
        }
        
        // Get departments for filter
        $departments = \App\Models\Department::when($companyId, function($q) use ($companyId) {
            return $q->forCompany($companyId);
        })->active()->get();
        
        // Get companies for SuperAdmin
        $companies = Auth::user()->hasRole('superAdmin') ? \App\Models\Company::active()->get() : null;
        
        return [
            'attendanceData' => $attendanceData,
            'summaryData' => $summaryData,
            'departments' => $departments,
            'companies' => $companies,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'companyId' => $companyId
        ];
    }

    /**
     * Mark attendance for employee (HR/Admin function).
     */
    public function markAttendance(Request $request)
    {
        $this->authorize('create', Attendance::class);
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,half_day,late,early_leave,unpaid_leave,without_notice',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'notes' => 'nullable|string|max:500'
        ]);
        
        $targetUser = User::findOrFail($request->user_id);
        
        // Check if current user can mark attendance for the target user using Gate
        if (!Auth::user()->can('markForOthers', [Attendance::class, $targetUser])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to mark attendance for this user']);
        }
        
        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'date' => $request->date
            ],
            [
                'company_id' => $targetUser->company_id,
                'check_in_time' => $request->check_in_time,
                'check_out_time' => $request->check_out_time,
                'status' => $request->status,
                'notes' => $request->notes,
                'is_weekend' => Carbon::parse($request->date)->isWeekend(),
            ]
        );
        
        // Update calculated fields
        $attendance->updateStatus();
        
        return response()->json([
            'success' => true,
            'message' => 'Attendance marked successfully',
            'attendance' => $attendance
        ]);
    }

    /**
     * Get working hours settings for company.
     */
    private function getWorkingHoursSettings($companyId)
    {
        $workingHours = \App\Models\WorkingHoursSettings::getForCompany($companyId);
        
        return [
            'standard_hours' => $workingHours->standard_hours,
            'late_threshold' => $workingHours->late_threshold,
            'check_in_time' => substr($workingHours->check_in_time, 0, 5), // Remove seconds for display
            'check_out_time' => substr($workingHours->check_out_time, 0, 5), // Remove seconds for display
            'break_duration' => $workingHours->break_duration,
            'overtime_rate' => $workingHours->overtime_rate,
            'grace_period' => $workingHours->grace_period,
            'early_leave_threshold' => $workingHours->early_leave_threshold,
            'auto_break_deduction' => $workingHours->auto_break_deduction,
            'flexible_hours' => $workingHours->flexible_hours,
            'working_days' => $workingHours->working_days,
            'track_location' => $workingHours->track_location
        ];
    }

    /**
     * Get attendance statistics API.
     */
    public function getStats(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        if ($user->hasRole('Employee')) {
            // Employee stats
            $stats = Attendance::getUserSummary(
                $user->id,
                Carbon::parse($date)->startOfMonth(),
                Carbon::parse($date)->endOfMonth()
            );
        } else {
            // Management stats
            $stats = Attendance::getCompanySummary($companyId, $date);
        }
        
        return response()->json($stats);
    }

    /**
     * Export attendance report.
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);
        
        // This would typically use Laravel Excel or similar
        // For now, return JSON data
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $reportData = $this->generateAttendanceReport($companyId, $dateFrom, $dateTo, $request);
        
        return response()->json($reportData, 200, [
            'Content-Type' => 'application/json; charset=utf-8'
        ]);
    }

    /**
     * Get employees that current user can manage attendance for.
     */
    public function getEmployees(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        // Build query based on user role
        $query = User::with(['department', 'roles'])
            ->where('company_id', $companyId)
            ->where('status', true); // Only active users
            
        // Filter based on what the current user can manage
        if ($user->hasRole('HR')) {
            // HR can manage everyone except SuperAdmin and Admin
            $query->whereDoesntHave('roles', function($roleQuery) {
                $roleQuery->whereIn('name', ['superAdmin', 'Admin']);
            });
        } elseif ($user->hasRole('Admin')) {
            // Admin can manage everyone except SuperAdmin and other Admins
            $query->whereDoesntHave('roles', function($roleQuery) {
                $roleQuery->whereIn('name', ['superAdmin', 'Admin']);
            });
        } elseif ($user->hasRole('superAdmin')) {
            // SuperAdmin can manage everyone
            // No additional filtering needed
        } else {
            // Other roles cannot manage anyone
            return response()->json(['employees' => []]);
        }
        
        $employees = $query->orderBy('name')->get()->map(function($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'department' => $employee->department ? $employee->department->name : 'N/A',
                'roles' => $employee->roles->pluck('name')->toArray()
            ];
        });
        
        return response()->json([
            'success' => true,
            'employees' => $employees
        ]);
    }
}
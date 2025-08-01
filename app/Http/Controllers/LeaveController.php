<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Notifications\LeaveApplicationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Display leave dashboard based on user role.
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
     * Employee leave dashboard.
     */
    private function employeeDashboard(Request $request)
    {
        $user = Auth::user();
        $currentYear = date('Y');
        
        // Get leave balances
        $leaveBalances = LeaveBalance::getUserSummary($user->id, $currentYear);
        
        // Get user's leaves (recent)
        $userLeaves = Leave::with(['leaveType', 'approver'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get available leave types for user
        $availableLeaveTypes = LeaveType::forCompany($user->company_id)
            ->active()
            ->get()
            ->filter(function($leaveType) use ($user) {
                return $leaveType->isAvailableForRole($user->getRoleNames()->first());
            });
            
        // Get upcoming leaves
        $upcomingLeaves = Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->limit(3)
            ->get();
        
        return view('EmployeeManagemntsystem.Employee.leave.dashboard', compact(
            'leaveBalances',
            'userLeaves',
            'availableLeaveTypes',
            'upcomingLeaves'
        ));
    }

    /**
     * Management leave dashboard (HR, Admin, SuperAdmin).
     */
    private function managementDashboard(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        // Get pending leaves for approval
        $pendingLeaves = Leave::with(['user', 'leaveType'])
            ->reviewableBy($user)
            ->pending()
            ->orderBy('applied_at')
            ->paginate(20);
        
        // Get leave statistics
        $leaveStats = $this->getLeaveStatistics($companyId);
        
        // Get filters
        $status = $request->get('status');
        $leaveTypeId = $request->get('leave_type_id');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));
        
        // Get all leaves with filters  
        $leavesQuery = Leave::with(['user', 'leaveType', 'approver'])
            ->reviewableBy($user)
            ->when($status, function($q) use ($status) {
                return $q->where('status', $status);
            })
            ->when($leaveTypeId, function($q) use ($leaveTypeId) {
                return $q->where('leave_type_id', $leaveTypeId);
            })
            ->dateRange($dateFrom, $dateTo)
            ->orderBy('applied_at', 'desc');
            
        $allLeaves = $leavesQuery->paginate(15);
        
        // Get leave types for filter
        $leaveTypes = LeaveType::forCompany($companyId)->active()->get();
        
        // Get companies for SuperAdmin
        $companies = $user->hasRole('superAdmin') ? \App\Models\Company::active()->get() : null;
        
        // Determine view based on user role
        $viewName = 'EmployeeManagemntsystem.Admin.leave.dashboard';
        if ($user->hasRole('HR')) {
            $viewName = 'EmployeeManagemntsystem.HR.leave.dashboard';
        }
        
        return view($viewName, compact(
            'pendingLeaves',
            'allLeaves',
            'leaveStats',
            'leaveTypes',
            'companies',
            'status',
            'leaveTypeId',
            'dateFrom',
            'dateTo',
            'companyId'
        ));
    }

    /**
     * Show the form for creating a new leave application.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get available leave types
        $leaveTypes = LeaveType::forCompany($user->company_id)
            ->active()
            ->get()
            ->filter(function($leaveType) use ($user) {
                return $leaveType->isAvailableForRole($user->getRoleNames()->first());
            });
        
        // Get leave balances
        $leaveBalances = LeaveBalance::getUserSummary($user->id);
        
        return view('EmployeeManagemntsystem.Employee.leave.create', compact(
            'leaveTypes',
            'leaveBalances'
        ));
    }

    /**
     * Store a newly created leave application.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration' => 'required|in:full_day,first_half,second_half',
            'reason' => 'required|string|max:1000',
            'comments' => 'nullable|string|max:500',
            'contact_number' => 'nullable|string|max:20',
            'handover_notes' => 'nullable|string|max:1000',
            'emergency_leave' => 'boolean',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        try {
            DB::beginTransaction();
            
            $leaveType = LeaveType::findOrFail($request->leave_type_id);
            
            // Create leave application
            $leave = new Leave();
            $leave->fill($request->all());
            $leave->user_id = $user->id;
            $leave->company_id = $user->company_id;
            $leave->emergency_leave = $request->boolean('emergency_leave');
            
            // Calculate total days
            $leave->total_days = $leave->calculateTotalDays();
            
            // Validate leave rules
            $this->validateLeaveRules($leave, $leaveType, $user);
            
            // Handle file uploads
            if ($request->hasFile('attachments')) {
                $attachments = [];
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('leave-attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType()
                    ];
                }
                $leave->attachments = $attachments;
            }
            
            $leave->save();
            
            // Update leave balance (add pending days)
            $leaveBalance = LeaveBalance::getOrCreateBalance($user->id, $request->leave_type_id);
            $leaveBalance->addPendingLeave($leave->total_days, $leave->id);
            
            // Send notification to HR/Admin
            $this->sendLeaveApplicationNotification($leave, 'applied');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Leave application submitted successfully!',
                'leave' => $leave->load(['leaveType'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified leave.
     */
    public function show(Leave $leave)
    {
        $this->authorize('view', $leave);
        
        $leave->load(['user', 'leaveType', 'approver', 'company']);
        
        return view('EmployeeManagemntsystem.Employee.leave.show', compact('leave'));
    }

    /**
     * Show the form for editing the specified leave.
     */
    public function edit(Leave $leave)
    {
        $this->authorize('update', $leave);
        
        if (!$leave->canBeEdited()) {
            return redirect()->back()->with('error', 'This leave application cannot be edited.');
        }
        
        $user = Auth::user();
        
        // Get available leave types
        $leaveTypes = LeaveType::forCompany($user->company_id)
            ->active()
            ->get()
            ->filter(function($leaveType) use ($user) {
                return $leaveType->isAvailableForRole($user->getRoleNames()->first());
            });
        
        // Get leave balances
        $leaveBalances = LeaveBalance::getUserSummary($user->id);
        
        return view('EmployeeManagemntsystem.Employee.leave.edit', compact(
            'leave',
            'leaveTypes',
            'leaveBalances'
        ));
    }

    /**
     * Update the specified leave.
     */
    public function update(Request $request, Leave $leave)
    {
        $this->authorize('update', $leave);
        
        if (!$leave->canBeEdited()) {
            return response()->json([
                'success' => false,
                'message' => 'This leave application cannot be edited.'
            ], 422);
        }

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration' => 'required|in:full_day,first_half,second_half',
            'reason' => 'required|string|max:1000',
            'comments' => 'nullable|string|max:500',
            'contact_number' => 'nullable|string|max:20',
            'handover_notes' => 'nullable|string|max:1000',
            'emergency_leave' => 'boolean'
        ]);

        try {
            DB::beginTransaction();
            
            $oldDays = $leave->total_days;
            $oldLeaveTypeId = $leave->leave_type_id;
            
            $leave->fill($request->all());
            $leave->emergency_leave = $request->boolean('emergency_leave');
            
            // Recalculate total days
            $leave->total_days = $leave->calculateTotalDays();
            
            $leaveType = LeaveType::findOrFail($request->leave_type_id);
            $this->validateLeaveRules($leave, $leaveType, $leave->user);
            
            $leave->save();
            
            // Update leave balances if days or leave type changed
            if ($oldDays != $leave->total_days || $oldLeaveTypeId != $leave->leave_type_id) {
                // Remove old pending days
                $oldBalance = LeaveBalance::getOrCreateBalance($leave->user_id, $oldLeaveTypeId);
                $oldBalance->removePendingLeave($oldDays, $leave->id);
                
                // Add new pending days
                $newBalance = LeaveBalance::getOrCreateBalance($leave->user_id, $leave->leave_type_id);
                $newBalance->addPendingLeave($leave->total_days, $leave->id);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Leave application updated successfully!',
                'leave' => $leave->load(['leaveType'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Remove the specified leave (cancel/withdraw).
     */
    public function destroy(Leave $leave)
    {
        $this->authorize('delete', $leave);
        
        if (!$leave->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'This leave application cannot be cancelled.'
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $status = $leave->status === 'approved' ? 'cancelled' : 'withdrawn';
            $leave->update(['status' => $status]);
            
            // Update leave balance
            $leaveBalance = LeaveBalance::getOrCreateBalance($leave->user_id, $leave->leave_type_id);
            
            if ($leave->status === 'approved') {
                // Return used days to available
                $leaveBalance->used_days -= $leave->total_days;
                $leaveBalance->addTransaction('returned', -$leave->total_days, "Leave cancelled", $leave->id);
            } else {
                // Remove pending days
                $leaveBalance->removePendingLeave($leave->total_days, $leave->id);
            }
            
            $leaveBalance->save();
            
            // Send notification
            $this->sendLeaveApplicationNotification($leave, $status);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Leave application {$status} successfully!"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel leave application.'
            ], 500);
        }
    }

    /**
     * Approve or reject leave application.
     */
    public function reviewLeave(Request $request, Leave $leave)
    {
        $this->authorize('review', $leave);
        
        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();
            
            $leave->status = $request->action === 'approve' ? 'approved' : 'rejected';
            $leave->admin_notes = $request->admin_notes;
            $leave->approved_by = Auth::id();
            $leave->reviewed_at = now();
            $leave->save();
            
            // Update attendance records if leave affects attendance
            $leave->updateAttendanceRecords();
            
            // Update leave balance
            $leaveBalance = LeaveBalance::getOrCreateBalance($leave->user_id, $leave->leave_type_id);
            
            if ($leave->status === 'approved') {
                // Convert pending to used
                $leaveBalance->useLeave($leave->total_days, $leave->id);
            } else {
                // Remove pending days
                $leaveBalance->removePendingLeave($leave->total_days, $leave->id);
            }
            
            // Send notification to employee
            $this->sendLeaveApplicationNotification($leave, $leave->status);
            
            DB::commit();
            
            $action = $request->action === 'approve' ? 'approved' : 'rejected';
            
            return response()->json([
                'success' => true,
                'message' => "Leave application {$action} successfully!",
                'leave' => $leave->load(['user', 'leaveType'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Leave approval failed: ' . $e->getMessage(), [
                'leave_id' => $leave->id,
                'user_id' => Auth::id(),
                'action' => $request->action,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process leave application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get leave statistics for company.
     */
    private function getLeaveStatistics($companyId)
    {
        $currentMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        return [
            'pending_applications' => Leave::forCompany($companyId)->pending()->count(),
            'approved_this_month' => Leave::forCompany($companyId)
                ->approved()
                ->whereMonth('start_date', $currentMonth->month)
                ->count(),
            'total_leave_days_used' => Leave::forCompany($companyId)
                ->approved()
                ->whereYear('start_date', date('Y'))
                ->sum('total_days'),
            'employees_on_leave_today' => Leave::forCompany($companyId)
                ->approved()
                ->where('start_date', '<=', today())
                ->where('end_date', '>=', today())
                ->count()
        ];
    }

    /**
     * Validate leave application rules.
     */
    private function validateLeaveRules(Leave $leave, LeaveType $leaveType, User $user)
    {
        // Check for overlapping leaves
        if ($leave->hasOverlappingLeaves()) {
            throw new \Exception('You have overlapping leave applications for the selected dates.');
        }
        
        // Check minimum notice period
        if ($leaveType->min_notice_days > 0 && !$leave->emergency_leave) {
            $noticeGiven = now()->diffInDays($leave->start_date);
            if ($noticeGiven < $leaveType->min_notice_days) {
                throw new \Exception("Minimum {$leaveType->min_notice_days} days notice required for {$leaveType->name}.");
            }
        }
        
        // Check maximum consecutive days
        if ($leaveType->max_consecutive_days > 0 && $leave->total_days > $leaveType->max_consecutive_days) {
            throw new \Exception("Maximum {$leaveType->max_consecutive_days} consecutive days allowed for {$leaveType->name}.");
        }
        
        // Check available balance
        $leaveBalance = LeaveBalance::getOrCreateBalance($user->id, $leaveType->id);
        if (!$leaveBalance->hasSufficientBalance($leave->total_days)) {
            throw new \Exception('Insufficient leave balance. Available: ' . $leaveBalance->remaining_days . ' days.');
        }
        
        // Check if medical certificate is required
        if ($leaveType->requires_medical_certificate && $leave->total_days >= 3 && empty($leave->attachments)) {
            throw new \Exception('Medical certificate is required for ' . $leaveType->name . ' of 3 or more days.');
        }
    }

    /**
     * Get leave balance for user.
     */
    public function getLeaveBalance(Request $request)
    {
        $user = Auth::user();
        $leaveTypeId = $request->get('leave_type_id');
        
        if (!$leaveTypeId) {
            return response()->json(['error' => 'Leave type ID is required'], 400);
        }
        
        $balance = LeaveBalance::getOrCreateBalance($user->id, $leaveTypeId);
        
        return response()->json([
            'success' => true,
            'balance' => [
                'available' => $balance->available_days,
                'remaining' => $balance->remaining_days,
                'used' => $balance->used_days,
                'pending' => $balance->pending_days,
                'total_entitled' => $balance->total_entitled
            ]
        ]);
    }

    /**
     * Send leave application notification to appropriate users.
     */
    private function sendLeaveApplicationNotification(Leave $leave, string $action): void
    {
        try {
            $notification = new LeaveApplicationNotification($leave, $action);
            
            if ($action === 'applied') {
                // Notify HR and Admin users when employee applies for leave
                $recipients = User::where('company_id', $leave->company_id)
                    ->whereHas('roles', function($query) {
                        $query->whereIn('name', ['HR', 'Admin']);
                    })
                    ->get();
                    
                Notification::send($recipients, $notification);
            } else {
                // Notify the employee when their leave is approved/rejected/cancelled
                $leave->user->notify($notification);
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to send leave notification: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve leave applications.
     */
    public function bulkApprove(Request $request)
    {
        $this->authorize('bulkApprove', Leave::class);
        
        $request->validate([
            'leave_ids' => 'required|array',
            'leave_ids.*' => 'exists:leaves,id',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();
            
            $processed = 0;
            $failed = 0;
            
            foreach ($request->leave_ids as $leaveId) {
                $leave = Leave::find($leaveId);
                
                if ($leave && $leave->status === 'pending') {
                    // Check if user can review this specific leave
                    if (!auth()->user()->can('review', $leave)) {
                        $failed++;
                        continue;
                    }
                    
                    try {
                        $leave->status = 'approved';
                        $leave->admin_notes = $request->admin_notes ?? 'Bulk approved by ' . auth()->user()->getRoleNames()->first();
                        $leave->approved_by = Auth::id();
                        $leave->reviewed_at = now();
                        $leave->save();
                        
                        // Update attendance records if leave affects attendance
                        $leave->updateAttendanceRecords();
                        
                        // Update leave balance
                        $leaveBalance = LeaveBalance::getOrCreateBalance($leave->user_id, $leave->leave_type_id);
                        $leaveBalance->useLeave($leave->total_days, $leave->id);
                        
                        // Send notification
                        $this->sendLeaveApplicationNotification($leave, 'approved');
                        
                        $processed++;
                    } catch (\Exception $e) {
                        $failed++;
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Bulk operation completed. {$processed} applications approved" . ($failed > 0 ? ", {$failed} failed" : ""),
                'processed' => $processed,
                'failed' => $failed
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk approval failed'
            ], 500);
        }
    }

    /**
     * Bulk reject leave applications.
     */
    public function bulkReject(Request $request)
    {
        $this->authorize('bulkApprove', Leave::class);
        
        $request->validate([
            'leave_ids' => 'required|array',
            'leave_ids.*' => 'exists:leaves,id',
            'admin_notes' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();
            
            $processed = 0;
            $failed = 0;
            
            foreach ($request->leave_ids as $leaveId) {
                $leave = Leave::find($leaveId);
                
                if ($leave && $leave->status === 'pending') {
                    // Check if user can review this specific leave
                    if (!auth()->user()->can('review', $leave)) {
                        $failed++;
                        continue;
                    }
                    
                    try {
                        $leave->status = 'rejected';
                        $leave->admin_notes = $request->admin_notes;
                        $leave->approved_by = Auth::id();
                        $leave->reviewed_at = now();
                        $leave->save();
                        
                        // Update attendance records if leave affects attendance
                        $leave->updateAttendanceRecords();
                        
                        // Remove pending days from balance
                        $leaveBalance = LeaveBalance::getOrCreateBalance($leave->user_id, $leave->leave_type_id);
                        $leaveBalance->removePendingLeave($leave->total_days, $leave->id);
                        
                        // Send notification
                        $this->sendLeaveApplicationNotification($leave, 'rejected');
                        
                        $processed++;
                    } catch (\Exception $e) {
                        $failed++;
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Bulk operation completed. {$processed} applications rejected" . ($failed > 0 ? ", {$failed} failed" : ""),
                'processed' => $processed,
                'failed' => $failed
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk rejection failed'
            ], 500);
        }
    }

    /**
     * Export leave data.
     */
    public function export(Request $request)
    {
        $this->authorize('export', Leave::class);
        
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $status = $request->get('status');
        $leaveTypeId = $request->get('leave_type_id');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));
        
        $leaves = Leave::with(['user', 'leaveType', 'approver'])
            ->when($companyId, function($q) use ($companyId) {
                return $q->forCompany($companyId);
            })
            ->when($status, function($q) use ($status) {
                return $q->where('status', $status);
            })
            ->when($leaveTypeId, function($q) use ($leaveTypeId) {
                return $q->where('leave_type_id', $leaveTypeId);
            })
            ->dateRange($dateFrom, $dateTo)
            ->orderBy('applied_at', 'desc')
            ->get();

        $filename = 'leave_report_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($leaves) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Application ID',
                'Employee Name',
                'Employee ID',
                'Department',
                'Leave Type',
                'Start Date',
                'End Date',
                'Total Days',
                'Status',
                'Applied Date',
                'Reviewed By',
                'Reviewed Date',
                'Notes'
            ]);
            
            // CSV Data
            foreach ($leaves as $leave) {
                fputcsv($file, [
                    $leave->application_id,
                    $leave->user->name,
                    $leave->user->employee_id ?? '-',
                    $leave->user->department->name ?? '-',
                    $leave->leaveType->name,
                    $leave->start_date,
                    $leave->end_date,
                    $leave->total_days,
                    ucfirst($leave->status),
                    $leave->applied_at->format('Y-m-d H:i:s'),
                    $leave->approver->name ?? '-',
                    $leave->reviewed_at ? $leave->reviewed_at->format('Y-m-d H:i:s') : '-',
                    $leave->admin_notes ?? '-'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export employee leave report.
     */
    public function exportEmployeeReport(Request $request)
    {
        $this->authorize('export', Leave::class);
        
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $employees = User::with(['department', 'leaves' => function($q) {
                $q->whereYear('start_date', date('Y'));
            }])
            ->where('company_id', $companyId)
            ->whereHas('roles', function($q) {
                $q->where('name', 'Employee');
            })
            ->get();

        $filename = 'employee_leave_summary_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($employees) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Employee Name',
                'Employee ID',
                'Department',
                'Total Applications',
                'Approved Leave Days',
                'Pending Applications',
                'Rejected Applications',
                'Last Leave Date'
            ]);
            
            // CSV Data
            foreach ($employees as $employee) {
                $totalApplications = $employee->leaves->count();
                $approvedDays = $employee->leaves->where('status', 'approved')->sum('total_days');
                $pendingCount = $employee->leaves->where('status', 'pending')->count();
                $rejectedCount = $employee->leaves->where('status', 'rejected')->count();
                $lastLeave = $employee->leaves->where('status', 'approved')->sortByDesc('end_date')->first();
                
                fputcsv($file, [
                    $employee->name,
                    $employee->employee_id ?? '-',
                    $employee->department->name ?? '-',
                    $totalApplications,
                    $approvedDays,
                    $pendingCount,
                    $rejectedCount,
                    $lastLeave ? $lastLeave->end_date : '-'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get leave balance report.
     */
    public function leaveBalanceReport(Request $request)
    {
        $this->authorize('export', Leave::class);
        
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $balances = LeaveBalance::with(['user', 'leaveType'])
            ->whereHas('user', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->where('year', date('Y'))
            ->get()
            ->groupBy('user_id');

        $filename = 'leave_balance_report_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($balances) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Employee Name',
                'Employee ID',
                'Department',
                'Leave Type',
                'Total Entitled',
                'Used Days',
                'Pending Days',
                'Available Days'
            ]);
            
            // CSV Data
            foreach ($balances as $userBalances) {
                $user = $userBalances->first()->user;
                
                foreach ($userBalances as $balance) {
                    $available = $balance->total_entitled - $balance->used_days - $balance->pending_days;
                    
                    fputcsv($file, [
                        $user->name,
                        $user->employee_id ?? '-',
                        $user->department->name ?? '-',
                        $balance->leaveType->name,
                        $balance->total_entitled,
                        $balance->used_days,
                        $balance->pending_days,
                        $available
                    ]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
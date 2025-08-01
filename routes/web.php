<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AdminSalaryController;
use App\Http\Controllers\HRDashboardController;
use App\Http\Controllers\HRIncrementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WorkingHoursController;
use App\Http\Controllers\SalaryHistoryController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\TeamLeaveCalendarController;
use App\Http\Controllers\UserManagement\HrController;
use App\Http\Controllers\UserManagement\AdminController;
use App\Http\Controllers\UserManagement\TeamLeadController;
use App\Http\Controllers\UserManagement\SuperAdminController;


// Authentication routes all 
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('loginpost');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


// ----------------------------
// SUPER ADMIN
// ----------------------------
Route::middleware(['role:superAdmin'])->prefix('superadmin')->as('superAdmin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('EmployeeManagemntsystem.SuperAdmin.dashboard');
    })->name('dashboard');

    // Company Routes
    Route::resource('/company', CompanyController::class)->names('company');
    Route::post('/company/validate-field', [CompanyController::class, 'validateField'])->name('company.validate-field');
    Route::post('/company/{id}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('company.toggle-status');
    
    // User Management Routes
    Route::resource('/users', SuperAdminController::class)->names('users');
    
    // Attendance Routes for SuperAdmin
    Route::prefix('attendance')->as('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/reports', [AttendanceController::class, 'reports'])->name('reports');
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');
        Route::post('/mark', [AttendanceController::class, 'markAttendance'])->name('mark');
        Route::get('/stats', [AttendanceController::class, 'getStats'])->name('stats');
    });
    
    // Working Hours Settings Routes for SuperAdmin
    Route::prefix('working-hours')->as('working-hours.')->group(function () {
        Route::get('/', [WorkingHoursController::class, 'index'])->name('index');
        Route::get('/create', [WorkingHoursController::class, 'create'])->name('create');
        Route::post('/', [WorkingHoursController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [WorkingHoursController::class, 'edit'])->name('edit');
        Route::put('/{id}', [WorkingHoursController::class, 'update'])->name('update');
        Route::delete('/{id}', [WorkingHoursController::class, 'destroy'])->name('destroy');
        Route::get('/settings', [WorkingHoursController::class, 'getSettings'])->name('settings');
    });
    
    // Leave Management Routes for SuperAdmin
    Route::prefix('leave')->as('leave.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::post('/{leave}/review', [LeaveController::class, 'reviewLeave'])->name('review');
        Route::get('/balance/{userId?}', [LeaveController::class, 'getLeaveBalance'])->name('balance');
    });
    
    // Leave Types Management Routes for SuperAdmin
    Route::prefix('leave-types')->as('leave-types.')->group(function () {
        Route::get('/', [LeaveTypeController::class, 'index'])->name('index');
        Route::get('/create', [LeaveTypeController::class, 'create'])->name('create');
        Route::post('/', [LeaveTypeController::class, 'store'])->name('store');
        Route::get('/{leaveType}', [LeaveTypeController::class, 'show'])->name('show');
        Route::get('/{leaveType}/edit', [LeaveTypeController::class, 'edit'])->name('edit');
        Route::put('/{leaveType}', [LeaveTypeController::class, 'update'])->name('update');
        Route::delete('/{leaveType}', [LeaveTypeController::class, 'destroy'])->name('destroy');
        Route::post('/{leaveType}/toggle-status', [LeaveTypeController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/create-defaults', [LeaveTypeController::class, 'createDefaults'])->name('create-defaults');
    });
});


// ----------------------------
// COMPANY ADMIN & SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|Admin'])->prefix('admin')->as('Admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Admin can only edit their own company (no create, delete, or index)
    Route::get('/company/edit', [CompanyController::class, 'editOwn'])->name('company.edit');
    Route::put('/company/update', [CompanyController::class, 'updateOwn'])->name('company.update');
    Route::post('/company/validate-field', [CompanyController::class, 'validateField'])->name('company.validate-field');
    
    // Department Routes
    Route::middleware(['company.scope'])->group(function () {
        Route::resource('departments', DepartmentController::class)->names('departments');
        Route::get('departments-assignments', [DepartmentController::class, 'assignments'])->name('departments.assignments');
        Route::post('departments-assign-user', [DepartmentController::class, 'assignUser'])->name('departments.assign-user');
        Route::post('departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
    });
    
    // User Management Routes for Admin
    Route::resource('/users', AdminController::class)->names('users');
    
    // Attendance Routes for Admin
    Route::middleware(['company.scope'])->prefix('attendance')->as('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/reports', [AttendanceController::class, 'reports'])->name('reports');
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');
        Route::post('/mark', [AttendanceController::class, 'markAttendance'])->name('mark');
        Route::get('/stats', [AttendanceController::class, 'getStats'])->name('stats');
        Route::get('/employees', [AttendanceController::class, 'getEmployees'])->name('employees');
    });
    
    // Working Hours Settings Routes for Admin
    Route::middleware(['company.scope'])->prefix('working-hours')->as('working-hours.')->group(function () {
        Route::get('/', [WorkingHoursController::class, 'index'])->name('index');
        Route::get('/create', [WorkingHoursController::class, 'create'])->name('create');
        Route::post('/', [WorkingHoursController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [WorkingHoursController::class, 'edit'])->name('edit');
        Route::put('/{id}', [WorkingHoursController::class, 'update'])->name('update');
        Route::delete('/{id}', [WorkingHoursController::class, 'destroy'])->name('destroy');
        Route::get('/settings', [WorkingHoursController::class, 'getSettings'])->name('settings');
    });
    
    // Leave Management Routes for Admin
    Route::middleware(['company.scope'])->prefix('leave')->as('leave.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('/create', [LeaveController::class, 'create'])->name('create');
        Route::post('/', [LeaveController::class, 'store'])->name('store');
        Route::get('/{leave}', [LeaveController::class, 'show'])->name('show');
        Route::post('/{leave}/review', [LeaveController::class, 'reviewLeave'])->name('review');
        Route::get('/balance/{userId?}', [LeaveController::class, 'getLeaveBalance'])->name('balance');
        Route::get('/export/data', [LeaveController::class, 'export'])->name('export');
        Route::get('/export/employee-report', [LeaveController::class, 'exportEmployeeReport'])->name('export.employee');
        Route::get('/export/balance-report', [LeaveController::class, 'leaveBalanceReport'])->name('export.balance');
        Route::post('/bulk-approve', [LeaveController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [LeaveController::class, 'bulkReject'])->name('bulk-reject');
        Route::get('/calendar', [TeamLeaveCalendarController::class, 'index'])->name('calendar');
    });
    
    // Leave Types Management Routes for Admin
    Route::middleware(['company.scope'])->prefix('leave-types')->as('leave-types.')->group(function () {
        Route::get('/', [LeaveTypeController::class, 'index'])->name('index');
        Route::get('/create', [LeaveTypeController::class, 'create'])->name('create');
        Route::post('/', [LeaveTypeController::class, 'store'])->name('store');
        Route::get('/{leaveType}', [LeaveTypeController::class, 'show'])->name('show');
        Route::get('/{leaveType}/edit', [LeaveTypeController::class, 'edit'])->name('edit');
        Route::put('/{leaveType}', [LeaveTypeController::class, 'update'])->name('update');
        Route::delete('/{leaveType}', [LeaveTypeController::class, 'destroy'])->name('destroy');
        Route::post('/{leaveType}/toggle-status', [LeaveTypeController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/create-defaults', [LeaveTypeController::class, 'createDefaults'])->name('create-defaults');
    });
});


// ----------------------------
// HR + ADMIN + SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|Admin|HR'])->prefix('hr')->as('HR.')->group(function () {
    Route::get('/dashboard', [HRDashboardController::class, 'index'])->name('dashboard');
    
    // User Management Routes for HR
    Route::middleware(['company.scope'])->group(function () {
        Route::resource('/users', HrController::class)->names('users');
    });
    
    // Department Management Routes for HR
    Route::middleware(['company.scope'])->group(function () {
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('/departments-assignments', [DepartmentController::class, 'assignments'])->name('departments.assignments');
        Route::post('/departments-assign-user', [DepartmentController::class, 'assignUser'])->name('departments.assign-user');
        Route::get('/departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
    });
    
    // Attendance Routes for HR
    Route::middleware(['company.scope'])->prefix('attendance')->as('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/reports', [AttendanceController::class, 'reports'])->name('reports');
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');
        Route::post('/mark', [AttendanceController::class, 'markAttendance'])->name('mark');
        Route::get('/stats', [AttendanceController::class, 'getStats'])->name('stats');
        Route::get('/employees', [AttendanceController::class, 'getEmployees'])->name('employees');
    });
    
    // Working Hours Management Routes for HR (read-only)
    Route::middleware(['company.scope'])->prefix('working-hours')->as('working-hours.')->group(function () {
        Route::get('/', [WorkingHoursController::class, 'index'])->name('index');
        Route::get('/settings', [WorkingHoursController::class, 'getSettings'])->name('settings');
    });
    
    // Leave Management Routes for HR
    Route::middleware(['company.scope'])->prefix('leave')->as('leave.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('/create', [LeaveController::class, 'create'])->name('create');
        Route::post('/', [LeaveController::class, 'store'])->name('store');
        Route::get('/{leave}', [LeaveController::class, 'show'])->name('show');
        Route::post('/{leave}/review', [LeaveController::class, 'reviewLeave'])->name('review');
        Route::get('/balance/{userId?}', [LeaveController::class, 'getLeaveBalance'])->name('balance');
        Route::get('/export/data', [LeaveController::class, 'export'])->name('export');
        Route::get('/export/employee-report', [LeaveController::class, 'exportEmployeeReport'])->name('export.employee');
        Route::get('/export/balance-report', [LeaveController::class, 'leaveBalanceReport'])->name('export.balance');
        Route::post('/bulk-approve', [LeaveController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [LeaveController::class, 'bulkReject'])->name('bulk-reject');
        Route::get('/calendar', [TeamLeaveCalendarController::class, 'index'])->name('calendar');
    });
    
    // Leave Types Management Routes for HR (read-only)
    Route::middleware(['company.scope'])->prefix('leave-types')->as('leave-types.')->group(function () {
        Route::get('/', [LeaveTypeController::class, 'index'])->name('index');
        Route::get('/{leaveType}', [LeaveTypeController::class, 'show'])->name('show');
    });
    
    // Salary Increment Requests Routes for HR
    Route::middleware(['company.scope'])->prefix('increment-requests')->as('increment-requests.')->group(function () {
        Route::get('/', [HRIncrementController::class, 'index'])->name('index');
        Route::post('/', [HRIncrementController::class, 'store'])->name('store');
        Route::get('/employee/{id}', [HRIncrementController::class, 'getEmployeeDetails'])->name('employee-details');
        Route::get('/my-requests', [HRIncrementController::class, 'getMyRequests'])->name('my-requests');
    });
});


// ----------------------------
// ADMIN SALARY MANAGEMENT - ADMIN + SUPERADMIN ONLY
// ----------------------------
Route::middleware(['role:superAdmin|Admin'])->prefix('admin')->as('admin.')->group(function () {
    // Salary Management Routes (Admin Only)
    Route::middleware(['company.scope'])->prefix('salary-management')->as('salary-management.')->group(function () {
        Route::get('/employees', [AdminSalaryController::class, 'getEmployees'])->name('employees');
        Route::post('/update', [AdminSalaryController::class, 'updateSalary'])->name('update');
    });
    
    // Increment Request Review Routes (Admin Only)
    Route::middleware(['company.scope'])->prefix('increment-requests')->as('increment-requests.')->group(function () {
        Route::get('/', [AdminSalaryController::class, 'getIncrementRequests'])->name('index');
        Route::post('/review', [AdminSalaryController::class, 'reviewIncrementRequest'])->name('review');
    });
});


// ----------------------------
// TEAM LEAD + HR + ADMIN + SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|Admin|HR|TeamLead'])->prefix('teamlead')->as('TeamLead.')->group(function () {
    Route::get('/dashboard', function () {
        return view('EmployeeManagemntsystem.TeamLead.dashboard');
    })->name('dashboard');

    // User Management Routes for Team Lead (only show, edit, delete - no create)
    Route::resource('/users', TeamLeadController::class)->except(['create', 'store'])->names('users');
});


// ----------------------------
// FINANCE + ADMIN + SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|Admin|Finance', 'finance'])->prefix('finance')->as('Finance.')->group(function () {
    Route::get('/dashboard', [FinanceController::class, 'index'])->name('dashboard');
    
    // Salary Management Routes
    Route::middleware(['company.scope'])->prefix('salaries')->as('salaries.')->group(function () {
        Route::get('/', [FinanceController::class, 'salaries'])->name('index');
        Route::put('/update/{user}', [FinanceController::class, 'updateSalary'])->name('update');
        Route::post('/bulk-update', [FinanceController::class, 'bulkUpdateSalaries'])->name('bulk-update');
        Route::get('/export', [FinanceController::class, 'exportSalaries'])->name('export');
    });
    
    // Payroll Management Routes
    Route::middleware(['company.scope'])->prefix('payroll')->as('payroll.')->group(function () {
        Route::get('/', [FinanceController::class, 'payroll'])->name('index');
        Route::post('/process', [FinanceController::class, 'processPayroll'])->name('process');
        Route::get('/export', [FinanceController::class, 'exportPayroll'])->name('export');
        Route::post('/generate/{user}', [FinanceController::class, 'generateIndividualPayroll'])->name('generate-individual');
    });
    
    // Expense Management Routes  
    Route::middleware(['company.scope'])->prefix('expenses')->as('expenses.')->group(function () {
        Route::get('/', [FinanceController::class, 'expenses'])->name('index');
        Route::post('/approve/{expense}', [FinanceController::class, 'approveExpense'])->name('approve');
        Route::post('/reject/{expense}', [FinanceController::class, 'rejectExpense'])->name('reject');
        Route::post('/reimburse/{expense}', [FinanceController::class, 'reimburseExpense'])->name('reimburse');
        Route::get('/export', [FinanceController::class, 'exportExpenses'])->name('export');
    });
    
    // Financial Reports Routes
    Route::middleware(['company.scope'])->prefix('reports')->as('reports.')->group(function () {
        Route::get('/', [FinanceController::class, 'reports'])->name('index');
        Route::get('/export', [FinanceController::class, 'exportReport'])->name('export');
        Route::get('/payroll-summary', [FinanceController::class, 'payrollSummaryReport'])->name('payroll-summary');
        Route::get('/expense-summary', [FinanceController::class, 'expenseSummaryReport'])->name('expense-summary');
    });
    
    // Tax Management Routes
    Route::middleware(['company.scope'])->prefix('tax')->as('tax.')->group(function () {
        // Tax Dashboard
        Route::get('/', [TaxController::class, 'index'])->name('index');
        
        // Tax Rates Management
        Route::get('/rates', [TaxController::class, 'taxRates'])->name('rates.index');
        Route::post('/rates', [TaxController::class, 'storeTaxRate'])->name('rates.store');
        Route::put('/rates/{taxRate}', [TaxController::class, 'updateTaxRate'])->name('rates.update');
        Route::delete('/rates/{taxRate}', [TaxController::class, 'destroyTaxRate'])->name('rates.destroy');
        
        // Tax Brackets Management
        Route::get('/brackets', [TaxController::class, 'taxBrackets'])->name('brackets.index');
        Route::post('/brackets', [TaxController::class, 'storeTaxBracket'])->name('brackets.store');
        Route::put('/brackets/{taxBracket}', [TaxController::class, 'updateTaxBracket'])->name('brackets.update');
        Route::delete('/brackets/{taxBracket}', [TaxController::class, 'destroyTaxBracket'])->name('brackets.destroy');
        
        // Tax Deductions Management
        Route::get('/deductions', [TaxController::class, 'taxDeductions'])->name('deductions.index');
        Route::post('/deductions', [TaxController::class, 'storeTaxDeduction'])->name('deductions.store');
        Route::put('/deductions/{taxDeduction}', [TaxController::class, 'updateTaxDeduction'])->name('deductions.update');
        Route::delete('/deductions/{taxDeduction}', [TaxController::class, 'destroyTaxDeduction'])->name('deductions.destroy');
        
        // Employee Tax Information Management
        Route::get('/employee-info', [TaxController::class, 'employeeTaxInfo'])->name('employee-info.index');
        Route::put('/employee-info/{employeeTaxInfo}', [TaxController::class, 'updateEmployeeTaxInfo'])->name('employee-info.update');
        
        // Tax Configuration & Tools
        Route::post('/setup-defaults', [TaxController::class, 'setupDefaults'])->name('setup-defaults');
        Route::post('/calculate', [TaxController::class, 'calculateTax'])->name('calculate');
    });
});


// ----------------------------
// EMPLOYEE ONLY
// ----------------------------
Route::middleware(['role:Employee'])->prefix('employee')->as('Employee.')->group(function () {
    Route::get('/dashboard', function () {
        return view('EmployeeManagemntsystem.Employee.dashboard');
    })->name('dashboard');
    
    // Attendance Routes for Employee
    Route::prefix('attendance')->as('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
        Route::post('/start-break', [AttendanceController::class, 'startBreak'])->name('start-break');
        Route::post('/end-break', [AttendanceController::class, 'endBreak'])->name('end-break');
        Route::get('/stats', [AttendanceController::class, 'getStats'])->name('stats');
    });
    
    // Leave Management Routes for Employee
    Route::prefix('leave')->as('leave.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('/create', [LeaveController::class, 'create'])->name('create');
        Route::post('/', [LeaveController::class, 'store'])->name('store');
        Route::get('/{leave}', [LeaveController::class, 'show'])->name('show');
        Route::get('/{leave}/edit', [LeaveController::class, 'edit'])->name('edit');
        Route::put('/{leave}', [LeaveController::class, 'update'])->name('update');
        Route::delete('/{leave}', [LeaveController::class, 'destroy'])->name('destroy');
        Route::get('/balance/{leaveTypeId?}', [LeaveController::class, 'getLeaveBalance'])->name('balance');
        Route::get('/calendar', [TeamLeaveCalendarController::class, 'index'])->name('calendar');
    });
});

// Notification routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// Role-based notification pages
Route::middleware(['auth'])->group(function () {
    // Admin notifications
    Route::get('/admin/notifications', [NotificationController::class, 'adminIndex'])->name('Admin.notifications.index');
    
    // HR notifications
    Route::get('/hr/notifications', [NotificationController::class, 'hrIndex'])->name('HR.notifications.index');
    
    // Employee notifications
    Route::get('/employee/notifications', [NotificationController::class, 'employeeIndex'])->name('Employee.notifications.index');
});

// Profile routes
Route::middleware(['auth'])->group(function () {
    // General profile routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile/image', [ProfileController::class, 'deleteProfileImage'])->name('profile.image.delete');
    Route::get('/profile/data', [ProfileController::class, 'getProfile'])->name('profile.data');
    Route::put('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');
});

// Role-based profile routes
Route::middleware(['auth'])->group(function () {
    // SuperAdmin profile
    Route::get('/superadmin/profile', [ProfileController::class, 'index'])->name('superAdmin.profile.index');
    
    // Admin profile
    Route::get('/admin/profile', [ProfileController::class, 'index'])->name('Admin.profile.index');
    
    // HR profile
    Route::get('/hr/profile', [ProfileController::class, 'index'])->name('HR.profile.index');
    
    // Employee profile
    Route::get('/employee/profile', [ProfileController::class, 'index'])->name('Employee.profile.index');
});

// Salary History routes
Route::middleware(['auth'])->prefix('salary-history')->as('salary-history.')->group(function () {
    // Employee salary history - accessible by authorized users
    Route::get('/employee/{employeeId}', [SalaryHistoryController::class, 'getEmployeeSalaryHistory'])->name('employee');
    Route::get('/employee/{employeeId}/stats', [SalaryHistoryController::class, 'getEmployeeSalaryStats'])->name('employee.stats');
    Route::get('/employee/{employeeId}/export', [SalaryHistoryController::class, 'exportSalaryHistory'])->name('employee.export');
    
    // Company-wide salary history - Admin/Finance only
    Route::get('/company', [SalaryHistoryController::class, 'getCompanySalaryHistory'])->name('company');
    Route::get('/export', [SalaryHistoryController::class, 'exportSalaryHistory'])->name('export');
});

// Team Leave Calendar routes
Route::middleware(['auth'])->prefix('calendar')->as('calendar.')->group(function () {
    // Calendar page - accessible to all authenticated users
    Route::get('/leave', [TeamLeaveCalendarController::class, 'index'])->name('leave');
    
    // API endpoints for calendar data - accessible to all authenticated users
    Route::get('/events', [TeamLeaveCalendarController::class, 'getCalendarEvents'])->name('events');
    Route::get('/employees', [TeamLeaveCalendarController::class, 'getEmployees'])->name('employees');
    
    // Advanced features - Admin/HR/TeamLead only
    Route::middleware(['role:Admin|superAdmin|HR|TeamLead'])->group(function () {
        Route::get('/availability', [TeamLeaveCalendarController::class, 'getTeamAvailability'])->name('availability');
        Route::get('/conflicts', [TeamLeaveCalendarController::class, 'getLeaveConflicts'])->name('conflicts');
        Route::get('/department-stats', [TeamLeaveCalendarController::class, 'getDepartmentStats'])->name('department-stats');
    });
});


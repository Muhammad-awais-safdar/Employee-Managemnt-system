<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use phpDocumentor\Reflection\DocBlock\Tags\Author;


// Authentication routes all 
Route::get('/',[AuthController::class,'index'])->name('login');
Route::post('/login',[AuthController::class,'login'])->name('loginpost');
Route::get('/logout',[AuthController::class,'logout'])->name('logout');



// ----------------------------
// SUPER ADMIN
// ----------------------------
Route::middleware(['role:superAdmin'])->prefix('superadmin')->as('superadmin.')->group(function () {
        // Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('dashboard');
        // Route::get('/company/manage', [CompanyController::class, 'index'])->name('company.manage');
        // Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
        // Route::get('/global/config', [GlobalConfigController::class, 'index'])->name('global.config');
    });


// ----------------------------
// COMPANY ADMIN & SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|admin'])->prefix('admin')->as('admin.')->group(function () {
        // Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        // Route::resource('/departments', DepartmentController::class);
        // Route::get('/employees/all', [EmployeeController::class, 'index'])->name('employees.all');
    });


// ----------------------------
// HR + ADMIN + SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|admin|hr'])->prefix('hr')->as('hr.')->group(function () {
        // Route::get('/dashboard', [HRController::class, 'index'])->name('dashboard');
        // Route::get('/employees/onboard', [HRController::class, 'onboard'])->name('onboard');
        // Route::get('/employees/offboard', [HRController::class, 'offboard'])->name('offboard');
        // Route::get('/leave/requests', [LeaveController::class, 'index'])->name('leave.requests');
        // Route::get('/attendance/manage', [AttendanceController::class, 'index'])->name('attendance.manage');
    });


// ----------------------------
// TEAM LEAD + HR + ADMIN + SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|admin|hr|teamLead'])->prefix('teamlead')->as('teamlead.')->group(function () {
        // Route::get('/dashboard', [TeamLeadController::class, 'index'])->name('dashboard');
        // Route::get('/tasks/assign', [TaskController::class, 'assign'])->name('tasks.assign');
        // Route::get('/feedback/give', [FeedbackController::class, 'create'])->name('feedback.create');
    });


// ----------------------------
// FINANCE + ADMIN + SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|admin|finance'])->prefix('finance')->as('finance.')->group(function () {
        // Route::get('/dashboard', [FinanceController::class, 'index'])->name('dashboard');
        // Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
        // Route::get('/salary/releases', [SalaryController::class, 'release'])->name('salary.release');
        // Route::get('/deductions', [FinanceController::class, 'deductions'])->name('deductions');
    });


// ----------------------------
// EMPLOYEE ONLY
// ----------------------------
Route::middleware(['role:employee'])->prefix('employee')->as('employee.')->group(function () {
        // Route::get('/dashboard', [EmployeeController::class, 'index'])->name('dashboard');
        // Route::get('/my/tasks', [EmployeeTaskController::class, 'index'])->name('tasks');
        // Route::get('/my/attendance', [AttendanceController::class, 'show'])->name('attendance');
        // Route::get('/my/leave', [LeaveController::class, 'myLeaves'])->name('leave');
        // Route::get('/my/payslip', [PayslipController::class, 'show'])->name('payslip');
    });

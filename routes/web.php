<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagement\AdminController;
use App\Http\Controllers\UserManagement\HrController;
use App\Http\Controllers\UserManagement\SuperAdminController;
use App\Http\Controllers\UserManagement\TeamLeadController;
use Illuminate\Support\Facades\Route;


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
});


// ----------------------------
// COMPANY ADMIN & SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|Admin'])->prefix('admin')->as('Admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('EmployeeManagemntsystem.Admin.dashboard');
    })->name('dashboard');
    
    // Admin can only edit their own company (no create, delete, or index)
    Route::get('/company/edit', [CompanyController::class, 'editOwn'])->name('company.edit');
    Route::put('/company/update', [CompanyController::class, 'updateOwn'])->name('company.update');
    Route::post('/company/validate-field', [CompanyController::class, 'validateField'])->name('company.validate-field');
    
    // User Management Routes for Admin
    Route::resource('/users', AdminController::class)->names('users');
});


// ----------------------------
// HR + ADMIN + SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|Admin|HR'])->prefix('hr')->as('HR.')->group(function () {
    Route::get('/dashboard', function () {
        return view('EmployeeManagemntsystem.HR.dashboard');
    })->name('dashboard');
    
    // User Management Routes for HR
    Route::resource('/users', HrController::class)->names('users');
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
Route::middleware(['role:superAdmin|Admin|Finance'])->prefix('finance')->as('Finance.')->group(function () {
    Route::get('/dashboard', function () {
        return view('EmployeeManagemntsystem.Finance.dashboard');
    })->name('dashboard');
   
});


// ----------------------------
// EMPLOYEE ONLY
// ----------------------------
Route::middleware(['role:Employee'])->prefix('employee')->as('Employee.')->group(function () {
    Route::get('/dashboard', function () {
        return view('EmployeeManagemntsystem.employee.dashboard');
    })->name('dashboard');

});

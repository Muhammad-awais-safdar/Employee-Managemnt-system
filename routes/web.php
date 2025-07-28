<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;
use phpDocumentor\Reflection\DocBlock\Tags\Author;


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

    Route::resource('/company',CompanyController::class)->names('company');
    Route::post('/company/validate-field', [CompanyController::class, 'validateField'])->name('company.validate-field');
    Route::post('/company/{id}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('company.toggle-status');
});


// ----------------------------
// COMPANY ADMIN & SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('EmployeeManagemntsystem.CompanyAdmin.dashboard');
    })->name('dashboard');
    
    // Admin can only edit their own company (no create, delete, or index)
    Route::get('/company/edit', [CompanyController::class, 'editOwn'])->name('company.edit');
    Route::put('/company/update', [CompanyController::class, 'updateOwn'])->name('company.update');
    Route::post('/company/validate-field', [CompanyController::class, 'validateField'])->name('company.validate-field');
});


// ----------------------------
// HR + ADMIN + SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|admin|hr'])->prefix('hr')->as('hr.')->group(function () {
    Route::get('/dashboard', function () {
        return view('EmployeeManagemntsystem.Hr.dashboard');
    })->name('dashboard');
});


// ----------------------------
// TEAM LEAD + HR + ADMIN + SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|admin|hr|teamLead'])->prefix('teamlead')->as('teamlead.')->group(function () {
    Route::get('/dashboard', function () {
        return view('EmployeeManagemntsystem.Teamlead.dashboard');
    })->name('dashboard');

});


// ----------------------------
// FINANCE + ADMIN + SUPERADMIN
// ----------------------------
Route::middleware(['role:superAdmin|admin|finance'])->prefix('finance')->as('finance.')->group(function () {
    Route::get('/dashboard', function () {
        return view('EmployeeManagemntsystem.Finance.dashboard');
    })->name('dashboard');
   
});


// ----------------------------
// EMPLOYEE ONLY
// ----------------------------
Route::middleware(['role:employee'])->prefix('employee')->as('employee.')->group(function () {
    Route::get('/dashboard', function () {
        return view('EmployeeManagemntsystem.employee.dashboard');
    })->name('dashboard');

});

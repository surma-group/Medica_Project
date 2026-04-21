<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeUserController;

Route::middleware(['auth:web'])->group(function () {
    Route::get('/dashboard', [EmployeeUserController::class, 'dashboard'])->name('employee.dashboard');
    Route::get('/profile', [EmployeeUserController::class, 'profile'])->name('employee.profile');
    Route::get('/statement', [EmployeeUserController::class, 'statement'])->name('employee.statement');
    Route::get('/salary', [EmployeeUserController::class, 'salary'])->name('employee.salary');
    Route::get('/salary/{id}', [EmployeeUserController::class, 'salaryDetails'])->name('employee.salary.details');
    Route::get('/withdraw-requests', [EmployeeUserController::class, 'viewWithdrawRequests'])->name('employee.withdraw.requests');
    Route::get('/wallet', [EmployeeUserController::class, 'wallet'])->name('employee.wallet');
    Route::post('/wallet/withdraw-request', [EmployeeUserController::class, 'withdrawRequest'])->name('employee.withdraw.request');
    Route::get('/accountant/search/{accountantId}', [EmployeeUserController::class, 'searchAccountant'])->name('employee.accountant.search');
    Route::get('/holiday-calendar', [EmployeeUserController::class, 'calendar'])->name('employee.holiday.calendar');
    Route::get('/holiday-calendar/events', [EmployeeUserController::class, 'calendarEvents'])->name('employee.holiday.calendar.events');
    Route::get('/beneficiaries', [EmployeeUserController::class, 'beneficiaries'])->name('employee.beneficiaries');
    Route::post('/beneficiaries/store', [EmployeeUserController::class, 'storeBeneficiary'])->name('employee.beneficiaries.store');
    Route::put('/beneficiaries/{id}', [EmployeeUserController::class, 'updateBeneficiary'])->name('employee.beneficiaries.update');
});
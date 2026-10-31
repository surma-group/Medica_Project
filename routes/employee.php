<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\Employee\EmployeeMarketingChainController;
use App\Http\Controllers\Employee\EmployeeMarketingAreaTreeController;
use App\Http\Controllers\Employee\EmployeeBeneficiariesController;
use App\Http\Controllers\Employee\EmployeeWalletController;

Route::middleware(['auth', 'employee'])->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');

    Route::get('/profile', function () {
        return view('employee.dashboard');
    })->name('employee.profile');

    Route::get('/employee/beneficiaries', [EmployeeBeneficiariesController::class, 'beneficiaries'])->name('employee.beneficiaries');
    Route::post('/employee/beneficiaries/store', [EmployeeBeneficiariesController::class, 'storeBeneficiary'])->name('employee.beneficiaries.store');
    Route::put('/employee/beneficiaries/{id}', [EmployeeBeneficiariesController::class, 'updateBeneficiary'])->name('employee.beneficiaries.update');


    Route::get('/attendance', function () {
        return view('employee.dashboard');
    })->name('employee.attendance');

    Route::get('/leave', function () {
        return view('employee.dashboard');
    })->name('employee.leave');

    Route::get(
        '/marketing-chain',
        [EmployeeMarketingChainController::class, 'index']
    )->name('employee.marketing.chain');

    Route::get(
        '/marketing-area-tree',
        [EmployeeMarketingAreaTreeController::class, 'index']
    )->name('employee.marketing.area-tree');
    // Salary
    Route::get('/salary', function () {
        return view('employee.dashboard');
    })->name('employee.salary');

    // Payslip
    Route::get('/payslip', function () {
        return view('employee.dashboard');
    })->name('employee.payslip');

    // Statement
    Route::get('/employee/statement', [EmployeeWalletController::class, 'statement'])->name('employee.statement');


    // Wallet
    Route::get('/wallet', [EmployeeDashboardController::class, 'wallet'])->name('employee.wallet');
    Route::post('/wallet/add-money', [EmployeeDashboardController::class, 'addMoney'])->name('employee.add.money');
    // Accountant search
    Route::get('/accountant/search/{accountantId}', [EmployeeWalletController::class, 'searchAccountant'])->name('employee.accountant.search');
    Route::post('/wallet/withdraw-request', [EmployeeWalletController::class, 'withdrawRequest'])->name('employee.withdraw.request');

    Route::get(
        '/transfer/receiver-search',
        [EmployeeWalletController::class, 'searchTransferReceiver']
    )->name('employee.transfer.receiver.search');

    Route::post(
        '/wallet/balance-transfer',
        [EmployeeWalletController::class, 'transferBalance']
    )->name('employee.transfer.submit');

    //remove hoite pare 
    Route::get('/employee/withdraw-requests', [EmployeeWalletController::class, 'viewWithdrawRequests'])->name('employee.withdraw.requests');


    // My Team
    Route::get('/team', function () {
        return view('employee.dashboard');
    })->name('employee.team');

    // Reporting Chain
    Route::get('/reporting-chain', function () {
        return view('employee.dashboard');
    })->name('employee.reporting.chain');

    // Notifications
    Route::get('/notifications', function () {
        return view('employee.dashboard');
    })->name('employee.notifications');

    // Holiday Calendar
    Route::get('/holiday-calendar', function () {
        return view('employee.dashboard');
    })->name('employee.holiday.calendar');
});

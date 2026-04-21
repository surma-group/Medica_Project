<?php

use Illuminate\Support\Facades\Route;

// Admin Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\SalaryComponentController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\SalaryGenerateController;
use App\Http\Controllers\Admin\AddMoneyController;
use App\Http\Controllers\Admin\WithdrawRequestController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\ProductCompanyController;
use App\Http\Controllers\Admin\ProductGenericController;
use App\Http\Controllers\Admin\OrderController;

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('login', [AdminAuthController::class, 'login']);
Route::get('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Protected Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:admin'])->group(function () {

    // Dashboard & Calendar
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/holiday-calendar', [HolidayController::class, 'calendar'])->name('admin.holiday.calendar');
    Route::get('/holiday-calendar/events', [HolidayController::class, 'calendarEvents'])->name('admin.holiday.calendar.events');

    // HR Management (Resources)
    Route::resource('companies', CompanyController::class)->names('company');
    Route::resource('departments', DepartmentController::class)->names('department');
    Route::resource('designation', DesignationController::class)->names('designation');
    Route::resource('branch', BranchController::class)->names('branch');

    // Employee Management
    Route::resource('employees', EmployeeController::class);
    Route::get('employees/{employee}/print', [EmployeeController::class, 'print'])->name('employees.print');
    Route::get('employees/get-branches/{company}', [EmployeeController::class, 'getBranches'])->name('employees.getBranches');

    // Holiday Management
    Route::resource('holiday', HolidayController::class);

    // Salary & Finance
    Route::resource('salary_components', SalaryComponentController::class);
    Route::post('/salary/structure', [SalaryController::class, 'salaryStructureStore'])->name('salary.structure');
    Route::get('/salary/structure/{employee}', [SalaryController::class, 'salaryStructureShow'])->name('salary.structure.show');

    Route::resource('salary_generate', SalaryGenerateController::class);
    Route::get('salary_generate/month/{month}', [SalaryGenerateController::class, 'show'])->name('salary_generate.show_by_month');
    Route::post('/salary/approve/{month}', [SalaryGenerateController::class, 'approve'])->name('salary.approve');

    // Wallet & Cash Flow
    Route::get('withdraw_requests', [WithdrawRequestController::class, 'index'])->name('withdraw_requests.index');
    Route::post('/withdraw/reject', [WithdrawRequestController::class, 'reject'])->name('withdraw.reject.submit');
    Route::post('/withdraw/approve', [WithdrawRequestController::class, 'approve'])->name('withdraw.approve.submit');

    Route::resource('add_money', AddMoneyController::class)->only(['index', 'create', 'store']);
    Route::get('/reports/cash-flow', [ReportController::class, 'cashFlow'])->name('reports.cash_flow');

    // Inventory / Product Management
    Route::resource('suppliers', SupplierController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('unit', UnitController::class);

    // Product Generic Import & Resource
    Route::get('product_generic/import', [ProductGenericController::class, 'import'])->name('product_generic.import');
    Route::post('product_generic/import', [ProductGenericController::class, 'importStore'])->name('product_generic.import.store');
    Route::get('product_generic/sample/download', [ProductGenericController::class, 'sampleDownload'])->name('product_generic.sample.download');
    Route::resource('product_generic', ProductGenericController::class);

    // Product Company Import & Resource
    Route::get('product_company/import', [ProductCompanyController::class, 'import'])->name('product_company.import');
    Route::post('product_company/import', [ProductCompanyController::class, 'importStore'])->name('product_company.import.store');
    Route::get('product_company/sample/download', [ProductCompanyController::class, 'sampleDownload'])->name('product_company.sample.download');
    Route::resource('product_company', ProductCompanyController::class);

    // Main Product Routes
    Route::get('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::post('products/import', [ProductController::class, 'importStore'])->name('products.import.store');
    Route::get('products/sample/download', [ProductController::class, 'sampleDownload'])->name('products.sample.download');
    Route::post('products/set-unit/{product}', [ProductController::class, 'setUnit'])->name('products.unit.set');
    Route::resource('products', ProductController::class);

    // Orders 
    Route::resource('orders', OrderController::class)->names([
        'index' => 'orders.index',
        'show' => 'orders.show',
    ]);
});

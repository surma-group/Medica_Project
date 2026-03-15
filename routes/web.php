<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EmployeeUserController;

// Admin
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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/vision', [HomeController::class, 'vision'])->name('vision');
Route::get('/mission', [HomeController::class, 'mission'])->name('mission');
Route::get('/core-values', [HomeController::class, 'coreValues'])->name('core-values');
Route::get('/career', [HomeController::class, 'career'])->name('career');
Route::get('/terms-and-conditions', [HomeController::class, 'terms'])->name('terms');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Employee dashboard (protected)
Route::middleware(['auth:web'])->group(function () {
    Route::get('/employee/dashboard', [EmployeeUserController::class, 'dashboard'])->name('employee.dashboard');
    Route::get('/employee/profile', [EmployeeUserController::class, 'profile'])->name('employee.profile');
    Route::get('/employee/statement', [EmployeeUserController::class, 'statement'])->name('employee.statement');
    Route::get('/employee/salary', [EmployeeUserController::class, 'salary'])->name('employee.salary');
    Route::get('/employee/salary/{id}', [EmployeeUserController::class, 'salaryDetails'])->name('employee.salary.details');
    Route::get('/employee/withdraw-requests', [EmployeeUserController::class, 'viewWithdrawRequests'])->name('employee.withdraw.requests');

    Route::get('/employee/wallet', [EmployeeUserController::class, 'wallet'])->name('employee.wallet');
    // Withdraw request
    Route::post('/employee/wallet/withdraw-request', [EmployeeUserController::class, 'withdrawRequest'])->name('employee.withdraw.request');

    // Accountant search
    Route::get('/employee/accountant/search/{accountantId}', [EmployeeUserController::class, 'searchAccountant'])->name('employee.accountant.search');


    Route::get('/employee/holiday-calendar', [EmployeeUserController::class, 'calendar'])->name('employee.holiday.calendar');
    Route::get('/holiday-calendar/events', [EmployeeUserController::class, 'calendarEvents'])->name('employee.holiday.calendar.events');

    Route::get('/employee/beneficiaries', [EmployeeUserController::class, 'beneficiaries'])->name('employee.beneficiaries');

    Route::post('/employee/beneficiaries/store', [EmployeeUserController::class, 'storeBeneficiary'])->name('employee.beneficiaries.store');
    Route::put('/employee/beneficiaries/{id}', [EmployeeUserController::class, 'updateBeneficiary'])->name('employee.beneficiaries.update');
});

Route::prefix('em_admin')->group(function () {
    Route::get('login', [App\Http\Controllers\Admin\AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [App\Http\Controllers\Admin\AdminAuthController::class, 'login']);
    Route::get('logout', [App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])->name('admin.logout');


    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/holiday-calendar', [HolidayController::class, 'calendar'])->name('admin.holiday.calendar');
        // Company list route
        Route::resource('companies', CompanyController::class)->names([
            'index' => 'company.index',
            'create' => 'company.create',
            'store' => 'company.store',
            'edit' => 'company.edit',
            'update' => 'company.update',
            'destroy' => 'company.destroy',
        ]);
        // Department routes
        Route::resource('departments', DepartmentController::class)->names([
            'index' => 'department.index',
            'create' => 'department.create',
            'store' => 'department.store',
            'edit' => 'department.edit',
            'update' => 'department.update',
            'destroy' => 'department.destroy',
        ]);

        // Designation routes
        Route::resource('designation', DesignationController::class)->names([
            'index' => 'designation.index',
            'create' => 'designation.create',
            'store' => 'designation.store',
            'edit' => 'designation.edit',
            'update' => 'designation.update',
            'destroy' => 'designation.destroy',
        ]);
        // Branch routes
        Route::resource('branch', BranchController::class)->names([
            'index' => 'branch.index',
            'create' => 'branch.create',
            'store' => 'branch.store',
            'edit' => 'branch.edit',
            'update' => 'branch.update',
            'destroy' => 'branch.destroy',
        ]);
        // Employee routes
        Route::resource('employees', EmployeeController::class)->names([
            'index'   => 'employees.index',
            'create'  => 'employees.create',
            'store'   => 'employees.store',
            'show'    => 'employees.show',
            'edit'    => 'employees.edit',
            'update'  => 'employees.update',
            'destroy' => 'employees.destroy',
        ]);
        // Employee PDF print
        Route::get('employees/{employee}/print', [EmployeeController::class, 'print'])->name('employees.print');
        Route::get('employees/get-branches/{company}', [EmployeeController::class, 'getBranches'])
            ->name('employees.getBranches');

        // Holiday routes
        Route::resource('holiday', HolidayController::class)->names([
            'index'   => 'holiday.index',
            'create'  => 'holiday.create',
            'store'   => 'holiday.store',
            'edit'    => 'holiday.edit',
            'update'  => 'holiday.update',
            'destroy' => 'holiday.destroy',
        ]);
        Route::get('/admin/holiday-calendar/events', [HolidayController::class, 'calendarEvents'])
            ->name('admin.holiday.calendar.events');

        // Salary Components
        Route::resource('salary_components', SalaryComponentController::class)->names([
            'index'   => 'salary_components.index',
            'create'  => 'salary_components.create',
            'store'   => 'salary_components.store',
            'edit'    => 'salary_components.edit',
            'update'  => 'salary_components.update',
            'destroy' => 'salary_components.destroy',
        ]);

        // Salary Structure Route
        Route::post('/salary/structure', [SalaryController::class, 'salaryStructureStore'])->name('salary.structure');
        Route::get('/salary/structure/{employee}', [SalaryController::class, 'salaryStructureShow'])->name('salary.structure.show');
        // Salary Generate
        Route::resource('salary_generate', SalaryGenerateController::class)->names([
            'index'   => 'salary_generate.index',
            'create'  => 'salary_generate.create',
            'store'   => 'salary_generate.store',
            'edit'    => 'salary_generate.edit',
            'update'  => 'salary_generate.update',
            'destroy' => 'salary_generate.destroy',
        ]);
        // Salary Generate - Show by Month
        Route::get('salary_generate/{month}', [SalaryGenerateController::class, 'show'])->name('salary_generate.show');


        Route::post('/salary/approve/{month}', [SalaryGenerateController::class, 'approve'])->name('salary.approve');


        Route::get('withdraw_requests', [WithdrawRequestController::class, 'index'])->name('withdraw_requests.index');
        Route::post('/withdraw/reject', [WithdrawRequestController::class, 'reject'])->name('withdraw.reject.submit');
        Route::post('/withdraw/approve', [WithdrawRequestController::class, 'approve'])->name('withdraw.approve.submit');

        Route::resource('add_money', AddMoneyController::class)
            ->only(['index', 'create', 'store'])
            ->names([
                'index' => 'add_money.index',
                'create' => 'add_money.create',
                'store' => 'add_money.store',
            ]);
        Route::get('/reports/cash-flow', [ReportController::class, 'cashFlow'])->name('reports.cash_flow');

        // Supplier routes
        Route::resource('suppliers', SupplierController::class)->names([
            'index'   => 'suppliers.index',
            'create'  => 'suppliers.create',
            'store'   => 'suppliers.store',
            'show'    => 'suppliers.show',
            'edit'    => 'suppliers.edit',
            'update'  => 'suppliers.update',
            'destroy' => 'suppliers.destroy',
        ]);

        // Brand routes
        Route::resource('brands', BrandController::class)->names([
            'index'   => 'brands.index',
            'create'  => 'brands.create',
            'store'   => 'brands.store',
            'show'    => 'brands.show',
            'edit'    => 'brands.edit',
            'update'  => 'brands.update',
            'destroy' => 'brands.destroy',
        ]);


        // Import Product Company Page
        Route::get('product_company/import', [ProductCompanyController::class, 'import'])
            ->name('product_company.import');

        Route::post('product_company/import', [ProductCompanyController::class, 'importStore'])
            ->name('product_company.import.store');

        Route::get('product_company/sample/download', [ProductCompanyController::class, 'sampleDownload'])
            ->name('product_company.sample.download');
        // Product Company routes
        Route::resource('product_company', ProductCompanyController::class)->names([
            'index'   => 'product_company.index',
            'create'  => 'product_company.create',
            'store'   => 'product_company.store',
            'show'    => 'product_company.show',
            'edit'    => 'product_company.edit',
            'update'  => 'product_company.update',
            'destroy' => 'product_company.destroy',
        ]);
        // Category routes
        Route::resource('categories', CategoryController::class)->names([
            'index'   => 'categories.index',
            'create'  => 'categories.create',
            'store'   => 'categories.store',
            'show'    => 'categories.show',
            'edit'    => 'categories.edit',
            'update'  => 'categories.update',
            'destroy' => 'categories.destroy',
        ]);

        // Import Product Page
        Route::get('products/import', [ProductController::class, 'import'])->name('products.import');
        Route::post('products/import', [ProductController::class, 'importStore'])->name('products.import.store');
        Route::get('products/sample/download', [ProductController::class, 'sampleDownload'])->name('products.sample.download');
        Route::post('products/set-unit/{product}', [ProductController::class, 'setUnit'])->name('products.unit.set');

        //products
        Route::resource('products', ProductController::class)->names([
            'index'   => 'products.index',
            'create'  => 'products.create',
            'store'   => 'products.store',
            'show'    => 'products.show',
            'edit'    => 'products.edit',
            'update'  => 'products.update',
            'destroy' => 'products.destroy',
        ]);

        Route::resource('unit', UnitController::class)->names([
            'index'   => 'unit.index',
            'create'  => 'unit.create',
            'store'   => 'unit.store',
            'show'    => 'unit.show',
            'edit'    => 'unit.edit',
            'update'  => 'unit.update',
            'destroy' => 'unit.destroy',
        ]);
    });
});

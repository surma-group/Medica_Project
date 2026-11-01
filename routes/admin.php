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
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\ProductCompanyController;
use App\Http\Controllers\Admin\ProductGenericController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderRequestController;
use App\Http\Controllers\Admin\PaymentAccountController;
use App\Http\Controllers\Admin\AddMoneyRequestController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\ProductStockController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\MarketingChainController;
use App\Http\Controllers\Admin\MarketingCommissionTargetController;
use App\Http\Controllers\Admin\MarketingAreaController;
use App\Http\Controllers\Admin\AddEmployeeBalanceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\OrderTransferController;

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
    Route::post('order-transfers', [OrderTransferController::class, 'store'])->name('order-transfers.store');
    Route::get('orders/{order}/transfer-items', [
        OrderController::class,
        'transferItems'
    ])->name('orders.transfer-items');

    // Employee Management
    Route::resource('employees', EmployeeController::class);
    Route::get('employees/{employee}/print', [EmployeeController::class, 'print'])->name('employees.print');
    Route::get('employees/get-branches/{company}', [EmployeeController::class, 'getBranches'])->name('employees.getBranches');


    // System Settings
    Route::get('settings', [SettingController::class, 'index'])
        ->name('settings.index');
    Route::put('settings/{setting}', [SettingController::class, 'update'])
        ->name('settings.update');


    // Marketing Management
    Route::prefix('marketing')->name('marketing.')->group(function () {
        Route::get('/chain', [MarketingChainController::class, 'index'])
            ->name('chain');

        Route::post('/chain/relation', [MarketingChainController::class, 'store'])
            ->name('chain.relation.store');

        Route::patch('/chain/relation/{relation}/cancel', [MarketingChainController::class, 'cancel'])
            ->name('chain.relation.cancel');

        // Commission & Target
        Route::get('/commission-target', [MarketingCommissionTargetController::class, 'index'])
            ->name('commission.target');

        // Save Target
        Route::post('/commission-target/save-target', [MarketingCommissionTargetController::class, 'saveTarget'])
            ->name('commission.target.save');

        // Save Commission
        Route::post('/commission-target/save-commission', [MarketingCommissionTargetController::class, 'saveCommission'])
            ->name('commission.target.commission.save');

        // Marketing Area
        Route::get('/area', [MarketingAreaController::class, 'index'])
            ->name('area');

        Route::get('/area/divisions', [MarketingAreaController::class, 'divisions'])
            ->name('area.divisions');

        Route::get('/area/districts', [MarketingAreaController::class, 'districts'])
            ->name('area.districts');

        Route::get('/area/upazilas', [MarketingAreaController::class, 'upazilas'])
            ->name('area.upazilas');

        Route::get('/area/assignment', [MarketingAreaController::class, 'getAssignment'])
            ->name('area.assignment');

        Route::post('/area/save', [MarketingAreaController::class, 'save'])
            ->name('area.save');
        Route::get('/area/filter-members', [
            MarketingAreaController::class,
            'filterMembers',
        ])->name('area.filter-members');
    });

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

    Route::get('add_money_requests', [AddMoneyRequestController::class, 'index'])
        ->name('add_money_requests.index');

    Route::post('/add-money/reject', [AddMoneyRequestController::class, 'reject'])
        ->name('add_money_requests.reject');

    Route::post('/add-money/approve', [AddMoneyRequestController::class, 'approve'])
        ->name('add_money_requests.approve');

    Route::resource('add_money', AddMoneyController::class)->only(['index', 'create', 'store']);
    Route::get('/reports/cash-flow', [ReportController::class, 'cashFlow'])->name('reports.cash_flow');
    Route::get(
        'add_employee_balance',
        [AddEmployeeBalanceController::class, 'index']
    )->name('add_employee_balance.index');

    Route::post(
        'add_employee_balance',
        [AddEmployeeBalanceController::class, 'store']
    )->name('add_employee_balance.store');

    // Inventory / Product Management
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('unit', UnitController::class);
    Route::resource('payment-accounts', PaymentAccountController::class);

    Route::get('/purchases/company-categories/{id}', [PurchaseController::class, 'companyCategories'])
        ->name('purchases.company-categories');

    Route::get('/purchases/company-products/{company}/{category?}', [PurchaseController::class, 'companyProducts'])
        ->name('purchases.company-products');

    Route::get('purchases/product-details/{product}', [PurchaseController::class, 'productDetails'])
        ->name('purchases.product-details');

    Route::resource('purchases', PurchaseController::class);

    Route::get(
        'sales/company-categories/{companyId}',
        [SaleController::class, 'companyCategories']
    )->name('sales.company-categories');

    Route::get(
        'sales/company-products/{companyId}/{categoryId?}',
        [SaleController::class, 'companyProducts']
    )->name('sales.company-products');

    Route::get(
        'sales/product-details/{productId}',
        [SaleController::class, 'productDetails']
    )->name('sales.product-details');

    Route::get(
        'sales/product-price',
        [SaleController::class, 'productPrice']
    )->name('sales.product-price');

    Route::get('sales/customers-by-type', [SaleController::class, 'customersByType'])->name('sales.customers-by-type');

    Route::resource('sales', SaleController::class);


    Route::prefix('partners/dealer')->name('partners.dealer.')->group(function () {
        Route::get('/', [PartnerController::class, 'dealerIndex'])->name('index');
        Route::get('/{partner}/edit', [PartnerController::class, 'dealerEdit'])->name('edit');
        Route::put('/{partner}', [PartnerController::class, 'dealerUpdate'])->name('update');
        Route::delete('/{partner}', [PartnerController::class, 'dealerDestroy'])->name('destroy');
    });
    Route::prefix('partners/agent')->name('partners.agent.')->group(function () {
        Route::get('/', [PartnerController::class, 'agentIndex'])->name('index');
        Route::get('/{partner}/edit', [PartnerController::class, 'agentEdit'])->name('edit');
        Route::put('/{partner}', [PartnerController::class, 'agentUpdate'])->name('update');
        Route::delete('/{partner}', [PartnerController::class, 'agentDestroy'])->name('destroy');
    });

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
    Route::post('products/set-price/{product}', [ProductController::class, 'setPrice'])->name('products.price.set');
    Route::post('products/set-commission/{product}', [ProductController::class, 'setCommission'])->name('products.commission.set');
    Route::get('products/request', [ProductController::class, 'productRequest'])->name('products.request');
    Route::resource('products', ProductController::class);

    Route::get('product-stock/history/{id}', [ProductStockController::class, 'history']);
    Route::resource('product-stock', ProductStockController::class);

    // Orders 
    Route::prefix('orders')->name('orders.')->group(function () {

        Route::get('/', [OrderController::class, 'index'])
            ->name('index');

        Route::get('/{order}', [OrderController::class, 'show'])
            ->name('show');
        Route::get('/{order}/track', [OrderController::class, 'track'])
            ->name('track');

        // UPDATE STATUS (Accept / Cancel / etc)
        Route::post('/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('update-status');

        // DELETE ORDER
        Route::delete('/{order}', [OrderController::class, 'destroy'])
            ->name('destroy');
        // ACCEPTED ORDERS
        Route::get('/accepted/list', [OrderController::class, 'accepted'])
            ->name('accepted');
    });

    Route::resource('order-requests', OrderRequestController::class)->names([
        'index' => 'order-requests.index',
        'show'  => 'order-requests.show',
    ]);
});

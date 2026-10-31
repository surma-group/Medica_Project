<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Partner\PartnerController;
use App\Http\Controllers\Partner\PartnerAuthController;
use App\Http\Controllers\Partner\PartnerOrdersController;
use App\Http\Controllers\Partner\PartnerAgentOrdersController;
use App\Http\Middleware\RedirectIfNotPartner;
use App\Http\Middleware\EnsurePartnerIsAgent;

/*
|--------------------------------------------------------------------------
| Partner Authentication Routes
|--------------------------------------------------------------------------
*/


Route::post('/logout', [PartnerAuthController::class, 'logout'])
    ->name('partner.logout');

Route::middleware(['auth:web', RedirectIfNotPartner::class])->group(function () {

    // Dashboard
    Route::get('/dashboard', [PartnerController::class, 'dashboard'])
        ->name('partner.dashboard');

    // Profile
    Route::get('/profile', [PartnerController::class, 'profile'])
        ->name('partner.profile');

    // Statement
    Route::get('/statement', [PartnerController::class, 'statement'])
        ->name('partner.statement');

    // Withdraw Requests
    Route::get('/withdraw-requests', [PartnerController::class, 'viewWithdrawRequests'])
        ->name('partner.withdraw.requests');

    // Wallet
    Route::get('/wallet', [PartnerController::class, 'wallet'])
        ->name('partner.wallet');

    Route::post('/wallet/withdraw-request', [PartnerController::class, 'withdrawRequest'])
        ->name('partner.withdraw.request');

    Route::post('/wallet/add-money', [PartnerController::class, 'addMoney'])
        ->name('partner.add.money');

    // Balance Transfer routes
    Route::get('/transfer/receiver-search', [PartnerController::class, 'searchTransferReceiver'])
        ->name('partner.transfer.receiver.search');

    Route::post('/wallet/balance-transfer', [PartnerController::class, 'transferBalance'])
        ->name('partner.transfer.submit');

    // Accountant Search
    Route::get('/accountant/search/{accountantId}', [PartnerController::class, 'searchAccountant'])
        ->name('partner.accountant.search');

    // Holiday Calendar
    Route::get('/holiday-calendar', [PartnerController::class, 'calendar'])
        ->name('partner.holiday.calendar');

    Route::get('/holiday-calendar/events', [PartnerController::class, 'calendarEvents'])
        ->name('partner.holiday.calendar.events');

    // Beneficiaries
    Route::get('/beneficiaries', [PartnerController::class, 'beneficiaries'])
        ->name('partner.beneficiaries');

    Route::post('/beneficiaries/store', [PartnerController::class, 'storeBeneficiary'])
        ->name('partner.beneficiaries.store');

    Route::put('/beneficiaries/{id}', [PartnerController::class, 'updateBeneficiary'])
        ->name('partner.beneficiaries.update');

    // Agent Orders - Dealer Only
    // Agent-only middleware group-এর বাইরে রাখা হয়েছে
    Route::get('/agent-orders', [PartnerAgentOrdersController::class, 'index'])
        ->name('partner.agent-orders');

    Route::get('/agent-orders/{id}', [PartnerAgentOrdersController::class, 'show'])
        ->name('partner.agent-orders.show');
    Route::get('/incoming-transfers', [PartnerAgentOrdersController::class, 'incoming'])
        ->name('partner.incoming-transfers');

    Route::post('/incoming-transfers/{id}/receive', [
        PartnerAgentOrdersController::class,
        'receive'
    ])->name('partner.incoming-transfers.receive');

    Route::post('/send-to-agent/{id}', [
        PartnerAgentOrdersController::class,
        'sendToAgent',
    ])->name('partner.transfers.send-to-agent');

    // Send products to Agent
    Route::get('/send-to-agent', [
        PartnerAgentOrdersController::class,
        'outgoing',
    ])->name('partner.transfers.outgoing');

    Route::get('/orders/{id}/track', [
        PartnerOrdersController::class,
        'track',
    ])->name('partner.orders.track');

    // Orders Routes - Agent Only
    Route::middleware([EnsurePartnerIsAgent::class])->group(function () {

        Route::get('/orders', [PartnerOrdersController::class, 'index'])
            ->name('partner.orders');

        Route::get('/orders/create', [PartnerOrdersController::class, 'create'])
            ->name('partner.orders.create');

        Route::post('/orders', [PartnerOrdersController::class, 'store'])
            ->name('partner.orders.store');

        Route::get('/orders/{id}', [PartnerOrdersController::class, 'show'])
            ->name('partner.orders.show');

        Route::get('/orders/{id}/edit', [PartnerOrdersController::class, 'edit'])
            ->name('partner.orders.edit');

        Route::put('/orders/{id}', [PartnerOrdersController::class, 'update'])
            ->name('partner.orders.update');

        Route::delete('/orders/{id}', [PartnerOrdersController::class, 'destroy'])
            ->name('partner.orders.delete');
            
        Route::post('/orders/transfers/{id}/receive', [
            PartnerOrdersController::class,
            'receiveTransfer',
        ])->name('partner.orders.receive-transfer');

        Route::get('/orders/company-products/{companyId}', [PartnerOrdersController::class, 'companyProducts']);

        Route::get('/orders/product-details/{productId}', [PartnerOrdersController::class, 'productDetails']);
    });
});

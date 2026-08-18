<?php

use Illuminate\Support\Facades\Route;

// Business Admin / Staff Routes
Route::prefix('business')->name('business.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Business\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('party-types', \App\Http\Controllers\Business\PartyTypeController::class);
    Route::resource('parties', \App\Http\Controllers\Business\PartyController::class);
    Route::post('parties/{party}/payments', [\App\Http\Controllers\Business\PaymentController::class, 'store'])->name('parties.payments.store');
    Route::resource('godowns', \App\Http\Controllers\Business\GodownController::class);
    Route::resource('grains', \App\Http\Controllers\Business\GrainController::class);
    Route::get('/purchases/{purchase}/print', [\App\Http\Controllers\Business\PurchaseController::class, 'print'])->name('purchases.print');
    Route::post('/purchases/{purchase}/pay', [\App\Http\Controllers\Business\PurchaseController::class, 'paySupplier'])->name('purchases.pay');
    Route::resource('purchases', \App\Http\Controllers\Business\PurchaseController::class);
    
    Route::get('/sales/{sale}/print', [\App\Http\Controllers\Business\SaleController::class, 'print'])->name('sales.print');
    Route::resource('sales', \App\Http\Controllers\Business\SaleController::class);
    Route::get('/api/lots', [\App\Http\Controllers\Business\SaleController::class, 'getLotsForGrain'])->name('api.lots');

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/stocks', [\App\Http\Controllers\Business\GrainStockController::class, 'index'])->name('stocks.index');
        Route::get('/lots', [\App\Http\Controllers\Business\LotController::class, 'index'])->name('lots.index');
        Route::get('/logs', [\App\Http\Controllers\Business\InventoryLogController::class, 'index'])->name('logs.index');
        Route::resource('adjustments', \App\Http\Controllers\Business\StockAdjustmentController::class);
    });

    // Reports Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/purchases', [\App\Http\Controllers\Business\ReportController::class, 'purchases'])->name('purchases');
        Route::get('/sales', [\App\Http\Controllers\Business\ReportController::class, 'sales'])->name('sales');
        Route::get('/current-stock', [\App\Http\Controllers\Business\ReportController::class, 'currentStock'])->name('current-stock');
        Route::get('/lot-wise-stock', [\App\Http\Controllers\Business\ReportController::class, 'lotWiseStock'])->name('lot-wise-stock');
        Route::get('/party-ledger', [\App\Http\Controllers\Business\ReportController::class, 'partyLedger'])->name('party-ledger');
        Route::get('/broker-commission', [\App\Http\Controllers\Business\ReportController::class, 'brokerCommission'])->name('broker-commission');
        Route::get('/profit', [\App\Http\Controllers\Business\ReportController::class, 'profit'])->name('profit');
        Route::get('/expenses', [\App\Http\Controllers\Business\ReportController::class, 'expenseSummary'])->name('expenses');
    });

    // Settings
    Route::prefix('settings')->group(function () {
        Route::get('/', [\App\Http\Controllers\Business\SettingController::class, 'index'])->name('settings.index');
        Route::post('/', [\App\Http\Controllers\Business\SettingController::class, 'update'])->name('settings.update');
    });

    // Expenses
    Route::resource('expenses/categories', \App\Http\Controllers\Business\ExpenseCategoryController::class)
        ->names('expenses.categories')->except(['show', 'create', 'edit']);
    Route::resource('expenses', \App\Http\Controllers\Business\ExpenseController::class)->except(['show']);

    // Financials Routes
    Route::prefix('financials')->name('financials.')->group(function () {
        Route::get('/ledger', [\App\Http\Controllers\Business\LedgerEntryController::class, 'index'])->name('ledger.index');
        Route::resource('commissions', \App\Http\Controllers\Business\BrokerCommissionController::class)->except(['show']);
        Route::post('/commissions/store-broker', [\App\Http\Controllers\Business\BrokerCommissionController::class, 'storeBroker'])->name('commissions.store-broker');
        // Broker profile + commission payment
        Route::get('/brokers/{broker}/profile', [\App\Http\Controllers\Business\BrokerCommissionController::class, 'profile'])->name('brokers.profile');
        Route::post('/commissions/{entry}/pay', [\App\Http\Controllers\Business\BrokerCommissionController::class, 'markPaid'])->name('commissions.pay');
    });

    // Sale Recovery / Collections
    Route::post('/sales/{sale}/collect', [\App\Http\Controllers\Business\SaleCollectionController::class, 'store'])->name('sales.collect');
    Route::get('/sales/{sale}/collections', [\App\Http\Controllers\Business\SaleCollectionController::class, 'history'])->name('sales.collections.history');
});

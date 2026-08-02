<?php

use Illuminate\Support\Facades\Route;

// Super Admin Routes (role is super_admin)
Route::prefix('super-admin')->name('superadmin.')->middleware('superadmin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

    // Impersonate
    Route::get('/impersonate/{id}', [\App\Http\Controllers\SuperAdmin\ImpersonateController::class, 'start'])->name('impersonate');

    Route::resource('companies', \App\Http\Controllers\SuperAdmin\CompanyController::class);
    Route::resource('plans', \App\Http\Controllers\SuperAdmin\PlanController::class);
    Route::resource('users', \App\Http\Controllers\SuperAdmin\UserController::class);
    Route::resource('invoices', \App\Http\Controllers\SuperAdmin\InvoiceController::class);

    // Settings
    Route::get('/settings', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'update'])->name('settings.update');
});

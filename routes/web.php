<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth')->group(function () {
    
    // Logout (Applies to all authenticated users)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    // Unified Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Redirect / to the correct dashboard based on role
    Route::get('/', function () {
        if (auth()->user()->role === 'super_admin') {
            return redirect()->route('superadmin.dashboard');
        }
        return redirect()->route('business.dashboard');
    });

    // Stop impersonating route (accessible globally when authenticated)
    Route::get('/stop-impersonating', [\App\Http\Controllers\SuperAdmin\ImpersonateController::class, 'stop'])->name('impersonate.stop');

    // Super Admin Routes (role is super_admin)
    require __DIR__.'/superadmin.php';

    // Business Admin / Staff Routes
    require __DIR__.'/business.php';

});

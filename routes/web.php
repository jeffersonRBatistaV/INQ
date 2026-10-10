<?php

use App\Http\Controllers\Admin\LandlordController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return redirect()->route(auth()->user()->isSuperadmin() ? 'admin.dashboard' : 'dashboard');
});

Route::middleware(['auth', 'verified', 'landlord'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('apartments', ApartmentController::class)->except('show');
    Route::resource('tenants', TenantController::class);
    Route::resource('leases', LeaseController::class)->except('show');

    Route::get('/charges', [ChargeController::class, 'index'])->name('charges.index');
    Route::post('/charges/generate', [ChargeController::class, 'generate'])->name('charges.generate');
    Route::delete('/charges/{charge}', [ChargeController::class, 'destroy'])->name('charges.destroy');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/receipt', [ReceiptController::class, 'download'])->name('receipts.download');

    Route::resource('expenses', ExpenseController::class)->except('show');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/mora', [ProfileController::class, 'updateMora'])->name('profile.mora');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [LandlordController::class, 'index'])->name('dashboard');
    Route::patch('/landlords/{landlord}/toggle', [LandlordController::class, 'toggle'])->name('landlords.toggle');
});

require __DIR__.'/auth.php';

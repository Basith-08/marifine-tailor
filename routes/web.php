<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\CustomerController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

require __DIR__.'/settings.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('dashboard', DashboardController::class)->only(['index']);
    Route::resource('/customers', CustomerController::class);
    Route::resource('orders', OrderController::class)->except(['create', 'show', 'edit']);
    Route::post('orders/{order}/status', [OrderController::class, 'changeStatus'])->name('orders.changeStatus');
    Route::resource('customers.measurements', MeasurementController::class)->only(['index', 'store']);
    Route::resource('measurements', MeasurementController::class)->only(['update', 'destroy']);
});

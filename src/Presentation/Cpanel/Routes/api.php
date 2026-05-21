<?php

use Illuminate\Support\Facades\Route;
use Src\Presentation\Cpanel\Controllers\OrderAssignmentController;
use Src\Presentation\Cpanel\Controllers\DriverOrdersController;

/*
|--------------------------------------------------------------------------
| Cpanel API Routes
|--------------------------------------------------------------------------
|
| Here are the API routes for the Cpanel presentation layer.
| These routes are loaded by the RouteServiceProvider and assigned
| the "api" middleware group.
|
*/

Route::prefix('orders')->group(function () {
    // Assign order to best available driver
    Route::post('{id}/assign', [OrderAssignmentController::class, 'assign'])
        ->name('orders.assign');

    // Unassign order from driver
    Route::post('{id}/unassign', [OrderAssignmentController::class, 'unassign'])
        ->name('orders.unassign');
});

Route::prefix('drivers')->group(function () {
    // Get all orders for a specific driver
    Route::get('{id}/orders', [DriverOrdersController::class, 'index'])
        ->name('drivers.orders.index');

    // Get active orders for a specific driver
    Route::get('{id}/orders/active', [DriverOrdersController::class, 'active'])
        ->name('drivers.orders.active');

    // Get order statistics for a driver
    Route::get('{id}/orders/statistics', [DriverOrdersController::class, 'statistics'])
        ->name('drivers.orders.statistics');
});

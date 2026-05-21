<?php

use Illuminate\Support\Facades\Route;
use Src\Presentation\Cpanel\Controllers\OrderAssignmentController;

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

<?php

use App\Http\Controllers\Api\V1\CustomerController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/customers', [CustomerController::class, 'store']); // Create a new customer
    Route::get('/customers/{id}', [CustomerController::class, 'show']); // Get customer by ID
    Route::get('/customers', [CustomerController::class, 'index']); // Get customers by name or email
    Route::put('/customers/{id}', [CustomerController::class, 'update']); // Update customer by ID
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']); // Delete customer by ID
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/customers', [CustomerController::class, 'store']); // Create a new customer
Route::get('/customers/{id}', [CustomerController::class, 'show']); // Get customer by ID
Route::get('/customers', [CustomerController::class, 'index']); // Get customers by name or email
Route::put('/customers/{id}', [CustomerController::class, 'update']); // Update customer by ID
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']); // Delete customer by ID


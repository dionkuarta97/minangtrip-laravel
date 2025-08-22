<?php

use App\Http\Controllers\Api\Owner\OwnerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Owner API Routes
|--------------------------------------------------------------------------
|
| Here are the routes for owner authentication and management
|
*/

// Public routes (no authentication required)
Route::post('/login', [OwnerController::class, 'login']);

// Protected routes (require owner authentication)
Route::middleware(['owner.auth'])->group(function () {
    Route::post('/logout', [OwnerController::class, 'logout']);
    Route::get('/profile', [OwnerController::class, 'profile']);
});
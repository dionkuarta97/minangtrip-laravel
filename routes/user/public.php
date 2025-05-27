<?php

use App\Http\Controllers\Api\Users\UsersController;
use Illuminate\Support\Facades\Route;




Route::post('/login', [UsersController::class, 'login']);

<?php

use Illuminate\Support\Facades\Route;

Route::prefix('public')->group(function () {
    require __DIR__ . '/public.php';
});

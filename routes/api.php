<?php

use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/projects', [ProjectController::class, 'index']);
});

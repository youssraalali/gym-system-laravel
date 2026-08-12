<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Member;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\MembershipPlanController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::name('api.')->group(function () {
        Route::apiResource('members', MemberController::class);
    })->middleware('role:admin');
    Route::get('/plans/{id}/convert', [MembershipPlanController::class, 'convert']);
});

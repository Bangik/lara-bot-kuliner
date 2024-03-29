<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// telegram webhook telegram/webhook
Route::post('telegram/webhook', [\App\Http\Controllers\Api\TelegramBotController::class, 'webhook']);

// routes for authentication
Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register'])->name('register');
Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// routes for the authenticated user
Route::middleware('auth:api')->group(function () {
    Route::get('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->name('logout');
    Route::get('user', [\App\Http\Controllers\Api\AuthController::class, 'user'])->name('user');
});
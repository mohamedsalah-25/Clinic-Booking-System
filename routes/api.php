<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function () {
Route::apiResource('appointments', AppointmentController::class);
});

Route::post('register', [AppointmentController::class, 'register']);
Route::post('login', [AppointmentController::class, 'login']);
Route::middleware('auth:sanctum')->get('user', [AppointmentController::class, 'user']);
Route::post('logout', [AppointmentController::class, 'logout'])->middleware('auth:sanctum');


<?php

use App\Http\Controllers\Api\IotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/scan-rfid', [IotController::class, 'scanRfid']);
Route::post('/submit-report', [IotController::class, 'submitReport']);
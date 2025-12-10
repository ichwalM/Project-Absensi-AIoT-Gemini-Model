<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IotController;

Route::post('/scan-rfid', [IotController::class, 'scanRfid']);
Route::post('/submit-report', [IotController::class, 'submitReport']);

Route::get('/', function () {
    return view('welcome');
});

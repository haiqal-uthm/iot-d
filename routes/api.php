<?php

use App\Http\Controllers\HarvestController;
use App\Http\Controllers\VibrationLogController;
use Illuminate\Support\Facades\Route;

//IoT Backend Things
Route::post('/harvest-log', [HarvestController::class, 'store']);
Route::post('/vibration-log', [VibrationLogController::class, 'store']); 


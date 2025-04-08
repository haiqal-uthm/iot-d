<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\DurianController;
use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrchardController;
use App\Http\Controllers\ProductionReportController;
use App\Http\Controllers\VibrationLogController;
use App\Http\Controllers\HarvestController;
use App\Models\HarvestDurianLog;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Weather routes
    Route::get('/weather', [WeatherController::class, 'index'])->name('weather');
    Route::get('/weather/fetch', [WeatherController::class, 'fetchWeather'])->name('weather.fetch');
    Route::get('/weather/current', [WeatherController::class, 'fetchCurrentWeather'])->name('weather.current');

    // Durian routes
    Route::get('/durian', [DurianController::class, 'index'])->name('durian');
    Route::post('/durian', [DurianController::class, 'store'])->name('durian.store');
    Route::put('/durians/{durianId}', [DurianController::class, 'update'])->name('durian.update');
    Route::delete('/durian/{durianId}', [DurianController::class, 'destroy'])->name('durian.destroy');
    Route::post('durian/save-vibration', [DurianController::class, 'saveVibration'])->name('save-vibration');

    //orchards
    Route::get('/orchards', [OrchardController::class, 'index'])->name('orchards');
    Route::post('/update-total', [DurianController::class, 'updateTotal'])->name('updateTotal');
    Route::get('/orchards/create', [OrchardController::class, 'create'])->name('orchards.create');
    Route::post('/orchards', [OrchardController::class, 'store'])->name('orchards.store');

    //devices routes
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices');
    Route::post('/devices/toggle', [DeviceController::class, 'toggleLed'])->name('devices.toggle');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/total-devices', [DeviceController::class, 'getTotalDevices']);
    Route::put('/devices/{deviceId}', [DeviceController::class, 'update'])->name('devices.update');
    Route::delete('/devices/{deviceId}', [DeviceController::class, 'destroy'])->name('devices.destroy');

    //notification
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //production
    Route::get('/production-report', [ProductionReportController::class, 'index'])->name('production-report');
    Route::post('/production-upload', [HarvestController::class, 'upload'])->name('production.upload');


    //harvest
    Route::post('/harvest/save', [HarvestController::class, 'save'])->name('harvest.save');
    Route::post('/harvest-details', [ProductionReportController::class, 'saveHarvestDetails'])->name('harvest.save-details');

    // dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/check-animal-detection', [DashboardController::class, 'checkAnimalDetection'])->name('checkAnimalDetection');

    
});


require __DIR__ . '/auth.php';

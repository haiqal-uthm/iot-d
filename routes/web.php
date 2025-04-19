<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\DurianController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\UserController; // Add this line
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrchardController;
use App\Http\Controllers\ProductionReportController;
use App\Http\Controllers\VibrationLogController;
use App\Http\Controllers\HarvestController;
use App\Models\HarvestDurianLog;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\FarmerHarvestController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Add farmer routes group
Route::middleware(['auth', 'role:farmer'])->name('farmer.')->prefix('farmer')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orchards', [OrchardController::class, 'index'])->name('orchards');
    Route::get('/harvest-entry', [FarmerHarvestController::class, 'create'])->name('harvestEntry');
    Route::post('/harvest', [FarmerHarvestController::class, 'store'])->name('harvest.store');
    Route::get('/harvest/{id}/edit', [FarmerHarvestController::class, 'edit'])->name('harvest.edit');
    Route::get('/harvest-report', [FarmerHarvestController::class, 'show'])
         ->name('harvest.report');
    // Add other farmer-specific routes here
});

// Keep existing dashboard route for other roles
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Admin routes
// In the admin routes group:
Route::middleware(['auth', 'role:admin'])->name('admin.')->prefix('admin')->group(function () {
    // Change this line:
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    // To this:
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    // Update other admin routes similarly:
    Route::get('/orchards', [OrchardController::class, 'index'])->name('orchards');
    Route::get('/durian', [DurianController::class, 'index'])->name('durian');
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices');
    Route::get('/production-report', [ProductionReportController::class, 'index'])->name('production-report');
    Route::get('/users', [UserController::class, 'index'])->name('users.');
    Route::resource('users', UserController::class)->except(['update']);
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/{user}/orchards', [UserController::class, 'showManageOrchards'])
             ->name('users.manage_orchards');
    Route::put('/users/{user}/orchards', [UserController::class, 'updateOrchards'])
             ->name('users.update-orchards');
});

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
    Route::delete('/orchards/{orchard}', [OrchardController::class, 'destroy'])->name('orchards.destroy');

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

    Route::get('/check-animal-detection', [DashboardController::class, 'checkAnimalDetection'])->name('checkAnimalDetection');
});

require __DIR__ . '/auth.php';


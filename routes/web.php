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
use App\Http\Controllers\FarmerInventoryController;
use App\Http\Controllers\AdminInventoryController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\DurianFallController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/firebase-test', function () {
    $service = app(\App\Services\FirebaseService::class);
    return $service->getDurianCount();
});


// Add farmer routes group
Route::middleware(['auth', 'role:farmer'])->name('farmer.')->prefix('farmer')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orchards', [OrchardController::class, 'index'])->name('orchards');
    Route::get('/orchards/{id}', [OrchardController::class, 'show'])->name('orchards.show');
    Route::get('/inventory', [FarmerInventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory', [FarmerInventoryController::class, 'store'])->name('inventory.store');
    Route::get('/harvest', [HarvestController::class, 'index'])->name('harvest');
    Route::get('/harvest/{id}', [HarvestController::class,'showDetail'])
         ->name('harvest.show');
    Route::get('/harvest-entry', [FarmerHarvestController::class, 'create'])->name('harvestEntry');
    Route::post('/harvest', [FarmerHarvestController::class, 'store'])->name('harvest.store');
    Route::get('/harvest/{id}/edit', [FarmerHarvestController::class, 'edit'])->name('harvest.edit');
    Route::put('/harvest/{id}', [FarmerHarvestController::class, 'update'])->name('harvest.update');
    Route::get('/harvest/{id}', [FarmerHarvestController::class, 'showDetail'])
     ->name('harvest.show');
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
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');
    Route::get('/orchards', [OrchardController::class, 'index'])->name('orchards');
    Route::get('/orchards/create', [OrchardController::class, 'create'])->name('orchards.create'); // Add this line
    Route::get('/orchards/{id}/edit', [OrchardController::class, 'edit'])->name('orchards.edit');
    Route::put('/orchards/{id}', [OrchardController::class, 'update'])->name('orchards.update');
    
    // Durian routes
    Route::get('/durian', [DurianController::class, 'index'])->name('durian');
    Route::get('/durian/create', [DurianController::class, 'create'])->name('durian.create');
    Route::get('/durian/{id}', [DurianController::class, 'show'])->name('durian.show');
    Route::get('/durian/{id}/edit', [DurianController::class, 'edit'])->name('durian.edit');
    
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices');
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
    Route::get('/devices/{device}', [DeviceController::class, 'show'])->name('devices.show');
    Route::post('/devices/toggle', [DeviceController::class, 'toggleLed'])->name('devices.toggle');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/total-devices', [DeviceController::class, 'getTotalDevices']);
    Route::put('/devices/{deviceId}', [DeviceController::class, 'update'])->name('devices.update');
    Route::delete('/devices/{deviceId}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    Route::get('/production-report', [ProductionReportController::class, 'index'])->name('production-report');
    
    // Add inventory management routes
    Route::get('/inventory', [App\Http\Controllers\Admin\AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory', [App\Http\Controllers\Admin\AdminInventoryController::class, 'store'])->name('inventory.store');
    Route::delete('/inventory/{transaction}', [App\Http\Controllers\Admin\AdminInventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::get('/users', [UserController::class, 'index'])->name('users.');
    Route::resource('users', UserController::class)->except(['update']);
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/{user}/orchards', [UserController::class, 'showManageOrchards'])
             ->name('users.manage_orchards');
    Route::put('/users/{user}/orchards', [UserController::class, 'updateOrchards'])
             ->name('users.update-orchards');
    
    // Storage Management Routes
    Route::get('/storage', [StorageController::class, 'index'])->name('storage.index');
    Route::post('/storage', [StorageController::class, 'store'])->name('storage.store');
    Route::put('/storage/{storage}', [StorageController::class, 'update'])->name('storage.update');
    Route::delete('/storage/{storage}', [StorageController::class, 'destroy'])->name('storage.destroy');
});

// Add these routes to your web.php file
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/durian-fall', [App\Http\Controllers\Manager\DurianFallController::class, 'index'])->name('durian-fall.index');
    Route::get('/manager/durian-fall/data', [DurianFallController::class, 'getData'])->name('durian-fall.data');
    Route::get('/notifications', [App\Http\Controllers\Manager\NotificationController::class, 'index'])->name('notification.index');
    Route::post('/notifications/mark-as-read', [App\Http\Controllers\Manager\NotificationController::class, 'markAsRead'])->name('notification.mark-as-read');
    Route::get('/performance', [App\Http\Controllers\Manager\PerformanceController::class, 'index'])->name('performance.index');
    Route::get('/performance/{farmer}', [App\Http\Controllers\Manager\PerformanceController::class, 'show'])->name('performance.show');
    Route::get('/performance/{farmer}/harvest-data', [App\Http\Controllers\Manager\PerformanceController::class, 'getHarvestDataJson'])->name('performance.harvest-data');
    
    // Inventory routes
    Route::get('/inventory', [App\Http\Controllers\Manager\InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/transactions', [App\Http\Controllers\Manager\InventoryController::class, 'getTransactions'])->name('inventory.transactions');
    
    // Report routes
    Route::get('/reports', [App\Http\Controllers\Manager\ReportController::class, 'index'])->name('report.index');
    Route::get('/reports/harvest', [App\Http\Controllers\Manager\ReportController::class, 'harvestReport'])->name('report.harvest');
    Route::get('/reports/fall-monitoring', [App\Http\Controllers\Manager\ReportController::class, 'fallMonitoringReport'])->name('report.fall-monitoring');
    Route::get('/reports/inventory', [App\Http\Controllers\Manager\ReportController::class, 'inventoryReport'])->name('report.inventory');
});

// Add these routes to the auth middleware group
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
    Route::get('/orchards/{orchard}', [OrchardController::class, 'show'])->name('orchards.show');
    Route::get('/orchards/total-falls', [OrchardController::class, 'getTotalFalls'])->name('orchards.total-falls');

    //devices routes
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices');
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
    Route::get('/devices/{device}', [DeviceController::class, 'show'])->name('devices.show');
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


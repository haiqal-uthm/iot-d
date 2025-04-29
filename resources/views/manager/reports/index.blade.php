<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reports & Analytics') }}
        </h2>
    </x-slot>

    <!-- Add CSS Link -->
    <link rel="stylesheet" href="{{ asset('css/manager/manager-reports.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 reports-container">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Harvest Report Card -->
                <div class="report-card bg-white dark:bg-gray-800">
                    <div class="p-6 card-content">
                        <div class="flex items-center mb-5">
                            <div class="card-icon-container bg-harvest mr-4">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                            <h3 class="card-title text-gray-900 dark:text-gray-100">Harvest Report</h3>
                        </div>
                        <p class="card-description dark:text-gray-400">
                            Generate detailed reports on durian harvests with filters for date range, farmer, durian type, and farm.
                        </p>
                        <a href="{{ route('manager.report.harvest') }}" class="card-action inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white">
                            <i class="fas fa-file-alt mr-2"></i> Generate Report
                        </a>
                    </div>
                </div>

                <!-- Fall Monitoring Report Card -->
                <div class="report-card bg-white dark:bg-gray-800">
                    <div class="p-6 card-content">
                        <div class="flex items-center mb-5">
                            <div class="card-icon-container bg-fall mr-4">
                                <i class="fas fa-apple-alt text-xl"></i>
                            </div>
                            <h3 class="card-title text-gray-900 dark:text-gray-100">Fall Monitoring Report</h3>
                        </div>
                        <p class="card-description dark:text-gray-400">
                            Track durian falls with filters for date range, farm name, and device ID.
                        </p>
                        <a href="{{ route('manager.report.fall-monitoring') }}" class="card-action inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white">
                            <i class="fas fa-file-alt mr-2"></i> Generate Report
                        </a>
                    </div>
                </div>

                <!-- Inventory Report Card -->
                <div class="report-card bg-white dark:bg-gray-800">
                    <div class="p-6 card-content">
                        <div class="flex items-center mb-5">
                            <div class="card-icon-container bg-inventory mr-4">
                                <i class="fas fa-boxes text-xl"></i>
                            </div>
                            <h3 class="card-title text-gray-900 dark:text-gray-100">Inventory Report</h3>
                        </div>
                        <p class="card-description dark:text-gray-400">
                            Monitor inventory with filters for date range, durian type, inventory type, and storage location.
                        </p>
                        <a href="{{ route('manager.report.inventory') }}" class="card-action inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white">
                            <i class="fas fa-file-alt mr-2"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Summary -->
            <div class="summary-section bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="summary-header">
                    <h3 class="summary-title text-gray-900 dark:text-gray-100">
                        <i class="fas fa-chart-bar mr-2"></i>Recent Activity Summary
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="summary-grid">
                        <div class="summary-card">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="summary-card-title">Recent Harvests</h4>
                                <i class="fas fa-seedling text-green-500"></i>
                            </div>
                            <p class="summary-card-value text-indigo-600 dark:text-indigo-400">
                                {{ App\Models\HarvestLog::whereBetween('harvest_date', [now()->subDays(7), now()])->count() }}
                            </p>
                            <p class="summary-card-subtitle">
                                in the last 7 days
                            </p>
                        </div>
                        
                        <div class="summary-card">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="summary-card-title">Durian Falls</h4>
                                <i class="fas fa-apple-alt text-yellow-500"></i>
                            </div>
                            <p class="summary-card-value text-yellow-600 dark:text-yellow-400">
                                {{ App\Models\VibrationLog::where('log_type', 'fall')->whereBetween('timestamp', [now()->subDays(7), now()])->sum('vibration_count') }}
                            </p>
                            <p class="summary-card-subtitle">
                                in the last 7 days
                            </p>
                        </div>
                        
                        <div class="summary-card">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="summary-card-title">Inventory Transactions</h4>
                                <i class="fas fa-exchange-alt text-blue-500"></i>
                            </div>
                            <p class="summary-card-value text-blue-600 dark:text-blue-400">
                                {{ App\Models\InventoryTransaction::whereBetween('created_at', [now()->subDays(7), now()])->count() }}
                            </p>
                            <p class="summary-card-subtitle">
                                in the last 7 days
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
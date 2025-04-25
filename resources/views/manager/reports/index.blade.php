<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reports & Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Harvest Report Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="rounded-full bg-green-100 dark:bg-green-900 p-3 mr-4">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Harvest Report</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Generate detailed reports on durian harvests with filters for date range, farmer, durian type, and farm.
                        </p>
                        <a href="{{ route('manager.report.harvest') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                            Generate Report
                        </a>
                    </div>
                </div>

                <!-- Fall Monitoring Report Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="rounded-full bg-yellow-100 dark:bg-yellow-900 p-3 mr-4">
                                <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Fall Monitoring Report</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Track durian falls with filters for date range, farm name, and device ID.
                        </p>
                        <a href="{{ route('manager.report.fall-monitoring') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                            Generate Report
                        </a>
                    </div>
                </div>

                <!-- Inventory Report Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="rounded-full bg-blue-100 dark:bg-blue-900 p-3 mr-4">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Inventory Report</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Monitor inventory with filters for date range, durian type, inventory type, and storage location.
                        </p>
                        <a href="{{ route('manager.report.inventory') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                            Generate Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Summary -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Recent Activity Summary</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">Recent Harvests</h4>
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                {{ App\Models\HarvestLog::whereBetween('harvest_date', [now()->subDays(7), now()])->count() }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                in the last 7 days
                            </p>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">Durian Falls</h4>
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ App\Models\VibrationLog::where('log_type', 'fall')->whereBetween('timestamp', [now()->subDays(7), now()])->sum('vibration_count') }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                in the last 7 days
                            </p>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">Inventory Transactions</h4>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ App\Models\InventoryTransaction::whereBetween('created_at', [now()->subDays(7), now()])->count() }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                in the last 7 days
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
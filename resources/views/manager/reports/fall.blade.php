<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Fall Monitoring Report') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('manager.report.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Reports
                </a>
            </div>
        </div>
    </x-slot>
    <link rel="stylesheet" href="{{ asset('css/manager/manager-reports.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12 fall-monitoring-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="filter-card bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="filter-header">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <h3 class="filter-title text-gray-900 dark:text-gray-100">Filter Fall Monitoring Data</h3>
                    </div>
                    
                    <form method="GET" action="{{ route('manager.report.fall-monitoring') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="form-group">
                                <label for="start_date" class="form-label text-gray-700 dark:text-gray-300">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="form-control mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            </div>
                            
                            <div class="form-group">
                                <label for="end_date" class="form-label text-gray-700 dark:text-gray-300">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="form-control mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            </div>
                            
                            <div class="form-group">
                                <label for="device_id" class="form-label text-gray-700 dark:text-gray-300">Device ID</label>
                                <select id="device_id" name="device_id" class="form-select mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <option value="">All Devices</option>
                                    @foreach($devices as $device)
                                        <option value="{{ $device->id }}" {{ request('device_id') == $device->id ? 'selected' : '' }}>
                                            {{ $device->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="orchard_id" class="form-label text-gray-700 dark:text-gray-300">Orchard</label>
                                <select id="orchard_id" name="orchard_id" class="form-select mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <option value="">All Orchards</option>
                                    @foreach($orchards as $orchard)
                                        <option value="{{ $orchard->id }}" {{ request('orchard_id') == $orchard->id ? 'selected' : '' }}>
                                            {{ $orchard->orchardName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" class="btn-primary inline-flex items-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 active:bg-orange-700 focus:outline-none focus:border-orange-700 focus:ring ring-orange-300 disabled:opacity-25 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Apply Filters
                            </button>
                            
                            <div class="flex space-x-2">
                                <button type="submit" name="export_type" value="pdf" class="export-button export-pdf">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Export PDF
                                </button>
                                <button type="submit" name="export_type" value="excel" class="export-button export-excel">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Export Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Summary Section (if data is available) -->
            @if(count($vibrationLogs) > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 summary-section">
                <div class="summary-header">
                    <h3 class="summary-title text-gray-900 dark:text-gray-100">
                        <i class="fas fa-chart-pie mr-2 text-orange-500"></i> Fall Monitoring Summary
                    </h3>
                </div>
                <div class="p-6">
                    <div class="summary-grid">
                        <div class="summary-card">
                            <div class="summary-card-title">Total Falls</div>
                            <div class="summary-card-value text-orange-600 dark:text-orange-400">{{ $vibrationLogs->sum('fall_count') }}</div>
                            <div class="summary-card-subtitle">Durians detected</div>
                        </div>
                        
                        <div class="summary-card">
                            <div class="summary-card-title">Active Devices</div>
                            <div class="summary-card-value text-blue-600 dark:text-blue-400">
                                {{ $vibrationLogs->pluck('device_id')->unique()->count() }}
                            </div>
                            <div class="summary-card-subtitle">Monitoring devices</div>
                        </div>
                        
                        <div class="summary-card">
                            <div class="summary-card-title">Monitored Orchards</div>
                            <div class="summary-card-value text-green-600 dark:text-green-400">
                                {{ $vibrationLogs->pluck('orchard_id')->unique()->count() }}
                            </div>
                            <div class="summary-card-subtitle">Orchards with activity</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Results Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg results-card">
                <div class="p-6">
                    <div class="results-header">
                        <i class="fas fa-apple-alt"></i>
                        <h3 class="results-title">Fall Monitoring Results</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="fall-table min-w-full">
                            <thead>
                                <tr>
                                    <th scope="col">Timestamp</th>
                                    <th scope="col">Orchard</th>
                                    <th scope="col">Device</th>
                                    <th scope="col">Fall Count</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($vibrationLogs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <span class="font-medium">{{ $log->timestamp->format('Y-m-d') }}</span>
                                            <span class="text-xs text-gray-400 dark:text-gray-500 ml-2">{{ $log->timestamp->format('H:i:s') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $log->orchard->orchardName }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $log->device->name ?? 'Device ' . $log->device_id }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <span class="fall-count-badge">
                                                {{ $log->fall_count }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No fall monitoring logs found</p>
                                                <p class="text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filter criteria</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="pagination-container mt-6">
                        {{ $vibrationLogs->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
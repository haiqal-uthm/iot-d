<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Harvest Report') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('manager.report.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Reports
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Add CSS Link -->
    <link rel="stylesheet" href="{{ asset('css/manager/manager-reports.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12 harvest-report-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 filter-card">
                <div class="p-6">
                    <div class="filter-header">
                        <i class="fas fa-filter"></i>
                        <h3 class="filter-title">Filter Harvest Data</h3>
                    </div>
                    
                    <form method="GET" action="{{ route('manager.report.harvest') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            
                            <div>
                                <label for="farmer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Farmer</label>
                                <select id="farmer_id" name="farmer_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">All Farmers</option>
                                    @foreach($farmers as $farmer)
                                        <option value="{{ $farmer->id }}" {{ request('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                            {{ $farmer->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="durian_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durian Type</label>
                                <select id="durian_id" name="durian_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">All Types</option>
                                    @foreach($durians as $durian)
                                        <option value="{{ $durian->id }}" {{ request('durian_id') == $durian->id ? 'selected' : '' }}>
                                            {{ $durian->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="farm_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Farm Name</label>
                                <select id="farm_name" name="farm_name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">All Farms</option>
                                    @foreach($farmNames as $name)
                                        <option value="{{ $name }}" {{ request('farm_name') == $name ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                                <i class="fas fa-search mr-2"></i> Apply Filters
                            </button>
                            
                            <div class="flex space-x-2">
                                <button type="submit" name="export_type" value="pdf" class="export-button export-pdf">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </button>
                                <button type="submit" name="export_type" value="excel" class="export-button export-excel">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Summary Section (if data is available) -->
            @if(count($harvestLogs) > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 summary-section">
                <div class="summary-header">
                    <h3 class="summary-title text-gray-900 dark:text-gray-100">
                        <i class="fas fa-chart-pie mr-2 text-indigo-500"></i> Harvest Summary
                    </h3>
                </div>
                <div class="p-6">
                    <div class="summary-grid">
                        <div class="summary-card">
                            <div class="summary-card-title">Total Harvests</div>
                            <div class="summary-card-value text-indigo-600 dark:text-indigo-400">{{ $harvestLogs->total() }}</div>
                            <div class="summary-card-subtitle">All time records</div>
                        </div>
                        
                        <div class="summary-card">
                            <div class="summary-card-title">Total Quantity</div>
                            <div class="summary-card-value text-green-600 dark:text-green-400">
                                {{ $harvestLogs->sum('total_harvested') }}
                            </div>
                            <div class="summary-card-subtitle">Durians harvested</div>
                        </div>
                        
                        <div class="summary-card">
                            <div class="summary-card-title">Active Farmers</div>
                            <div class="summary-card-value text-blue-600 dark:text-blue-400">
                                {{ $harvestLogs->pluck('farmer_id')->unique()->count() }}
                            </div>
                            <div class="summary-card-subtitle">Contributing farmers</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Results Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg results-card">
                <div class="p-6">
                    <div class="results-header">
                        <i class="fas fa-leaf"></i>
                        <h3 class="results-title">Harvest Results</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="harvest-table min-w-full">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Farmer</th>
                                    <th scope="col">Durian Type</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Storage</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($harvestLogs as $log)
                                    <tr>
                                        <td>
                                            <span class="font-medium">{{ $log->harvest_date->format('M d, Y') }}</span>
                                        </td>
                                        <td>
                                            {{ $log->farmer->user->name }}
                                        </td>
                                        <td>
                                            <span class="font-medium">{{ $log->durian->name }}</span>
                                        </td>
                                        <td>
                                            <span class="font-medium">{{ $log->total_harvested }}</span>
                                        </td>
                                        <td>
                                            {{ $log->storage->name ?? 'Not Assigned' }}
                                        </td>
                                        <td>
                                            @php
                                                $status = $log->storage->status ?? 'N/A';
                                                $statusClass = 'status-pending';
                                                
                                                if ($status == 'approved' || $status == 'active') {
                                                    $statusClass = 'status-approved';
                                                } elseif ($status == 'rejected' || $status == 'inactive') {
                                                    $statusClass = 'status-rejected';
                                                }
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $log->remarks ?? 'No remarks' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <i class="fas fa-seedling"></i>
                                                <p>No harvest logs found matching the criteria.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="pagination-container mt-4">
                        {{ $harvestLogs->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
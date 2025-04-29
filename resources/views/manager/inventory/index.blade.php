<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Inventory Management') }}
        </h2>
    </x-slot>

    <!-- Add CSS Link -->
    <link rel="stylesheet" href="{{ asset('css/manager/manager-inventory.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="summary-grid">
                <div class="summary-card animate-fade-in">
                    <div class="summary-icon">
                        <i class="fas fa-weight-hanging"></i>
                    </div>
                    <div class="summary-title">Total Stock</div>
                    <div class="summary-value">
                        @php
                            $totalStock = 0;
                            foreach($durianStocks as $durian) {
                                $totalStock += $durian->current_stock ?? 0;
                            }
                        @endphp
                        {{ number_format($totalStock, 1) }} kg
                    </div>
                </div>
                
                <div class="summary-card animate-fade-in delay-100">
                    <div class="summary-icon" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div class="summary-title">Storage Locations</div>
                    <div class="summary-value">{{ count($storageLocations) }}</div>
                </div>
                
                <div class="summary-card animate-fade-in delay-200">
                    <div class="summary-icon" style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fas fa-apple-alt"></i>
                    </div>
                    <div class="summary-title">Durian Varieties</div>
                    <div class="summary-value">{{ count($durianStocks) }}</div>
                </div>
                
                <div class="summary-card animate-fade-in delay-300">
                    <div class="summary-icon" style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="summary-title">Recent Transactions</div>
                    <div class="summary-value">{{ $transactions->count() }}</div>
                </div>
            </div>
            
            <!-- Durian Stock Overview -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="dashboard-header">
                        <h3 class="dashboard-title">
                            <i class="fas fa-apple-alt"></i> Durian Stock Levels
                        </h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($durianStocks as $durian)
                            <div class="stock-card">
                                <div class="stock-card-header">
                                    <h4 class="stock-card-title">{{ $durian->name }}</h4>
                                    <span class="stock-card-value {{ $durian->current_stock > 0 ? 'positive' : 'negative' }}">
                                        {{ $durian->current_stock ?? 0 }} kg
                                    </span>
                                </div>
                                
                                <div class="progress-container">
                                    @php
                                        $percentage = $durian->total > 0 ? min(100, ($durian->current_stock / $durian->total) * 100) : 0;
                                        $colorClass = $percentage > 66 ? 'success' : ($percentage > 33 ? 'warning' : 'danger');
                                    @endphp
                                    <div class="progress-bar {{ $colorClass }}" style="width: {{ $percentage }}%"></div>
                                </div>
                                
                                <div class="stock-card-footer">
                                    <span>Total Capacity</span>
                                    <span>{{ $durian->total }} kg</span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                                <p class="text-gray-500 dark:text-gray-400">No durian stock data available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Storage Locations -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="dashboard-header">
                        <h3 class="dashboard-title">
                            <i class="fas fa-warehouse"></i> Storage Locations
                        </h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($storageLocations as $storage)
                            <div class="stock-card">
                                <div class="stock-card-header">
                                    <h4 class="stock-card-title">{{ $storage->name }}</h4>
                                    <span class="stock-card-value {{ $storage->capacity_percentage < 80 ? 'positive' : 'negative' }}">
                                        {{ $storage->current_stock }} / {{ $storage->capacity }} kg
                                    </span>
                                </div>
                                
                                <div class="progress-container">
                                    @php
                                        $colorClass = $storage->capacity_percentage < 50 ? 'success' : 
                                                     ($storage->capacity_percentage < 80 ? 'warning' : 'danger');
                                    @endphp
                                    <div class="progress-bar {{ $colorClass }}" style="width: {{ $storage->capacity_percentage }}%"></div>
                                </div>
                                
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                    {{ $storage->description }}
                                    @if($storage->temperature_control)
                                        <span class="badge badge-blue ml-2">
                                            <i class="fas fa-temperature-low mr-1"></i> Temperature Controlled
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                                <p class="text-gray-500 dark:text-gray-400">No storage locations available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Recent Transactions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="dashboard-header">
                        <h3 class="dashboard-title">
                            <i class="fas fa-history"></i> Recent Inventory Transactions
                        </h3>
                        <a href="{{ route('manager.inventory.transactions') }}" class="btn-primary">
                            <i class="fas fa-eye"></i> View All Transactions
                        </a>
                    </div>
                    
                    <div id="transactions-container" class="transactions-table-container">
                        @include('manager.inventory.partials.transactions-table')
                    </div>
                    
                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
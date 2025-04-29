<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Inventory Report') }}
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

    <div class="py-12 inventory-report-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Inventory Summary Stats -->
            <div class="inventory-stats mb-6">
                <div class="inventory-stat-card">
                    <div class="inventory-stat-title">
                        <i class="fas fa-boxes"></i> Total Inventory
                    </div>
                    <div class="inventory-stat-value">
                        {{ number_format($transactions->sum('quantity'), 1) }} kg
                    </div>
                    <div class="inventory-stat-subtitle">Current total weight</div>
                </div>
                
                <div class="inventory-stat-card">
                    <div class="inventory-stat-title">
                        <i class="fas fa-arrow-circle-down"></i> Incoming
                    </div>
                    <div class="inventory-stat-value">
                        {{ number_format($transactions->where('type', 'in')->sum('quantity'), 1) }} kg
                    </div>
                    <div class="inventory-stat-subtitle">Total incoming inventory</div>
                </div>
                
                <div class="inventory-stat-card">
                    <div class="inventory-stat-title">
                        <i class="fas fa-arrow-circle-up"></i> Outgoing
                    </div>
                    <div class="inventory-stat-value">
                        {{ number_format($transactions->where('type', 'out')->sum('quantity'), 1) }} kg
                    </div>
                    <div class="inventory-stat-subtitle">Total outgoing inventory</div>
                </div>
                
                <div class="inventory-stat-card">
                    <div class="inventory-stat-title">
                        <i class="fas fa-balance-scale"></i> Adjustments
                    </div>
                    <div class="inventory-stat-value">
                        {{ number_format($transactions->where('type', 'adjustment')->sum('quantity'), 1) }} kg
                    </div>
                    <div class="inventory-stat-subtitle">Total inventory adjustments</div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 inventory-card">
                <div class="p-6">
                    <div class="inventory-header">
                        <i class="fas fa-filter"></i>
                        <h3 class="inventory-title">Filter Inventory Data</h3>
                    </div>
                    
                    <form method="GET" action="{{ route('manager.report.inventory') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                            </div>
                            
                            <div>
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                            </div>
                            
                            <div>
                                <label for="durian_id" class="form-label">Durian Type</label>
                                <select id="durian_id" name="durian_id" class="form-select">
                                    <option value="">All Types</option>
                                    @foreach($durians as $durian)
                                        <option value="{{ $durian->id }}" {{ request('durian_id') == $durian->id ? 'selected' : '' }}>
                                            {{ $durian->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="type" class="form-label">Inventory Type</label>
                                <select id="type" name="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Incoming</option>
                                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Outgoing</option>
                                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="storage_location" class="form-label">Storage Location</label>
                                <select id="storage_location" name="storage_location" class="form-select">
                                    <option value="">All Locations</option>
                                    @foreach($storageLocations as $storage)
                                        <option value="{{ $storage->id }}" {{ request('storage_location') == $storage->id ? 'selected' : '' }}>
                                            {{ $storage->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" class="btn-primary">
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
            
            <!-- Results Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg inventory-card">
                <div class="p-6">
                    <div class="inventory-header">
                        <i class="fas fa-clipboard-list"></i>
                        <h3 class="inventory-title">Inventory Results</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="inventory-table min-w-full">
                            <thead>
                                <tr>
                                    <th>Date Added</th>
                                    <th>Durian Type</th>
                                    <th>Weight/Quantity</th>
                                    <th>Type</th>
                                    <th>Storage Location</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>
                                            {{ $transaction->created_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td>
                                            {{ $transaction->durian->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="font-medium">{{ $transaction->quantity }}</span> kg
                                        </td>
                                        <td>
                                            @if($transaction->type == 'in')
                                                <span class="inventory-badge inventory-badge-green">Incoming</span>
                                            @elseif($transaction->type == 'out')
                                                <span class="inventory-badge inventory-badge-red">Outgoing</span>
                                            @else
                                                <span class="inventory-badge inventory-badge-yellow">Adjustment</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $transaction->storage->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $transaction->notes ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8">
                                            <div class="empty-state">
                                                <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                                                <p class="text-gray-500">No inventory transactions found matching the criteria.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6 pagination-container">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
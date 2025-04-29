<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Inventory Management') }}
        </h2>
    </x-slot>

    <!-- Add CSS Link -->
    <link rel="stylesheet" href="{{ asset('css/admin-inventory.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Summary Cards -->
            <div class="summary-grid mb-6">
                <div class="summary-card">
                    <div class="summary-title">Total Stock</div>
                    <div class="summary-value">
                        @php
                            $totalStock = 0;
                            foreach($farmers as $farmer) {
                                foreach($storageLocations as $location) {
                                    $totalStock += $stockLevels[$farmer->id][$location] ?? 0;
                                }
                            }
                        @endphp
                        {{ $totalStock }} kg
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-title">Storage Locations</div>
                    <div class="summary-value">{{ count($storageLocations) }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-title">Farmers</div>
                    <div class="summary-value">{{ count($farmers) }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-title">Durian Varieties</div>
                    <div class="summary-value">{{ count($durianTypes) }}</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="inventory-card mb-6">
                <div class="inventory-card-header">
                    <h3 class="inventory-card-title">
                        <i class="fas fa-filter"></i> Filter Transactions
                    </h3>
                </div>
                <div class="inventory-card-body filter-container">
                    <form action="{{ route('admin.inventory.index') }}" method="GET" class="filter-form">
                        <div class="form-control">
                            <label for="farmer_id" class="form-label">Farmer</label>
                            <select name="farmer_id" id="farmer_id" class="form-select">
                                <option value="">All Farmers</option>
                                @foreach($farmers as $farmer)
                                    <option value="{{ $farmer->id }}" {{ request('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                        {{ $farmer->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-control">
                            <label for="durian_id" class="form-label">Durian Type</label>
                            <select name="durian_id" id="durian_id" class="form-select">
                                <option value="">All Types</option>
                                @foreach($durianTypes as $durian)
                                    <option value="{{ $durian->id }}" {{ request('durian_id') == $durian->id ? 'selected' : '' }}>
                                        {{ $durian->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-control">
                            <label for="storage_location" class="form-label">Storage Location</label>
                            <select name="storage_location" id="storage_location" class="form-select">
                                <option value="">All Locations</option>
                                @foreach($storageLocations as $location)
                                    <option value="{{ $location }}" {{ request('storage_location') == $location ? 'selected' : '' }}>
                                        {{ $storageNames[$location] ?? ucfirst(str_replace('_', ' ', $location)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-control">
                            <label for="type" class="form-label">Transaction Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stock In</option>
                                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stock Out</option>
                            </select>
                        </div>
                        
                        <div class="form-control">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-input">
                        </div>
                        
                        <div class="form-control">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-input">
                        </div>
                        
                        <div class="filter-buttons-container">
                            <button type="submit" class="btn btn-primary btn-mini">
                                <i class="fas fa-search btn-icon"></i> Filter
                            </button>
                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-danger btn-mini">
                                <i class="fas fa-times btn-icon"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stock Levels Summary -->
            <div class="inventory-card mb-6">
                <div class="inventory-card-header">
                    <h3 class="inventory-card-title">
                        <i class="fas fa-chart-pie"></i> Current Stock Levels
                    </h3>
                </div>
                <div class="inventory-card-body">
                    <div class="overflow-x-auto">
                        <table class="inventory-table">
                            <thead>
                                <tr>
                                    <th>Farmer</th>
                                    @foreach($storageLocations as $location)
                                        <th>
                                            {{ $storageNames[$location] ?? ucfirst(str_replace('_', ' ', $location)) }}
                                        </th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($farmers as $farmer)
                                    <tr>
                                        <td class="font-medium">
                                            {{ $farmer->user->name }}
                                        </td>
                                        @php
                                            $farmerTotal = 0;
                                        @endphp
                                        @foreach($storageLocations as $location)
                                            @php
                                                $locationStock = $stockLevels[$farmer->id][$location] ?? 0;
                                                $farmerTotal += $locationStock;
                                            @endphp
                                            <td>
                                                {{ $locationStock }} kg
                                            </td>
                                        @endforeach
                                        <td class="font-medium">
                                            {{ $farmerTotal }} kg
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Add New Inventory Transaction -->
            <div class="inventory-card mb-6">
                <div class="inventory-card-header">
                    <h3 class="inventory-card-title">
                        <i class="fas fa-plus-circle"></i> Add New Inventory Transaction
                    </h3>
                    <a href="{{ route('admin.storage.index') }}" class="btn btn-success">
                        <i class="fas fa-warehouse btn-icon"></i> Manage Storage
                    </a>
                </div>
                <div class="inventory-card-body transaction-form-container">
                    <form action="{{ route('admin.inventory.store') }}" method="POST" class="filter-form">
                        @csrf
                        <div class="form-control">
                            <label for="farmer_id_new" class="form-label">Farmer</label>
                            <select name="farmer_id" id="farmer_id_new" required class="form-select">
                                <option value="">Select Farmer</option>
                                @foreach($farmers as $farmer)
                                    <option value="{{ $farmer->id }}">{{ $farmer->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-control">
                            <label for="durian_id_new" class="form-label">Durian Type</label>
                            <select name="durian_id" id="durian_id_new" required class="form-select">
                                <option value="">Select Type</option>
                                @foreach($durianTypes as $durian)
                                    <option value="{{ $durian->id }}">{{ $durian->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-control">
                            <label for="storage_location_new" class="form-label">Storage Location</label>
                            <select name="storage_location" id="storage_location_new" required class="form-select">
                                <option value="">Select Location</option>
                                @foreach($storageLocations as $location)
                                <option value="{{ $location }}" {{ request('storage_location') == $location ? 'selected' : '' }}>
                                    {{ $storageNames[$location] ?? ucfirst(str_replace('_', ' ', $location)) }}
                                </option>
                            @endforeach
                            </select>
                        </div>
                        
                        <div class="form-control">
                            <label for="type_new" class="form-label">Transaction Type</label>
                            <select name="type" id="type_new" required class="form-select">
                                <option value="in">Stock In</option>
                                <option value="out">Stock Out</option>
                            </select>
                        </div>
                        
                        <div class="form-control">
                            <label for="quantity" class="form-label">Quantity (kg)</label>
                            <input type="number" name="quantity" id="quantity" required min="0.1" step="0.1" class="form-input">
                        </div>
                        
                        <div class="form-control col-span-full">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea name="remarks" id="remarks" rows="2" class="form-input"></textarea>
                        </div>
                        
                        <div class="transaction-button-container">
                            <button type="submit" class="btn btn-success btn-mini">
                                <i class="fas fa-plus btn-icon"></i> Add Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Inventory Transactions Table -->
            <div class="inventory-card">
                <div class="inventory-card-header">
                    <h3 class="inventory-card-title">
                        <i class="fas fa-exchange-alt"></i> Inventory Transactions
                    </h3>
                </div>
                <div class="inventory-card-body">
                    <div class="overflow-x-auto">
                        <table class="inventory-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Farmer</th>
                                    <th>Durian Type</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventoryTransactions as $transaction)
                                    <tr>
                                        <td>
                                            {{ $transaction->created_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td>
                                            {{ $transaction->farmer->user->name }}
                                        </td>
                                        <td>
                                            {{ $transaction->durian->name }}
                                        </td>
                                        <td>
                                            {{ ucfirst(str_replace('_', ' ', $transaction->storage_location)) }}
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $transaction->type === 'in' ? 'status-in' : 'status-out' }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ abs($transaction->quantity) }} kg
                                        </td>
                                        <td>
                                            {{ $transaction->remarks ?? '-' }}
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.inventory.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction? This will update the inventory levels.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">No transactions found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
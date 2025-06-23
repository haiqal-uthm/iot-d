<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Inventory Management') }}
        </h2>
    </x-slot>

    <!-- Add CSS Link -->
    <link rel="stylesheet" href="{{ asset('css/farmer/farmer-inventory.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 inventory-container">
            <!-- Display success message if any -->
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Display error message if any -->
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Current Stock Levels -->
            <div class="stock-level-container">
                <h3 class="stock-level-title">
                    <i class="fas fa-warehouse"></i>
                    Current Stock Levels
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($stockLevels as $location => $quantity)
                        <div class="stock-card">
                            <h4>Storage {{ $storageNames[$location] ?? 'Storage '.$location }}</h4>
                            <p class="text-2xl font-bold {{ $quantity < 0 ? 'negative' : 'positive' }}">
                                {{ number_format($quantity, 0) }} Durian
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Add Stock Form -->
            <div class="form-section">
                <h3 class="form-title">
                    <i class="fas fa-exchange-alt"></i>
                    Record Stock Movement
                </h3>
                <form method="POST" action="{{ route('farmer.inventory.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Durian Type</label>
                            <select name="durian_id" class="form-control" required>
                                @foreach ($durianTypes as $durian)
                                    <option value="{{ $durian->id }}">{{ $durian->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Storage Location</label>
                            <select name="storage_location" class="form-control" required>
                                @foreach ($storageLocations as $location)
                                    <option value="{{ $location }}">{{ $storageNames[$location] ?? 'Storage '.$location }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Quantity (Durian)</label>
                            <input type="number" name="quantity" class="form-control" min="1"
                                step="0.1" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Remarks</label>
                            <input type="text" name="remarks" class="form-control"
                                placeholder="Additional notes...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <div class="btn-group">
                                <button type="submit" name="type" value="in"
                                    class="btn btn-stock-in">
                                    <i class="fas fa-arrow-down"></i>
                                    Stock In
                                </button>
                                <button type="submit" name="type" value="out"
                                    class="btn btn-stock-out">
                                    <i class="fas fa-arrow-up"></i>
                                    Stock Out
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Inventory Table -->
            <div class="table-section">
                <h3 class="table-title">
                    <i class="fas fa-history"></i>
                    Recent Transactions
                </h3>
                <div class="overflow-x-auto">
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Durian Type</th>
                                <th>Storage Location</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventoryTransactions as $transaction)
                                <tr>
                                    <td>
                                        <span class="transaction-type {{ $transaction->type == 'in' ? 'transaction-in' : 'transaction-out' }}">
                                            {{ strtoupper($transaction->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->durian->name ?? 'Unknown' }}</td>
                                    <td>{{ $storageNames[$transaction->storage_location] ?? 'Storage '.$transaction->storage_location }}</td>
                                    <td>{{ abs($transaction->quantity) }} Durian</td>
                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $transaction->remarks ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        <div>
                                            <i class="fas fa-box-open"></i>
                                            <p>No inventory transactions found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-4">
                    @if(isset($inventoryTransactions) && method_exists($inventoryTransactions, 'links'))
                        {{ $inventoryTransactions->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

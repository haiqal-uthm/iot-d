<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Inventory Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <!-- Add Stock Form -->
                <div class="mb-8 border-b pb-6">
                    <h3 class="text-lg font-semibold mb-4">Record Stock Movement</h3>
                    <form method="POST" action="{{ route('farmer.inventory.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Storage Location</label>
                                <select name="storage_location" class="w-full border rounded p-2" required>
                                    @foreach ($storageLocations as $location)
                                        <option value="{{ $location }}">Storage {{ $location }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Quantity (kg)</label>
                                <input type="number" name="quantity" class="w-full border rounded p-2" min="1"
                                    step="0.1" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Type</label>
                                <div class="flex gap-4 mt-2">
                                    <button type="submit" name="type" value="in"
                                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                        Stock In
                                    </button>
                                    <button type="submit" name="type" value="out"
                                        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                        Stock Out
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Remarks</label>
                                <input type="text" name="remarks" class="w-full border rounded p-2"
                                    placeholder="Additional notes...">
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Current Stock Levels -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Current Stock Levels</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach ($stockLevels as $location => $quantity)
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded shadow">
                                <h4 class="font-medium">Storage {{ $location }}</h4>
                                <p class="text-2xl font-bold {{ $quantity < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($quantity, 2) }} kg
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Inventory Table -->
                <h3 class="text-lg font-semibold mb-4">Recent Transactions</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="pb-2 text-left">Type</th>
                                <th class="pb-2 text-left">Storage Location</th>
                                <th class="pb-2 text-left">Quantity</th>
                                <th class="pb-2 text-left">Date</th>
                                <th class="pb-2 text-left">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventoryTransactions as $transaction)
                                <tr class="border-b">
                                    <td class="py-4">
                                        <span
                                            class="px-2 py-1 rounded {{ $transaction->type == 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ strtoupper($transaction->type) }}
                                        </span>
                                    </td>
                                    <td>Storage {{ $transaction->storage_location }}</td>
                                    <td>{{ $transaction->quantity }} kg</td>
                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $transaction->remarks ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center">No inventory transactions found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

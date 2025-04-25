<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Inventory Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Durian Stock Overview -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Durian Stock Levels</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($durianStocks as $durian)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ $durian->name }}</h4>
                                    <span class="text-sm font-semibold {{ $durian->current_stock > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $durian->current_stock ?? 0 }}/kg in stock
                                    </span>
                                </div>
                                
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5 mb-2">
                                    @php
                                        $percentage = $durian->total > 0 ? min(100, ($durian->current_stock / $durian->total) * 100) : 0;
                                        $colorClass = $percentage > 66 ? 'bg-green-600' : ($percentage > 33 ? 'bg-yellow-400' : 'bg-red-600');
                                    @endphp
                                    <div class="{{ $colorClass }} h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                
                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span>Total</span>
                                    <span>{{ $durian->total }}</span>
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
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Storage Locations</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($storageLocations as $storage)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ $storage->name }}</h4>
                                    <span class="text-sm font-semibold {{ $storage->capacity_percentage < 80 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $storage->current_stock }} / {{ $storage->capacity }}/kg
                                    </span>
                                </div>
                                
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5 mb-2">
                                    @php
                                        $colorClass = $storage->capacity_percentage < 50 ? 'bg-green-600' : 
                                                     ($storage->capacity_percentage < 80 ? 'bg-yellow-400' : 'bg-red-600');
                                    @endphp
                                    <div class="{{ $colorClass }} h-2.5 rounded-full" style="width: {{ $storage->capacity_percentage }}%"></div>
                                </div>
                                
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                    {{ $storage->description }}
                                    @if($storage->temperature_control)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            Temperature Controlled
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
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Recent Inventory Transactions</h3>
                        <a href="{{ route('manager.inventory.transactions') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            View All Transactions
                        </a>
                    </div>
                    
                    <div id="transactions-container">
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
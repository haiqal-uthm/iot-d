<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Inventory Transactions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Filters -->
                    <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">Filter Transactions</h3>
                        <form id="filter-form" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="durian_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durian Type</label>
                                <select id="durian_id" name="durian_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">All Types</option>
                                    @foreach(App\Models\Durian::all() as $durian)
                                        <option value="{{ $durian->id }}" {{ request('durian_id') == $durian->id ? 'selected' : '' }}>
                                            {{ $durian->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="storage_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Storage Location</label>
                                <select id="storage_location" name="storage_location" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">All Locations</option>
                                    @foreach(App\Models\Storage::getLocations() as $id => $name)
                                        <option value="{{ $id }}" {{ request('storage_location') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Transaction Type</label>
                                <select id="type" name="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">All Types</option>
                                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>In</option>
                                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Out</option>
                                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                </select>
                            </div>
                            
                            <div class="md:col-span-3 flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Apply Filters
                                </button>
                                <button type="button" id="reset-filters" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Transactions Table -->
                    <div id="transactions-container">
                        @include('manager.inventory.partials.transactions-table')
                    </div>
                    
                    <div class="mt-4">
                        {{ $transactions->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sorting functionality
            const table = document.getElementById('transactions-table');
            const sortableHeaders = document.querySelectorAll('.sortable');
            let currentSort = {
                column: 'created_at',
                order: 'desc'
            };
            
            sortableHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const column = this.getAttribute('data-sort');
                    
                    // Toggle sort order or set default
                    if (currentSort.column === column) {
                        currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
                    } else {
                        currentSort.column = column;
                        currentSort.order = 'asc';
                    }
                    
                    // Update UI to show sort direction
                    sortableHeaders.forEach(h => {
                        const icon = h.querySelector('.sort-icon');
                        if (h.getAttribute('data-sort') === currentSort.column) {
                            icon.textContent = currentSort.order === 'asc' ? '↑' : '↓';
                        } else {
                            icon.textContent = '↕';
                        }
                    });
                    
                    // Fetch sorted data
                    fetchTransactions();
                });
            });
            
            // Filter form submission
            const filterForm = document.getElementById('filter-form');
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                fetchTransactions();
            });
            
            // Reset filters
            const resetButton = document.getElementById('reset-filters');
            resetButton.addEventListener('click', function() {
                filterForm.reset();
                fetchTransactions();
            });
            
            // Function to fetch transactions with current sort and filter
            function fetchTransactions() {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                
                // Add sorting parameters
                params.append('sort_by', currentSort.column);
                params.append('sort_order', currentSort.order);
                
                // Fetch data
                fetch(`{{ route('manager.inventory.transactions') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('transactions-container').innerHTML = html;
                    
                    // Re-attach event listeners to the new table
                    const newSortableHeaders = document.querySelectorAll('.sortable');
                    newSortableHeaders.forEach(header => {
                        header.addEventListener('click', function() {
                            const column = this.getAttribute('data-sort');
                            
                            if (currentSort.column === column) {
                                currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
                            } else {
                                currentSort.column = column;
                                currentSort.order = 'asc';
                            }
                            
                            newSortableHeaders.forEach(h => {
                                const icon = h.querySelector('.sort-icon');
                                if (h.getAttribute('data-sort') === currentSort.column) {
                                    icon.textContent = currentSort.order === 'asc' ? '↑' : '↓';
                                } else {
                                    icon.textContent = '↕';
                                }
                            });
                            
                            fetchTransactions();
                        });
                    });
                })
                .catch(error => {
                    console.error('Error fetching transactions:', error);
                });
            }
        });
    </script>
</x-app-layout>
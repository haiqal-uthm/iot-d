<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="transactions-table">
        <thead>
            <tr>
                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sortable" data-sort="created_at">
                    Date/Time
                    <span class="sort-icon ml-1">↕</span>
                </th>
                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sortable" data-sort="farmer_id">
                    Farmer
                    <span class="sort-icon ml-1">↕</span>
                </th>
                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sortable" data-sort="durian_id">
                    Durian Type
                    <span class="sort-icon ml-1">↕</span>
                </th>
                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sortable" data-sort="storage_location">
                    Storage Location
                    <span class="sort-icon ml-1">↕</span>
                </th>
                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sortable" data-sort="quantity">
                    Quantity/Durian
                    <span class="sort-icon ml-1">↕</span>
                </th>
                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sortable" data-sort="type">
                    Transaction Type
                    <span class="sort-icon ml-1">↕</span>
                </th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($transactions as $transaction)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $transaction->created_at->format('Y-m-d H:i:s') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $transaction->farmer->user->name ?? 'Unknown' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $transaction->durian->name ?? 'Unknown' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $transaction->storage->name ?? 'Unknown' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->quantity > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $transaction->quantity > 0 ? '+' : '' }}{{ $transaction->quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $transaction->type == 'in' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                               ($transaction->type == 'out' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                               'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }}">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        No transactions found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
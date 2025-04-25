<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Durian Fall Monitoring') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <!-- Orchard Summary Cards -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Durian Falls by Orchard</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @forelse($orchardFallCounts as $fallCount)
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow">
                                        <h4 class="font-medium text-gray-800 dark:text-gray-200">
                                            {{ $fallCount->orchard->orchardName ?? 'Unknown Orchard' }}
                                        </h4>
                                        <div class="mt-2 flex items-center">
                                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                                {{ $fallCount->total_falls }}
                                            </span>
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                                Durian Falls
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Device ID: {{ $fallCount->device_id }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                                        <p class="text-gray-500 dark:text-gray-400">No durian fall data available</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
            <!-- Line Chart -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Durian Falls Over Time</h3>
                    <div style="height: 300px;">
                        <canvas id="durianFallChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Records Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Device ID</th>
                                <th
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Orchard</th>
                                <th
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fall Count</th>
                                <th
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700"
                            id="recordsTable">
                            @forelse($vibrationLogs as $log)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $log->device_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $log->orchard->orchardName ?? 'Unknown' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $log->vibration_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $log->timestamp->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No
                                        Vibration Data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $vibrationLogs->appends(request()->except('page'))->links() }}
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($chartData);
            
            const ctx = document.getElementById('durianFallChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(item => item.date),
                    datasets: [{
                        label: 'Number of Durian Falls',
                        data: chartData.map(item => item.count),
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Falls'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>

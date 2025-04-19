<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Harvest Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Harvest Chart -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold mb-4">Harvest Distribution</h3>
                <div class="chart-container">
                    <canvas id="harvestChart" height="100"></canvas>
                </div>
            </div>

            <!-- Harvest Logs Table -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="pb-2">Durian Type</th>
                                <th class="pb-2">Harvest Date</th>
                                <th class="pb-2">Grades</th>
                                <th class="pb-2">Conditions</th>
                                <th class="pb-2">Storage</th>
                                <th class="pb-2">Status</th>
                                <th class="pb-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($harvestLogs as $log)
                            <tr class="border-b">
                                <td>{{ $log->durian ? $log->durian->name : $log->durian_type }}</td>
                                <td>{{ $log->harvest_date->format('M d, Y') }}</td>
                                <td>
                                    @php
                                        $grades = is_string($log->grade) ? json_decode($log->grade, true) : $log->grade;
                                        echo implode(', ', (array)$grades);
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        $conditions = is_string($log->condition) ? json_decode($log->condition, true) : $log->condition;
                                        echo implode(', ', (array)$conditions);
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        $storage = is_string($log->storage_location) ? json_decode($log->storage_location, true) : $log->storage_location;
                                        echo implode(', ', (array)$storage);
                                    @endphp
                                </td>
                                <td>
                                    <span class="px-2 py-1 rounded 
                                        {{ $log->status === 'approved' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800' }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('farmer.harvest.edit', $log->id) }}" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('harvestChart').getContext('2d');
            const chartData = @json($chartData);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(chartData),
                    datasets: [{
                        label: 'Harvest Count',
                        data: Object.values(chartData),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
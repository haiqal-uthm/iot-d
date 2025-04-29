<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Harvest Report') }}
        </h2>
    </x-slot>

    <!-- Add CSS Link -->
    <link rel="stylesheet" href="{{ asset('css/farmer/farmer-report.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12 harvest-report-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Harvest Chart -->
            <div class="chart-section">
                <h3 class="chart-title">
                    <i class="fas fa-chart-bar"></i>
                    Harvest Distribution
                </h3>
                <div class="chart-container">
                    <canvas id="harvestChart"></canvas>
                </div>
            </div>

            <!-- Harvest Logs Table -->
            <div class="table-section">
                <h3 class="table-title">
                    <i class="fas fa-list"></i>
                    Harvest Records
                </h3>
                <div class="overflow-x-auto">
                    <table class="harvest-table">
                        <thead>
                            <tr>
                                <th>Durian Type</th>
                                <th>Harvest Date</th>
                                <th>Grades</th>
                                <th>Conditions</th>
                                <th>Storage</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($harvestLogs as $log)
                            <tr>
                                <td>{{ $log->durian ? $log->durian->name : $log->durian_type }}</td>
                                <td>{{ $log->harvest_date->format('M d, Y') }}</td>
                                <td>{{ $log->grade }}</td>
                                <td>{{ $log->condition }}</td>
                                <td>
                                    @php
                                        // Get storage name from storage table using the storage_location as foreign key
                                        $storageName = App\Models\Storage::find($log->storage_location)->name ?? 'N/A';
                                        echo $storageName;
                                    @endphp
                                </td>
                                <td>
                                    <span class="status-badge {{ $log->status === 'approved' ? 'status-approved' : 'status-pending' }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('farmer.harvest.show', $log->id) }}" 
                                           class="action-button view-button" title="View Details">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('farmer.harvest.edit', $log->id) }}" 
                                           class="action-button edit-button" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <div class="py-8">
                                        <i class="fas fa-seedling text-4xl text-gray-400 mb-4"></i>
                                        <p>No harvest records found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
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
                        backgroundColor: [
                            'rgba(56, 161, 105, 0.2)',
                            'rgba(66, 153, 225, 0.2)',
                            'rgba(237, 137, 54, 0.2)',
                            'rgba(183, 148, 244, 0.2)',
                            'rgba(72, 187, 120, 0.2)',
                            'rgba(246, 173, 85, 0.2)'
                        ],
                        borderColor: [
                            'rgba(56, 161, 105, 1)',
                            'rgba(66, 153, 225, 1)',
                            'rgba(237, 137, 54, 1)',
                            'rgba(183, 148, 244, 1)',
                            'rgba(72, 187, 120, 1)',
                            'rgba(246, 173, 85, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 10,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            },
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
            
            // Apply dark mode adjustments if needed
            if (document.documentElement.classList.contains('dark')) {
                Chart.defaults.color = '#e2e8f0';
                Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
            }
        });
    </script>
</x-app-layout>
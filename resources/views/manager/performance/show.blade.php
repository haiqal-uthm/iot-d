<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Farmer Performance') }}: {{ $farmer->user->name }}
            </h2>
            <a href="{{ route('manager.performance.index') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-arrow-left mr-2"></i> Back to All Farmers
            </a>
        </div>
    </x-slot>

    <!-- Add CSS Link -->
    <link rel="stylesheet" href="{{ asset('css/manager/manager-farmer.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 performance-detail-container">
            <!-- Farmer Details Card -->
            <div class="profile-card mb-6">
                <div class="profile-header">
                    <div class="flex flex-col md:flex-row items-center md:items-start">
                        <div class="md:mr-6 flex flex-col items-center md:items-start">
                            @if ($farmer->profile_image)
                                <img class="profile-image" src="{{ asset('storage/' . $farmer->profile_image) }}"
                                    alt="{{ $farmer->user->name }}">
                            @else
                                <div class="profile-image-placeholder">
                                    {{ substr($farmer->user->name, 0, 1) }}
                                </div>
                            @endif
                            <h3 class="profile-name">{{ $farmer->user->name }}</h3>
                            <p class="profile-farm">{{ $farmer->farm_name }}</p>
                        </div>

                        <div class="flex-grow w-full md:w-auto">
                            <div class="info-grid">

                                @if ($farmer->notes)
                                    <div class="info-item col-span-full">
                                        <div class="info-label">
                                            <i class="fas fa-sticky-note mr-1"></i> Notes
                                        </div>
                                        <div class="info-value">{{ $farmer->notes }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assigned Orchards -->
            <div class="section-card">
                <div class="section-card-header">
                    <i class="fas fa-tree"></i>
                    <h3 class="section-card-title">Assigned Orchards</h3>
                </div>

                <div class="section-card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($farmer->orchards as $orchard)
                            <div class="orchard-card">
                                <h4 class="orchard-name">
                                    {{ $orchard->orchardName }}
                                </h4>
                                <p class="orchard-detail">
                                    <i class="fas fa-map-pin text-indigo-500 mr-1"></i>
                                    {{ $orchard->location ?? 'Not specified' }}
                                </p>
                                <p class="orchard-detail">
                                    <i class="fas fa-ruler-combined text-green-500 mr-1"></i>
                                    {{ $orchard->orchardSize ?? 'Not specified' }} acres
                                </p>
                            </div>
                        @empty
                            <div class="col-span-3 p-4 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-info-circle mr-2"></i> No orchards assigned to this farmer.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Performance Chart -->
            <div class="section-card">
                <div class="section-card-header">
                    <i class="fas fa-chart-bar"></i>
                    <h3 class="section-card-title">Harvest Performance</h3>
                </div>

                <div class="section-card-body">
                    <div class="chart-container">
                        <canvas id="harvestChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Harvests -->
            <div class="section-card">
                <div class="section-card-header">
                    <i class="fas fa-history"></i>
                    <h3 class="section-card-title">Recent Harvests</h3>
                </div>

                <div class="section-card-body">
                    <div class="overflow-x-auto">
                        <table class="harvest-table">
                            <thead>
                                <tr>
                                    <th><i class="far fa-calendar-alt mr-1"></i> Date</th>
                                    <th><i class="fas fa-tree mr-1"></i> Orchard</th>
                                    <th><i class="fas fa-leaf mr-1"></i> Durian Type</th>
                                    <th><i class="fas fa-sort-amount-up mr-1"></i> Total Harvested</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentHarvests = App\Models\HarvestLog::where('farmer_id', $farmer->id)
                                        ->with(['orchard', 'durian'])
                                        ->orderBy('harvest_date', 'desc')
                                        ->limit(10)
                                        ->get();
                                @endphp

                                @forelse($recentHarvests as $harvest)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td>
                                            {{ $harvest->harvest_date->format('Y-m-d') }}
                                        </td>
                                        <td>
                                            {{ $harvest->orchard->orchardName ?? 'Unknown' }}
                                        </td>
                                        <td>
                                            {{ $harvest->durian->name ?? 'Not specified' }}
                                        </td>
                                        <td>
                                            <span class="font-medium">{{ $harvest->total_harvested }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-info-circle mr-2"></i> No recent harvests found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Chart initialization started');

            // Initial data
            const dates = @json($harvestData['dates']);
            const counts = @json($harvestData['counts']);

            console.log('Data loaded:', {
                dates,
                counts
            });

            // Get the canvas element
            const ctx = document.getElementById('harvestChart');

            if (!ctx) {
                alert('Canvas element not found');
                return;
            }

            // Create chart with improved styling
            const harvestChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Number of Harvests',
                        data: counts,
                        backgroundColor: 'rgba(79, 70, 229, 0.2)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 2,
                        borderRadius: 4,
                        barPercentage: 0.8,
                        categoryPercentage: 0.9
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date',
                                color: '#6B7280',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                autoSkip: true,
                                maxRotation: 45
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Harvests',
                                color: '#6B7280',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                precision: 0,
                                stepSize: 1
                            },
                            grid: {
                                color: 'rgba(107, 114, 128, 0.1)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#6B7280',
                                font: {
                                    weight: 'bold'
                                },
                                boxWidth: 15,
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.8)',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            padding: 12,
                            cornerRadius: 6,
                            displayColors: false
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    layout: {
                        padding: {
                            top: 10,
                            right: 10,
                            bottom: 10,
                            left: 10
                        }
                    }
                }
            });

            console.log('Chart initialized');
        });
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Farmer Performance') }}: {{ $farmer->user->name }}
            </h2>
            <a href="{{ route('manager.performance.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Back to All Farmers
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Farmer Details Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-1/4 flex justify-center md:justify-start mb-4 md:mb-0">
                            @if($farmer->profile_image)
                                <img class="h-32 w-32 rounded-full object-cover" src="{{ asset('storage/' . $farmer->profile_image) }}" alt="{{ $farmer->user->name }}">
                            @else
                                <div class="h-32 w-32 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                    <span class="text-4xl font-medium text-indigo-800 dark:text-indigo-200">
                                        {{ substr($farmer->user->name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="md:w-3/4">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $farmer->user->name }}</h3>
                            <p class="text-lg text-gray-600 dark:text-gray-400">{{ $farmer->farm_name }}</p>
                            
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Information</h4>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $farmer->user->email }}</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">IC: {{ $farmer->ic_number }}</p>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</h4>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $farmer->address }}</p>
                                </div>
                            </div>
                            
                            @if($farmer->notes)
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes</h4>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $farmer->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Assigned Orchards -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Assigned Orchards</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($farmer->orchards as $orchard)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow">
                                <h4 class="font-medium text-gray-800 dark:text-gray-200">
                                    {{ $orchard->orchardName }}
                                </h4>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Location: {{ $orchard->location ?? 'Not specified' }}
                                </p>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Size: {{ $orchard->size ?? 'Not specified' }} acres
                                </p>
                            </div>
                        @empty
                            <div class="col-span-3 p-4 text-center text-gray-500 dark:text-gray-400">
                                No orchards assigned to this farmer.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Performance Chart -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Harvest Performance</h3>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-900 p-4 rounded-lg">
                        <canvas id="harvestChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Recent Harvests -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Recent Harvests</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Orchard</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Durian Type</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Harvested</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Est. Weight</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @php
                                    $recentHarvests = App\Models\HarvestLog::where('farmer_id', $farmer->id)
                                        ->with(['orchard', 'durian'])
                                        ->orderBy('harvest_date', 'desc')
                                        ->limit(10)
                                        ->get();
                                @endphp
                                
                                @forelse($recentHarvests as $harvest)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $harvest->harvest_date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $harvest->orchard->orchardName ?? 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $harvest->durian_type ?? 'Not specified' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $harvest->total_harvested }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $harvest->estimated_weight }} kg
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No recent harvests found
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
            
            console.log('Data loaded:', { dates, counts });
            
            // Get the canvas element
            const ctx = document.getElementById('harvestChart');
            
            if (!ctx) {
                alert('Canvas element not found');
                return;
            }
            
            // Create chart
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
                                color: '#6B7280'
                            },
                            ticks: {
                                autoSkip: true,
                                maxRotation: 45
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: { 
                                display: true, 
                                text: 'Total Harvests',
                                color: '#6B7280'
                            },
                            ticks: { 
                                precision: 0,
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#6B7280'
                            }
                        }
                    }
                }
            });
            
            console.log('Chart initialized');
        });
    </script>        
</x-app-layout>
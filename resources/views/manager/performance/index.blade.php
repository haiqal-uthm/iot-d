<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Farmer Performance') }}
        </h2>
    </x-slot>

    <!-- Add CSS Link -->
    <link rel="stylesheet" href="{{ asset('css/manager/manager-farmer.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 performance-container">
                    <div class="section-header">
                        <i class="fas fa-users"></i>
                        <h3>Farmers Overview</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($farmers as $farmer)
                            <div class="farmer-card">
                                <div class="farmer-header">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if($farmer->profile_image)
                                                <img class="farmer-avatar" src="{{ asset('storage/' . $farmer->profile_image) }}" alt="{{ $farmer->user->name }}">
                                            @else
                                                <div class="farmer-avatar-placeholder">
                                                    {{ substr($farmer->user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="farmer-name">
                                                {{ $farmer->user->name }}
                                            </h4>
                                            <p class="farmer-farm">
                                                {{ $farmer->farm_name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="farmer-content">
                                    <div class="orchard-section">
                                        <h5 class="orchard-title">
                                            <i class="fas fa-tree"></i>
                                            Assigned Orchards
                                        </h5>
                                        <div class="flex flex-wrap">
                                            @forelse($farmer->orchards as $orchard)
                                                <span class="orchard-tag">
                                                    <i class="fas fa-leaf mr-1"></i>
                                                    {{ $orchard->orchardName }}
                                                </span>
                                            @empty
                                                <span class="text-sm text-gray-500 dark:text-gray-400">No orchards assigned</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="farmer-footer">
                                    <a href="{{ route('manager.performance.show', $farmer->id) }}" class="view-btn">
                                        <i class="fas fa-chart-line"></i>
                                        View Performance
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 empty-state">
                                <i class="fas fa-users-slash"></i>
                                <p>No farmers found in the system.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
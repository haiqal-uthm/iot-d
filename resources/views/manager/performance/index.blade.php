<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Farmer Performance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Farmers Overview</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($farmers as $farmer)
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-600 hover:shadow-lg transition-shadow">
                                <div class="p-4 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            @if($farmer->profile_image)
                                                <img class="h-12 w-12 rounded-full object-cover" src="{{ asset('storage/' . $farmer->profile_image) }}" alt="{{ $farmer->user->name }}">
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                                    <span class="text-xl font-medium text-indigo-800 dark:text-indigo-200">
                                                        {{ substr($farmer->user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $farmer->user->name }}
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $farmer->farm_name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-4">
                                    <div class="mb-4">
                                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assigned Orchards</h5>
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($farmer->orchards as $orchard)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    {{ $orchard->orchardName }}
                                                </span>
                                            @empty
                                                <span class="text-sm text-gray-500 dark:text-gray-400">No orchards assigned</span>
                                            @endforelse
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 flex justify-end">
                                        <a href="{{ route('manager.performance.show', $farmer->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-black bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            View Performance
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 p-4 text-center text-gray-500 dark:text-gray-400">
                                No farmers found in the system.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
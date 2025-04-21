<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Harvest Record Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Farmer Name</label>
                            <p class="mt-1">{{ $harvestLog->farmer->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Orchard</label>
                            <p class="mt-1">{{ $harvestLog->orchard->orchardName }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Durian Type</label>
                            <p class="mt-1">{{ $harvestLog->durian->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Harvest Date</label>
                            <p class="mt-1">{{ $harvestLog->harvest_date->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Checklist Details -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Grades</label>
                            <p class="mt-1">{{ $harvestLog->grade }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Conditions</label>
                            <p class="mt-1">{{ $harvestLog->condition }}</p>
                        </div>
                        @if($harvestLog->remarks)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Remarks</label>
                            <p class="mt-1 whitespace-pre-wrap">{{ $harvestLog->remarks }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('farmer.harvest.report') }}" 
                       class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Back to Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
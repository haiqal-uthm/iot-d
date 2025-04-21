<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Harvest Entry') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('farmer.harvest.update', $harvestLog->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Orchard Display (non-editable) -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Orchard</label>
                            <div class="p-2 bg-gray-100 rounded">{{ $harvestLog->orchard->orchardName }}</div>
                        </div>

                        <!-- Durian Type -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Durian Type</label>
                            <select name="durian_id" class="w-full border rounded p-2" required>
                                @foreach($durians as $durian)
                                    <option value="{{ $durian->id }}" 
                                        {{ $harvestLog->durian_id == $durian->id ? 'selected' : '' }}>
                                        {{ $durian->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Harvest Date -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Harvest Date</label>
                            <input type="date" name="harvest_date"
                                value="{{ $harvestLog->harvest_date->format('Y-m-d') }}"
                                class="w-full border rounded p-2" required>
                        </div>

                        <!-- Checklist Section -->
                        <div class="mb-6 border-t pt-4">
                            <h3 class="text-lg font-semibold mb-4">Harvest Checklist</h3>

                            <!-- Grade Selection -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Durian Grade</label>
                                <select name="grade" class="w-full border rounded p-2" required>
                                    <option value="">Select Grade</option>
                                    <option value="A" {{ $harvestLog->grade == 'A' ? 'selected' : '' }}>Grade A</option>
                                    <option value="B" {{ $harvestLog->grade == 'B' ? 'selected' : '' }}>Grade B</option>
                                    <option value="C" {{ $harvestLog->grade == 'C' ? 'selected' : '' }}>Grade C</option>
                                </select>
                            </div>

                            <!-- Condition Selection -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Condition</label>
                                <select name="condition" class="w-full border rounded p-2" required>
                                    <option value="">Select Condition</option>
                                    <option value="excellent" {{ $harvestLog->condition == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="good" {{ $harvestLog->condition == 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="damaged" {{ $harvestLog->condition == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                </select>
                            </div>
                            
                            <!-- Storage Selection -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Storage Location</label>
                                <select name="storage" class="w-full border rounded p-2">
                                    <option value="">Select Storage Location</option>
                                    @foreach(App\Models\Storage::getLocations() as $id => $name)
                                        <option value="{{ $id }}" {{ $harvestLog->storage_location == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Status Selection -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Status</label>
                                <select name="status" class="w-full border rounded p-2" required>
                                    <option value="pending" {{ $harvestLog->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="complete" {{ $harvestLog->status == 'complete' ? 'selected' : '' }}>Complete</option>
                                </select>
                                <p class="text-sm text-gray-500 mt-1">
                                    Setting status to "Complete" will add items to inventory.
                                </p>
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Update Harvest
                            </button>
                            <a href="{{ route('farmer.harvest.report') }}"
                                class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

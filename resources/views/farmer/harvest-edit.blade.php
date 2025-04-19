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

                    <form action="{{ route('farmer.harvest.edit', $harvestLog->id) }}" method="POST">
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
                                <div class="grid grid-cols-3 gap-4">
                                    <label class="flex items-center">
                                        @php
                                            $grades = is_string($harvestLog->grade) 
                                                ? json_decode($harvestLog->grade, true) 
                                                : $harvestLog->grade;
                                        @endphp
                                        <input type="checkbox" name="grade[]" value="A" class="mr-2"
                                            {{ in_array('A', $grades) ? 'checked' : '' }}>
                                        Grade A
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="grade[]" value="B" class="mr-2"
                                            {{ in_array('B', $grades) ? 'checked' : '' }}>
                                        Grade B
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="grade[]" value="C" class="mr-2"
                                            {{ in_array('C', $grades) ? 'checked' : '' }}>
                                        Grade C
                                    </label>
                                </div>
                            </div>

                            <!-- Condition Checklist -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Condition</label>
                                <div class="grid grid-cols-3 gap-4">
                                    @php
                                        $conditions = is_string($harvestLog->condition)
                                            ? json_decode($harvestLog->condition, true)
                                            : $harvestLog->condition;
                                    @endphp
                                    <label class="flex items-center">
                                        <input type="checkbox" name="condition[]" value="excellent" class="mr-2"
                                            {{ in_array('excellent', $conditions) ? 'checked' : '' }}>
                                        Excellent
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="condition[]" value="good" class="mr-2"
                                            {{ in_array('good', $conditions) ? 'checked' : '' }}>
                                        Good
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="condition[]" value="damaged" class="mr-2"
                                            {{ in_array('damaged', $conditions) ? 'checked' : '' }}>
                                        Damaged
                                    </label>
                                </div>
                            </div>

                            <!-- Storage Selection -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Storage Location</label>
                                <div class="grid grid-cols-2 gap-4">
                                    @php
                                        $storage = is_string($harvestLog->storage_location)
                                            ? json_decode($harvestLog->storage_location, true)
                                            : $harvestLog->storage_location;
                                    @endphp
                                    <label class="flex items-center">
                                        <input type="checkbox" name="storage[]" value="cold_storage" class="mr-2"
                                            {{ in_array('cold_storage', $storage) ? 'checked' : '' }}>
                                        Cold Storage
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="storage[]" value="warehouse" class="mr-2"
                                            {{ in_array('warehouse', $storage) ? 'checked' : '' }}>
                                        Warehouse
                                    </label>
                                </div>
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

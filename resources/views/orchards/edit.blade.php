<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Orchard: {{ $orchard->orchardName }}
            </h2>
            <a href="{{ route('orchards.show', $orchard->id) }}" class="btn-secondary flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Details
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.orchards.update', $orchard->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="orchardName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Orchard Name</label>
                                <input type="text" name="orchardName" id="orchardName" class="mt-1 form-input block w-full" value="{{ $orchard->orchardName }}" required>
                            </div>

                            <div>
                                <label for="numTree" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of Trees</label>
                                <input type="number" name="numTree" id="numTree" class="mt-1 form-input block w-full" value="{{ $orchard->numTree }}" required>
                            </div>

                            <div>
                                <label for="orchardSize" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Orchard Size (acres)</label>
                                <input type="number" step="0.1" name="orchardSize" id="orchardSize" class="mt-1 form-input block w-full" value="{{ $orchard->orchardSize }}" required>
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                                <input type="text" name="location" id="location" class="mt-1 form-input block w-full" value="{{ $orchard->location }}" required>
                            </div>

                            <div>
                                <label for="device_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign Device</label>
                                <select name="device_id" id="device_id" class="mt-1 form-select block w-full">
                                    <option value="">-- Select Device --</option>
                                    @foreach ($devices as $device)
                                        <option value="{{ $device->id }}" {{ $orchard->device_id == $device->id ? 'selected' : '' }}>
                                            {{ $device->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="durian_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign Durian</label>
                                <select name="durian_id" id="durian_id" class="mt-1 form-select block w-full">
                                    <option value="">-- Select Durian --</option>
                                    @foreach ($durians as $durian)
                                        <option value="{{ $durian->id }}" {{ $orchard->durian_id == $durian->id ? 'selected' : '' }}>
                                            {{ $durian->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('orchards.show', $orchard->id) }}" class="btn-secondary">Cancel</a>
                            <button type="submit" class="btn-primary">Update Orchard</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
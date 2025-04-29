<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add New Orchard') }}
            </h2>
            <a href="{{ route('admin.orchards') }}" class="btn-secondary flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Orchards
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('orchards.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">
                                        Basic Information
                                    </h3>
                                    
                                    <div class="mb-4">
                                        <label for="orchardName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Orchard Name <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text" name="orchardName" id="orchardName" 
                                            class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" 
                                            value="{{ old('orchardName') }}" required>
                                        @error('orchardName')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Location <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text" name="location" id="location" 
                                            class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" 
                                            value="{{ old('location') }}" required>
                                        @error('location')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">
                                        Orchard Specifications
                                    </h3>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="mb-4">
                                            <label for="numTree" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Number of Trees <span class="text-red-600">*</span>
                                            </label>
                                            <input type="number" name="numTree" id="numTree" min="1" 
                                                class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" 
                                                value="{{ old('numTree') }}" required>
                                            @error('numTree')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label for="orchardSize" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Size (acres) <span class="text-red-600">*</span>
                                            </label>
                                            <input type="number" step="0.1" name="orchardSize" id="orchardSize" min="0.1" 
                                                class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" 
                                                value="{{ old('orchardSize') }}" required>
                                            @error('orchardSize')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">
                                        Assignments
                                    </h3>
                                    
                                    <div class="mb-4">
                                        <label for="device_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Assign Device
                                        </label>
                                        <select name="device_id" id="device_id" 
                                            class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                            <option value="">-- Select Device --</option>
                                            @foreach ($devices as $device)
                                                <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                                    {{ $device->name }} ({{ $device->status }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('device_id')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="durian_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Assign Durian Type
                                        </label>
                                        <select name="durian_id" id="durian_id" 
                                            class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                            <option value="">-- Select Durian Type --</option>
                                            @foreach ($durians as $durian)
                                                <option value="{{ $durian->id }}" {{ old('durian_id') == $durian->id ? 'selected' : '' }}>
                                                    {{ $durian->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('durian_id')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 pt-6 border-t">
                            <a href="{{ route('admin.orchards') }}" class="btn-secondary px-4 py-2 rounded-md">
                                Cancel
                            </a>
                            <button type="submit" class="btn-primary px-4 py-2 rounded-md">
                                Create Orchard
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
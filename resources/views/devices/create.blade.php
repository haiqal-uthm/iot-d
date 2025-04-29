<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add New Device') }}
            </h2>
            <a href="{{ route('devices') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left btn-icon"></i>Back to Devices
            </a>
        </div>
    </x-slot>

    <!-- Add CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin-devices.css') }}">
    <!-- Add Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Response Message Modal -->
    <div id="responseModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md modal-content">
            <div class="text-center">
                <div id="responseIcon" class="mb-4">
                    <!-- Icon will be inserted by JavaScript -->
                </div>
                <h3 id="responseTitle" class="text-xl font-bold mb-2 modal-title">
                    <!-- Title will be inserted by JavaScript -->
                </h3>
                <p id="responseMessage" class="text-gray-600 dark:text-gray-300 mb-6">
                    <!-- Message will be inserted by JavaScript -->
                </p>
                <button type="button" class="btn btn-primary w-full" onclick="closeModal('responseModal')">
                    <i class="fas fa-check btn-icon"></i>OK
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="add-device-container">
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                            <h2 class="text-xl font-semibold mb-4 border-b pb-2">Device Information</h2>
                            
                            <!-- Add Device Form -->
                            <form method="POST" action="{{ route('devices.store') }}" class="space-y-6">
                                @csrf
                                
                                <!-- Device Name -->
                                <div class="form-group">
                                    <label for="name" class="block text-sm font-medium text-gray-700 form-label">Device Name</label>
                                    <input type="text" name="name" id="name" 
                                        class="w-full mt-2 p-2 border rounded form-control @error('name') border-red-500 @enderror" 
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Device ID -->
                                <div class="form-group">
                                    <label for="device_id" class="block text-sm font-medium text-gray-700 form-label">Device ID</label>
                                    <input type="text" name="device_id" id="device_id" 
                                        class="w-full mt-2 p-2 border rounded form-control @error('device_id') border-red-500 @enderror" 
                                        value="{{ old('device_id') }}" required>
                                    @error('device_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-gray-500 text-xs mt-1">This ID must be unique and will be used to identify the device in the system.</p>
                                </div>

                                <!-- Status -->
                                <div class="form-group">
                                    <label for="status" class="block text-sm font-medium text-gray-700 form-label">Status</label>
                                    <select name="status" id="status" 
                                        class="w-full mt-2 p-2 border rounded form-select @error('status') border-red-500 @enderror" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    </select>
                                    @error('status')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                
                                <!-- Form Actions -->
                                <div class="flex justify-end space-x-2 pt-4 border-t">
                                    <a href="{{ route('devices') }}" class="btn btn-secondary">
                                        <i class="fas fa-times btn-icon"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save btn-icon"></i>Save Device
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="{{ asset('js/devices.js') }}"></script>
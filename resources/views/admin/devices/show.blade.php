<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Device Details') }}
            </h2>
        </div>
    </x-slot>

    <!-- Add CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin-devices.css') }}">
    <link rel="stylesheet" href="{{ asset('css/device-details.css') }}">
    <!-- Add Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="{{ route('admin.devices') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Devices
                        </a>
                    </div>
                    
                    <!-- Device Header -->
                    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="device-icon-large bg-blue-100 dark:bg-blue-900 text-blue-500 dark:text-blue-300 rounded-full p-3 mr-4">
                                <i class="fas fa-microchip text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $device->name }}</h1>
                                <p class="text-gray-500 dark:text-gray-400">ID: {{ $device->device_id }}</p>
                            </div>
                        </div>
                        <span class="device-status status-{{ $device->status ?? 'active' }} text-sm px-3 py-1 rounded-full">
                            {{ ucfirst($device->status ?? 'Active') }}
                        </span>
                    </div>
                    
                    <!-- Device Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Basic Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Device Information</h2>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Name:</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $device->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Device ID:</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $device->device_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ ucfirst($device->status ?? 'Active') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Created:</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $device->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Last Updated:</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $device->updated_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Control Panel -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Control Panel</h2>
                            <div class="space-y-4">
                                <!-- LED Toggle -->
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                    <div>
                                        <h3 class="font-medium text-gray-800 dark:text-gray-200">LED Status</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Toggle device LED on/off</p>
                                    </div>
                                    <div class="toggle-container">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="ledToggle-{{ $device->id }}"
                                                data-device-id="{{ $device->device_id }}"
                                                onchange="toggleLed('{{ $device->device_id }}', this.checked)"
                                                {{ $device->led_status ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <span id="ledStatus-{{ $device->id }}" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $device->led_status ? 'ON' : 'OFF' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Other controls can be added here -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activity Log -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Activity Log</h2>
                        <div class="space-y-3">
                            <!-- Sample activity logs - replace with actual data -->
                            <div class="flex items-start p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                <div class="text-green-500 dark:text-green-400 mr-3">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-200">Device connected</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Today at 10:30 AM</p>
                                </div>
                            </div>
                            <div class="flex items-start p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                <div class="text-blue-500 dark:text-blue-400 mr-3">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-200">LED status changed to ON</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Yesterday at 3:45 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button class="btn btn-primary" onclick="openEditModal('{{ $device->id }}')">
                            <i class="fas fa-edit btn-icon"></i>Edit Device
                        </button>
                        <form method="POST" action="{{ route('devices.destroy', $device->id) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this device?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt btn-icon"></i>Delete Device
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Device Modal (same as in devices.blade.php) -->
    <div id="editDeviceModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md modal-content">
            <h2 class="text-xl font-bold mb-4 modal-title">Edit Device</h2>
            <form method="POST" id="editDeviceForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editDeviceId" name="device_id">

                <div class="mb-4 form-group">
                    <label for="editDeviceName" class="block text-sm font-medium text-gray-700 form-label">Device Name</label>
                    <input type="text" id="editDeviceName" name="name" class="w-full mt-2 p-2 border rounded form-control"
                        required>
                </div>

                <!-- Status Input for Edit Form -->
                <div class="mb-4 form-group">
                    <label for="editDeviceStatus" class="block text-sm font-medium text-gray-700 form-label">Status</label>
                    <select id="editDeviceStatus" name="status" class="w-full mt-2 p-2 border rounded form-select" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-2 modal-footer">
                    <button type="button" class="btn btn-secondary"
                        onclick="closeModal('editDeviceModal')">
                        <i class="fas fa-times btn-icon"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save btn-icon"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Include the devices.js script -->
    <script src="{{ asset('js/devices.js') }}"></script>
</x-app-layout>
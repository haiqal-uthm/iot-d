<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Device Details') }}
            </h2>
            <a href="{{ route('devices') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left btn-icon"></i>Back to Devices
            </a>
        </div>
    </x-slot>

    <!-- Add CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin-devices.css') }}">
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
                    <div class="device-detail-container">
                        <!-- Device Header -->
                        <div class="device-detail-header">
                            <div class="flex items-center justify-between mb-6">
                                <h1 class="text-2xl font-bold">{{ $device->name }}</h1>
                                <span class="device-status status-{{ $device->status ?? 'active' }}">
                                    {{ ucfirst($device->status ?? 'Active') }}
                                </span>
                            </div>
                            
                        </div>

                        <!-- Device Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Information Card -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                                <h2 class="text-xl font-semibold mb-4 border-b pb-2">Basic Information</h2>
                                
                                <div class="space-y-4">
                                    <div class="flex flex-col">
                                        <span class="text-gray-500 dark:text-gray-400 text-sm">Device ID</span>
                                        <span class="font-medium">{{ $device->device_id }}</span>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-gray-500 dark:text-gray-400 text-sm">Name</span>
                                        <span class="font-medium">{{ $device->name }}</span>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-gray-500 dark:text-gray-400 text-sm">Status</span>
                                        <span class="font-medium">{{ ucfirst($device->status ?? 'Active') }}</span>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-gray-500 dark:text-gray-400 text-sm">Created At</span>
                                        <span class="font-medium">{{ $device->created_at->format('F j, Y, g:i a') }}</span>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-gray-500 dark:text-gray-400 text-sm">Last Updated</span>
                                        <span class="font-medium">{{ $device->updated_at->format('F j, Y, g:i a') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Control Card -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                                <h2 class="text-xl font-semibold mb-4 border-b pb-2">Device Control</h2>
                                
                                <div class="space-y-6">
                                    <!-- LED Control -->
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">LED Status</span>
                                        <div class="toggle-container">
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="ledToggle-{{ $device->id }}"
                                                    data-device-id="{{ $device->device_id }}"
                                                    onchange="toggleLed('{{ $device->device_id }}', this.checked)"
                                                    {{ $device->led_status ? 'checked' : '' }}>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span id="ledStatus-{{ $device->id }}" class="toggle-label ml-2">
                                                {{ $device->led_status ? 'ON' : 'OFF' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Vibration Count -->
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">Current Vibration Count</span>
                                        <div class="flex items-center">
                                            <span id="vibrationCount-{{ $device->id }}" class="text-lg font-bold mr-4">
                                                {{ $device->vibration_count ?? 0 }}
                                            </span>
                                            <button class="btn btn-sm btn-primary" onclick="saveVibrationCount('{{ $device->device_id }}', {{ $device->vibration_count ?? 0 }})">
                                                <i class="fas fa-save btn-icon"></i>Collect
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Activity History -->
                        <div class="mt-8 bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                            <h2 class="text-xl font-semibold mb-4 border-b pb-2">Activity History</h2>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Activity</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                        @if(isset($device->logs) && count($device->logs) > 0)
                                            @foreach($device->logs as $log)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->created_at->format('F j, Y, g:i a') }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->activity }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->value }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No activity records found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Device Modal -->
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
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md modal-content">
            <div class="text-center">
                <div id="deleteIcon" class="mb-4">
                    <div class="bg-red-100 rounded-full p-3 mx-auto inline-block">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
                    </div>
                </div>
                <h3 id="deleteTitle" class="text-xl font-bold mb-2 modal-title">Confirm Delete</h3>
                <p id="deleteMessage" class="text-gray-600 dark:text-gray-300 mb-6">
                    Are you sure you want to delete this device? This action cannot be undone.
                </p>
                <div class="flex justify-center space-x-4">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('deleteConfirmModal')">
                        <i class="fas fa-times btn-icon"></i>Cancel
                    </button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger">
                        <i class="fas fa-trash btn-icon"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="{{ asset('js/devices.js') }}"></script>
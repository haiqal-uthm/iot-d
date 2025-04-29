<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Devices') }}
            </h2>
        </div>
    </x-slot>

    <!-- Add CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin-devices.css') }}">
    <!-- Add Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Add Device Modal -->
    <div id="addDeviceModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md modal-content">
            <h2 class="text-xl font-bold mb-4 modal-title">Add New Device</h2>
            <form method="POST" action="{{ route('admin.devices.create') }}">
                @csrf
                <div class="mb-4 form-group">
                    <label for="name" class="block text-sm font-medium text-gray-700 form-label">Device Name</label>
                    <input type="text" name="name" id="name" class="w-full mt-2 p-2 border rounded form-control" required>
                </div>

                <!-- Device ID Input -->
                <div class="mb-4 form-group">
                    <label for="device_id" class="block text-sm font-medium text-gray-700 form-label">Device ID</label>
                    <input type="text" name="device_id" id="device_id" class="w-full mt-2 p-2 border rounded form-control"
                        required>
                </div>

                <!-- Status Input -->
                <div class="mb-4 form-group">
                    <label for="status" class="block text-sm font-medium text-gray-700 form-label">Status</label>
                    <select name="status" id="status" class="w-full mt-2 p-2 border rounded form-select" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-2 modal-footer">
                    <button type="button" class="btn btn-secondary"
                        onclick="closeModal('addDeviceModal')">
                        <i class="fas fa-times btn-icon"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save btn-icon"></i>Save
                    </button>
                </div>
            </form>
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
    
    <!-- Response Message Modal -->
    <div id="responseModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md modal-content">
            <div class="text-center">
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
    
    <!-- Main Section -->
    <div class="py-12">
        <div class="device-container">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title"></h1>
                <a href="{{ route('devices.create') }}" class="add-device-btn">
                    <i class="fas fa-plus-circle mr-2"></i> Add New Device
                </a>
            </div>

            <!-- Device List -->
            <div class="device-grid">
                @foreach ($devices as $device)
                    <div class="device-card" data-device-id="{{ $device->device_id }}">
                        <div class="device-header">
                            <h3 class="device-name">{{ $device->name }}</h3>
                            <span class="device-status status-{{ $device->status ?? 'active' }}">
                                {{ ucfirst($device->status ?? 'Active') }}
                            </span>
                        </div>
                        
                        <div class="device-body">
                            <!-- Device Information -->
                            <div class="device-info">
                                <div class="info-label">Device ID</div>
                                <div class="info-value">{{ $device->device_id }}</div>
                            </div>

                            <!-- LED Toggle -->
                            <div class="toggle-container">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="ledToggle-{{ $device->id }}"
                                        data-device-id="{{ $device->device_id }}"
                                        onchange="toggleLed('{{ $device->device_id }}', this.checked)"
                                        {{ $device->led_status ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span id="ledStatus-{{ $device->id }}" class="toggle-label">
                                    {{ $device->led_status ? 'ON' : 'OFF' }}
                                </span>
                            </div>
                        </div>

                        <!-- Device Actions -->
                        <div class="device-actions">
                            <a href="{{ route('admin.devices.show', $device->id) }}" class="btn btn-secondary">
                                <i class="fas fa-eye btn-icon"></i>Details
                            </a>
                            <!-- Replace the existing edit button with this -->
                            <a href="{{ route('devices.edit', $device->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button type="button" class="btn btn-danger" onclick="openDeleteModal('{{ $device->id }}', '{{ $device->name }}')">
                                <i class="fas fa-trash btn-icon"></i>Delete
                            </button>
                            <form id="delete-form-{{ $device->id }}" method="POST" action="{{ route('devices.destroy', $device->id) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>

<script src="{{ asset('js/devices.js') }}"></script>


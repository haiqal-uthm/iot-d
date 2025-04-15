<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Device Management') }}
            </h2>
        </div>
    </x-slot>

    <!-- Add Device Modal -->
    <div id="addDeviceModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Add New Device</h2>
            <form method="POST" action="{{ route('devices.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Device Name</label>
                    <input type="text" name="name" id="name" class="w-full mt-2 p-2 border rounded" required>
                </div>

                <!-- Device ID Input -->
                <div class="mb-4">
                    <label for="device_id" class="block text-sm font-medium text-gray-700">Device ID</label>
                    <input type="text" name="device_id" id="device_id" class="w-full mt-2 p-2 border rounded"
                        required>
                </div>

                <!-- Orchard Selection (Checkboxes for Orchard A, B, C) -->
                <div class="mb-4">
                    <label for="editDeviceOrchard" class="block text-sm font-medium text-gray-700">Orchard</label>
                    <select id="editDeviceOrchard" name="orchard_id" class="w-full mt-2 p-2 border rounded" required>
                        <option value="1" id="orchardA">Orchard A</option>
                        <option value="2" id="orchardB">Orchard B</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                        onclick="closeModal('addDeviceModal')">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Device Modal -->
    <div id="editDeviceModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Edit Device</h2>
            <form method="POST" id="editDeviceForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editDeviceId" name="device_id">

                <div class="mb-4">
                    <label for="editDeviceName" class="block text-sm font-medium text-gray-700">Device Name</label>
                    <input type="text" id="editDeviceName" name="name" class="w-full mt-2 p-2 border rounded"
                        required>
                </div>

                <div class="mb-4">
                    <label for="editDeviceOrchard" class="block text-sm font-medium text-gray-700">Orchard</label>
                    <select id="editDeviceOrchard" name="orchard_id" class="w-full mt-2 p-2 border rounded" required>
                        <option value="1" id="orchardA">Orchard A</option>
                        <option value="2" id="orchardB">Orchard B</option>
                    </select>
                </div>


                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                        onclick="closeModal('editDeviceModal')">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Main Section -->
    <div class="py-12 flex justify-center">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg custom-shadow w-full max-w-2xl">
            <!-- Add New Device Button -->
            <div class="flex justify-end mb-4">
                <button class="px-4 py-2 bg-blue-500 text-black rounded hover:bg-blue-600"
                    onclick="openModal('addDeviceModal')">Add New Device</button>
            </div>

            <!-- Device List -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($devices as $device)
                    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-md flex flex-col items-center">
                        <h1 class="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">
                            {{ $device->name }}
                        </h1>
                        <!-- Device Information -->
                        <p class="text-gray-500 dark:text-gray-400 mb-4">
                            <!-- Access Orchard and Device attributes individually -->
                            Orchard Name: {{ $device->orchard->orchardName ?? 'No Orchard' }}<br>
                        </p>

                        <!-- LED Toggle -->
                        <label for="ledToggle-{{ $device->id }}" class="flex items-center cursor-pointer">
                            <div class="relative">
                                <!-- LED Toggle Checkbox -->
                                <input type="checkbox" id="ledToggle-{{ $device->id }}" class="sr-only"
                                    data-device-id="{{ $device->id }}"
                                    onchange="toggleLed('{{ $device->id }}', this.checked)">
                                <div class="block bg-gray-300 w-14 h-8 rounded-full peer-checked:bg-green-500"></div>
                                <div
                                    class="top-1 left-1 bg-white w-6 h-6 rounded-full transition transform peer-checked:translate-x-6 shadow-md">
                                </div>
                            </div>
                            <!-- LED Status -->
                            <span id="ledStatus-{{ $device->id }}"
                                class="ml-3 text-gray-600 dark:text-gray-300">{{ $device->led_status ? 'ON' : 'OFF' }}</span>
                        </label>

                        <!-- Edit and Delete Buttons -->
                        <div class="flex mt-4 space-x-2">
                            <button
                                class="bg-blue-500 hover:bg-blue-600 text-black py-2 px-4 rounded focus:outline-none"
                                onclick="openEditModal('{{ $device->id }}')">Edit</button>

                            <form method="POST" action="{{ route('devices.destroy', $device->id) }}"
                                onsubmit="return confirm('Are you sure you want to delete this device?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-600 text-black py-2 px-4 rounded focus:outline-none">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    var durianData = @json($devices);
</script>
<script src="{{ asset('js/devices.js') }}"></script>


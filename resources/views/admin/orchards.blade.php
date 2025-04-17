<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Orchard Monitoring') }}
            </h2>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <!-- Add New Orchard Button -->
        <div class="flex justify-end mb-4">
            <button onclick="openModal('addOrchardModal')" class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">
                Add New Orchard
            </button>
        </div>

        <!-- Modal for Adding a New Orchard -->
        <div id="addOrchardModal"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg max-w-lg w-full">
                <!-- Close Button -->
                <button onclick="closeViewModal()"
                    class="absolute top-4 right-4 text-gray-600 hover:text-gray-800">
                    <span class="font-bold text-xl">&times;</span>
                </button>

                <h2 class="text-xl font-bold mb-6">Add New Orchard</h2>
                <form action="{{ route('orchards.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="orchardName" class="block text-gray-700 font-bold mb-1">Orchard Name</label>
                        <input type="text" name="orchardName" id="orchardName"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                    </div>

                    // In the Add New Orchard modal form
                    <div>
                        <label for="numTree" class="block text-gray-700 font-bold mb-1">Number of Trees</label>
                        <input type="number" name="numTree" id="numTree"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                    </div>

                    {{-- Add these new fields --}}
                    <div>
                        <label for="orchardSize" class="block text-gray-700 font-bold mb-1">Orchard Size (acres)</label>
                        <input type="number" step="0.1" name="orchardSize" id="orchardSize"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                    </div>

                    <div>
                        <label for="location" class="block text-gray-700 font-bold mb-1">Location</label>
                        <input type="text" name="location" id="location"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                    </div>

                    <div>
                        <label for="device_id" class="block text-gray-700 font-bold mb-1">Assign Device</label>
                        <select name="device_id" id="device_id"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            <option value="">-- Select Device --</option>
                            @foreach ($devices as $device)
                                <option value="{{ $device->id }}">{{ $device->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="durian_id" class="block text-gray-700 font-bold mb-1">Assign Durian</label>
                        <select name="durian_id" id="durian_id"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            <option value="">-- Select Durian --</option>
                            @foreach ($durians as $durian)
                                <option value="{{ $durian->id }}">{{ $durian->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeViewModal()"
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                            Save Orchard
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!--orchards list-->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-10px" style="width: fit-content; margin-left: 230px">
            @foreach ($orchards as $orchard)
                <div
                    class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-transform transform hover:scale-105">
                    <h2 class="text-lg font-bold text-gray-800">Orchard {{ $orchard->orchardName }}</h2>
                    <p class="text-gray-600">
                        Device Name:
                        <span class="font-bold">{{ $orchard->device->name ?? 'No Device Assigned' }}</span>
                    </p>
                    <p class="text-gray-600">
                        Total Durian Fall:
                        <span id="vibration-count-sensor-{{ $orchard->id }}" class="font-bold">Loading...</span>
                    </p>

                    <div class="flex space-x-4 mt-4">
                        <button
                            onclick="saveVibrationCount('{{ $orchard->id }}', document.getElementById('vibration-count-sensor-{{ $orchard->id }}').innerText)"
                            class="bg-blue-500 text-black px-4 py-2 rounded hover:bg-blue-700">
                            Collect & Reset
                        </button>
                        <button onclick="openViewModal({{ $orchard->id }})"
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">
                            View Orchard Details
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg max-w-lg w-full relative">
            <button onclick="closeViewModal()" class="absolute top-4 right-4 text-gray-600 hover:text-gray-800">
                <span class="font-bold text-xl">&times;</span>
            </button>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <h3 class="font-bold text-lg mb-4">Orchard Monitoring</h3>
                    <img id="orchardImage" class="w-full h-auto" alt="Orchard Image">
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">Orchard Info</h3>
                    <p><strong>Orchard Name:</strong> <span id="nameOrchard"></span></p>
                    <p><strong>Number of Trees:</strong> <span id="numTrees"></span></p>
                    <p><strong>Device Name:</strong> <span id="deviceName"></span></p>
                    <p><strong>Durian Type:</strong> <span id="durianName"></span></p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script src="{{ asset('js/orchard.js') }}"></script>
<script>
    var orchards = @json($orchards);
</script>


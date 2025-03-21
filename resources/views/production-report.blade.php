<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Production Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4">
                    <form method="GET" action="{{ route('production-report') }}"
                        class="flex flex-wrap gap-4 items-center">
                        <!-- In your filter form -->
                        <select name="orchard" class="border rounded p-2">
                            <option value="">All Devices</option>
                            @foreach ($vibrationLogs->unique('device_id') as $log)
                                <option value="{{ $log->device_id }}"
                                    {{ request('orchard') == $log->device_id ? 'selected' : '' }}>
                                    Device {{ $log->device_id }}
                                </option>
                            @endforeach
                        </select>
                        <select name="durian_type" class="border rounded p-2">
                            <option value="">All Durian Types</option>
                            @foreach ($durianTypes as $type)
                                <option value="{{ $type }}"
                                    {{ request('durian_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="date" class="border rounded p-2" value="{{ request('date') }}">
                        <button type="submit" class="bg-blue-500 text-black rounded p-2">Filter</button>
                    </form>
                </div>

                <div class="mb-6">
                    <button onclick="showTable('recordFall')" class="bg-blue-500 text-black rounded p-2">Record
                        Fall</button>
                    <button onclick="showTable('harvestReport')" class="bg-green-500 text-black rounded p-2">Harvest
                        Report</button>
                    <button onclick="showTable('inventoryReport')"
                        class="bg-yellow-500 text-black rounded p-2">Inventory Report</button>
                </div>

                <!-- Record Fall Section with Chart -->
                <div id="recordFall" class="table-container">
                    <h3 class="text-lg font-bold mb-4">Record Fall</h3>

                    <!-- Add Chart Canvas -->
                    <div class="chart-container" style="overflow-x: auto; position: relative;">
                        <canvas id="fallChart" style="min-width: 700px; width: 100%; height: 400px;"></canvas>
                    </div>

                    <table class="w-full border-collapse border border-gray-300 mb-8">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border p-2">No.</th>
                                <th class="border p-2">Device ID</th>
                                <th class="border p-2">Vibration Count</th>
                                <th class="border p-2">Log Type</th>
                                <th class="border p-2">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vibrationLogs as $index => $log)
                                <tr>
                                    <td class="border p-2">{{ $index + 1 }}</td>
                                    <td class="border p-2">{{ $log->device_id }}</td>
                                    <td class="border p-2">{{ $log->vibration_count }}</td>
                                    <td class="border p-2">{{ $log->log_type == 1 ? 'Fall' : 'Other' }}</td>
                                    <td class="border p-2">{{ $log->timestamp->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="border p-2 text-center">No Vibration Data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Harvest Report Section -->
                <div id="harvestReport" class="table-container hidden">
                    <h3 class="text-lg font-bold mb-4">Harvest Report</h3>
                    <div class="chart-container" style="overflow-x: auto; position: relative; max-height: 300px;">
                        <canvas id="harvestChart" style="min-width: 700px; width: 100%; max-height: 300px;"></canvas>
                    </div>
                    <table class="w-full border-collapse border border-gray-300 mb-8">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border p-2">No.</th>
                                <th class="border p-2">Orchard</th>
                                <th class="border p-2">Durian Type</th>
                                <th class="border p-2">Harvest Date</th>
                                <th class="border p-2">Total Harvested</th>
                                <th class="border p-2">Status</th>
                                <th class="border p-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($harvestReports as $index => $report)
                                <tr>
                                    <td class="border p-2">{{ $index + 1 }}</td>
                                    <td class="border p-2">{{ $report->orchard }}</td>
                                    <td class="border p-2">{{ $report->durian_type }}</td>
                                    <td class="border p-2">{{ $report->harvest_date->format('Y-m-d') }}</td>
                                    <td class="border p-2">{{ $report->total_harvested }}</td>
                                    <td class="border p-2">{{ $report->status }}</td>
                                    <td class="border p-2"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#harvestModal">
                                        Document
                                    </button></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="border p-2 text-center">No Harvest Report Data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- 🔹 Modal for Document Harvest -->
                <div x-data="{ showModal: false, harvestId: '', harvestDate: '', orchard: '', durianType: '', totalHarvest: '' }">
                    <!-- Modal Background -->
                    <div x-show="showModal"
                        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                            <h2 class="text-xl font-bold mb-4">Harvest Record</h2>

                            <!-- Form -->
                            <form method="POST" action="{{ route('harvest.save') }}">
                                @csrf
                                <input type="hidden" name="harvest_id" x-model="harvestId">

                                <div class="mb-4">
                                    <label class="block text-sm font-medium">Harvest Date</label>
                                    <input type="date" name="harvest_date" x-model="harvestDate"
                                        class="w-full border p-2 rounded">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium">Location</label>
                                    <input type="text" name="location" x-model="orchard"
                                        class="w-full border p-2 rounded">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium">Crop (Durian Type)</label>
                                    <input type="text" name="durian_type" x-model="durianType"
                                        class="w-full border p-2 rounded">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium">Quantity</label>
                                    <input type="number" name="quantity" x-model="totalHarvest"
                                        class="w-full border p-2 rounded">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium">Where It’s Stored</label>
                                    <input type="text" name="storage_location" class="w-full border p-2 rounded"
                                        required>
                                </div>

                                <!-- Buttons -->
                                <div class="flex justify-end space-x-2">
                                    <button type="button" @click="showModal = false"
                                        class="px-4 py-2 bg-gray-500 text-black rounded">Cancel</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-500 text-black rounded">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Inventory Report Section -->
                <div id="inventoryReport" class="table-container hidden">
                    <h3 class="text-lg font-bold mb-4">Inventory Report</h3>
                    <table class="w-full border-collapse border border-gray-300 mb-8">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border p-2">Storage</th>
                                <th class="border p-2">Durian Type</th>
                                <th class="border p-2">Quantity</th>
                                <th class="border p-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventoryReports as $report)
                                <tr>
                                    <td class="border p-2">{{ $report->storage }}</td>
                                    <td class="border p-2">{{ $report->durian_type }}</td>
                                    <td class="border p-2">{{ $report->quantity }}</td>
                                    <td class="border p-2">{{ $report->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="border p-2 text-center">No Inventory Report Data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1"></script>
    <script>
        var chartData = @json($chartData);
        var harvestReports = @json($harvestReports);
    </script>
    <script src="{{ asset('js/production.js') }}"></script>
</x-app-layout>

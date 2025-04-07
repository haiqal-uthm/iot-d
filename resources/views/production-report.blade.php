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
                    <div x-data="{ showModal: false, selectedReport: null }">
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
                                        <td class="border p-2">
                                            <button type="button" class="btn btn-primary"
                                                @click="selectedReport = @js($report); showModal = true;">
                                                Document
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No Harvest Report Data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Modal Outside -->
                        <div x-show="showModal"
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                            <div class="bg-white p-6 rounded-lg shadow-lg text-sm w-full max-w-2xl mx-auto">

                                <button @click="showModal = false"
                                    class="absolute top-3 right-3 text-gray-500 hover:text-black text-xl">&times;</button>
                                <div id="printable">
                                    <h2 class="text-2xl font-bold mb-4 text-center">Durian Harvest Checklist Report</h2>

                                    <div class="mb-4">
                                        <p><strong>Harvest ID:</strong> <span x-text="selectedReport.id"></span></p>
                                        <p><strong>Date:</strong> <span x-text="selectedReport.harvest_date"></span></p>
                                        <p><strong>Orchard Location:</strong> <span
                                                x-text="selectedReport.orchard"></span></p>
                                        <p><strong>Durian Type:</strong> <span
                                                x-text="selectedReport.durian_type"></span></p>
                                        <p><strong>Total Harvested:</strong> <span
                                                x-text="selectedReport.total_harvested"></span> fruits</p>
                                        <p><strong>Storage Location:</strong> <span
                                                x-text="selectedReport.storage_location"></span></p>
                                        <p><strong>Status:</strong> <span x-text="selectedReport.status"></span></p>
                                    </div>

                                    <hr class="my-4">

                                    <div class="mb-4">
                                        <h3 class="text-lg font-semibold mb-2">Pre-Harvest Checklist</h3>
                                        <ul class="list-disc pl-6 space-y-1">
                                            <li>[ ] Tools sanitized and ready</li>
                                            <li>[ ] Protective gear worn</li>
                                            <li>[ ] Weather conditions checked</li>
                                            <li>[ ] Harvest route planned</li>
                                        </ul>
                                    </div>

                                    <div class="mb-4">
                                        <h3 class="text-lg font-semibold mb-2">During Harvest</h3>
                                        <ul class="list-disc pl-6 space-y-1">
                                            <li>[ ] Fruits carefully collected</li>
                                            <li>[ ] No damage on fruit observed</li>
                                            <li>[ ] Counted and placed in baskets</li>
                                        </ul>
                                    </div>

                                    <div class="mb-4">
                                        <h3 class="text-lg font-semibold mb-2">Post-Harvest</h3>
                                        <ul class="list-disc pl-6 space-y-1">
                                            <li>[ ] All fruits stored in cool place</li>
                                            <li>[ ] Storage tagged with harvest date</li>
                                            <li>[ ] Tools cleaned and stored</li>
                                        </ul>
                                    </div>

                                    <div class="mt-6 flex justify-between">
                                        <div>
                                            <p class="mb-1 font-semibold">Harvested By:</p>
                                            <p>_______________________</p>
                                        </div>
                                        <div>
                                            <p class="mb-1 font-semibold">Verified By:</p>
                                            <p>_______________________</p>
                                        </div>
                                    </div>
                                </div>
                                <button onclick="printReport()"
                                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                    🖨️ Print Report
                                </button>
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
                                            <td colspan="4" class="border p-2 text-center">No Inventory Report Data
                                            </td>
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
            <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</x-app-layout>

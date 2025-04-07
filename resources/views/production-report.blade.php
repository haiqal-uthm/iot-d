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
                <!-- Harvest Report Section -->
                <div id="harvestReport" class="table-container hidden">
                    <h3 class="text-lg font-bold mb-4">Harvest Report</h3>
                    <div class="chart-container" style="overflow-x: auto; position: relative; max-height: 300px;">
                        <canvas id="harvestChart" style="min-width: 700px; width: 100%; max-height: 300px;"></canvas>
                    </div>

                    <div x-data="{
                        showModal: false,
                        selectedReport: null,
                        showUploadModal: false,
                        selectedReportID: null
                    }">
                        <!-- Harvest Reports Table -->
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
                                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                                <!-- Action Dropdown -->
                                                <button @click="open = !open"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                    Action
                                                </button>

                                                <!-- Dropdown Menu -->
                                                <div x-show="open" @click.away="open = false"
                                                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                                    <div class="py-1">
                                                        <button type="button"
                                                            @click="selectedReport = @js($report); showModal = true; open = false"
                                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                            View Document
                                                        </button>
                                                        <button type="button" @click="window.print(); open = false"
                                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                            Print
                                                        </button>
                                                        <button type="button"
                                                            @click="selectedReportID = {{ $report->id }}; showUploadModal = true; open = false"
                                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                            Upload File
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No Harvest Report Data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- View Report Modal -->
                        <div x-show="showModal"
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
                            <div class="bg-white p-6 rounded-lg shadow-lg text-sm w-full max-w-2xl mx-auto relative">
                                <button @click="showModal = false"
                                    class="absolute top-3 right-3 text-gray-500 hover:text-black text-xl">&times;</button>
                                <div id="printable">
                                    <!-- Printable Content -->
                                    <h2 class="text-2xl font-bold mb-4 text-center">Durian Harvest Checklist Report
                                    </h2>
                                    <div class="mb-4">
                                        <p><strong>Harvest ID:</strong> <span x-text="selectedReport.id"></span></p>
                                        <p><strong>Date:</strong> <span x-text="selectedReport.harvest_date"></span>
                                        </p>
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

                                    <!-- Checklist Sections -->
                                    <hr class="my-4">
                                    <div class="mb-4">
                                        <h3 class="text-lg font-semibold mb-2">Pre-Harvest Checklist</h3>
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600" id="tools">
                                                <label for="tools" class="ml-2">Tools sanitized and ready</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600" id="gear">
                                                <label for="gear" class="ml-2">Protective gear worn</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600" id="weather">
                                                <label for="weather" class="ml-2">Weather conditions checked</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600" id="route">
                                                <label for="route" class="ml-2">Harvest route planned</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ... rest of checklist content ... -->

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
                                <button @click="printReport()"
                                    class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                    🖨️ Print Report
                                </button>
                            </div>
                        </div>

                        <!-- Upload File Modal -->
                        <div x-show="showUploadModal"
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
                            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
                                <button @click="showUploadModal = false"
                                    class="absolute top-2 right-3 text-xl text-gray-600 hover:text-black">&times;</button>
                                <form method="POST" action="{{ route('production.upload') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="harvest_report_id" x-bind:value="selectedReportID">

                                    <div class="mb-4">
                                        <label for="harvest_document" class="block text-sm font-medium mb-1">
                                            Upload Harvest Document
                                        </label>
                                        <input type="file" name="harvest_document" id="harvest_document"
                                            class="w-full border p-2 rounded" required>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit"
                                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                            Upload
                                        </button>
                                    </div>
                                </form>
                            </div>
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
    <script src="//unpkg.com/alpinejs" defer></script>
</x-app-layout>

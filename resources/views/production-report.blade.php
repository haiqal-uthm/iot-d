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
                    <!-- Record Fall Filter -->
                    <form method="GET" action="{{ route('production-report') }}" 
                        class="flex flex-wrap gap-4 items-center mb-4" 
                        id="recordFallFilter">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" name="search" placeholder="Search..." 
                                class="w-full border rounded p-2" value="{{ request('search') }}">
                        </div>
                        <select name="device_id" class="border rounded p-2">
                            <option value="">All Devices</option>
                            @foreach ($vibrationLogs->unique('device_id') as $log)
                                <option value="{{ $log->device_id }}"
                                    {{ request('device_id') == $log->device_id ? 'selected' : '' }}>
                                    Device {{ $log->device_id }}
                                </option>
                            @endforeach
                        </select>
                        <select name="log_type" class="border rounded p-2">
                            <option value="">All Log Types</option>
                            <option value="1" {{ request('log_type') == '1' ? 'selected' : '' }}>Fall</option>
                            <option value="0" {{ request('log_type') == '0' ? 'selected' : '' }}>Other</option>
                        </select>
                        <input type="date" name="date" class="border rounded p-2" value="{{ request('date') }}">
                        <button type="submit" class="bg-blue-500 text-black rounded p-2">Filter</button>
                    </form>

                    <!-- Harvest Report Filter -->
                    <form method="GET" action="{{ route('production-report') }}" 
                        class="flex flex-wrap gap-4 items-center mb-4 hidden" 
                        id="harvestReportFilter">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" name="search" placeholder="Search..." 
                                class="w-full border rounded p-2" value="{{ request('search') }}">
                        </div>
                        <select name="orchard" class="border rounded p-2">
                            <option value="">All Orchards</option>
                            @foreach ($harvestReports->unique('orchard') as $report)
                                <option value="{{ $report->orchard }}"
                                    {{ request('orchard') == $report->orchard ? 'selected' : '' }}>
                                    {{ $report->orchard }}
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

                    <div x-data="{
                        showModal: false,
                        selectedReport: null
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
                                            <!-- Action Dropdown -->
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
                                                        <button type="button" @click="printReport(); open = false"
                                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                            Print
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
                                <button @click="showModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-black text-xl">&times;</button>
                                <form @submit.prevent="saveHarvestDetails()" id="harvestDetailsForm" x-data="{ isSubmitting: false }">
                                    <div id="printable">
                                        <!-- Basic Info (unchanged) -->
                                        <h2 class="text-2xl font-bold mb-4 text-center">Durian Harvest Checklist Report</h2>
                                        <div class="mb-4">
                                            <input type="hidden" x-model="selectedReport.id" name="harvest_id">
                                            <p><strong>Harvest ID:</strong> <span x-text="selectedReport.id"></span></p>
                                            <p><strong>Date:</strong> <span x-text="selectedReport.harvest_date"></span>
                                            </p>
                                            <p><strong>Orchard Location:</strong> <span
                                                    x-text="selectedReport.orchard"></span></p>
                                            <p><strong>Durian Type:</strong> <span
                                                    x-text="selectedReport.durian_type"></span></p>
                                            <p><strong>Total Harvested:</strong> <span
                                                    x-text="selectedReport.total_harvested"></span> fruits</p>
                                            <p><strong>Status:</strong> <span x-text="selectedReport.status"></span></p>
                                        </div>
                                    
                                        <!-- Checklist Sections -->
                                        <hr class="my-4">
                                        <div class="mb-4">
                                            <h3 class="text-lg font-semibold mb-2">Durian Details</h3>
                                            <div class="space-y-4">
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label for="estimated_weight" class="block text-sm font-medium mb-1">Estimated Weight (kg)</label>
                                                        <input type="number" id="estimated_weight" name="estimated_weight" 
                                                            class="w-full border rounded p-2" step="0.01" required
                                                            x-bind:value="selectedReport.estimated_weight">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium mb-2">Grade</label>
                                                        <div class="grid grid-cols-3 gap-4">
                                                            <div class="flex items-center">
                                                                <input type="checkbox" id="grade_a" name="grade[]" value="A" 
                                                                    class="form-checkbox h-4 w-4 text-blue-600"
                                                                    x-bind:checked="selectedReport.grade && selectedReport.grade.includes('A')">
                                                                <label for="grade_a" class="ml-2">Grade A</label>
                                                            </div>
                                                            <div class="flex items-center">
                                                                <input type="checkbox" id="grade_b" name="grade[]" value="B" 
                                                                    class="form-checkbox h-4 w-4 text-blue-600"
                                                                    x-bind:checked="selectedReport.grade && selectedReport.grade.includes('B')">
                                                                <label for="grade_b" class="ml-2">Grade B</label>
                                                            </div>
                                                            <div class="flex items-center">
                                                                <input type="checkbox" id="grade_c" name="grade[]" value="C" 
                                                                    class="form-checkbox h-4 w-4 text-blue-600"
                                                                    x-bind:checked="selectedReport.grade && selectedReport.grade.includes('C')">
                                                                <label for="grade_c" class="ml-2">Grade C</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label for="condition" class="block text-sm font-medium mb-1">Condition</label>
                                                    <div class="grid grid-cols-3 gap-4">
                                                        <div class="flex items-center">
                                                            <input type="checkbox" id="condition_excellent" name="condition[]" value="excellent" 
                                                                class="form-checkbox h-4 w-4 text-blue-600"
                                                                x-bind:checked="selectedReport.condition && selectedReport.condition.includes('excellent')">
                                                            <label for="condition_excellent" class="ml-2">Excellent</label>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <input type="checkbox" id="condition_good" name="condition[]" value="good" 
                                                                class="form-checkbox h-4 w-4 text-blue-600"
                                                                x-bind:checked="selectedReport.condition && selectedReport.condition.includes('good')">
                                                            <label for="condition_good" class="ml-2">Good</label>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <input type="checkbox" id="condition_damaged" name="condition[]" value="damaged" 
                                                                class="form-checkbox h-4 w-4 text-blue-600"
                                                                x-bind:checked="selectedReport.condition && selectedReport.condition.includes('damaged')">
                                                            <label for="condition_damaged" class="ml-2">Damaged</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <!-- Pre-Harvest Checklist section remains unchanged -->
                                    
                                        <!-- Post-Harvest Handling Section -->
                                        <hr class="my-4">
                                        <div class="mb-4">
                                            <h3 class="text-lg font-semibold mb-2">Post-Harvest Handling</h3>
                                            <div class="space-y-3">
                                                <div class="space-y-2">
                                                    <p class="font-medium">Storage Location:</p>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div class="flex items-center">
                                                            <input type="checkbox" id="storage_a" name="storage_location[]" value="A" 
                                                                class="form-checkbox h-4 w-4 text-blue-600"
                                                                x-bind:checked="selectedReport.storage_location && selectedReport.storage_location.includes('A')">
                                                            <label for="storage_a" class="ml-2">Storage A</label>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <input type="checkbox" id="storage_b" name="storage_location[]" value="B" class="form-checkbox h-4 w-4 text-blue-600">
                                                            <label for="storage_b" class="ml-2">Storage B</label>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <input type="checkbox" id="storage_c" name="storage_location[]" value="C" class="form-checkbox h-4 w-4 text-blue-600">
                                                            <label for="storage_c" class="ml-2">Storage C</label>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <input type="checkbox" id="storage_d" name="storage_location[]" value="D" class="form-checkbox h-4 w-4 text-blue-600">
                                                            <label for="storage_d" class="ml-2">Storage D</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label for="remarks" class="block text-sm font-medium mb-1">Remarks</label>
                                                    <textarea id="remarks" name="remarks" rows="3" class="w-full border rounded p-2" 
                                                        class="w-full border rounded p-2" 
                                                        placeholder="Enter any additional notes or observations..."
                                                        x-text="selectedReport.remarks"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <div class="mt-6 flex justify-between">
                                            <div class="w-1/2">
                                                <p class="mb-1 font-semibold">Harvested By:</p>
                                                <p>_______________________</p>
                                            </div>
                                            <div class="w-1/2 text-right">
                                                <p class="mb-1 font-semibold">Verified By:</p>
                                                <p>_______________________</p>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" 
                                            :disabled="isSubmitting"
                                            class="bg-green-600 text-black px-4 py-2 rounded hover:bg-green-700">
                                        Save Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Upload File Modal -->
                      
                <!-- Inventory Report Section -->
                <div id="inventoryReport" class="table-container hidden">
                    <h3 class="text-lg font-bold mb-4">Inventory Report</h3>
                    <table class="w-full border-collapse border border-gray-300 mb-8">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border p-2">Storage</th>
                                <th class="border p-2">Durian Type</th>
                                <th class="border p-2">Quantity</th>
                                <th class="border p-2">Date Added</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($storageReports as $report)
                                <tr>
                                    <td class="border p-2">Storage {{ $report->storage_location }}</td>
                                    <td class="border p-2">{{ $report->durian_type }}</td>
                                    <td class="border p-2">{{ $report->quantity }}</td>
                                    <td class="border p-2">{{ $report->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="border p-2 text-center">No Storage Data</td>
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

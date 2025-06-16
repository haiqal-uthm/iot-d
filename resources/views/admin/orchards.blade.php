<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Orchard Monitoring') }}
            </h2>
        </div>
    </x-slot>


    <div class="container mx-auto px-4 pt-6">
        <a href="{{ route('admin.orchards.create') }}" class="add-orchard-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                    clip-rule="evenodd" />
            </svg>
            Add New Orchard
        </a>
        <div class="dashboard-summary mb-8">
            <div class="summary-card">
                <div class="summary-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="summary-title">Total Orchards</div>
                <div class="summary-value">{{ count($orchards) }}</div>
            </div>

            <div class="summary-card">
                <div class="summary-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <div class="summary-title">Active Devices</div>
                <div class="summary-value">{{ $devices->where('status', 'active')->count() }}</div>
            </div>

            <div class="summary-card">
                <div class="summary-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="summary-title">Total Durian Falls</div>
                <div class="summary-value">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400" id="total-durian-falls">
                        <div class="loading-indicator"></div> Loading...
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div class="summary-title">Durian Varieties</div>
                <div class="summary-value">{{ $durians->count() }}</div>
            </div>
        </div>

        <!-- Orchards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($orchards as $orchard)
                <div class="orchard-card">
                    <div class="orchard-card-header">
                        <h2 class="text-lg font-bold text-gray-800">Orchard {{ $orchard->orchardName }}</h2>
                        <span class="badge {{ $orchard->device ? 'badge-success' : 'badge-warning' }}">
                            {{ $orchard->device ? 'Connected' : 'No Device' }}
                        </span>
                    </div>

                    <div class="orchard-card-body">
                        <div class="orchard-stats">
                            <div class="stat-item">
                                <div class="text-sm text-gray-500">Trees</div>
                                <div class="font-semibold">{{ $orchard->numTree }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="text-sm text-gray-500">Size</div>
                                <div class="font-semibold">{{ $orchard->orchardSize }} acres</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="text-sm text-gray-500 mb-1">Location</div>
                            <div class="font-medium">{{ $orchard->location }}</div>
                        </div>

                        <div class="mb-4">
                            <div class="text-sm text-gray-500 mb-1">Device</div>
                            <div class="font-medium">{{ $orchard->device->name ?? 'No Device Assigned' }}</div>
                        </div>

                        <div class="mb-4">
                            <div class="text-sm text-gray-500 mb-1">Durian Type</div>
                            <div class="font-medium">{{ $orchard->durian->name ?? 'Not Specified' }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500 mb-1">Total Durian Fall</div>
                            <div class="font-bold text-lg text-blue-600">
                                <span id="vibration-count-sensor-{{ $orchard->id }}">
                                    <div class="loading-indicator"></div> Loading...
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="orchard-card-footer">
                        <div class="flex space-x-2">
                            <button
                                onclick="resetVibrationCount('{{ $orchard->id }}')"
                                class="btn-primary flex-1 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Counter
                            </button>
                            <a href="{{ route('orchards.show', $orchard->id) }}"
                                class="btn-secondary flex-1 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Details
                            </a>
                            <form action="{{ route('orchards.destroy', $orchard->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete(event, '{{ $orchard->orchardName }}')" class="text-red-600 hover:text-red-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal for Adding a New Orchard -->
    <div id="addOrchardModal"
        class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="modal-content p-6 max-w-lg w-full">
            <!-- Close Button -->
            <button onclick="closeViewModal()" class="absolute top-4 right-4 text-gray-600 hover:text-gray-800">
                <span class="font-bold text-xl">&times;</span>
            </button>

            <h2 class="text-xl font-bold mb-6">Add New Orchard</h2>
            <form action="{{ route('orchards.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="orchardName" class="form-label">Orchard Name</label>
                    <input type="text" name="orchardName" id="orchardName" class="form-input" required>
                </div>

                <div>
                    <label for="numTree" class="form-label">Number of Trees</label>
                    <input type="number" name="numTree" id="numTree" class="form-input" required>
                </div>

                <div>
                    <label for="orchardSize" class="form-label">Orchard Size (acres)</label>
                    <input type="number" step="0.1" name="orchardSize" id="orchardSize" class="form-input"
                        required>
                </div>

                <div>
                    <label for="location" class="form-label">Location</label>
                    <input type="text" name="location" id="location" class="form-input" required>
                </div>

                <div>
                    <label for="device_id" class="form-label">Assign Device</label>
                    <select name="device_id" id="device_id" class="form-input">
                        <option value="">-- Select Device --</option>
                        @foreach ($devices as $device)
                            <option value="{{ $device->id }}">{{ $device->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="durian_id" class="form-label">Assign Durian</label>
                    <select name="durian_id" id="durian_id" class="form-input">
                        <option value="">-- Select Durian --</option>
                        @foreach ($durians as $durian)
                            <option value="{{ $durian->id }}">{{ $durian->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t">
                    <button type="button" onclick="closeViewModal()" class="btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        Save Orchard
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="modal-content p-6 max-w-lg w-full relative">
            <button onclick="closeViewModal()" class="absolute top-4 right-4 text-gray-600 hover:text-gray-800">
                <span class="font-bold text-xl">&times;</span>
            </button>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <h3 class="font-bold text-lg mb-4">Orchard Monitoring</h3>
                    <img id="orchardImage" class="w-full h-auto rounded-lg shadow-md" alt="Orchard Image">
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">Orchard Info</h3>
                    <div class="space-y-3">
                        <div class="bg-gray-50 p-3 rounded-md">
                            <p class="text-sm text-gray-500">Orchard Name</p>
                            <p class="font-medium" id="nameOrchard"></p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-md">
                            <p class="text-sm text-gray-500">Number of Trees</p>
                            <p class="font-medium" id="numTrees"></p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-md">
                            <p class="text-sm text-gray-500">Device Name</p>
                            <p class="font-medium" id="deviceName"></p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-md">
                            <p class="text-sm text-gray-500">Durian Type</p>
                            <p class="font-medium" id="durianName"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Response Message Modal -->
    <div id="responseModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6  max-w-md modal-content">
            <div class="text-center">
                <div id="responseIcon" class="mb-4"></div>
                <h3 id="responseTitle" class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2"></h3>
                <p id="responseMessage" class="text-gray-600 dark:text-gray-400 mb-6"></p>
                <button onclick="closeModal('responseModal')" class="btn-primary w-full">
                    Close
                </button>
            </div>
        </div>
    </div>
    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6  max-w-md modal-content">
            <div class="text-center">
                <h3 id="confirmModalTitle" class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2"></h3>
                <p id="confirmModalBody" class="text-gray-600 dark:text-gray-400 mb-6"></p>
                <div class="flex space-x-3">
                    <button onclick="closeModal('confirmModal')" class="btn-secondary flex-1">
                        Cancel
                    </button>
                    <button id="confirmModalYesBtn" class="btn-danger flex-1">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="{{ asset('js/orchard.js') }}"></script>
<script>
    // Initialize flash messages if they exist
    document.addEventListener('DOMContentLoaded', function() {
        // Check for flash messages from server-side redirects
        @if (session('success'))
            showSuccessModal("{{ session('success') }}");
        @endif

        @if (session('error'))
            showErrorModal("{{ session('error') }}");
        @endif

        @if (session('warning'))
            showWarningModal("{{ session('warning') }}");
        @endif

        @if (session('info'))
            showInfoModal("{{ session('info') }}");
        @endif

        // Add event listeners for delete buttons
        document.querySelectorAll('.delete-orchard-btn').forEach(button => {
            button.addEventListener('click', function() {
                const orchardId = this.getAttribute('data-orchard-id');
                const orchardName = this.getAttribute('data-orchard-name');

                showConfirmModal(
                    `Are you sure you want to delete "${orchardName}"?`,
                    function() {
                        document.getElementById(`delete-form-${orchardId}`).submit();
                    },
                    null,
                    'Delete Confirmation'
                );
            });
        });
    });
</script>
<link rel="stylesheet" href="{{ asset('css/admin-orchard.css') }}">
<script>
    var orchards = @json($orchards);

    // Update total durian falls without page reload
    document.addEventListener('DOMContentLoaded', function() {
        const database = initializeFirebase();

        // Set up a listener for total durian falls
        const totalFallsElement = document.getElementById('total-durian-falls');

        // Update total falls every 5 seconds without page reload
        setInterval(function() {
            let totalFalls = 0;
            const fallElements = document.querySelectorAll('[id^="vibration-count-sensor-"]');
            fallElements.forEach(el => {
                const count = parseInt(el.innerText);
                if (!isNaN(count)) {
                    totalFalls += count;
                }
            });
            if (totalFallsElement) {
                totalFallsElement.innerText = totalFalls;
            }
        }, 5000);
    });
</script>

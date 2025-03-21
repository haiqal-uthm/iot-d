<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Main Layout -->
                <div class="grid grid-cols-2 lg:grid-cols-2 gap-6">
                    <!-- Overview Box -->
                    <div
                        class="col-span-2 bg-gradient-to-r from-purple-500 to-indigo-500 p-6 rounded-lg text-black custom-shadow">
                        <h3 class="text-lg font-bold text-center">Durian Production Breakdown</h3>
                        <div style="max-width: 250px; margin: auto;">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>

                    <!-- Daily Logging Boxes -->
                    <div>
                        <div
                            class="bg-gradient-to-r from-pink-500 to-red-500 p-6 rounded-lg text-black custom-shadow mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-bold">Current Weather</h3>
                                <a href="{{ route('weather') }}" class="arrow-icon">➔</a>
                            </div>
                            <div class="text-center">
                                <div id="weather-info">Loading weather...</div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-pink-500 to-red-500 p-6 rounded-lg text-black custom-shadow">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold">Durian Fall Count</h3>
                                <a href="{{ route('durian') }}" class="arrow-icon">➔</a>
                            </div>
                            <div class="text-center mt-2">
                                <table style="width: 100%; text-align: center;">
                                    <tr>
                                        <td>
                                            <!-- Tilt Sensor -->
                                            <img style="width: 50px; display: block; margin: auto;" src="{{ asset('images/durian.png') }}" alt="Tilt Sensor Icon">
                                            <p id="vibration-count">Loading...</p>
                                        </td>
                                        <td>
                                            <!-- Camera AI -->
                                            <img style="width: 50px; display: block; margin: auto;" src="{{ asset('images/cctv-camera.png') }}" alt="Camera AI Icon">
                                            <p id="detectionCounts">Loading...</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <p>Total Confirmed Durian: </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lower Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                    <!-- Orchard Monitoring Box -->
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg custom-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-bold">Orchard Monitoring</h3>
                            <a href="{{ route('orchards') }}" class="arrow-icon">➔</a>
                        </div>
                        <div class="text-center mt-2">
                            <img style="width: 50px; margin-left: auto; margin-right: auto; display: block;"
                                src="{{ asset('images/orchard.png') }}" alt="IoT Icon" class="w-8 h-8 mr-2">
                            <p>Orchards: {{ $totalOrchards }}</p>
                        </div>
                    </div>
                    <!-- Device Controller Box -->
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg custom-shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold">Device Management</h3>
                            <a href="{{ route('devices') }}" class="arrow-icon">➔</a>
                        </div>
                        <div class="text-center mt-2">
                            <img style="width: 50px; margin-left: auto; margin-right: auto; display: block;"
                                src="{{ asset('images/iot.png') }}" alt="IoT Icon" class="w-8 h-8 mr-2">
                            <p>Devices: {{ $totalDevice }}</p>
                        </div>
                    </div>
                    <!-- Harvest Production Box -->
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg custom-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-bold">Production Report</h3>
                            <a href="{{ route('production-report') }}" class="arrow-icon">➔</a>
                        </div>
                        <div class="text-center">
                            <canvas id="durianChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="notification-box bg-white dark:bg-gray-800 p-4 rounded-lg custom-shadow mt-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">Notifications</h3>
                        <span class="arrow-icon">➔</span>
                    </div>
                    <ul class="notification-list mt-4">
                        @if($logs->isNotEmpty())
                            @foreach($logs as $log)
                                <li class="notification-item mt-2">
                                    <span class="status-dot {{ $log->log_type == 1 ? 'green' : 'red' }}"></span>
                                    <div>
                                        <p class="text-sm">Device: {{ $log->device_id }} | Durian Fall</p>
                                        <p class="timestamp">{{ $log->timestamp }}</p>
                                    </div>
                                </li>
                            @endforeach
                        @else
                            <li class="text-sm text-gray-500">No Vibration Logs Found</li>
                        @endif
                    </ul>
                </div>                                                         
            </div>
        </div>
    </div>
</x-app-layout>
<script src="/js/dashboard.js?v=<?= time(); ?>"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js"></script>
<script>
    var durianData = @json($durianData);
    var weatherRoute = "{{ route('weather.current') }}";
</script>





<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <!-- Back Arrow Icon -->
            <a href="{{ route('dashboard') }}" class="text-gray-800 dark:text-gray-200 mr-4 text-lg font-bold">
                ←
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Weather') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 flex justify-center">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg custom-shadow w-full max-w-2xl">
            <h1 class="text-center text-lg font-semibold text-gray-800 dark:text-gray-200">Pahang, Raub</h1>
            <!-- Weather Info Section -->
            <div id="weather-info" class="text-center">
                <!-- Weather info will be dynamically injected here by JS -->
            </div>

            <!-- Additional Weather Details -->
            <div class="bg-gray-100 dark:bg-gray-900 p-6 rounded-lg shadow-lg dark:shadow-black-900/50">
                <div class="text-center">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Wind</p>
                        <p id="wind-speed" class="font-bold text-gray-900 dark:text-white">N/A</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Pressure</p>
                        <p id="pressure" class="font-bold text-gray-900 dark:text-white">N/A</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Humidity</p>
                        <p id="humidity" class="font-bold text-gray-900 dark:text-white">N/A</p>
                    </div>
                </div>
            </div>

            <!-- Forecast Section -->
            <h3 class="text-xl font-bold text-center mt-6">3-Day Forecast</h3>
            <div id="forecast" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <!-- Forecast items will be inserted here -->
            </div>
        </div>
    </div>
</x-app-layout>
<script src="{{ asset('js/weather.js') }}"></script>

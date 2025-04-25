<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Notification Tabs -->
            <div class="mb-4">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex -mb-px">
                        <button id="all-tab" class="tab-button px-6 py-3 border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400 font-medium text-sm">
                            All Notifications
                        </button>
                        <button id="durian-tab" class="tab-button px-6 py-3 border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 font-medium text-sm">
                            Durian Falls
                        </button>
                        <button id="system-tab" class="tab-button px-6 py-3 border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 font-medium text-sm">
                            Animal Threat Alerts
                        </button>
                        <button id="other-tab" class="tab-button px-6 py-3 border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 font-medium text-sm">
                            Other Alerts
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Notification Panels -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- All Notifications Panel -->
                    <div id="all-panel" class="notification-panel">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">All Recent Notifications</h3>
                        
                        @if(count($notifications['durian_falls']) + count($notifications['animal_threat_alerts']) + count($notifications['other_alerts']) > 0)
                            <div class="space-y-4">
                                @foreach($notifications['durian_falls'] as $log)
                                    <div class="p-4 border-l-4 border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-600 rounded-r-md">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                                    <strong>Durian Fall Alert:</strong> {{ $log->vibration_count }} durians fell at {{ $log->orchard->orchardName ?? 'Unknown Orchard' }} (Device ID: {{ $log->device_id }})
                                                </p>
                                                <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-300">
                                                    {{ $log->timestamp->diffForHumans() }} ({{ $log->timestamp->format('Y-m-d H:i:s') }})
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @foreach($notifications['animal_threat_alerts'] as $log)
                                    <div class="p-4 border-l-4 border-red-400 bg-red-50 dark:bg-red-900/20 dark:border-red-600 rounded-r-md">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-red-700 dark:text-red-200">
                                                    <strong>Animal Threat Alert:</strong> Device {{ $log->device_id }} at {{ $log->orchard->orchardName ?? 'Unknown Orchard' }} reported an issue (Code: {{ $log->vibration_count }})
                                                </p>
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-300">
                                                    {{ $log->timestamp->diffForHumans() }} ({{ $log->timestamp->format('Y-m-d H:i:s') }})
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @foreach($notifications['other_alerts'] as $log)
                                    <div class="p-4 border-l-4 border-blue-400 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-600 rounded-r-md">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-blue-700 dark:text-blue-200">
                                                    <strong>Notification:</strong> Device {{ $log->device_id }} at {{ $log->orchard->orchardName ?? 'Unknown Orchard' }} reported activity (Type: {{ $log->log_type }}, Count: {{ $log->vibration_count }})
                                                </p>
                                                <p class="mt-1 text-xs text-blue-600 dark:text-blue-300">
                                                    {{ $log->timestamp->diffForHumans() }} ({{ $log->timestamp->format('Y-m-d H:i:s') }})
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">No recent notifications</p>
                        @endif
                    </div>
                    
                    <!-- Durian Falls Panel -->
                    <div id="durian-panel" class="notification-panel hidden">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Durian Fall Alerts</h3>
                        
                        @if(count($notifications['durian_falls']) > 0)
                            <div class="space-y-4">
                                @foreach($notifications['durian_falls'] as $log)
                                    <div class="p-4 border-l-4 border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-600 rounded-r-md">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                                    <strong>Durian Fall Alert:</strong> {{ $log->vibration_count }} durians fell at {{ $log->orchard->orchardName ?? 'Unknown Orchard' }} (Device ID: {{ $log->device_id }})
                                                </p>
                                                <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-300">
                                                    {{ $log->timestamp->diffForHumans() }} ({{ $log->timestamp->format('Y-m-d H:i:s') }})
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">No durian fall alerts</p>
                        @endif
                    </div>
                    
                    <!-- Animal Threat Alerts Panel (formerly System Alerts) -->
                    <div id="system-panel" class="notification-panel hidden">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Animal Threat Alerts</h3>
                        
                        @if(count($notifications['animal_threat_alerts']) > 0)
                            <div class="space-y-4">
                                @foreach($notifications['animal_threat_alerts'] as $log)
                                    <div class="p-4 border-l-4 border-red-400 bg-red-50 dark:bg-red-900/20 dark:border-red-600 rounded-r-md">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-red-700 dark:text-red-200">
                                                    <strong>Animal Threat Alert:</strong> Device {{ $log->device_id }} at {{ $log->orchard->orchardName ?? 'Unknown Orchard' }} reported an issue (Code: {{ $log->vibration_count }})
                                                </p>
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-300">
                                                    {{ $log->timestamp->diffForHumans() }} ({{ $log->timestamp->format('Y-m-d H:i:s') }})
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">No animal threat alerts</p>
                        @endif
                    </div>
                    
                    <!-- Other Alerts Panel -->
                    <div id="other-panel" class="notification-panel hidden">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Other Alerts</h3>
                        
                        @if(count($notifications['other_alerts']) > 0)
                            <div class="space-y-4">
                                @foreach($notifications['other_alerts'] as $log)
                                    <div class="p-4 border-l-4 border-blue-400 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-600 rounded-r-md">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-blue-700 dark:text-blue-200">
                                                    <strong>Notification:</strong> Device {{ $log->device_id }} at {{ $log->orchard->orchardName ?? 'Unknown Orchard' }} reported activity (Type: {{ $log->log_type }}, Count: {{ $log->vibration_count }})
                                                </p>
                                                <p class="mt-1 text-xs text-blue-600 dark:text-blue-300">
                                                    {{ $log->timestamp->diffForHumans() }} ({{ $log->timestamp->format('Y-m-d H:i:s') }})
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">No other alerts</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-button');
            const panels = document.querySelectorAll('.notification-panel');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs
                    tabs.forEach(t => {
                        t.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                        t.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                    });
                    
                    // Add active class to clicked tab
                    tab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                    tab.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                    
                    // Hide all panels
                    panels.forEach(panel => {
                        panel.classList.add('hidden');
                    });
                    
                    // Show corresponding panel
                    const panelId = tab.id.replace('-tab', '-panel');
                    document.getElementById(panelId).classList.remove('hidden');
                });
            });
        });
    </script>
</x-app-layout>
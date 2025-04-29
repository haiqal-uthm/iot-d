<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <!-- Add our custom CSS and Font Awesome -->
    <link rel="stylesheet" href="{{ asset('css/manager/manager-notification.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Display any error messages -->
            @if(session('error'))
                <div class="mb-4 p-4 border-l-4 border-red-500 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Notification Tabs -->
            <div class="mb-4">
                <div class="tab-navigation">
                    <button id="all-tab" class="tab-button active">
                        <i class="fas fa-bell"></i> All Notifications
                    </button>
                    <button id="durian-tab" class="tab-button">
                        <i class="fas fa-fruit-alt"></i> Durian Falls
                    </button>
                    <button id="system-tab" class="tab-button">
                        <i class="fas fa-exclamation-triangle"></i> Animal Threat Alerts
                    </button>
                    <button id="other-tab" class="tab-button">
                        <i class="fas fa-info-circle"></i> Other Alerts
                    </button>
                </div>
            </div>

            <!-- Notification Panels -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 notification-container">
                    <!-- All Notifications Panel -->
                    <div id="all-panel" class="notification-panel">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">All Recent Notifications</h3>
                        
                        @if(count($notifications['durian_falls']) + count($notifications['animal_threat_alerts']) + count($notifications['other_alerts']) > 0)
                            <div class="space-y-4">
                                @foreach($notifications['durian_falls'] as $log)
                                    <div class="notification-card durian-notification">
                                        <div class="notification-icon">
                                            <i class="fas fa-fruit-alt"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">
                                                Durian Fall Alert
                                            </div>
                                            <div class="notification-message">
                                                <span class="notification-badge badge-yellow">{{ $log->vibration_count ?? 0 }} durians</span> fell at {{ $log->orchard->orchardName ?? 'Unknown Orchard' }}
                                            </div>
                                            <div class="notification-meta">
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-clock"></i> {{ $log->timestamp->diffForHumans() }}
                                                </div>
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-microchip"></i> Device ID: {{ $log->device_id ?? 'Unknown' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @foreach($notifications['animal_threat_alerts'] as $log)
                                    <div class="notification-card animal-notification">
                                        <div class="notification-icon">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">
                                                Animal Threat Alert
                                            </div>
                                            <div class="notification-message">
                                                Device {{ $log->device_id ?? 'Unknown' }} at {{ $log->orchard->orchardName ?? 'Unknown Orchard' }} reported an issue
                                            </div>
                                            <div class="notification-meta">
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-clock"></i> {{ $log->timestamp->diffForHumans() }}
                                                </div>
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-code"></i> Code: {{ $log->vibration_count ?? 0 }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @foreach($notifications['other_alerts'] as $log)
                                    <div class="notification-card other-notification">
                                        <div class="notification-icon">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">
                                                System Notification
                                            </div>
                                            <div class="notification-message">
                                                Device {{ $log->device_id ?? 'Unknown' }} at {{ $log->orchard->orchardName ?? 'Unknown Orchard' }} reported activity
                                            </div>
                                            <div class="notification-meta">
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-clock"></i> {{ $log->timestamp->diffForHumans() }}
                                                </div>
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-tag"></i> Type: {{ $log->log_type ?? 'Unknown' }}
                                                </div>
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-hashtag"></i> Count: {{ $log->vibration_count ?? 0 }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-bell-slash"></i>
                                <p>No recent notifications</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Durian Falls Panel -->
                    <div id="durian-panel" class="notification-panel hidden">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Durian Fall Alerts</h3>
                        
                        @if(count($notifications['durian_falls']) > 0)
                            <div class="space-y-4">
                                @foreach($notifications['durian_falls'] as $log)
                                    <div class="notification-card durian-notification">
                                        <div class="notification-icon">
                                            <i class="fas fa-fruit-alt"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">
                                                Durian Fall Alert
                                            </div>
                                            <div class="notification-message">
                                                <span class="notification-badge badge-yellow">{{ $log->fall_count }} durians</span> fell at {{ $log->orchard_name }}
                                            </div>
                                            <div class="notification-meta">
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-clock"></i> {{ $log->timestamp->diffForHumans() }}
                                                </div>
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-microchip"></i> Device: {{ $log->device->name ?? 'Unknown' }}
                                                </div>
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-farm"></i> Reported by: {{ $log->farm_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-fruit-alt"></i>
                                <p>No durian fall alerts</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Animal Threat Alerts Panel -->
                    <div id="system-panel" class="notification-panel hidden">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Animal Threat Alerts</h3>
                        
                        @if(count($notifications['animal_threat_alerts']) > 0)
                            <div class="space-y-4">
                                @foreach($notifications['animal_threat_alerts'] as $log)
                                    <div class="notification-card animal-notification">
                                        <div class="notification-icon">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">
                                                Animal Threat Alert
                                            </div>
                                            <div class="notification-message">
                                                Device {{ $log->device_id }} at {{ $log->orchard->orchardName ?? 'Unknown Orchard' }} reported an issue
                                            </div>
                                            <div class="notification-meta">
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-clock"></i> {{ $log->timestamp->diffForHumans() }}
                                                </div>
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-code"></i> Code: {{ $log->vibration_count }}
                                                </div>
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-calendar"></i> {{ $log->timestamp->format('Y-m-d H:i:s') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>No animal threat alerts</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Other Alerts Panel -->
                    <div id="other-panel" class="notification-panel hidden">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Other Alerts</h3>
                        
                        @if(count($notifications['other_alerts']) > 0)
                            <div class="space-y-4">
                                @foreach($notifications['other_alerts'] as $log)
                                    <div class="notification-card other-notification">
                                        <div class="notification-icon">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">
                                                System Notification
                                            </div>
                                            <div class="notification-message">
                                                Device {{ $log->device_id }} at {{ $log->orchard->orchardName ?? 'Unknown Orchard' }} reported activity
                                            </div>
                                            <div class="notification-meta">
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-clock"></i> {{ $log->timestamp->diffForHumans() }}
                                                </div>
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-tag"></i> Type: {{ $log->log_type }}
                                                </div>
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-hashtag"></i> Count: {{ $log->vibration_count }}
                                                </div>
                                                <div class="notification-meta-item">
                                                    <i class="fas fa-calendar"></i> {{ $log->timestamp->format('Y-m-d H:i:s') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-info-circle"></i>
                                <p>No other alerts</p>
                            </div>
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
                        t.classList.remove('active');
                    });
                    
                    // Add active class to clicked tab
                    tab.classList.add('active');
                    
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
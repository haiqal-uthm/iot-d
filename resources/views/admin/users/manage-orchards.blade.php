<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Orchard Management for ') }} {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 orchard-management-container">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Orchard Selection Card -->
                        <div class="lg:col-span-2">
                            <div class="orchard-selection-card">
                                <div class="orchard-card-header">
                                    <i class="fas fa-tree"></i>
                                    <h3 class="orchard-card-title">Assign Orchards</h3>
                                </div>
                                <div class="orchard-card-body">
                                    <form method="POST" action="{{ route('admin.users.update-orchards', $user) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="mb-6">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                                Select Orchards
                                            </label>
                                            <select name="orchards[]" multiple 
                                                class="orchards-select w-full rounded-md border-gray-300 shadow-sm"
                                                data-placeholder="Search and select orchards...">
                                                @foreach($orchards as $orchard)
                                                    <option value="{{ $orchard->id }}" 
                                                        {{ in_array($orchard->id, old('orchards', $selectedOrchards)) ? 'selected' : '' }}
                                                        data-size="{{ $orchard->orchardSize }}"
                                                        data-location="{{ $orchard->location }}">
                                                        {{ $orchard->orchardName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('orchards')
                                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Selected Orchards Info -->
                                        <div id="orchard-details" class="mt-6 space-y-4 hidden">
                                            <div class="flex items-center mb-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Selected Orchards Details</h4>
                                            </div>
                                            <div id="selected-orchards-info" class="space-y-3"></div>
                                        </div>

                                        <div class="orchard-action-buttons">
                                            <button type="submit" class="btn-save">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Save Assignments
                                            </button>
                                            <a href="{{ route('admin.users.index') }}" class="btn-cancel">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Cancel
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- User Summary Card -->
                        <div class="orchard-selection-card h-fit">
                            <div class="orchard-card-header">
                                <i class="fas fa-user"></i>
                                <h3 class="orchard-card-title">User Information</h3>
                            </div>
                            <div class="orchard-card-body">
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <span class="text-gray-600 dark:text-gray-400 w-24">Name:</span>
                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $user->name }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-gray-600 dark:text-gray-400 w-24">Email:</span>
                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $user->email }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-gray-600 dark:text-gray-400 w-24">Role:</span>
                                        <span class="px-2 py-1 rounded-full text-sm font-medium 
                                            {{ $user->role === 'admin' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                               ($user->role === 'manager' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-gray-600 dark:text-gray-400 w-24">Created:</span>
                                        <span class="font-medium text-gray-800 dark:text-gray-200">
                                            {{ $user->created_at->format('M j, Y') }}
                                        </span>
                                    </div>
                                    
                                    <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                                        <div class="text-gray-600 dark:text-gray-400 mb-2">Current Assignments:</div>
                                        <div id="current-orchards" class="flex flex-wrap gap-2">
                                            @if(count($selectedOrchards) > 0)
                                                @foreach($orchards->whereIn('id', $selectedOrchards) as $orchard)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                        {{ $orchard->orchardName }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-sm text-gray-500 dark:text-gray-400 italic">No orchards assigned</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect('.orchards-select', {
                plugins: ['remove_button', 'checkbox_options'],
                render: {
                    option: function(data, escape) {
                        return `<div class='flex justify-between items-center p-2'>
                                    <div>
                                        <span class='font-medium'>${escape(data.text)}</span>
                                        <div class='text-xs text-gray-500 mt-1'>
                                            <span class="inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                                                </svg>
                                                ${escape(data.size)}
                                            </span>
                                            <span class="inline-flex items-center ml-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                ${escape(data.location)}
                                            </span>
                                        </div>
                                    </div>
                                </div>`;
                    }
                }
            });

            // Update orchard details when selection changes
            document.querySelector('.orchards-select').addEventListener('change', function() {
                const selected = Array.from(this.selectedOptions);
                const infoContainer = document.getElementById('selected-orchards-info');
                
                infoContainer.innerHTML = selected.map(option => `
                    <div class="selected-orchard-card">
                        <h5 class="selected-orchard-title">
                            <i class="fas fa-tree"></i>
                            ${option.text}
                        </h5>
                        <div class="selected-orchard-info">
                            <div class="selected-orchard-info-item">
                                <span class="selected-orchard-info-label">Size:</span>
                                <span class="selected-orchard-info-value">${option.dataset.size}</span>
                            </div>
                            <div class="selected-orchard-info-item">
                                <span class="selected-orchard-info-label">Location:</span>
                                <span class="selected-orchard-info-value">${option.dataset.location}</span>
                            </div>
                        </div>
                    </div>
                `).join('');
                
                document.getElementById('orchard-details').classList.toggle('hidden', selected.length === 0);
                
                // Update the current orchards display in the user info card
                updateCurrentOrchards(selected);
            });
            
            // Function to update the current orchards display
            function updateCurrentOrchards(selectedOptions) {
                const currentOrchardsContainer = document.getElementById('current-orchards');
                
                if (selectedOptions.length > 0) {
                    currentOrchardsContainer.innerHTML = Array.from(selectedOptions).map(option => `
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            ${option.text}
                        </span>
                    `).join('');
                } else {
                    currentOrchardsContainer.innerHTML = '<span class="text-sm text-gray-500 dark:text-gray-400 italic">No orchards assigned</span>';
                }
            }
            
            // Initialize the display
            const initialSelected = Array.from(document.querySelector('.orchards-select').selectedOptions);
            if (initialSelected.length > 0) {
                document.querySelector('.orchards-select').dispatchEvent(new Event('change'));
            }
        });
    </script>
    @endpush
</x-app-layout>
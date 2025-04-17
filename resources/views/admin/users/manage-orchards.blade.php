<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Orchard Management for ') }} {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                ← Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Orchard Selection Card -->
                        <div class="lg:col-span-2">
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                                <form method="POST" action="{{ route('admin.users.update-orchards', $user) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                            Select Orchards (Searchable)
                                        </label>
                                        <select name="orchards[]" multiple 
                                            class="orchards-select w-full rounded-md border-gray-300 shadow-sm"
                                            data-placeholder="Search orchards...">
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
                                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Selected Orchards Details</h4>
                                        <div id="selected-orchards-info" class="space-y-3"></div>
                                    </div>

                                    <div class="flex items-center justify-between mt-8 border-t pt-6">
                                        <button type="submit" 
                                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                            Save Assignments
                                        </button>
                                        <a href="{{ route('admin.users.index') }}" 
                                            class="px-6 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                                            Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- User Summary Card -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6 h-fit">
                            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">User Information</h3>
                            <div class="space-y-2">
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
                                        {{ $user->role === 'admin' ? 'bg-green-100 text-green-800' : 
                                           ($user->role === 'manager' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-600 dark:text-gray-400 w-24">Created:</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">
                                        {{ $user->created_at->format('M j, Y') }}
                                    </span>
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
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        new TomSelect('.orchards-select', {
            plugins: ['remove_button', 'checkbox_options'],
            render: {
                option: function(data, escape) {
                    return `<div class='flex justify-between items-center p-2'>
                                <div>
                                    <span class='font-medium'>${escape(data.text)}</span>
                                    <div class='text-xs text-gray-500 mt-1'>
                                        ${escape(data.size)} • ${escape(data.location)}
                                    </div>
                                </div>
                                <div class='text-xs text-gray-500'>${escape(data.value)}</div>
                            </div>`;
                }
            }
        });

        // Update orchard details when selection changes
        document.querySelector('.orchards-select').addEventListener('change', function() {
            const selected = Array.from(this.selectedOptions);
            const infoContainer = document.getElementById('selected-orchards-info');
            
            infoContainer.innerHTML = selected.map(option => `
                <div class="bg-gray-50 dark:bg-gray-600 p-3 rounded-lg">
                    <h5 class="font-medium text-gray-800 dark:text-gray-200">${option.text}</h5>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        <p>Size: ${option.dataset.size}</p>
                        <p>Location: ${option.dataset.location}</p>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('orchard-details').classList.toggle('hidden', selected.length === 0);
        });
    </script>
    @endpush
</x-app-layout>
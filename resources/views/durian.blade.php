<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Durian') }}
            </h2>
        </div>
    </x-slot>

    <link rel="stylesheet" href="{{ asset('css/durian.css') }}">

    <!-- Add Durian Modal -->
    <div id="addDurianModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Add New Durian Type</h2>
            <form id="addDurianForm" method="POST" action="{{ route('durian.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Durian
                        Name</label>
                    <input type="text" name="name" id="name" class="w-full mt-2 p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="total"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total</label>
                    <input type="number" name="total" id="total" class="w-full mt-2 p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="editOrchard"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Orchard</label>
                    <select id="editOrchard" name="orchard_id" class="w-full mt-2 p-2 border rounded" required>
                        <option value="1">Orchard A</option>
                        <option value="2">Orchard B</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                        onclick="closeModal('addDurianModal')">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Durian Modal -->
    <div id="editDurianModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Edit Durian Type</h2>
            <form id="editDurianForm" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="editDurianId" name="id">
                <div class="mb-4">
                    <label for="editName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Durian
                        Name</label>
                    <input type="text" id="editName" name="name" class="w-full mt-2 p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="editTotal"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total</label>
                    <input type="number" id="editTotal" name="total" class="w-full mt-2 p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="editOrchard"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Orchard</label>
                    <select id="editOrchard" name="orchard_id" class="w-full mt-2 p-2 border rounded" required>
                        <option value="1">Orchard A</option>
                        <option value="2">Orchard B</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                        onclick="closeModal('editDurianModal')">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="py-12 flex justify-center">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow w-full max-w-2xl">
            <div class="flex justify-between items-center mb-4">
                <!-- Add New Durian Button -->
                <button class="px-4 py-2 bg-green-500 text-black rounded-lg hover:bg-green-600"
                    onclick="openModal('addDurianModal')">Add New Durian</button>
            </div>

            <!-- Durian List -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($durians as $durian)
                    <div class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-center">{{ $durian->name }}</h3>
                        <p class="text-center">Total Harvested: <span class="font-bold">{{ $durian->total }}</span></p>
                        <p class="text-center">Orchard:
                            <span class="font-bold">
                                {{ $durian->orchard_id == 1 ? 'A' : ($durian->orchard_id == 2 ? 'B' : 'N/A') }}
                            </span>
                        </p>
                        <!-- Edit and Delete Buttons -->
                        <div class="flex mt-4 space-x-2 justify-center">
                            <button class="bg-blue-500 hover:bg-blue-600 text-black py-2 px-4 rounded"
                                onclick="openEditModal('{{ $durian->id }}', '{{ $durian->name }}', {{ $durian->total }}, {{ $durian->orchard_id }})">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('durian.destroy', $durian->id) }}"
                                onsubmit="return confirm('Are you sure you want to delete this durian?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-600 text-black py-2 px-4 rounded">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center">No durians available.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
<script src="{{ asset('js/durian.js') }}"></script>

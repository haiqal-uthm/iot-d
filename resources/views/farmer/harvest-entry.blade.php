<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Harvest Entry') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('farmer.harvest.store') }}" method="POST">
                        @csrf

                        <!-- Orchard Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Assigned Orchard</label>
                            <select name="orchard_id" class="w-full border rounded p-2" required>
                                <option value="">Select Orchard</option>
                                @foreach(auth()->user()->farmer->orchards as $orchard)
                                <option value="{{ $orchard->id }}">{{ $orchard->orchardName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Durian Type -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Durian Type</label>
                            <select name="durian_type" class="w-full border rounded p-2" required>
                                <option value="">Select Durian Type</option>
                                @foreach($durianTypes as $durian)
                                <option value="{{ $durian->name }}">{{ $durian->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Harvest Date -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Harvest Date</label>
                            <input type="date" name="harvest_date" class="w-full border rounded p-2" required value="{{ old('harvest_date', date('Y-m-d')) }}">
                        </div>

                        <!-- Total Harvested -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Total Harvested</label>
                            <input type="number" name="total_harvested" class="w-full border rounded p-2" required min="1" value="{{ old('total_harvested', 1) }}">
                        </div>

                        <!-- Checklist Section -->
                        <div class="mb-6 border-t pt-4">
                            <h3 class="text-lg font-semibold mb-4">Harvest Checklist</h3>

                            <!-- Grade Selection -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Durian Grade</label>
                                <div class="grid grid-cols-3 gap-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="grade[]" value="A" class="mr-2">
                                        Grade A
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="grade[]" value="B" class="mr-2">
                                        Grade B
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="grade[]" value="C" class="mr-2">
                                        Grade C
                                    </label>
                                </div>
                            </div>

                            <!-- Condition Checklist -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Condition</label>
                                <div class="grid grid-cols-3 gap-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="condition[]" value="excellent" class="mr-2">
                                        Excellent
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="condition[]" value="good" class="mr-2">
                                        Good
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="condition[]" value="damaged" class="mr-2">
                                        Damaged
                                    </label>
                                </div>
                            </div>

                            <!-- Storage Selection -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Storage Location</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="storage[]" value="cold_storage" class="mr-2">
                                        Cold Storage
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="storage[]" value="warehouse" class="mr-2">
                                        Warehouse
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600">
                            Submit Harvest
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1"></script>
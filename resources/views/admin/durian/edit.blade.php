<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Durian') }}
            </h2>
        </div>
    </x-slot>

    <link rel="stylesheet" href="{{ asset('css/durian.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-durian.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6 flex items-center">
                        <a href="{{ route('admin.durian') }}" class="text-gray-500 hover:text-gray-700 mr-2">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h2 class="text-xl font-bold">Edit Durian: {{ $durian->name }}</h2>
                    </div>
                    
                    <form method="POST" action="{{ route('durian.update', $durian->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name" class="form-label">Durian Name</label>
                            <input type="text" name="name" id="name" class="form-input" value="{{ $durian->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="total" class="form-label">Total Harvested</label>
                            <input type="number" name="total" id="total" class="form-input" value="{{ $durian->total }}" required>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.durian') }}" class="btn bg-gray-500 text-white hover:bg-gray-600">
                                Cancel
                            </a>
                            <button type="submit" class="btn bg-blue-500 text-white hover:bg-blue-600">
                                Update Durian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
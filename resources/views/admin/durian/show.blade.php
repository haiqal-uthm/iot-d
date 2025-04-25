<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Durian Details') }}
            </h2>
        </div>
    </x-slot>

    <link rel="stylesheet" href="{{ asset('css/durian.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-durian.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6 flex items-center">
                        <a href="{{ route('admin.durian') }}" class="text-gray-500 hover:text-gray-700 mr-2">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h2 class="text-xl font-bold">{{ $durian->name }}</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h3 class="text-lg font-medium mb-2">Basic Information</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Name</p>
                                    <p class="font-medium">{{ $durian->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Total Harvested</p>
                                    <p class="font-medium">{{ $durian->total }}</p>
                                </div>
                            </div>
                        </div>
                        
                        @if($durian->orchards && $durian->orchards->count() > 0)
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h3 class="text-lg font-medium mb-2">Associated Orchards</h3>
                            <ul class="list-disc pl-5">
                                @foreach($durian->orchards as $orchard)
                                <li>{{ $orchard->orchardName }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        
                        <div class="flex justify-end space-x-2 mt-6">
                            <a href="{{ route('admin.durian.edit', $durian->id) }}" class="btn bg-blue-500 text-white hover:bg-blue-600">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('durian.destroy', $durian->id) }}"
                                onsubmit="return confirm('Are you sure you want to delete this durian?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn bg-red-500 text-white hover:bg-red-600">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
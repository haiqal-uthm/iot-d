<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Harvest Entry') }}
        </h2>
    </x-slot>

    <!-- Add CSS Link -->
    <link rel="stylesheet" href="{{ asset('css/farmer/farmer-entry.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <ul>
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="form-container">
                        <div class="form-header">
                            <h3><i class="fas fa-leaf mr-2"></i>New Harvest Record</h3>
                            <p>Enter the details of your durian harvest below</p>
                        </div>

                        <form action="{{ route('farmer.harvest.store') }}" method="POST">
                            @csrf

                            <!-- Basic Information Section -->
                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-info-circle"></i>
                                    <h4>Basic Information</h4>
                                </div>
                                
                                <div class="form-row">
                                    <!-- Orchard Selection -->
                                    <div class="form-col form-col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Assigned Orchard</label>
                                            <select name="orchard_id" class="form-control" required>
                                                <option value="">Select Orchard</option>
                                                @foreach(auth()->user()->farmer->orchards as $orchard)
                                                <option value="{{ $orchard->id }}">{{ $orchard->orchardName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Durian Type -->
                                    <div class="form-col form-col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Durian Type</label>
                                            <select name="durian_type" class="form-control" required>
                                                <option value="">Select Durian Type</option>
                                                @foreach($durianTypes as $durian)
                                                <option value="{{ $durian->name }}">{{ $durian->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <!-- Harvest Date -->
                                    <div class="form-col form-col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Harvest Date</label>
                                            <input type="date" name="harvest_date" class="form-control" required value="{{ old('harvest_date', date('Y-m-d')) }}">
                                        </div>
                                    </div>

                                    <!-- Total Harvested -->
                                    <div class="form-col form-col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Total Harvested (kg)</label>
                                            <input type="number" name="total_harvested" class="form-control" required min="1" value="{{ old('total_harvested', 1) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Harvest Quality Section -->
                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-clipboard-check"></i>
                                    <h4>Harvest Quality Checklist</h4>
                                </div>

                                <div class="form-row">
                                    <!-- Grade Selection -->
                                    <div class="form-col form-col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Durian Grade</label>
                                            <select name="grade" class="form-control" required>
                                                <option value="">Select Grade</option>
                                                <option value="A">Grade A (Premium)</option>
                                                <option value="B">Grade B (Standard)</option>
                                                <option value="C">Grade C (Processing)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Condition Selection -->
                                    <div class="form-col form-col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Condition</label>
                                            <select name="condition" class="form-control" required>
                                                <option value="">Select Condition</option>
                                                <option value="excellent">Excellent</option>
                                                <option value="good">Good</option>
                                                <option value="damaged">Damaged</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Storage Selection -->
                                <div class="form-group">
                                    <label class="form-label">Storage Location</label>
                                    <select name="storage" class="form-control">
                                        <option value="">Select Storage Location</option>
                                        @if(isset($storageLocations) && count($storageLocations) > 0)
                                            @foreach($storageLocations as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn-submit">
                                <i class="fas fa-save mr-2"></i>Submit Harvest
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1"></script>
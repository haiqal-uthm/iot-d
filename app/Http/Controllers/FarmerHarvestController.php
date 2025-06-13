<?php

namespace App\Http\Controllers;

use App\Models\HarvestLog;
use App\Models\Durian;
use App\Models\Orchard;
use App\Models\Storage;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmerHarvestController extends Controller
{
    public function create()
    {
        // Get all durian types from the database
        $durianTypes = Durian::all();
        $orchards = auth()->user()->farmer->orchards;
        $storageLocations = Storage::getLocations(); // Get active storage locations
        
        return view('farmer.harvest-entry', compact('durianTypes', 'orchards', 'storageLocations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'orchard_id' => 'required|exists:orchards,id',
            'durian_type' => 'required|string|max:255',
            'harvest_date' => 'required|date',
            'total_harvested' => 'required|integer|min:1',
            'grade' => 'required|string',
            'condition' => 'required|string',
            'storage' => 'nullable|string', // Changed from array to string
            'status' => 'nullable|string',
        ]);

        try {
            // Get the durian_id based on the durian_type
            $durian = Durian::where('name', $validated['durian_type'])->first();
            $durian_id = $durian ? $durian->id : null;
            
            // Set status to 'complete' if not provided
            $status = $validated['status'] ?? 'complete';

            $harvestLog = HarvestLog::create([
                'farmer_id' => auth()->user()->farmer->id,
                'orchard_id' => $validated['orchard_id'],
                'durian_id' => $durian_id,
                'harvest_date' => $validated['harvest_date'],
                'total_harvested' => $validated['total_harvested'],
                'grade' => $validated['grade'],
                'condition' => $validated['condition'],
                'storage_location' => $validated['storage'],
                'status' => $status
            ]);
            
            // If status is complete and storage location is provided, create inventory transaction
            if ($status === 'complete' && !empty($validated['storage'])) {
                // Create a new inventory transaction
                InventoryTransaction::create([
                    'farmer_id' => auth()->user()->farmer->id,
                    'durian_id' => $durian_id,
                    'storage_location' => $validated['storage'],
                    'quantity' => $validated['total_harvested'],
                    'type' => 'in', // Set transaction type to 'in'
                    'remarks' => 'New harvest - ID: ' . $harvestLog->id
                ]);
            }

            return redirect()->back()->with('success', 'Harvest entry submitted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error submitting harvest: ' . $e->getMessage())->withInput();
        }
    }

    public function show()
    {
        $harvestLogs = HarvestLog::forFarmer(auth()->id())
            ->with('durian')  // Ensure durian relationship is eager loaded
            ->orderBy('harvest_date', 'desc')
            ->get();

        $chartData = $harvestLogs->groupBy(function($log) {
            return $log->durian ? $log->durian->name : $log->durian_type;
        })->mapWithKeys(fn($logs, $type) => [$type => $logs->sum('total_harvested')]);

        return view('farmer.harvest-report', compact('harvestLogs', 'chartData'));
    }

    public function edit($id)
    {
        $harvestLog = HarvestLog::findOrFail($id);
        
        // Security check - ensure the farmer only edits their own records
        if ($harvestLog->farmer_id != auth()->user()->farmer->id) {
            return redirect()->route('farmer.harvest.report')
                ->with('error', 'You are not authorized to edit this harvest record.');
        }
        
        $durians = Durian::all(); // Add this line
        return view('farmer.harvest-edit', compact('harvestLog', 'durians')); // Update this line
    }

    public function showDetail($id)
    {
        $harvestLog = HarvestLog::with(['farmer.user', 'orchard', 'durian'])->findOrFail($id);
        
        // Security check - ensure the farmer only views their own records
        if ($harvestLog->farmer_id != auth()->user()->farmer->id) {
            return redirect()->route('farmer.harvest.report')
                ->with('error', 'You are not authorized to view this record.');
        }
        
        return view('farmer.harvest-show', compact('harvestLog'));
    }

    public function update(Request $request, $id)
    {
        $harvestLog = HarvestLog::findOrFail($id);
        
        // Security check - ensure the farmer only updates their own records
        if ($harvestLog->farmer_id != auth()->user()->farmer->id) {
            return redirect()->route('farmer.harvest.report')
                ->with('error', 'You are not authorized to update this harvest record.');
        }
        
        $validated = $request->validate([
            'durian_id' => 'required|exists:durians,id',
            'harvest_date' => 'required|date',
            'total_harvested' => 'required|integer|min:0', // Add this line
            'grade' => 'required|string',
            'condition' => 'required|string',
            'storage' => 'nullable|string',
            'status' => 'nullable|string',
            'remarks' => 'nullable|string|max:1000', // Add this line
        ]);
        
        // Set status to 'complete' if not provided
        $status = $validated['status'] ?? 'complete';
        
        $harvestLog->update([
            'durian_id' => $validated['durian_id'],
            'harvest_date' => $validated['harvest_date'],
            'total_harvested' => $validated['total_harvested'], // Add this line
            'grade' => $validated['grade'],
            'condition' => $validated['condition'],
            'storage_location' => $validated['storage'] ?? null,
            'status' => $status,
            'remarks' => $validated['remarks'] // Add this line
        ]);
        
        // If status is complete and storage location is provided, create inventory transaction
        if ($status === 'complete' && !empty($validated['storage'])) {
            // Create a new inventory transaction
            InventoryTransaction::create([
                'farmer_id' => $harvestLog->farmer_id,
                'durian_id' => $validated['durian_id'],
                'storage_location' => $validated['storage'],
                'quantity' => $harvestLog->total_harvested,
                'type' => 'in', // Set transaction type to 'in'
                'remarks' => 'Harvest completed - ID: ' . $harvestLog->id
            ]);
        }
        
        return redirect()->route('farmer.harvest.report')
            ->with('success', 'Harvest record updated successfully!');
    }
}
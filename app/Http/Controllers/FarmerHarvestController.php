<?php

namespace App\Http\Controllers;

use App\Models\HarvestLog;
use App\Models\Durian;
use App\Models\Orchard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmerHarvestController extends Controller
{
    public function create()
    {
        // Get all durian types from the database
        $durianTypes = Durian::all();
        $orchards = auth()->user()->farmer->orchards;
        
        return view('farmer.harvest-entry', compact('durianTypes', 'orchards'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'orchard_id' => 'required|exists:orchards,id',
            'durian_type' => 'required|string|max:255',
            'harvest_date' => 'required|date',
            'total_harvested' => 'required|integer|min:1',
            'grade' => 'required|array',
            'condition' => 'required|array',
            'storage' => 'required|array',
        ]);

        try {
            // Get the durian_id based on the durian_type
            $durian = Durian::where('name', $validated['durian_type'])->first();
            $durian_id = $durian ? $durian->id : null;

            HarvestLog::create([
                'farmer_id' => auth()->user()->farmer->id,
                'orchard_id' => $validated['orchard_id'],
                'durian_id' => $durian_id,
                'harvest_date' => $validated['harvest_date'],
                'total_harvested' => $validated['total_harvested'],
                'grade' => json_encode($validated['grade']),
                'condition' => json_encode($validated['condition']),
                'storage_location' => json_encode($validated['storage']),
                'status' => 'pending'
            ]);

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
            'durian_id' => 'required|exists:durians,id', // Changed from durian_type
            'harvest_date' => 'required|date',
            'grade' => 'required|array',
            'condition' => 'required|array',
            'storage' => 'required|array',
        ]);
        
        $harvestLog->update([
            'durian_id' => $validated['durian_id'], // Store the ID reference
            'harvest_date' => $validated['harvest_date'],
            'grade' => json_encode($validated['grade']),
            'condition' => json_encode($validated['condition']),
            'storage_location' => json_encode($validated['storage']),
        ]);
        
        return redirect()->route('farmer.harvest.report')
            ->with('success', 'Harvest record updated successfully!');
    }
}
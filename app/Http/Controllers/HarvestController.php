<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HarvestLog;
use App\Models\Durian;
use Illuminate\Support\Facades\DB;

class HarvestController extends Controller
{
   public function store(Request $request)
    {
        $validatedData = $request->validate([
            'orchard_id' => 'required|integer|exists:orchards,id',
            'durian_id' => 'required|integer|exists:durians,id',
            'farmer_id' => 'required|integer|exists:farmers,id',
            'total_harvested' => 'required|integer|min:1',
            'status' => 'required|string|max:50',
            'harvest_date' => 'nullable|date',
            'grade' => 'nullable|string|max:50',
            'condition' => 'nullable|string|max:100',
            'storage_location' => 'nullable|string|max:100',
        ]);
    
        try {
            $harvestLog = \App\Models\HarvestLog::create([
                'orchard_id' => $validatedData['orchard_id'],
                'durian_id' => $validatedData['durian_id'],
                'farmer_id' => $validatedData['farmer_id'],
                'harvest_date' => $validatedData['harvest_date'] ?? now()->toDateString(),
                'total_harvested' => $validatedData['total_harvested'],
                'status' => $validatedData['status'],
                'grade' => $validatedData['grade'] ?? null,
                'condition' => $validatedData['condition'] ?? null,
                'storage_location' => $validatedData['storage_location'] ?? null,
            ]);
    
            // Optionally update the durian total
            \App\Models\Durian::where('id', $validatedData['durian_id'])
                ->increment('total', $validatedData['total_harvested']);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Harvest log recorded successfully',
                'data' => $harvestLog,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error saving harvest log: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    
        public function save(Request $request)
        {
            HarvestLog::create([
                'harvest_id' => $request->harvest_id,
                'harvest_date' => $request->harvest_date,
                'location' => $request->location,
                'durian_type' => $request->durian_type,
                'quantity' => $request->quantity,
                'storage_location' => $request->storage_location,
            ]);
    
            return redirect()->back()->with('success', 'Harvest recorded successfully!');
        }
    
        private function getOrchardId($orchardName)
        {
            $orchardMapping = [
                'A' => 1,
                'B' => 2,
                'C' => 3,
            ];
    
            return $orchardMapping[$orchardName] ?? null; // Return null if not found
        }
    }

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryTransaction;
use App\Models\Durian;
use App\Models\Storage;
use Illuminate\Support\Facades\DB;

class FarmerInventoryController extends Controller
{
    public function index()
    {
        $farmerId = auth()->user()->farmer->id;
        $durianTypes = Durian::all();
        
        // Check if the table exists before querying
        try {
            // Get all storage locations used by this farmer
            $storageLocations = InventoryTransaction::where('farmer_id', $farmerId)
                ->distinct('storage_location')
                ->pluck('storage_location');
            
            // Get recent transactions with durian info
            $transactions = InventoryTransaction::with(['durian', 'storage'])
                ->where('farmer_id', $farmerId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
                
            // Calculate current stock levels for each location
            $stockLevels = [];
            foreach ($storageLocations as $location) {
                $stockLevels[$location] = InventoryTransaction::getCurrentStock($farmerId, $location);
            }
            
            // Get storage names for display
            $storageNames = Storage::whereIn('id', $storageLocations)->pluck('name', 'id')->toArray();
        } catch (\Exception $e) {
            // If there's an error (like table not existing), use defaults
            $storageLocations = collect(['cold_storage', 'warehouse']);
            $transactions = collect([]);
            $stockLevels = [
                'cold_storage' => 0,
                'warehouse' => 0
            ];
            $storageNames = [
                'cold_storage' => 'Cold Storage',
                'warehouse' => 'Warehouse'
            ];
        }
        
        // If no locations exist yet, provide default options
        if ($storageLocations->isEmpty()) {
            $storageLocations = collect(['cold_storage', 'warehouse']);
            $stockLevels = [
                'cold_storage' => 0,
                'warehouse' => 0
            ];
            $storageNames = [
                'cold_storage' => 'Cold Storage',
                'warehouse' => 'Warehouse'
            ];
        }

        return view('farmer.inventory-index', [
            'storageLocations' => $storageLocations,
            'inventoryTransactions' => $transactions ?? collect([]),
            'stockLevels' => $stockLevels,
            'durianTypes' => $durianTypes,
            'storageNames' => $storageNames ?? []
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'storage_location' => 'required|string|max:255',
            'durian_id' => 'required|exists:durians,id',
            'quantity' => 'required|numeric|min:0.1',
            'type' => 'required|in:in,out',
            'remarks' => 'nullable|string|max:255'
        ]);

        // Start a database transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            // Create inventory transaction
            $transaction = InventoryTransaction::create([
                'farmer_id' => auth()->user()->farmer->id,
                'durian_id' => $validated['durian_id'],
                'storage_location' => $validated['storage_location'],
                'quantity' => $validated['type'] === 'out' ? -$validated['quantity'] : $validated['quantity'],
                'type' => $validated['type'],
                'remarks' => $validated['remarks']
            ]);
            
            // Update durian total
            $durian = Durian::findOrFail($validated['durian_id']);
            
            if ($validated['type'] === 'in') {
                $durian->total += $validated['quantity'];
            } else {
                // Ensure we don't go below zero
                $durian->total = max(0, $durian->total - $validated['quantity']);
            }
            
            $durian->save();
            
            DB::commit();
            return redirect()->back()->with('success', 'Inventory updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update inventory: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryTransaction;

class FarmerInventoryController extends Controller
{
    public function index()
    {
        $farmerId = auth()->user()->farmer->id;
        
        // Check if the table exists before querying
        try {
            // Get all storage locations used by this farmer
            $storageLocations = InventoryTransaction::where('farmer_id', $farmerId)
                ->distinct('storage_location')
                ->pluck('storage_location');
            
            // Get recent transactions
            $transactions = InventoryTransaction::where('farmer_id', $farmerId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
                
            // Calculate current stock levels for each location
            $stockLevels = [];
            foreach ($storageLocations as $location) {
                $stockLevels[$location] = InventoryTransaction::getCurrentStock($farmerId, $location);
            }
        } catch (\Exception $e) {
            // If there's an error (like table not existing), use defaults
            $storageLocations = collect(['cold_storage', 'warehouse']);
            $transactions = collect([]);
            $stockLevels = [
                'cold_storage' => 0,
                'warehouse' => 0
            ];
        }
        
        // If no locations exist yet, provide default options
        if ($storageLocations->isEmpty()) {
            $storageLocations = collect(['cold_storage', 'warehouse']);
            $stockLevels = [
                'cold_storage' => 0,
                'warehouse' => 0
            ];
        }

        return view('farmer.inventory-index', [
            'storageLocations' => $storageLocations,
            'inventoryTransactions' => $transactions ?? collect([]),
            'stockLevels' => $stockLevels
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'storage_location' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.1',
            'type' => 'required|in:in,out',
            'remarks' => 'nullable|string|max:255'
        ]);

        InventoryTransaction::create([
            'farmer_id' => auth()->user()->farmer->id,
            'storage_location' => $validated['storage_location'],
            'quantity' => $validated['type'] === 'out' ? -$validated['quantity'] : $validated['quantity'],
            'type' => $validated['type'],
            'remarks' => $validated['remarks']
        ]);

        return redirect()->back()->with('success', 'Inventory updated successfully');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Durian;
use App\Models\Farmer;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Storage;

class AdminInventoryController extends Controller
{
    public function index(Request $request)
    {
        // Get storage locations from Storage model
        $storageLocations = Storage::getLocations();
        
        // Get all farmers for the dropdown
        $farmers = Farmer::with('user')->get();
        $durianTypes = Durian::all();
        
        // Build the query with relationships
        $query = InventoryTransaction::with(['farmer.user', 'durian'])
            ->orderBy('created_at', 'desc');
        
        // Apply filters if provided
        if ($request->filled('farmer_id')) {
            $query->where('farmer_id', $request->farmer_id);
        }
        
        if ($request->filled('durian_id')) {
            $query->where('durian_id', $request->durian_id);
        }
        
        if ($request->filled('storage_location')) {
            $query->where('storage_location', $request->storage_location);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Get all storage locations
        $storageLocations = InventoryTransaction::distinct('storage_location')
            ->pluck('storage_location');
            
        // If no storage locations exist yet, provide defaults
        if ($storageLocations->isEmpty()) {
            $storageLocations = collect(['cold_storage', 'warehouse']);
        }
        
        // Get transactions with pagination
        $transactions = $query->paginate(10)->withQueryString();
        
        // Calculate stock levels by farmer and location
        $stockLevels = [];
        foreach ($farmers as $farmer) {
            $stockLevels[$farmer->id] = [];
            foreach ($storageLocations as $location) {
                $stockLevels[$farmer->id][$location] = InventoryTransaction::getCurrentStock($farmer->id, $location);
            }
        }
        
        return view('admin.inventory.index', [
            'farmers' => $farmers,
            'durianTypes' => $durianTypes,
            'storageLocations' => $storageLocations,
            'inventoryTransactions' => $transactions,
            'stockLevels' => $stockLevels
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'storage_location' => 'required|string|max:255',
            'durian_id' => 'required|exists:durians,id',
            'quantity' => 'required|numeric|min:0.1',
            'type' => 'required|in:in,out',
            'remarks' => 'nullable|string|max:255',
            'new_location' => 'nullable|string|max:255|unique:storage,name',
        ]);
    
        // Handle new storage location creation
        if ($request->storage_location === 'new_location' && $request->filled('new_location')) {
            $storage = Storage::create([
                'name' => $request->new_location,
                'status' => 'active'
            ]);
            $validated['storage_location'] = $storage->id;
        }

        // Start a database transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            // For stock out, check if there's enough stock
            if ($validated['type'] === 'out') {
                $currentStock = InventoryTransaction::getCurrentStock(
                    $validated['farmer_id'], 
                    $validated['storage_location']
                );
                
                if ($currentStock < $validated['quantity']) {
                    return redirect()->back()->with('error', 'Not enough stock available in this location.');
                }
            }
            
            // Create inventory transaction
            $transaction = InventoryTransaction::create([
                'farmer_id' => $validated['farmer_id'],
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

    public function destroy(InventoryTransaction $transaction)
    {
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            // Reverse the effect on durian total
            $durian = Durian::findOrFail($transaction->durian_id);
            
            // If it was an 'in' transaction, reduce the total
            // If it was an 'out' transaction, increase the total
            if ($transaction->type === 'in') {
                $durian->total = max(0, $durian->total - abs($transaction->quantity));
            } else {
                $durian->total += abs($transaction->quantity);
            }
            
            $durian->save();
            
            // Delete the transaction
            $transaction->delete();
            
            DB::commit();
            return redirect()->back()->with('success', 'Inventory transaction deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete inventory transaction: ' . $e->getMessage());
        }
    }

}
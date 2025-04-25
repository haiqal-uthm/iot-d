<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Durian;
use App\Models\Storage;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        // Get all durian types with their current stock levels
        $durianStocks = Durian::with('inventoryTransactions')
            ->select('durians.id', 'durians.name', 'durians.total')
            ->selectRaw('COALESCE(SUM(inventory_transactions.quantity), 0) as current_stock')
            ->leftJoin('inventory_transactions', 'durians.id', '=', 'inventory_transactions.durian_id')
            ->groupBy('durians.id', 'durians.name', 'durians.total') // Include all selected columns
            ->get();
            
        // Get all storage locations with their capacity usage
        $storageLocations = Storage::where('status', 'active')->get();
        
        // Get recent inventory transactions
        $transactions = InventoryTransaction::with(['farmer', 'durian', 'storage'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('manager.inventory.index', compact('durianStocks', 'storageLocations', 'transactions'));
    }
    
    public function getTransactions(Request $request)
    {
        $query = InventoryTransaction::with(['farmer', 'durian', 'storage']);
        
        // Handle sorting
        if ($request->has('sort_by') && $request->has('sort_order')) {
            $query->orderBy($request->sort_by, $request->sort_order);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        // Handle filtering
        if ($request->has('durian_id') && $request->durian_id) {
            $query->where('durian_id', $request->durian_id);
        }
        
        if ($request->has('storage_location') && $request->storage_location) {
            $query->where('storage_location', $request->storage_location);
        }
        
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        $transactions = $query->paginate(15);
        
        if ($request->ajax()) {
            return view('manager.inventory.partials.transactions-table', compact('transactions'))->render();
        }
        
        return view('manager.inventory.transactions', compact('transactions'));
    }
}
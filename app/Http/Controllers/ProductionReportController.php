<?php

namespace App\Http\Controllers;

use App\Models\Orchard;
use App\Models\Durian;
use App\Models\VibrationLog;
use Illuminate\Http\Request;
use App\Models\HarvestLog;
use App\Models\Inventory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\InventoryTransaction;

class ProductionReportController extends Controller
{
    public function index(Request $request)
    {
        // Fetch dropdown data
        $orchards = Orchard::pluck('orchardName')->unique();
        $durianTypes = Durian::pluck('name')->unique();
    
        // Vibration Logs (Record Fall)
        $vibrationQuery = VibrationLog::query();
    
        // Search functionality for Record Fall
        if ($request->filled('search') && $request->has('device_id')) {
            $search = $request->search;
            $vibrationQuery->where(function($query) use ($search) {
                $query->where('device_id', 'LIKE', "%{$search}%")
                      ->orWhere('vibration_count', 'LIKE', "%{$search}%");
            });
        }
    
        // Specific filters for Record Fall
        if ($request->filled('device_id')) {
            $vibrationQuery->where('device_id', $request->device_id);
        }
    
        if ($request->filled('log_type')) {
            $vibrationQuery->where('log_type', $request->log_type);
        }
    
        if ($request->filled('date')) {
            $vibrationQuery->whereDate('timestamp', $request->date);
        }
    
        // Vibration Logs (Record Fall)
        $vibrationLogs = $vibrationQuery->orderBy('timestamp', 'desc')->paginate(5);
    
        // For admin, fetch all harvest logs; for farmers, only fetch their own
        $harvestQuery = HarvestLog::query()
            ->with(['farmer.user', 'orchard', 'durian'])
            ->orderBy('harvest_date', 'desc');
        
        // Apply filters if provided
        if ($request->filled('orchard')) {
            $harvestQuery->whereHas('orchard', function($query) use ($request) {
                $query->where('orchardName', 'like', "%{$request->orchard}%");
            });
        }
        
        if ($request->filled('durian_type')) {
            $harvestQuery->whereHas('durian', function($query) use ($request) {
                $query->where('name', 'like', "%{$request->durian_type}%");
            });
        }
        
        if ($request->filled('date')) {
            $harvestQuery->whereDate('harvest_date', $request->date);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $harvestQuery->where(function($query) use ($search) {
                $query->whereHas('orchard', function($q) use ($search) {
                    $q->where('orchardName', 'like', "%{$search}%");
                })
                ->orWhereHas('durian', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%");
            });
        }
        
        $harvestReports = $harvestQuery->paginate(5)->withQueryString();
    
        // Get storage reports with pagination
        $storageReports = InventoryTransaction::select(
            'storage_location',  // Changed from 'inventory_transactions'
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('MAX(updated_at) as updated_at')
        )
            ->groupBy('storage_location')
            ->paginate(5);
    
        // Chart Data for Last 30 Days (Vibration)
        $startDate = now()->subDays(30);
        $endDate = now();
    
        $vibrationData = VibrationLog::whereBetween('timestamp', [$startDate, $endDate])
            ->selectRaw('DATE(timestamp) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
    
        // Generate complete date range for chart data
        $chartData = [];
        $currentDate = $startDate->copy();
    
        while ($currentDate <= $endDate) {
            $formattedDate = $currentDate->format('Y-m-d');
            $chartData[$formattedDate] = $vibrationData->has($formattedDate) ? $vibrationData[$formattedDate]->count : 0;
            $currentDate->addDay();
        }
    
        // Chart Data for Harvest Report
        $harvestChartData = HarvestLog::select('durian_id', DB::raw('SUM(total_harvested) as total_harvested'))
            ->groupBy('durian_id')
            ->get();
    
        // Add durian name to the chart data
        $harvestChartData = $harvestChartData->map(function ($item) {
            $durian = Durian::find($item->durian_id);
            $item->durian_type = $durian ? $durian->name : 'Unknown';
            return $item;
        });
    
        // Chart Data for Inventory Report
        $inventoryChartData = InventoryTransaction::select(
            'storage_location', 
            DB::raw('SUM(quantity) as total_quantity')
        )
            ->groupBy('storage_location')
            ->get();
    
        // Debug line - add this temporarily
        Log::info('Storage Reports:', ['data' => $storageReports]);
    
        return view('admin.production-report', compact(
            'vibrationLogs',
            'harvestReports',
            'storageReports',
            'orchards',
            'durianTypes',
            'harvestChartData',
            'inventoryChartData',
            'chartData'
        ));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'harvest_document' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        $file = $request->file('harvest_document');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('harvest_documents', $filename, 'public');

        HarvestLog::create([
            'file_path' => $path, // You still need this column in your table
            'status' => 'Pending', // Or 'Unverified', 'Uploaded', etc.
        ]);

        return back()->with('success', 'Harvest document uploaded successfully!');
    }

    public function saveHarvestDetails(Request $request)
    {
        DB::beginTransaction();
        try {
            $harvestLog = HarvestLog::findOrFail($request->harvest_id);
            
            // Update harvest log details
            $harvestLog->update([
                'estimated_weight' => $request->estimated_weight,
                'grade' => $request->grade,
                'condition' => $request->condition,
                'storage_location' => $request->storage_location,
                'remarks' => $request->remarks,
                'status' => 'Completed' // Set status to Completed when details are saved
            ]);
    
            // Delete existing storage entries for this harvest log
            Storage::where('harvest_log_id', $harvestLog->id)->delete();
        
            // Create storage entries for each selected storage location
            if ($request->storage_location) {
                $processedLocations = [];
                
                foreach ($request->storage_location as $location) {
                    // Skip if we've already processed this location
                    if (in_array($location, $processedLocations)) {
                        continue;
                    }
    
                    Storage::create([
                        'storage_location' => $location,
                        'harvest_log_id' => $harvestLog->id,
                        'durian_type' => $harvestLog->durian_type,
                        'quantity' => $harvestLog->total_harvested
                    ]);
    
                    $processedLocations[] = $location;
                }
            }
    
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

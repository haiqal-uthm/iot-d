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
    
        $vibrationLogs = $vibrationQuery->orderBy('timestamp', 'desc')->paginate(10);
    
        // Harvest Reports
        $harvestQuery = HarvestLog::query();
    
        // Search functionality for Harvest Report
        if ($request->filled('search') && $request->has('orchard')) {
            $search = $request->search;
            $harvestQuery->where(function($query) use ($search) {
                $query->where('orchard', 'LIKE', "%{$search}%")
                      ->orWhere('durian_type', 'LIKE', "%{$search}%")
                      ->orWhere('total_harvested', 'LIKE', "%{$search}%");
            });
        }
    
        // Specific filters for Harvest Report
        if ($request->filled('orchard')) {
            $harvestQuery->where('orchard', $request->orchard);
        }
    
        if ($request->filled('durian_type')) {
            $harvestQuery->where('durian_type', $request->durian_type);
        }
    
        if ($request->filled('date')) {
            $harvestQuery->whereDate('harvest_date', $request->date);
        }
    
        // Modify the harvest query to include all details
        $harvestQuery = HarvestLog::query()->select([
            'harvest_logs.*',
            'estimated_weight',
            'grade',
            'condition',
            'storage_location',
            'remarks'
        ]);
    
        $harvestReports = $harvestQuery->orderBy('harvest_date', 'desc')->get();
    
        // Chart Data for Last 30 Days (Vibration)
        $startDate = now()->subDays(30);
        $endDate = now();
    
        $vibrationData = VibrationLog::whereBetween('timestamp', [$startDate, $endDate])
            ->selectRaw('DATE(timestamp) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
    
        // Generate complete date range
        $chartData = [];
        $currentDate = $startDate->copy();
    
        while ($currentDate <= $endDate) {
            $formattedDate = $currentDate->format('Y-m-d');
            $chartData[$formattedDate] = $vibrationData->has($formattedDate) ? $vibrationData[$formattedDate]->count : 0;
            $currentDate->addDay();
        }
    
        // Harvest Timeline Data
        $harvestTimeline = HarvestLog::select('durian_type', 'harvest_date', 'total_harvested')
            ->orderBy('harvest_date')
            ->get()
            ->groupBy('durian_type');
    
        // Get storage reports
        $storageReports = Storage::select('storage_location', 
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('MAX(updated_at) as updated_at'))
            ->groupBy('storage_location')
            ->get();
    
        // Debug line - add this temporarily
Log::info('Storage Reports:', ['data' => $storageReports]);
    
        return view('production-report', compact(
            'vibrationLogs',
            'harvestReports',
            'storageReports',
            'orchards',
            'durianTypes',
            'chartData',
            'harvestTimeline'
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

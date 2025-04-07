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

class ProductionReportController extends Controller
{
    public function index(Request $request)
    {
        // Fetch dropdown data
        $orchards = Orchard::pluck('orchardName')->unique();
        $durianTypes = Durian::pluck('name')->unique();

        // Vibration Logs (Record Fall)
        $vibrationQuery = VibrationLog::query();

        if ($request->filled('date')) {
            $vibrationQuery->whereDate('timestamp', $request->date);
        }

        if ($request->filled('orchard')) {
            $vibrationQuery->where('device_id', $request->orchard);
        }

        $vibrationLogs = $vibrationQuery->orderBy('timestamp', 'desc')->paginate(10);

        // Harvest Reports
        $harvestQuery = HarvestLog::query();

        if ($request->filled('orchard')) {
            $harvestQuery->where('orchard', $request->orchard);
        }

        if ($request->filled('durian_type')) {
            $harvestQuery->where('durian_type', $request->durian_type);
        }

        if ($request->filled('date')) {
            $harvestQuery->whereDate('harvest_date', $request->date);
        }

        $harvestReports = $harvestQuery->orderBy('harvest_date', 'desc')->get();

        // Inventory Reports
        $inventoryReports = Inventory::all();

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

        // 🆕 Harvest Timeline Data (Gantt Chart)
        $harvestTimeline = HarvestLog::select('durian_type', 'harvest_date', 'total_harvested')
            ->orderBy('harvest_date')
            ->get()
            ->groupBy('durian_type');

        return view('production-report', compact(
            'vibrationLogs',
            'harvestReports',
            'inventoryReports',
            'orchards',
            'durianTypes',
            'chartData',
            'harvestTimeline' // 🆕 Pass to Blade View
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
}

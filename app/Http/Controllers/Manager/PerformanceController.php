<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Orchard;
use App\Models\User;
use App\Models\HarvestLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    public function index()
    {
        // Get all farmers with their users and orchards
        $farmers = Farmer::with(['user', 'orchards'])->get();
        
        return view('manager.performance.index', compact('farmers'));
    }
    
    public function show($id)
    {
        // Get the farmer with relationships
        $farmer = Farmer::with(['user', 'orchards'])->findOrFail($id);
        
        // Get harvest data for the last 30 days by default
        // Hardcode 30 days period
        $harvestData = $this->getHarvestData($farmer->id, 30);
        return view('manager.performance.show', compact('farmer', 'harvestData'));
    }
    
    public function getHarvestData($farmerId, $days = 30)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($days);

        $harvestCounts = HarvestLog::where('farmer_id', $farmerId)
            ->whereBetween('harvest_date', [$startDate, $endDate])
            ->select(DB::raw('DATE(harvest_date) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        // Generate continuous date range
        $dates = [];
        $counts = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d'); // Remove extra spaces
            $dates[] = $dateString;
            $counts[] = $harvestCounts[$dateString]->count ?? 0;
            $currentDate->addDay();
        }

        return [
            'dates' => $dates,
            'counts' => $counts
        ];
    }
    
    public function getHarvestDataJson(Request $request, $farmerId)
    {
        // Remove period parameter handling and hardcode 30 days
        $harvestData = $this->getHarvestData($farmerId, 30);
        return response()->json($harvestData);
    }
}
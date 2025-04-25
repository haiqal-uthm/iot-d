<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HarvestLog;
use App\Models\VibrationLog;
use App\Models\InventoryTransaction;
use App\Models\Farmer;
use App\Models\Durian;
use App\Models\Orchard;
use App\Models\Device;
use App\Models\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Changed from 'use PDF;'
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HarvestReportExport;
use App\Exports\FallMonitoringExport;
use App\Exports\InventoryReportExport;

class ReportController extends Controller
{
    public function index()
    {
        $farmers = Farmer::with('user')->get();
        $durians = Durian::all();
        $orchards = Orchard::all();
        $devices = Device::all();
        $storageLocations = Storage::where('status', 'active')->get();
        
        return view('manager.reports.index', compact(
            'farmers', 
            'durians', 
            'orchards', 
            'devices', 
            'storageLocations'
        ));
    }
    
    public function harvestReport(Request $request)
    {
        $query = HarvestLog::with(['farmer', 'orchard', 'durian'])
            ->when($request->start_date && $request->end_date, function($q) use ($request) {
                return $q->whereBetween('harvest_date', [$request->start_date, $request->end_date]);
            })
            ->when($request->farmer_id, function($q) use ($request) {
                return $q->where('farmer_id', $request->farmer_id);
            })
            ->when($request->durian_id, function($q) use ($request) {
                return $q->where('durian_id', $request->durian_id);
            })
            ->when($request->orchard_id, function($q) use ($request) {
                return $q->where('orchard_id', $request->orchard_id);
            })
            ->orderBy('harvest_date', 'desc');
            
        $harvestLogs = $query->paginate(15);  // Changed from $harvests
        
        if ($request->export_type) {
            $allHarvests = $query->get();
            
            if ($request->export_type === 'pdf') {
                $pdf = Pdf::loadView('manager.reports.exports.harvest_pdf', [ // Changed from PDF:: to Pdf::
                    'harvests' => $allHarvests,
                    'filters' => $request->only(['start_date', 'end_date', 'farmer_id', 'durian_id', 'orchard_id'])
                ]);
                
                return $pdf->download('harvest_report_' . now()->format('Y-m-d') . '.pdf');
            } elseif ($request->export_type === 'excel') {
                return Excel::download(new HarvestReportExport($allHarvests), 'harvest_report_' . now()->format('Y-m-d') . '.xlsx');
            }
        }
        
        $farmers = Farmer::with('user')->get();
        $durians = Durian::all();
        $orchards = Orchard::all();
        $farmNames = Farmer::with('user')->get()->unique('user.name')->pluck('user.name');
        
        return view('manager.reports.harvest', compact(
            'harvestLogs', 
            'farmers', 
            'durians', 
            'orchards',
            'farmNames'
        ));
    }
    
    public function fallMonitoringReport(Request $request)
    {
        $query = VibrationLog::with(['orchard.farmer.user', 'device'])
            ->when($request->start_date && $request->end_date, function($q) use ($request) {
                return $q->whereBetween('timestamp', [$request->start_date, $request->end_date]);
            })
            ->when($request->device_id, function($q) use ($request) {
                return $q->where('device_id', $request->device_id);
            })
            ->when($request->orchard_id, function($q) use ($request) {
                return $q->whereHas('orchard', function($subq) use ($request) {
                    return $subq->where('id', $request->orchard_id);
                });
            })
            // Remove the farm_name filter
            ->orderBy('timestamp', 'desc');
            
        $vibrationLogs = $query->paginate(15);
        
        if ($request->export_type) {
            $allLogs = $query->get();
            
            if ($request->export_type === 'pdf') {
                $pdf = Pdf::loadView('manager.reports.exports.fall_monitoring_pdf', [
                    'vibrationLogs' => $allLogs,
                    'filters' => $request->only(['start_date', 'end_date', 'device_id', 'orchard_id'])
                ]);
                
                return $pdf->download('fall_monitoring_report_' . now()->format('Y-m-d') . '.pdf');
            } elseif ($request->export_type === 'excel') {
                return Excel::download(new FallMonitoringExport($allLogs), 'fall_monitoring_report_' . now()->format('Y-m-d') . '.xlsx');
            }
        }
        
        $devices = Device::all();
        $orchards = Orchard::all();
        $farmNames = Farmer::with('user')->get()->unique('user.name')->pluck('user.name');
        
        return view('manager.reports.fall', compact(
            'vibrationLogs', 
            'devices', 
            'orchards'  // Changed from farmNames
        ));
    }
    
    public function inventoryReport(Request $request)
    {
        $query = InventoryTransaction::with(['farmer', 'durian', 'storage'])
            ->when($request->start_date && $request->end_date, function($q) use ($request) {
                return $q->whereBetween('created_at', [$request->start_date, $request->end_date]);
            })
            ->when($request->durian_id, function($q) use ($request) {
                return $q->where('durian_id', $request->durian_id);
            })
            ->when($request->storage_location, function($q) use ($request) {
                return $q->where('storage_location', $request->storage_location);
            })
            ->when($request->type, function($q) use ($request) {
                return $q->where('type', $request->type);
            })
            ->orderBy('created_at', 'desc');
            
        $transactions = $query->paginate(15);
        
        if ($request->export_type) {
            $allTransactions = $query->get();
            
            if ($request->export_type === 'pdf') {
                $pdf = Pdf::loadView('manager.reports.exports.inventory_pdf', [
                    'transactions' => $allTransactions,
                    'filters' => $request->only(['start_date', 'end_date', 'durian_id', 'storage_location', 'type'])
                ]);
                
                return $pdf->download('inventory_report_' . now()->format('Y-m-d') . '.pdf');
            } elseif ($request->export_type === 'excel') {
                return Excel::download(new InventoryReportExport($allTransactions), 'inventory_report_' . now()->format('Y-m-d') . '.xlsx');
            }
        }
        
        $durians = Durian::all();
        $storageLocations = Storage::where('status', 'active')->get();
        $transactionTypes = [
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'transfer' => 'Transfer',
            'adjustment' => 'Adjustment'
        ];
        
        return view('manager.reports.inventory', compact(
            'transactions', 
            'durians', 
            'storageLocations', 
            'transactionTypes'
        ));
    }
}
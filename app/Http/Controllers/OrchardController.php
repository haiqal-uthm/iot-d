<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orchard;
use App\Models\Device;
use App\Models\Durian;
use App\Models\VibrationLog;

class OrchardController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        $durians = Durian::all();
        
        $orchards = auth()->user()->role === 'farmer'
            ? auth()->user()->farmer->orchards()->with(['durian', 'device'])->get()
            : Orchard::with(['durian', 'device'])->get();
        
        // Fetch vibration logs for the orchards
        $orchardDeviceIds = $orchards->pluck('device_id')->filter()->toArray();
        
        // Filter vibration logs based on user role
        if (auth()->user()->role === 'farmer') {
            // For farmers, only show logs from their assigned orchards
            $vibrationLogs = VibrationLog::whereIn('device_id', $orchardDeviceIds)
                ->orderBy('timestamp', 'desc')
                ->take(10)
                ->get();
        } else {
            // For admins, show all logs
            $vibrationLogs = VibrationLog::orderBy('timestamp', 'desc')
                ->take(10)
                ->get();
        }
    
        $view = auth()->user()->role === 'farmer' 
            ? 'farmer.orchards' 
            : 'admin.orchards';
    
        return view($view, compact('orchards', 'devices', 'durians', 'vibrationLogs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'orchardName' => 'required|string|max:255',
            'numTree' => 'required|integer|min:1',
            'orchardSize' => 'required|numeric|min:0.1',
            'location' => 'required|string|max:255',
            'device_id' => 'nullable|exists:devices,id',
            'durian_id' => 'nullable|exists:durians,id',
            'user_id' => 'required|exists:users,id'  // Add user_id validation
        ]);

        $orchard = Orchard::create($validated);
        
        // Attach to farmer if farmer is creating
        if (auth()->user()->role === 'farmer') {
            auth()->user()->farmer->orchards()->attach($orchard->id);
        }

        return redirect()->route('farmer.orchards')->with('success', 'Orchard added successfully!');
    }

    public function updateTotalDurianFall(Request $request, $orchardId)
    {
        $validated = $request->validate([
            'total_durian_fall' => 'required|integer',
        ]);

        $orchard = Orchard::find($orchardId);

        if (!$orchard) {
            return response()->json(['status' => 'error', 'message' => 'Orchard not found'], 400);
        }

        // Assuming you have a 'total_durian_fall' column in your orchards table
        $orchard->total_durian_fall = $validated['total_durian_fall'];
        $orchard->save();

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $orchard = Orchard::findOrFail($id);
        $orchard->delete();
        return redirect()->route('orchards')->with('success', 'Orchard deleted successfully!');
    }
}

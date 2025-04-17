<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orchard;
use App\Models\Device;
use App\Models\Durian;

class OrchardController extends Controller
{
    public function index()
    {
        // Fetch all orchards with their related 'durians' and 'devices'
        $devices = Device::all(); 
        $durians = Durian::all(); 
        $orchards = Orchard::with(['durian', 'device'])->get();

        // Ensure your Blade file is correctly named (e.g., "resources/views/orchards/index.blade.php")
        return view('orchards', compact('orchards', 'devices', 'durians'));
    }

    public function create()
    {
        $devices = Device::all(); // Fetch available devices
        $durians = Durian::all(); // Fetch available durians
        return view('orchards.create', compact('devices', 'durians'));
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
        ]);

        Orchard::create($validated);

        // Ensure the correct route name is used
        return redirect()->route('orchards')->with('success', 'Orchard added successfully!');
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

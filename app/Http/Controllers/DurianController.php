<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Durian;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DurianController extends Controller
{
    public function index()
    {
        $durians = Durian::with('orchard')->get();
        return view('durian', compact('durians'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total' => 'required|integer|min:0',
            'orchard' => 'nullable|array', // Validate the array of orchard values
            'orchard.*' => 'in:1,2', // Ensure the orchard values are either 1 or 2
        ]);

        // Save the durian with the first selected orchard
        Durian::create([
            'name' => $request->name,
            'total' => $request->total,
            'orchard_id' => $request->orchard[0] ?? null, // Use the first orchard selected
        ]);

        return redirect()->route('durian')->with('success', 'Durian added successfully!');
    }

    public function fetchDurianData()
    {
        $durianData = DB::table('durians')->select('type', DB::raw('SUM(total) as total'))->groupBy('type')->get();

        return response()->json($durianData);
    }

    public function destroy($id)
    {
        $durian = Durian::findOrFail($id);
        $durian->delete();

        return redirect()->route('durian')->with('success', 'Durian deleted successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total' => 'required|integer|min:0',
            'orchard_id' => 'required|in:1,2',
        ]);

        // Find the durian by its ID and update it
        $durian = Durian::findOrFail($id);
        $durian->update([
            'name' => $request->name,
            'total' => $request->total,
            'orchard_id' => $request->orchard_id,
        ]);

        return redirect()->route('durian')->with('success', 'Durian updated successfully!');
    }

    public function updateTotal(Request $request)
    {
        $validated = $request->validate([
            'orchard_id' => 'required|exists:orchards,id',
            'total' => 'required|integer|min:0',
        ]);

        // Find the durian by orchard_id and update the total
        $durian = Durian::where('orchard_id', $validated['orchard_id'])->first();

        if (!$durian) {
            return response()->json(['error' => 'Durian record not found'], 404);
        }

        // Update the total count
        $durian->total = $validated['total'];
        $durian->save();

        return response()->json(['success' => true, 'message' => 'Total count updated successfully']);
    }

    public function saveVibration(Request $request)
    {
        try {
            $validated = $request->validate([
                'orchard_id' => 'required|exists:orchards,id',
                'vibration_count' => 'required|integer|min:0',
            ]);

            // Find the corresponding durian record for the orchard
            $durian = Durian::where('orchard_id', $validated['orchard_id'])->first();

            if ($durian) {
                // Update the total with the vibration count
                $durian->total += $validated['vibration_count'];
                $durian->save();
            } else {
                // Create a new record if none exists
                Durian::create([
                    'orchard_id' => $validated['orchard_id'],
                    'name' => 'Default Name', // Adjust this as needed
                    'total' => $validated['vibration_count'],
                ]);
            }

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error saving vibration count: ' . $e->getMessage());

            // Return a failure response
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

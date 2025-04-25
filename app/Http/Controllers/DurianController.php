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
        // To:
        $durians = Durian::with('orchards')->get();
        return view('admin.durian', compact('durians'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total' => 'required|integer|min:0',
        ]);

        // Save the durian
        Durian::create([
            'name' => $request->name,
            'total' => $request->total,
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
        ]);

        // Find the durian by its ID and update it
        $durian = Durian::findOrFail($id);
        $durian->update([
            'name' => $request->name,
            'total' => $request->total,
        ]);

        return redirect()->route('durian')->with('success', 'Durian updated successfully!');
    }

    public function updateTotal(Request $request)
    {
        $validated = $request->validate([
            'durian_id' => 'required|exists:durians,id',
            'total' => 'required|integer|min:0',
        ]);

        // Find the durian and update the total
        $durian = Durian::findOrFail($validated['durian_id']);

        // Update the total count
        $durian->total = $validated['total'];
        $durian->save();

        return response()->json(['success' => true, 'message' => 'Total count updated successfully']);
    }

    public function saveVibration(Request $request)
    {
        try {
            $validated = $request->validate([
                'durian_id' => 'required|exists:durians,id',
                'vibration_count' => 'required|integer|min:0',
            ]);

            // Find the durian record
            $durian = Durian::findOrFail($validated['durian_id']);

            // Update the total with the vibration count
            $durian->total += $validated['vibration_count'];
            $durian->save();

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error saving vibration count: ' . $e->getMessage());

            // Return a failure response
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        return view('admin.durian.create');
    }

    public function show($id)
    {
        $durian = Durian::findOrFail($id);
        return view('admin.durian.show', compact('durian'));
    }

    public function edit($id)
    {
        $durian = Durian::findOrFail($id);
        return view('admin.durian.edit', compact('durian'));
    }
}

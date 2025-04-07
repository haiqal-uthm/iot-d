<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HarvestLog;
use App\Models\Durian;
use Illuminate\Support\Facades\DB;

class HarvestController extends Controller
{
    public function store(Request $request)
    {
        if ($request->isJson()) {
            $data = $request->json()->all();
        } else {
            $data = $request->all(); // ✅ Accept both JSON & form data
        }

        $request->replace($data); // ✅ Ensure data is accessible

        $request->validate([
            'orchard' => 'required|string|max:100',
            'durian_type' => 'required|string|max:50',
            'total_harvested' => 'required|integer|min:1',
            'status' => 'required|string|max:50',
        ]);

        try {
            // Convert orchard name ('A', 'B', etc.) into an integer ID
            $orchardId = $this->getOrchardId($request->orchard);

            // Insert into `harvestDurianLog`
            $harvestLog = HarvestLog::create([
                'orchard' => $request->orchard,
                'durian_type' => $request->durian_type,
                'harvest_date' => now()->toDateString(),
                'total_harvested' => $request->total_harvested,
                'status' => $request->status,
            ]);

            // Ensure the orchard_id is correct before updating durians
            if ($orchardId === null) {
                throw new \Exception('Invalid orchard ID');
            }

            // Find existing durian record
            $durian = Durian::lockForUpdate()->where('name', $request->durian_type)->where('orchard_id', $orchardId)->first();

            if ($durian) {
                // Update existing total
                $durian->increment('total', $request->total_harvested);
            } else {
                // Insert a new durian entry
                Durian::create([
                    'name' => $request->durian_type,
                    'total' => $request->total_harvested,
                    'orchard_id' => $orchardId,
                ]);
            }

            DB::commit(); // Commit changes

            return response()->json([
                'status' => 'success',
                'message' => 'Harvest log recorded successfully',
                'data' => $harvestLog,
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback if error

            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Database error: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function save(Request $request)
    {
        HarvestLog::create([
            'harvest_id' => $request->harvest_id,
            'harvest_date' => $request->harvest_date,
            'location' => $request->location,
            'durian_type' => $request->durian_type,
            'quantity' => $request->quantity,
            'storage_location' => $request->storage_location,
        ]);

        return redirect()->back()->with('success', 'Harvest recorded successfully!');
    }

    private function getOrchardId($orchardName)
    {
        $orchardMapping = [
            'A' => 1,
            'B' => 2,
            'C' => 3,
        ];

        return $orchardMapping[$orchardName] ?? null; // Return null if not found
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

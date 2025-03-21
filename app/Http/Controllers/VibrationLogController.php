<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VibrationLog;
use Illuminate\Support\Facades\Log;

class VibrationLogController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'deviceID' => 'required|string|max:50',
            'vibrationCount' => 'required|integer|min:1',
            'logType' => 'required|integer',
        ]);

        \App\Models\VibrationLog::create([
            'device_id' => $validatedData['deviceID'],
            'vibration_count' => $validatedData['vibrationCount'],
            'log_type' => $validatedData['logType'],
            'timestamp' => now(),
        ]);

        return response()->json(['status' => 'success'], 200);
    }
}

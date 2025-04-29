<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\VibrationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            // Get recent vibration logs (last 7 days) with required relationships
            $recentLogs = VibrationLog::where('timestamp', '>=', now()->subDays(7))
                ->with(['orchard.farmer.user', 'device'])
                ->orderBy('timestamp', 'desc')
                ->get();
            
            // Group notifications by log_type with type 1 as durian falls
            $notifications = [
                'durian_falls' => $recentLogs->where('log_type', 1),
                'animal_threat_alerts' => $recentLogs->where('log_type', 2),
                'other_alerts' => $recentLogs->whereNotIn('log_type', [1, 2]),
            ];
            
            return view('manager.notifications', compact('notifications'));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error loading notifications: ' . $e->getMessage());
            
            // Return empty notifications if there's an error
            $notifications = [
                'durian_falls' => collect(),
                'animal_threat_alerts' => collect(),
                'other_alerts' => collect(),
            ];
            
            return view('manager.notifications', compact('notifications'))
                ->with('error', 'There was an error loading notifications. Please try again later.');
        }
    }
    
    public function markAsRead(Request $request)
    {
        // For future implementation - mark notifications as read
        return redirect()->back()->with('success', 'Notification marked as read');
    }
}
<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\VibrationLog;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // Get recent vibration logs (last 7 days)
        $recentLogs = VibrationLog::where('timestamp', '>=', now()->subDays(7))
            ->with('orchard')
            ->orderBy('timestamp', 'desc')
            ->get();
        
        // Group notifications by log_type
        $notifications = [
            'durian_falls' => $recentLogs->where('log_type', 1),
            'animal_threat_alerts' => $recentLogs->where('log_type', 2),
            'other_alerts' => $recentLogs->whereNotIn('log_type', [1, 2]),
        ];
        
        return view('manager.notifications', compact('notifications'));
    }
    
    public function markAsRead(Request $request)
    {
        // For future implementation - mark notifications as read
        return redirect()->back()->with('success', 'Notification marked as read');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use Google\Cloud\Firestore\FirestoreClient;


class DeviceController extends Controller
{
    protected $database;
    protected $firestore;

    public function __construct()
    {
        $this->firestore = new FirestoreClient([
            'keyFilePath' => config('firebase.credentials.file'), // Path to your Firebase service account credentials
        ]);
    }

    public function index()
    {
        $devices = Device::all();
        return view('devices', compact('devices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|string|max:255|unique:devices',
            'status' => 'required|string|in:active,inactive,maintenance',
        ]);

        Device::create([
            'name' => $request->name,
            'device_id' => $request->device_id,
            'status' => $request->status,
        ]);

        return redirect()->route('devices')->with('success', 'Device added successfully!');
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:active,inactive,maintenance',
        ]);

        // Find and update the device
        $device = Device::findOrFail($id);
        $device->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        // Redirect back to devices page with success message
        return redirect()->route('devices')->with('success', 'Device updated successfully!');
    }

    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        $device->delete();

        return redirect()->route('devices')->with('success', 'Device deleted successfully!');
    }

    public function toggleLed(Request $request)
    {
        $request->validate([
            'status' => 'required|in:ON,OFF',
            'device_id' => 'required|string|max:255',
        ]);

        // Get the device ID from the request
        $deviceId = $request->device_id;

        // Convert LED status to boolean for Firebase
        $ledStatus = $request->status === 'ON';

        // Now reference the specific device's LED control
        $ledRef = $this->database->getReference('sensors/ledControl/fromWebApp/' . $deviceId);

        // Retrieve the current status (if available)
        $currentLedStatus = $ledRef->getValue(); // This will get the current value from Firebase

        // If current status exists, toggle it, else set it based on the new status
        if ($currentLedStatus !== null) {
            $ledStatus = !$currentLedStatus; // Toggle the status
        }

        // Set the new LED status in Firebase for the specific device
        $ledRef->set($ledStatus);

        return response()->json([
            'message' => 'LED status updated successfully.',
            'status' => $ledStatus ? 'ON' : 'OFF',
        ]);
    }

    public function getTotalDevices()
    {
        // Fetch the total devices count from Firestore
        $devicesCollection = $this->firestore->collection('devices');
        $devicesSnapshot = $devicesCollection->documents();

        // Convert the QuerySnapshot to an array to count
        $devices = iterator_to_array($devicesSnapshot);

        // Count the number of documents (devices)
        $totalDevices = count($devices);

        return response()->json([
            'total_devices' => $totalDevices,
        ]);
    }
}

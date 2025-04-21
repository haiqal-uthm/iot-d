<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function index()
    {
        $storages = Storage::all();
        return view('admin.storage.index', compact('storages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:storage,name|max:255',
            'capacity' => 'required|numeric|min:0',
            'temperature_control' => 'sometimes|boolean',
            'status' => 'required|in:active,maintenance,closed', // Add status
            'description' => 'nullable|string'
        ]);

        // Handle checkbox value properly
        $validated['temperature_control'] = $request->has('temperature_control');

        Storage::create($validated);
        return redirect()->back()->with('success', 'Storage location added');
    }

    public function update(Request $request, Storage $storage)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:storage,name,'.$storage->id,
            'capacity' => 'required|numeric|min:0',
            'temperature_control' => 'required|boolean',
            'description' => 'nullable|string',
            'status' => 'required|in:active,maintenance,closed'
        ]);

        // Convert checkbox value to boolean
        $validated['temperature_control'] = (bool)$validated['temperature_control'];
        
        $storage->update($validated);
        return redirect()->back()->with('success', 'Storage updated');
    }

    public function destroy(Storage $storage)
    {
        $storage->delete();
        return redirect()->back()->with('success', 'Storage removed');
    }
}
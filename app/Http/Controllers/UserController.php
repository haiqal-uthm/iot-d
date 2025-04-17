<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Orchard;
use App\Models\Farmer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['farmer.orchards'])
            ->when(request('search'), function ($query) {
                $search = request('search');
                return $query->where('name', 'like', "%$search%")
                             ->orWhere('email', 'like', "%$search%");
            })
            ->paginate(5); // Changed from get() to paginate(2)
        // To this:
        return view('admin.users.index', compact('users'));
    }

    // In create method
    public function create()
    {
        $orchards = Orchard::all(); // Add this line
        return view('admin.users.create', compact('orchards'));
    }

    // In edit method
    public function edit(User $user)
    {
        $orchards = Orchard::all(); // Add this line to get orchards
        $selectedOrchards = $user->role === 'farmer' && $user->farmer ? $user->farmer->orchards->pluck('id')->toArray() : [];
        
        return view('admin.users.edit', compact('user', 'orchards', 'selectedOrchards'));
    }

    // In showManageOrchards method
    public function showManageOrchards(User $user)
    {
        $orchards = Orchard::all(); // Add orchards collection
        $selectedOrchards = $user->farmer ? $user->farmer->orchards->pluck('id')->toArray() : [];
        
        return view('admin.users.manage-orchards', compact('user', 'orchards', 'selectedOrchards'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,farmer,manager'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $profileImage = $user->profile_image;
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($profileImage) {
                Storage::disk('public')->delete($profileImage);
            }
            $profileImage = $request->file('profile_image')->store('profile-pictures', 'public');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'role' => $request->role,
            'profile_image' => $profileImage,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function updateOrchards(Request $request, User $user)
    {
        $request->validate([
            'orchards' => 'required|array',
        ]);

        if ($user->role !== 'farmer') {
            return redirect()->back()->with('error', 'Orchards can only be assigned to farmers');
        }

        $farmer = Farmer::firstOrCreate(['user_id' => $user->id]);
        $farmer->orchards()->sync($request->orchards);

        return redirect()->route('users.index')->with('success', 'Orchards updated successfully');
    }

    // In store method
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,farmer,manager'],
            'orchards' => 'required_if:role,farmer|array',
            'orchards.*' => 'integer|exists:orchards,id', // Add array element validation
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Create user first
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'profile_image' => $request->file('profile_image') ? $request->file('profile_image')->store('profile-pictures', 'public') : null,
        ]);

        // Then handle farmer relationships
        if ($request->role === 'farmer') {
            $orchards = array_filter($request->orchards ?? [], 'is_numeric');
            $farmer = Farmer::firstOrCreate(['user_id' => $user->id]);
            $farmer->orchards()->sync($orchards);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}

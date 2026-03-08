<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ModeratorController extends Controller
{
    /**
     * Display a listing of the moderators.
     */
    public function index()
    {
        $moderators = User::where('user_type', 'moderator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('backend.pages.moderator.index', compact('moderators'));
    }

    /**
     * Show the form for creating a new moderator.
     */
    public function create()
    {
        return view('backend.pages.moderator.create');
    }

    /**
     * Store a newly created moderator in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        $moderator = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'user_type' => 'moderator',
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('dashboard.moderators.index')
            ->with('success', 'Moderator created successfully.');
    }

    /**
     * Display the specified moderator.
     */
    public function show(string $id)
    {
        $moderator = User::where('user_type', 'moderator')->findOrFail($id);
        
        return view('backend.pages.moderator.show', compact('moderator'));
    }

    /**
     * Show the form for editing the specified moderator.
     */
    public function edit(string $id)
    {
        $moderator = User::where('user_type', 'moderator')->findOrFail($id);
        
        return view('backend.pages.moderator.edit', compact('moderator'));
    }

    /**
     * Update the specified moderator in storage.
     */
    public function update(Request $request, string $id)
    {
        $moderator = User::where('user_type', 'moderator')->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$moderator->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        $moderator->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $moderator->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('dashboard.moderators.index')
            ->with('success', 'Moderator updated successfully.');
    }

    /**
     * Remove the specified moderator from storage.
     */
    public function destroy(string $id)
    {
        $moderator = User::where('user_type', 'moderator')->findOrFail($id);
        $moderator->delete();

        return redirect()->route('dashboard.moderators.index')
            ->with('success', 'Moderator deleted successfully.');
    }

    /**
     * Toggle moderator status.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $moderator = User::where('user_type', 'moderator')->findOrFail($id);
        $moderator->update(['is_active' => !$moderator->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'is_active' => $moderator->is_active
        ]);
    }
}

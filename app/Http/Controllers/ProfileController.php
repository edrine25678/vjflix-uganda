<?php

namespace App\Http\Controllers;

use App\Models\Vj;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile and preferences.
     */
    public function show(): View
    {
        $user = auth()->user()->load(['preferredVj']);
        $vjs = Vj::where('is_active', true)->orderBy('stage_name')->get();

        $stats = [
            'watchlist_count' => $user->watchlists()->count(),
            'reviews_count' => $user->reviews()->count(),
            'watched_count' => $user->watchProgress()->where('completed', true)->count(),
        ];

        return view('profile.show', [
            'user' => $user,
            'vjs' => $vjs,
            'stats' => $stats,
        ]);
    }

    /**
     * Update user profile settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'preferred_language' => ['required', 'string', 'in:Luganda,English,Swahili,Runyankole,Lusoga'],
            'preferred_vj_id' => ['nullable', 'exists:vjs,id'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')->store('profiles', 'public');
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}

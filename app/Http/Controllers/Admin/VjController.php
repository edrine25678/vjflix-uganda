<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vj;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VjController extends Controller
{
    /**
     * Display a listing of Video Jockeys.
     */
    public function index(Request $request): View|Factory
    {
        $search = $request->query('search');

        $query = Vj::withCount(['movies', 'series']);

        if ($search) {
            $query->where('stage_name', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%");
        }

        $vjs = $query->orderBy('stage_name')->paginate(15);

        return view('admin.vjs.index', [
            'vjs' => $vjs,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new Video Jockey.
     */
    public function create(): View|Factory
    {
        return view('admin.vjs.create');
    }

    /**
     * Store a newly created Video Jockey in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'stage_name' => ['required', 'string', 'max:255', 'unique:vjs,stage_name'],
            'name' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'avatar_url' => ['nullable', 'url'],
            'avatar_file' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:4096'],
            'cover_url' => ['nullable', 'url'],
            'cover_file' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:4096'],
            'is_verified' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $avatar = $validated['avatar_url'] ?? null;
        if ($request->hasFile('avatar_file')) {
            $avatar = $request->file('avatar_file')->store('uploads/vjs', 'public');
        }

        $cover = $validated['cover_url'] ?? null;
        if ($request->hasFile('cover_file')) {
            $cover = $request->file('cover_file')->store('uploads/vjs', 'public');
        }

        $slug = Str::slug($validated['stage_name']);

        $vj = Vj::create([
            'stage_name' => $validated['stage_name'],
            'name' => $validated['name'] ?? $validated['stage_name'],
            'slug' => $slug,
            'biography' => $validated['biography'] ?? null,
            'specialization' => $validated['specialization'] ?? 'Action & Thrillers',
            'rating' => $validated['rating'] ?? 4.80,
            'profile_photo' => $avatar,
            'cover_photo' => $cover,
            'is_verified' => $request->boolean('is_verified'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.vjs.index')->with('success', "VJ '{$vj->stage_name}' created successfully.");
    }

    /**
     * Show the form for editing the specified Video Jockey.
     */
    public function edit(Vj $vj): View|Factory
    {
        return view('admin.vjs.edit', [
            'vj' => $vj,
        ]);
    }

    /**
     * Update the specified Video Jockey in storage.
     */
    public function update(Request $request, Vj $vj): RedirectResponse
    {
        $validated = $request->validate([
            'stage_name' => ['required', 'string', 'max:255', 'unique:vjs,stage_name,'.$vj->id],
            'name' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'avatar_url' => ['nullable', 'url'],
            'avatar_file' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:4096'],
            'cover_url' => ['nullable', 'url'],
            'cover_file' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:4096'],
            'is_verified' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $avatar = $validated['avatar_url'] ?? $vj->profile_photo;
        if ($request->hasFile('avatar_file')) {
            $avatar = $request->file('avatar_file')->store('uploads/vjs', 'public');
        }

        $cover = $validated['cover_url'] ?? $vj->cover_photo;
        if ($request->hasFile('cover_file')) {
            $cover = $request->file('cover_file')->store('uploads/vjs', 'public');
        }

        $vj->update([
            'stage_name' => $validated['stage_name'],
            'name' => $validated['name'] ?? $vj->name,
            'biography' => $validated['biography'] ?? $vj->biography,
            'specialization' => $validated['specialization'] ?? $vj->specialization,
            'rating' => $validated['rating'] ?? $vj->rating,
            'profile_photo' => $avatar,
            'cover_photo' => $cover,
            'is_verified' => $request->boolean('is_verified'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.vjs.index')->with('success', "VJ '{$vj->stage_name}' updated successfully.");
    }

    /**
     * Remove the specified Video Jockey from storage.
     */
    public function destroy(Vj $vj): RedirectResponse
    {
        $name = $vj->stage_name;
        $vj->delete();

        return redirect()->route('admin.vjs.index')->with('success', "VJ '{$name}' deleted successfully.");
    }
}

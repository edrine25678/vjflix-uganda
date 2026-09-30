<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EpisodeController extends Controller
{
    /**
     * Store a newly created episode in the given season.
     */
    public function store(Request $request, Season $season): RedirectResponse
    {
        $validated = $request->validate([
            'episode_number' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'overview' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url'],
            'video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm,video/x-matroska,video/ogg', 'max:1048576'],
            'duration' => ['nullable', 'integer'],
            'vj_id' => ['nullable', 'exists:vjs,id'],
            'is_free' => ['nullable', 'boolean'],
        ]);

        $videoPath = null;
        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('uploads/videos', 'public');
        }

        $episode = Episode::create([
            'season_id' => $season->id,
            'episode_number' => $validated['episode_number'],
            'title' => $validated['title'],
            'overview' => $validated['overview'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'video_path' => $videoPath,
            'duration' => $validated['duration'] ?? 45,
            'vj_id' => $validated['vj_id'] ?? $season->series->vj_id,
            'is_free' => $request->boolean('is_free'),
        ]);

        return back()->with('success', "Episode {$episode->episode_number} added to Season {$season->season_number}.");
    }

    /**
     * Update the specified episode.
     */
    public function update(Request $request, Episode $episode): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'overview' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url'],
            'video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm,video/x-matroska,video/ogg', 'max:1048576'],
            'duration' => ['nullable', 'integer'],
            'is_free' => ['nullable', 'boolean'],
        ]);

        $videoPath = $episode->video_path;
        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('uploads/videos', 'public');
        }

        $episode->update([
            'title' => $validated['title'],
            'overview' => $validated['overview'] ?? null,
            'video_url' => $validated['video_url'] ?? $episode->video_url,
            'video_path' => $videoPath,
            'duration' => $validated['duration'] ?? $episode->duration,
            'is_free' => $request->boolean('is_free'),
        ]);

        return back()->with('success', "Episode {$episode->episode_number} updated successfully.");
    }

    /**
     * Remove the specified episode.
     */
    public function destroy(Episode $episode): RedirectResponse
    {
        $number = $episode->episode_number;
        $episode->delete();

        return back()->with('success', "Episode {$number} deleted successfully.");
    }
}

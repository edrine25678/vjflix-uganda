<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Movie;
use App\Models\Season;
use App\Models\Series;
use App\Models\WatchProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StreamController extends Controller
{
    /**
     * Stream a Movie with HTTP 206 Partial Content byte-range support.
     */
    public function streamMovie(Request $request, string $slug)
    {
        $movie = Movie::where(function ($query) use ($slug) {
            $query->where('slug', $slug);
            if (is_numeric($slug)) {
                $query->orWhere('id', (int) $slug);
            }
        })->firstOrFail();

        // If local storage video file exists
        if ($movie->video_path && Storage::disk('public')->exists($movie->video_path)) {
            $path = Storage::disk('public')->path($movie->video_path);
            $mime = (file_exists($path) ? mime_content_type($path) : null) ?: 'video/mp4';

            return $this->serveByteRangeStream($request, $path, $mime);
        }

        // If remote URL is provided
        if (! empty($movie->video_url)) {
            return redirect()->away($movie->video_url);
        }

        // Fallback sample demo video
        return redirect()->away('https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4');
    }

    /**
     * Stream a TV Series Episode with HTTP 206 Partial Content byte-range support.
     */
    public function streamEpisode(Request $request, string $seriesSlug, int $seasonNumber, int $episodeNumber)
    {
        $series = Series::where('slug', $seriesSlug)->firstOrFail();
        $season = Season::where('series_id', $series->id)->where('season_number', $seasonNumber)->firstOrFail();
        $episode = Episode::where('season_id', $season->id)->where('episode_number', $episodeNumber)->firstOrFail();

        if ($episode->video_path && Storage::disk('public')->exists($episode->video_path)) {
            $path = Storage::disk('public')->path($episode->video_path);
            $mime = (file_exists($path) ? mime_content_type($path) : null) ?: 'video/mp4';

            return $this->serveByteRangeStream($request, $path, $mime);
        }

        if (! empty($episode->video_url)) {
            return redirect()->away($episode->video_url);
        }

        return redirect()->away('https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4');
    }

    /**
     * Download a Movie file.
     */
    public function downloadMovie(Request $request, string $slug)
    {
        $movie = Movie::where(function ($query) use ($slug) {
            $query->where('slug', $slug);
            if (is_numeric($slug)) {
                $query->orWhere('id', (int) $slug);
            }
        })->firstOrFail();

        $movie->increment('views');

        $cleanTitle = \Illuminate\Support\Str::slug($movie->title) . '-Luganda-VJFlix.mp4';

        if ($movie->video_path && Storage::disk('public')->exists($movie->video_path)) {
            return Storage::disk('public')->download($movie->video_path, $cleanTitle);
        }

        if (! empty($movie->video_url)) {
            return redirect()->away($movie->video_url);
        }

        return redirect()->away('https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4');
    }

    /**
     * Download a TV Series Episode file.
     */
    public function downloadEpisode(Request $request, string $seriesSlug, int $seasonNumber, int $episodeNumber)
    {
        $series = Series::where('slug', $seriesSlug)->firstOrFail();
        $season = Season::where('series_id', $series->id)->where('season_number', $seasonNumber)->firstOrFail();
        $episode = Episode::where('season_id', $season->id)->where('episode_number', $episodeNumber)->firstOrFail();

        $episode->increment('views');

        $cleanTitle = \Illuminate\Support\Str::slug($series->title . '-S' . $seasonNumber . 'E' . $episodeNumber) . '-Luganda-VJFlix.mp4';

        if ($episode->video_path && Storage::disk('public')->exists($episode->video_path)) {
            return Storage::disk('public')->download($episode->video_path, $cleanTitle);
        }

        if (! empty($episode->video_url)) {
            return redirect()->away($episode->video_url);
        }

        return redirect()->away('https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4');
    }

    /**
     * Save watch progress (called periodically via AJAX during video playback).
     */
    public function updateProgress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'watchable_type' => ['required', 'string', 'in:movie,episode'],
            'watchable_id' => ['required', 'integer'],
            'progress_seconds' => ['required', 'integer', 'min:0'],
            'duration_seconds' => ['required', 'integer', 'min:1'],
        ]);

        $modelClass = $validated['watchable_type'] === 'movie'
            ? Movie::class
            : Episode::class;

        // Ensure the media item exists
        $modelClass::findOrFail($validated['watchable_id']);

        $completed = ($validated['progress_seconds'] / $validated['duration_seconds']) >= 0.90;

        $progress = WatchProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'watchable_type' => $modelClass,
                'watchable_id' => $validated['watchable_id'],
            ],
            [
                'progress_seconds' => $validated['progress_seconds'],
                'duration_seconds' => $validated['duration_seconds'],
                'completed' => $completed,
                'last_watched_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'progress_seconds' => $progress->progress_seconds,
            'percentage' => $progress->percentage(),
            'completed' => $progress->completed,
            'remaining' => $progress->remainingFormatted(),
        ]);
    }

    /**
     * Retrieve stored watch progress for resuming playback.
     */
    public function getProgress(Request $request, string $type, int $id): JsonResponse
    {
        $modelClass = $type === 'movie' ? Movie::class : Episode::class;

        $progress = WatchProgress::where('user_id', auth()->id())
            ->where('watchable_type', $modelClass)
            ->where('watchable_id', $id)
            ->first();

        if (! $progress) {
            return response()->json([
                'has_progress' => false,
                'progress_seconds' => 0,
            ]);
        }

        return response()->json([
            'has_progress' => ! $progress->completed && $progress->progress_seconds > 10,
            'progress_seconds' => $progress->progress_seconds,
            'percentage' => $progress->percentage(),
            'completed' => $progress->completed,
            'formatted' => $progress->progressFormatted(),
        ]);
    }

    /**
     * Helper to stream local media files supporting HTTP 206 Range headers.
     */
    protected function serveByteRangeStream(Request $request, string $filePath, string $mimeType)
    {
        $fileSize = filesize($filePath);
        $file = fopen($filePath, 'rb');

        $start = 0;
        $end = $fileSize - 1;
        $status = 200;
        $headers = [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
        ];

        if ($request->headers->has('Range')) {
            $range = $request->header('Range');
            if (preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
                $start = (int) $matches[1];
                if (! empty($matches[2])) {
                    $end = (int) $matches[2];
                }
                $status = 206;
                $headers['Content-Range'] = sprintf('bytes %d-%d/%d', $start, $end, $fileSize);
            }
        }

        $length = $end - $start + 1;
        $headers['Content-Length'] = $length;

        return response()->stream(function () use ($file, $start, $length) {
            fseek($file, $start);
            $chunkSize = 1024 * 64; // 64KB buffer
            $bytesSent = 0;

            while (! feof($file) && $bytesSent < $length && connection_status() === CONNECTION_NORMAL) {
                $bytesToRead = min($chunkSize, $length - $bytesSent);
                echo fread($file, $bytesToRead);
                flush();
                $bytesSent += $bytesToRead;
            }

            fclose($file);
        }, $status, $headers);
    }
}

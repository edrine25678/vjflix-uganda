<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'season_id',
        'episode_number',
        'title',
        'overview',
        'video_url',
        'video_path',
        'duration',
        'thumbnail',
        'vj_id',
        'views',
        'is_free',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'episode_number' => 'integer',
        'duration' => 'integer',
        'views' => 'integer',
        'is_free' => 'boolean',
    ];

    /**
     * Parent season.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Video Jockey translation for this specific episode.
     */
    public function vj(): BelongsTo
    {
        return $this->belongsTo(Vj::class);
    }

    /**
     * Formatted duration string (e.g. 45m).
     */
    public function durationFormatted(): string
    {
        if (! $this->duration) {
            return '45m';
        }

        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    /**
     * Get the streamable URL for the video player.
     */
    public function streamUrl(): string
    {
        if (! empty($this->video_path)) {
            return asset('storage/' . $this->video_path);
        }

        if (! empty($this->video_url)) {
            return $this->video_url;
        }

        // Demo sample video if no link provided yet
        return 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4';
    }

    /**
     * Get the episode thumbnail.
     */
    public function thumbnailUrl(): string
    {
        if (! empty($this->thumbnail)) {
            if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
                return $this->thumbnail;
            }

            return asset('storage/' . $this->thumbnail);
        }

        if ($this->season && $this->season->series) {
            return $this->season->series->backdropUrl();
        }

        return 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?auto=format&fit=crop&w=600&q=80';
    }
}

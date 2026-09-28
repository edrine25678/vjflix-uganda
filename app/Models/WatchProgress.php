<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WatchProgress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'watch_progress';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'watchable_type',
        'watchable_id',
        'progress_seconds',
        'duration_seconds',
        'completed',
        'last_watched_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'progress_seconds' => 'integer',
        'duration_seconds' => 'integer',
        'completed' => 'boolean',
        'last_watched_at' => 'datetime',
    ];

    /**
     * The polymorphic watchable target (Movie or Episode).
     */
    public function watchable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The viewer who owns this watch progress.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Percentage of media watched (0 - 100).
     */
    public function percentage(): int
    {
        if ($this->duration_seconds <= 0) {
            return 0;
        }

        $percentage = (int) round(($this->progress_seconds / $this->duration_seconds) * 100);

        return min(100, max(0, $percentage));
    }

    /**
     * Format current progress as MM:SS or HH:MM:SS.
     */
    public function progressFormatted(): string
    {
        $hours = floor($this->progress_seconds / 3600);
        $minutes = floor(($this->progress_seconds % 3600) / 60);
        $seconds = $this->progress_seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Human-readable remaining watch time (e.g. "32 mins left").
     */
    public function remainingFormatted(): string
    {
        $remaining = max(0, $this->duration_seconds - $this->progress_seconds);
        $minutes = ceil($remaining / 60);

        if ($minutes <= 1) {
            return 'Less than a minute left';
        }

        return "{$minutes} mins left";
    }
}

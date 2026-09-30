<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HeroSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'vj_name',
        'release_year',
        'rating',
        'badge_text',
        'synopsis',
        'backdrop_url',
        'backdrop_path',
        'watch_url',
        'download_url',
        'movie_id',
        'series_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function backdropUrl(): string
    {
        if ($this->backdrop_path && Storage::disk('public')->exists($this->backdrop_path)) {
            return Storage::disk('public')->url($this->backdrop_path);
        }

        if (! empty($this->backdrop_url)) {
            return $this->backdrop_url;
        }

        if ($this->movie) {
            return $this->movie->backdropUrl();
        }

        if ($this->series) {
            return $this->series->backdropUrl();
        }

        return 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?auto=format&fit=crop&w=1920&q=80';
    }

    public function getResolvedVjAttribute(): string
    {
        if (! empty($this->vj_name)) {
            return $this->vj_name;
        }

        if ($this->movie && $this->movie->vj) {
            return $this->movie->vj->stage_name;
        }

        if ($this->series && $this->series->vj) {
            return $this->series->vj->stage_name;
        }

        return 'VJ Translated';
    }

    public function getResolvedYearAttribute(): string
    {
        if (! empty($this->release_year)) {
            return (string) $this->release_year;
        }

        if ($this->movie && $this->movie->release_year) {
            return (string) $this->movie->release_year;
        }

        if ($this->series && $this->series->first_air_year) {
            return (string) $this->series->first_air_year;
        }

        return (string) date('Y');
    }

    public function getResolvedRatingAttribute(): string
    {
        if (! empty($this->rating)) {
            return (string) $this->rating;
        }

        if ($this->movie && $this->movie->average_rating) {
            return number_format($this->movie->average_rating, 1);
        }

        if ($this->series && $this->series->average_rating) {
            return number_format($this->series->average_rating, 1);
        }

        return '4.8';
    }

    public function getResolvedSynopsisAttribute(): string
    {
        if (! empty($this->synopsis)) {
            return $this->synopsis;
        }

        if ($this->movie) {
            return $this->movie->synopsis ?: ($this->movie->description ?: 'Exclusive Luganda translated blockbuster available to stream.');
        }

        if ($this->series) {
            return $this->series->synopsis ?: ($this->series->description ?: 'Exclusive Luganda translated TV series available to stream.');
        }

        return 'Exclusive Luganda translated blockbuster available to stream.';
    }

    public function getResolvedWatchUrlAttribute(): string
    {
        if (! empty($this->watch_url)) {
            return $this->watch_url;
        }

        if ($this->movie) {
            return route('movies.show', $this->movie->slug);
        }

        if ($this->series) {
            return route('series.show', $this->series->slug);
        }

        return route('vjflix.index');
    }

    public function getResolvedDownloadUrlAttribute(): ?string
    {
        if (! empty($this->download_url)) {
            return $this->download_url;
        }

        if ($this->movie) {
            return route('movies.download', $this->movie->slug);
        }

        return null;
    }
}

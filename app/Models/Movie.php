<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'original_title',
        'description',
        'synopsis',
        'poster',
        'backdrop',
        'trailer_url',
        'video_url',
        'video_path',
        'duration',
        'release_year',
        'original_language',
        'translated_language',
        'vj_id',
        'country_of_origin',
        'age_rating',
        'status',
        'featured',
        'trending',
        'views',
        'average_rating',
        'ratings_count',
        'published_at',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'trending' => 'boolean',
        'published_at' => 'datetime',
        'average_rating' => 'decimal:2',
        'release_year' => 'integer',
        'duration' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function (Movie $movie) {
            if (empty($movie->slug)) {
                $movie->slug = Str::slug($movie->title) . '-' . Str::random(5);
            }
            if (empty($movie->published_at) && $movie->status === 'published') {
                $movie->published_at = now();
            }
        });
    }

    public function vj(): BelongsTo
    {
        return $this->belongsTo(Vj::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    public function posterUrl(): string
    {
        if ($this->poster) {
            if (Str::startsWith($this->poster, ['http://', 'https://'])) {
                return $this->poster;
            }
            return asset('storage/' . $this->poster);
        }

        return 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&w=600&q=80';
    }

    public function backdropUrl(): string
    {
        if ($this->backdrop) {
            if (Str::startsWith($this->backdrop, ['http://', 'https://'])) {
                return $this->backdrop;
            }
            return asset('storage/' . $this->backdrop);
        }

        return $this->posterUrl();
    }

    public function durationFormatted(): string
    {
        if (! $this->duration) {
            return 'N/A';
        }

        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        }

        return "{$minutes}m";
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true)->published();
    }

    public function scopeTrending(Builder $query): Builder
    {
        return $query->where('trending', true)->published();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Series extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'synopsis',
        'description',
        'poster',
        'backdrop',
        'vj_id',
        'language_id',
        'first_air_year',
        'status',
        'featured',
        'trending',
        'views',
        'average_rating',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'featured' => 'boolean',
        'trending' => 'boolean',
        'views' => 'integer',
        'first_air_year' => 'integer',
        'average_rating' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($series) {
            if (empty($series->slug)) {
                $series->slug = Str::slug($series->title);
            }
        });
    }

    /**
     * Ugandan Video Jockey who voiced / translated this series.
     */
    public function vj(): BelongsTo
    {
        return $this->belongsTo(Vj::class);
    }

    /**
     * Primary spoken language / translation dialect.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Seasons belonging to this series.
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class)->orderBy('season_number');
    }

    /**
     * Episodes across all seasons of this series.
     */
    public function episodes(): HasManyThrough
    {
        return $this->hasManyThrough(Episode::class, Season::class);
    }

    /**
     * Genres associated with this series.
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'genre_series');
    }

    /**
     * Scope published series.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope featured series for hero showcases.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true)->where('status', 'published');
    }

    /**
     * Scope trending series.
     */
    public function scopeTrending(Builder $query): Builder
    {
        return $query->where('trending', true)->where('status', 'published');
    }

    /**
     * Get the full poster image URL.
     */
    public function posterUrl(): string
    {
        if (! empty($this->poster)) {
            if (str_starts_with($this->poster, 'http://') || str_starts_with($this->poster, 'https://')) {
                return $this->poster;
            }

            return asset('storage/' . $this->poster);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->title) . '&background=f59e0b&color=000&size=500';
    }

    /**
     * Get the backdrop banner image URL.
     */
    public function backdropUrl(): string
    {
        if (! empty($this->backdrop)) {
            if (str_starts_with($this->backdrop, 'http://') || str_starts_with($this->backdrop, 'https://')) {
                return $this->backdrop;
            }

            return asset('storage/' . $this->backdrop);
        }

        return $this->posterUrl();
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable')->approved()->latest();
    }

    public function watchlists(): MorphMany
    {
        return $this->morphMany(Watchlist::class, 'watchable');
    }

    public function recalculateRating(): void
    {
        $avg = $this->reviews()->avg('rating') ?: 0;
        $this->update([
            'average_rating' => round($avg, 2),
        ]);
    }
}

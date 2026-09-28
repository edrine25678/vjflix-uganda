<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'series_id',
        'season_number',
        'title',
        'overview',
        'poster',
        'release_year',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'season_number' => 'integer',
        'release_year' => 'integer',
    ];

    /**
     * The parent TV series.
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    /**
     * Episodes within this season.
     */
    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class)->orderBy('episode_number');
    }

    /**
     * Get the poster image URL or fallback to series poster.
     */
    public function posterUrl(): string
    {
        if (! empty($this->poster)) {
            if (str_starts_with($this->poster, 'http://') || str_starts_with($this->poster, 'https://')) {
                return $this->poster;
            }

            return asset('storage/'.$this->poster);
        }

        return $this->series ? $this->series->posterUrl() : 'https://ui-avatars.com/api/?name=Season+'.$this->season_number;
    }
}

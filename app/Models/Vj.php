<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Vj extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'stage_name',
        'biography',
        'profile_photo',
        'cover_photo',
        'specialization',
        'social_links',
        'is_verified',
        'is_active',
        'views_count',
        'rating',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function (Vj $vj) {
            if (empty($vj->slug)) {
                $vj->slug = Str::slug($vj->stage_name ?: $vj->name);
            }
        });
    }

    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class);
    }

    public function series(): HasMany
    {
        return $this->hasMany(Series::class);
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    public function avatarUrl(): string
    {
        if ($this->profile_photo) {
            if (Str::startsWith($this->profile_photo, ['http://', 'https://'])) {
                return $this->profile_photo;
            }

            return asset('storage/'.$this->profile_photo);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->stage_name).'&background=f59e0b&color=000&size=256';
    }

    public function coverUrl(): string
    {
        if ($this->cover_photo) {
            if (Str::startsWith($this->cover_photo, ['http://', 'https://'])) {
                return $this->cover_photo;
            }

            return asset('storage/'.$this->cover_photo);
        }

        return asset('img/home-full-page.png');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

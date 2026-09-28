<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_ugx',
        'interval_unit',
        'interval_count',
        'duration_days',
        'features',
        'badge',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price_ugx' => 'integer',
        'duration_days' => 'integer',
        'sort_order' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function (Plan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function formattedPrice(): string
    {
        return 'UGX '.number_format($this->price_ugx);
    }

    public function durationLabel(): string
    {
        return match ($this->interval_unit) {
            'day' => $this->interval_count === 1 ? '24 Hours' : "{$this->interval_count} Days",
            'week' => $this->interval_count === 1 ? '7 Days' : "{$this->interval_count} Weeks",
            'month' => $this->interval_count === 1 ? '1 Month' : "{$this->interval_count} Months",
            'year' => $this->interval_count === 1 ? '1 Year' : "{$this->interval_count} Years",
            default => "{$this->duration_days} Days",
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'profile_photo',
        'preferred_language',
        'is_active',
        'provider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Mutator to bcrypt passwords if raw string passed.
     */
    public function setPasswordAttribute($password)
    {
        if (! empty($password)) {
            $this->attributes['password'] = password_needs_rehash($password, PASSWORD_BCRYPT)
                ? bcrypt($password)
                : $password;
        }
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $role): bool
    {
        if ($this->role === $role) {
            return true;
        }

        return $this->roles->contains('name', $role);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'content_manager'], true)
            || $this->username === 'admin'
            || $this->hasRole('super_admin')
            || $this->hasRole('admin');
    }

    public function avatarUrl(): string
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=f59e0b&color=000';
    }
}

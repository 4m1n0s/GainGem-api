<?php

namespace App\Models;

use App\Casts\Bcrypt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'username',
        'password',
        'email',
        'points',
        'total_points_earned',
        'role',
        'ip',
        'referred_by',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => Bcrypt::class,
        'points' => 'integer',
        'total_points_earned' => 'integer',
        'email_verified_at' => 'datetime',
        'banned_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function isAdminRole(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUserRole(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by');
    }

    public function referredUsers(): HasMany
    {
        return $this->hasMany(self::class, 'referred_by');
    }

    public function urlTokens(): HasMany
    {
        return $this->hasMany(UrlToken::class);
    }

    public function markUnreadNotificationAsRead(string $token): void
    {
        $unreadNotification = $this->unreadNotifications()->where('data->token', $token)->first();

        if ($unreadNotification) {
            $unreadNotification->markAsRead();
        }
    }
}

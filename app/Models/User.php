<?php

namespace App\Models;

use App\Casts\Bcrypt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'username',
        'password',
        'email',
        'profile_image',
        'role',
        'ip',
        'referred_by',
        'referral_token',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => Bcrypt::class,
        'email_verified_at' => 'datetime',
        'banned_at' => 'datetime',
    ];

    protected $appends = [
        'points',
        'total_points',
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

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function completedTasks(): HasMany
    {
        return $this->hasMany(CompletedTask::class);
    }

    public function urlTokens(): HasMany
    {
        return $this->hasMany(UrlToken::class);
    }

    public function getPointsAttribute(): Float
    {
        return $this->total_points - $this->transactions()->sum('points');
    }

    public function getTotalPointsAttribute(): Float
    {
        return $this->completedTasks()->sum('points');
    }

    public function markUnreadNotificationAsRead(int $urlTokenId): void
    {
        $unreadNotification = $this->unreadNotifications()->where('data->url_token_id', $urlTokenId)->first();

        if ($unreadNotification) {
            $unreadNotification->markAsRead();
        }
    }
}

<?php

namespace App\Models;

use App\Casts\Bcrypt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
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
        'available_points',
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

    public function scopeWithTotalPoints(Builder $query): void
    {
        $query->withSum('completedTasks as total_points', 'points');
    }

    public function scopeWithWastedPoints(Builder $query): void
    {
        $query->withSum('transactions as wasted_points', 'points');
    }

    public function scopeWithAvailablePoints(Builder $query): void
    {
        $query->withTotalPoints()->withWastedPoints();
    }

    public function withTotalPoints(): self
    {
        return $this->loadSum('completedTasks as total_points', 'points');
    }

    public function withWastedPoints(): self
    {
        return $this->loadSum('transactions as wasted_points', 'points');
    }

    public function withAvailablePoints(): self
    {
        return $this->withTotalPoints()->withWastedPoints();
    }

    public function getTotalPointsAttribute(): ?float
    {
        if (! Arr::has($this->getAttributes(), 'total_points')) {
            return null;
        }

        return (float) $this->getAttributes()['total_points'];
    }

    public function getWastedPointsAttribute(): ?float
    {
        if (! Arr::has($this->getAttributes(), 'wasted_points')) {
            return null;
        }

        return (float) $this->getAttributes()['wasted_points'];
    }

    public function getAvailablePointsAttribute(): ?float
    {
        $attributes = $this->getAttributes();
        if (! Arr::has($attributes, 'total_points') || ! Arr::has($attributes, 'wasted_points')) {
            return null;
        }

        return $this->total_points - $this->wasted_points;
    }

    public function markUnreadNotificationAsRead(int $urlTokenId): void
    {
        $unreadNotification = $this->unreadNotifications()->where('data->url_token_id', $urlTokenId)->first();

        if ($unreadNotification) {
            $unreadNotification->markAsRead();
        }
    }
}

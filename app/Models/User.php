<?php

namespace App\Models;

use App\Builders\UserBuilder;
use App\Casts\Bcrypt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User.
 *
 * @property int $id
 * @property string $username
 * @property \App\Casts\Bcrypt $password
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $profile_image
 * @property string $role
 * @property string|null $ip
 * @property int|null $referred_by
 * @property string $referral_token
 * @property \Illuminate\Support\Carbon|null $banned_at
 * @property string|null $ban_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CompletedTask[] $completedTasks
 * @property-read int|null $completed_tasks_count
 * @property-read float|null $available_points
 * @property-read float|null $total_points
 * @property-read float|null $wasted_points
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\User|null $referredBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $referredUsers
 * @property-read int|null $referred_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UrlToken[] $urlTokens
 * @property-read int|null $url_tokens_count
 * @method static \App\Builders\UserBuilder|\App\Models\User newModelQuery()
 * @method static \App\Builders\UserBuilder|\App\Models\User newQuery()
 * @method static \App\Builders\UserBuilder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereBanReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereBannedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereProfileImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereReferralToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereReferredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUsername($value)
 * @mixin \Eloquent
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

    public static function query() : UserBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query)
    {
        return new UserBuilder($query);
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

    public function markVerificationNotificationAsRead(int $urlTokenId): void
    {
        /** @var DatabaseNotification|null $unreadNotification */
        $unreadNotification = $this->unreadNotifications()->where('data->url_token_id', $urlTokenId)->first();

        if ($unreadNotification) {
            $unreadNotification->markAsRead();
        }
    }
}

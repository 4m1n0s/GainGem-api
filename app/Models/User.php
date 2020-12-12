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
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User.
 *
 * @property int $id
 * @property string $username
 * @property \App\Casts\Bcrypt $password
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $profile_image
 * @property string $role
 * @property string|null $ip
 * @property int|null $referred_by
 * @property string $referral_token
 * @property \Illuminate\Support\Carbon|null $banned_at
 * @property string|null $ban_reason
 * @property \Illuminate\Support\Carbon|null $registered_giveaway_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CompletedTask[] $completedTasks
 * @property-read int|null $completed_tasks_count
 * @property-read float|null $available_points
 * @property-read string|null $formatted_banned_at
 * @property-read string|null $formatted_created_at
 * @property-read string|null $formatted_email_verified_at
 * @property-read string $profile_image_url
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
 * @method static \App\Builders\UserBuilder|\App\Models\User search(array $columns, string $term)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereBanReason($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereBannedAt($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereCreatedAt($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereEmail($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereEmailVerifiedAt($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereId($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereIp($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User wherePassword($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereProfileImage($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereReferralToken($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereReferredBy($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereRegisteredGiveawayAt($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereRole($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereUpdatedAt($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereUsername($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User withAvailablePoints()
 * @method static \App\Builders\UserBuilder|\App\Models\User withTotalPoints()
 * @method static \App\Builders\UserBuilder|\App\Models\User withWastedPoints()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    const ROLE_USER = 'user';
    const ROLE_SUPPLIER = 'supplier';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPER_ADMIN = 'super_admin';

    protected $fillable = [
        'username',
        'password',
        'email',
        'email_verified_at',
        'profile_image',
        'role',
        'ip',
        'referred_by',
        'referral_token',
        'banned_at',
        'ban_reason',
        'registered_giveaway_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => Bcrypt::class,
        'email_verified_at' => 'datetime',
        'banned_at' => 'datetime',
        'registered_giveaway_at' => 'datetime',
    ];

    protected $appends = [
        'available_points',
        'profile_image_url',
        'formatted_created_at',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function newEloquentBuilder($query)
    {
        return new UserBuilder($query);
    }

    public function isSuperAdminRole(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdminRole(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSupplierRole(): bool
    {
        return $this->role === self::ROLE_SUPPLIER;
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

        return $this->getAttributes()['total_points'];
    }

    public function getWastedPointsAttribute(): ?float
    {
        if (! Arr::has($this->getAttributes(), 'wasted_points')) {
            return null;
        }

        return $this->getAttributes()['wasted_points'];
    }

    public function getAvailablePointsAttribute(): ?float
    {
        $attributes = $this->getAttributes();
        if (! Arr::has($attributes, 'total_points') || ! Arr::has($attributes, 'wasted_points')) {
            return null;
        }

        return $this->total_points - $this->wasted_points;
    }

    public function getProfileImageUrlAttribute(): string
    {
        if (! $this->profile_image || ! Storage::exists($this->profile_image)) {
            return asset('storage/assets/user.png');
        }

        return Storage::url($this->profile_image);
    }

    public function markVerificationNotificationAsRead(int $urlTokenId): void
    {
        /** @var DatabaseNotification|null $unreadNotification */
        $unreadNotification = $this->unreadNotifications()->where('data->url_token_id', $urlTokenId)->first();

        if ($unreadNotification) {
            $unreadNotification->markAsRead();
        }
    }

    public function getFormattedAvailablePointsAttribute(): string
    {
        return points_format($this->available_points);
    }

    public function getFormattedTotalPointsAttribute(): string
    {
        return points_format($this->total_points);
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return optional($this->created_at)->format('M d Y');
    }

    public function getFormattedEmailVerifiedAtAttribute(): ?string
    {
        return optional($this->email_verified_at)->format('M d Y');
    }

    public function getFormattedBannedAtAttribute(): ?string
    {
        return optional($this->banned_at)->format('M d Y');
    }
}

<?php

namespace App\Models;

use App\Casts\Bcrypt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use phpDocumentor\Reflection\Types\Float_;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $username
 * @property Bcrypt $password
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $profile_image
 * @property string $role
 * @property string|null $ip
 * @property int|null $referred_by
 * @property \Illuminate\Support\Carbon|null $banned_at
 * @property string|null $ban_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CompletedTask[] $completedTasks
 * @property-read int|null $completed_tasks_count
 * @property-read float $points
 * @property-read float $total_points
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read User|null $referredBy
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $referredUsers
 * @property-read int|null $referred_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UrlToken[] $urlTokens
 * @property-read int|null $url_tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBanReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBannedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfileImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereReferredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
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
        'total_points'
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

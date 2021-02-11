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
use Illuminate\Support\Facades\Cache;
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
 * @property string|null $robux_rate
 * @property \Illuminate\Support\Carbon|null $two_factor_enabled_at
 * @property string|null $two_factor_code
 * @property \Illuminate\Support\Carbon|null $two_factor_expires_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CompletedTask[] $completedTasks
 * @property-read int|null $completed_tasks_count
 * @property-read float|null $available_points
 * @property-read string $formatted_available_points
 * @property-read string|null $formatted_banned_at
 * @property-read string|null $formatted_created_at
 * @property-read string|null $formatted_email_verified_at
 * @property-read float|null $formatted_robux_rate
 * @property-read string $formatted_total_points
 * @property-read string $profile_image_url
 * @property-read float|null $total_points
 * @property-read float $total_supplier_withdrawals
 * @property-read float|null $wasted_points
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SupplierPayment[] $paidSupplierPayments
 * @property-read int|null $paid_supplier_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SupplierPayment[] $pendingOrPaidSupplierPayments
 * @property-read int|null $pending_or_paid_supplier_payments_count
 * @property-read \App\Models\User|null $referredBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $referredUsers
 * @property-read int|null $referred_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RobuxGroup[] $robuxGroups
 * @property-read int|null $robux_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SupplierPayment[] $supplierPayments
 * @property-read int|null $supplier_payments_count
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
 * @method static \App\Builders\UserBuilder|\App\Models\User whereRobuxRate($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereRole($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereTwoFactorCode($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereTwoFactorEnabledAt($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereTwoFactorExpiresAt($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereUpdatedAt($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User whereUsername($value)
 * @method static \App\Builders\UserBuilder|\App\Models\User withAvailablePoints()
 * @method static \App\Builders\UserBuilder|\App\Models\User withTotalPoints()
 * @method static \App\Builders\UserBuilder|\App\Models\User withTotalSupplierWithdrawals()
 * @method static \App\Builders\UserBuilder|\App\Models\User withWastedPoints()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    const ROLE_USER = 'user';
    const ROLE_SUPPLIER = 'supplier';
    const ROLE_SPONSOR = 'sponsor';
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
        'robux_rate',
        'two_factor_enabled_at',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => Bcrypt::class,
        'email_verified_at' => 'datetime',
        'banned_at' => 'datetime',
        'registered_giveaway_at' => 'datetime',
        'two_factor_enabled_at' => 'datetime',
        'two_factor_expires_at' => 'datetime',
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

    public function isSponsorRole(): bool
    {
        return $this->role === self::ROLE_SPONSOR;
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

    public function robuxGroups(): HasMany
    {
        return $this->hasMany(RobuxGroup::class, 'supplier_user_id');
    }

    public function supplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class, 'supplier_user_id');
    }

    public function pendingOrPaidSupplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class, 'supplier_user_id')->whereIn('status', [SupplierPayment::STATUS_PAID, SupplierPayment::STATUS_PENDING]);
    }

    public function paidSupplierPayments(): HasMany
    {
        return $this->supplierPayments()->where('status', SupplierPayment::STATUS_PAID);
    }

    public function loadTotalPoints(): self
    {
        return $this->loadSum('completedTasks as total_points', 'points');
    }

    public function loadWastedPoints(): self
    {
        return $this->loadSum('transactions as wasted_points', 'points');
    }

    public function loadAvailablePoints(): self
    {
        return $this->loadTotalPoints()->loadWastedPoints();
    }

    public function loadTotalSupplierWithdrawals(): self
    {
        return $this->loadSum('paidSupplierPayments as total_supplier_withdrawals', 'value');
    }

    public function loadTotalPendingOrPaidSupplierWithdrawals(): self
    {
        return $this->loadSum('pendingOrPaidSupplierPayments as total_supplier_withdrawals', 'value');
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

    public function getTotalSupplierWithdrawalsAttribute(): float
    {
        if (! Arr::has($this->getAttributes(), 'total_supplier_withdrawals')) {
            return 0;
        }

        return $this->getAttributes()['total_supplier_withdrawals'] ?? 0;
    }

    public function getProfileImageUrlAttribute(): string
    {
        if (! $this->profile_image || ! Storage::exists($this->profile_image)) {
            return asset('storage/assets/user.png');
        }

        return Storage::url($this->profile_image);
    }

    public function markNotificationAsRead(int $urlTokenId): void
    {
        /** @var DatabaseNotification|null $unreadNotification */
        $unreadNotification = $this->unreadNotifications()->where('data->url_token_id', $urlTokenId)->first();

        if ($unreadNotification) {
            $unreadNotification->markAsRead();
        }
    }

    public function generateTwoFactorCode(): void
    {
        $this->update([
            'two_factor_code' => rand(100000, 999999),
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);
    }

    public function resetTwoFactorCode(): void
    {
        $this->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);
    }

    public function getFormattedAvailablePointsAttribute(): string
    {
        return currency_format($this->available_points);
    }

    public function getFormattedTotalPointsAttribute(): string
    {
        return currency_format($this->total_points);
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

    public function getFormattedRobuxRateAttribute(): ?float
    {
        $rate = $this->robux_rate ?? Cache::get('robux-supplier-rate');

        return $rate * 1000;
    }
}

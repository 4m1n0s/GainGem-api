<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\CompletedTask
 *
 * @property int $id
 * @property string $type
 * @property string|null $provider
 * @property int $user_id
 * @property float $points
 * @property array|null $data
 * @property int|null $coupon_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Coupon|null $coupon
 * @property-read \App\Models\User $user
 * @method static Builder|CompletedTask newModelQuery()
 * @method static Builder|CompletedTask newQuery()
 * @method static Builder|CompletedTask query()
 * @method static Builder|CompletedTask whereCouponId($value)
 * @method static Builder|CompletedTask whereCreatedAt($value)
 * @method static Builder|CompletedTask whereData($value)
 * @method static Builder|CompletedTask whereId($value)
 * @method static Builder|CompletedTask wherePoints($value)
 * @method static Builder|CompletedTask whereProvider($value)
 * @method static Builder|CompletedTask whereType($value)
 * @method static Builder|CompletedTask whereTypesAvailableForReferring()
 * @method static Builder|CompletedTask whereUpdatedAt($value)
 * @method static Builder|CompletedTask whereUserId($value)
 * @mixin \Eloquent
 */
class CompletedTask extends Model
{
    use HasFactory;

    const TYPE_EMAIL_VERIFICATION = 'email_verification';
    const TYPE_GIVEAWAY = 'giveaway';
    const TYPE_COUPON = 'coupon';
    const TYPE_REFERRAL_INCOME = 'referral_income';

    protected $fillable = [
        'type',
        'provider',
        'user_id',
        'points',
        'data',
        'coupon_id',
    ];

    protected $casts = [
        'data' => 'array',
        'points' => 'float',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isTypeEmailVerification(): bool
    {
        return $this->type === self::TYPE_EMAIL_VERIFICATION;
    }

    public function isTypeGiveAway(): bool
    {
        return $this->type === self::TYPE_GIVEAWAY;
    }

    public function isTypeCoupon(): bool
    {
        return $this->type === self::TYPE_COUPON;
    }

    public function isTypeReferralIncome(): bool
    {
        return $this->type === self::TYPE_REFERRAL_INCOME;
    }

    public function isTypeAvailableForReferring(): bool
    {
        return ! $this->isTypeCoupon() && ! $this->isTypeReferralIncome();
    }

    public function scopeWhereTypesAvailableForReferring(Builder $query): Builder
    {
        return $query->whereNotIn('type', [self::TYPE_REFERRAL_INCOME, self::TYPE_COUPON]);
    }
}
<?php

namespace App\Models;

use App\Builders\CompletedTaskBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\CompletedTask.
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
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask newModelQuery()
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask newQuery()
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereUserId($value)
 * @mixin \Eloquent
 */
class CompletedTask extends Model
{
    use HasFactory;

    const TYPE_OFFER = 'offer';
    const TYPE_EMAIL_VERIFICATION = 'email_verification';
    const TYPE_GIVEAWAY = 'giveaway';
    const TYPE_TASK = 'task';
    const TYPE_COUPON = 'coupon';
    const TYPE_REFERRAL_INCOME = 'referral_income';

    const POINTS_EMAIL_VERIFICATION = 2;

    const COMMISSION_PERCENT_REFERRAL = 0.1;

    const TASKS = [
        1 => 0.25,
        3 => 1.00,
        5 => 2.00,
        10 => 4.00,
    ];

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

    public function newEloquentBuilder($query)
    {
        return new CompletedTaskBuilder($query);
    }

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

    public function isTypeOffer(): bool
    {
        return $this->type === self::TYPE_OFFER;
    }

    public function isTypeGiveAway(): bool
    {
        return $this->type === self::TYPE_GIVEAWAY;
    }

    public function isTypeTask(): bool
    {
        return $this->type === self::TYPE_TASK;
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
        return ! $this->isTypeCoupon() && ! $this->isTypeReferralIncome() && ! $this->isTypeTask();
    }
}

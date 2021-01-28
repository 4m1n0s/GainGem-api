<?php

namespace App\Models;

use App\Builders\CompletedTaskBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

/**
 * App\Models\CompletedTask.
 *
 * @property int $id
 * @property string $type
 * @property string|null $provider
 * @property int|null $user_id
 * @property float $points
 * @property array|null $data
 * @property int|null $coupon_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Coupon|null $coupon
 * @property-read string|null $formatted_created_at
 * @property-read string $formatted_points
 * @property-read string $formatted_type
 * @property-read int|null $offers_count
 * @property-read \App\Models\User|null $user
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask availableForReferring()
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask hourlyOffers()
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask newModelQuery()
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask newQuery()
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask query()
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask todayDailyTasks()
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask todayOffers()
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask whereCouponId($value)
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask whereCreatedAt($value)
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask whereData($value)
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask whereId($value)
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask wherePoints($value)
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask whereProvider($value)
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask whereType($value)
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask whereUpdatedAt($value)
 * @method static \App\Builders\CompletedTaskBuilder|\App\Models\CompletedTask whereUserId($value)
 * @mixin \Eloquent
 */
class CompletedTask extends Model
{
    use HasFactory;

    const TYPE_OFFER = 'offer';
    const TYPE_EMAIL_VERIFICATION = 'email_verification';
    const TYPE_GIVEAWAY = 'giveaway';
    const TYPE_DAILY_TASK = 'daily_task';
    const TYPE_SOCIAL_MEDIA = 'social_media';
    const TYPE_PROMO_CODE = 'promo_code';
    const TYPE_REFERRAL_INCOME = 'referral_income';
    const TYPE_CHARGEBACK = 'chargeback';
    const TYPE_ADMIN = 'admin';

    const POINTS_EMAIL_VERIFICATION = 2;

    const COMMISSION_PERCENT_REFERRAL = 0.1;

    const GIVEAWAY_MIN_POINTS = 10;
    const GIVEAWAY_MAX_POINTS = 50;

    /*
     * The keys are referenced to the amount of offers that must be completed
     * in order to redeem a task.
     * The values are the points for each task completion.
     */
    const DAILY_TASK_OFFERS_OPTIONS = [
        1 => 0.25,
        3 => 1.00,
        5 => 2.00,
        10 => 4.00,
    ];

    const SOCIAL_MEDIA_TASK_OFFERS_OPTIONS = [
        'youtube' => 0.5,
        'discord' => 0.5,
        'instagram' => 0.25,
        'twitter' => 0.25,
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

    protected $appends = [
        'formatted_created_at',
        'formatted_points',
        'formatted_type',
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

    public function isTypeDailyTask(): bool
    {
        return $this->type === self::TYPE_DAILY_TASK;
    }

    public function isTypeSocialMedia(): bool
    {
        return $this->type === self::TYPE_SOCIAL_MEDIA;
    }

    public function isTypeCoupon(): bool
    {
        return $this->type === self::TYPE_PROMO_CODE;
    }

    public function isTypeReferralIncome(): bool
    {
        return $this->type === self::TYPE_REFERRAL_INCOME;
    }

    public function isTypeAdmin(): bool
    {
        return $this->type === self::TYPE_ADMIN;
    }

    public function isTypeChargeback(): bool
    {
        return $this->type === self::TYPE_CHARGEBACK;
    }

    public function isAvailableForReferring(): bool
    {
        return ! $this->isTypeCoupon() && ! $this->isTypeReferralIncome() && ! $this->isTypeDailyTask() && ! $this->isTypeChargeback() && ! $this->isTypeAdmin();
    }

    public function getOffersCountAttribute(): ?int
    {
        return Arr::get($this, 'data.offers_count');
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return optional($this->created_at)->format('M d Y H:i');
    }

    public function getFormattedPointsAttribute(): string
    {
        return currency_format($this->points);
    }

    public function getFormattedTypeAttribute(): string
    {
        if (is_null($this->provider)) {
            return str_replace('_', ' ', ucwords($this->type, '_'));
        }

        if ($this->isTypeChargeback()) {
            return $this->provider.' '.str_replace('_', ' ', ucwords($this->type, '_'));
        }

        return $this->provider;
    }
}

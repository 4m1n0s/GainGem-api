<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereTypesAvailableForReferring()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompletedTask whereUserId($value)
 * @mixin \Eloquent
 */
	class IdeHelperCompletedTask extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Coupon
 *
 * @property int $id
 * @property string $code
 * @property float $points
 * @property int $max_usages
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CompletedTask[] $completedTasks
 * @property-read int|null $completed_tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereMaxUsages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class IdeHelperCoupon extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GiftCard
 *
 * @property int $id
 * @property string|null $country
 * @property string $code
 * @property string $provider
 * @property int $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Transaction|null $transaction
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftCard query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftCard whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftCard whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftCard whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftCard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftCard whereValue($value)
 * @mixin \Eloquent
 */
	class IdeHelperGiftCard extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property string $type
 * @property int $user_id
 * @property float $points
 * @property int|null $gift_card_id
 * @property string|null $destination
 * @property int|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GiftCard|null $giftCard
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereGiftCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereValue($value)
 * @mixin \Eloquent
 */
	class IdeHelperTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UrlToken
 *
 * @property int $id
 * @property string $type
 * @property string $token
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UrlToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UrlToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UrlToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UrlToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UrlToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UrlToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UrlToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UrlToken whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UrlToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UrlToken whereUserId($value)
 * @mixin \Eloquent
 */
	class IdeHelperUrlToken extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User withAvailablePoints()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User withTotalPoints()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User withWastedPoints()
 * @mixin \Eloquent
 */
	class IdeHelperUser extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject {}
}


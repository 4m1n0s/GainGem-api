<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\CompletedTask
 *
 * @property int $id
 * @property string $type
 * @property string|null $provider
 * @property int $user_id
 * @property int $points
 * @property array|null $data
 * @property int|null $coupon_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Coupon|null $coupon
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedTask whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedTask whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedTask wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedTask whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedTask whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompletedTask whereUserId($value)
 * @mixin \Eloquent
 */
class CompletedTask extends Model
{
    use HasFactory;

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
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

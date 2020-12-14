<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

/**
 * App\Models\Coupon.
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
 * @property-read string $formatted_expires_at
 * @property-read string $formatted_points
 * @property-read bool $is_active
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
class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'points',
        'max_usages',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'points' => 'float',
    ];

    protected $appends = [
        'formatted_expires_at',
        'formatted_points',
        'is_active',
    ];

    public function completedTasks(): HasMany
    {
        return $this->hasMany(CompletedTask::class);
    }

    public function getFormattedExpiresAtAttribute(): string
    {
        return $this->expires_at->format('M d Y');
    }

    public function getFormattedPointsAttribute(): string
    {
        return points_format($this->points);
    }

    public function getIsActiveAttribute(): bool
    {
        if (Arr::has($this->getAttributes(), 'uses') && $this->max_usages !== 0) {
            return ! $this->expires_at->isPast() && $this->getAttributes()['uses'] < $this->max_usages;
        }

        return ! $this->expires_at->isPast();
    }
}

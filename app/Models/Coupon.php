<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperCoupon
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

    public function completedTasks(): HasMany
    {
        return $this->hasMany(CompletedTask::class);
    }
}

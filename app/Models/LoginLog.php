<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LoginLog.
 *
 * @property int $id
 * @property int $user_id
 * @property string $ip
 * @property string|null $previous_ip
 * @property string|null $location
 * @property string|null $device
 * @property string|null $browser
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $formatted_created_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginLog whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginLog whereDevice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginLog whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginLog wherePreviousIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LoginLog whereUserId($value)
 * @mixin \Eloquent
 */
class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip',
        'previous_ip',
        'location',
        'device',
        'browser',
    ];

    protected $appends = [
        'formatted_created_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return optional($this->created_at)->format('M d Y H:i:s');
    }
}

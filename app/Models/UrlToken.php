<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * App\Models\UrlToken.
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
class UrlToken extends Model
{
    public const TYPE_VERIFICATION = 'verification';
    public const TYPE_FORGOT_PASSWORD = 'forgot_password';

    protected $fillable = [
        'type',
        'token',
        'user_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public static function getRandomToken(): string
    {
        return Str::uuid()->toString();
    }

    public function getVerificationUrl(): string
    {
        return config('app.user_app_url')."/verify?token={$this->token}";
    }

    public function getForgotPasswordUrl(): string
    {
        return config('app.user_app_url')."/reset-password?token={$this->token}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

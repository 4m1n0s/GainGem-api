<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperUrlToken
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

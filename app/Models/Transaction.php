<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperTransaction
 */
class Transaction extends Model
{
    use HasFactory;

    const TYPE_GIFT_CARD = 'gift_card';
    const TYPE_BITCOIN = 'bitcoin';
    const TYPE_ROBLOX = 'roblox';

    protected $fillable = [
        'type',
        'user_id',
        'points',
        'gift_card_id',
        'destination',
        'value',
    ];

    protected $casts = [
        'points' => 'float',
    ];

    public function giftCard(): BelongsTo
    {
        return $this->belongsTo(GiftCard::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isTypeGiftCard(): bool
    {
        return $this->type === self::TYPE_GIFT_CARD;
    }

    public function isTypeBitcoin(): bool
    {
        return $this->type === self::TYPE_BITCOIN;
    }

    public function isTypeRoblox(): bool
    {
        return $this->type === self::TYPE_ROBLOX;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereGiftCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereValue($value)
 * @mixin \Eloquent
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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Transaction.
 *
 * @property int $id
 * @property string $type
 * @property int $user_id
 * @property float $points
 * @property int|null $gift_card_id
 * @property string|null $destination
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $robux_group_id
 * @property int|null $robux_amount
 * @property string|null $bitcoin_amount
 * @property-read string|null $formatted_created_at
 * @property-read string|null $formatted_provider
 * @property-read \App\Models\GiftCard|null $giftCard
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereBitcoinAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereGiftCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereRobuxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereRobuxGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereValue($value)
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    use HasFactory;

    const TYPE_GIFT_CARD = 'gift_card';
    const TYPE_BITCOIN = 'bitcoin';
    const TYPE_ROBUX = 'robux';

    protected $fillable = [
        'type',
        'user_id',
        'points',
        'gift_card_id',
        'destination',
        'value',
        'robux_group_id',
    ];

    protected $casts = [
        'points' => 'float',
    ];

    protected $appends = [
        'formatted_created_at',
        'formatted_provider',
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

    public function isTypeRobux(): bool
    {
        return $this->type === self::TYPE_ROBUX;
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return optional($this->created_at)->format('M d Y');
    }

    public function getFormattedProviderAttribute(): ?string
    {
        if ($this->type !== self::TYPE_GIFT_CARD || ! $this->relationLoaded('giftCard')) {
            return str_replace('_', ' ', ucwords($this->type, '_'));
        }

        return optional($this->giftCard)->formatted_provider;
    }
}

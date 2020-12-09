<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\GiftCard.
 *
 * @property int $id
 * @property string|null $country
 * @property string $code
 * @property string $provider
 * @property int $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $formatted_provider
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
class GiftCard extends Model
{
    use HasFactory;

    const PROVIDER_APP_STORE = 'app_store';
    const PROVIDER_XBOX = 'xbox';
    const PROVIDER_ROBLOX = 'roblox';
    const PROVIDER_PSN = 'psn';
    const PROVIDER_GOOGLE_PLAY = 'google_play';
    const PROVIDER_NETFLIX = 'netflix';
    const PROVIDER_SPOTIFY = 'spotify';
    const PROVIDER_DISCORD = 'discord';

    protected $fillable = [
        'country',
        'code',
        'provider',
        'value',
    ];

    protected $appends = [
        'formatted_provider',
    ];

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    public function getFormattedProviderAttribute(): string
    {
        if (in_array($this->provider, [self::PROVIDER_XBOX, self::PROVIDER_PSN])) {
            return strtoupper($this->provider);
        }

        return str_replace('_', ' ', ucwords($this->provider, '_'));
    }
}

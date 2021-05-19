<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\CurrencyValue.
 *
 * @property int $id
 * @property int $currency_id
 * @property int $apple
 * @property int $xbox
 * @property int $roblox
 * @property int $psn
 * @property int $google_play
 * @property int $netflix
 * @property int $spotify
 * @property int $discord
 * @property int $steam
 * @property int $fortnite
 * @property int $valorant
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Currency $currency
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereApple($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereDiscord($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereFortnite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereGooglePlay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereNetflix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue wherePsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereRoblox($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereSpotify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereSteam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereValorant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CurrencyValue whereXbox($value)
 * @mixin \Eloquent
 */
class CurrencyValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_id',
        ...GiftCard::PROVIDERS,
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}

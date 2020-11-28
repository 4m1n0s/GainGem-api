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

    protected $fillable = [
        'country',
        'code',
        'provider',
        'value',
    ];

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }
}

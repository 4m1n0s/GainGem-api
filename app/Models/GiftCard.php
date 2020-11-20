<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\GiftCard
 *
 * @property int $id
 * @property string|null $country
 * @property string $code
 * @property string $provider
 * @property int $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Transaction|null $transaction
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCard query()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCard whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCard whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCard whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCard whereValue($value)
 * @mixin \Eloquent
 */
class GiftCard extends Model
{
    use HasFactory;

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Currency.
 *
 * @property int $id
 * @property string $currency
 * @property string $name
 * @property string $symbol
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CurrencyValue|null $currencyValue
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Currency query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Currency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Currency whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Currency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Currency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Currency whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Currency whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency',
        'name',
        'symbol',
    ];

    public function currencyValue(): HasOne
    {
        return $this->hasOne(CurrencyValue::class);
    }
}

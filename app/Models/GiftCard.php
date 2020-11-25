<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin IdeHelperGiftCard
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

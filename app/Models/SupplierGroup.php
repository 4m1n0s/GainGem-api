<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\SupplierGroup.
 *
 * @property int $id
 * @property int $user_id
 * @property string $cookie
 * @property int $group_id
 * @property int $owner_id
 * @property int|null $rate
 * @property \Illuminate\Support\Carbon|null $disabled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SupplierPayment[] $supplierPayments
 * @property-read int|null $supplier_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierGroup whereCookie($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierGroup whereDisabledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierGroup whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierGroup whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierGroup whereUserId($value)
 * @mixin \Eloquent
 */
class SupplierGroup extends Model
{
    use HasFactory;

    protected $casts = [
        'disabled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}

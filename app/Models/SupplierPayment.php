<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SupplierPayment.
 *
 * @property int $id
 * @property int|null $supplier_group_id
 * @property string $method
 * @property string $destination
 * @property string $value
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SupplierGroup|null $supplierGroup
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereSupplierGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereValue($value)
 * @mixin \Eloquent
 */
class SupplierPayment extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_DENIED = 'denied';

    const METHOD_BITCOIN = 'bitcoin';
    const METHOD_PAYPAL = 'paypal';

    public function supplierGroup(): BelongsTo
    {
        return $this->belongsTo(SupplierGroup::class);
    }
}

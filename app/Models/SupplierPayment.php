<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SupplierPayment.
 *
 * @property int $id
 * @property int $supplier_user_id
 * @property string $method
 * @property string $destination
 * @property float $value
 * @property string $status
 * @property string|null $denial_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $formatted_created_at
 * @property-read string $formatted_method
 * @property-read string $formatted_status
 * @property-read string $formatted_value
 * @property-read \App\Models\RobuxAccount $robuxAccount
 * @property-read \App\Models\User $supplierUser
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereDenialReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SupplierPayment whereSupplierUserId($value)
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

    protected $fillable = [
        'method',
        'destination',
        'value',
        'status',
        'denial_reason',
    ];

    protected $appends = [
        'formatted_method',
        'formatted_status',
        'formatted_value',
        'formatted_created_at',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    public function robuxAccount(): BelongsTo
    {
        return $this->belongsTo(RobuxAccount::class);
    }

    public function supplierUser(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedMethodAttribute(): string
    {
        return ucfirst($this->method);
    }

    public function getFormattedStatusAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getFormattedValueAttribute(): string
    {
        return currency_format($this->value);
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return optional($this->created_at)->format('M d Y');
    }
}

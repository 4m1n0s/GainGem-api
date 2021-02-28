<?php

namespace App\Models;

use App\Builders\RobuxAccountBuilder;
use App\Casts\Encrypt;
use App\Services\Robux;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * App\Models\RobuxAccount
 *
 * @property int $id
 * @property int $supplier_user_id
 * @property string $cookie
 * @property int $robux_account_id
 * @property string $robux_account_username
 * @property int $robux_amount
 * @property \Illuminate\Support\Carbon|null $disabled_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $formatted_disabled_at
 * @property-read string $formatted_robux_amount
 * @property-read string $formatted_total_withdrawn
 * @property-read float $total_earnings
 * @property-read int $total_withdrawn
 * @property-read \App\Models\User $supplierUser
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount bestMatch()
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount newModelQuery()
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RobuxAccount onlyTrashed()
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount query()
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount whereCookie($value)
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount whereCreatedAt($value)
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount whereDeletedAt($value)
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount whereDisabledAt($value)
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount whereId($value)
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount whereRobuxAccountId($value)
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount whereRobuxAccountUsername($value)
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount whereRobuxAmount($value)
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount whereSupplierUserId($value)
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount whereUpdatedAt($value)
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount withTotalEarnings()
 * @method static \App\Builders\RobuxAccountBuilder|\App\Models\RobuxAccount withTotalWithdrawn()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RobuxAccount withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RobuxAccount withoutTrashed()
 * @mixin \Eloquent
 */
class RobuxAccount extends Model
{
    use HasFactory, SoftDeletes;

    const MIN_ROBUX_AMOUNT = 250;

    protected $fillable = [
        'supplier_user_id',
        'cookie',
        'robux_account_id',
        'robux_account_username',
        'robux_amount',
        'rate',
        'disabled_at',
    ];

    protected $casts = [
        'cookie' => Encrypt::class,
        'rate' => 'float',
        'disabled_at' => 'datetime',
    ];

    protected $appends = [
        'total_earnings',
        'total_withdrawn',
        'formatted_robux_amount',
        'formatted_disabled_at',
    ];

    public function newEloquentBuilder($query)
    {
        return new RobuxAccountBuilder($query);
    }

    public function supplierUser(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function loadTotalEarnings(): self
    {
        return $this->loadSum('transactions as total_earnings', 'value');
    }

    public function loadTotalWithdrawn(): self
    {
        return $this->loadSum('transactions as total_withdrawn', 'robux_amount');
    }

    public function getTotalEarningsAttribute(): float
    {
        if (! Arr::has($this->getAttributes(), 'total_earnings')) {
            return 0;
        }

        return $this->getAttributes()['total_earnings'] ?? 0;
    }

    public function getTotalWithdrawnAttribute(): int
    {
        if (! Arr::has($this->getAttributes(), 'total_withdrawn')) {
            return 0;
        }

        return $this->getAttributes()['total_withdrawn'] ?? 0;
    }

    public function getFormattedDisabledAtAttribute(): ?string
    {
        return optional($this->disabled_at)->format('M d Y');
    }

    public function getFormattedTotalWithdrawnAttribute(): string
    {
        return currency_format($this->total_withdrawn);
    }

    public function getFormattedRobuxAmountAttribute(): string
    {
        return currency_format($this->robux_amount);
    }
}

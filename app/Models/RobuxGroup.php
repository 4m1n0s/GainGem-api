<?php

namespace App\Models;

use App\Builders\RobuxGroupBuilder;
use App\Casts\Encrypt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * App\Models\RobuxGroup.
 *
 * @property int $id
 * @property int $supplier_user_id
 * @property string $cookie
 * @property int $robux_group_id
 * @property int $robux_owner_id
 * @property string $robux_owner_username
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
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup bestMatch()
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup newModelQuery()
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RobuxGroup onlyTrashed()
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup query()
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereCookie($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereCreatedAt($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereDeletedAt($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereDisabledAt($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereId($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereRobuxAmount($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereRobuxGroupId($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereRobuxOwnerId($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereRobuxOwnerUsername($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereSupplierUserId($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereUpdatedAt($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup withTotalEarnings()
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup withTotalWithdrawn()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RobuxGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RobuxGroup withoutTrashed()
 * @mixin \Eloquent
 */
class RobuxGroup extends Model
{
    use HasFactory, SoftDeletes;

    const MIN_ROBUX_AMOUNT = 100;

    protected $fillable = [
        'supplier_user_id',
        'cookie',
        'robux_group_id',
        'robux_owner_id',
        'robux_owner_username',
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
        return new RobuxGroupBuilder($query);
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

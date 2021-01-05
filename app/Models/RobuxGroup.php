<?php

namespace App\Models;

use App\Builders\RobuxGroupBuilder;
use App\Casts\Encrypt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property \Illuminate\Support\Carbon|null $disabled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read float $total_earnings
 * @property-read \App\Models\User $supplierUser
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup bestMatch()
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup newModelQuery()
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup newQuery()
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup query()
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereCookie($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereCreatedAt($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereDisabledAt($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereId($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereRobuxGroupId($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereRobuxOwnerId($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereRobuxOwnerUsername($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereSupplierUserId($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup whereUpdatedAt($value)
 * @method static \App\Builders\RobuxGroupBuilder|\App\Models\RobuxGroup withTotalEarnings()
 * @mixin \Eloquent
 */
class RobuxGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_user_id',
        'cookie',
        'robux_group_id',
        'robux_owner_id',
        'robux_owner_username',
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

    public function getTotalEarningsAttribute(): float
    {
        if (! Arr::has($this->getAttributes(), 'total_earnings')) {
            return 0;
        }

        return $this->getAttributes()['total_earnings'] ?? 0;
    }
}

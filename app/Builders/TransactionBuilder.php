<?php

namespace App\Builders;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TransactionBuilder extends Builder
{
    public function whereSupplier(User $supplier): self
    {
        $this->where('type', Transaction::TYPE_ROBUX)
            ->whereHas('robuxAccount', function ($query) use ($supplier) {
                $query->where('supplier_user_id', $supplier->id);
            });

        return $this;
    }

    public function whereSupplierWithTrashed(User $supplier): self
    {
        $this->where('type', Transaction::TYPE_ROBUX)
            ->whereHas('robuxAccount', function ($query) use ($supplier) {
                $query->where('supplier_user_id', $supplier->id)->withTrashed();
            });

        return $this;
    }
}

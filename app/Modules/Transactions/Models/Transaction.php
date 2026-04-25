<?php

namespace App\Modules\Transactions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\Analytics\Models\CustomerProfile;

class Transaction extends Model
{
    protected $fillable = ['customer_profile_id', 'order_id', 'payment_method', 'grand_total', 'status'];

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}

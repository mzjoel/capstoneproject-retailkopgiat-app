<?php

namespace App\Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'price', 'image', 'is_available', 'category_id', 'tags', 'ingredients', 'nutrition'];

    protected $casts = [
        'nutrition' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(UserInteraction::class);
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}

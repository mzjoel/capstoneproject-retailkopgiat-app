<?php

namespace App\Modules\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class AdminProfile extends Model
{
    protected $fillable = ['user_id', 'name', 'employee_id', 'department'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

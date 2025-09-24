<?php

namespace Acme\UserDiscounts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDiscount extends Model
{
    protected $fillable = ['user_id', 'discount_id', 'used_count'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }
}

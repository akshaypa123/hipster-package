<?php

namespace Acme\UserDiscounts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDiscount extends Model
{
protected $table = 'user_discounts';
    protected $fillable = ['user_id','discount_id','usage_count'];
   
}

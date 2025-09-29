<?php

namespace Acme\Discont\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DiscountAudit extends Model
{
    protected $fillable = ['user_id','discount_id','applied_value','context'];
}

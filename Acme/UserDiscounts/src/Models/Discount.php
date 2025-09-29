<?php

namespace Acme\Discont\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Discount extends Model
{
    protected $fillable = ['name','type','value','active','expires_at','max_usage'];
    protected $dates = ['expires_at'];

    public function users() {
        return $this->belongsToMany(User::class,'user_discounts')
                    ->withPivot('usage_count')
                    ->withTimestamps();
    }

    public function isActive() {
        return $this->active && (!$this->expires_at || $this->expires_at->isFuture());
    }
}


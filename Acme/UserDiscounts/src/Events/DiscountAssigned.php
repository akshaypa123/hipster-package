<?php
namespace Acme\UserDiscounts\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Acme\UserDiscounts\Models\UserDiscount;

class DiscountAssigned
{
    use Dispatchable;
    public $userDiscount;

    public function __construct(UserDiscount $userDiscount)
    {
        $this->userDiscount = $userDiscount;
    }
}

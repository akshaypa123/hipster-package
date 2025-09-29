<?php
namespace Acme\UserDiscounts\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Acme\UserDiscounts\Models\UserDiscount;

class DiscountApplied { public function __construct(public Discount $discount, public User $user, public float $value) {} }


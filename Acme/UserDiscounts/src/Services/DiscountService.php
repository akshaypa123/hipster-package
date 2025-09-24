<?php

namespace Acme\UserDiscounts\Services;

use Acme\UserDiscounts\Models\Discount;
use Acme\UserDiscounts\Models\UserDiscount;
use Acme\UserDiscounts\Models\DiscountAudit;
use Acme\UserDiscounts\Events\DiscountAssigned;
use Acme\UserDiscounts\Events\DiscountRevoked;
use Acme\UserDiscounts\Events\DiscountApplied;

class DiscountService
{
    public function assign($user, Discount $discount)
    {
        $userDiscount = UserDiscount::firstOrCreate([
            'user_id' => $user->id,
            'discount_id' => $discount->id
        ]);
        event(new DiscountAssigned($userDiscount));
        DiscountAudit::create([
            'user_id' => $user->id,
            'discount_id' => $discount->id,
            'action' => 'assigned'
        ]);
        return $userDiscount;
    }

    public function revoke($user, Discount $discount)
    {
        $userDiscount = UserDiscount::where([
            'user_id' => $user->id,
            'discount_id' => $discount->id
        ])->first();
        if($userDiscount) {
            $userDiscount->delete();
            event(new DiscountRevoked($userDiscount));
            DiscountAudit::create([
                'user_id' => $user->id,
                'discount_id' => $discount->id,
                'action' => 'revoked'
            ]);
        }
        return true;
    }

    public function eligibleFor($user)
    {
        return UserDiscount::with('discount')
            ->where('user_id', $user->id)
            ->get()
            ->filter(fn($ud) => $ud->discount->isActive() && 
                                ($ud->discount->usage_limit === null || $ud->used_count < $ud->discount->usage_limit));
    }

    public function apply($user, $amount)
    {
        $eligible = $this->eligibleFor($user);

        $stacking_order = config('userdiscounts.stacking_order', 'highest_first');
        $eligible = $stacking_order === 'highest_first'
            ? $eligible->sortByDesc(fn($ud) => $ud->discount->percentage)
            : $eligible->sortBy(fn($ud) => $ud->discount->percentage);

        $totalDiscount = 0;

        foreach($eligible as $ud) {
            $discPercent = $ud->discount->percentage;
            $totalDiscount += $discPercent;
            $ud->increment('used_count');
            event(new DiscountApplied($ud));
            DiscountAudit::create([
                'user_id' => $user->id,
                'discount_id' => $ud->discount->id,
                'action' => 'applied'
            ]);
        }

        $maxCap = config('userdiscounts.max_percentage_cap', 50);
        $totalDiscount = min($totalDiscount, $maxCap);

        $rounding = config('userdiscounts.rounding', 'ceil');
        $finalDiscount = match($rounding) {
            'ceil' => ceil($amount * $totalDiscount / 100),
            'floor' => floor($amount * $totalDiscount / 100),
            default => round($amount * $totalDiscount / 100)
        };

        return $finalDiscount;
    }
}

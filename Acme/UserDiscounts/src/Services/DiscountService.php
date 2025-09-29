<?php

namespace Acme\UserDiscounts\Services;

use Acme\UserDiscounts\Models\Discount;
use Acme\UserDiscounts\Models\UserDiscount;
use Acme\UserDiscounts\Models\DiscountAudit;
use Acme\UserDiscounts\Events\DiscountAssigned;
use Acme\UserDiscounts\Events\DiscountRevoked;
use Acme\UserDiscounts\Events\DiscountApplied;

class DiscountManager
{
    public function assign(User $user, Discount $discount)
    {
        if (!$discount->isActive()) return false;
        $ud = UserDiscount::firstOrCreate(
            ['user_id'=>$user->id, 'discount_id'=>$discount->id]
        );
        event(new DiscountAssigned($discount,$user));
        return $ud;
    }

    public function revoke(User $user, Discount $discount)
    {
        UserDiscount::where(['user_id'=>$user->id,'discount_id'=>$discount->id])->delete();
        event(new DiscountRevoked($discount,$user));
    }

    public function eligibleFor(User $user): Collection
    {
        return $user->discounts()->where('active', true)
            ->where(fn($q)=>$q->whereNull('expires_at')->orWhere('expires_at','>',now()))
            ->get();
    }

    public function apply(User $user, float $amount, string $context=''): float
    {
        $discounts = $this->eligibleFor($user)
            ->sortBy(fn($d)=>array_search($d->type, config('discounts.stacking_order')));
        $totalDiscount = 0;

        DB::transaction(function() use ($user, $discounts, $amount, $context, &$totalDiscount) {
            foreach($discounts as $discount){
                $ud = UserDiscount::where(['user_id'=>$user->id,'discount_id'=>$discount->id])->lockForUpdate()->first();
                if($discount->max_usage && $ud->usage_count >= $discount->max_usage) continue;

                $applied = $discount->type==='percentage' ? ($amount*$discount->value/100) : $discount->value;
                $applied = round($applied, config('discounts.rounding'));
                $totalDiscount += $applied;

                $ud->increment('usage_count');
                DiscountAudit::create([
                    'user_id'=>$user->id,
                    'discount_id'=>$discount->id,
                    'applied_value'=>$applied,
                    'context'=>$context
                ]);
                event(new DiscountApplied($discount,$user,$applied));
            }

            $cap = config('discounts.max_percentage_cap')/100 * $amount;
            $totalDiscount = min($totalDiscount,$cap);
        });

        return $totalDiscount;
    }
}



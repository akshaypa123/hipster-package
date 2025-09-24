<?php
namespace Acme\UserDiscounts\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Acme\UserDiscounts\Facades\UserDiscounts;
use Acme\UserDiscounts\Models\Discount;
use App\Models\User;

class DiscountTest extends TestCase
{
    protected function getPackageProviders($app) {
        return ['Acme\\UserDiscounts\\UserDiscountsServiceProvider'];
    }

    protected function getPackageAliases($app) {
        return ['UserDiscounts' => 'Acme\\UserDiscounts\\Facades\\UserDiscounts'];
    }

    public function test_assign_and_apply_discount() {
        $user = User::factory()->create();
        $discount = Discount::create(['name'=>'Promo', 'percentage'=>10, 'active'=>true]);

        UserDiscounts::assign($user, $discount);
        $applied = UserDiscounts::apply($user, 1000);

        $this->assertEquals(100, $applied); // 10% of 1000
    }
}
                                                                                                                                                            
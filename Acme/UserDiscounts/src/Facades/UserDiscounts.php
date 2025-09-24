<?php
namespace Acme\UserDiscounts\Facades;

use Illuminate\Support\Facades\Facade;

class UserDiscounts extends Facade
{
    protected static function getFacadeAccessor() {
        return 'userdiscounts';
    }
}

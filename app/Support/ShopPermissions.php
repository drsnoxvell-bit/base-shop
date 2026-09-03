<?php

namespace App\Support;

class ShopPermissions
{
    public const INDEX = 'platform.index';

    public const USERS = 'platform.systems.users';

    public const ROLES = 'platform.systems.roles';

    public const CATEGORIES = 'platform.shop.categories';

    public const PRODUCTS = 'platform.shop.products';

    public const ORDERS = 'platform.shop.orders';

    public const SETTINGS = 'platform.shop.settings';

    public const ROLE_ADMINISTRATOR = 'administrator';

    public const ROLE_EDITOR = 'editor';

    public const ROLE_USER = 'user';

    public static function administrator(): array
    {
        return [
            self::INDEX => true,
            self::USERS => true,
            self::ROLES => true,
            self::CATEGORIES => true,
            self::PRODUCTS => true,
            self::ORDERS => true,
            self::SETTINGS => true,
        ];
    }

    public static function editor(): array
    {
        return [
            self::INDEX => true,
            self::CATEGORIES => true,
            self::PRODUCTS => true,
            self::ORDERS => true,
        ];
    }

    public static function user(): array
    {
        return [];
    }
}

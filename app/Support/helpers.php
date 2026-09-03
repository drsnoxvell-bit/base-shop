<?php

use App\Services\Shop\SettingService;

if (! function_exists('shop_money')) {
    function shop_money(int|float|string|null $amount): string
    {
        $value = (float) $amount;

        return number_format($value, 0, ',', ' ').' '.(config('shop.currency', '₽'));
    }
}

if (! function_exists('shop_setting')) {
    function shop_setting(string $key, mixed $default = null): mixed
    {
        return app(SettingService::class)->get($key, $default);
    }
}

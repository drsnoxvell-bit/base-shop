<?php

return [

    'currency' => env('SHOP_CURRENCY', '₽'),

    'cart_session_key' => 'shop_cart',

    'max_gallery_images' => 10,

    'order_prefix' => 'ORD',

    'stack' => env('SHOP_STACK', 'blade'),

    'social_providers' => ['yandex', 'vkontakte'],

];

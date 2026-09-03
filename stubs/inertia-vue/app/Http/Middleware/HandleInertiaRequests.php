<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Services\Shop\CartService;
use App\Services\Shop\SettingService;
use App\Support\ShopPayload;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        $settings = app(SettingService::class);
        $cart = app(CartService::class);

        return [
            ...parent::share($request),
            'shopSite' => $settings->site(),
            'cartCount' => $cart->count(),
            'navCategories' => Category::query()->active()->ordered()->get(['id', 'name', 'slug']),
            'auth' => [
                'user' => ShopPayload::user($request->user()),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ];
    }
}

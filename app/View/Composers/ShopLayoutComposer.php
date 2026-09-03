<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Services\Shop\CartService;
use App\Services\Shop\SettingService;
use Illuminate\View\View;

class ShopLayoutComposer
{
    public function __construct(
        private readonly SettingService $settings,
        private readonly CartService $cart,
    ) {}

    public function compose(View $view): void
    {
        $view->with('shopSite', $this->settings->site());
        $view->with('cartCount', $this->cart->count());
        $view->with('navCategories', Category::query()->active()->ordered()->get());
    }
}

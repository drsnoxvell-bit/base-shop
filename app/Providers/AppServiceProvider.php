<?php

namespace App\Providers;

use App\Services\Shop\SettingService;
use App\View\Composers\ShopLayoutComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingService::class);
    }

    public function boot(): void
    {
        $this->app->make(SettingService::class)->applyMailConfig();

        View::composer('layouts.shop', ShopLayoutComposer::class);
    }
}

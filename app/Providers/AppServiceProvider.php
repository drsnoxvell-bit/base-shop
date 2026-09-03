<?php

namespace App\Providers;

use App\Services\Shop\SettingService;
use App\View\Composers\ShopLayoutComposer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\VKontakte\Provider as VkontakteProvider;
use SocialiteProviders\Yandex\Provider as YandexProvider;

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

        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('yandex', YandexProvider::class);
            $event->extendSocialite('vkontakte', VkontakteProvider::class);
        });
    }
}

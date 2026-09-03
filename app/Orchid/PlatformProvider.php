<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Support\ShopPermissions;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformProvider extends OrchidServiceProvider
{
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);
    }

    public function menu(): array
    {
        return [
            Menu::make('Категории')
                ->icon('bs.folder')
                ->route('platform.shop.categories')
                ->permission(ShopPermissions::CATEGORIES)
                ->title('Магазин'),

            Menu::make('Товары')
                ->icon('bs.box-seam')
                ->route('platform.shop.products')
                ->permission(ShopPermissions::PRODUCTS),

            Menu::make('Заказы')
                ->icon('bs.bag-check')
                ->route('platform.shop.orders')
                ->permission(ShopPermissions::ORDERS),

            Menu::make('Настройки')
                ->icon('bs.gear')
                ->route('platform.shop.settings')
                ->permission(ShopPermissions::SETTINGS)
                ->divider(),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission(ShopPermissions::USERS)
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission(ShopPermissions::ROLES),
        ];
    }

    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission(ShopPermissions::ROLES, __('Roles'))
                ->addPermission(ShopPermissions::USERS, __('Users')),
            ItemPermission::group('Магазин')
                ->addPermission(ShopPermissions::CATEGORIES, 'Категории')
                ->addPermission(ShopPermissions::PRODUCTS, 'Товары')
                ->addPermission(ShopPermissions::ORDERS, 'Заказы')
                ->addPermission(ShopPermissions::SETTINGS, 'Настройки'),
        ];
    }
}

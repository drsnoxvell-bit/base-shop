<?php

declare(strict_types=1);

namespace App\Orchid;

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
                ->title('Магазин'),

            Menu::make('Товары')
                ->icon('bs.box-seam')
                ->route('platform.shop.products'),

            Menu::make('Заказы')
                ->icon('bs.bag-check')
                ->route('platform.shop.orders'),

            Menu::make('Настройки')
                ->icon('bs.gear')
                ->route('platform.shop.settings')
                ->divider(),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),
        ];
    }

    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}

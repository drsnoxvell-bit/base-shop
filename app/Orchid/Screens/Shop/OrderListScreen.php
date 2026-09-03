<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Shop;

use App\Models\Order;
use App\Orchid\Layouts\Shop\OrderListLayout;
use App\Support\ShopPermissions;
use Orchid\Screen\Screen;

class OrderListScreen extends Screen
{
    public function permission(): ?iterable
    {
        return [ShopPermissions::ORDERS];
    }

    public function query(): iterable
    {
        return [
            'orders' => Order::filters()->defaultSort('id', 'desc')->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Заказы';
    }

    public function description(): ?string
    {
        return 'Заявки с витрины, без онлайн-оплаты';
    }

    public function layout(): iterable
    {
        return [
            OrderListLayout::class,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Shop;

use App\Models\Order;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class OrderListLayout extends Table
{
    protected $target = 'orders';

    protected function columns(): iterable
    {
        return [
            TD::make('number', 'Номер')
                ->sort()
                ->render(fn (Order $order) => Link::make($order->number)
                    ->route('platform.shop.orders.edit', $order)),
            TD::make('name', 'Покупатель'),
            TD::make('phone', 'Телефон'),
            TD::make('total', 'Сумма')
                ->sort()
                ->render(fn (Order $order) => shop_money($order->total)),
            TD::make('status', 'Статус')
                ->render(fn (Order $order) => $order->status->label()),
            TD::make('created_at', 'Дата')
                ->sort()
                ->render(fn (Order $order) => $order->created_at?->format('d.m.Y H:i')),
        ];
    }
}

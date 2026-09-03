<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Shop;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Orchid\Layouts\Shop\OrderEditLayout;
use App\Services\Shop\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class OrderEditScreen extends Screen
{
    public ?Order $order = null;

    public function query(Order $order): iterable
    {
        $order->load('items');

        return [
            'order' => $order,
            'items' => $order->items,
        ];
    }

    public function name(): ?string
    {
        return 'Заказ '.$this->order?->number;
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->icon('bs.check-circle')
                ->method('save'),

            Button::make('Удалить')
                ->icon('bs.trash3')
                ->method('remove')
                ->confirm('Удалить заказ? Остатки будут возвращены, если заказ не отменён.')
                ->canSee((bool) $this->order?->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            OrderEditLayout::class,
            Layout::table('items', [
                TD::make('name', 'Товар'),
                TD::make('price', 'Цена')->render(fn ($item) => shop_money($item->price)),
                TD::make('qty', 'Кол-во'),
                TD::make('sum', 'Сумма')->render(fn ($item) => shop_money($item->sum)),
            ]),
        ];
    }

    public function save(Request $request, Order $order, OrderService $orders): RedirectResponse
    {
        $data = $request->validate([
            'order.status' => ['required', 'in:new,processing,done,cancelled'],
        ]);

        $orders->updateStatus($order, OrderStatus::from($data['order']['status']));

        Toast::info('Заказ обновлён, остатки пересчитаны при необходимости');

        return redirect()->route('platform.shop.orders');
    }

    public function remove(Order $order, OrderService $orders): RedirectResponse
    {
        $orders->delete($order);

        Toast::info('Заказ удалён');

        return redirect()->route('platform.shop.orders');
    }
}

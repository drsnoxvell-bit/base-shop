<?php

namespace App\Services\Shop;

use App\Enums\OrderStatus;
use App\Mail\Shop\OrderCreatedMail;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly SettingService $settings,
    ) {}

    public function checkout(array $customer): Order
    {
        $this->cart->recalculate();
        $summary = $this->cart->summary();

        if ($summary['count'] < 1) {
            throw ValidationException::withMessages([
                'cart' => 'Корзина пуста.',
            ]);
        }

        $order = DB::transaction(function () use ($customer, $summary) {
            $order = Order::query()->create([
                'number' => $this->makeNumber(),
                'name' => $customer['name'],
                'phone' => $customer['phone'],
                'email' => $customer['email'] ?? null,
                'address' => $customer['address'],
                'comment' => $customer['comment'] ?? null,
                'status' => OrderStatus::New,
                'total' => $summary['total'],
                'stock_taken' => true,
            ]);

            foreach ($summary['lines'] as $line) {
                /** @var Product $product */
                $product = $line['product'];
                $qty = (int) $line['qty'];

                if ($product->quantity < $qty) {
                    throw ValidationException::withMessages([
                        'cart' => 'Недостаточно товара «'.$product->name.'».',
                    ]);
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'qty' => $qty,
                    'sum' => $line['sum'],
                ]);

                $product->decrement('quantity', $qty);
            }

            return $order->load('items');
        });

        $this->cart->clear();
        $this->notify($order);

        return $order;
    }

    public function updateStatus(Order $order, OrderStatus $status): Order
    {
        return DB::transaction(function () use ($order, $status) {
            $order->refresh();

            if ($status === OrderStatus::Cancelled && $order->stock_taken && $order->status !== OrderStatus::Cancelled) {
                $this->restoreStock($order);
                $order->stock_taken = false;
            }

            if (
                $status !== OrderStatus::Cancelled
                && $order->status === OrderStatus::Cancelled
                && ! $order->stock_taken
            ) {
                $this->takeStock($order);
                $order->stock_taken = true;
            }

            $order->status = $status;
            $order->save();

            return $order;
        });
    }

    public function delete(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->refresh();

            if ($order->stock_taken && $order->status !== OrderStatus::Cancelled) {
                $this->restoreStock($order);
            }

            $order->items()->delete();
            $order->delete();
        });
    }

    private function takeStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->decrement('quantity', $item->qty);
            }
        }
    }

    private function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('quantity', $item->qty);
            }
        }
    }

    private function makeNumber(): string
    {
        return config('shop.order_prefix', 'ORD').'-'.now()->format('ymd').'-'.strtoupper(Str::random(4));
    }

    private function notify(Order $order): void
    {
        $to = $this->settings->get('site.email') ?: config('mail.from.address');

        if (blank($to)) {
            return;
        }

        try {
            Mail::to($to)->send(new OrderCreatedMail($order));
        } catch (\Throwable) {
            // Письмо не должно ломать оформление заказа.
        }
    }
}

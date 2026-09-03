<?php

namespace App\Services\Shop;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function raw(): array
    {
        return Session::get($this->key(), []);
    }

    public function add(Product $product, int $qty = 1): void
    {
        $qty = max(1, $qty);
        $items = $this->raw();
        $current = (int) ($items[$product->id] ?? 0);
        $next = $current + $qty;

        $this->assertStock($product, $next);

        $items[$product->id] = $next;
        $this->put($items);
    }

    public function update(Product $product, int $qty): void
    {
        $items = $this->raw();

        if ($qty <= 0) {
            unset($items[$product->id]);
            $this->put($items);

            return;
        }

        $this->assertStock($product, $qty);
        $items[$product->id] = $qty;
        $this->put($items);
    }

    public function remove(int $productId): void
    {
        $items = $this->raw();
        unset($items[$productId]);
        $this->put($items);
    }

    public function clear(): void
    {
        Session::forget($this->key());
    }

    public function isEmpty(): bool
    {
        return $this->raw() === [];
    }

    /**
     * Пересчитывает корзину: убирает исчезнувшие товары и обрезает количество по остатку.
     */
    public function recalculate(): array
    {
        $items = $this->raw();
        $changed = false;

        foreach ($items as $productId => $qty) {
            $product = Product::query()->active()->find($productId);

            if (! $product || $product->quantity < 1) {
                unset($items[$productId]);
                $changed = true;

                continue;
            }

            $qty = (int) $qty;

            if ($qty > $product->quantity) {
                $items[$productId] = $product->quantity;
                $changed = true;
            }
        }

        if ($changed) {
            $this->put($items);
        }

        return $this->summary();
    }

    public function lines(): Collection
    {
        $items = $this->raw();
        $products = Product::query()
            ->with('attachment')
            ->whereIn('id', array_keys($items))
            ->get()
            ->keyBy('id');

        return collect($items)
            ->map(function (int $qty, int $productId) use ($products) {
                $product = $products->get($productId);

                if (! $product) {
                    return null;
                }

                $sum = (float) $product->price * $qty;

                return [
                    'product' => $product,
                    'qty' => $qty,
                    'sum' => $sum,
                ];
            })
            ->filter()
            ->values();
    }

    public function summary(): array
    {
        $lines = $this->lines();
        $count = (int) $lines->sum('qty');
        $total = (float) $lines->sum('sum');

        return [
            'lines' => $lines,
            'count' => $count,
            'total' => $total,
        ];
    }

    public function json(): array
    {
        $summary = $this->recalculate();

        return [
            'count' => $summary['count'],
            'total' => $summary['total'],
            'total_formatted' => shop_money($summary['total']),
            'empty' => $summary['count'] < 1,
            'lines' => $summary['lines']->map(fn (array $line) => [
                'id' => $line['product']->id,
                'qty' => $line['qty'],
                'max' => $line['product']->quantity,
                'sum_formatted' => shop_money($line['sum']),
            ])->values()->all(),
        ];
    }

    public function count(): int
    {
        return (int) array_sum($this->raw());
    }

    private function assertStock(Product $product, int $qty): void
    {
        if (! $product->is_active) {
            throw ValidationException::withMessages([
                'product' => 'Товар недоступен.',
            ]);
        }

        if ($qty > $product->quantity) {
            throw ValidationException::withMessages([
                'qty' => 'Недостаточно товара на складе. Доступно: '.$product->quantity,
            ]);
        }
    }

    private function put(array $items): void
    {
        Session::put($this->key(), $items);
    }

    private function key(): string
    {
        return config('shop.cart_session_key', 'shop_cart');
    }
}

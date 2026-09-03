<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ShopPayload
{
    public static function productCard(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'price' => (float) $product->price,
            'price_formatted' => shop_money($product->price),
            'old_price' => $product->old_price ? (float) $product->old_price : null,
            'old_price_formatted' => $product->old_price ? shop_money($product->old_price) : null,
            'quantity' => $product->quantity,
            'in_stock' => $product->inStock(),
            'cover_url' => $product->coverUrl(),
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
        ];
    }

    public static function product(Product $product, Collection $gallery, Collection $related): array
    {
        return array_merge(self::productCard($product), [
            'description' => $product->description,
            'gallery' => $gallery->map(fn ($image) => [
                'url' => $image->url(),
            ])->values()->all(),
            'related' => $related->map(fn (Product $item) => self::productCard($item))->values()->all(),
            'meta_description' => \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 160),
        ]);
    }

    public static function category(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'products_count' => $category->products_count ?? $category->products()->active()->count(),
        ];
    }

    public static function paginator(LengthAwarePaginator $paginator, callable $map): array
    {
        return [
            'data' => $paginator->getCollection()->map($map)->values()->all(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'links' => $paginator->linkCollection()->toArray(),
        ];
    }

    public static function cart(array $summary): array
    {
        return [
            'count' => $summary['count'],
            'total' => $summary['total'],
            'total_formatted' => shop_money($summary['total']),
            'empty' => $summary['count'] < 1,
            'lines' => collect($summary['lines'])->map(fn (array $line) => [
                'id' => $line['product']->id,
                'name' => $line['product']->name,
                'slug' => $line['product']->slug,
                'qty' => $line['qty'],
                'max' => $line['product']->quantity,
                'price_formatted' => shop_money($line['product']->price),
                'sum_formatted' => shop_money($line['sum']),
            ])->values()->all(),
        ];
    }

    public static function order(Order $order): array
    {
        return [
            'id' => $order->id,
            'number' => $order->number,
            'status' => $order->status->value ?? (string) $order->status,
            'total' => (float) $order->total,
            'total_formatted' => shop_money($order->total),
            'created_at' => $order->created_at?->toDateTimeString(),
            'items' => $order->items->map(fn ($item) => [
                'name' => $item->name,
                'qty' => $item->qty,
                'sum_formatted' => shop_money($item->sum),
            ])->values()->all(),
        ];
    }

    public static function user(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}

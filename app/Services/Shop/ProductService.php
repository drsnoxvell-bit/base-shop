<?php

namespace App\Services\Shop;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductService
{
    public function save(Product $product, array $data): Product
    {
        if (blank($data['slug'] ?? null) && filled($data['name'] ?? null)) {
            $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        }

        $product->fill($data)->save();

        return $product->refresh();
    }

    public function syncAttachments(Product $product, array $ids): void
    {
        $product->attachment()->sync($ids);
    }

    public function delete(Product $product): void
    {
        $product->attachment()->detach();
        $product->delete();
    }

    public function changeQuantity(Product $product, int $quantity): Product
    {
        if ($quantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Количество не может быть отрицательным.',
            ]);
        }

        $product->quantity = $quantity;
        $product->save();

        return $product;
    }

    public function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: Str::random(8);
        $slug = $base;
        $i = 1;

        while (
            Product::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}

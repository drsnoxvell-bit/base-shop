<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Shop;

use App\Models\Product;
use App\Orchid\Layouts\Shop\ProductEditLayout;
use App\Services\Shop\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class ProductEditScreen extends Screen
{
    public ?Product $product = null;

    public function query(Product $product): iterable
    {
        $product->load('attachment');

        return [
            'product' => $product,
        ];
    }

    public function name(): ?string
    {
        return $this->product?->exists ? 'Редактирование товара' : 'Новый товар';
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
                ->confirm('Удалить товар?')
                ->canSee((bool) $this->product?->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            ProductEditLayout::class,
        ];
    }

    public function save(Request $request, Product $product, ProductService $products): RedirectResponse
    {
        $data = $request->validate([
            'product.category_id' => ['required', 'exists:categories,id'],
            'product.name' => ['required', 'string', 'max:255'],
            'product.slug' => ['nullable', 'string', 'max:255'],
            'product.sku' => ['nullable', 'string', 'max:64'],
            'product.description' => ['nullable', 'string'],
            'product.price' => ['required', 'numeric', 'min:0'],
            'product.old_price' => ['nullable', 'numeric', 'min:0'],
            'product.quantity' => ['required', 'integer', 'min:0'],
            'product.sort' => ['nullable', 'integer', 'min:0'],
            'product.is_active' => ['sometimes'],
            'product.attachment' => ['nullable', 'array'],
        ])['product'];

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort'] = (int) ($data['sort'] ?? 0);
        $attachments = $request->input('product.attachment', []);

        $products->save($product, $data);
        $products->syncAttachments($product, $attachments);

        Toast::info('Товар сохранён');

        return redirect()->route('platform.shop.products');
    }

    public function remove(Product $product, ProductService $products): RedirectResponse
    {
        $products->delete($product);

        Toast::info('Товар удалён');

        return redirect()->route('platform.shop.products');
    }
}

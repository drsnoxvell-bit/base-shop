<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Shop;

use App\Models\Product;
use App\Orchid\Layouts\Shop\ProductListLayout;
use App\Services\Shop\ProductService;
use App\Support\ShopPermissions;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class ProductListScreen extends Screen
{
    public function permission(): ?iterable
    {
        return [ShopPermissions::PRODUCTS];
    }

    public function query(): iterable
    {
        return [
            'products' => Product::filters()->with(['category', 'attachment'])->defaultSort('id', 'desc')->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Товары';
    }

    public function description(): ?string
    {
        return 'Добавление, удаление и пересчёт остатков';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить')
                ->icon('bs.plus-circle')
                ->route('platform.shop.products.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            ProductListLayout::class,
        ];
    }

    public function remove(Request $request, ProductService $products): void
    {
        $product = Product::query()->findOrFail($request->get('id'));
        $products->delete($product);

        Toast::info('Товар удалён');
    }
}

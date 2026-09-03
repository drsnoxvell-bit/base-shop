<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Shop;

use App\Models\Product;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProductListLayout extends Table
{
    protected $target = 'products';

    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')->sort()->width('70'),
            TD::make('name', 'Название')
                ->sort()
                ->render(fn (Product $product) => Link::make($product->name)
                    ->route('platform.shop.products.edit', $product)),
            TD::make('category_id', 'Категория')
                ->render(fn (Product $product) => $product->category?->name),
            TD::make('price', 'Цена')
                ->sort()
                ->render(fn (Product $product) => shop_money($product->price)),
            TD::make('quantity', 'Остаток')->sort(),
            TD::make('is_active', 'Активен')
                ->render(fn (Product $product) => $product->is_active ? 'Да' : 'Нет'),
            TD::make('actions', '')
                ->align(TD::ALIGN_RIGHT)
                ->width('120px')
                ->render(fn (Product $product) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make('Изменить')
                            ->icon('bs.pencil')
                            ->route('platform.shop.products.edit', $product),
                        Button::make('Удалить')
                            ->icon('bs.trash3')
                            ->confirm('Удалить товар?')
                            ->method('remove', ['id' => $product->id]),
                    ])),
        ];
    }
}

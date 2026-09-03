<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Shop;

use App\Models\Category;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class CategoryListLayout extends Table
{
    protected $target = 'categories';

    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')->sort()->width('80'),
            TD::make('name', 'Название')
                ->sort()
                ->render(fn (Category $category) => Link::make($category->name)
                    ->route('platform.shop.categories.edit', $category)),
            TD::make('slug', 'Слаг'),
            TD::make('sort', 'Порядок')->sort(),
            TD::make('is_active', 'Активна')
                ->render(fn (Category $category) => $category->is_active ? 'Да' : 'Нет'),
            TD::make('actions', '')
                ->align(TD::ALIGN_RIGHT)
                ->width('120px')
                ->render(fn (Category $category) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make('Изменить')
                            ->icon('bs.pencil')
                            ->route('platform.shop.categories.edit', $category),
                        Button::make('Удалить')
                            ->icon('bs.trash3')
                            ->confirm('Удалить категорию?')
                            ->method('remove', ['id' => $category->id]),
                    ])),
        ];
    }
}

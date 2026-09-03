<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Shop;

use App\Models\Category;
use App\Orchid\Layouts\Shop\CategoryListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class CategoryListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'categories' => Category::filters()->defaultSort('sort')->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Категории';
    }

    public function description(): ?string
    {
        return 'Каталог магазина';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить')
                ->icon('bs.plus-circle')
                ->route('platform.shop.categories.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            CategoryListLayout::class,
        ];
    }

    public function remove(Request $request): void
    {
        Category::query()->findOrFail($request->get('id'))->delete();

        Toast::info('Категория удалена');
    }
}

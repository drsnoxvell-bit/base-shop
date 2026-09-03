<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Shop;

use App\Models\Category;
use App\Orchid\Layouts\Shop\CategoryEditLayout;
use App\Support\ShopPermissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class CategoryEditScreen extends Screen
{
    public ?Category $category = null;

    public function permission(): ?iterable
    {
        return [ShopPermissions::CATEGORIES];
    }

    public function query(Category $category): iterable
    {
        return [
            'category' => $category,
        ];
    }

    public function name(): ?string
    {
        return $this->category?->exists ? 'Редактирование категории' : 'Новая категория';
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
                ->confirm('Удалить категорию? Товары тоже будут удалены.')
                ->canSee((bool) $this->category?->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            CategoryEditLayout::class,
        ];
    }

    public function save(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'category.name' => ['required', 'string', 'max:255'],
            'category.slug' => ['nullable', 'string', 'max:255'],
            'category.description' => ['nullable', 'string'],
            'category.sort' => ['nullable', 'integer', 'min:0'],
            'category.is_active' => ['sometimes'],
        ])['category'];

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort'] = (int) ($data['sort'] ?? 0);

        $category->fill($data)->save();

        Toast::info('Категория сохранена');

        return redirect()->route('platform.shop.categories');
    }

    public function remove(Category $category): RedirectResponse
    {
        $category->delete();

        Toast::info('Категория удалена');

        return redirect()->route('platform.shop.categories');
    }
}

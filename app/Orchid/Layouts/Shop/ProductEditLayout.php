<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Shop;

use App\Models\Category;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Rows;

class ProductEditLayout extends Rows
{
    protected function fields(): iterable
    {
        return [
            Select::make('product.category_id')
                ->fromModel(Category::class, 'name')
                ->title('Категория')
                ->required()
                ->empty('Выберите категорию'),
            Input::make('product.name')
                ->title('Название')
                ->required(),
            Input::make('product.slug')
                ->title('Слаг')
                ->help('Если пусто — сгенерируется из названия'),
            Input::make('product.sku')
                ->title('Артикул'),
            TextArea::make('product.description')
                ->title('Описание')
                ->rows(5),
            Input::make('product.price')
                ->type('number')
                ->step('0.01')
                ->title('Цена')
                ->required(),
            Input::make('product.old_price')
                ->type('number')
                ->step('0.01')
                ->title('Старая цена'),
            Input::make('product.quantity')
                ->type('number')
                ->title('Остаток')
                ->required()
                ->help('Пересчитывается при оформлении и отмене заказа'),
            Input::make('product.sort')
                ->type('number')
                ->title('Порядок')
                ->value(0),
            CheckBox::make('product.is_active')
                ->title('Активен')
                ->sendTrueOrFalse()
                ->value(true),
            Upload::make('product.attachment')
                ->title('Галерея')
                ->groups('gallery')
                ->acceptedFiles('image/*')
                ->maxFiles(config('shop.max_gallery_images', 10))
                ->media()
                ->help('От 1 до 10 изображений. Первое станет обложкой.'),
        ];
    }
}

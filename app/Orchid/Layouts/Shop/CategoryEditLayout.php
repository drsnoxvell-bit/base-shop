<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Shop;

use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

class CategoryEditLayout extends Rows
{
    protected function fields(): iterable
    {
        return [
            Input::make('category.name')
                ->title('Название')
                ->required(),
            Input::make('category.slug')
                ->title('Слаг')
                ->help('Если пусто — сгенерируется из названия'),
            TextArea::make('category.description')
                ->title('Описание')
                ->rows(4),
            Input::make('category.sort')
                ->type('number')
                ->title('Порядок')
                ->value(0),
            CheckBox::make('category.is_active')
                ->title('Активна')
                ->sendTrueOrFalse()
                ->value(true),
        ];
    }
}

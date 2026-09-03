<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Shop;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

class SettingsSiteLayout extends Rows
{
    protected function fields(): iterable
    {
        return [
            Input::make('site.name')
                ->title('Название сайта')
                ->required(),
            TextArea::make('site.description')
                ->title('Описание')
                ->rows(3),
            Input::make('site.phone')
                ->title('Телефон'),
            Input::make('site.email')
                ->title('Email магазина')
                ->help('Сюда уходит письмо о новом заказе'),
            TextArea::make('site.address')
                ->title('Адрес')
                ->rows(2),
        ];
    }
}

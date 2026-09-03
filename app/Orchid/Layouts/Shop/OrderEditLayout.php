<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Shop;

use App\Enums\OrderStatus;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

class OrderEditLayout extends Rows
{
    protected function fields(): iterable
    {
        return [
            Input::make('order.number')
                ->title('Номер')
                ->readonly(),
            Input::make('order.name')
                ->title('Имя')
                ->readonly(),
            Input::make('order.phone')
                ->title('Телефон')
                ->readonly(),
            Input::make('order.email')
                ->title('Email')
                ->readonly(),
            TextArea::make('order.address')
                ->title('Адрес')
                ->rows(2)
                ->readonly(),
            TextArea::make('order.comment')
                ->title('Комментарий')
                ->rows(2)
                ->readonly(),
            Select::make('order.status')
                ->title('Статус')
                ->options(collect(OrderStatus::cases())->mapWithKeys(
                    fn (OrderStatus $status) => [$status->value => $status->label()]
                )->all())
                ->help('При отмене остатки возвращаются на склад'),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Shop;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class SettingsMailLayout extends Rows
{
    protected function fields(): iterable
    {
        return [
            Select::make('mail.mailer')
                ->title('Драйвер')
                ->options([
                    'smtp' => 'SMTP',
                    'log' => 'Лог (без отправки)',
                    'sendmail' => 'Sendmail',
                ]),
            Input::make('mail.host')
                ->title('SMTP хост'),
            Input::make('mail.port')
                ->title('Порт'),
            Input::make('mail.username')
                ->title('Логин'),
            Password::make('mail.password')
                ->title('Пароль')
                ->help('Оставьте пустым, чтобы не менять'),
            Select::make('mail.encryption')
                ->title('Шифрование')
                ->options([
                    '' => 'Нет',
                    'tls' => 'TLS',
                    'ssl' => 'SSL',
                ])
                ->empty('Нет'),
            Input::make('mail.from_address')
                ->title('From email'),
            Input::make('mail.from_name')
                ->title('From имя'),
        ];
    }
}

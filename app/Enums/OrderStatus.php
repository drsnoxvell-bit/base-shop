<?php

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case Processing = 'processing';
    case Done = 'done';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Новый',
            self::Processing => 'В работе',
            self::Done => 'Выполнен',
            self::Cancelled => 'Отменён',
        };
    }
}

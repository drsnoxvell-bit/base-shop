<?php

namespace App\Support;

class ShopStack
{
    public const BLADE = 'blade';

    public const INERTIA_VUE = 'inertia-vue';

    public const INERTIA_REACT = 'inertia-react';

    public const SPA_VUE = 'spa-vue';

    public const SPA_REACT = 'spa-react';

    public static function all(): array
    {
        return [
            self::BLADE => 'Blade (Laravel-шаблоны)',
            self::INERTIA_VUE => 'Inertia + Vue (монолит)',
            self::INERTIA_REACT => 'Inertia + React (монолит)',
            self::SPA_VUE => 'Laravel API + Vue SPA',
            self::SPA_REACT => 'Laravel API + React SPA',
        ];
    }

    public static function current(): string
    {
        $stack = (string) config('shop.stack', self::BLADE);

        return array_key_exists($stack, self::all()) ? $stack : self::BLADE;
    }

    public static function isBlade(): bool
    {
        return self::current() === self::BLADE;
    }

    public static function isInertia(): bool
    {
        return in_array(self::current(), [self::INERTIA_VUE, self::INERTIA_REACT], true);
    }

    public static function isSpa(): bool
    {
        return in_array(self::current(), [self::SPA_VUE, self::SPA_REACT], true);
    }

    public static function composerPackages(string $stack): array
    {
        return match ($stack) {
            self::INERTIA_VUE, self::INERTIA_REACT => ['inertiajs/inertia-laravel'],
            self::SPA_VUE, self::SPA_REACT => ['laravel/sanctum'],
            default => [],
        };
    }
}

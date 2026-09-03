# Обновления Laravel и Orchid

Проект рассчитан на безопасные минорные обновления. Мажорные версии поднимаются отдельно.

## Что уже сделано

- В `composer.json` caret-ограничения: `laravel/framework: ^12.0`, `orchid/platform: ^14.0`
- Нельзя править `vendor/` и публиковать Blade Orchid в `resources/views/vendor/platform`
- Витрина изолирована: `resources/views/shop`, `resources/css/app.css`, `app/Services/Shop`
- Админка — только Screens / Layouts / `PlatformProvider`
- После `composer update` автоматически выполняются `orchid:publish` и `view:clear`

## Минорное обновление (патчи 12.x / 14.x)

```bash
composer update
npm install
npm run build
php artisan migrate
php artisan about
```

Проверка:

```bash
php artisan test
```

## Мажорное обновление

1. Прочитать [Laravel upgrade](https://laravel.com/docs/upgrade) и [Orchid upgrade](https://orchid.software/en/docs/upgrade/)
2. Поднять PHP, если требуется (Laravel 13 — PHP 8.3+)
3. Изменить caret в `composer.json`, например `"laravel/framework": "^13.0"`
4. `composer update`
5. Прогнать тесты и пройти витрину: главная, карточка, корзина, checkout, `/admin`

Не копируйте ядро Orchid и не форкайте его шаблоны — это ломает следующие обновления.

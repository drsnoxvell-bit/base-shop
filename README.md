# Base Shop

Базовый интернет-магазин на Laravel 12 и Orchid 14. Ставится как Composer-проект, без `git clone`.

## Что входит

- Каталог, карточка товара с галереей, корзина, оформление заказа без онлайн-оплаты
- Админка Orchid: категории, товары, заказы, настройки сайта и SMTP
- Роли: администратор, редактор, пользователь
- Регистрация, вход, OAuth Яндекс и ВКонтакте

## Требования

- PHP 8.2+
- Composer 2
- Node.js 18+
- MySQL 5.7+ / 8.x

## Установка

В пустой каталог сайта (например `C:\OSPanel\home\mysite`):

```powershell
composer create-project drsnoxvell-bit/base-shop .
php artisan shop:install
```

Или в новую папку рядом:

```powershell
cd C:\OSPanel\home
composer create-project drsnoxvell-bit/base-shop my-shop
cd my-shop
php artisan shop:install
```

1. Composer скачает проект и зависимости, создаст `.env`, ключ приложения.
2. `php artisan shop:install` — выберите стек **1–5**, миграции, администратор, `npm`.

В OSPanel модуль MySQL должен быть включён. Если в `.env` останется `DB_HOST=127.0.0.1`, инсталлятор сам переключит на `mysql-8.0`. При желании пропишите до установки:

```
APP_URL=http://mysite
DB_HOST=mysql-8.0
DB_DATABASE=base_shop
DB_USERNAME=root
DB_PASSWORD=
```

Если `composer` пишет `Could not open input file: \composer.phar`, это сломанный `composer.bat` OSPanel. Вызовите:

```powershell
& "C:\ProgramData\ComposerSetup\bin\composer.bat" create-project drsnoxvell-bit/base-shop .
```

### Пока пакет ещё не на Packagist

Без публикации на [Packagist](https://packagist.org/packages/submit) Composer не знает имя `drsnoxvell-bit/base-shop`. Один раз укажите GitHub как источник (без JSON, работает в PowerShell):

```powershell
composer config -g repositories.base-shop vcs https://github.com/drsnoxvell-bit/base-shop.git
composer create-project drsnoxvell-bit/base-shop my-shop --stability=dev
cd my-shop
php artisan shop:install
```

После публикации на Packagist достаточно `composer create-project drsnoxvell-bit/base-shop my-shop`.

Стек витрины:

1. Blade — шаблоны Laravel, без Vue/React
2. Inertia + Vue — монолит Laravel + Vue
3. Inertia + React — монолит Laravel + React
4. Laravel API + Vue SPA
5. Laravel API + React SPA

Невыбранные фронты не ставятся. В установленной копии каталог `stubs/` удаляется.

Неинтерактивно:

```powershell
php artisan shop:install --stack=1
php artisan shop:install --stack=3
php artisan shop:install --stack=blade --no-interaction --skip-admin
```

Админка: `/admin`. Витрина: `/`.

Это **скелет приложения** (`type: project`), как Laravel. В уже готовый Laravel его нельзя поставить через `composer require`.

## OAuth Яндекс и ВКонтакте

В кабинете приложения укажите callback:

- `{APP_URL}/auth/yandex/callback`
- `{APP_URL}/auth/vkontakte/callback`

В `.env`:

```
YANDEX_CLIENT_ID=
YANDEX_CLIENT_SECRET=
VKONTAKTE_CLIENT_ID=
VKONTAKTE_CLIENT_SECRET=
```

Если пользователя ещё нет, он создаётся с ролью «пользователь». Если email совпал — вход привязывается к существующему аккаунту. Если ВК не отдал email, создаётся технический адрес `vkontakte-{id}@users.local`.

## Роли

- **Администратор** — каталог, заказы, настройки, пользователи и роли
- **Редактор** — категории, товары, заказы (без настроек и пользователей)
- **Пользователь** — витрина, профиль и свои заказы, без `/admin`

Регистрация и соцсети всегда создают роль «пользователь».

## Вклад в upstream

Публичный репозиторий только для чтения у всех, кроме владельца [drsnoxvell-bit](https://github.com/drsnoxvell-bit). `git push` в origin у установщика будет отклонён. Не добавляйте коллабораторов с правом Write.

Чтобы `composer create-project drsnoxvell-bit/base-shop` работал без ручного `repositories`, владелец один раз публикует пакет:

1. Закоммитить и запушить `main`
2. Поставить тег релиза, например `v1.1.0`
3. На [packagist.org/packages/submit](https://packagist.org/packages/submit) вставить `https://github.com/drsnoxvell-bit/base-shop` и привязать GitHub

См. [docs/UPGRADE.md](docs/UPGRADE.md).

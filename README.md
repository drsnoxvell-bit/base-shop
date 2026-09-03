# Base Shop

Базовый интернет-магазин на Laravel 12 и Orchid 14. Один репозиторий, выбор стека витрины при установке.

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

Репозиторий публичный. Клонировать и ставить может кто угодно; **писать в этот репозиторий может только владелец**. Свои правки — через форк и pull request. Rebase и слияние делает владелец.

Проще всего клонировать репозиторий (так надёжнее в Windows PowerShell):

```bash
git clone https://github.com/drsnoxvell-bit/base-shop.git my-shop
cd my-shop
composer install
php artisan shop:install
```

Либо `composer create-project`. В **PowerShell** не используйте `\"` и не ставьте `\` в конце строки:

```powershell
composer create-project drsnoxvell-bit/base-shop my-shop --stability=dev --repository='{"type":"vcs","url":"https://github.com/drsnoxvell-bit/base-shop.git"}'
```

Если `composer` пишет `Could not open input file: \composer.phar`, это сломанный `composer.bat` OSPanel (`COMPOSER_HOME` пустой). Вызовите рабочий Composer:

```powershell
& "C:\ProgramData\ComposerSetup\bin\composer.bat" install
```

После копирования файлов запустится `php artisan shop:install`. Выберите стек:

1. Blade — шаблоны Laravel, без Vue/React
2. Inertia + Vue — монолит Laravel + Vue
3. Inertia + React — монолит Laravel + React
4. Laravel API + Vue SPA — бэкенд Laravel и фронт Vue в одном проекте
5. Laravel API + React SPA — бэкенд Laravel и фронт React в одном проекте

Невыбранные фронты не ставятся: инсталлятор копирует только нужный stub и подключает только нужные Composer/npm пакеты. В установленной копии каталог `stubs/` удаляется (в исходном git-репозитории шаблоны остаются).

Неинтерактивно:

```bash
php artisan shop:install --stack=blade --no-interaction --skip-admin
php artisan shop:install --stack=inertia-vue
php artisan shop:install --stack=spa-react --keep-stubs
```

Затем:

```bash
# .env: APP_URL, MySQL, ключи OAuth при необходимости
php artisan migrate
php artisan db:seed --class=Database\\Seeders\\RoleSeeder
php artisan orchid:admin
npm install
npm run build
```

Админка: `/admin`. Витрина: `/`.

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

См. [docs/UPGRADE.md](docs/UPGRADE.md).

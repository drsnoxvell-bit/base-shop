# Base Shop

Базовый интернет-магазин на Laravel 12 и Orchid 14. Разворачивается как Composer-проект из приватного GitHub-репозитория.

- Витрина: главная, каталог, категория, карточка с галереей, корзина, оформление заказа
- Админка Orchid: категории, товары, заказы, настройки сайта и SMTP
- Заказ без онлайн-оплаты (заявка / при получении)

## Требования

- PHP 8.2+
- Composer 2
- Node.js 18+ (для Vite)
- SQLite или MySQL

## Установка из GitHub

Репозиторий приватный. Нужен доступ и токен:

```bash
composer config --global github-oauth.github.com YOUR_GITHUB_TOKEN
```

Затем:

```bash
composer create-project drsnoxvell-bit/base-shop my-shop --repository="{\"type\":\"vcs\",\"url\":\"https://github.com/drsnoxvell-bit/base-shop.git\"}"
```

Если тега ещё нет:

```bash
composer create-project drsnoxvell-bit/base-shop my-shop --stability=dev --repository="{\"type\":\"vcs\",\"url\":\"https://github.com/drsnoxvell-bit/base-shop.git\"}"
```

## Локальная настройка (OSPanel)

1. Скопировать `.env.example` в `.env` при необходимости
2. Указать `APP_URL` (например `http://baseLaravelShop`) и базу
3. Команды:

```bash
php artisan key:generate
php artisan storage:link
php artisan migrate
php artisan orchid:admin
npm install
npm run build
```

Админка: `/admin`

Демо-товары и категории создаются миграцией `2026_09_03_100004_seed_shop_demo_data`.

## Обновления Laravel и Orchid

См. [docs/UPGRADE.md](docs/UPGRADE.md). Коротко: внутри текущего мажора — `composer update`, шаблоны Orchid не копируются в проект, витрина живёт в своих Blade/CSS.

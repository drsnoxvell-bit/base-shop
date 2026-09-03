# Base Shop

Базовый интернет-магазин на Laravel 12 и Orchid 14. Основной способ установки — **Docker** (на компьютере достаточно Docker Desktop). PHP, Composer, Node, npm и MySQL поднимаются контейнерами.

Пакет: [packagist.org/packages/drsnoxvell-bit/base-shop](https://packagist.org/packages/drsnoxvell-bit/base-shop).

## Что входит

- Каталог, карточка товара с галереей, корзина, оформление заказа без онлайн-оплаты
- Админка Orchid: категории, товары, заказы, настройки сайта и SMTP
- Роли: администратор, редактор, пользователь
- Регистрация, вход, OAuth Яндекс и ВКонтакте

## Требования

- Docker Desktop (или Docker Engine + Compose v2)

Локальные PHP, Node, nvm и MySQL **не нужны**.

## Установка (Docker)

В **пустой** папке сайта:

```powershell
cd C:\OSPanel\home\mysite
docker run --rm -v ${PWD}:/app -w /app composer:2 create-project drsnoxvell-bit/base-shop . --stability=dev --ignore-platform-reqs
.\docker\setup.ps1
docker compose exec app php artisan shop:install
```

Linux / macOS:

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD":/app -w /app composer:2 create-project drsnoxvell-bit/base-shop . --stability=dev --ignore-platform-reqs
bash docker/setup.sh
docker compose exec app php artisan shop:install
```

`setup` спросит версии **PHP** (8.2 / 8.3 / 8.4), **Node** (18 / 20 / 22) и **MySQL** (8.0 / 8.4), запишет их в `.env` и поднимет контейнеры.

Сайт: [http://localhost:8080](http://localhost:8080). Админка: [http://localhost:8080/admin](http://localhost:8080/admin).

`--stability=dev` нужен, пока на Packagist нет стабильного тега (есть ветка `main`). Команды запускайте **по одной**.

`php artisan shop:install` — стек витрины **1–5**, миграции, администратор, npm.

1. Blade — шаблоны Laravel, без Vue/React
2. Inertia + Vue — монолит Laravel + Vue
3. Inertia + React — монолит Laravel + React
4. Laravel API + Vue SPA
5. Laravel API + React SPA

Невыбранные фронты не ставятся.

Неинтерактивно:

```powershell
docker compose exec app php artisan shop:install --stack=1
docker compose exec app php artisan shop:install --stack=3
```

### Смена версий

В `.env` измените `PHP_VERSION`, `NODE_VERSION` или `MYSQL_VERSION` и пересоберите:

```powershell
docker compose build --no-cache app
docker compose up -d
```

Либо снова запустите `.\docker\setup.ps1` / `docker/setup.sh`.

## Запасной путь: OSPanel / локальный PHP

Если Docker не используете:

```powershell
composer create-project drsnoxvell-bit/base-shop . --stability=dev
php artisan shop:install
```

Нужны PHP 8.2+, Composer 2, Node.js 18+, MySQL. В OSPanel модуль MySQL должен быть включён; инсталлятор сам поставит `DB_HOST=mysql-8.0`, если `127.0.0.1` недоступен.

Если `composer` пишет `Could not open input file: \composer.phar`:

```powershell
& "C:\ProgramData\ComposerSetup\bin\composer.bat" create-project drsnoxvell-bit/base-shop . --stability=dev
```

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

Пакет: [packagist.org/packages/drsnoxvell-bit/base-shop](https://packagist.org/packages/drsnoxvell-bit/base-shop). Новый релиз — git-тег (`v1.1.0` и т.п.), Packagist подхватит сам.

См. [docs/UPGRADE.md](docs/UPGRADE.md).

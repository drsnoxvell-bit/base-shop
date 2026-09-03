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

## Установка

Нужен **запущенный Docker Desktop** (кит в трее не анимируется, статус Running).

В **пустой** папке сайта. Не используйте `irm | iex` (PowerShell 5.1 ломает кодировку). Скачайте файл и запустите его. Папка может содержать сам `install.ps1` — установщик скачает проект во временный каталог.

```powershell
cd C:\OSPanel\home\mysite
Invoke-WebRequest -Uri https://raw.githubusercontent.com/drsnoxvell-bit/base-shop/main/install.ps1 -OutFile install.ps1
powershell -NoProfile -ExecutionPolicy Bypass -File .\install.ps1
```

```powershell
Invoke-WebRequest -Uri https://raw.githubusercontent.com/drsnoxvell-bit/base-shop/main/install.ps1 -OutFile install.ps1
powershell -NoProfile -ExecutionPolicy Bypass -File .\install.ps1
```

Если репозиторий уже на диске:

```powershell
cd C:\OSPanel\home\baseLaravelShop
.\install.bat
```

Linux / macOS:

```bash
curl -fsSL https://raw.githubusercontent.com/drsnoxvell-bit/base-shop/main/install.sh | bash
```

Установщик сам: проверит Docker, скачает проект, спросит версии PHP / Node / MySQL, поднимет контейнеры и запустит `shop:install` (стек витрины 1–5).

Сайт: [http://localhost:8080](http://localhost:8080). Админка: [http://localhost:8080/admin](http://localhost:8080/admin).

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

Либо снова запустите `.\install.bat` / `bash install.sh`.

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

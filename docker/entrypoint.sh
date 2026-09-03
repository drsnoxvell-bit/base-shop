#!/bin/sh
set -e

cd /var/www/html

if [ -d storage ]; then
    mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
    chmod -R ug+rwx storage bootstrap/cache || true
fi

if [ -f artisan ] && [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

wait_for_mysql() {
    host="${DB_HOST:-mysql}"
    user="${DB_USERNAME:-root}"
    pass="${DB_PASSWORD:-secret}"
    i=0
    while [ "$i" -lt 60 ]; do
        if php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';port=3306', getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: 'secret'); exit(0); } catch (Throwable \$e) { exit(1); }" \
            >/dev/null 2>&1; then
            return 0
        fi
        i=$((i + 1))
        sleep 2
    done
    echo "MySQL (${host}) не ответил за 120 с." >&2
    return 1
}

export DB_HOST="${DB_HOST:-mysql}"
export DB_USERNAME="${DB_USERNAME:-root}"
export DB_PASSWORD="${DB_PASSWORD:-secret}"

if [ -f composer.json ]; then
    composer install --no-interaction --prefer-dist --no-ansi
fi

if [ -f artisan ]; then
    wait_for_mysql
    php artisan key:generate --ansi >/dev/null 2>&1 || true
    php artisan storage:link --ansi >/dev/null 2>&1 || true
fi

exec "$@"

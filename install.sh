#!/usr/bin/env bash
set -euo pipefail

package="drsnoxvell-bit/base-shop"
root="$(cd "$(dirname "$0")" && pwd)"
cd "$root"

assert_docker() {
    echo "Проверяю Docker..."
    if docker info >/dev/null 2>&1; then
        return
    fi
    echo
    echo "Docker не запущен."
    echo "Установите Docker Desktop / Docker Engine, дождитесь Running и повторите: bash install.sh"
    exit 1
}

install_project() {
    if [ -f docker-compose.yml ] && [ -f artisan ]; then
        return
    fi
    echo "Скачиваю ${package}..."
    docker run --rm -u "$(id -u):$(id -g)" -v "$root":/app -w /app composer:2 \
        create-project "$package" . --stability=dev --ignore-platform-reqs
}

assert_docker
install_project
bash "$root/docker/setup.sh"
echo
echo "Запускаю php artisan shop:install..."
docker compose exec -it app php artisan shop:install || docker compose exec app php artisan shop:install

echo
echo "Готово."
echo "Сайт:    http://localhost:8080"
echo "Админка: http://localhost:8080/admin"

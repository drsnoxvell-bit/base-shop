#!/usr/bin/env bash
set -euo pipefail

root="$(cd "$(dirname "$0")/.." && pwd)"
cd "$root"

if ! docker info >/dev/null 2>&1; then
    echo "Docker не запущен. Дождитесь Running и повторите: bash install.sh" >&2
    exit 1
fi

php_versions=(8.2 8.3 8.4)
node_versions=(18 20 22)
mysql_versions=(8.0 8.4)

php_default=8.3
node_default=22
mysql_default=8.0

set_env() {
    local key="$1"
    local value="$2"
    local file="$root/.env"

    if grep -qE "^${key}=" "$file"; then
        sed -i.bak -E "s|^${key}=.*|${key}=${value}|" "$file"
        rm -f "$file.bak"
    else
        printf '\n%s=%s\n' "$key" "$value" >> "$file"
    fi
}

choose() {
    local label="$1"
    shift
    local default="$1"
    shift
    local options=("$@")
    local i=1

    echo "$label" >&2
    for opt in "${options[@]}"; do
        if [ "$opt" = "$default" ]; then
            echo "  $i. $opt (по умолчанию)" >&2
        else
            echo "  $i. $opt" >&2
        fi
        i=$((i + 1))
    done

    local answer=""
    read -r -p "Номер [1-${#options[@]}]: " answer || true
    if [ -z "$answer" ]; then
        printf '%s\n' "$default"
        return
    fi

    if [[ "$answer" =~ ^[0-9]+$ ]] && [ "$answer" -ge 1 ] && [ "$answer" -le "${#options[@]}" ]; then
        printf '%s\n' "${options[$((answer - 1))]}"
        return
    fi

    for opt in "${options[@]}"; do
        if [ "$answer" = "$opt" ]; then
            printf '%s\n' "$opt"
            return
        fi
    done

    printf '%s\n' "$default"
}

if [ ! -f .env ]; then
    cp .env.example .env
    echo "Создан .env из .env.example"
fi

echo "Версии для Docker-стека"
echo

php_version="$(choose "PHP" "$php_default" "${php_versions[@]}")"
node_version="$(choose "Node.js" "$node_default" "${node_versions[@]}")"
mysql_version="$(choose "MySQL" "$mysql_default" "${mysql_versions[@]}")"

set_env PHP_VERSION "$php_version"
set_env NODE_VERSION "$node_version"
set_env MYSQL_VERSION "$mysql_version"
set_env COMPOSER_VERSION 2
set_env APP_URL "http://localhost:8080"
set_env APP_PORT 8080
set_env FORWARD_DB_PORT 3307
set_env DB_HOST mysql
set_env DB_PORT 3306
set_env DB_DATABASE base_shop
set_env DB_USERNAME root
set_env DB_PASSWORD secret
set_env SHOP_DOCKER true

echo
echo "PHP=$php_version  Node=$node_version  MySQL=$mysql_version"
echo "Собираю контейнеры..."

docker compose up --build -d

echo
echo "Готово: http://localhost:8080"
echo "Дальше: docker compose exec app php artisan shop:install"

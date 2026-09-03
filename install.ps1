#Requires -Version 5.1
# Установка Base Shop одной командой. Нужен запущенный Docker Desktop.
$ErrorActionPreference = 'Stop'

$Package = 'drsnoxvell-bit/base-shop'
$Root = if ($PSScriptRoot) { $PSScriptRoot } else { (Get-Location).Path }
Set-Location $Root

function Assert-Docker {
    Write-Host 'Проверяю Docker...'
    try {
        docker info 1>$null 2>$null
        if ($LASTEXITCODE -eq 0) {
            return
        }
    } catch {
    }

    Write-Host ''
    Write-Host 'Docker не запущен. Так ставить нельзя: PHP, MySQL и Node живут в контейнерах.'
    Write-Host '1. Установите Docker Desktop: https://www.docker.com/products/docker-desktop/'
    Write-Host '2. Откройте Docker Desktop и дождитесь статуса Running (кит перестанет анимироваться).'
    Write-Host '3. Снова запустите install.bat или: powershell -File .\install.ps1'
    Write-Host ''
    exit 1
}

function Install-Project {
    if ((Test-Path (Join-Path $Root 'docker-compose.yml')) -and (Test-Path (Join-Path $Root 'artisan'))) {
        return
    }

    Write-Host "Скачиваю $Package..."
    docker run --rm -v "${Root}:/app" -w /app composer:2 create-project $Package . --stability=dev --ignore-platform-reqs
    if ($LASTEXITCODE -ne 0) {
        Write-Host 'Не удалось скачать проект. Папка должна быть пустой.'
        exit 1
    }
}

function Invoke-Setup {
    $setup = Join-Path $Root 'docker\setup.ps1'
    if (-not (Test-Path $setup)) {
        Write-Host 'Не найден docker\setup.ps1. Сначала должен успешно завершиться create-project.'
        exit 1
    }
    & $setup
    if ($LASTEXITCODE -ne 0) {
        exit $LASTEXITCODE
    }
}

function Invoke-ShopInstall {
    Write-Host ''
    Write-Host 'Запускаю php artisan shop:install (стек 1–5, админ, npm)...'
    docker compose exec -it app php artisan shop:install
    if ($LASTEXITCODE -ne 0) {
        docker compose exec app php artisan shop:install
    }
}

Assert-Docker
Install-Project
Invoke-Setup
Invoke-ShopInstall

Write-Host ''
Write-Host 'Готово.'
Write-Host 'Сайт:    http://localhost:8080'
Write-Host 'Админка: http://localhost:8080/admin'

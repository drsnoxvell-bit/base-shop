#Requires -Version 5.1
$ErrorActionPreference = 'Stop'

$Package = 'drsnoxvell-bit/base-shop'
$Root = if ($PSScriptRoot) { $PSScriptRoot } else { (Get-Location).Path }
Set-Location $Root

function Assert-Docker {
    Write-Host 'Checking Docker...'
    try {
        docker info 1>$null 2>$null
        if ($LASTEXITCODE -eq 0) {
            return
        }
    } catch {
    }

    Write-Host ''
    Write-Host 'Docker is not running. PHP, MySQL and Node run inside containers.'
    Write-Host '1. Install Docker Desktop: https://www.docker.com/products/docker-desktop/'
    Write-Host '2. Start Docker Desktop and wait until status is Running.'
    Write-Host '3. Run install.bat again (do not use irm | iex).'
    Write-Host ''
    exit 1
}

function Install-Project {
    if ((Test-Path (Join-Path $Root 'docker-compose.yml')) -and (Test-Path (Join-Path $Root 'artisan'))) {
        Write-Host 'Project files already present.'
        return
    }

    $stage = Join-Path $Root '.shop-stage'
    if (Test-Path $stage) {
        Remove-Item $stage -Recurse -Force
    }
    New-Item -ItemType Directory -Path $stage | Out-Null

    Write-Host "Downloading $Package (folder may contain install.ps1)..."
    docker run --rm -v "${stage}:/app" -w /app composer:2 create-project $Package . --stability=dev --ignore-platform-reqs
    if ($LASTEXITCODE -ne 0) {
        Write-Host 'Download failed.'
        exit 1
    }

    Get-ChildItem -LiteralPath $stage -Force | ForEach-Object {
        Copy-Item -LiteralPath $_.FullName -Destination (Join-Path $Root $_.Name) -Recurse -Force
    }
    Remove-Item -LiteralPath $stage -Recurse -Force
}

function Invoke-Setup {
    $setup = Join-Path $Root 'docker\setup.ps1'
    if (-not (Test-Path $setup)) {
        Write-Host 'docker\setup.ps1 not found. create-project must finish first.'
        exit 1
    }
    & powershell -NoProfile -ExecutionPolicy Bypass -File $setup
    if ($LASTEXITCODE -ne 0) {
        exit $LASTEXITCODE
    }
}

function Invoke-ShopInstall {
    Write-Host ''
    Write-Host 'Running php artisan shop:install...'
    docker compose exec -it app php artisan shop:install
    if ($LASTEXITCODE -ne 0) {
        docker compose exec app php artisan shop:install
    }
}

Write-Host 'Installer v2 (downloads into .shop-stage)...'
Assert-Docker
Install-Project
Invoke-Setup
Invoke-ShopInstall

Write-Host ''
Write-Host 'Done.'
Write-Host 'Shop:  http://localhost:8080'
Write-Host 'Admin: http://localhost:8080/admin'

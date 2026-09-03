#Requires -Version 5.1
$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
if (-not $root) {
    $root = Split-Path -Parent $MyInvocation.MyCommand.Path
    $root = Split-Path -Parent $root
}
Set-Location $root

function Assert-Docker {
    try {
        docker info 1>$null 2>$null
        if ($LASTEXITCODE -eq 0) {
            return
        }
    } catch {
    }
    Write-Host 'Docker не запущен. Откройте Docker Desktop, дождитесь Running и повторите установку: .\install.bat'
    exit 1
}

Assert-Docker

function Set-EnvValue([string]$Key, [string]$Value) {
    $file = Join-Path $root '.env'
    $content = Get-Content -Raw -Path $file
    $pattern = "(?m)^$([regex]::Escape($Key))=.*"
    if ($content -match $pattern) {
        $content = [regex]::Replace($content, $pattern, "$Key=$Value")
    } else {
        $content = $content.TrimEnd() + "`n$Key=$Value`n"
    }
    [System.IO.File]::WriteAllText($file, $content)
}

function Choose-Version([string]$Label, [string[]]$Options, [string]$Default) {
    Write-Host $Label
    for ($i = 0; $i -lt $Options.Length; $i++) {
        $mark = if ($Options[$i] -eq $Default) { ' (по умолчанию)' } else { '' }
        Write-Host ("  {0}. {1}{2}" -f ($i + 1), $Options[$i], $mark)
    }
    $answer = Read-Host "Номер [1-$($Options.Length)]"
    if ([string]::IsNullOrWhiteSpace($answer)) {
        return $Default
    }
    $n = 0
    if ([int]::TryParse($answer, [ref]$n) -and $n -ge 1 -and $n -le $Options.Length) {
        return $Options[$n - 1]
    }
    if ($Options -contains $answer) {
        return $answer
    }
    return $Default
}

if (-not (Test-Path '.env')) {
    Copy-Item '.env.example' '.env'
    Write-Host 'Создан .env из .env.example'
}

Write-Host 'Версии для Docker-стека'
Write-Host ''

$phpVersion = Choose-Version 'PHP' @('8.2', '8.3', '8.4') '8.3'
$nodeVersion = Choose-Version 'Node.js' @('18', '20', '22') '22'
$mysqlVersion = Choose-Version 'MySQL' @('8.0', '8.4') '8.0'

Set-EnvValue 'PHP_VERSION' $phpVersion
Set-EnvValue 'NODE_VERSION' $nodeVersion
Set-EnvValue 'MYSQL_VERSION' $mysqlVersion
Set-EnvValue 'COMPOSER_VERSION' '2'
Set-EnvValue 'APP_URL' 'http://localhost:8080'
Set-EnvValue 'APP_PORT' '8080'
Set-EnvValue 'FORWARD_DB_PORT' '3307'
Set-EnvValue 'DB_HOST' 'mysql'
Set-EnvValue 'DB_PORT' '3306'
Set-EnvValue 'DB_DATABASE' 'base_shop'
Set-EnvValue 'DB_USERNAME' 'root'
Set-EnvValue 'DB_PASSWORD' 'secret'
Set-EnvValue 'SHOP_DOCKER' 'true'

Write-Host ''
Write-Host "PHP=$phpVersion  Node=$nodeVersion  MySQL=$mysqlVersion"
Write-Host 'Собираю контейнеры...'

docker compose up --build -d

Write-Host ''
Write-Host 'Готово: http://localhost:8080'
Write-Host 'Дальше: docker compose exec app php artisan shop:install'

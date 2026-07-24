# Script de inicio local (sin Docker) para el Sistema de Taller Mecánico
# Este script inicia Laravel y Vite automáticamente

$ErrorActionPreference = 'Stop'

Write-Host "Iniciando Sistema de Taller Mecánico (modo local)..." -ForegroundColor Green

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $projectRoot

# Verificar PHP
try {
    php --version | Out-Null
    Write-Host "PHP está disponible ✓" -ForegroundColor Green
} catch {
    Write-Host "Error: PHP no está instalado o no está en el PATH." -ForegroundColor Red
    exit 1
}

# Verificar Composer
try {
    composer --version | Out-Null
    Write-Host "Composer está disponible ✓" -ForegroundColor Green
} catch {
    Write-Host "Error: Composer no está instalado o no está en el PATH." -ForegroundColor Red
    exit 1
}

# Verificar Node.js
try {
    node --version | Out-Null
    Write-Host "Node.js está disponible ✓" -ForegroundColor Green
} catch {
    Write-Host "Error: Node.js no está instalado o no está en el PATH." -ForegroundColor Red
    exit 1
}

# Crear archivo .env si no existe
if (-not (Test-Path ".env")) {
    Write-Host "Creando archivo .env..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env" -Force
    php artisan key:generate --force | Out-Null
}

# Instalar dependencias si es necesario
if (-not (Test-Path "vendor")) {
    Write-Host "Instalando dependencias de PHP..." -ForegroundColor Yellow
    composer install
}

if (-not (Test-Path "node_modules")) {
    Write-Host "Instalando dependencias de Node..." -ForegroundColor Yellow
    npm install
}

# Crear base de datos SQLite si no existe
if (-not (Test-Path "database/database.sqlite")) {
    Write-Host "Creando base de datos SQLite..." -ForegroundColor Yellow
    New-Item -ItemType File -Path "database/database.sqlite" -Force | Out-Null
}

# Ejecutar migraciones
Write-Host "Ejecutando migraciones de base de datos..." -ForegroundColor Yellow
php artisan migrate --force

# Ejecutar seeders
Write-Host "Cargando datos de prueba..." -ForegroundColor Yellow
php artisan db:seed --force

# Compilar assets
Write-Host "Compilando assets..." -ForegroundColor Yellow
npm run build

# Limpiar caché
Write-Host "Limpiando caché..." -ForegroundColor Yellow
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Iniciar backend y frontend
Write-Host "Iniciando backend en http://127.0.0.1:8000..." -ForegroundColor Yellow
Start-Process powershell.exe -ArgumentList "-NoExit", "-Command", "Set-Location '$projectRoot'; php artisan serve --host=0.0.0.0 --port=8000" -WindowStyle Normal | Out-Null

Write-Host "Iniciando frontend en http://127.0.0.1:5173..." -ForegroundColor Yellow
Start-Process powershell.exe -ArgumentList "-NoExit", "-Command", "Set-Location '$projectRoot'; npm run dev -- --host 0.0.0.0" -WindowStyle Normal | Out-Null

Write-Host "✓ Sistema iniciado en modo local!" -ForegroundColor Green
Write-Host "✓ Aplicación disponible en: http://127.0.0.1:8000" -ForegroundColor Green
Write-Host "✓ Vite disponible en: http://127.0.0.1:5173" -ForegroundColor Green

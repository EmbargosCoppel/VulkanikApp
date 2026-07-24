# Script de inicio con Docker para el Sistema de Taller Mecánico
# Levanta Laravel, MySQL y Redis automáticamente

$ErrorActionPreference = 'Stop'

Write-Host "Iniciando Sistema de Taller Mecánico con Docker..." -ForegroundColor Green

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $projectRoot

# Verificar Docker
try {
    docker --version | Out-Null
    Write-Host "Docker está disponible ✓" -ForegroundColor Green
} catch {
    Write-Host "Error: Docker no está instalado o no está en el PATH." -ForegroundColor Red
    exit 1
}

# Crear archivo .env si no existe
if (-not (Test-Path ".env")) {
    Write-Host "Creando archivo .env..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env" -Force
}

# Levantar contenedores
Write-Host "Levantando contenedores Docker..." -ForegroundColor Yellow
docker compose up -d --build

# Esperar a que la base de datos esté lista
Write-Host "Esperando a que los servicios estén listos..." -ForegroundColor Yellow
Start-Sleep -Seconds 15

# Ejecutar migraciones
Write-Host "Ejecutando migraciones..." -ForegroundColor Yellow
docker compose exec -T laravel.test php artisan migrate --force

# Ejecutar seeders
Write-Host "Cargando datos de prueba..." -ForegroundColor Yellow
docker compose exec -T laravel.test php artisan db:seed --force

# Limpiar caché
Write-Host "Limpiando caché..." -ForegroundColor Yellow
docker compose exec -T laravel.test php artisan cache:clear
docker compose exec -T laravel.test php artisan config:clear
docker compose exec -T laravel.test php artisan view:clear

Write-Host "✓ Sistema iniciado correctamente con Docker" -ForegroundColor Green
Write-Host "✓ App: http://localhost" -ForegroundColor Green
Write-Host "✓ Base de datos MySQL: localhost:3307" -ForegroundColor Green
Write-Host "✓ Redis: localhost:6379" -ForegroundColor Green

# setup-and-run.ps1
# Script de configuración y ejecución automática para Vulcanizadora Don Chuy
# Ejecutar desde PowerShell: .\setup-and-run.ps1

param(
    [switch]$SkipInstall = $false,
    [switch]$Production = $false
)

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Vulcanizadora Don Chuy - Setup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Cambiar al directorio del proyecto
Set-Location $PSScriptRoot

# 1. Verificar .env
Write-Host "[1/8] Verificando archivo .env..." -ForegroundColor Yellow
if (-not (Test-Path ".env")) {
    Write-Host "  -> Creando .env desde .env.example" -ForegroundColor Gray
    Copy-Item ".env.example" ".env"
} else {
    Write-Host "  -> .env ya existe" -ForegroundColor Green
}

# 2. Instalar dependencias de Composer
if (-not $SkipInstall) {
    Write-Host "[2/8] Instalando dependencias de PHP (Composer)..." -ForegroundColor Yellow
    if (-not (Test-Path "vendor")) {
        Write-Host "  -> Ejecutando composer install..." -ForegroundColor Gray
        composer install --no-interaction --prefer-dist
    } else {
        Write-Host "  -> vendor/ ya existe" -ForegroundColor Green
    }

    # 3. Instalar dependencias de Node.js
    Write-Host "[3/8] Instalando dependencias de Node.js (npm)..." -ForegroundColor Yellow
    if (-not (Test-Path "node_modules")) {
        Write-Host "  -> Ejecutando npm install..." -ForegroundColor Gray
        npm install
    } else {
        Write-Host "  -> node_modules/ ya existe" -ForegroundColor Green
    }
} else {
    Write-Host "[2/8] Saltando instalación de dependencias (SkipInstall)" -ForegroundColor Gray
    Write-Host "[3/8] Saltando instalación de dependencias (SkipInstall)" -ForegroundColor Gray
}

# 4. Generar APP_KEY
Write-Host "[4/8] Generando APP_KEY..." -ForegroundColor Yellow
$envContent = Get-Content ".env" -Raw
if ($envContent -match "APP_KEY=base64:" -and $envContent -match "APP_KEY=base64:[a-zA-Z0-9+/=]{20,}") {
    Write-Host "  -> APP_KEY ya está configurada" -ForegroundColor Green
} else {
    Write-Host "  -> Generando nueva clave..." -ForegroundColor Gray
    php artisan key:generate
}

# 5. Ejecutar migraciones y seeders
Write-Host "[5/8] Ejecutando migraciones y seeders..." -ForegroundColor Yellow
if ($Production) {
    php artisan migrate:fresh --seed --force
} else {
    php artisan migrate:fresh --seed
}

# 6. Compilar assets frontend
Write-Host "[6/8] Compilando assets frontend..." -ForegroundColor Yellow
if ($Production) {
    npm run build
} else {
    Write-Host "  -> Modo desarrollo: Vite servirá assets en caliente" -ForegroundColor Gray
}

# 7. Cachear configuración
Write-Host "[7/8] Cacheando configuración..." -ForegroundColor Yellow
if ($Production) {
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
} else {
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
}

# 8. Crear enlace de storage
Write-Host "[8/8] Creando enlace de storage..." -ForegroundColor Yellow
php artisan storage:link 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "  -> Enlace creado" -ForegroundColor Green
} else {
    Write-Host "  -> Enlace ya existe o no se pudo crear" -ForegroundColor Gray
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  ¡Configuración completada!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Credenciales de prueba:" -ForegroundColor Cyan
Write-Host "  Admin:    admin@taller.com / (ver ADMIN_PASSWORD en .env)" -ForegroundColor White
Write-Host "  Mecánico: mecanico@taller.com / (ver MECANICO_PASSWORD en .env)" -ForegroundColor White
Write-Host ""

# Iniciar servidores
if ($Production) {
    Write-Host "Iniciando servidor de producción..." -ForegroundColor Yellow
    php artisan serve
} else {
    Write-Host "Iniciando servidores de desarrollo..." -ForegroundColor Yellow
    Write-Host "  - Laravel: http://localhost:8000" -ForegroundColor Gray
    Write-Host "  - Vite:    http://localhost:5173" -ForegroundColor Gray
    Write-Host ""
    npm run start
}

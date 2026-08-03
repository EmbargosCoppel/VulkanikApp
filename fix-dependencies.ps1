# Script para solucionar problemas de dependencias
# Ejecutar en PowerShell: .\fix-dependencies.ps1

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Solucionando dependencias de Composer" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Set-Location $PSScriptRoot

# Paso 1: Limpiar cache de Laravel
Write-Host "[1/5] Limpiando cache de Laravel..." -ForegroundColor Yellow
if (Test-Path "bootstrap/cache/packages.php") {
    Remove-Item "bootstrap/cache/packages.php" -Force
    Write-Host "  -> Cache eliminada" -ForegroundColor Green
} else {
    Write-Host "  -> No hay cache que eliminar" -ForegroundColor Gray
}

# Paso 2: Eliminar composer.lock
Write-Host "[2/5] Eliminando composer.lock..." -ForegroundColor Yellow
if (Test-Path "composer.lock") {
    Remove-Item "composer.lock" -Force
    Write-Host "  -> composer.lock eliminado" -ForegroundColor Green
} else {
    Write-Host "  -> No existe composer.lock" -ForegroundColor Gray
}

# Paso 3: Eliminar carpeta vendor
Write-Host "[3/5] Eliminando carpeta vendor..." -ForegroundColor Yellow
if (Test-Path "vendor") {
    Remove-Item "vendor" -Recurse -Force
    Write-Host "  -> vendor/ eliminado" -ForegroundColor Green
} else {
    Write-Host "  -> No existe carpeta vendor" -ForegroundColor Gray
}

# Paso 4: Configurar Composer e instalar dependencias
Write-Host "[4/5] Instalando dependencias (esto puede tardar 2-3 minutos)..." -ForegroundColor Yellow
Write-Host "  -> Deshabilitando bloqueo de security advisories..." -ForegroundColor Gray
composer config --global policy.advisories.block false

Write-Host "  -> Ejecutando composer install..." -ForegroundColor Gray
composer install --no-interaction --prefer-dist --ignore-platform-reqs

if ($LASTEXITCODE -eq 0) {
    Write-Host "  -> Dependencias instaladas correctamente" -ForegroundColor Green
} else {
    Write-Host "  -> Error al instalar dependencias" -ForegroundColor Red
    Write-Host "  -> Intentando con --no-scripts..." -ForegroundColor Yellow
    composer install --no-interaction --prefer-dist --ignore-platform-reqs --no-scripts
}

# Paso 5: Regenerar autoload
Write-Host "[5/5] Regenerando autoload de Composer..." -ForegroundColor Yellow
composer dump-autoload

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  Proceso completado" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

if ($LASTEXITCODE -eq 0) {
    Write-Host "OK - Ahora puedes ejecutar: .\setup-and-run.ps1" -ForegroundColor Cyan
} else {
    Write-Host "ERROR - Hubo errores. Revisa la salida anterior." -ForegroundColor Red
}

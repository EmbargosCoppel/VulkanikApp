# Script de inicio automático para el Sistema de Taller Mecánico
# Este script inicia Docker Compose y ejecuta los comandos necesarios de Laravel

Write-Host "Iniciando Sistema de Taller Mecánico..." -ForegroundColor Green

# Verificar si Docker está corriendo
try {
    docker ps | Out-Null
    Write-Host "Docker está corriendo ✓" -ForegroundColor Green
} catch {
    Write-Host "Error: Docker no está corriendo. Por favor inicia Docker Desktop primero." -ForegroundColor Red
    exit 1
}

# Iniciar contenedores Docker
Write-Host "Iniciando contenedores Docker..." -ForegroundColor Yellow
docker-compose up -d

# Esperar a que los contenedores estén listos
Write-Host "Esperando a que los servicios estén listos..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Ejecutar migraciones
Write-Host "Ejecutando migraciones de base de datos..." -ForegroundColor Yellow
docker-compose exec laravel.test php artisan migrate --force

# Ejecutar seeders
Write-Host "Cargando datos de prueba..." -ForegroundColor Yellow
docker-compose exec laravel.test php artisan db:seed --force

# Compilar assets
Write-Host "Compilando assets..." -ForegroundColor Yellow
docker-compose exec laravel.test npm run build

# Limpiar caché
Write-Host "Limpiando caché..." -ForegroundColor Yellow
docker-compose exec laravel.test php artisan cache:clear
docker-compose exec laravel.test php artisan config:clear
docker-compose exec laravel.test php artisan view:clear

Write-Host "✓ Sistema iniciado correctamente!" -ForegroundColor Green
Write-Host "✓ Aplicación disponible en: http://localhost" -ForegroundColor Green
Write-Host "✓ Credenciales de prueba:" -ForegroundColor Green
Write-Host "  - Admin: admin@taller.com / password" -ForegroundColor Cyan
Write-Host "  - Mecánico: mecanico@taller.com / password" -ForegroundColor Cyan

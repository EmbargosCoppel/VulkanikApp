# Script para detener el Sistema de Taller Mecánico
# Este script detiene los contenedores Docker

Write-Host "Deteniendo Sistema de Taller Mecánico..." -ForegroundColor Yellow

# Detener contenedores Docker
docker-compose down

Write-Host "✓ Sistema detenido correctamente" -ForegroundColor Green

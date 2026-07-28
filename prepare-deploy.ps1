# Script de Preparacion para Deployment
# Este script prepara tu proyecto para ser desplegado en Render.com

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Preparacion para Deployment" -ForegroundColor Cyan
Write-Host "  Vulcanizadora Don Chuy" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verificar que git este instalado
Write-Host "Verificando Git..." -ForegroundColor Yellow
try {
    $gitVersion = git --version
    Write-Host "  Git encontrado: $gitVersion" -ForegroundColor Green
} catch {
    Write-Host "  ERROR: Git no esta instalado. Por favor instala Git desde https://git-scm.com/" -ForegroundColor Red
    exit 1
}

# Verificar que composer este instalado
Write-Host "`nVerificando Composer..." -ForegroundColor Yellow
try {
    $composerVersion = composer --version
    Write-Host "  Composer encontrado: $composerVersion" -ForegroundColor Green
} catch {
    Write-Host "  ERROR: Composer no esta instalado. Por favor instala Composer desde https://getcomposer.org/" -ForegroundColor Red
    exit 1
}

# Verificar que node/npm esten instalados
Write-Host "`nVerificando Node.js y npm..." -ForegroundColor Yellow
try {
    $nodeVersion = node --version
    $npmVersion = npm --version
    Write-Host "  Node.js encontrado: $nodeVersion" -ForegroundColor Green
    Write-Host "  npm encontrado: $npmVersion" -ForegroundColor Green
} catch {
    Write-Host "  ERROR: Node.js o npm no estan instalados. Por favor instala Node.js desde https://nodejs.org/" -ForegroundColor Red
    exit 1
}

# Compilar assets para produccion
Write-Host "`nCompilando assets para produccion..." -ForegroundColor Yellow
Set-Location ..
try {
    npm run build
    Write-Host "  Assets compilados exitosamente" -ForegroundColor Green
} catch {
    Write-Host "  ERROR: Fallo la compilacion de assets" -ForegroundColor Red
    exit 1
}

# Instalar dependencias de Composer (sin dev)
Write-Host "`nInstalando dependencias de Composer..." -ForegroundColor Yellow
try {
    composer install --no-dev --optimize-autoloader --no-interaction
    Write-Host "  Dependencias instaladas exitosamente" -ForegroundColor Green
} catch {
    Write-Host "  ERROR: Fallo la instalacion de dependencias" -ForegroundColor Red
    exit 1
}

# Generar APP_KEY si no existe
Write-Host "`nVerificando APP_KEY..." -ForegroundColor Yellow
$envContent = Get-Content ".env" -Raw
if ($envContent -match "APP_KEY=$") {
    Write-Host "  Generando APP_KEY..." -ForegroundColor Yellow
    try {
        php artisan key:generate --force
        Write-Host "  APP_KEY generada exitosamente" -ForegroundColor Green
    } catch {
        Write-Host "  ERROR: Fallo la generacion de APP_KEY" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "  APP_KEY ya existe" -ForegroundColor Green
}

# Limpiar caches
Write-Host "`nLimpiando caches..." -ForegroundColor Yellow
try {
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    Write-Host "  Caches limpiados exitosamente" -ForegroundColor Green
} catch {
    Write-Host "  ERROR: Fallo la limpieza de caches" -ForegroundColor Red
    exit 1
}

# Crear enlace simbolico de storage
Write-Host "`nCreando enlace de storage..." -ForegroundColor Yellow
try {
    php artisan storage:link
    Write-Host "  Enlace de storage creado exitosamente" -ForegroundColor Green
} catch {
    Write-Host "  ADVERTENCIA: No se pudo crear el enlace de storage (puede que ya exista)" -ForegroundColor Yellow
}

# Verificar archivos importantes
Write-Host "`nVerificando archivos importantes..." -ForegroundColor Yellow

$filesToCheck = @(
    "render.yaml",
    "composer.json",
    "package.json",
    "vite.config.js",
    "public/build/manifest.json"
)

$allFilesExist = $true
foreach ($file in $filesToCheck) {
    if (Test-Path $file) {
        Write-Host "  $file existe" -ForegroundColor Green
    } else {
        Write-Host "  $file NO existe" -ForegroundColor Red
        $allFilesExist = $false
    }
}

if (-not $allFilesExist) {
    Write-Host "`n  ADVERTENCIA: Algunos archivos importantes no existen" -ForegroundColor Yellow
}

# Mostrar resumen
Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Preparacion Completada" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Proximos pasos:" -ForegroundColor Yellow
Write-Host "1. Crea un repositorio en GitHub" -ForegroundColor White
Write-Host "2. Ejecuta los comandos de git (ver DEPLOY.md)" -ForegroundColor White
Write-Host "3. Crea una cuenta en Render.com" -ForegroundColor White
Write-Host "4. Sigue la guia en DEPLOY.md" -ForegroundColor White
Write-Host ""
Write-Host "Documentacion: DEPLOY.md" -ForegroundColor Cyan
Write-Host ""

# Preguntar si desea inicializar git
$initGit = Read-Host "Deseas inicializar Git ahora? (s/n)"
if ($initGit -eq "s" -or $initGit -eq "S") {
    Write-Host "`nInicializando Git..." -ForegroundColor Yellow
    
    if (-not (Test-Path ".git")) {
        git init
        git add .
        git commit -m "Initial commit - Proyecto Vulcanizadora Don Chuy"
        git branch -M main
        Write-Host "  Git inicializado" -ForegroundColor Green
        Write-Host "`n  Ahora ejecuta:" -ForegroundColor Yellow
        Write-Host "  git remote add origin https://github.com/TU_USUARIO/vulcanizadora-don-chuy.git" -ForegroundColor White
        Write-Host "  git push -u origin main" -ForegroundColor White
    } else {
        Write-Host "  Git ya esta inicializado" -ForegroundColor Yellow
    }
}

Write-Host "`nListo! Tu proyecto esta preparado para deployment." -ForegroundColor Green
Write-Host ""
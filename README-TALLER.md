# Sistema de Taller Mecánico

Sistema de gestión para taller mecánico con Laravel 11.

## Características

- Gestión de clientes
- Gestión de vehículos
- Gestión de refacciones e inventario
- Gestión de órdenes de trabajo
- Sistema de alertas de stock bajo
- Dashboard para administradores y mecánicos
- Autenticación de usuarios con roles

## Requisitos

- PHP 8.3+
- Composer
- Node.js 18+
- MySQL 8.0+ o SQLite
- Docker (opcional)

## Instalación

### Opción 1: Usar Docker (Recomendado)

1. Clonar el repositorio
2. Ejecutar el script de inicio:
   ```bash
   ./start.ps1
   ```

Este script automáticamente:
- Verifica que Docker esté corriendo
- Inicia los contenedores Docker (Laravel, MySQL, Redis)
- Ejecuta las migraciones de base de datos
- Carga los datos de prueba (seeders)
- Compila los assets de frontend
- Limpia el caché

### Opción 2: Instalación Local

1. Clonar el repositorio
2. Instalar dependencias:
   ```bash
   composer install
   npm install
   ```

3. Configurar el archivo `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Ejecutar migraciones y seeders:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. Compilar assets:
   ```bash
   npm run build
   ```

6. Iniciar servidor:
   ```bash
   php artisan serve
   ```

O usar el script de inicio local:
   ```bash
   ./start-local.ps1
   ```

## Credenciales de Prueba

- **Admin:** admin@taller.com / password
- **Mecánico:** mecanico@taller.com / password

## Scripts Disponibles

- `start.ps1` - Inicia el sistema con Docker
- `stop.ps1` - Detiene los contenedores Docker
- `start-local.ps1` - Inicia el sistema en modo local (sin Docker)

## Estructura del Proyecto

- `app/Models/` - Modelos de Eloquent
- `app/Http/Controllers/` - Controladores
- `database/migrations/` - Migraciones de base de datos
- `database/seeders/` - Seeders de datos de prueba
- `resources/views/` - Vistas Blade
- `routes/` - Definición de rutas

## Tecnologías Utilizadas

- Laravel 11
- PHP 8.3
- MySQL 8.0
- TailwindCSS
- Alpine.js
- Vite

## Licencia

MIT

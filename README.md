# VulkanikApp
Sistema de gestión de talleres mecánicos (Prueba funcional)

## Descripción
Aplicación Laravel para la gestión de talleres mecánicos con control de clientes, vehículos, refacciones y órdenes de trabajo.

## Características
- Gestión de clientes y vehículos
- Control de inventario de refacciones
- Sistema de órdenes de trabajo
- Dashboard administrativo y mecánico
- Autenticación de usuarios

## Instalación Local
```bash
# Copiar archivo de entorno
cp .env.example .env

# Instalar dependencias
composer install
npm install

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Compilar assets
npm run build

# Iniciar servidor
php artisan serve
```

## Credenciales de Prueba
- **Admin**: admin@taller.com / password
- **Mecánico**: mecanico@taller.com / password

## Despliegue en Laravel Cloud
1. Conectar repositorio de GitHub a Laravel Cloud
2. Configurar variables de entorno
3. Desplegar con un clic

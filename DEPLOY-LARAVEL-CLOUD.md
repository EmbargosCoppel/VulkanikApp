# Instrucciones de Despliegue en Laravel Cloud

## Paso 1: Crear Cuenta en Laravel Cloud

1. Ve a https://laravel.com/cloud
2. Click en "Get Started"
3. Regístrate con tu cuenta de GitHub
4. Obtendrás $5 de crédito gratis + primer mes gratis

## Paso 2: Conectar Repositorio

1. En el panel de Laravel Cloud, click en "New Project"
2. Selecciona "Connect GitHub Repository"
3. Autoriza Laravel Cloud para acceder a tu GitHub
4. Selecciona el repositorio: `EmbargosCoppel/VulkanikApp`
5. Click en "Connect"

## Paso 3: Configurar Variables de Entorno

Laravel Cloud detectará automáticamente tu archivo `.env.cloud.example`. Configura las siguientes variables:

```env
APP_NAME="Vulcanizadora Don Chuy"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-app.laravel.cloud
SANCTUM_STATEFUL_DOMAINS=*.laravel.cloud

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vulcanizadora
DB_USERNAME=root
DB_PASSWORD=
```

**Nota**: Laravel Cloud maneja automáticamente la base de datos, cache y otros servicios.

## Paso 4: Desplegar

1. Click en "Deploy"
2. Laravel Cloud ejecutará automáticamente:
   - Instalación de dependencias (composer install)
   - Ejecución de migraciones (php artisan migrate)
   - Compilación de assets (npm run build)
   - Configuración de SSL
3. Espera unos minutos para que termine el despliegue

## Paso 5: Configurar Base de Datos

1. En el panel de Laravel Cloud, ve a "Databases"
2. Crea una nueva base de datos MySQL
3. Laravel Cloud proporcionará las credenciales automáticamente
4. Actualiza las variables de entorno con las credenciales proporcionadas

## Paso 6: Ejecutar Seeders (Opcional)

Para cargar los datos de prueba (admin, clientes, refacciones):

1. Ve a "Console" en Laravel Cloud
2. Ejecuta: `php artisan db:seed --force`
3. Esto cargará los datos de prueba

## Paso 7: Acceder a la Aplicación

1. Laravel Cloud te proporcionará una URL como: `https://tu-app.laravel.cloud`
2. Accede con las credenciales:
   - **Admin**: admin@taller.com / password
   - **Mecánico**: mecanico@taller.com / password

## Características Incluidas

- ✅ SSL automático
- ✅ Base de datos MySQL gestionada
- ✅ Escalado automático
- ✅ Backups automáticos
- ✅ Monitoreo de salud
- ✅ Dominios personalizados (puedes agregar tu propio dominio)

## Costos

- **Primer mes**: GRATIS
- **Crédito inicial**: $5 USD
- **Escalado a cero**: No pagas cuando la app no está en uso
- **Plan Starter**: ~$5/mes después del crédito

## Soporte

Si tienes problemas, consulta la documentación oficial: https://laravel.com/docs/cloud

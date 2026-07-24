# Instrucciones de Despliegue en Deploynix

## Paso 1: Crear Cuenta en Deploynix

1. Ve a https://deploynix.io/
2. Click en "Sign Up" o "Get Started Free"
3. Regístrate con tu cuenta de GitHub
4. **No requiere tarjeta de crédito** - Free forever

## Paso 2: Crear Servidor Gratuito

1. En el panel de Deploynix, click en "Provision your free server"
2. Selecciona el plan gratuito:
   - 1 Server
   - 3 Sites
   - 2 Databases
   - Vanity SSL (*.deploynix.cloud)
   - Sin tarjeta de crédito

## Paso 3: Conectar Repositorio

1. Click en "Add Site"
2. Selecciona "Connect GitHub Repository"
3. Autoriza Deploynix para acceder a tu GitHub
4. Selecciona el repositorio: `EmbargosCoppel/VulkanikApp`
5. Configura el dominio: `vulcanizadora.deploynix.cloud` (o el que prefieras)

## Paso 4: Configurar Variables de Entorno

Deploynix detectará automáticamente tu archivo `.env.deploynix.example`. Configura las siguientes variables:

```env
APP_NAME="Vulcanizadora Don Chuy"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://vulcanizadora.deploynix.cloud
SANCTUM_STATEFUL_DOMAINS=*.deploynix.cloud

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vulcanizadora
DB_USERNAME=root
DB_PASSWORD=
```

**Nota**: Deploynix maneja automáticamente la base de datos, cache y otros servicios.

## Paso 5: Configurar Base de Datos

1. En el panel de Deploynix, ve a "Databases"
2. Crea una nueva base de datos MySQL
3. Deploynix proporcionará las credenciales automáticamente
4. Actualiza las variables de entorno con las credenciales proporcionadas

## Paso 6: Desplegar

1. Click en "Deploy"
2. Deploynix ejecutará automáticamente:
   - Instalación de dependencias (composer install)
   - Ejecución de migraciones (php artisan migrate)
   - Compilación de assets (npm run build)
   - Configuración de SSL
3. Espera unos minutos para que termine el despliegue

## Paso 7: Ejecutar Seeders (Opcional)

Para cargar los datos de prueba (admin, clientes, refacciones):

1. Ve a "Console" en Deploynix
2. Ejecuta: `php artisan db:seed --force`
3. Esto cargará los datos de prueba

## Paso 8: Acceder a la Aplicación

1. Deploynix te proporcionará una URL como: `https://vulcanizadora.deploynix.cloud`
2. Accede con las credenciales:
   - **Admin**: admin@taller.com / password
   - **Mecánico**: mecanico@taller.com / password

## Características Incluidas (Plan Gratuito)

- ✅ 1 Server
- ✅ 3 Sites
- ✅ 2 Databases
- ✅ SSL automático (*.deploynix.cloud)
- ✅ Zero Downtime Deployments
- ✅ Priority Support
- ✅ Advanced Monitoring
- ✅ API Access
- ✅ **Sin tarjeta de crédito**
- ✅ **Free forever**

## Ventajas de Deploynix

- **Sin costo**: Free forever con características generosas
- **Fácil despliegue**: Conecta GitHub y deploy en un clic
- **Zero downtime**: Despliegues sin interrupciones
- **Laravel optimizado**: Diseñado específicamente para Laravel
- **Soporte incluido**: Priority support incluso en plan gratuito

## Limitaciones del Plan Gratuito

- Solo vanity domains (*.deploynix.cloud)
- 1 server, 3 sites, 2 databases
- Para dominios personalizados necesitas plan pago

## Escalabilidad

Cuando tu aplicación crezca, puedes actualizar a planes pagos con:
- Más servidores
- Dominios personalizados
- Más recursos
- Mismo stack, sin migraciones

## Soporte

Si tienes problemas, consulta la documentación: https://deploynix.io/docs

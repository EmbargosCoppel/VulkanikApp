# 🚀 Guía de Deployment en Railway.app

Railway es una excelente alternativa a Render con mejor soporte para proyectos Laravel + Node.js.

---

## 📋 Paso 1: Crear Cuenta en Railway

1. Ve a [railway.app](https://railway.app)
2. Click en **"Login"** o **"Sign Up"**
3. Regístrate con tu cuenta de **GitHub** (recomendado)
4. Autoriza a Railway para acceder a tus repositorios

---

## 📋 Paso 2: Crear Base de Datos MySQL

1. En el dashboard de Railway, click en **"New Project"**
2. Selecciona **"Provision MySQL"**
3. Railway creará automáticamente la base de datos
4. Ve a la pestaña **"Variables"** de la base de datos
5. Copia las variables de conexión:
   - `MYSQL_HOST`
   - `MYSQL_PORT`
   - `MYSQL_USER`
   - `MYSQL_PASSWORD`
   - `MYSQL_DATABASE`

---

## 📋 Paso 3: Crear el Servicio Web

1. En tu proyecto, click en **"New"** → **"GitHub Repo"**
2. Selecciona `EmbargosCoppel/VulkanikApp`
3. Railway detectará automáticamente que es un proyecto PHP/Laravel

---

## 📋 Paso 4: Configurar Variables de Entorno

En la pestaña **"Variables"** de tu servicio web, agrega:

```
APP_NAME="Vulcanizadora Don Chuy"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:gOJzIWM+tlvv3blriTbuBb4KYPguNIoSUX0tOrIHO9g=
APP_URL=https://tu-app.up.railway.app

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQL_HOST}}
DB_PORT=${{MySQL.MYSQL_PORT}}
DB_DATABASE=${{MySQL.MYSQL_DATABASE}}
DB_USERNAME=${{MySQL.MYSQL_USER}}
DB_PASSWORD=${{MySQL.MYSQL_PASSWORD}}

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync

IVA_RATE=0.16
PAGINATION_PER_PAGE=15
PAYMENT_CURRENCY=mxn
```

**Nota**: Railway usa variables de referencia como `${{MySQL.MYSQL_HOST}}` para conectar servicios automáticamente.

---

## 📋 Paso 5: Configurar Comandos de Build

En Railway, ve a la pestaña **"Settings"** de tu servicio:

### Build Command:
```bash
composer install --no-dev --optimize-autoloader && npm install --force && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

### Start Command:
```bash
php artisan migrate --force && php artisan db:seed --class=AdminSeeder --force && php artisan serve --host 0.0.0.0 --port $PORT
```

---

## 📋 Paso 6: Esperar el Deployment

El deployment tomará entre **3-5 minutos** la primera vez.

Railway ejecutará automáticamente:
1. ✅ Clona tu repositorio
2. ✅ Instala dependencias de Composer
3. ✅ Instala dependencias de Node.js
4. ✅ Compila los assets (CSS/JS)
5. ✅ Ejecuta migraciones
6. ✅ Ejecuta seeders
7. ✅ Inicia el servidor

---

## 📋 Paso 7: Verificar que Funciona

Una vez completado:
1. Railway te proporcionará una URL como: `https://tu-app.up.railway.app`
2. Abre la URL en tu navegador
3. Prueba con las credenciales:
   - **Admin**: `admin@taller.com` / `change-me-immediately`
   - **Mecánico**: `mecanico@taller.com` / `change-me-immediately`

---

## 🔧 Solución de Problemas

### Error de compilación de Node.js

**Solución**: Railway tiene mejor manejo de dependencias. Si hay errores, revisa los logs en la pestaña **"Deployments"**.

### No se conecta a la base de datos

**Solución**:
1. Verifica que las variables `${{MySQL.MYSQL_*}}` estén correctamente referenciadas
2. Asegúrate de que el servicio de MySQL esté en el mismo proyecto

### La app se duerme

**Solución**: Railway no duerme las apps en el tier gratuito (a diferencia de Render).

---

## 📊 Actualizaciones Futuras

Railway redesplegará automáticamente cuando hagas push a GitHub:

```bash
git add .
git commit -m "Cambios"
git push
```

---

## 🎓 Para tu Profesor

**URL**: `https://tu-app.up.railway.app`

**Credenciales**:
- Admin: `admin@taller.com` / `change-me-immediately`
- Mecánico: `mecanico@taller.com` / `change-me-immediately`

---

## 💡 Ventajas de Railway sobre Render:

- ✅ Mejor manejo de dependencias de Node.js
- ✅ No se "duerme" la aplicación
- ✅ Variables de entorno más fáciles de configurar
- ✅ Mejor soporte para Laravel
- ✅ 500 horas/mes gratuitas
- ✅ Interfaz más intuitiva

---

## 📞 Soporte

Si tienes problemas:
1. Revisa los logs en la pestaña **"Deployments"**
2. Verifica las variables de entorno
3. Asegúrate de que MySQL esté en el mismo proyecto

---

*Generado el 3 de agosto de 2026*
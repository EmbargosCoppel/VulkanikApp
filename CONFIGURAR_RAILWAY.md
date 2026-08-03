# ⚙️ Configurar Variables de Entorno en Railway

Tu app ya está desplegada, pero necesitas configurar las variables de entorno para que funcione.

---

## 📋 Paso 1: Cambiar a MySQL en Railway

**Opción A - Crear nueva base de datos MySQL (Recomendado):**

1. En Railway, en el menú izquierdo, click en **"New"** (botón +)
2. Selecciona **"Database"**
3. Elige **"MySQL"**
4. Railway creará automáticamente una base de datos MySQL
5. Nómbrala como `vulcanizadora-db-mysql` (o el nombre que prefieras)
6. Ve a la pestaña **"Variables"** de la nueva base de datos MySQL
7. Verás variables **MYSQL_*** (MYSQL_HOST, MYSQL_PORT, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE)

**Opción B - Eliminar PostgreSQL y crear MySQL:**

1. En la base de datos actual `vulcanizadora-db`, ve a **"Settings"**
2. Scroll hasta abajo y busca **"Delete"** o **"Danger Zone"**
3. Elimina la base de datos PostgreSQL
4. Crea una nueva base de datos MySQL como en la Opción A

**Nota**: La Opción A es más segura porque mantienes la base de datos PostgreSQL por si acaso.

---

## 📋 Paso 2: Configurar variables en el servicio web

1. Volver al servicio **`vulcanizadora-don-chuy`**
2. Click en la pestaña **"Variables"**
3. Agrega estas variables:

### Variables de la aplicación:

```
APP_NAME="Vulcanizadora Don Chuy"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:gOJzIWM+tlvv3blriTbuBb4KYPguNIoSUX0tOrIHO9g=
APP_URL=https://vulcanizadora-don-chuy-production.up.railway.app
```

**Nota**: Reemplaza la URL con la URL real de tu app (la verás en la parte superior de Railway)

### Variables de base de datos:

```
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQL_HOST}}
DB_PORT=${{MySQL.MYSQL_PORT}}
DB_DATABASE=${{MySQL.MYSQL_DATABASE}}
DB_USERNAME=${{MySQL.MYSQL_USER}}
DB_PASSWORD=${{MySQL.MYSQL_PASSWORD}}
```

**Importante**: Usa la sintaxis `${{MySQL.VARIABLE}}` para conectar automáticamente con la base de datos MySQL.

### Variables adicionales:

```
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync

IVA_RATE=0.16
PAGINATION_PER_PAGE=15
PAYMENT_CURRENCY=mxn
```

---

## 📋 Paso 3: Redesplegar

1. Después de agregar todas las variables, Railway redesplegará automáticamente
2. Espera 1-2 minutos
3. La app estará lista

---

## 📱 Acceso a la aplicación

**URL**: `https://vulcanizadora-don-chuy-production.up.railway.app` (o la URL que te proporcionó Railway)

**Credenciales**:
- **Admin**: `admin@taller.com` / `change-me-immediately`
- **Mecánico**: `mecanico@taller.com` / `change-me-immediately`

---

## ✅ Verificar que funciona

1. Abre la URL en tu navegador
2. Deberías ver la página de login
3. Inicia sesión con las credenciales de admin
4. Verifica que puedes ver el dashboard

---

## 🔧 Si hay problemas

### Error 500 - Internal Server Error

**Solución**:
1. Ve a la pestaña **"Console"** en Railway
2. Revisa los logs de error
3. Verifica que todas las variables de entorno estén configuradas

### No se conecta a la base de datos

**Solución**:
1. Verifica que las variables `${{MySQL.MYSQL_*}}` estén correctas
2. Asegúrate de que la base de datos esté en el mismo proyecto
3. Verifica que el servicio de MySQL esté **Online**

### La página carga en blanco

**Solución**:
1. Verifica que `APP_URL` esté configurado correctamente
2. Revisa los logs en la pestaña **"Console"**

---

## 🎓 Para tu Profesor

**URL de la aplicación**: `https://vulcanizadora-don-chuy-production.up.railway.app`

**Credenciales de acceso**:
- **Administrador**: `admin@taller.com` / `change-me-immediately`
- **Mecánico**: `mecanico@taller.com` / `change-me-immediately`

**Características destacadas**:
- ✅ Sistema de gestión completo (clientes, vehículos, refacciones, órdenes)
- ✅ API RESTful con Sanctum
- ✅ Patrones de diseño (Strategy, State, Adapter, Builder)
- ✅ Arquitectura en capas (Controllers, Services, Repositories)
- ✅ Control de acceso por roles
- ✅ Cálculo automático de IVA
- ✅ Gestión de inventario con alertas de stock
- ✅ Interfaz responsive con Tailwind CSS

---

## 📊 Actualizaciones futuras

Railway redesplegará automáticamente cuando hagas push a GitHub:

```bash
git add .
git commit -m "Cambios"
git push
```

---

*Generado el 3 de agosto de 2026*
# 🚀 Instrucciones para Desplegar en Render

Tu código ya está en GitHub. Ahora sigue estos pasos para publicar tu aplicación:

---

## 📋 Paso 1: Crear Cuenta en Render

1. Ve a [render.com](https://render.com)
2. Click en **"Get Started"** o **"Sign Up"**
3. Puedes registrarte con tu cuenta de **GitHub** (recomendado) o con email
4. Si usas GitHub, autoriza a Render para acceder a tus repositorios

---

## 📋 Paso 2: Crear Base de Datos MySQL

1. Una vez en el dashboard de Render, click en **"New +"** (esquina superior derecha)
2. Selecciona **"MySQL"**
3. Completa el formulario:
   - **Name**: `vulcanizadora-db`
   - **Database**: `vulcanizadora`
   - **User**: `vulcanizadora_user`
   - **Region**: Elige la más cercana (ej: "Ohio" o "Frankfurt")
   - **Plan**: Free
4. Click en **"Create Database"**
5. **IMPORTANTE**: Después de crear la base de datos, verás una sección llamada **"Internal Database URL"**
   - Cópiala completa (se verá algo como: `mysql://vulcanizadora_user:password@xxx.xxx.xxx.xxx:3306/vulcanizadora`)
   - La necesitarás en el siguiente paso

---

## 📋 Paso 3: Crear Web Service (Aplicación)

1. En Render, click en **"New +"** → **"Web Service"**
2. Conecta tu repositorio:
   - Si autorizaste GitHub, deberías ver `EmbargosCoppel/VulkanikApp` en la lista
   - Click en **"Connect"** junto a ese repositorio
3. Configura el servicio:
   - **Name**: `vulcanizadora-don-chuy`
   - **Region**: Elige la MISMA región que seleccionaste para la base de datos
   - **Runtime**: PHP (se detectará automáticamente)
   - **Plan**: Free
   - **Branch**: main
4. En la sección **"Build & Deploy"**, Render detectará automáticamente el archivo `render.yaml`
   - **Build Command**: Se llenará solo
   - **Start Command**: Se llenará solo
5. En **"Environment Variables"**, agrega estas variables:

### Variables Obligatorias:

```
APP_KEY=base64:gOJzIWM+tlvv3blriTbuBb4KYPguNIoSUX0tOrIHO9g=
APP_ENV=production
APP_DEBUG=false
APP_NAME="Vulcanizadora Don Chuy"
APP_URL=https://vulcanizadora-don-chuy.onrender.com

DB_CONNECTION=mysql
DB_HOST=xxx.xxx.xxx.xxx (reemplaza con el host de tu Internal Database URL)
DB_PORT=3306
DB_DATABASE=vulcanizadora
DB_USERNAME=vulcanizadora_user
DB_PASSWORD=xxxxxxxx (reemplaza con la contraseña de tu Internal Database URL)

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync

IVA_RATE=0.16
PAGINATION_PER_PAGE=15
PAYMENT_CURRENCY=mxn
```

**⚠️ IMPORTANTE**: Para obtener `DB_HOST` y `DB_PASSWORD`:
- Ve a tu base de datos en Render
- Copia la **Internal Database URL**
- Ejemplo: `mysql://vulcanizadora_user:abc123xyz@mysql.xxx-xxx-xxx.ohio-mysql.render.com:3306/vulcanizadora`
  - `DB_HOST` = `mysql.xxx-xxx-xxx.ohio-mysql.render.com`
  - `DB_USERNAME` = `vulcanizadora_user`
  - `DB_PASSWORD` = `abc123xyz`
  - `DB_PORT` = `3306`
  - `DB_DATABASE` = `vulcanizadora`

6. Click en **"Create Web Service"**

---

## 📋 Paso 4: Esperar el Deployment

El deployment tomará entre **5-10 minutos** la primera vez.

### ¿Qué está pasando?
1. ✅ Clona tu repositorio desde GitHub
2. ✅ Instala dependencias de Composer
3. ✅ Instala dependencias de Node.js
4. ✅ Compila los assets (CSS/JS) con Vite
5. ✅ Ejecuta migraciones de base de datos
6. ✅ Ejecuta seeders (crea usuario admin y mecánico)
7. ✅ Inicia el servidor

Puedes ver el progreso en tiempo real en la consola de Render.

---

## 📋 Paso 5: Verificar que Funciona

Una vez completado el deployment:

1. Render te proporcionará una URL como: `https://vulcanizadora-don-chuy.onrender.com`
2. Abre la URL en tu navegador
3. Prueba con las credenciales:
   - **Admin**: `admin@taller.com` / `change-me-immediately`
   - **Mecánico**: `mecanico@taller.com` / `change-me-immediately`

---

## 🔧 Solución de Problemas Comunes

### Error 500 - Internal Server Error

**Solución**:
1. Ve a la pestaña "Logs" en Render
2. Revisa el error específico
3. Verifica que las variables de entorno estén correctas

### No se conecta a la base de datos

**Solución**:
1. Verifica que `DB_CONNECTION=mysql` (para MySQL)
2. Verifica que `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` sean correctos
3. Asegúrate de que la base de datos esté en la misma región que la app web

### Los estilos CSS no cargan

**Solución**:
1. Verifica que `npm run build` se ejecutó correctamente en los logs
2. Verifica que `APP_URL` esté configurado correctamente

### Error de migraciones

**Solución**:
1. Asegúrate de que la base de datos esté creada antes de desplegar
2. Verifica que el usuario tenga permisos en la base de datos
3. Revisa los logs para ver el error específico

---

## 📊 Actualizaciones Futuras

Para actualizar tu aplicación después de hacer cambios:

```bash
# 1. Hacer cambios en tu código local
# 2. Commit y push a GitHub
git add .
git commit -m "Descripción de cambios"
git push

# 3. Render detectará el push y redesplegará automáticamente
```

---

## 🎓 Para tu Profesor

**URL de la aplicación**: `https://vulcanizadora-don-chuy.onrender.com`

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

## 📞 Soporte

Si tienes problemas con el deployment:
1. Revisa los logs en Render (pestaña "Logs")
2. Verifica las variables de entorno
3. Asegúrate de que la base de datos esté en la misma región

---

## 🎉 ¡Listo!

Tu aplicación estará disponible 24/7 en la URL proporcionada por Render.

**Nota**: El tier gratuito de Render tiene algunas limitaciones:
- La app se "duerme" después de 15 minutos de inactividad
- Se despierta automáticamente cuando alguien accede (toma ~30 segundos)
- 750 horas/mes gratuitas (suficiente para demostración)

---

*Generado el 3 de agosto de 2026*
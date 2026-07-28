# 🚀 Guía de Deployment - Vulcanizadora Don Chuy

Esta guía te ayudará a publicar tu proyecto en **Render.com** de forma gratuita.

---

## 📋 Requisitos Previos

1. **Cuenta en GitHub** (para alojar tu código)
2. **Cuenta en Render.com** (para hosting gratuito)
3. **Git** instalado en tu computadora

---

## 🎯 Paso 1: Preparar el Repositorio en GitHub

### 1.1. Crear un nuevo repositorio en GitHub

1. Ve a [github.com/new](https://github.com/new)
2. Nombre del repositorio: `vulcanizadora-don-chuy` (o el que prefieras)
3. **NO** marques "Initialize this repository with a README"
4. Click en "Create repository"

### 1.2. Subir tu código a GitHub

Abre una terminal en la carpeta del proyecto y ejecuta:

```bash
# Inicializar git (si no lo has hecho)
git init

# Agregar todos los archivos
git add .

# Hacer el primer commit
git commit -m "Initial commit - Proyecto Vulcanizadora Don Chuy"

# Renombrar la rama a main
git branch -M main

# Conectar con tu repositorio de GitHub (reemplaza con tu usuario y nombre de repo)
git remote add origin https://github.com/TU_USUARIO/vulcanizadora-don-chuy.git

# Subir el código
git push -u origin main
```

**Nota**: Reemplaza `TU_USUARIO` con tu nombre de usuario de GitHub.

---

## 🎯 Paso 2: Crear Base de Datos en Render

1. Ve a [dashboard.render.com](https://dashboard.render.com)
2. Click en **"New +"** → **"PostgreSQL"** (o MySQL si prefieres)
3. Completa la información:
   - **Name**: `vulcanizadora-db`
   - **Database**: `vulcanizadora`
   - **User**: `vulcanizadora_user`
   - **Plan**: Free
4. Click en **"Create Database"**
5. **IMPORTANTE**: Guarda la **Internal Database URL** que te proporciona Render (la necesitarás después)

---

## 🎯 Paso 3: Desplegar la Aplicación Web

1. En Render, click en **"New +"** → **"Web Service"**
2. Conecta tu repositorio de GitHub
3. Configura el servicio:
   - **Name**: `vulcanizadora-don-chuy`
   - **Runtime**: PHP (se detectará automáticamente)
   - **Plan**: Free
   - **Build Command**: (se llenará automáticamente desde render.yaml)
   - **Start Command**: (se llenará automáticamente desde render.yaml)
4. En **Environment Variables**, agrega:

```
APP_KEY=base64:xxxxxxxxxxxx (Render lo genera automáticamente)
APP_ENV=production
APP_DEBUG=false
APP_NAME="Vulcanizadora Don Chuy"
APP_URL=https://vulcanizadora-don-chuy.onrender.com

DB_CONNECTION=mysql (o postgresql según la base de datos que creaste)
DB_HOST=xxx.xxx.xxx.xxx (de la Internal Database URL)
DB_PORT=3306 (o 5432 para PostgreSQL)
DB_DATABASE=vulcanizadora
DB_USERNAME=vulcanizadora_user
DB_PASSWORD=xxxxxxxxxxxx (de la Internal Database URL)

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync

IVA_RATE=0.16
PAGINATION_PER_PAGE=15
PAYMENT_CURRENCY=mxn
```

5. Click en **"Create Web Service"**

---

## 🎯 Paso 4: Esperar el Deployment

El deployment tomará entre **5-10 minutos** la primera vez. Verás el progreso en la consola de Render.

### ¿Qué está pasando durante el deployment?

1. ✅ Clona tu repositorio
2. ✅ Instala dependencias de Composer
3. ✅ Instala dependencias de Node.js
4. ✅ Compila los assets (CSS/JS) con Vite
5. ✅ Ejecuta migraciones de base de datos
6. ✅ Inicia el servidor

---

## 🎯 Paso 5: Verificar que Funciona

Una vez completado el deployment:

1. Render te proporcionará una URL como: `https://vulcanizadora-don-chuy.onrender.com`
2. Abre la URL en tu navegador
3. Prueba con las credenciales:
   - **Admin**: `admin@taller.com` / `password`
   - **Mecánico**: `mecanico@taller.com` / `password`

---

## 🔧 Solución de Problemas Comunes

### Error 500 - Internal Server Error

**Solución**:
1. Ve a la pestaña "Logs" en Render
2. Revisa el error específico
3. Verifica que las variables de entorno estén correctas

### No se conecta a la base de datos

**Solución**:
1. Verifica que `DB_CONNECTION` coincida con el tipo de base de datos (mysql/postgresql)
2. Verifica que `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` sean correctos
3. Asegúrate de que la base de datos esté en la misma región que la app web

### Los estilos CSS no cargan

**Solución**:
1. Verifica que `npm run build` se ejecutó correctamente
2. Revisa que los archivos en `public/build/` existan
3. Verifica que `APP_URL` esté configurado correctamente

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
- **Administrador**: `admin@taller.com` / `password`
- **Mecánico**: `mecanico@taller.com` / `password`

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

Si tienes problemas con el deployment, revisa:
1. Logs de Render (pestaña "Logs")
2. Consola de base de datos en Render
3. Variables de entorno configuradas

---

## 🎉 ¡Listo!

Tu aplicación estará disponible 24/7 en la URL proporcionada por Render, lista para que tu profesor la pruebe.

**Nota**: El tier gratuito de Render tiene algunas limitaciones:
- La app se "duerme" después de 15 minutos de inactividad
- Se despierta automáticamente cuando alguien accede (toma ~30 segundos)
- 750 horas/mes gratuitas (suficiente para demostración)

---

*Última actualización: 27 de julio de 2026*
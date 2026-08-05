# 🔧 Actualizar Variables en Railway

## ⚠️ IMPORTANTE: Pasos a Seguir

Después del último deploy, necesitas actualizar las variables de entorno en Railway para:
1. Solucionar el problema de HTTPS
2. Cambiar las contraseñas de prueba

---

## 📋 Paso 1: Actualizar Variables de Entorno

### **En Railway:**

1. Ve a tu proyecto → Click en **`VulkanikApp`** (servicio web)
2. Click en la pestaña **"Variables"**
3. Click en **"Raw Editor"** o actualiza las variables

### **Agrega/Modifica estas variables:**

```
# Configuración de Proxy (SOLUCIONA HTTPS)
TRUSTED_PROXIES=*
TRUSTED_HEADERS=X-Forwarded-For,X-Forwarded-Host,X-Forwarded-Port,X-Forwarded-Proto

# Contraseñas de Prueba (CAMBIADAS)
ADMIN_PASSWORD=password
MECANICO_PASSWORD=password
```

**⚠️ IMPORTANTE**: 
- Agrega las variables `TRUSTED_PROXIES` y `TRUSTED_HEADERS`
- Modifica `ADMIN_PASSWORD` y `MECANICO_PASSWORD` a `password`
- Las demás variables mantienen como están

---

## 🔄 Paso 2: Forzar Redeploy

Después de actualizar las variables:

1. Ve a la pestaña **"Deployments"**
2. Click en los **3 puntos** del último deployment
3. Click en **"Redeploy"**
4. Espera 2-3 minutos

---

## ✅ Paso 3: Verificar Cambios

### **Verificar HTTPS:**
1. Abre: https://vulcanizadora-don-chuy-production.up.railway.app
2. Intenta hacer login
3. **NO deberías ver** la advertencia "no es seguro enviar información"
4. El formulario debería enviarse correctamente

### **Verificar Contraseñas:**
1. Usa estas credenciales:
   - **Admin**: `admin@taller.com` / `password`
   - **Mecánico**: `mecanico@taller.com` / `password`
2. Deberías poder iniciar sesión correctamente

---

## 🔍 Si el Problema Persiste

### **Opción A: Eliminar y Crear Base de Datos**

Si las contraseñas no funcionan (porque ya se crearon con las antiguas):

1. En Railway, ve a **`Vulkanikapp_dtb`** (MySQL)
2. Ve a **"Settings"** → **"Danger Zone"**
3. Click en **"Delete"** para eliminar la base de datos
4. Crea una nueva base de datos MySQL
5. Conecta la nueva base de datos al servicio web
6. Redeploy

**⚠️ Esto eliminará todos los datos**, pero creará los usuarios con las nuevas contraseñas.

### **Opción B: Actualizar Contraseña Manualmente**

Si no quieres eliminar la base de datos:

1. Conéctate a MySQL en Railway (puedes usar MySQL Workbench o la consola de Railway)
2. Ejecuta:
```sql
UPDATE users SET password = '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewKyNiy2qJqSqS6' WHERE email = 'admin@taller.com';
UPDATE users SET password = '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewKyNiy2qJqSqS6' WHERE email = 'mecanico@taller.com';
```

El hash `$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewKyNiy2qJqSqS6` corresponde a la contraseña `password`.

---

## 📝 Resumen de Cambios

### **Variables Agregadas:**
- `TRUSTED_PROXIES=*` - Confía en todos los proxies (Railway)
- `TRUSTED_HEADERS=X-Forwarded-For,X-Forwarded-Host,X-Forwarded-Port,X-Forwarded-Proto` - Headers de proxy

### **Variables Modificadas:**
- `ADMIN_PASSWORD=password` (antes: `change-me-immediately`)
- `MECANICO_PASSWORD=password` (antes: `change-me-immediately`)

### **Archivos Modificados:**
- `database/seeders/AdminSeeder.php` - Contraseñas actualizadas
- `.env.railway.corrected` - Variables actualizadas

---

## 🚀 Acceso a la Aplicación

**URL**: https://vulcanizadora-don-chuy-production.up.railway.app

**Nuevas Credenciales:**
- **Admin**: `admin@taller.com` / `password`
- **Mecánico**: `mecanico@taller.com` / `password`

---

## ⏱️ Tiempo Estimado

- Actualizar variables: 1 minuto
- Redeploy: 2-3 minutos
- Verificar: 1 minuto

**Total**: ~5 minutos

---

## ❓ Problemas Comunes

### **"No es seguro enviar información"**
**Solución**: Asegúrate de haber agregado `TRUSTED_PROXIES=*` y `TRUSTED_HEADERS`

### **"Credenciales incorrectas"**
**Solución**: 
1. Verifica que las contraseñas sean `password`
2. Si no funciona, elimina la base de datos y vuelve a crear (Opción A)

### **"Error de conexión a MySQL"**
**Solución**: Verifica que las variables `${{Vulkanikapp_dtb.*}}` estén conectadas correctamente

---

**Actualiza las variables AHORA y comparte el resultado.**
# 🔍 Cómo Obtener los Errores Exactos

## 📋 Pasos para Diagnosticar

### **Método 1: Consola del Navegador (MÁS RÁPIDO)**

1. **Abre la aplicación**: https://vulcanizadora-don-chuy-production.up.railway.app
2. **Inicia sesión** con: `admin@taller.com` / `password`
3. **Presiona F12** para abrir DevTools
4. **Ve a la pestaña "Console"** (Consola)
5. **Navega a** "Órdenes" o cualquier otra página
6. **Busca errores en ROJO** en la consola
7. **Toma una captura de pantalla** de los errores

### **Método 2: Pestaña Network (Red)**

1. Con DevTools abierto (F12)
2. Ve a la pestaña **"Network"** (Red)
3. Recarga la página (F5)
4. Click en la petición que aparece en rojo (si hay)
5. Ve a la pestaña **"Response"** para ver el contenido
6. Toma captura de pantalla

### **Método 3: Activar APP_DEBUG (TEMPORAL)**

Si no ves errores en la consola, activa el modo debug:

1. Ve a Railway → **VulkanikApp** → **Variables**
2. Cambia: `APP_DEBUG=false` → `APP_DEBUG=true`
3. Click en **"Save"**
4. Redeploy (Deployments → 3 puntos → Redeploy)
5. Ahora verás el error completo en la página

**⚠️ IMPORTANTE**: Después de diagnosticar, vuelve a poner `APP_DEBUG=false` por seguridad.

---

## 🔍 Qué Buscar

### **En la Consola (F12 → Console):**
- Errores en **ROJO**
- Mensajes como:
  - `Uncaught TypeError`
  - `Uncaught ReferenceError`
  - `Failed to load resource`
  - `500 (Internal Server Error)`

### **En Network (F12 → Network):**
- Peticiones en **ROJO**
- Status **500** o **404**
- Click en la petición y ver la pestaña **"Response"**

### **En Railway Logs:**
- Líneas en **ROJO**
- Mensajes como:
  - `ErrorException`
  - `TypeError`
  - `Undefined variable`
  - `Class not found`

---

## 📸 Información a Compartir

Por favor comparte:

1. **Captura de pantalla** de la consola del navegador (F12 → Console)
2. **Captura de pantalla** de la pestaña Network si hay errores
3. **El mensaje de error exacto** que aparece

Con esta información podré solucionar el problema exacto.

---

## 🎯 Mientras Tanto

Puedes probar la ruta de debug:
- https://vulcanizadora-don-chuy-production.up.railway.app/debug

Si esa ruta funciona pero las otras no, el problema está en las vistas específicas.
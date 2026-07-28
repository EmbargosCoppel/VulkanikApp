# ⚡ Inicio Rápido - Publicar en Línea

## 🎯 Opción 1: Usar el Script Automático (Recomendado)

```powershell
# En Windows, ejecuta:
.\prepare-deploy.ps1
```

Este script hará todo automáticamente:
- ✅ Verifica herramientas necesarias
- ✅ Compila assets
- ✅ Instala dependencias
- ✅ Genera APP_KEY
- ✅ Limpia cachés
- ✅ Inicializa Git (opcional)

Luego sigue los pasos en **DEPLOY.md**

---

## 🎯 Opción 2: Manual (Paso a Paso)

### 1. Compilar assets
```bash
npm run build
```

### 2. Instalar dependencias
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Generar APP_KEY
```bash
php artisan key:generate
```

### 4. Optimizar para producción
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

### 5. Inicializar Git y subir a GitHub
```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/TU_USUARIO/vulcanizadora-don-chuy.git
git push -u origin main
```

### 6. Desplegar en Render
1. Ve a [render.com](https://render.com)
2. Crea cuenta (gratis con GitHub)
3. New + → Web Service
4. Conecta tu repo de GitHub
5. Render detectará `render.yaml` automáticamente
6. Agrega las variables de entorno (ver DEPLOY.md)
7. Click en "Create Web Service"

---

## 📋 Checklist Pre-Deployment

- [ ] Código compilado con `npm run build`
- [ ] Dependencias instaladas con `composer install --no-dev`
- [ ] APP_KEY generada
- [ ] Cachés limpiados
- [ ] Storage link creado
- [ ] Código subido a GitHub
- [ ] Cuenta en Render creada
- [ ] Base de datos creada en Render
- [ ] Variables de entorno configuradas

---

## 🔗 Enlaces Útiles

- **Render Dashboard**: https://dashboard.render.com
- **GitHub**: https://github.com/new
- **Documentación Completa**: Ver `DEPLOY.md`
- **Guía del Proyecto**: Ver `GUIA_DEL_PROYECTO.md`

---

## 🆘 ¿Problemas?

1. Revisa los logs en Render (pestaña "Logs")
2. Verifica las variables de entorno
3. Asegúrate de que la base de datos esté creada
4. Consulta la sección "Solución de Problemas" en DEPLOY.md

---

## 🎓 Credenciales para el Profesor

Una vez desplegado:
- **Admin**: `admin@taller.com` / `password`
- **Mecánico**: `mecanico@taller.com` / `password`

---

**Tiempo estimado**: 15-20 minutos
**Costo**: $0 USD (100% gratuito)
**URL final**: `https://vulcanizadora-don-chuy.onrender.com`
# 🐳 Acceso a la Aplicación desde Otros Dispositivos

Esta guía explica cómo configurar y acceder a la aplicación Vulcanizadora Don Chuy desde otros dispositivos en la red usando Docker.

---

## 📋 Requisitos Previos

1. **Docker Desktop** instalado y ejecutándose
2. **Docker Compose** incluido en Docker Desktop
3. La aplicación debe estar en la misma red que los dispositivos que accederán
4. Firewall de Windows debe permitir conexiones en el puerto 80 (o el puerto configurado)

---

## 🚀 Inicio Rápido

### Paso 1: Configurar variables de entorno

Copia el archivo `.env.example` a `.env`:

```bash
copy .env.example .env
```

### Paso 2: Generar clave de aplicación

```bash
php artisan key:generate
```

### Paso 3: Iniciar contenedores Docker

```bash
# Opción 1: Usando el script de PowerShell
.\start-docker.ps1

# Opción 2: Usando Docker Compose directamente
docker-compose up -d --build

# Opción 3: Usando Laravel Sail (si está instalado)
./vendor/bin/sail up -d
```

### Paso 4: Instalar dependencias y ejecutar migraciones

```bash
# Instalar dependencias de Composer
docker-compose exec laravel.test composer install

# Ejecutar migraciones
docker-compose exec laravel.test php artisan migrate --force

# Poblar base de datos con datos de prueba
docker-compose exec laravel.test php artisan db:seed --class=AdminSeeder
```

---

## 🌐 Acceso desde Otros Dispositivos

### Método 1: Usar la IP local de tu computadora

1. **Obtener tu IP local:**
   ```powershell
   # En PowerShell
   Get-NetIPAddress -AddressFamily IPv4 | Where-Object {$_.IPAddress -notlike "127.*" -and $_.IPAddress -notlike "169.*"}
   ```

   O simplemente ejecuta `ipconfig` en Command Prompt y busca la "IPv4 Address" de tu adaptador de red activo (WiFi o Ethernet).

2. **Acceder desde otros dispositivos:**
   ```
   http://TU_IP_LOCAL:80
   ```
   
   Ejemplo: `http://192.168.1.100:80`

### Método 2: Usar el nombre del host

Si tu red lo permite, también puedes usar el nombre de tu computadora:

```
http://NOMBRE-PC:80
```

Para ver el nombre de tu PC:
```powershell
hostname
```

---

## ⚙️ Configuración de Red

### Configuración actual de Docker Compose

El archivo `docker-compose.yml` ya está configurado para exponer los puertos:

```yaml
ports:
    - '${APP_PORT:-80}:8000'      # Puerto HTTP (80 en host, 8000 en contenedor)
    - '${VITE_PORT:-5173}:${VITE_PORT:-5173}'  # Puerto Vite para desarrollo
```

Esto significa que:
- El contenedor escucha en `0.0.0.0:8000` (todas las interfaces)
- El puerto `80` de tu computadora se mapea al puerto `8000` del contenedor
- Cualquier dispositivo en la red puede acceder a `http://TU_IP:80`

### Variables de entorno importantes

En el archivo `.env`, asegúrate de configurar:

```env
# URL de la aplicación (cambiar por tu IP o dominio)
APP_URL=http://192.168.1.100

# Dominios permitidos para CORS (agregar tu IP si es necesario)
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,192.168.1.100

# URL del frontend para CORS (si tienes un frontend separado)
FRONTEND_URL=http://192.168.1.100:3000
```

---

## 🔒 Configuración de CORS para Acceso Remoto

Para que la API funcione correctamente desde otros dispositivos, actualiza `config/cors.php`:

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    
    'allowed_methods' => ['*'],
    
    // Permitir acceso desde cualquier origen en desarrollo
    // En producción, especificar dominios exactos
    'allowed_origins' => [env('FRONTEND_URL', '*')],
    
    'allowed_origins_patterns' => [],
    
    'allowed_headers' => ['*'],
    
    'exposed_headers' => [],
    
    'max_age' => 0,
    
    'supports_credentials' => true,
];
```

---

## 🔥 Configuración del Firewall de Windows

Para permitir conexiones externas, debes abrir el puerto 80 en el firewall:

### Opción 1: Usando PowerShell (Administrador)

```powershell
# Permitir conexiones HTTP (puerto 80)
New-NetFirewallRule -DisplayName "HTTP Web Server" -Direction Inbound -Protocol TCP -LocalPort 80 -Action Allow

# Permitir conexiones HTTPS (puerto 443) si usas SSL
New-NetFirewallRule -DisplayName "HTTPS Web Server" -Direction Inbound -Protocol TCP -LocalPort 443 -Action Allow
```

### Opción 2: Usando Windows Defender Firewall GUI

1. Abrir "Windows Defender Firewall con seguridad avanzada"
2. Click en "Reglas de entrada" → "Nueva regla"
3. Seleccionar "Puerto" → Siguiente
4. Seleccionar "TCP" y "Puertos locales específicos: 80"
5. Seleccionar "Permitir la conexión" → Siguiente
6. Marcar todas las redes (Dominio, Privada, Pública) → Siguiente
7. Nombre: "HTTP Web Server" → Finalizar

---

## 📱 Probar Acceso desde Otro Dispositivo

1. **Conectar el dispositivo a la misma red WiFi/Ethernet**
2. **Abrir navegador en el dispositivo**
3. **Navegar a:** `http://TU_IP_LOCAL:80`
4. **Deberías ver** la página de login de Vulcanizadora Don Chuy

### Credenciales de prueba

```
Admin:
- Email: admin@taller.com
- Password: (la que configuraste en .env)

Mecánico:
- Email: mecanico@taller.com
- Password: (la que configuraste en .env)
```

---

## 🛠️ Comandos Útiles

### Ver logs de Docker
```bash
docker-compose logs -f laravel.test
```

### Detener contenedores
```bash
docker-compose stop
```

### Reiniciar contenedores
```bash
docker-compose restart
```

### Ver estado de contenedores
```bash
docker-compose ps
```

### Acceder a la terminal del contenedor
```bash
docker-compose exec laravel.test bash
```

### Ejecutar comandos Artisan
```bash
docker-compose exec laravel.test php artisan [comando]
```

Ejemplos:
```bash
docker-compose exec laravel.test php artisan migrate
docker-compose exec laravel.test php artisan db:seed
docker-compose exec laravel.test php artisan route:list
```

---

## 🌍 Acceso desde Internet (Opcional)

Si necesitas acceso desde fuera de tu red local:

### Opción 1: ngrok (Túnel temporal)
```bash
# Instalar ngrok
# Luego exponer el puerto 80
ngrok http 80
```

Ngrok te dará una URL pública como `https://abc123.ngrok.io` que redirige a tu localhost.

### Opción 2: Port forwarding en tu router
1. Acceder a la configuración de tu router (generalmente `192.168.1.1`)
2. Buscar "Port Forwarding" o "Redirección de puertos"
3. Crear regla: Puerto externo 80 → Puerto interno 80 → IP de tu computadora
4. Acceder desde internet usando tu IP pública

**⚠️ Advertencia de seguridad:** Solo hacer esto en entornos de desarrollo. Para producción, usa un servidor web dedicado con HTTPS.

---

## 🐛 Solución de Problemas

### No puedo acceder desde otro dispositivo

1. **Verificar que Docker esté corriendo:**
   ```bash
   docker-compose ps
   ```
   Deberías ver el servicio `laravel.test` con estado "Up"

2. **Verificar que el puerto 80 esté escuchando:**
   ```bash
   netstat -ano | findstr :80
   ```

3. **Verificar firewall:**
   ```powershell
   Get-NetFirewallRule -DisplayName "*80*" | Select-Object DisplayName, Enabled, Action
   ```

4. **Verificar que ambos dispositivos estén en la misma red:**
   - Ambos deben estar conectados al mismo WiFi o red Ethernet
   - Verificar que no haya redes de invitados o VLANs separadas

5. **Probar acceso local primero:**
   - Abre `http://localhost` en la misma computadora
   - Si funciona localmente pero no desde otros dispositivos, es un problema de red/firewall

### Error de conexión a base de datos

Si los contenedores están en red Docker, usa el nombre del servicio:

```env
# En .env
DB_HOST=mysql  # Nombre del servicio en docker-compose.yml
DB_PORT=3306
DB_DATABASE=vulkanikapp
DB_USERNAME=sail
DB_PASSWORD=password
```

### La aplicación carga pero no se pueden iniciar sesión

Verifica que las credenciales en `.env` coincidan con las del seeder:

```env
ADMIN_PASSWORD=tu_password_admin
MECANICO_PASSWORD=tu_password_mecanico
```

Luego vuelve a ejecutar el seeder:
```bash
docker-compose exec laravel.test php artisan db:seed --class=AdminSeeder
```

---

## 📊 Verificar que Todo Funciona

1. **Acceder localmente:** `http://localhost`
2. **Acceder desde otro dispositivo:** `http://TU_IP:80`
3. **Verificar API:** `http://TU_IP/api/health` (si tienes el endpoint)
4. **Verificar base de datos:** `http://TU_IP` → Login con credenciales de prueba

---

## 🔐 Consideraciones de Seguridad

⚠️ **IMPORTANTE:** Esta configuración es para desarrollo y pruebas. Para producción:

1. **Cambiar todas las contraseñas** en `.env`
2. **Configurar HTTPS** con certificados SSL (Let's Encrypt)
3. **Especificar dominios exactos** en `config/cors.php` (no usar `*`)
4. **Usar MySQL en producción** (no SQLite)
5. **Configurar Redis** para caché
6. **Habilitar modo debug solo en desarrollo:**
   ```env
   APP_DEBUG=false
   ```
7. **Usar variables de entorno seguras** (no commitear `.env` a git)

---

## 📚 Recursos Adicionales

- [Documentación de Laravel Sail](https://laravel.com/docs/sail)
- [Documentación de Docker Compose](https://docs.docker.com/compose/)
- [Documentación de Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Guía de despliegue del proyecto](DEPLOY.md)

---

*Guía generada el 30 de julio de 2026. Para soporte, consultar la documentación del proyecto.*
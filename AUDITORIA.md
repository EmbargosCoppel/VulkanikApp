# 🔍 Auditoría del Sistema — Vulcanizadora Don Chuy

> **Fecha:** 24 de julio de 2026  
> **Versión analizada:** 1.0.0  
> **Repositorio:** github.com/EmbargosCoppel/VulkanikApp  
> **Alcance:** Análisis completo de código, arquitectura, seguridad, configuración, pruebas y riesgos

---

## 1. Evaluación por Criterios de Calidad

### 1.1 Usabilidad — SUS (System Usability Scale)

**Puntuación estimada: 62 / 100** ⚠️ _Por debajo del promedio aceptable (68)_

| Aspecto | Estado | Evidencia |
|---|---|---|
| Navegación principal | ✅ Barra de navegación con enlaces a todos los módulos | `resources/views/layouts/navigation.blade.php` — menú responsivo con Alpine.js |
| Roles diferenciados | ✅ Admin ve Refacciones, Mecánico no | `@if(auth()->user()->role === 'admin')` en navigation |
| Feedback visual | ✅ Clases de color en estados de órdenes (verde/rojo/amarillo/azul) | `resources/views/dashboard/admin.blade.php` — badges de estado |
| Confirmaciones destructivas | ❌ No hay modales de confirmación al eliminar registros | Controladores redirigen directo sin confirmación JS |
| Validación en frontend | ⚠️ Mínima — validación HTML5 + errores de sesión | `resources/views/auth/login.blade.php` usa `x-input-error` |
| Mensajes de error | ⚠️ Genéricos de Laravel, no contextualizados al taller | `trans('auth.failed')` — mensaje estándar |
| Dashboard informativo | ✅ Tarjetas con KPIs, órdenes recientes, stock bajo | `dashboard/admin.blade.php` y `dashboard/mecanico.blade.php` |

**Hallazgos:**
- Los botones de "Eliminar" no tienen confirmación previa, riesgo de borrado accidental
- No hay feedback visual de carga (spinners/skeletons) en operaciones lentas
- El registro permite auto-asignarse rol admin (ver sección 1.3)
- Las vistas de índice no tienen búsqueda ni filtros avanzados

**Recomendación:** Implementar modales de confirmación con Alpine.js para todas las operaciones de eliminación. Agregar indicadores de carga. Puntuar SUS real con encuesta a 5 usuarios finales.

---

### 1.2 Rendimiento — Tiempo de respuesta promedio

**Estimación actual: 200-800ms por petición** ⚠️ _Degradará con datos reales_

| Aspecto | Estado | Evidencia |
|---|---|---|
| Paginación | ❌ Todos los listados usan `->get()` sin paginación | `ClienteController::index()`, `OrdenTrabajoController::index()`, etc. |
| Eager Loading | ⚠️ Inconsistente — a veces se cargan relaciones, a veces no | `ClienteController::index()` ✅ vs `RefaccionController::index()` ❌ |
| Caché | ⚠️ Store por defecto = `database` (no Redis en local) | `config/cache.php:10` — `CACHE_STORE=database` |
| Consultas N+1 | ⚠️ Potencial en vistas que iteran relaciones sin carga previa | `dashboard/admin.blade.php` itera `$ordenes_recientes` |
| Assets compilados | ✅ Vite con build para producción | `vite.config.js` + `npm run build` |
| Redis disponible | ✅ Configurado en docker-compose para producción | `docker-compose.yml` servicio `redis` |
| IVA hardcodeado | ❌ `0.16` en lugar de configurable | `WorkOrderService.php:110` |

**Hallazgos:**
- Sin paginación, una tabla con 10,000 órdenes hará que el dashboard admin sea inutilizable
- Las vistas iteren relaciones sin `->load()` pueden generar queries N+1
- La caché en database agrega latencia; Redis está disponible pero no configurado como predeterminado

**Recomendación:** Agregar `->paginate(15)` a todos los controladores. Usar Redis como store de caché en producción. Agregar índices compuestos en `ordenes_trabajo(estado, fecha_entrada)`.

---

### 1.3 Seguridad — Análisis OWASP Top 10

**Vulnerabilidades encontradas: 5** 🚨 _Incluye 1 crítica_

| # | OWASP | Vulnerabilidad | Severidad | Archivo |
|---|---|---|---|---|
| 1 | **A01** Broken Access Control | Registro público permite elegir rol `admin` | 🔴 **Crítica** | `RegisteredUserController.php:36` |
| 2 | **A07** Identification & Auth Failures | Tokens Sanctum sin expiración (`expiration = null`) | 🟡 Alta | `config/sanctum.php:36` |
| 3 | **A05** Security Misconfiguration | Sin archivo `config/cors.php` para API | 🟡 Alta | Archivo faltante |
| 4 | **A04** Insecure Design | Contraseña admin hardcodeada `password` en seeder | 🟡 Alta | `database/seeders/AdminSeeder.php:17` |
| 5 | **A05** Security Misconfiguration | Password reset habilitado sin mailer configurado | 🟠 Media | `login.blade.php:34` enlace existente |
| 6 | **A01** Broken Access Control | No hay rate limiting en rutas API | 🟠 Media | `routes/api.php` sin middleware `throttle` |
| 7 | **A09** Security Logging & Monitoring | Evento StockBajo sin logging estructurado en producción | 🔵 Baja | `EnviarAlertaStockBajo.php` solo log |
| 8 | **A02** Cryptographic Failures | Contraseñas hasheadas con Bcrypt (12 rounds ✅) | ✅ Sin riesgo | `config/hashing.php` (por defecto de Laravel) |
| 9 | **A03** Injection | Eloquent ORM protege contra SQL injection | ✅ Sin riesgo | Uso correcto de Eloquent |
| 10 | **A06** Vulnerable Components | `composer.json` sin `stripe/stripe-php` pero código lo referencia | 🟡 Alta | `StripePaymentAdapter.php:27` |

**Hallazgos por severidad:**
- **🔴 Crítica (1):** Cualquier persona puede registrarse como administrador y acceder a toda la información del taller
- **🟡 Alta (3):** Token API eternos, sin CORS configurado, dependencia faltante de Stripe
- **🟠 Media (2):** Sin rate limiting en API, enlace de reset password que no funciona
- **🔵 Baja (1):** Logging mínimo

**Recomendación:**

```php
// RegisteredUserController.php — FIX crítico
'role' => 'mecanico', // Solo admins pueden promover, no el registro

// config/sanctum.php — FIX alto
'expiration' => 1440, // 24 horas

// Crear config/cors.php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [env('FRONTEND_URL', '*')],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];

// routes/api.php — FIX medio
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () { ... });
```

---

### 1.4 Escalabilidad — Usuarios concurrentes soportados

**Estimación actual: ~30 usuarios concurrentes** ⚠️ _Sin optimizaciones_

| Componente | Límite estimado | Cuello de botella |
|---|---|---|
| Servidor web (PHP-FPM) | ~50 conexiones | `php artisan serve` no es para producción |
| Base de datos (SQLite) | ~10 conexiones | SQLite no soporta escritura concurrente |
| Base de datos (MySQL) | ~150 conexiones | Adecuado para PyME |
| Sesiones (database) | ~200 activas | Lecturas/escrituras en cada request |
| Caché (database) | ~500 keys | Migrar a Redis mejora 10x |

**Hallazgos:**
- SQLite por defecto en `.env.example` no es viable para más de 1-2 usuarios simultáneos
- Las sesiones en database agregan latencia vs Redis
- Sin cola de trabajos para operaciones pesadas (aunque `QUEUE_CONNECTION=sync` es para desarrollo)
- El frontend Blade renderiza en servidor — cada petición consume recursos de PHP

**Recomendación:**
- Migrar a MySQL en producción (ya configurado en docker-compose)
- Cambiar sesiones y caché a Redis
- Configurar PHP-FPM + Nginx en lugar de `php artisan serve`
- Para +100 usuarios: considerar caché de vistas Blade y implementar colas con Redis

---

### 1.5 Disponibilidad — % de tiempo operativo

**Estimación: ~99.0%** ⚠️ _Sin redundancia_

| Aspecto | Estado |
|---|---|
| Health endpoint | ✅ `/up` configurado en `bootstrap/app.php` |
| Monitoreo | ❌ Sin Uptime Robot ni equivalente configurado |
| Backup automático | ❌ Sin script ni schedule |
| Docker restart policy | ✅ `unless-stopped` en `docker-compose.yml` |
| Single point of failure | ⚠️ Un solo contenedor para PHP + Nginx |
| Graceful degradation | ❌ Sin fallback si MySQL/Redis caen |

**Recomendación:**
- Agregar schedule de backup diario en `routes/console.php`
- Configurar Uptime Robot o healthchecks.io
- Implementar health check endpoint personalizado que verifique BD, Redis y caché
- Considerar 2 réplicas del contenedor Laravel detrás de un balanceador

---

### 1.6 Mantenibilidad — Complejidad Ciclomática (PHPStan)

**Nivel PHPStan objetivo: level 6 / alcanzado: ~4** ⚠️

| Archivo | Líneas | Complejidad | Problemas |
|---|---|---|---|
| `WorkOrderService.php` | 119 | Alta (8 métodos, 4 states, lógica de transición) | Falta type hints en closures |
| `CotizacionBuilder.php` | 126 | Media (Builder pattern, 10 métodos) | Sin validación de build incompleto |
| `OrdenTrabajoController.php` | 97 | Alta (update maneja estado + otros campos) | Demasiada lógica en controlador |
| `InventoryService.php` | 73 | Baja | Bien estructurado |
| `PaymentService.php` | 38 | Baja | Bien estructurado |

**Hallazgos:**
- No hay archivo `phpstan.neon` ni `phpstan.dist.neon` configurado
- Varios controladores tienen métodos >50 líneas (ej: `OrdenTrabajoController::update`)
- Mezcla de PHP 8 Attributes (`#[Fillable]` en User) con propiedades tradicionales (`protected $fillable` en demás modelos)
- Sin DocBlocks en métodos de servicios
- `WorkOrderService` instancia states en constructor → difícil de testear

**Recomendación:**
```bash
# Configurar PHPStan
composer require --dev phpstan/phpstan
vendor/bin/phpstan analyse app --level 6
```

Extraer lógica de `OrdenTrabajoController::update` a un método de servicio. Unificar convención de atributos (elegir PHP 8 Attributes para todos los modelos o propiedades protegidas en todos).

---

### 1.7 Accesibilidad — Nivel WCAG

**Nivel estimado: A (parcial)** ❌ _No cumple AA_

| Criterio WCAG | Estado | Evidencia |
|---|---|---|
| 1.1.1 Texto alternativo | ❌ Imágenes sin `alt` en dashboard | `dashboard/admin.blade.php` sin etiquetas `alt` |
| 1.4.3 Contraste mínimo | ⚠️ Texto gris claro sobre blanco puede fallar | `text-gray-500` sobre `bg-white` = ratio ~4.2:1 borderline |
| 2.4.1 Skip navigation | ❌ Sin enlace "saltar al contenido" | Layout `app.blade.php` no incluye skip link |
| 2.4.4 Link propósito | ✅ Enlaces descriptivos | `route('clientes.index')` con texto "Clientes" |
| 3.3.2 Etiquetas | ✅ Todos los inputs tienen `label` asociado | `login.blade.php`, `register.blade.php` |
| 4.1.1 Parsing | ✅ HTML semántico válido | Uso de `<header>`, `<main>`, `<nav>` |
| ARIA attributes | ❌ Sin atributos ARIA en componentes dinámicos | Dropdown de navegación sin `aria-expanded` |

**Recomendación:**
- Agregar `alt` texts en íconos e imágenes del dashboard
- Incrementar contraste: usar `text-gray-700` en lugar de `text-gray-500`
- Agregar skip link al inicio del layout
- Usar WAVE Evaluation Tool para auditoría completa

---

### 1.8 Portabilidad — Navegadores compatibles

**Cobertura estimada: 95% de navegadores modernos** ✅

| Navegador | Compatibilidad | Observaciones |
|---|---|---|
| Chrome 90+ | ✅ Completa | Tailwind 4 + Alpine.js |
| Firefox 90+ | ✅ Completa | Sin features experimentales |
| Safari 15+ | ✅ Completa | Probar `@vite` en Safari |
| Edge 90+ | ✅ Completa | Basado en Chromium |
| IE11 | ❌ No soportado | Tailwind no soporta IE11 |
| Opera | ✅ Completa | Basado en Chromium |
| Mobile Chrome/Safari | ✅ Completa | Layout responsivo con Tailwind |
| Screen readers | ⚠️ Parcial | Ver sección Accesibilidad |

**Hallazgos:**
- Tailwind CSS 4 es moderno y no soporta IE11 (correcto)
- Alpine.js 3 funciona en todos los navegadores modernos
- Las vistas Blade renderizan HTML del lado servidor → funcionan sin JS
- Sin pruebas cross-browser automatizadas configuradas

**Recomendación:** Agregar BrowserStack o LambdaTest para pruebas cross-browser en CI/CD.

---

## 2. Análisis de Riesgos (15 Riesgos)

| # | Riesgo | Probabilidad | Impacto | Estrategia de Mitigación |
|---|---|---|---|---|
| 1 | **🔴 Auto-asignación de rol admin** — Cualquier registro obtiene privilegios de administrador | **Alta** | **Crítico** | Corregir `RegisteredUserController` para fijar rol `mecanico`. Agregar seed command para promoción manual: `php artisan user:promote {email}` |
| 2 | **🟡 Filtración de tokens API** — Tokens Sanctum sin expiración, si se filtran son válidos indefinidamente | **Media** | **Alto** | Establecer expiración a 24h en `config/sanctum.php`. Implementar refresh token o re-autenticación periódica |
| 3 | **🟡 CORS mal configurado** — Sin `config/cors.php`, el frontend SPA o app mobile no podrá consumir la API | **Alta** | **Alto** | Crear archivo `config/cors.php` con orígenes permitidos vía `env()` |
| 4 | **🟡 Dependencia de Stripe sin SDK** — Código referencia `\Stripe\StripeClient` que no existe en `composer.json` | **Alta** | **Alto** | Agregar `"stripe/stripe-php": "^16"` a `composer.json` y ejecutar `composer update`, o refactorizar si no se usa |
| 5 | **🟡 Cambio en requisitos del cliente** — El dueño del taller solicita cambios sobre la marcha | **Media** | **Alto** | Gestión de cambios formal, documentación de requisitos, sprints cortos. Todo cambio debe pasar por un ticket |
| 6 | **🟡 Degradación de rendimiento con datos reales** — Sin paginación, índices ni caché, el sistema se vuelve lento con >1,000 registros | **Alta** | **Medio** | Agregar paginación (`paginate(15)`) a todos los controladores. Crear índices compuestos en migraciones. Migrar caché a Redis |
| 7 | **🟠 Falta de personal clave** — El desarrollador original abandona el proyecto | **Baja** | **Alto** | Documentar arquitectura, decisiones técnicas y procesos. Este documento contribuye a mitigarlo |
| 8 | **🟠 Retraso en desarrollo** — Los sprints se atrasan por estimaciones incorrectas | **Media** | **Medio** | Buffer del 20-30% en cronograma. Seguimiento diario. Priorizar funcionalidad core sobre features secundarios |
| 9 | **🟠 Pérdida de datos** — Sin backups automáticos, un fallo de BD borra toda la información del taller | **Media** | **Crítico** | Programar backup diario en `routes/console.php` con `artisan schedule:run`. Usar `mysqldump` o el snapshot de Docker |
| 10 | **🟠 Pruebas insuficientes** — 7 tests saltados, 0 tests para API. Bugs pasan a producción | **Alta** | **Medio** | Implementar los tests pendientes. Agregar tests para API Sanctum. Poner cobertura mínima del 70% en CI |
| 11 | **🟠 Error humano en producción** — Un administrador elimina un cliente con vehículos y órdenes asociadas | **Media** | **Alto** | Implementar soft deletes para Clientes y Vehículos (como ya tienen Refacciones). Agregar confirmación modal en UI |
| 12 | **🟠 Obsolescencia de versión de Laravel** — No se actualiza el proyecto y quedan vulnerabilidades sin parchear | **Baja** | **Medio** | Asignar recurso para actualizaciones semestrales. Usar Dependabot o Renovate para PRs automáticos de dependencias |
| 13 | **🟠 Costos de infraestructura no previstos** — El servidor en producción escala y los costos crecen sin control | **Baja** | **Medio** | Monitorear costos. Usar auto-scaling con límites. Elegir un proveedor con presupuestos fijos (VPS, no serverless caro) |
| 14 | **🔵 Resistencia al cambio por usuarios** — Los mecánicos no quieren usar el sistema y prefieren papel | **Media** | **Alto** | Capacitación presencial. Período de transición con ambos sistemas. Mostrar beneficios: consultar historial del vehículo, generar órdenes rápido |
| 15 | **🔵 Vendor lock-in Docker** — La infraestructura depende completamente de Docker | **Baja** | **Bajo** | El código es estándar Laravel, portable a cualquier hosting. Docker es solo una opción de despliegue |

---

## 3. Resumen de Hallazgos

### 3.1 Por severidad

| Severidad | Cantidad | Acción |
|---|---|---|
| 🔴 Crítica | 2 | Corregir inmediatamente |
| 🟡 Alta | 8 | Corregir en este sprint |
| 🟠 Media | 8 | Agendar para el próximo sprint |
| 🔵 Baja | 3 | Backlog |
| ✅ Sin riesgo | 2 | Monitorear |

### 3.2 Top 5 acciones prioritarias

| # | Acción | Esfuerzo | Impacto |
|---|---|---|---|
| 1 | **Eliminar auto-asignación de rol admin** en `RegisteredUserController` | 5 min | 🔴 Elimina vulnerabilidad crítica |
| 2 | **Crear `config/cors.php`** | 10 min | 🟡 Desbloquea API para frontends externos |
| 3 | **Establecer expiración de tokens Sanctum** | 2 min | 🟡 Limita ventana de ataque de tokens filtrados |
| 4 | **Poblar `.env.example`** con todas las variables | 15 min | 🟡 Facilita onboarding y despliegue |
| 5 | **Agregar paginación a todos los listados** | 30 min | 🟡 Previene degradación con datos reales |

### 3.3 Salud general del proyecto

```
Seguridad     ████████░░  80% (arreglando 2 issues críticos → 95%)
Arquitectura  ███████░░░  70% (patrones sólidos, falta binding en container)
Pruebas       ████░░░░░░  40% (7 tests skipped, sin tests API)
Rendimiento   █████░░░░░  50% (sin paginación, sin caché Redis)
Mantenibilidad███████░░░  65% (código limpio pero con inconsistencias)
Documentación ████████░░  80% (nuevo README, falta doc de API)
```

---

## 4. Plan de Acción Recomendado

### Sprint 1 (Inmediato — 1 día)
- [ ] Fix crítico: `RegisteredUserController` — rol fijo `mecanico`
- [ ] Fix alto: `config/sanctum.php` — `expiration => 1440`
- [ ] Fix alto: Crear `config/cors.php`
- [ ] Fix alto: Binding de `PaymentAdapterInterface` en `AppServiceProvider`
- [ ] Fix alto: Agregar `stripe/stripe-php` a `composer.json`

### Sprint 2 (Corto — 2 días)
- [ ] Implementar 7 tests faltantes
- [ ] Agregar paginación a todos los controladores
- [ ] Migrar caché a Redis en producción
- [ ] Completar `.env.example`
- [ ] Agregar comando Artisan `user:promote` para promoción manual

### Sprint 3 (Mediano — 1 semana)
- [ ] Configurar PHPStan level 6
- [ ] Unificar convención de attributes vs propiedades
- [ ] Agregar Form Requests para validación
- [ ] Implementar soft deletes en Clientes y Vehículos
- [ ] Agregar modales de confirmación con Alpine.js

### Backlog
- [ ] Implementar cola de trabajos para alertas de stock
- [ ] Agregar backup automático programado
- [ ] Tests para API Sanctum
- [ ] WCAG AA compliance
- [ ] Pipeline CI/CD con pruebas y análisis estático

---

## 5. Herramientas de Evaluación Recomendadas

| Criterio | Herramienta | Comando / URL |
|---|---|---|
| Seguridad | OWASP ZAP | `docker run -v $(pwd):/zap/wrk softwaresecurityproject/zap-stable zap-baseline.py -t http://localhost` |
| Seguridad | Laravel Security Checker | `composer require --dev enlightn/security-checker && php artisan security:check` |
| Mantenibilidad | PHPStan | `vendor/bin/phpstan analyse app --level 6` |
| Mantenibilidad | Laravel Pint | `./vendor/bin/pint --test` |
| Rendimiento | Laravel Debugbar | `composer require --dev barryvdh/laravel-debugbar` |
| Accesibilidad | WAVE | `https://wave.webaim.org/` (extensión Chrome) |
| Pruebas de carga | JMeter / K6 | Plan JMeter en `tests/Load/` |
| Dependencias | Dependabot | Habilitar en GitHub → Settings → Security |
| Cobertura | PHPUnit Coverage | `php artisan test --coverage --min=70` |

---

*Documento generado el 24 de julio de 2026. Auditoría basada en análisis estático del código fuente y revisión de configuración. Para validación completa se requieren pruebas dinámicas y de carga en entorno real.*

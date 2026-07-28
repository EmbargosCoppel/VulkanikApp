<div align="center">
  <h1>🔧 Vulcanizadora Don Chuy</h1>
  <p><strong>Sistema de Gestión para Taller Mecánico</strong></p>
  <p>
    <img src="https://img.shields.io/badge/Laravel-11-red?logo=laravel" alt="Laravel 11">
    <img src="https://img.shields.io/badge/PHP-8.3-777BB4?logo=php" alt="PHP 8.3">
    <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss" alt="Tailwind CSS 4">
    <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql" alt="MySQL 8.0">
    <img src="https://img.shields.io/badge/Docker-2496ED?logo=docker" alt="Docker">
  </p>
</div>

---

## 📋 Tabla de Contenidos

- [Descripción](#-descripción)
- [Arquitectura](#-arquitectura)
- [Stack Tecnológico](#-stack-tecnológico)
- [Modelo de Datos](#-modelo-de-datos)
- [Patrones de Diseño](#-patrones-de-diseño)
- [Módulos del Sistema](#-módulos-del-sistema)
- [Rutas](#-rutas)
- [API RESTful](#-api-restful)
- [Instalación](#-instalación)
- [Uso](#-uso)
- [Pruebas](#-pruebas)
- [Despliegue con Docker](#-despliegue-con-docker)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Capturas](#-capturas)
- [Licencia](#-licencia)

---

## 📖 Descripción

**Vulcanizadora Don Chuy** es un sistema web integral para la administración de un taller mecánico / vulcanizadora. Está diseñado para gestionar clientes, vehículos, órdenes de trabajo, inventario de refacciones y cobros, con roles diferenciados para administradores y mecánicos.

### Funcionalidades principales

- **Dashboard** con estadísticas en tiempo real para administradores y mecánicos
- **Gestión de clientes** con soporte para personas físicas y empresas (RFC, razón social)
- **Registro de vehículos** con todos los datos del automóvil (VIN, placas, color, etc.)
- **Órdenes de trabajo** con máquina de estados: diagnóstico → esperando piezas → reparación → finalizado
- **Control de inventario** con alertas automáticas de stock bajo
- **Cotizaciones** con estrategias de precio diferenciadas (público general, premium, flotilla)
- **Procesamiento de pagos** mediante adaptador Stripe (extensible)
- **API RESTful** con autenticación por tokens Sanctum
- **Roles**: administrador (control total) y mecánico (ver/editar órdenes y vehículos)

---

## 🏗️ Arquitectura

El sistema sigue una arquitectura **monolítica moderna** sobre Laravel, con separación clara en capas:

```
┌─────────────────────────────────────────────────┐
│                  Presentación                    │
│  Blade + Tailwind + Alpine.js + Vite            │
├─────────────────────────────────────────────────┤
│               Controladores (Web + API)          │
├─────────────────────────────────────────────────┤
│              Capa de Servicios                   │
│  WorkOrderService │ InventoryService            │
│  PaymentService   │ CotizacionBuilder           │
├─────────────────────────────────────────────────┤
│               Patrones de Diseño                 │
│  State │ Strategy │ Builder │ Adapter            │
├─────────────────────────────────────────────────┤
│              Modelos + Eloquent ORM              │
├─────────────────────────────────────────────────┤
│              Base de Datos (MySQL/SQLite)        │
└─────────────────────────────────────────────────┘
```

### Principios aplicados

- **Domain-Driven Design** — lógica de negocio encapsulada en servicios
- **Patrón State** — ciclo de vida de órdenes de trabajo
- **Patrón Strategy** — cálculo de precios según tipo de cliente
- **Patrón Builder** — construcción de cotizaciones paso a paso
- **Patrón Adapter** — abstracción de pasarelas de pago
- **Role-Based Access Control** — middleware de roles
- **Event-Driven** — eventos de stock bajo con listeners

---

## 🛠️ Stack Tecnológico

| Tecnología | Versión | Propósito |
|---|---|---|
| **Laravel** | 11.x | Framework principal |
| **PHP** | 8.3+ | Lenguaje de backend |
| **MySQL** | 8.0 | Base de datos en producción |
| **SQLite** | — | Base de datos en desarrollo |
| **Redis** | Alpine | Caché y sesiones |
| **Tailwind CSS** | 4.x | Estilos utilitarios |
| **Alpine.js** | 3.x | Interactividad frontend |
| **Vite** | 8.x | Bundler y HMR |
| **Laravel Breeze** | 2.x | Scaffolding de autenticación |
| **Laravel Sanctum** | 4.x | API tokens |
| **Docker / Laravel Sail** | — | Contenedores |

---

## 💾 Modelo de Datos

### Diagrama Entidad-Relación

```
┌──────────┐     ┌──────────┐     ┌────────────────┐
│  Users   │     │ Clientes │     │   Vehículos    │
├──────────┤     ├──────────┤     ├────────────────┤
│ id       │◄────│ id       │     │ id             │
│ name     │     │ nombre   │     │ cliente_id ────┤
│ email    │     │ telefono │     │ marca          │
│ password │     │ email    │     │ modelo         │
│ role     │     │ direccion│     │ anio           │
└──────────┘     │ rfc      │     │ placa (unique) │
     │           │ es_empresa│    │ color          │
     │           │ nombre_emp│    │ vin            │
     │           └──────────┘     │ notas          │
     │                            └───────┬────────┘
     │                                    │
     │    ┌──────────────────────┐        │
     │    │   Ordenes Trabajo    │        │
     │    ├──────────────────────┤        │
     └────│ mecanico_id         │        │
          │ vehiculo_id ────────┼────────┘
          │ estado (enum)       │
          │ diagnostico         │
          │ trabajos_realizados │
          │ mano_obra (decimal) │
          │ subtotal (decimal)  │
          │ iva (decimal)       │
          │ total (decimal)     │
          │ fecha_entrada       │
          │ fecha_salida        │
          └──────────┬───────────┘
                     │  M:N (orden_refaccion)
          ┌──────────┴───────────┐
          │     Refacciones      │
          ├──────────────────────┤
          │ id                   │
          │ nombre               │
          │ sku (unique)         │
          │ descripcion          │
          │ costo (decimal)      │
          │ precio_venta (dec)   │
          │ stock_actual         │
          │ stock_minimo         │
          │ ubicacion            │
          │ proveedor            │
          │ activo (boolean)     │
          │ deleted_at (soft)    │
          └──────────────────────┘
```

### Tabla Pivote: `orden_refaccion`

| Campo | Tipo |
|---|---|
| orden_trabajo_id | FK |
| refaccion_id | FK |
| cantidad | integer |
| precio_unitario | decimal(10,2) |
| subtotal | decimal(10,2) |
| created_at | timestamp |
| updated_at | timestamp |

### Estados de Orden de Trabajo

```
diagnóstico ──→ esperando_piezas ──→ reparación ──→ finalizado
      │              │                   │
      └──────────────┴───────────────────┘
                    (salto directo)
```

---

## 🧩 Patrones de Diseño

### 1. State Pattern — Ciclo de Vida de Órdenes

```php
// Interfaz
OrdenStateInterface {
    puedeAgregarRefacciones(): bool
    puedeCambiarEstado(): bool
    getEstadoNombre(): string
    siguienteEstado(): ?string
}

// Implementaciones concretas
DiagnosticoState      → puede agregar refacciones, transiciona a cualquier estado
EsperandoPiezasState  → puede agregar refacciones, transiciona a reparación o finalizado
ReparacionState       → puede agregar refacciones, transiciona solo a finalizado
FinalizadoState       → no permite cambios ni refacciones
```

Gestionado por `WorkOrderService` que orquesta las transiciones válidas.

### 2. Strategy Pattern — Cálculo de Precios

```php
PricingStrategyInterface {
    calcularTotal(float $subtotal): float
    aplicarDescuento(float $subtotal): float
}

// Estrategias concretas:
PublicoGeneralStrategy   → sin descuento
ClientePremiumStrategy   → 15% de descuento
FlotillaStrategy         → 25% de descuento (flotas empresariales)
```

### 3. Builder Pattern — Cotizaciones

`CotizacionBuilder` permite construir cotizaciones paso a paso:

```php
$cotizacion = (new CotizacionBuilder)
    ->setCliente('Juan Pérez')
    ->setVehiculo('Nissan', 'Tsuru', 'ABC-123')
    ->setManoObra(800.00)
    ->addRefaccion($filtro, 2)
    ->addServicio('Alineación', 300.00)
    ->setPricingStrategy(new ClientePremiumStrategy())
    ->setNotas('Válido por 7 días')
    ->build();
```

### 4. Adapter Pattern — Pasarela de Pago

```php
PaymentAdapterInterface {
    procesarPago(float $monto, array $datosPago): array
    reembolsar($orden): array
}

// Implementación:
StripePaymentAdapter → procesa pagos con Stripe (simulado)
```

### 5. Event-Driven — Alertas de Stock

```php
Event:  StockBajo(refaccion, stockActual, stockMinimo)
        → se dispara al actualizar stock por debajo del mínimo

Listener: EnviarAlertaStockBajo
        → loguea warning (extensible a email/SMS)
```

---

## 📦 Módulos del Sistema

### 1. Dashboard

- **Admin**: tarjetas con conteo de clientes, vehículos, órdenes pendientes/finalizadas, stock bajo + tabla de órdenes recientes + refacciones críticas
- **Mecánico**: órdenes asignadas, conteo de completadas, pendientes

### 2. Clientes (CRUD)

- Campos: nombre, teléfono, email, dirección, RFC, tipo (persona/empresa), nombre de empresa
- Relación 1:N con vehículos
- Acceso: solo administradores (`role:admin`)

### 3. Vehículos (CRUD)

- Campos: marca, modelo, año, placa (única), color, VIN, notas
- Relación N:1 con cliente, 1:N con órdenes de trabajo
- Acceso: administradores y mecánicos (`role:admin,mecanico`)

### 4. Refacciones / Inventario (CRUD + Control de Stock)

- Campos: nombre, SKU (único), descripción, costo, precio_venta, stock_actual, stock_minimo, ubicación, proveedor
- Soft deletes (eliminación suave)
- Vista de stock bajo con alertas
- Actualización manual de stock con evento de alerta
- Acceso: solo administradores

### 5. Órdenes de Trabajo

- Ciclo completo con máquina de estados
- Asignación a mecánico
- Agregar refacciones con descuento automático de inventario
- Cálculo automático de subtotal, IVA (16%) y total
- Fecha de entrada automática, fecha de salida al finalizar
- Acceso: administradores y mecánicos

### 6. Procesamiento de Pagos

- Integración con adaptador Stripe (interfaz preparada para otros)
- Finalización de orden al procesar pago
- Reembolsos

### 7. Autenticación y Roles

- Login/Register con Laravel Breeze
- Middleware de roles: `role:admin` o `role:admin,mecanico`
- API autenticada con tokens Sanctum

### 8. API RESTful

Endpoints protegidos con Sanctum:

| Método | Endpoint | Descripción |
|---|---|---|
| POST | `/api/login` | Obtener token |
| GET | `/api/user` | Usuario autenticado |
| CRUD | `/api/clientes` | Clientes |
| CRUD | `/api/vehiculos` | Vehículos |
| GET | `/api/vehiculos/cliente/{id}` | Vehículos por cliente |
| CRUD | `/api/ordenes-trabajo` | Órdenes de trabajo |
| POST | `/api/ordenes-trabajo/{id}/refacciones` | Agregar refacción |
| GET | `/api/ordenes-trabajo/{id}/totales` | Calcular totales |
| CRUD | `/api/refacciones` | Refacciones |
| PUT | `/api/refacciones/{id}/stock` | Actualizar stock |
| GET | `/api/refacciones/stock-bajo` | Refacciones con stock bajo |

---

## 🧭 Rutas

### Web (web.php)

| Método | URI | Middleware | Controlador |
|---|---|---|---|
| GET | `/` | auth | DashboardController |
| GET/PUT/DELETE | `/profile` | auth | ProfileController |
| CRUD | `/clientes` | auth, role:admin | ClienteController |
| CRUD | `/vehiculos` | auth, role:admin,mecanico | VehiculoController |
| CRUD | `/refacciones` | auth, role:admin | RefaccionController |
| GET/PUT | `/refacciones/{id}/stock` | auth, role:admin | RefaccionController |
| GET | `/refacciones/stock-bajo` | auth, role:admin | RefaccionController |
| CRUD | `/ordenes` | auth, role:admin,mecanico | OrdenTrabajoController |
| POST | `/ordenes/{id}/refacciones` | auth, role:admin,mecanico | OrdenTrabajoController |
| GET/POST | `/login` | guest | Auth |
| GET/POST | `/register` | guest | Auth |

---

## 🚀 Instalación

### Requisitos

- PHP 8.3+
- Composer 2.x
- Node.js 20+
- MySQL 8.0 (o SQLite para desarrollo)
- Docker + Docker Compose (opcional)

### Instalación local

```bash
# 1. Clonar el repositorio
git clone https://github.com/EmbargosCoppel/VulkanikApp.git
cd VulkanikApp

# 2. Instalar dependencias de PHP
composer install

# 3. Instalar dependencias de Node
npm install

# 4. Configurar entorno
cp .env.example .env
php artisan key:generate

# 5. Configurar base de datos en .env
#    DB_CONNECTION=sqlite (por defecto) o mysql

# 6. Ejecutar migraciones y seeders
php artisan migrate --seed

# 7. Compilar assets
npm run build

# 8. Iniciar servidor
php artisan serve
```

### Usuarios por defecto (seeders)

| Rol | Email | Password |
|---|---|---|
| **Admin** | admin@taller.com | password |
| **Mecánico** | mecanico@taller.com | password |

### Instalación con Docker (Laravel Sail)

```powershell
# Windows PowerShell (como administrador)
.\start-docker.ps1

# --- O manualmente ---

# Instalar dependencias
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

# Iniciar contenedores
./vendor/bin/sail up -d

# Migrar y seedear
./vendor/bin/sail artisan migrate --seed

# Compilar assets
npm install
npm run build
```

---

## 🖥️ Uso

### Desarrollo con recarga en caliente

```bash
# Opción 1: Laravel + Vite juntos
npm start

# Opción 2: En dos terminales
php artisan serve     # Terminal 1
npm run dev           # Terminal 2
```

### Scripts disponibles

| Comando | Descripción |
|---|---|
| `npm start` | Inicia servidor PHP + Vite simultáneamente |
| `npm run dev` | Compila assets con Vite (modo desarrollo) |
| `npm run build` | Compila assets para producción |
| `php artisan migrate --seed` | Migra BD + datos de prueba |
| `php artisan make:migration` | Crear migración |
| `composer run setup` | Configuración inicial (.env + key + migrate) |
| `.\start-local.ps1` | Inicio rápido en local (Windows) |
| `.\start-docker.ps1` | Inicio rápido con Docker (Windows) |
| `.\stop.ps1` | Detener servicios (Windows) |

---

## 🧪 Pruebas

```bash
# Ejecutar toda la suite de pruebas
php artisan test

# Ejecutar pruebas con cobertura
php artisan test --coverage

# Ejecutar pruebas de un módulo específico
php artisan test tests/Feature/ClienteTest.php
php artisan test tests/Feature/OrdenTrabajoTest.php
php artisan test tests/Feature/RefaccionTest.php
```

### Tests disponibles

| Archivo | Descripción |
|---|---|
| `ClienteTest.php` | CRUD clientes, validaciones, empresa |
| `OrdenTrabajoTest.php` | CRUD órdenes, estados, refacciones, cálculos |
| `RefaccionTest.php` | CRUD refacciones, stock, validaciones |
| `VehiculoTest.php` | CRUD vehículos |
| `DashboardTest.php` | Vistas de dashboard |
| `ProfileTest.php` | Actualización de perfil |
| `Auth/*` | Autenticación |

---

## 🐳 Despliegue con Docker

El proyecto incluye Docker Compose con tres servicios:

```yaml
servicios:
  laravel.test:   # PHP 8.3 + Nginx (puerto 80)
  mysql:          # MySQL 8.0 (puerto 3307)
  redis:          # Redis (puerto 6379)
```

```bash
# Construir y levantar
docker compose up -d

# Ver logs
docker compose logs -f

# Ejecutar comandos dentro del contenedor
docker compose exec laravel.test php artisan migrate

# Detener
docker compose down
```

---

## 📁 Estructura del Proyecto

```
taller/
├── app/
│   ├── Events/
│   │   └── StockBajo.php              # Evento de stock bajo
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                   # Controladores API
│   │   │   │   ├── ClienteController.php
│   │   │   │   ├── VehiculoController.php
│   │   │   │   ├── OrdenTrabajoController.php
│   │   │   │   └── RefaccionController.php
│   │   │   ├── Auth/                  # Auth (Breeze)
│   │   │   ├── ClienteController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── OrdenTrabajoController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── RefaccionController.php
│   │   │   └── VehiculoController.php
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php     # Control de acceso por rol
│   │   └── Requests/
│   ├── Listeners/
│   │   └── EnviarAlertaStockBajo.php  # Listener de stock bajo
│   ├── Models/
│   │   ├── Cliente.php
│   │   ├── OrdenTrabajo.php
│   │   ├── Refaccion.php
│   │   ├── User.php
│   │   └── Vehiculo.php
│   ├── Providers/
│   ├── Services/
│   │   ├── Adapters/
│   │   │   └── StripePaymentAdapter.php
│   │   ├── Builders/
│   │   │   └── CotizacionBuilder.php
│   │   ├── States/
│   │   │   ├── DiagnosticoState.php
│   │   │   ├── EsperandoPiezasState.php
│   │   │   ├── FinalizadoState.php
│   │   │   ├── OrdenStateInterface.php
│   │   │   └── ReparacionState.php
│   │   ├── Strategies/
│   │   │   ├── ClientePremiumStrategy.php
│   │   │   ├── FlotillaStrategy.php
│   │   │   ├── PricingStrategyInterface.php
│   │   │   └── PublicoGeneralStrategy.php
│   │   ├── InventoryService.php
│   │   ├── PaymentAdapterInterface.php
│   │   ├── PaymentService.php
│   │   └── WorkOrderService.php
│   └── View/Components/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2024_01_01_000002_create_clientes_table.php
│   │   ├── 2024_01_01_000003_create_vehiculos_table.php
│   │   ├── 2024_01_01_000004_create_refacciones_table.php
│   │   ├── 2024_01_01_000005_create_ordenes_trabajo_table.php
│   │   ├── 2024_01_01_000006_create_orden_refaccion_table.php
│   │   ├── 2026_07_20_175623_add_deleted_at_to_refacciones_table.php
│   │   └── 2026_07_24_214732_create_personal_access_tokens_table.php
│   └── seeders/
│       ├── AdminSeeder.php
│       ├── ClienteSeeder.php
│       ├── DatabaseSeeder.php
│       └── RefaccionSeeder.php
├── docker/
│   └── 8.3/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── auth/
│       ├── clientes/
│       ├── components/
│       ├── dashboard/
│       ├── layouts/
│       ├── ordenes/
│       ├── profile/
│       ├── refacciones/
│       ├── vehiculos/
│       ├── dashboard.blade.php
│       └── welcome.blade.php
├── routes/
│   ├── api.php
│   ├── auth.php
│   ├── console.php
│   └── web.php
├── storage/
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   ├── ClienteTest.php
│   │   ├── DashboardTest.php
│   │   ├── ExampleTest.php
│   │   ├── OrdenTrabajoTest.php
│   │   ├── ProfileTest.php
│   │   ├── RefaccionTest.php
│   │   └── VehiculoTest.php
│   └── Unit/
├── .editorconfig
├── .env.example
├── .gitattributes
├── .gitignore
├── .npmrc
├── composer.json
├── docker-compose.yml
├── package.json
├── phpunit.xml
├── postcss.config.js
├── tailwind.config.js
└── vite.config.js
```

---

## 📸 Capturas

> *(Agrega capturas de pantalla aquí)*

### Dashboard Admin
![Dashboard Admin](docs/screenshots/dashboard-admin.png)

### Lista de Clientes
![Clientes](docs/screenshots/clientes.png)

### Órdenes de Trabajo
![Órdenes](docs/screenshots/ordenes.png)

### Inventario
![Refacciones](docs/screenshots/refacciones.png)

---

## 🧠 Detalles Técnicos

### Migraciones

El proyecto cuenta con **10 migraciones** ejecutadas en orden específico:

1. `users` — tabla de usuarios con campo `role` (admin/mecanico)
2. `cache` — tabla de caché
3. `jobs` — cola de trabajos
4. `clientes` — información de clientes
5. `vehiculos` — vehículos con placa única
6. `refacciones` — inventario con soft deletes
7. `ordenes_trabajo` — órdenes con máquina de estados
8. `orden_refaccion` — tabla pivote N:M con cantidades y precios
9. `add_deleted_at_to_refacciones` — soft deletes para refacciones
10. `personal_access_tokens` — tokens de Sanctum

### Factory Seeders

```bash
# AdminSeeder:
#   - admin@taller.com (admin) / password
#   - mecanico@taller.com (mecanico) / password

# ClienteSeeder:
#   - 10 clientes con datos realistas mexicanos

# RefaccionSeeder:
#   - 20 refacciones comunes de taller
```

### Eventos y Listeners

- **Evento**: `StockBajo` — se dispara cuando `stock_actual <= stock_minimo`
- **Listener**: `EnviarAlertaStockBajo` — registra en log (extensible a email/SMS)

### Soft Deletes

Las `Refacciones` usan **SoftDeletes** de Laravel para eliminación lógica, preservando el historial de órdenes que las referencian.

---

## 🔌 Endpoints de API

### Autenticación

```bash
# Obtener token
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@taller.com","password":"password"}'

# Respuesta:
{
  "token": "1|abc123...",
  "user": { "id": 1, "name": "Admin User", "email": "admin@taller.com", "role": "admin" }
}
```

### Usar token en peticiones

```bash
curl http://localhost/api/clientes \
  -H "Authorization: Bearer 1|abc123..."
```

### Ejemplos de respuesta

```json
// GET /api/clientes
[
  {
    "id": 1,
    "nombre": "Juan Pérez López",
    "telefono": "555-1234-5678",
    "email": "juan@example.com",
    "direccion": "Av. Siempre Viva 123, Col. Centro",
    "rfc": "PELJ800101XXX",
    "es_empresa": false,
    "nombre_empresa": null,
    "vehiculos": [
      {
        "id": 1,
        "marca": "Nissan",
        "modelo": "Versa",
        "placa": "ABC-123"
      }
    ]
  }
]

// GET /api/refacciones/stock-bajo
[
  {
    "id": 5,
    "nombre": "Aceite de motor 20W-50",
    "sku": "ACE-001",
    "stock_actual": 2,
    "stock_minimo": 10
  }
]
```

---

## 🤝 Contribuir

1. Haz fork del proyecto
2. Crea tu rama (`git checkout -b feature/nueva-funcionalidad`)
3. Haz commit de tus cambios (`git commit -m 'Agrega nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

### Convenciones de código

- PHP: PSR-12, tipado estricto, DocBlocks en interfaces
- Nombres de métodos: verbos en español (crear, obtener, actualizar, eliminar)
- Servicios con inyección de dependencias vía constructor
- Validación en controladores con Form Requests cuando sea posible

---

## 📄 Licencia

MIT — Este proyecto es de código abierto. Si lo usas, agradecemos la atribución.

---

<div align="center">
  <p>Hecho con 🔧 para <strong>Vulcanizadora Don Chuy</strong></p>
  <p>
    <a href="https://github.com/EmbargosCoppel/VulkanikApp">GitHub</a>
  </p>
</div>

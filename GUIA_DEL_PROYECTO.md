# 📋 Guía Completa del Proyecto: Vulcanizadora Don Chuy

> **Sistema de Gestión de Taller Mecánico**  
> Una aplicación web completa construida con **Laravel 11**, **Vite**, **Tailwind CSS** y **Alpine.js** para la gestión integral de un taller de vulcanización y mecánica automotriz.

---

## 1. 🏗️ Visión General del Proyecto

### Descripción
La **Vulcanizadora Don Chuy** es un sistema de gestión para talleres mecánicos que permite:

- **Gestión de Clientes**: Registro, edición y eliminación de clientes (personas físicas y empresas).
- **Gestión de Vehículos**: Registro de vehículos asociados a clientes.
- **Gestión de Refacciones**: Control de inventario con alertas de stock bajo.
- **Órdenes de Trabajo**: Creación y seguimiento de órdenes de reparación con cálculo automático de totales e IVA.
- **API RESTful**: Interfaz de programación de aplicaciones protegida con Sanctum.
- **Autenticación y Autorización**: Sistema de roles (admin, mecánico) con control de acceso granular.

### Usuarios del Sistema
| Email | Contraseña | Rol | Descripción |
|-------|------------|-----|-------------|
| `admin@taller.com` | `password` | **admin** | Acceso completo a todas las funcionalidades |
| `mecanico@taller.com` | `password` | **mecanico** | Acceso a vehículos, órdenes y su propio dashboard |
| *(registro)* | *(cualquiera)* | **mecanico** | Los nuevos usuarios registrados son mecánicos por defecto |

---

## 2. 🛠️ Tecnologías Utilizadas

### Backend
| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| **PHP** | 8.3+ | Lenguaje de programación |
| **Laravel** | 11.x | Framework web (MVC) |
| **SQLite** | 3 | Base de datos (configurable a MySQL) |
| **Laravel Sanctum** | 4.3 | Autenticación API con tokens |
| **Laravel Breeze** | 2.4 | Scaffolding de autenticación |

### Frontend
| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| **Vite** | 8.x | Bundler y servidor de desarrollo |
| **Tailwind CSS** | 3.x | Framework de estilos CSS |
| **Alpine.js** | 3.x | Interactividad en el navegador |
| **Font Awesome** | 6.5 | Iconografía |
| **Blade** | - | Motor de plantillas de Laravel |

### Patrones de Diseño
- **Strategy Pattern**: Estrategias de precios (ClientePremium, Flotilla, Público General)
- **State Pattern**: Estados de órdenes de trabajo (Diagnóstico, Esperando Piezas, Reparación, Finalizado)
- **Adapter Pattern**: Adaptador de pagos (Stripe)
- **Service Layer**: Servicios de negocio (InventoryService, WorkOrderService, PaymentService)
- **Repository/Builder Pattern**: CotizacionBuilder para construcción de cotizaciones

---

## 3. 🗄️ Arquitectura y Estructura de Archivos

```
taller/
├── app/                          # Código de la aplicación
│   ├── Console/                  # Comandos de consola
│   ├── Events/                   # Eventos (StockBajo)
│   ├── Http/
│   │   ├── Controllers/          # Controladores
│   │   │   ├── Api/              # Controladores API RESTful
│   │   │   ├── Auth/             # Controladores de autenticación
│   │   │   ├── ClienteController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── OrdenTrabajoController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── RefaccionController.php
│   │   │   └── VehiculoController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php  # Middleware de roles
│   ├── Models/                   # Modelos Eloquent
│   │   ├── Cliente.php
│   │   ├── OrdenTrabajo.php
│   │   ├── Refaccion.php
│   │   ├── User.php
│   │   └── Vehiculo.php
│   ├── Providers/                # Proveedores de servicios
│   │   ├── AppServiceProvider.php  # Binding de dependencias
│   │   └── EventServiceProvider.php
│   └── Services/                 # Servicios de negocio
│       ├── Adapters/
│       │   └── StripePaymentAdapter.php
│       ├── Builders/
│       │   └── CotizacionBuilder.php
│       ├── States/               # State Pattern
│       │   ├── DiagnosticoState.php
│       │   ├── EsperandoPiezasState.php
│       │   ├── FinalizadoState.php
│       │   ├── OrdenStateInterface.php
│       │   └── ReparacionState.php
│       ├── Strategies/           # Strategy Pattern
│       │   ├── ClientePremiumStrategy.php
│       │   ├── FlotillaStrategy.php
│       │   ├── PricingStrategyInterface.php
│       │   └── PublicoGeneralStrategy.php
│       ├── InventoryService.php
│       ├── PaymentAdapterInterface.php
│       ├── PaymentService.php
│       └── WorkOrderService.php
├── bootstrap/                    # Arranque de la aplicación
├── config/                       # Configuración
│   └── taller.php               # Configuración específica del taller
├── database/
│   ├── database.sqlite          # Base de datos SQLite
│   ├── factories/               # Factories de prueba
│   ├── migrations/              # Migraciones de base de datos
│   └── seeders/                 # Seeders
│       ├── AdminSeeder.php      # Crea usuarios admin y mecánico
│       ├── ClienteSeeder.php    # Crea clientes de prueba
│       ├── DatabaseSeeder.php   # Orquestador de seeders
│       └── RefaccionSeeder.php  # Crea refacciones de prueba
├── public/                       # Archivos públicos
│   └── index.php                # Punto de entrada
├── resources/                    # Recursos frontend
│   ├── css/
│   │   └── app.css              # Tailwind CSS imports
│   ├── js/
│   │   ├── app.js               # Alpine.js initialization
│   │   └── bootstrap.js         # Bootstrap de Vite
│   └── views/                   # Vistas Blade
│       ├── auth/                # Vistas de autenticación
│       ├── clientes/            # Vistas de clientes
│       ├── components/          # Componentes Blade reutilizables
│       ├── dashboard/           # Dashboards (admin/mecánico)
│       ├── layouts/             # Layouts principales
│       ├── ordenes/             # Vistas de órdenes
│       ├── profile/             # Vistas de perfil
│       ├── refacciones/         # Vistas de refacciones
│       └── vehiculos/           # Vistas de vehículos
├── routes/                       # Rutas
│   ├── api.php                  # Rutas API RESTful
│   ├── auth.php                 # Rutas de autenticación
│   └── web.php                  # Rutas web
├── storage/                      # Almacenamiento
├── vendor/                       # Dependencias de Composer
└── node_modules/                 # Dependencias de npm
```

---

## 4. 🗃️ Esquema de Base de Datos

### Tabla: `users`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint (PK) | ID autoincremental |
| `name` | string | Nombre completo |
| `email` | string (unique) | Email único |
| `email_verified_at` | datetime | Fecha de verificación de email |
| `password` | string | Contraseña hasheada |
| `role` | string | Rol: `admin` o `mecanico` |
| `remember_token` | string | Token de "recordarme" |
| `created_at` / `updated_at` | datetime | Timestamps |

### Tabla: `clientes`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint (PK) | ID autoincremental |
| `nombre` | string | Nombre del cliente |
| `telefono` | string | Teléfono de contacto |
| `email` | string (nullable) | Email del cliente |
| `direccion` | text (nullable) | Dirección física |
| `rfc` | string (nullable) | RFC (13 caracteres) |
| `es_empresa` | boolean | Si es empresa (default: false) |
| `nombre_empresa` | string (nullable) | Nombre de la empresa |
| `deleted_at` | datetime (nullable) | Soft delete |
| `created_at` / `updated_at` | datetime | Timestamps |

### Tabla: `vehiculos`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint (PK) | ID autoincremental |
| `cliente_id` | bigint (FK) | Referencia a clientes |
| `marca` | string | Marca del vehículo |
| `modelo` | string | Modelo del vehículo |
| `anio` | integer | Año de fabricación |
| `placa` | string (unique) | Placa única |
| `color` | string (nullable) | Color del vehículo |
| `vin` | string (nullable) | VIN (17 caracteres) |
| `notas` | text (nullable) | Notas adicionales |
| `deleted_at` | datetime (nullable) | Soft delete |
| `created_at` / `updated_at` | datetime | Timestamps |

### Tabla: `refacciones`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint (PK) | ID autoincremental |
| `nombre` | string | Nombre de la refacción |
| `sku` | string (unique) | SKU único |
| `descripcion` | text (nullable) | Descripción detallada |
| `costo` | decimal(10,2) | Costo de adquisición |
| `precio_venta` | decimal(10,2) | Precio de venta |
| `stock_actual` | integer | Stock disponible |
| `stock_minimo` | integer | Stock mínimo de alerta |
| `ubicacion` | string (nullable) | Ubicación en almacén |
| `proveedor` | string (nullable) | Proveedor |
| `activo` | boolean | Si está activo (default: true) |
| `deleted_at` | datetime (nullable) | Soft delete |
| `created_at` / `updated_at` | datetime | Timestamps |

### Tabla: `ordenes_trabajo`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint (PK) | ID autoincremental |
| `vehiculo_id` | bigint (FK) | Referencia a vehículos |
| `mecanico_id` | bigint (FK) | Referencia a users (mecánico) |
| `estado` | enum | `diagnóstico`, `esperando_piezas`, `reparación`, `finalizado` |
| `diagnostico` | text (nullable) | Diagnóstico inicial |
| `trabajos_realizados` | text (nullable) | Trabajos realizados |
| `mano_obra` | decimal(10,2) | Costo de mano de obra |
| `subtotal` | decimal(10,2) | Subtotal (refacciones + mano obra) |
| `iva` | decimal(10,2) | IVA (16%) |
| `total` | decimal(10,2) | Total (subtotal + iva) |
| `fecha_entrada` | datetime | Fecha de entrada |
| `fecha_salida` | datetime (nullable) | Fecha de salida |
| `observaciones` | text (nullable) | Observaciones |
| `created_at` / `updated_at` | datetime | Timestamps |

### Tabla: `orden_refaccion` (tabla pivote)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint (PK) | ID autoincremental |
| `orden_trabajo_id` | bigint (FK) | Referencia a ordenes_trabajo |
| `refaccion_id` | bigint (FK) | Referencia a refacciones |
| `cantidad` | integer | Cantidad utilizada |
| `precio_unitario` | decimal(10,2) | Precio unitario al momento |
| `subtotal` | decimal(10,2) | Subtotal (cantidad × precio) |
| `created_at` / `updated_at` | datetime | Timestamps |

### Tabla: `personal_access_tokens`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint (PK) | ID autoincremental |
| `tokenable_type` | string | Tipo de modelo (User) |
| `tokenable_id` | bigint | ID del usuario |
| `name` | string | Nombre del token |
| `token` | string (unique) | Hash del token |
| `abilities` | text (nullable) | Habilidades del token |
| `last_used_at` | datetime (nullable) | Último uso |
| `expires_at` | datetime (nullable) | Fecha de expiración |
| `created_at` / `updated_at` | datetime | Timestamps |

---

## 5. 📊 Modelos y Relaciones

### User (app/Models/User.php)
- **Traits**: `HasApiTokens`, `HasFactory`, `Notifiable`
- **Interfaz**: `MustVerifyEmail` (verificación de email)
- **Campos fillables**: `name`, `email`, `password`, `role`
- **Castings**: `email_verified_at` → datetime, `password` → hashed
- **Relaciones**: Ninguna directa (es el modelo de usuarios)

### Cliente (app/Models/Cliente.php)
- **Traits**: `HasFactory`, `SoftDeletes`
- **Campos fillables**: `nombre`, `telefono`, `email`, `direccion`, `rfc`, `es_empresa`, `nombre_empresa`
- **Relaciones**:
  - `vehiculos()` → HasMany (un cliente tiene muchos vehículos)

### Vehiculo (app/Models/Vehiculo.php)
- **Traits**: `HasFactory`, `SoftDeletes`
- **Campos fillables**: `cliente_id`, `marca`, `modelo`, `anio`, `placa`, `color`, `vin`, `notas`
- **Relaciones**:
  - `cliente()` → BelongsTo (un vehículo pertenece a un cliente)
  - `ordenesTrabajo()` → HasMany (un vehículo tiene muchas órdenes)

### Refaccion (app/Models/Refaccion.php)
- **Traits**: `HasFactory`, `SoftDeletes`
- **Campos fillables**: `nombre`, `sku`, `descripcion`, `costo`, `precio_venta`, `stock_actual`, `stock_minimo`, `ubicacion`, `proveedor`, `activo`
- **Castings**: `costo` → decimal:2, `precio_venta` → decimal:2, `activo` → boolean
- **Relaciones**:
  - `ordenesTrabajo()` → BelongsToMany (muchas a muchas con ordenes_trabajo)
- **Métodos**:
  - `estaBajoStock()` → bool: Verifica si el stock actual ≤ stock mínimo

### OrdenTrabajo (app/Models/OrdenTrabajo.php)
- **Traits**: `HasFactory`
- **Campos fillables**: `vehiculo_id`, `mecanico_id`, `estado`, `diagnostico`, `trabajos_realizados`, `mano_obra`, `subtotal`, `iva`, `total`, `fecha_entrada`, `fecha_salida`, `observaciones`
- **Castings**: `mano_obra`, `subtotal`, `iva`, `total` → decimal:2; `fecha_entrada`, `fecha_salida` → datetime
- **Relaciones**:
  - `vehiculo()` → BelongsTo
  - `mecanico()` → BelongsTo (User, clave `mecanico_id`)
  - `refacciones()` → BelongsToMany (muchas a muchas con refacciones)
- **Métodos**:
  - `puedeAgregarRefacciones()` → bool: Verifica si el estado permite agregar refacciones
  - `estaFinalizada()` → bool: Verifica si el estado es `finalizado`

---

## 6. 🎮 Controladores y Funciones

### DashboardController (app/Http/Controllers/DashboardController.php)
- **`index()`**: Muestra el dashboard según el rol del usuario
  - **Admin**: Estadísticas de clientes, vehículos, órdenes, refacciones, órdenes pendientes/finalizadas, stock bajo, usuarios. Órdenes recientes y refacciones con stock bajo.
  - **Mecánico**: Órdenes asignadas, órdenes completadas, total de órdenes.

### ClienteController (app/Http/Controllers/ClienteController.php)
- **`index(Request)`**: Lista clientes con búsqueda y paginación
- **`create()`**: Muestra formulario de creación
- **`store(Request)`**: Valida y crea un nuevo cliente
- **`show(Cliente)`**: Muestra detalles del cliente con sus vehículos y órdenes
- **`edit(Cliente)`**: Muestra formulario de edición
- **`update(Request, Cliente)`**: Valida y actualiza el cliente
- **`destroy(Cliente)`**: Elimina el cliente (soft delete)

### VehiculoController (app/Http/Controllers/VehiculoController.php)
- **`index(Request)`**: Lista vehículos con búsqueda y paginación
- **`create()`**: Muestra formulario con lista de clientes
- **`store(Request)`**: Valida y crea un nuevo vehículo
- **`show(Vehiculo)`**: Muestra detalles con cliente, órdenes y refacciones
- **`edit(Vehiculo)`**: Muestra formulario de edición con lista de clientes
- **`update(Request, Vehiculo)`**: Valida y actualiza el vehículo
- **`destroy(Vehiculo)`**: Elimina el vehículo (soft delete)

### RefaccionController (app/Http/Controllers/RefaccionController.php)
- **`index(Request)`**: Lista refacciones activas con búsqueda y paginación
- **`create()`**: Muestra formulario de creación
- **`store(Request)`**: Valida y crea una nueva refacción (usa InventoryService)
- **`show(Refaccion)`**: Muestra detalles de la refacción
- **`edit(Refaccion)`**: Muestra formulario de edición
- **`update(Request, Refaccion)`**: Valida y actualiza la refacción
- **`destroy(Refaccion)`**: Elimina la refacción (soft delete)
- **`stock(Refaccion)`**: Muestra formulario para actualizar stock
- **`actualizarStock(Request, Refaccion)`**: Actualiza el stock (usa InventoryService)
- **`stockBajo()`**: Lista refacciones con stock bajo

### OrdenTrabajoController (app/Http/Controllers/OrdenTrabajoController.php)
- **`index(Request)`**: Lista órdenes con búsqueda y paginación
- **`create()`**: Muestra formulario con vehículos y mecánicos
- **`store(Request)`**: Crea una nueva orden (usa WorkOrderService)
- **`show(OrdenTrabajo)`**: Muestra detalles con vehículo, mecánico, refacciones y totales
- **`edit(OrdenTrabajo)`**: Muestra formulario de edición con mecánicos
- **`update(Request, OrdenTrabajo)`**: Actualiza estado, mecánico, diagnóstico, trabajos, mano de obra (usa WorkOrderService)
- **`agregarRefaccion(Request, OrdenTrabajo)`**: Agrega refacción a la orden (usa WorkOrderService)

### ProfileController (app/Http/Controllers/ProfileController.php)
- **`edit(Request)`**: Muestra formulario de perfil
- **`update(ProfileUpdateRequest)`**: Actualiza información del perfil
- **`destroy(Request)`**: Elimina la cuenta del usuario

### Controladores de Autenticación (app/Http/Controllers/Auth/)
- **AuthenticatedSessionController**: Login, logout
- **RegisteredUserController**: Registro (rol: mecanico por defecto)
- **VerificationController**: Verificación de email (show, verify, resend)
- **ForgotPasswordController**: Recuperación de contraseña
- **PasswordController**: Actualización de contraseña

### Controladores API (app/Http/Controllers/Api/)
- **ClienteController**: API RESTful de clientes
- **VehiculoController**: API RESTful de vehículos + `indexByCliente`
- **OrdenTrabajoController**: API RESTful de órdenes + `agregarRefaccion`, `calcularTotales`
- **RefaccionController**: API RESTful de refacciones + `actualizarStock`, `stockBajo`

---

## 7. ⚙️ Servicios y Patrones de Diseño

### InventoryService (app/Services/InventoryService.php)
Servicio de gestión de inventario de refacciones.
- **`crearRefaccion(array)`**: Crea una nueva refacción
- **`actualizarStock(Refaccion, int)`**: Actualiza el stock y dispara evento si está bajo
- **`incrementarStock(Refaccion, int)`**: Incrementa el stock
- **`decrementarStock(Refaccion, int)`**: Decrementa el stock (lanza excepción si no hay suficiente)
- **`obtenerRefaccionesBajoStock()`**: Obtiene refacciones con stock bajo
- **`obtenerHistorialMovimientos(Refaccion)`**: Historial de movimientos (implementación futura)

### WorkOrderService (app/Services/WorkOrderService.php)
Servicio de gestión de órdenes de trabajo con **State Pattern**.
- **`getState(string)`**: Obtiene el estado actual de la orden
- **`crearOrden(array)`**: Crea una nueva orden en estado `diagnóstico`
- **`actualizarEstado(OrdenTrabajo, string)`**: Cambia el estado de la orden (valida transiciones)
- **`agregarRefaccion(OrdenTrabajo, Refaccion, int)`**: Agrega refacción a la orden (valida stock y estado)
- **`calcularTotales(OrdenTrabajo)`**: Calcula subtotal, IVA y total
- **`recalcularTotales(OrdenTrabajo)`**: Recalcula y guarda los totales
- **`obtenerTransicionesValidas(string)`**: Define las transiciones válidas entre estados

#### State Pattern - Transiciones de Estados
```
diagnóstico → esperando_piezas, reparación, finalizado
esperando_piezas → reparación, finalizado
reparación → finalizado
finalizado → (no hay transiciones)
```

### PaymentService (app/Services/PaymentService.php)
Servicio de procesamiento de pagos con **Adapter Pattern**.
- **`procesarPago(OrdenTrabajo, array)`**: Procesa un pago (marca la orden como finalizada)
- **`reembolsarPago(OrdenTrabajo)`**: Procesa un reembolso

### StripePaymentAdapter (app/Services/Adapters/StripePaymentAdapter.php)
Adaptador para Stripe (implementación simulada para desarrollo).
- **`procesarPago(float, array)`**: Procesa un pago (simulado)
- **`reembolsar($orden)`**: Procesa un reembolso (simulado)
- **`getPublicKey()`**: Devuelve la clave pública de Stripe

### Strategy Pattern - Estrategias de Precios
- **PricingStrategyInterface**: Interfaz común
- **PublicoGeneralStrategy**: Precio para público general
- **ClientePremiumStrategy**: Precio para clientes premium
- **FlotillaStrategy**: Precio para flotas de vehículos

### CotizacionBuilder (app/Services/Builders/CotizacionBuilder.php)
Constructor de cotizaciones (Builder Pattern).

---

## 8. 🛣️ Rutas

### Rutas Web (routes/web.php)
| Método | Ruta | Controlador | Middleware | Descripción |
|--------|------|-------------|------------|-------------|
| GET | `/` | DashboardController | `auth` | Dashboard (admin/mecánico) |
| GET | `/profile` | ProfileController | `auth` | Editar perfil |
| PUT/PATCH | `/profile` | ProfileController | `auth` | Actualizar perfil |
| DELETE | `/profile` | ProfileController | `auth` | Eliminar cuenta |
| GET | `/clientes` | ClienteController | `auth, role:admin` | Listar clientes |
| GET | `/clientes/create` | ClienteController | `auth, role:admin` | Formulario crear |
| POST | `/clientes` | ClienteController | `auth, role:admin` | Crear cliente |
| GET | `/clientes/{cliente}` | ClienteController | `auth, role:admin` | Ver cliente |
| GET | `/clientes/{cliente}/edit` | ClienteController | `auth, role:admin` | Formulario editar |
| PUT/PATCH | `/clientes/{cliente}` | ClienteController | `auth, role:admin` | Actualizar cliente |
| DELETE | `/clientes/{cliente}` | ClienteController | `auth, role:admin` | Eliminar cliente |
| GET | `/vehiculos` | VehiculoController | `auth, role:admin,mecanico` | Listar vehículos |
| GET | `/vehiculos/create` | VehiculoController | `auth, role:admin,mecanico` | Formulario crear |
| POST | `/vehiculos` | VehiculoController | `auth, role:admin,mecanico` | Crear vehículo |
| GET | `/vehiculos/{vehiculo}` | VehiculoController | `auth, role:admin,mecanico` | Ver vehículo |
| GET | `/vehiculos/{vehiculo}/edit` | VehiculoController | `auth, role:admin,mecanico` | Formulario editar |
| PUT/PATCH | `/vehiculos/{vehiculo}` | VehiculoController | `auth, role:admin,mecanico` | Actualizar vehículo |
| DELETE | `/vehiculos/{vehiculo}` | VehiculoController | `auth, role:admin,mecanico` | Eliminar vehículo |
| GET | `/refacciones` | RefaccionController | `auth, role:admin` | Listar refacciones |
| GET | `/refacciones/create` | RefaccionController | `auth, role:admin` | Formulario crear |
| POST | `/refacciones` | RefaccionController | `auth, role:admin` | Crear refacción |
| GET | `/refacciones/{refaccion}` | RefaccionController | `auth, role:admin` | Ver refacción |
| GET | `/refacciones/{refaccion}/edit` | RefaccionController | `auth, role:admin` | Formulario editar |
| PUT/PATCH | `/refacciones/{refaccion}` | RefaccionController | `auth, role:admin` | Actualizar refacción |
| DELETE | `/refacciones/{refaccion}` | RefaccionController | `auth, role:admin` | Eliminar refacción |
| GET | `/refacciones/stock-bajo` | RefaccionController | `auth, role:admin` | Refacciones con stock bajo |
| GET | `/refacciones/{refaccion}/stock` | RefaccionController | `auth, role:admin` | Formulario stock |
| PUT | `/refacciones/{refaccion}/stock` | RefaccionController | `auth, role:admin` | Actualizar stock |
| GET | `/ordenes` | OrdenTrabajoController | `auth, role:admin,mecanico` | Listar órdenes |
| GET | `/ordenes/create` | OrdenTrabajoController | `auth, role:admin,mecanico` | Formulario crear |
| POST | `/ordenes` | OrdenTrabajoController | `auth, role:admin,mecanico` | Crear orden |
| GET | `/ordenes/{ordenTrabajo}` | OrdenTrabajoController | `auth, role:admin,mecanico` | Ver orden |
| GET | `/ordenes/{ordenTrabajo}/edit` | OrdenTrabajoController | `auth, role:admin,mecanico` | Formulario editar |
| PUT/PATCH | `/ordenes/{ordenTrabajo}` | OrdenTrabajoController | `auth, role:admin,mecanico` | Actualizar orden |
| DELETE | `/ordenes/{ordenTrabajo}` | OrdenTrabajoController | `auth, role:admin,mecanico` | Eliminar orden |
| POST | `/ordenes/{ordenTrabajo}/refacciones` | OrdenTrabajoController | `auth, role:admin,mecanico` | Agregar refacción |

### Rutas de Autenticación (routes/auth.php)
| Método | Ruta | Controlador | Middleware | Descripción |
|--------|------|-------------|------------|-------------|
| GET | `/register` | RegisteredUserController | `guest` | Formulario registro |
| POST | `/register` | RegisteredUserController | `guest` | Registrar usuario (rol: mecanico) |
| GET | `/login` | AuthenticatedSessionController | `guest` | Formulario login |
| POST | `/login` | AuthenticatedSessionController | `guest` | Iniciar sesión |
| GET | `/forgot-password` | ForgotPasswordController | `guest` | Formulario recuperar |
| POST | `/forgot-password` | ForgotPasswordController | `guest` | Enviar enlace recuperación |
| GET | `/reset-password/{token}` | ForgotPasswordController | `guest` | Formulario reset |
| POST | `/reset-password` | ForgotPasswordController | `guest` | Resetear contraseña |
| PUT | `/password` | PasswordController | `auth` | Actualizar contraseña |
| POST | `/logout` | AuthenticatedSessionController | `auth` | Cerrar sesión |
| GET | `/email/verify` | VerificationController | `auth` | Notificación verificación |
| GET | `/email/verify/{id}/{hash}` | VerificationController | `auth, signed` | Verificar email |
| POST | `/email/resend` | VerificationController | `auth` | Reenviar verificación |
| POST | `/email/verify/send` | VerificationController | `auth` | Alias de reenvío |

### Rutas API (routes/api.php)
| Método | Ruta | Controlador | Middleware | Descripción |
|--------|------|-------------|------------|-------------|
| POST | `/api/login` | (closure) | - | Login API (obtiene token) |
| GET | `/api/user` | (closure) | `auth:sanctum, throttle` | Usuario autenticado |
| GET | `/api/clientes` | ClienteController | `auth:sanctum, throttle` | Listar clientes |
| POST | `/api/clientes` | ClienteController | `auth:sanctum, throttle` | Crear cliente |
| GET | `/api/clientes/{cliente}` | ClienteController | `auth:sanctum, throttle` | Ver cliente |
| PUT | `/api/clientes/{cliente}` | ClienteController | `auth:sanctum, throttle` | Actualizar |
| DELETE | `/api/clientes/{cliente}` | ClienteController | `auth:sanctum, throttle` | Eliminar |
| GET | `/api/vehiculos` | VehiculoController | `auth:sanctum, throttle` | Listar vehículos |
| POST | `/api/vehiculos` | VehiculoController | `auth:sanctum, throttle` | Crear vehículo |
| GET | `/api/vehiculos/{vehiculo}` | VehiculoController | `auth:sanctum, throttle` | Ver vehículo |
| PUT | `/api/vehiculos/{vehiculo}` | VehiculoController | `auth:sanctum, throttle` | Actualizar |
| DELETE | `/api/vehiculos/{vehiculo}` | VehiculoController | `auth:sanctum, throttle` | Eliminar |
| GET | `/api/vehiculos/cliente/{cliente}` | VehiculoController | `auth:sanctum, throttle` | Vehículos por cliente |
| GET | `/api/ordenes-trabajo` | OrdenTrabajoController | `auth:sanctum, throttle` | Listar órdenes |
| POST | `/api/ordenes-trabajo` | OrdenTrabajoController | `auth:sanctum, throttle` | Crear orden |
| GET | `/api/ordenes-trabajo/{ordenTrabajo}` | OrdenTrabajoController | `auth:sanctum, throttle` | Ver orden |
| PUT | `/api/ordenes-trabajo/{ordenTrabajo}` | OrdenTrabajoController | `auth:sanctum, throttle` | Actualizar |
| DELETE | `/api/ordenes-trabajo/{ordenTrabajo}` | OrdenTrabajoController | `auth:sanctum, throttle` | Eliminar |
| POST | `/api/ordenes-trabajo/{ordenTrabajo}/refacciones` | OrdenTrabajoController | `auth:sanctum, throttle` | Agregar refacción |
| GET | `/api/ordenes-trabajo/{ordenTrabajo}/totales` | OrdenTrabajoController | `auth:sanctum, throttle` | Calcular totales |
| GET | `/api/refacciones` | RefaccionController | `auth:sanctum, throttle` | Listar refacciones |
| POST | `/api/refacciones` | RefaccionController | `auth:sanctum, throttle` | Crear refacción |
| GET | `/api/refacciones/{refaccion}` | RefaccionController | `auth:sanctum, throttle` | Ver refacción |
| PUT | `/api/refacciones/{refaccion}` | RefaccionController | `auth:sanctum, throttle` | Actualizar |
| DELETE | `/api/refacciones/{refaccion}` | RefaccionController | `auth:sanctum, throttle` | Eliminar |
| PUT | `/api/refacciones/{refaccion}/stock` | RefaccionController | `auth:sanctum, throttle` | Actualizar stock |
| GET | `/api/refacciones/stock-bajo` | RefaccionController | `auth:sanctum, throttle` | Stock bajo |

---

## 9. 👁️ Vistas y Componentes

### Layouts
- **`layouts/app.blade.php`**: Layout principal con navegación, header y scripts
- **`layouts/guest.blade.php`**: Layout para páginas de autenticación (login, register)
- **`layouts/navigation.blade.php`**: Barra de navegación con menú responsive (Alpine.js)

### Componentes Blade
- **`components/app-layout.blade.php`**: Layout con navegación y contenido
- **`components/guest-layout.blade.php`**: Layout para invitados
- **`components/application-logo.blade.php`**: Logo de la aplicación
- **`components/nav-link.blade.php`**: Enlace de navegación
- **`components/responsive-nav-link.blade.php`**: Enlace responsive
- **`components/dropdown.blade.php`**: Menú desplegable
- **`components/dropdown-link.blade.php`**: Enlace en dropdown
- **`components/primary-button.blade.php`**: Botón primario
- **`components/secondary-button.blade.php`**: Botón secundario
- **`components/danger-button.blade.php`**: Botón de peligro (rojo)
- **`components/text-input.blade.php`**: Campo de texto
- **`components/input-label.blade.php`**: Etiqueta de input
- **`components/input-error.blade.php`**: Mensaje de error
- **`components/modal.blade.php`**: Modal de diálogo
- **`components/action-message.blade.php`**: Mensaje de acción
- **`components/auth-session-status.blade.php`**: Estado de sesión
- **`components/profile/`**: Componentes de perfil (update-profile, update-password, delete-user)

### Dashboards
- **`dashboard/admin.blade.php`**: Dashboard de administrador con estadísticas, órdenes recientes y stock bajo
- **`dashboard/mecanico.blade.php`**: Dashboard de mecánico con órdenes asignadas

### Vistas de Recursos
- **`clientes/`**: index, create, edit, show
- **`vehiculos/`**: index, create, edit, show
- **`refacciones/`**: index, create, edit, show, stock, stock-bajo
- **`ordenes/`**: index, create, edit, show

### Vistas de Autenticación
- **`auth/login.blade.php`**: Formulario de login
- **`auth/register.blade.php`**: Formulario de registro
- **`auth/forgot-password.blade.php`**: Recuperación de contraseña
- **`auth/verify-email.blade.php`**: Verificación de email

---

## 10. 🔐 Middleware

### RoleMiddleware (app/Http/Middleware/RoleMiddleware.php)
Middleware de autorización basado en roles.
- **Parámetros**: `...$roles` (variadic - permite múltiples roles)
- **Funcionamiento**:
  1. Verifica si el usuario está autenticado (redirige a login si no)
  2. Verifica si el rol del usuario está en la lista de roles permitidos
  3. Si no tiene permiso, aborta con 403
- **Uso**: `role:admin`, `role:admin,mecanico`

### Middleware Registrados (bootstrap/app.php)
- `auth`: Autenticación de usuarios
- `role`: Autorización basada en roles
- `verified`: Verificación de email (desactivado en rutas web)
- `guest`: Acceso solo para invitados
- `signed`: URLs firmadas
- `throttle`: Rate limiting (API: 60 req/min)

---

## 11. 🔑 Autenticación y Autorización

### Sistema de Autenticación
- **Login**: Email + contraseña
- **Registro**: Solo mecánicos (rol asignado automáticamente)
- **Verificación de Email**: Desactivada temporalmente (el usuario solicitó dejarla sin terminar)
- **Recuperación de Contraseña**: Soporte completo (forgot-password, reset)
- **Sesión**: Driver de base de datos, 120 minutos de vida

### Sistema de Autorización
- **Roles**: `admin` (acceso completo), `mecanico` (acceso limitado)
- **Control de Acceso**:
  - **Admin**: Clientes, vehículos, refacciones, órdenes, dashboard completo
  - **Mecánico**: Vehículos, órdenes, dashboard propio, perfil
- **Middleware**: `role:admin` y `role:admin,mecanico`

### API Authentication (Sanctum)
- **Login**: POST `/api/login` con email + password → obtiene token
- **Token**: Bearer token en header `Authorization`
- **Rate Limiting**: 60 requests por minuto
- **Token Format**: `{id}|{plainTextToken}`

---

## 12. 🌐 API RESTful

### Endpoints Públicos
- **POST `/api/login`**: Autenticación y obtención de token

### Endpoints Protegidos (auth:sanctum)
- **GET `/api/user`**: Información del usuario autenticado
- **Recursos**: clientes, vehiculos, ordenes-trabajo, refacciones
- **Funcionalidades Especiales**:
  - `GET /api/vehiculos/cliente/{cliente}`: Vehículos por cliente
  - `POST /api/ordenes-trabajo/{ordenTrabajo}/refacciones`: Agregar refacción
  - `GET /api/ordenes-trabajo/{ordenTrabajo}/totales`: Calcular totales
  - `PUT /api/refacciones/{refaccion}/stock`: Actualizar stock
  - `GET /api/refacciones/stock-bajo`: Refacciones con stock bajo

### Formato de Respuesta
```json
// Login
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@taller.com",
    "role": "admin"
  }
}

// Error de validación
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

---

## 13. ⚙️ Configuración

### Archivo: `.env`
| Variable | Valor | Descripción |
|----------|-------|-------------|
| `APP_NAME` | "Vulcanizadora Don Chuy" | Nombre de la aplicación |
| `APP_ENV` | local/production | Entorno |
| `APP_KEY` | base64:... | Clave de encriptación |
| `APP_DEBUG` | true/false | Modo debug |
| `APP_URL` | http://localhost | URL base |
| `DB_CONNECTION` | sqlite/mysql | Conexión BD |
| `DB_DATABASE` | database/database.sqlite | Base de datos |
| `SESSION_DRIVER` | database | Driver de sesiones |
| `CACHE_STORE` | database | Driver de caché |
| `IVA_RATE` | 0.16 | Tasa de IVA (16% México) |
| `PAGINATION_PER_PAGE` | 15 | Elementos por página |
| `STRIPE_KEY` / `STRIPE_SECRET` | - | Configuración Stripe |

### Archivo: `config/taller.php`
Configuración específica del taller:
- **IVA**: Tasa de impuesto (16%)
- **Paginación**: Elementos por página
- **Pagos**: Moneda, Stripe key/secret
- **Backup**: Disco y ruta

---

## 14. 🚀 Cómo Ejecutar el Proyecto

### Requisitos
- PHP 8.3+
- Composer 2.x
- Node.js 20+
- npm 10+
- SQLite 3 (o MySQL)

### Instalación
```bash
# 1. Clonar el repositorio
git clone https://github.com/EmbargosCoppel/VulkanikApp.git
cd taller

# 2. Instalar dependencias de PHP
composer install

# 3. Instalar dependencias de Node.js
npm install

# 4. Configurar el entorno
cp .env.example .env
php artisan key:generate

# 5. Configurar la base de datos (SQLite por defecto)
# Editar .env: DB_CONNECTION=sqlite, DB_DATABASE=database/database.sqlite

# 6. Ejecutar migraciones y seeders
php artisan migrate:fresh --seed

# 7. Iniciar servidores de desarrollo
npm run start
# O por separado:
# php artisan serve
# npm run dev
```

### Credenciales de Prueba
- **Admin**: `admin@taller.com` / `password`
- **Mecánico**: `mecanico@taller.com` / `password`

### Comandos Útiles
```bash
# Ejecutar migraciones
php artisan migrate

# Resetear y sembrar base de datos
php artisan migrate:fresh --seed

# Iniciar servidor de desarrollo
php artisan serve
npm run dev

# Ejecutar ambos servidores
npm run start

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ejecutar tests
php artisan test
```

---

## 15. 🐛 Problemas Conocidos y Soluciones

### 1. Error 419 (CSRF Token Mismatch)
**Causa**: El token CSRF de la página de login no es válido después de iniciar sesión.
**Solución**: Obtener un token CSRF fresco de una página autenticada antes de hacer POST.

### 2. Error 500 en /profile (RouteNotFoundException)
**Causa**: La vista `update-profile-information-form.blade.php` hacía referencia a `route('verification.send')` que no existía.
**Solución**: Se agregó la ruta `verification.send` en `routes/auth.php` y se corrigió la vista para usar un formulario en lugar de un enlace.

### 3. Redirección a /email/verify en el dashboard
**Causa**: El middleware `verified` redirigía a usuarios sin email verificado.
**Solución**: Se eliminó el middleware `verified` de la ruta del dashboard. Los usuarios registrados ahora tienen `email_verified_at` asignado automáticamente.

### 4. RoleMiddleware no reconocía múltiples roles
**Causa**: El middleware usaba `string $role` como parámetro, pero Laravel pasa múltiples parámetros separados por coma como argumentos individuales.
**Solución**: Se cambió a `...$roles` (parámetro variádico) para recibir todos los roles.

### 5. Vite no sirve assets en producción
**Causa**: En producción, Vite necesita compilar los assets.
**Solución**: Ejecutar `npm run build` antes de desplegar.

---

## 16. ✅ Resultados de Pruebas

### Pruebas de Apariencia (Frontend)
- ✅ Vite sirve assets CSS y JS correctamente
- ✅ Tailwind CSS aplicado (clases bg-gray, rounded, shadow, etc.)
- ✅ Font Awesome iconografía cargada
- ✅ Alpine.js interactividad funcional (menú responsive)
- ✅ Diseño responsive (mobile-friendly)

### Pruebas de Funcionalidades (Frontend)
- ✅ Login funciona correctamente
- ✅ Registro de usuarios (rol: mecanico)
- ✅ Dashboard admin y mecánico
- ✅ CRUD de clientes (admin)
- ✅ CRUD de vehículos (admin + mecánico)
- ✅ CRUD de refacciones (admin)
- ✅ CRUD de órdenes de trabajo (admin + mecánico)
- ✅ Gestión de stock de refacciones
- ✅ Perfil de usuario
- ✅ Logout

### Pruebas de Funcionalidades (Backend)
- ✅ API login (POST /api/login)
- ✅ API autenticación con Sanctum
- ✅ API listar recursos (clientes, vehículos, refacciones, órdenes)
- ✅ API crear recursos
- ✅ API rate limiting (60 req/min)
- ✅ Validaciones de formulario
- ✅ Soft deletes funcionando
- ✅ Cálculo automático de totales e IVA
- ✅ State Pattern para órdenes
- ✅ InventoryService para stock

### Pruebas de Autorización
- ✅ Admin: acceso completo (200 en todas las rutas)
- ✅ Mecánico: acceso a vehículos y órdenes (200)
- ✅ Mecánico: bloqueado de clientes y refacciones (403)
- ✅ Usuario no autenticado: redirigido a login (302)

### Pruebas de Base de Datos
- ✅ Migraciones ejecutan correctamente
- ✅ Seeders crean datos de prueba
- ✅ Relaciones funcionan (cliente→vehículos→órdenes→refacciones)
- ✅ Soft deletes funcionan
- ✅ Validaciones de unicidad (placa, SKU, email)

---

## 17. 📦 Preparación para Despliegue en Hosting

### Pasos para Producción
1. **Compilar assets frontend**:
   ```bash
   npm run build
   ```

2. **Configurar `.env` para producción**:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://tudominio.com
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nombre_db
   DB_USERNAME=usuario
   DB_PASSWORD=contraseña
   ```

3. **Optimizar para producción**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan storage:link
   ```

4. **Ejecutar migraciones**:
   ```bash
   php artisan migrate --force
   ```

### Notas para Hosting Compartido
- Asegurar permisos en `storage/` y `bootstrap/cache/` (755 o 775)
- Configurar `public/` como directorio raíz o usar `.htaccess`
- Verificar que `mod_rewrite` esté habilitado en Apache
- Configurar `APP_KEY` con `php artisan key:generate`

---

## 18. 📝 Eventos y Listeners

### Eventos
- **`App\Events\StockBajo`**: Disparado cuando una refacción tiene stock bajo
  - Parámetros: `Refaccion $refaccion`, `int $stockActual`, `int $stockMinimo`

### Listeners
- (Por implementar) Notificación de stock bajo por email

---

## 19. 🧪 Tests

### Archivos de Test
- `tests/Feature/` - Tests de funcionalidades
- `tests/Unit/` - Tests unitarios
- `tests/TestCase.php` - Caso base de prueba

### Ejecutar Tests
```bash
php artisan test
```

---

## 20. 📄 Archivos de Configuración Adicionales

### `composer.json`
- Laravel 11, Sanctum, Breeze, PHPUnit, Pint, PHPStan

### `package.json`
- Vite 8, Tailwind CSS 3, Alpine.js 3, Laravel Vite Plugin

### `vite.config.js`
- Configuración de Vite con Laravel plugin
- Entradas: `resources/css/app.css`, `resources/js/app.js`

### `tailwind.config.js`
- Content paths configurados
- Fuente: Figtree
- Plugin: @tailwindcss/forms

### `phpstan.neon`
- Análisis estático de código

### `phpunit.xml`
- Configuración de tests

---

## 📞 Soporte

Para soporte técnico o consultas sobre el proyecto, contactar al equipo de desarrollo.

---

*Esta guía fue generada como parte de la verificación completa del proyecto. Última actualización: 27 de julio de 2026.*

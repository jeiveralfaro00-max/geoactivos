# 📊 ANÁLISIS COMPLETO DEL PROYECTO GEOACTIVOS

**Fecha de Análisis:** 3 de Marzo de 2026  
**Versión:** 1.0  
**Tipo de Proyecto:** Aplicación Web Multi-Tenant de Gestión de Activos Fijos

---

## 📋 TABLA DE CONTENIDO

1. [Descripción General](#descripción-general)
2. [Arquitectura del Proyecto](#arquitectura-del-proyecto)
3. [Estructura de Directorios](#estructura-de-directorios)
4. [Base de Datos](#base-de-datos)
5. [Módulos Principales](#módulos-principales)
6. [Componentes Clave](#componentes-clave)
7. [Sistema de Autenticación](#sistema-de-autenticación)
8. [APIs AJAX](#apis-ajax)
9. [Flujos de Procesos](#flujos-de-procesos)
10. [Tecnologías Utilizadas](#tecnologías-utilizadas)
11. [Consideraciones de Seguridad](#consideraciones-de-seguridad)

---

## 📌 Descripción General

### ¿Qué es GeoActivos?

**GeoActivos** es una plataforma web de gestión integral de activos fijos diseñada para empresas que necesitan:

- ✅ Registrar y mantener un inventario detallado de activos (equipos, maquinaria, etc.)
- ✅ Programar y registrar mantenimientos preventivos y correctivos
- ✅ Gestionar calibraciones técnicas de equipos
- ✅ Control multi-tenant (múltiples clientes en una sola aplicación)
- ✅ Auditoría completa de movimientos y cambios
- ✅ Generación de reportes y certificados
- ✅ Control de acceso basado en roles y permisos

### Características Principales

| Feature | Descripción |
|---------|-----------|
| **Multi-Tenant** | Soporta múltiples clientes/empresas en una sola instancia |
| **RBAC** | Control de acceso basado en roles (Role-Based Access Control) |
| **Auditoría** | Registro completo de acciones de usuarios |
| **Calibraciones** | Gestión de calibración de equipos con certificados |
| **Mantenimiento** | Registro preventivo y correctivo con costos |
| **Archivos** | Carga de documentos y adjuntos en procesos |
| **QR** | Generación de códigos QR para etiquetado de activos |
| **Reportes** | Hojas de vida imprimibles y reportes |

---

## 🏗️ Arquitectura del Proyecto

### Patrón Arquitectónico: MVC Moderno

La aplicación sigue un patrón **MVC (Model-View-Controller)** simplificado sin framework:

```
REQUEST
   ↓
public/index.php (Router Central)
   ↓
├─→ Auth (Verificación de sesión)
├─→ Permisos (RBAC)
└─→ Views (Vistas PHP)
   ↓
app/core/ (Controladores lógicos)
   ├─ Auth.php
   └─ Helpers.php
   ↓
app/config/ (Base de datos)
   ├─ config.php (Configuración)
   └─ db.php (Conexión PDO)
   ↓
DATABASE (MySQL/MariaDB)
```

### Stack Tecnológico

| Capa | Tecnología |
|-----|-----------|
| **Frontend** | HTML5 + CSS3 (AdminLTE 3) + JavaScript (jQuery) |
| **Backend** | PHP 7.4+ (Procedural + POO) |
| **Base de Datos** | MySQL/MariaDB (utf8mb4) |
| **Framework CSS** | AdminLTE 3.2 (Bootstrap 4) |
| **Icons** | FontAwesome 5.15.4 |
| **Hosting** | XAMPP Local / Hosting compartido |

---

## 📁 Estructura de Directorios

### Vista General

```
geoactivos/
├── public/                      # Punto de entrada público
│   ├── index.php               # Router principal
│   ├── hash.php                # Generador de hashes para contraseñas
│   ├── seed_admin.php          # Datos iniciales
│   ├── assets/                 # Recursos estáticos
│   │   ├── css/                # Estilos personalizados
│   │   └── js/                 # Scripts del cliente
│   └── uploads/                # Archivos subidos por usuarios
│       ├── activos/
│       ├── firmas/
│       └── mantenimientos/
│
├── app/                        # Lógica de la aplicación
│   ├── config/                 # Configuración
│   │   ├── config.php          # Parámetros generales
│   │   └── db.php              # Conexión a BD
│   │
│   ├── core/                   # Componentes centrales
│   │   ├── Auth.php            # Autenticación y autorización
│   │   └── Helpers.php         # Funciones útiles
│   │
│   ├── views/                  # Vistas (Presentación)
│   │   ├── layout/             # Plantilla maestra (header, sidebar, footer)
│   │   ├── activos/            # Módulo de activos
│   │   ├── calibraciones/      # Módulo de calibraciones
│   │   ├── mantenimientos/     # Módulo de mantenimiento
│   │   ├── auditoria/          # Módulo de auditoría
│   │   ├── usuarios/           # Gestión de usuarios
│   │   ├── roles/              # Gestión de roles
│   │   ├── empresas/           # Gestión de tenants
│   │   ├── patrones/           # Patrones de medida
│   │   ├── componentes/        # Componentes de activos
│   │   ├── config/             # Configuración de datos maestros
│   │   ├── dashboard/          # Panel principal
│   │   └── auth/               # Paágina de login
│   │
│   ├── ajax/                   # Endpoints AJAX (API interna)
│   │   ├── act_*.php           # Activos
│   │   ├── mant_*.php          # Mantenimientos
│   │   ├── ajax_cal_*.php      # Calibraciones
│   │   ├── ajax_patron_*.php   # Patrones
│   │   └── ...
│   │
│   └── storage/                # Almacenamiento de la app
│       └── firmas/             # Firmas digitales
│
├── database/                   # Scripts de base de datos
│   ├── geoactivos_schema.sql  # Esquema completo
│   └── geoactivos_clean.sql   # Base de datos limpia
│
└── README.txt                 # Instrucciones de instalación
```

---

## 🔗 Base de Datos

### Modelo Multi-Tenant

La BD está diseñada para soportar múltiples clientes (tenants):

```sql
tenants (id, nombre, nit, email, estado)
  ├── usuarios (id, tenant_id, rol_id, ...)
  ├── roles (id, tenant_id, nombre, ...)
  ├── activos (id, tenant_id, categoria_id, ...)
  ├── mantenimientos (id, tenant_id, activo_id, ...)
  ├── calibraciones (id, tenant_id, activo_id, ...)
  ├── categorias_activo (id, tenant_id, nombre)
  ├── marcas (id, tenant_id, nombre)
  ├── sedes (id, tenant_id, nombre)
  ├── areas (id, tenant_id, sede_id, nombre)
  ├── proveedores (id, tenant_id, nombre, ...)
  └── tipos_activo (id, tenant_id, ...)
```

### Tablas Principales

#### 1. **TENANTS** - Clientes/Empresas
```sql
CREATE TABLE tenants (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(150) NOT NULL,
  nit VARCHAR(30),
  email VARCHAR(120),
  estado ENUM('ACTIVO','SUSPENDIDO') DEFAULT 'ACTIVO',
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);
```
**Propósito:** Definir clientes en el sistema multi-tenant

---

#### 2. **USUARIOS & ROLES** - Autenticación
```sql
CREATE TABLE roles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  tenant_id INT NOT NULL,
  nombre VARCHAR(50) NOT NULL,
  es_superadmin TINYINT DEFAULT 0
);

CREATE TABLE usuarios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  tenant_id INT NOT NULL,
  rol_id INT NOT NULL,
  nombre VARCHAR(120),
  email VARCHAR(120) UNIQUE,
  pass_hash VARCHAR(255),
  estado ENUM('ACTIVO','INACTIVO') DEFAULT 'ACTIVO'
);
```
**Propósito:** Sistema de acceso con control de roles

---

#### 3. **ACTIVOS** - Inventario
```sql
CREATE TABLE activos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  tenant_id INT NOT NULL,
  categoria_id INT NOT NULL,
  codigo_interno VARCHAR(50) UNIQUE,
  nombre VARCHAR(150),
  modelo VARCHAR(120),
  serial VARCHAR(120),
  marca_id INT,
  area_id INT,
  estado ENUM('ACTIVO','EN_MANTENIMIENTO','BAJA'),
  fecha_compra DATE,
  fecha_instalacion DATE,
  garantia_hasta DATE
);
```
**Propósito:** Registro maestro de todos los activos

---

#### 4. **MANTENIMIENTOS** - Registros de Servicio
```sql
CREATE TABLE mantenimientos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  tenant_id INT NOT NULL,
  activo_id INT NOT NULL,
  tipo ENUM('PREVENTIVO','CORRECTIVO','PREDICTIVO'),
  estado ENUM('PROGRAMADO','EN_PROCESO','CERRADO','ANULADO'),
  fecha_programada DATE,
  fecha_inicio DATETIME,
  fecha_fin DATETIME,
  costo_mano_obra DECIMAL(12,2),
  costo_repuestos DECIMAL(12,2)
);
```
**Propósito:** Historial completo de mantenimientos

---

#### 5. **CALIBRACIONES** - Verificación Técnica
```sql
CREATE TABLE calibraciones (
  id INT PRIMARY KEY AUTO_INCREMENT,
  tenant_id INT NOT NULL,
  activo_id INT NOT NULL,
  fecha_calibracion DATE,
  fecha_proxima DATE,
  estado ENUM('CONFORME','NO_CONFORME','EN_CALIBRACION'),
  certificado_numero VARCHAR(100)
);
```
**Propósito:** Control de calibraciones de equipos

---

#### 6. **AUDITORÍA** - Trazabilidad
```sql
CREATE TABLE audit_log (
  id INT PRIMARY KEY AUTO_INCREMENT,
  tenant_id INT NOT NULL,
  usuario_id INT NOT NULL,
  tabla VARCHAR(80),
  accion VARCHAR(20),
  registro_id INT,
  cambios JSON,
  creado_en DATETIME
);
```
**Propósito:** Registro de todos los cambios en el sistema

---

### Relaciones Clave

```
tenant_id ← La mayoría de tablas tienen esta FK
├─ usuarios (rol_id → roles)
├─ activos (categoria_id, marca_id, area_id, proveedor_id)
├─ mantenimientos (activo_id)
├─ calibraciones (activo_id)
└─ Todas las maestras (categorías, marcas, sedes, etc.)
```

---

## 🎯 Módulos Principales

### 1. **DASHBOARD**
- **Ruta:** `/index.php?route=dashboard`
- **Archivo:** `app/views/dashboard/index.php`
- **Función:** Panel de control principal con KPIs
- **Permisos requeridos:** `dashboard.view`

---

### 2. **ACTIVOS** 
El módulo más completo de la aplicación

#### Funcionalidades:
- ✅ Listar activos con filtros
- ✅ Crear/Editar activos
- ✅ Eliminar con soft-delete
- ✅ Ver detalle y hoja de vida
- ✅ Gestionar software instalado
- ✅ Carga de fotografías
- ✅ Carga de adjuntos (docs, certificados)
- ✅ Generar código QR y etiquetas
- ✅ Imprimir hoja de vida

#### Rutas:
| Ruta | Archivo | Función |
|------|---------|---------|
| `activos` | `activos/index.php` | Listado principal |
| `activos_form` | `activos/form.php` | Formulario crear/editar |
| `activo_detalle` | `activos/detalle.php` | Ver activo completo |
| `activo_software` | `activos/software.php` | Gestionar software |
| `activos_delete` | `activos/activos_delete.php` | Eliminar (soft) |
| `activos_eliminados` | `activos/activos_eliminados.php` | Ver eliminados |
| `activos_restore` | `activos/activos_restore.php` | Restaurar eliminados |

#### APIs AJAX:
```
POST: ajax/activos_adj_upload.php     → Subir adjuntos
POST: ajax/activos_adj_delete.php     → Eliminar adjuntos
POST: ajax/activos_foto_delete.php    → Eliminar fotos
POST: ajax/act_foto_upload.php        → Subir fotos
GET:  ajax/act_adj_download.php       → Descargar archivos
GET:  ajax/act_adj_preview.php        → Vista previa
```

---

### 3. **MANTENIMIENTOS**
Control integral de servicios realizados

#### Funcionalidades:
- ✅ Programar mantenimientos
- ✅ Registrar en proceso
- ✅ Cerrar con costos
- ✅ Documentar con archivos
- ✅ Registrar firmas (técnico/cliente)
- ✅ Auditoría de cambios
- ✅ Reportes imprimibles

#### Rutas:
| Ruta | Archivo | Función |
|------|---------|---------|
| `mantenimientos` | `mantenimientos/index.php` | Listado |
| `mantenimiento_form` | `mantenimientos/form.php` | Crear/Editar |
| `mantenimiento_detalle` | `mantenimientos/detalle.php` | Vista completa |
| `mantenimiento_ver` | `mantenimientos/detalle.php` | Abierto (mismo) |
| `mantenimiento_print` | `mantenimientos/mantenimiento_print.php` | Impresión |

#### APIs AJAX:
```
POST: ajax/mant_adj_upload.php        → Subir documentos
POST: ajax/mant_adj_delete.php        → Eliminar documentos
GET:  ajax/mant_adj_download.php      → Descargar archivos
```

---

### 4. **CALIBRACIONES**
Gestión de verificaciones técnicas

#### Funcionalidades:
- ✅ Registrar calibraciones
- ✅ Definir puntos de medida
- ✅ Generar certificados
- ✅ Verificación pública por token (sin login)
- ✅ Gestionar patrones (referencias)
- ✅ Firmas digitales
- ✅ Reporte de conformidad

#### Rutas:
| Ruta | Archivo | Función |
|------|---------|---------|
| `calibraciones` | `calibraciones/index.php` | Listado |
| `calibracion_form` | `calibraciones/form.php` | Crear/Editar |
| `calibracion_detalle` | `calibraciones/detalle.php` | Ver detalles |
| `calibracion_puntos` | `calibraciones/puntos.php` | Gestionar puntos |
| `calibracion_certificado` | `calibraciones/certificado_print.php` | Ver certificado |
| `calibracion_verificar` | `calibraciones/verificar.php` | **PÚBLICA** (no requiere login) |

#### APIs AJAX:
```
GET:  ajax/patron_puntos.php                → Obtener puntos del patrón
POST: ajax/ajax_cal_adj_upload.php          → Subir adjuntos
POST: ajax/ajax_cal_adj_delete.php          → Eliminar adjuntos
GET:  ajax/ajax_cal_adj_download.php        → Descargar archivos
GET:  ajax/ajax_cal_adj_preview.php         → Vista previa
POST: ajax/ajax_cal_firma_tecnico.php       → Obtener firma técnico
POST: ajax/ajax_cal_firma_recibido.php      → Obtener firma cliente
POST: ajax/ajax_patron_cert_upload.php      → Gestos certificados patrón
POST: ajax/ajax_patron_cert_delete.php      → Eliminar cert patrón
GET:  ajax/ajax_patron_cert_download.php    → Descargar cert
GET:  ajax/ajax_patron_cert_preview.php     → Vista previa
```

---

### 5. **PATRONES**
Sistema de referencias para calibración

#### Funcionalidades:
- ✅ Crear patrones de medida
- ✅ Subir certificados ISO
- ✅ Asociar puntos (valores estándar)
- ✅ Gestionar trazabilidad

#### Rutas:
| Ruta | Archivo | Función |
|------|---------|---------|
| `patrones` | `patrones/index.php` | Listado |
| `patron_form` | `patrones/form.php` | Crear/Editar |

---

### 6. **CONFIGURACIÓN** (Maestros)
Gestión de datos de referencia

#### Sub-módulos:
| Módulo | Rutas | Descripción |
|--------|-------|-------------|
| **Categorías** | `categorias`, `categoria_form` | Tipos de activos |
| **Tipos de Activo** | `tipos_activo`, `tipo_activo_form` | Subtipo detallado |
| **Marcas** | `marcas`, `marca_form` | Fabricantes |
| **Sedes** | `sedes`, `sede_form` | Ubicaciones principales |
| **Áreas** | `areas`, `area_form` | Departamentos por sede |
| **Proveedores** | `proveedores`, `proveedor_form` | Servicios técnicos |

**API:**
```
POST: ajax/tipo_reglas.php              → Obtener reglas por tipo activo
```

---

### 7. **USUARIOS & ROLES** (Administración)
Control de acceso

#### Funcionalidades:
- ✅ Crear usuarios
- ✅ Asignar roles
- ✅ Gestionar permisos por rol
- ✅ Cambiar estado (activo/inactivo)
- ✅ Autenticación con hash bcrypt

#### Rutas:
| Ruta | Archivo | Función |
|------|---------|---------|
| `usuarios` | `usuarios/index.php` | Listado usuarios |
| `usuario_form` | `usuarios/form.php` | Crear/Editar |
| `roles` | `roles/index.php` | Listado roles |
| `rol_form` | `roles/form.php` | Crear/Editar rol |
| `rol_permisos` | `roles/permisos.php` | Asignar permisos |

---

### 8. **AUDITORÍA** (Trazabilidad)
Registro de acciones

#### Funcionalidades:
- ✅ Log de cambios en tablas
- ✅ Quién, qué, cuándo
- ✅ Timeline de cambios
- ✅ Auditoría por usuario
- ✅ Auditoría por activo

#### Rutas:
| Ruta | Archivo | Función |
|------|---------|---------|
| `auditoria` | `auditoria/audit_log.php` | Log general |
| `usuario_auditoria` | `auditoria/auditoria_usuario.php` | Por usuario |
| `activo_auditoria` | `auditoria/activo_auditoria.php` | Por activo |
| `timeline` | `auditoria/timeline.php` | Vista cronológica |

---

### 9. **EMPRESAS** (Solo SUPERADMIN)
Gestión de tenants

#### Funcionalidades:
- ✅ Crear clientes
- ✅ Activar/Suspender
- ✅ Ver datos

#### Rutas:
| Ruta | Archivo | Función |
|------|---------|---------|
| `empresas` | `empresas/index.php` | Listado |
| `empresa_form` | `empresas/form.php` | Crear/Editar |

---

### 10. **COMPONENTES**
Gestión de partes de activos

#### Funcionalidades:
- ✅ Registrar componentes
- ✅ Asociar a activos
- ✅ Eliminar componentes

#### Rutas:
| Ruta | Archivo | Función |
|------|---------|---------|
| `componentes` | `componentes/componente_form.php` | Gestionar |

---

## 🔐 Componentes Clave

### 1. **Auth.php** - Núcleo de Autenticación

**Ubicación:** `app/core/Auth.php`

#### Métodos Principales:

```php
// Verificación de sesión
Auth::check()              // boolean - ¿Usuario logueado?
Auth::user()               // array - Datos del usuario actual
Auth::userId()             // int - ID del usuario
Auth::tenantId()           // int - ID de la empresa
Auth::rolId()              // int - ID del rol

// Autenticación
Auth::attempt($email, $pass)  // [bool, mensaje]
Auth::logout()                // Cerrar sesión

// Autorización
Auth::requireLogin()       // Guard: redirige a login si no auth
Auth::requirePerm($perm)   // Guard: redirige si no tiene permiso
Auth::hasPerm($perm)       // boolean - ¿Tiene permiso?
Auth::isSuperadmin()       // boolean - ¿Es superadmin?

// Cargar permisos
Auth::loadPerms($refresh)  // Carga en sesión los permisos del rol
```

#### Flujo de Login:
```
formulario login 
  ↓
Auth::attempt(email, password)
  ↓
Verificar credenciales vs BD
  ↓
Verificar usuario ACTIVO
  ↓
Verificar tenant ACTIVO
  ↓
Cargar datos en $_SESSION['user']
  ↓
Permitir acceso
```

#### Sistema de Permisos (RBAC):
```
Tabla: rol_permisos
├── rol_id (FK a roles)
├── permiso (string, ej: "activos.view")
└── Verificado en Auth::requirePerm()
```

**Permisos utilizados en el sistema:**
```
dashboard.view              - Ver panel
activos.view                - Ver listado activos
activos.edit                - Crear/Editar activos
mantenimientos.view         - Ver mantenimientos
mantenimientos.edit         - Crear/Editar
calibraciones.view          - Ver calibraciones
calibraciones.edit          - Crear/Editar calibraciones
usuarios.manage             - Gestionar usuarios
roles.manage                - Gestionar roles
empresas.manage             - Gestionar tenants (SUPERADMIN)
auditoria.view              - Ver auditoría
```

---

### 2. **Helpers.php** - Funciones Utilitarias

**Ubicación:** `app/core/Helpers.php`

#### Funciones Disponibles:

```php
// HTML Escaping
e($str)                     // Escapa para HTML (XSS prevention)

// URLs
base_url()                  // URL base de la app
redirect($path)             // Redirige HTTP

// Validaciones
activo_es_calibrable($t, $a)    // ¿Activo requiere calibración?
require_activo_calibrable()     // Guard para calibrables

// Config
db()                        // Instancia PDO (singleton)
```

---

### 3. **db.php** - Conexión a Base de Datos

**Ubicación:** `app/config/db.php`

```php
// Singleton PDO
$pdo = db();  // Reutiliza conexión

// Configuración desde config.php:
[
  'host' => 'localhost',
  'name' => 'geoactivos_clean',
  'user' => 'root',
  'pass' => '',
  'charset' => 'utf8mb4'
]

// Errores: PDO::ERRMODE_EXCEPTION
// Fetch mode: PDO::FETCH_ASSOC
```

---

### 4. **index.php (Router)** - Control Central

**Ubicación:** `public/index.php`

#### Flujo:
```
1. Inicia sesión
2. Carga núcleos (Auth, Helpers)
3. Obtiene ruta de ?route=XXX (o dashboard por defecto)
4. Define rutas públicas (sin login)
5. Si no es pública → Auth::requireLogin()
6. Switch statement → Carga vista correspondiente
```

#### Rutas Públicas (Sin Login):
```php
$publicRoutes = [
  'login',
  'logout',
  'calibracion_verificar'  // Verificación QR pública
];
```

---

## 📡 APIs AJAX

### Patrón de Uso

**Ubicación:** `app/ajax/`

Las APIs AJAX son endpoints que responden tipicamente JSON:

```php
<?php
// Ejemplo: act_foto_upload.php
require '../config/db.php';
require '../core/Auth.php';

Auth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $file = $_FILES['archivo'] ?? null;
  // ... procesamiento
  echo json_encode(['success' => true, 'message' => 'Guardado']);
}
?>
```

### APIs Disponibles

#### **Activos**
```
POST ajax/act_foto_upload.php            → Upload foto
POST ajax/act_foto_delete.php            → Eliminar foto
GET  ajax/act_adj_download.php           → Descargar adjunto
GET  ajax/act_adj_preview.php            → Preview adjunto
POST ajax/activos_adj_upload.php         → Upload adjunto
POST ajax/activos_adj_delete.php         → Eliminar adjunto
GET  ajax/next_codigo_activo.php         → Próximo código
```

#### **Mantenimientos**
```
POST ajax/mant_adj_upload.php            → Upload documento
POST ajax/mant_adj_delete.php            → Eliminar documento
GET  ajax/mant_adj_download.php          → Descargar archivo
```

#### **Calibraciones**
```
GET  ajax/patron_puntos.php              → Obtener puntos patrón
POST ajax/ajax_cal_adj_upload.php        → Upload adjunto
POST ajax/ajax_cal_adj_delete.php        → Eliminar adjunto
GET  ajax/ajax_cal_adj_download.php      → Descargar
GET  ajax/ajax_cal_adj_preview.php       → Vista previa
POST ajax/ajax_cal_firma_tecnico.php     → Firma técnico
POST ajax/ajax_cal_firma_recibido.php    → Firma cliente
POST ajax/ajax_patron_cert_upload.php    → Upload cert patrón
POST ajax/ajax_patron_cert_delete.php    → Eliminar cert
GET  ajax/ajax_patron_cert_download.php  → Descargar cert
GET  ajax/ajax_patron_cert_preview.php   → Preview cert
```

#### **Configuración**
```
POST ajax/tipo_reglas.php                → Reglas por tipo activo
```

---

## 🔄 Flujos de Procesos Principales

### Flujo 1: Crear Nuevo Activo

```
Usuario en /activos
  ↓ Click "Nuevo"
  ↓ GET ?route=activos_form
  ↓ Cargar activos/form.php
  ↓ Mostrar formulario vacío
  ↓ Usuario completa datos
  ↓ POST form → BD
  ↓ INSERT activos con tenant_id
  ↓ Redirige a /activos (listado)
  ↓ Muestra activo nuevo en tabla
```

### Flujo 2: Registrar Mantenimiento

```
Ver activo en detalle
  ↓ Click "Nuevo mantenimiento"
  ↓ GET ?route=mantenimiento_form&activo_id=X
  ↓ Cargar mantenimientos/form.php
  ↓ Usuario llena datos (tipo, costos, etc)
  ↓ POST form → BD
  ↓ INSERT mantenimientos
  ↓ Create audit_log (tracking)
  ↓ Redirige a detalle
  ↓ Muestra lista de mantenimientos actualizados
```

### Flujo 3: Subir Archivos a Activo

```
En activos/detalle.php
  ↓ Usuario selecciona archivo
  ↓ JS llama POST ajax/activos_adj_upload.php
  ↓ PHP valida (mime, tamaño)
  ↓ Mueve archivo a /public/uploads/activos/
  ↓ INSERT en tabla activos_adjuntos
  ↓ Retorna JSON {success: true}
  ↓ JS actualiza tabla de archivos
```

### Flujo 4: Generar Certificado de Calibración

```
En calibraciones/form.php
  ↓ Técnico llena datos de calibración
  ↓ Selecciona puntos de medida
  ↓ Sube certificado (PDF/imagen)
  ↓ POST form
  ↓ INSERT calibraciones
  ↓ INSERT calibracion_puntos
  ↓ Genera enlace público único
  ↓ Puede enviarse a cliente
  ↓ Cliente accede sin login por token
  ↓ (Ruta calibracion_verificar es pública)
```

### Flujo 5: Auditoría

```
Cualquier cambio en tabla auditada
  ↓ INSERT audit_log con:
    - usuario_id (quién)
    - tabla (qué)
    - accion (INSERT/UPDATE/DELETE)
    - registro_id (cuál)
    - cambios (en JSON)
    - creado_en (cuándo)
  ↓ Usuario puede ver en auditoria/
  ↓ Filtrar por usuario, activo, tabla
```

---

## 🛠️ Tecnologías Utilizadas

### Backend
| Tecnología | Uso |
|-----------|-----|
| **PHP 7.4+** | Lenguaje servidor |
| **PDO** | Acceso a BD |
| **MySQLi/MySQL** | Base de datos |
| **bcrypt** | Hash de contraseñas |
| **Sessions** | Autenticación |

### Frontend
| Tecnología | Uso |
|-----------|-----|
| **HTML5** | Estructura |
| **CSS3** | Estilos (AdminLTE) |
| **Bootstrap 4** | Framework responsivo |
| **jQuery** | Interactividad AJAX |
| **FontAwesome 5** | Iconos |
| **Chart.js** | Gráficos (si existe) |

### Infraestructura
| Componente | Versión |
|-----------|---------|
| **XAMPP** | Local dev |
| **Apache** | Web server |
| **MySQL/MariaDB** | BD |
| **PHP** | 7.4+ |

---

## 🔒 Consideraciones de Seguridad

### Implementadas ✅

1. **Autenticación**
   - Contraseñas hasheadas con bcrypt
   - Sesiones PHP
   - CSRF implícito (redireccionamiento)

2. **Autorización**
   - RBAC (Role-Based Access Control)
   - Guards en cada ruta
   - Verificación de tenant_id

3. **SQL Injection Prevention**
   - Prepared Statements (PDO)
   - Parámetros nombrados (:param)

4. **XSS Prevention**
   - Función `e()` para escapar HTML
   - htmlspecialchars()

5. **Multi-Tenant Isolation**
   - Cada query filtra por tenant_id
   - Usuarios no pueden ver datos de otros clientes

### Recomendaciones de Mejora 🔐

1. **HTTPS Mandatory**
   ```php
   // En public/index.php
   if (empty($\_SERVER['HTTPS'])) {
       header('Location: https://...');
   }
   ```

2. **CSRF Tokens**
   ```php
   // En formularios
   <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
   ```

3. **Rate Limiting**
   - En login (intentos fallidos)
   - En APIs AJAX

4. **Validación de Inputs**
   - Validar tipos de datos
   - Rangos numéricos
   - Formatos de email

5. **Logs de Seguridad**
   - Intentos de login fallidos
   - Acceso a rutas no autorizadas
   - Cambios sensibles (usuarios, roles)

6. **Encriptación de Datos Sensibles**
   - Números de serial
   - Datos técnicos críticos

---

## 📊 Estadísticas del Proyecto

| Métrica | Valor |
|---------|-------|
| **Archivos PHP** | ~60+ |
| **Vistas HTML** | ~30+ |
| **APIs AJAX** | ~25+ |
| **Tablas BD** | ~20+ |
| **Módulos Funcionales** | 10 |
| **Permisos RBAC** | 10+ |
| **Contacto con BD** | 150+ queries |

---

## 🚀 Flujo de Instalación Rápida

### Paso 1: Copiar Carpeta
```
C:\xampp\htdocs\geoactivos\
```

### Paso 2: Crear Base de Datos
```
http://localhost/phpmyadmin
Crear: geoactivos
Importar: database/geoactivos_schema.sql
```

### Paso 3: Configurar config.php
```php
c:\xampp\htdocs\geoactivos\app\config\config.php
// Ya viene configurado para XAMPP default
```

### Paso 4: Crear Admin
```
Abrir: http://localhost/geoactivos/public/hash.php
→ Poner contraseña "Admin123*" → Copiar hash
```

```sql
INSERT INTO tenants (nombre, nit, email, estado)
VALUES ('Cliente Demo', '900000000', 'demo@cliente.com', 'ACTIVO');

INSERT INTO roles (tenant_id, nombre, es_superadmin)
VALUES (1, 'ADMIN', 1);

INSERT INTO categorias_activo (tenant_id, nombre)
VALUES (1, 'GENERAL');

INSERT INTO usuarios (tenant_id, rol_id, nombre, email, pass_hash, estado)
VALUES (1, 1, 'Administrador', 'admin@demo.com', '[HASH]', 'ACTIVO');
```

### Paso 5: Acceder
```
http://localhost/geoactivos/public/index.php?route=login
Email: admin@demo.com
Pass: Admin123*
```

---

## 📝 Convenciones del Código

### Nomenclatura

| Tipo | Convención | Ejemplo |
|------|-----------|---------|
| **Archivos** | snake_case.php | `activos_delete.php` |
| **Directorios** | snake_case | `mantenimientos/` |
| **Variables** | $camelCase | `$tenantId`, `$activoId` |
| **Funciones** | camelCase() | `e()`, `base_url()` |
| **Clases** | PascalCase | `Auth`, `Helpers` |
| **Constantes** | UPPERCASE | N/A (no se usan tantas) |
| **SQL** | UPPERCASE | `SELECT`, `WHERE` |

### Estructura de Vistas

```php
<?php
// 1. Requires
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../config/db.php';

// 2. Guard (permisos)
Auth::requirePerm('activos.view');

// 3. Obtener datos
$tenantId = Auth::tenantId();
$datos = db()->prepare("SELECT * FROM activos WHERE tenant_id = :t")->execute([':t' => $tenantId])->fetchAll();

// 4. Requerir layout
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/sidebar.php';
?>
<!-- 5. HTML / Presentación -->
<div class="card">...</div>

<?php
// 6. Footer (si aplica)
require __DIR__ . '/../layout/footer.php';
?>
```

---

## 🎓 Conclusiones

### Fortalezas ✅
1. **Arquitectura clara** - MVC separado, fácil de mantener
2. **Multi-tenant integrado** - Soporta múltiples clientes
3. **RBAC robusto** - Control granular de permisos
4. **Auditoría completa** - Trazabilidad de cambios
5. **Módulos encapsulados** - Poco acoplamiento
6. **BD bien normalizada** - Relaciones limpias

### Áreas de Mejora 🔄
1. **Usar un framework** (Laravel, Symfony) para escalar
2. **APIs REST** en lugar de AJAX (integración móvil)
3. **Tests automatizados** (PHPUnit)
4. **Caching** (Redis) para BD
5. **Validación centralizada** en una clase
6. **Migraciones de BD** (Doctrine/Liquibase)
7. **Logging** centralizado (Monolog)

### Próximos Pasos Recomendados 🚀
- [ ] Implementar HTTPS
- [ ] Agregar CSRF tokens
- [ ] Rate limiting en login
- [ ] Validación en lado servidor más robusta
- [ ] Integración con API externa (Google Drive para backups)
- [ ] Dashboard mejorado con más KPIs
- [ ] Exportar a Excel/PDF
- [ ] Notificaciones por email
- [ ] Aplicación móvil (consumir API REST)

---

**Generado:** 3 de Marzo de 2026  
**Versión de Análisis:** 1.0  
**Estado:** ✅ Completado

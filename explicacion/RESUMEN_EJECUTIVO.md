# 📑 RESUMEN EJECUTIVO - GEOACTIVOS

## 🎯 Vista Rápida del Proyecto

**Nombre:** GeoActivos  
**Tipo:** Aplicación Web - Gestión de Activos Fijos  
**Modelo:** Multi-Tenant (SaaS)  
**Stack:** PHP + MySQL + AdminLTE  
**Estado:** ✅ Funcional y operativo  

---

## 🏢 ¿Para Quién es?

Empresas que necesitan:
- Mantener inventario detallado de equipos
- Programar y registrar mantenimientos
- Certificar calibraciones técnicas
- Auditar cambios y movimientos
- Generar reportes para clientes/reguladores

---

## 📊 Diagrama de Módulos

```
┌─────────────────────────────────────────────────────────┐
│               GEOACTIVOS v1.0                            │
│  (Multi-tenant) Gestión de Activos Fijos                 │
└─────────────────────────────────────────────────────────┘
                          │
          ┌───────────────┼───────────────┐
          ▼               ▼               ▼
       LOGIN          DASHBOARD          CONFIG
      (Auth)          (KPIs)            (Maestros)
          │               │                 │
          ├─ Hash          ├─ Estadísticas  ├─ Categorías
          └─ SessionPHP    └─ Gráficos      ├─ Marcas
                                            ├─ Sedes
                                            ├─ Áreas
                                            ├─ Proveedores
                                            └─ Tipos Activo
                          │
          ┌───────────────┼───────────────┐
          ▼               ▼               ▼
       ACTIVOS       MANTENIMIENTOS   CALIBRACIONES
       (Inventario)   (Servicio)      (Técnico)
          │               │                 │
    ┌─────┴─────┐    ┌────┴────┐      ┌────┴────┐
    ▼           ▼    ▼         ▼      ▼         ▼
  Crear      Eliminar Form   Auditoría Puntos  Certificados
  Editar     Restore  Costos  Firma           Patrones
  QR         Softdel  Archivo Impresión       Verificar
  Fotos                                       (Pública)
  Adjuntos
  Software

                          │
          ┌───────────────┼───────────────┐
          ▼               ▼               ▼
      USUARIOS          ROLES           EMPRESAS
      (Cuentas)        (Permisos)       (Tenants)
          │               │                 │
    ┌─────┴─────┐    ┌────┴────┐      ┌────┴────┐
    ▼           ▼    ▼         ▼      ▼         ▼
  Crear      Imagen RBAC    Permisos  Crear    Estado
  Editar            Cambios  CRUD                Datos
  Eliminar          Auditar                      Email

                          │
                          ▼
                      AUDITORÍA
                    (Trazabilidad)
                          │
                    ┌─────┼─────┐
                    ▼     ▼     ▼
                 Tabla  Usuario Activo
                 Log    Log     Timeline
```

---

## 🗄️ Arquitectura de BD (Simplificado)

```
tenants (Clientes)
  ├─ roles → usuarios (1:N)
  ├─ categorias_activo
  ├─ marcas
  ├─ sedes → areas
  ├─ proveedores
  ├─ tipos_activo
  └─ activos (1:N)
       ├─ mantenimientos (1:N)
       │   └─ mantenimiento_archivos
       ├─ calibraciones (1:N)
       │   ├─ calibracion_puntos
       │   └─ calibracion_archivos
       ├─ activos_adjuntos
       ├─ activos_fotos
       ├─ componentes (1:N)
       └─ software_activo (1:N)

audit_log (Auditoría global)
  └─ Registra cambios en todas las tablas
```

---

## 🔀 Flujo de Autenticación

```
┌─────────────────────┐
│   Usuario accede    │
│ index.php?route=... │
└──────────┬──────────┘
           │
    ┌──────▼──────┐
    │ ¿Es pública?│
    └──────┬──────┘
           │
      NO   │   SÍ
      ┌────┘────┐
      ▼         ▼
  ["login",  ["calibracion_
   "logout", verificar"]
   session→ruta
   ]
      │
      ├─┐ SÍ → Carga vista
      │
      └─┐ NO → Auth::requireLogin()
           ├─ ¿Sesión activa?
           │   └─ NO → Redire a login
           │
           ├─ ¿Tiene permiso?
           │   └─ NO → Error 403
           │
           └─ ✅ Carga vista con datos
```

---

## 📋 Tabla de Módulos vs Funcionalidades

| Módulo | Crear | Leer | Actualizar | Eliminar | Extra |
|--------|-------|------|-----------|---------|-------|
| **Activos** | ✅ | ✅ | ✅ | ✅ Soft | QR, Fotos, Software |
| **Mantenimiento** | ✅ | ✅ | ✅ | ✅ | Costos, Archivos |
| **Calibraciones** | ✅ | ✅ | ✅ | ✅ | Certificados, Público |
| **Usuarios** | ✅ | ✅ | ✅ | ✅ | Roles |
| **Roles** | ✅ | ✅ | ✅ | ✅ | Permisos |
| **Maestros** | ✅ | ✅ | ✅ | ✅ | Multiples |
| **Auditoría** | ❌ | ✅ | ❌ | ❌ | Timeline |
| **Empresas** | ✅ | ✅ | ✅ | ✅ | Solo ADMIN |

---

## 🔒 Seguridad Implementada

### ✅ Presente
- [x] Autenticación con hash bcrypt
- [x] Sesiones PHP
- [x] RBAC (Role-Based Access Control)
- [x] Prepared Statements (SQL Injection)
- [x] Función e() para XSS
- [x] Aislamiento multi-tenant
- [x] Auditoría de cambios

### ⚠️ Recomendado
- [ ] HTTPS Obligatorio
- [ ] CSRF Tokens
- [ ] Rate Limiting
- [ ] Validación de inputs más robusta
- [ ] 2FA (Two-Factor Authentication)
- [ ] Password reset por email
- [ ] Encriptación de datos sensibles

---

## 📁 Estructura de Archivos Clave

### Configuración
```
app/config/
├── config.php      ← Parámetros (BD, timezone)
└── db.php          ← Conexión PDO (singleton)
```

### Núcleo (Core)
```
app/core/
├── Auth.php        ← Autenticación + Autorización
└── Helpers.php     ← Funciones utilitarias
```

### Vistas (Frontend)
```
app/views/
├── layout/         ← header, sidebar, footer
├── activos/        ← Módulo activos
├── mantenimientos/ ← Módulo mantenimientos
├── calibraciones/  ← Módulo calibraciones
└── ... (10 módulos más)
```

### APIs (Backend ligero)
```
app/ajax/
├── act_*.php       ← APIs activos
├── mant_*.php      ← APIs mantenimientos
├── ajax_cal_*.php  ← APIs calibraciones
└── ... (25 endpoints más)
```

### Entrada Principal
```
public/
├── index.php       ← Router central (requisite todas las rutas)
├── hash.php        ← Generador de contraseñas
└── assets/         ← CSS, JS, imgs
```

### Base de Datos
```
database/
├── geoactivos_schema.sql  ← Creación de tablas
└── geoactivos_clean.sql   ← Datos iniciales
```

---

## 🔑 Archivos Críticos a Conocer

### 1️⃣ public/index.php
**Por qué:** Punto de entrada de TODA la aplicación  
**Qué hace:**
- Inicia sesión
- Carga clases core (Auth, Helpers)
- Router con switch statement
- Carga vista por ruta

**Si necesitas:** Agregar nuevas rutas, cambiar flujo de autenticación

### 2️⃣ app/core/Auth.php
**Por qué:** Motor de seguridad  
**Qué hace:**
- Login/logout
- Verificar permisos
- Cargar datos de usuario
- Guard (requireLogin, requirePerm)

**Si necesitas:** Cambiar lógica de permisos, agregar 2FA, cambiar roles

### 3️⃣ app/config/db.php
**Por qué:** Conexión a BD  
**Qué hace:**
- PDO singleton
- Preparación de statements
- Manejo de errores

**Si necesitas:** Cambiar servidor BD, añadir caché, migraciones

### 4️⃣ app/views/layout/header.php
**Por qué:** Plantilla maestra  
**Qué hace:**
- HTML base
- Navbar
- Scripts globales
- Breadcrumb

**Si necesitas:** Cambiar diseño, agregar scripts, modificar navbar

### 5️⃣ app/views/layout/sidebar.php
**Por qué:** Menú lateral  
**Qué hace:**
- Links de navegación
- Módulos disponibles
- Verificación de permisos por enlace

**Si necesitas:** Agregar items de menú, reorganizar módulos

---

## 🚀 Rutas Principales (50+)

### Públicas (Sin login)
```
login               → Formulario de acceso
logout              → Cierra sesión
calibracion_verificar → Ver certificado por token (sin login)
```

### Privadas (Requieren login)
```
dashboard           → Panel de control
activos             → Listado de activos
activos_form        → Crear/editar activo
activo_detalle      → Detalles completos
mantenimientos      → Listado
mantenimiento_form  → Crear/editar
... (30+ más)
```

---

## 🔄 Ciclo de Vida Típico

### Crear un Nuevo Activo

```
Step 1: Usuario en /activos (listado)
↓
Step 2: Click en "Nuevo" → GET ?route=activos_form
↓
Step 3: Carga app/views/activos/form.php
        Muestra formulario HTML vacío
↓
Step 4: Usuario completa:
        - Código interno
        - Nombre
        - Modelo
        - Serial
        - Categoría
        - Marca
        - Área
        - etc.
↓
Step 5: Submit POST form → INSERT activos
        ├─ Valida datos
        ├─ Checkea tenant_id (multi-tenant)
        ├─ Guarda en BD
        ├─ CREATE audit_log (quién, qué, cuándo)
        └─ Redirige a /activos
↓
Step 6: Ve activo en listado (tabla)
        ✅ Listo para:
           - Ver detalles
           - Crear mantenimientos
           - Subir fotos
           - Generar QR
```

---

## 💾 Operaciones CRUD por Módulo

### ACTIVOS
```php
CREATE  → INSERT activos (con tenant_id)
READ    → SELECT activos WHERE tenant_id = :t
UPDATE  → UPDATE activos SET ... WHERE id = :a
DELETE  → UPDATE activos SET eliminado = 1 (soft delete)
RESTORE → UPDATE activos SET eliminado = 0
```

### MANTENIMIENTOS
```php
CREATE  → INSERT mantenimientos
READ    → SELECT mantenimientos WHERE activo_id = :a
UPDATE  → UPDATE mantenimientos ...
DELETE  → UPDATE mantenimientos SET estado = 'ANULADO'
```

### CALIBRACIONES
```php
CREATE  → INSERT calibraciones + INSERT puntos + certificados
READ    → SELECT calibraciones + puntos
UPDATE  → UPDATE calibraciones ...
DELETE  → DELETE calibraciones (hard delete puede ser riesgo)
SHARE   → Token público para verificación
```

---

## 🎨 Componentes Frontend

### Layout
```
┌─────────────────────────────────────────────┐
│ NAVBAR (Links, User, Logout)                │
├──────────────┬──────────────────────────────┤
│              │                              │
│  SIDEBAR     │    CONTENIDO                 │
│  (Módulos)   │    (Vistas)                  │
│              │                              │
│              ├──────────────────────────────┤
│              │ FOOTER                       │
└──────────────┴──────────────────────────────┘
```

### Componentes Reutilizables
- **Cards** - Contenedores de datos
- **Tables** - Listados con paginación
- **Forms** - Formularios con validación
- **Buttons** - CRUD actions (Create, Read, Update, Delete)
- **Badges** - Estados (Activo, Inactivo, etc)
- **Modals** - Confirmaciones

---

## 📊 Métricas Técnicas

| Métrica | Valor |
|---------|-------|
| Líneas código PHP | ~5,000+ |
| Archivos PHP | ~65 |
| Archivos vistas HTML | ~30 |
| APIs AJAX | ~25 |
| Tablas BD | ~20 |
| Modulos funcionales | 10 |
| Permisos RBAC | 12+ |
| Rámificaciones router | 50+ |
| Queries BD promedio | 150+ |

---

## 🎓 Próximos Pasos Recomendados

### Corto Plazo (1-2 semanas)
- [ ] Agregar HTTPS
- [ ] CSRF Tokens en formularios
- [ ] Rate limiting en login
- [ ] Validación más robusta de inputs
- [ ] Documentar APIs AJAX con Postman

### Mediano Plazo (1-2 meses)
- [ ] Migrar a Laravel (si va a crecer)
- [ ] Tests automatizados (PHPUnit)
- [ ] Caching con Redis
- [ ] Email notifications
- [ ] Excel export

### Largo Plazo (3+ meses)
- [ ] API REST (para mobile)
- [ ] Aplicación móvil (Flutter/React Native)
- [ ] Machine Learning (predictive maintenance)
- [ ] Integración con IoT (sensores)
- [ ] Blockchain para certificados

---

## 🆘 Troubleshooting Rápido

### "Usuario o contraseña incorrectos"
→ Verificar hash en `public/hash.php`
→ Confirmar datos en tabla `usuarios`

### "Error al subir archivo"
→ Verificar permisos en `/public/uploads/`
→ Revisar `mime` permitido en PHP
→ Tamaño máximo en `php.ini`

### "Permiso denegado (403)"
→ Revisar permisos del rol en `rol_permisos`
→ Confirmar usuario tiene rol asignado correctamente

### "Base de datos no conecta"
→ `app/config/config.php` - servidor/usuario/contraseña
→ MySQL está corriendo en XAMPP
→ BD existe (si no → importar SQL)

### "Cierra sesión aleatoriamente"
→ Problema con garbage collection de sesiones
→ `php.ini`: session.gc_maxlifetime
→ Limpiar `C:\xampp\tmp\`

---

## 📚 Documentación Relacionada

Consulta los siguientes archivos:
- [ANALISIS_PROYECTO.md](./ANALISIS_PROYECTO.md) - Análisis técnico completo
- [README.txt](./README.txt) - Instalación
- [database/geoactivos_schema.sql](./database/geoactivos_schema.sql) - Estructura BD

---

## 💬 Preguntas Frecuentes

### ¿Qué es un "tenant"?
Un cliente/empresa que usa la aplicación. Todo está aislado por `tenant_id`.

### ¿Puedo usar PHP 5.6?
No recomendado. Usa PHP 7.4+ mínimo (bcrypt nativo, type hints).

### ¿Cómo agregar un nuevo módulo?
1. Crear ruta en `public/index.php`
2. Crear vista en `app/views/mi_modulo/`
3. Crear permisos en tabla `rol_permisos`
4. Agregar link en `app/views/layout/sidebar.php`

### ¿Puedo eliminar datos?
Sí, usa soft delete (`eliminado = 1`) para auditoría.

### ¿Soporta multi-idioma?
No actualmente. Usa un traductor o agrega locales.

---

## 👨‍💻 Stack Tecnológico Detallado

```
Frontend                Backend                 BD
─────────────────────────────────────────────────────
HTML5                   PHP 7.4+               MySQL 5.7+
CSS3/Bootstrap4         PDO                    MariaDB 10.5
jQuery 3.x              bcrypt                 utf8mb4
AdminLTE 3.2            JSON                   InnoDB
FontAwesome 5           Prepared Stmts         Foreign Keys
Chart.js                Sessions               Indexes
```

---

**Análisis Generado:** 3 de Marzo de 2026  
**Tiempo de Lectura:** ~15 minutos  
**Nivel de Detalle:** Ejecutivo + Técnico  
✅ Documento Completo

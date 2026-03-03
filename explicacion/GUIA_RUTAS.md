# 🗺️ GUÍA DE RUTAS Y FUNCIONALIDADES

**Documento:** Mapeo completo de rutas de la aplicación  
**Fecha:** 3 de Marzo de 2026  
**Versión:** 1.0  

---

## 📍 Índice de Rutas por Módulo

1. [Autenticación](#autenticación)
2. [Dashboard](#dashboard)
3. [Activos](#activos)
4. [Mantenimientos](#mantenimientos)
5. [Calibraciones](#calibraciones)
6. [Configuración (Maestros)](#configuración)
7. [Usuarios & Roles](#usuarios--roles)
8. [Auditoría](#auditoría)
9. [Empresas](#empresas)
10. [Patrones](#patrones)
11. [Componentes](#componentes)

---

## 🔐 AUTENTICACIÓN

### Rutas Públicas (Sin login requerido)

| Ruta | Método | Archivo | Descripción |
|------|--------|---------|-------------|
| `login` | GET/POST | `auth/login.php` | Formulario y procesamiento de login |
| `logout` | GET | `auth/login.php` | Cierra sesión y redirige |
| `calibracion_verificar` | GET | `calibraciones/verificar.php` | **PÚBLICA**: Ver certificado por token sin login |

### Funcionalidades
```
✅ Formulario de acceso (email + contraseña)
✅ Validación de credenciales
✅ Hash bcrypt de contraseñas
✅ Sesión PHP iniciada
✅ Redirección a dashboard
✅ Cierre de sesión
✅ Recuerda último usuario (optional)
```

### Generador de Contraseña
```
URL: http://localhost/geoactivos/public/hash.php
Ingresa: Mi_Contraseña_123
Salida: $2y$10$... (hash bcrypt)
Copia el hash y pégalo en INSERT usuarios
```

---

## 📊 DASHBOARD

### Rutas

| Ruta | Método | Archivo | Descripción |
|------|--------|---------|-------------|
| `dashboard` | GET | `dashboard/index.php` | Panel principal con KPIs |

### Funcionalidades
```
📈 Estadísticas de activos
   - Total activos
   - Por estado (ACTIVO, EN_MANTENIMIENTO, BAJA)
   - Por categoría
   
📅 Mantenimientos próximos
   └─ Mostrar listado de programados
   
🔧 Mantenimientos en proceso
   └─ Mostrar en tiempo real
   
📋 Calibraciones próximas
   └─ Mostrar por vencer
   
✅ Gráficos (si están implementados)
   └─ Charts.js o similar

🔗 Accesos rápidos
   └─ Botones a módulos principales
```

### Permisos Requeridos
```
✅ dashboard.view
```

---

## 🖥️ ACTIVOS (Módulo Principal)

### Rutas del Módulo

| Ruta | Método | Archivo | Función |
|------|--------|---------|---------|
| `activos` | GET | `activos/index.php` | Listado principal de activos |
| `activos_form` | GET/POST | `activos/form.php` | Crear o editar activo |
| `activo_detalle` | GET | `activos/detalle.php` | Ver detalles completos |
| `activo_software` | GET | `activos/software.php` | Gestionar software instalado |
| `activo_software_delete` | POST | `activos/software_delete.php` | Eliminar software |
| `activo_hoja_vida` | GET | `activos/activo_hoja_vida.php` | Hoja de vida del activo |
| `activo_hoja_vida_print` | GET | `activos/activo_hoja_vida_print.php` | Imprimir hoja de vida |
| `activo_qr_etiqueta` | GET | `activos/activo_qr_etiqueta.php` | Generar y imprimir código QR |
| `activos_delete` | GET/POST | `activos/activos_delete.php` | Marcar como eliminado (soft) |
| `activos_eliminados` | GET | `activos/activos_eliminados.php` | Ver eliminados |
| `activos_restore` | GET/POST | `activos/activos_restore.php` | Restaurar eliminados |
| `activos_purge` | GET/POST | `activos/activos_purge.php` | **[Peligro]** Eliminar permanentemente |

### Funcionalidades Detalladas

#### 1. Listado de Activos (`activos`)
```
Tabla con columnas:
├─ Código interno
├─ Nombre
├─ Categoría
├─ Tipo de activo
├─ Marca
├─ Modelo
├─ Serial
├─ Proveedor
├─ Área
├─ Sede
├─ Estado (ACTIVO, EN_MANTENIMIENTO, BAJA)
└─ Acciones (Ver, Editar, Eliminar)

Características:
✅ Paginación (300 por defecto)
✅ Filtros por estado
✅ Búsqueda por código/nombre
✅ Soft delete integrado
✅ Vista responsiva
```

#### 2. Formulario Activo (`activos_form`)
```
Campos principales:
├─ Código interno (UNIQUE)
├─ Nombre
├─ Modelo
├─ Serial
├─ Placa (opcional)
├─ Categoría *
├─ Tipo de activo
├─ Marca
├─ Proveedor
├─ Área
└─ Sede

Campos adicionales:
├─ Fecha compra
├─ Fecha instalación
├─ Garantía hasta
├─ Estado
└─ Observaciones

AJAX Helper:
└─ next_codigo_activo.php → Genera próximo código

Validaciones:
✅ Código único por tenant
✅ Categoría obligatoria
✅ Email proveedor (si aplica)
✅ Fechas válidas
```

#### 3. Detalles Activo (`activo_detalle`)
```
Secciones:
├─ Datos maestros (todos los campos)
├─ Fotografías (subidas)
├─ Adjuntos (documentos, certificados)
├─ Software instalado
├─ Componentes incluidos
├─ Mantenimientos históricos
│  ├─ Preventivos
│  ├─ Correctivos
│  └─ Predictivos
├─ Calibraciones
│  ├─ Historial
│  ├─ Próximas
│  └─ Desconformes
└─ Historial de cambios (auditoría)

Acciones disponibles:
✅ Editar datos
✅ Subir fotos
✅ Subir documentos
✅ Agregar software
✅ Nuevo mantenimiento
✅ Nueva calibración
✅ Ver QR
✅ Imprimir hoja de vida
✅ Descargar certificados
```

#### 4. Gestión de Software (`activo_software`)
```
Tabla de software:
├─ Nombre
├─ Licencia
├─ Versión
├─ Nro. licencia
└─ Acciones (Eliminar)

Operaciones:
✅ Agregar new software
✅ Editar software
✅ Eliminar con confirmación
✅ Ver total por activo
```

#### 5. Hoja de Vida (`activo_hoja_vida`)
```
Documento completo que incluye:
├─ Datos generales
├─ Especificaciones técnicas
├─ Mantenimientos realizados
├─ Calibraciones vigentes
├─ Fotos del activo
├─ Certificados
└─ Observaciones importantes

Permite:
✅ Imprimir a PDF
✅ Compartir con cliente
✅ Firmar digitalmente
✅ Descargar como HTML
```

#### 6. Código QR (`activo_qr_etiqueta`)
```
Genera:
├─ Código QR único por activo
├─ Apunta a: calibracion_verificar?token=XXX
├─ Permite verificación sin login
├─ Etiqueta imprimible (A4, A5)
└─ Logo personalizable

Uso:
✅ Pegar en equipo físico
✅ Clientelea verifica estado
✅ Acceso público a verificación
```

#### 7. Eliminación Lógica (`activos_delete`)
```
Soft Delete:
├─ UPDATE activos SET eliminado = 1
├─ No elimina datos (auditoría)
├─ Oculta del listado principal
└─ Recuperable

Opciones:
✅ Eliminar un activo
✅ Restaurar
✅ Ver eliminados
✅ Purgar permanentemente (admin only)
```

### APIs AJAX Activos

```
POST /app/ajax/act_foto_upload.php
├─ Parámetros: file, activo_id
├─ Respuesta: {success: bool, id: int, path: string}
└─ Almacena en: /public/uploads/activos/

POST /app/ajax/act_foto_delete.php
├─ Parámetros: foto_id, activo_id
├─ Respuesta: {success: bool}
└─ Soft delete

POST /app/ajax/activos_adj_upload.php
├─ Parámetros: file, activo_id, tipo (doc/cert)
├─ Respuesta: {success: bool, id: int, path: string}
└─ Validaciones mime (PDF, Doc, Jan)

POST /app/ajax/activos_adj_delete.php
├─ Parámetros: adjunto_id, activo_id
├─ Respuesta: {success: bool}
└─ Elimina archivo

GET /app/ajax/act_adj_download.php
├─ Parámetros: id, activo_id
├─ Respuesta: Descarga archivo
└─ Verifica permisos tenant

GET /app/ajax/act_adj_preview.php
├─ Parámetros: id, activo_id
├─ Respuesta: Muestra en nuevo tab (PDF/IMG)
└─ inline view

GET /app/ajax/next_codigo_activo.php
├─ Parámetros: categoria_id, tenant_id
├─ Respuesta: {codigo_sugerido: "ACT-001"}
└─ Auto genera códigos secuenciales

POST /app/ajax/activos_foto_delete.php
├─ Parámetros: foto_id
├─ Respuesta: {success: bool}
└─ Elimina foto directamente
```

### Permisos Requeridos
```
✅ activos.view        → Ver listado y detalles
✅ activos.edit        → Crear, editar, eliminar
```

---

## 🔧 MANTENIMIENTOS

### Rutas del Módulo

| Ruta | Método | Archivo | Función |
|------|--------|---------|---------|
| `mantenimientos` | GET | `mantenimientos/index.php` | Listado de mantenimientos |
| `mantenimiento_form` | GET/POST | `mantenimientos/form.php` | Crear/editar mantenimiento |
| `mantenimiento_detalle` | GET | `mantenimientos/detalle.php` | Ver detalles completos |
| `mantenimiento_ver` | GET | `mantenimientos/detalle.php` | Alias de detalle |
| `mantenimiento_print` | GET | `mantenimientos/mantenimiento_print.php` | Imprimir orden |
| `mantenimiento_auditoria` | GET | `mantenimientos/mantenimiento_auditoria.php` | Historial de cambios |

### Funcionalidades Detalladas

#### 1. Listado Mantenimientos (`mantenimientos`)
```
Tabla con columnas:
├─ ID / Código
├─ Activo
├─ Tipo (PREVENTIVO, CORRECTIVO, PREDICTIVO)
├─ Estado (PROGRAMADO, EN_PROCESO, CERRADO, ANULADO)
├─ Prioridad (BAJA, MEDIA, ALTA, CRITICA)
├─ Fecha programada
├─ Fecha ejecución
└─ Acciones

Filtros:
✅ Por estado
✅ Por tipo
✅ Por prioridad
✅ Por fechas
✅ Por activo
```

#### 2. Formulario Mantenimiento (`mantenimiento_form`)
```
Seccion 1: Básicos
├─ Activo (búsqueda/select)
├─ Tipo (PREVENTIVO/CORRECTIVO/PREDICTIVO) *
├─ Estado (PROGRAMADO/EN_PROCESO/CERRADO) *
└─ Prioridad (BAJA/MEDIA/ALTA/CRITICA) *

Sección 2: Técnico
├─ Falla reportada (textarea)
├─ Diagnóstico (textarea)
├─ Actividades realizadas (textarea)
└─ Recomendaciones (textarea)

Sección 3: Fechas
├─ Fecha programada
├─ Fecha inicio
└─ Fecha fin

Sección 4: Costos
├─ Costo mano de obra (DECIMAL)
└─ Costo repuestos (DECIMAL)

Funcionalidades:
✅ Autovalidación de fechas
✅ Cálculo automático de duración
✅ Desglose de costos
✅ Guardado de borradores
```

#### 3. Detalles Mantenimiento (`mantenimiento_detalle`)
```
Secciones:
├─ Información principal (editable)
├─ Archivos adjuntos
│  ├─ Fotos del trabajo
│  ├─ Certificados de prueba
│  └─ Documentos técnicos
├─ Firmas digitales
│  ├─ Firma del técnico
│  └─ Firma del cliente
├─ Repuestos utilizados (tabla)
│  ├─ Nombre
│  ├─ Cantidad
│  └─ Costo unitario
├─ Historial de cambios (auditoría)
└─ Estado actual (badge)

Acciones:
✅ Editar
✅ Cambiar estado
✅ Subir adjuntos
✅ Obtener firmas
✅ Imprimir
✅ Generar reporte
```

#### 4. Impresión (`mantenimiento_print`)
```
Documento printable que incluye:
├─ Encabezado empresa
├─ Datos activo
├─ Datos mantenimiento
├─ Especificaciones técnicas
├─ Trabajo realizado
├─ Costos desglosados
├─ Firmas con fecha
└─ QR de verificación
```

### APIs AJAX Mantenimientos

```
POST /app/ajax/mant_adj_upload.php
├─ Parámetros: file, mantenimiento_id
├─ Respuesta: {success: bool, id: int}
└─ Soporta múltiples archivos

POST /app/ajax/mant_adj_delete.php
├─ Parámetros: adjunto_id, mantenimiento_id
├─ Respuesta: {success: bool}
└─ Elimina archivo y registro

GET /app/ajax/mant_adj_download.php
├─ Parámetros: id, mantenimiento_id
├─ Respuesta: Descarga con headers correctos
└─ Mime type automático
```

### Permisos Requeridos
```
✅ mantenimientos.view     → Ver listado y detalles
✅ mantenimientos.edit     → Crear, editar, cambiar estado
```

---

## 📐 CALIBRACIONES

### Rutas del Módulo

| Ruta | Método | Archivo | Función |
|------|--------|---------|---------|
| `calibraciones` | GET | `calibraciones/index.php` | Listado de calibraciones |
| `calibracion_form` | GET/POST | `calibraciones/form.php` | Crear/editar |
| `calibracion_detalle` | GET | `calibraciones/detalle.php` | Ver completo |
| `calibracion_puntos` | GET/POST | `calibraciones/puntos.php` | Gestionar puntos medida |
| `calibracion_certificado` | GET | `calibraciones/certificado_print.php` | Ver/imprimir certificado |
| `calibracion_certificado_edit` | GET/POST | `calibraciones/certificado_edit.php` | Editar certificado |
| `calibracion_verificar` | GET | `calibraciones/verificar.php` | **[PÚBLICA]** Ver sin login |

### Funcionalidades Detalladas

#### 1. Listado (`calibraciones`)
```
Tabla con columnas:
├─ Nro. Certificado
├─ Activo
├─ Patrón utilizado
├─ Fecha calibración
├─ Próxima calibración
├─ Estado (CONFORME/NO_CONFORME/EN_CALIBRACION)
├─ Técnico responsable
└─ Acciones

Filtros:
✅ Por estado
✅ Por rango fechas
✅ Por técnico
✅ Por patrón
✅ Por conformidad
```

#### 2. Formulario Calibración (`calibracion_form`)
```
Sección 1: Identificación
├─ Activo calibrable *
├─ Patrón utilizado *
└─ Técnico responsable *

Sección 2: Fechas
├─ Fecha calibración *
├─ Próxima calibración *
└─ Vigencia (autocalculada)

Sección 3: Certificado
├─ Nro. certificado *
├─ Archivo PDF/imagen
├─ Norma aplicada
└─ Rango de tolerancia

Sección 4: Puntos de Medida
├─ Tabla editable
├─ Valor especificado
├─ Valor medido
├─ Diferencia
├─ Dentro tolerancia (SI/NO)
└─ Observaciones

Sección 5: Resultado Final
├─ Estado (CONFORME/NO_CONFORME)
└─ Observaciones generales

Validaciones:
✅ Próxima >= Calibración
✅ Activo en vw_activos_calibrables
✅ Puntos dentro tolerancia
```

#### 3. Gestión de Puntos (`calibracion_puntos`)
```
Puntos de Medida:
├─ Parámetro medible
├─ Valor estándar (del patrón)
├─ Unidad de medida
├─ Tolerancia (±)
└─ Rango aceptable

Operaciones:
✅ Agregar punto
✅ Editar punto
✅ Eliminar punto
✅ Reordenar
✅ Validar contra patrón

AJAX Helper:
└─ patron_puntos.php → GET puntos del patrón
```

#### 4. Certificado (`calibracion_certificado`)
```
Documento profesional que incluye:
├─ Encabezado empresa
├─ Datos activo
├─ Datos patrón
├─ Tabla de puntos medidos
├─ Gráficos de conformidad
├─ Firma técnico
├─ Firma cliente/responsable
├─ Fecha vigencia
├─ Código QR público
└─ Notas técnicas

Permite:
✅ Ver en pantalla
✅ Imprimir a PDF
✅ Descargar como HTML
✅ Compartir por email (token)
```

#### 5. Verificación Pública (`calibracion_verificar`)
```
🔓 RUTA PÚBLICA - NO REQUIERE LOGIN

URL: ?route=calibracion_verificar&token=ABC123

Acceso:
├─ Por token único (seguro)
├─ Muestra solo datos certificado
├─ Valida vigencia
├─ Indicador de conformidad
└─ Descarga cert PDF

Datos mostrados:
✅ Nro. certificado
✅ Activo
✅ Fecha calibración
✅ Próxima calibración
✅ Estado (CONFORME/NO)
✅ Firma técnico
└─ Puntos medidos (tabla)
```

### APIs AJAX Calibraciones

```
GET /app/ajax/patron_puntos.php
├─ Parámetros: patron_id
├─ Respuesta: [{id, parametro, valor, tolerancia, unidad}, ...]
└─ Llena tabla de puntos automáticamente

POST /app/ajax/ajax_cal_adj_upload.php
├─ Parámetros: file, calibracion_id
├─ Respuesta: {success, id, path}
└─ Certificados, fotos, documentos

POST /app/ajax/ajax_cal_adj_delete.php
├─ Parámetros: adjunto_id, calibracion_id
├─ Respuesta: {success}
└─ Elimina adjunto

GET /app/ajax/ajax_cal_adj_download.php
├─ Parámetros: id, calibracion_id
├─ Respuesta: Descarga
└─ Verifica permisos

GET /app/ajax/ajax_cal_adj_preview.php
├─ Parámetros: id, calibracion_id
├─ Respuesta: Vista previa inline
└─ PDF, IMG, etc

POST /app/ajax/ajax_cal_firma_tecnico.php
├─ Parámetros: calibracion_id
├─ Respuesta: {imagen_firma_base64}
└─ Pad de firma táctil o digital

POST /app/ajax/ajax_cal_firma_recibido.php
├─ Parámetros: calibracion_id
├─ Respuesta: {imagen_firma_base64}
└─ Firma de cliente/testigo
```

### Permisos Requeridos
```
✅ calibraciones.view   → Ver listado y detalles
✅ calibraciones.edit   → Crear, editar, certificar
```

---

## ⚙️ CONFIGURACIÓN (Maestros)

### Rutas del Módulo

| Ruta | Método | Archivo | Función |
|------|--------|---------|---------|
| `categorias` | GET | `config/categorias/...` | Listado categorías |
| `categoria_form` | GET/POST | `config/categorias/form.php` | Crear/editar |
| `tipos_activo` | GET | `config/tipos_activo/...` | Tipos de activos |
| `tipo_activo_form` | GET/POST | `config/tipos_activo/form.php` | Crear/editar |
| `marcas` | GET | `config/marcas/...` | Listado marcas |
| `marca_form` | GET/POST | `config/marcas/form.php` | Crear/editar |
| `sedes` | GET | `config/sedes/...` | Listado sedes |
| `sede_form` | GET/POST | `config/sedes/form.php` | Crear/editar |
| `areas` | GET | `config/areas/...` | Listado de áreas |
| `area_form` | GET/POST | `config/areas/form.php` | Crear/editar |
| `proveedores` | GET | `config/proveedores/...` | Listado proveedores |
| `proveedor_form` | GET/POST | `config/proveedores/form.php` | Crear/editar |

### Funcionalidades por Maestro

#### CATEGORÍAS
```
Campos:
├─ Nombre *
├─ Descripción
└─ Estado

Propósito:
✅ Clasificar activos (Equipos, Maquinaria, etc)
✅ Definir reglas por categoría
├─ Requiere calibración
├─ Periodo mantenimiento
├─ Vida útil esperada
└─ Costos promedio

AJAX: tipo_reglas.php
└─ Obtiene reglas cuando cambian categoría
```

#### TIPOS DE ACTIVO
```
Campos:
├─ Código
├─ Nombre
├─ Categoría (FK)
├─ Especificaciones técnicas
└─ Estado

Propósito:
✅ Subtipificación dentro categoría
✅ Ej: Categoría "EQUIPOS"
       │
       ├─ Tipo: "Osciloscopio"
       ├─ Tipo: "Multímetro"
       └─ Tipo: "Fuente Alimentación"
```

#### MARCAS
```
Campos:
├─ Nombre *
├─ Código (opcional)
├─ Website
└─ Estado

Propósito:
✅ Registrar fabricantes
✅ Asociar a activos
✅ Seguimiento de calidad por marca
```

#### SEDES
```
Campos:
├─ Nombre *
├─ Dirección
├─ Ciudad
├─ Teléfono
└─ Responsable

Propósito:
✅ Ubicaciones principales
✅ Organización geográfica
✅ Puntos de mantenimiento
```

#### ÁREAS
```
Campos:
├─ Nombre *
├─ Sede (FK) *
├─ Responsable
└─ Descripción

Propósito:
✅ Departamentos dentro sede
✅ Ej: Sede Centro
       │
       ├─ Area: Laboratorio
       ├─ Area: Producción
       └─ Area: Almacén
```

#### PROVEEDORES
```
Campos:
├─ Nombre *
├─ NIT
├─ Email
├─ Teléfono
├─ Contacto
└─ Servicios

Propósito:
✅ Técnicos y servicios
✅ Ej: Empresa mantenimiento XYZ
✅ Registro de garantías
✅ Historial de trabajos
```

### APIs AJAX Configuración

```
POST /app/ajax/tipo_reglas.php
├─ Parámetros: tipo_activo_id
├─ Respuesta: {
│    requiere_calibracion: bool,
│    periodo_mantenim: int (días),
│    vida_util: int (años),
│    costo_mant_anual: decimal
│  }
└─ Usado en formulario activos
```

---

## 👥 USUARIOS & ROLES

### Rutas del Módulo

| Ruta | Método | Archivo | Función |
|------|--------|---------|---------|
| `usuarios` | GET | `usuarios/index.php` | Listado usuarios |
| `usuario_form` | GET/POST | `usuarios/form.php` | Crear/editar |
| `roles` | GET | `roles/index.php` | Listado roles |
| `rol_form` | GET/POST | `roles/form.php` | Crear/editar |
| `rol_permisos` | GET/POST | `roles/permisos.php` | Asignar permisos |

### Funcionalidades Detalladas

#### USUARIOS (`usuario_form`)
```
Campos:
├─ Nombre *
├─ Email * (unique per tenant)
├─ Rol * (FK roles)
├─ Contraseña (solo crear)
├─ Confirmar contraseña
├─ Estado (ACTIVO/INACTIVO)
├─ Teléfono (opcional)
└─ Foto de perfil (opcional)

Validaciones:
✅ Email válido
✅ Email único por tenant
✅ Contraseña mín 8 caracteres
✅ Contraseña + mayúsculas + números + símbolos
✅ Rol debe existir

Operaciones:
✅ Crear usuario
✅ Editar datos (sin cambiar pass)
✅ Cambiar contraseña
✅ Desactivar usuario
✅ Eliminar usuario (soft delete)
```

#### ROLES (`rol_form`)
```
Campos:
├─ Nombre * (unique per tenant)
├─ Descripción
├─ Es superadmin (checkbox)
└─ Estado

Propósito:
✅ Agrupar permisos
✅ Ej: ADMIN, TECNICO, SUPERVISOR, CLIENTE

Roles predefinidos:
├─ ADMIN     → todos los permisos
├─ TECNICO   → calibraciones, mantenimientos
├─ SUPERVISOR → solo lectura con reportes
└─ CLIENTE   → acceso limitado (solo sus activos)
```

#### PERMISOS (`rol_permisos`)
```
Estructura:
├─ Rol (FK roles)
└─ Lista de permisos disponibles

Permisos principales:
├─ dashboard.view
├─ activos.view
├─ activos.edit
├─ mantenimientos.view
├─ mantenimientos.edit
├─ calibraciones.view
├─ calibraciones.edit
├─ usuarios.manage
├─ roles.manage
├─ empresas.manage (solo superadmin)
└─ auditoria.view

Operaciones:
✅ Seleccionar/deseleccionar permisos
✅ Guardar combinación
✅ Copiar de otro rol
```

---

## 📋 AUDITORÍA

### Rutas del Módulo

| Ruta | Método | Archivo | Función |
|------|--------|---------|---------|
| `auditoria` | GET | `auditoria/audit_log.php` | Log general de cambios |
| `usuario_auditoria` | GET | `auditoria/auditoria_usuario.php` | Log por usuario |
| `activo_auditoria` | GET | `auditoria/activo_auditoria.php` | Historial de activo |
| `timeline` | GET | `auditoria/timeline.php` | Vista cronológica |

### Funcionalidades

```
Tabla audit_log:
├─ Quién: usuario_id + nombre
├─ Qué: tabla + acción (INSERT/UPDATE/DELETE)
├─ Cuál: registro_id
├─ Cuándo: creado_en (datetime)
└─ Cambios: JSON con datos anteriores/nuevos

Vistas disponibles:
✅ Log general (todas las acciones)
├─ Filtrar por tabla
├─ Filtrar por usuario
├─ Filtrar por fechas
└─ Filtrar por acción

✅ Por usuario (auditoría de usuario)
├─ Qué cambios realizó
├─ Cuándo los realizó
└─ Detalle de valores

✅ Por activo (historial completo)
├─ Todos los cambios del activo
├─ Quién los hizo
├─ Cuándo
└─ Valores antes/después

✅ Timeline (vista cronológica)
├─ Ordenado por fecha
├─ Mostrar todos los cambios
├─ Gráfica de actividad
```

---

## 🏢 EMPRESAS (Solo SUPERADMIN)

### Rutas del Módulo

| Ruta | Método | Archivo | Función |
|------|--------|---------|---------|
| `empresas` | GET | `empresas/index.php` | Listado de tenants |
| `empresa_form` | GET/POST | `empresas/form.php` | Crear/editar |

### Funcionalidades

```
Campos:
├─ Nombre *
├─ NIT (opcional)
├─ Email
├─ Teléfono
├─ Dirección
├─ Ciudad
├─ Estado (ACTIVO/SUSPENDIDO)
└─ Plan (si hay multiplan)

Operaciones (Solo SUPERADMIN):
✅ Ver todos los tenants
✅ Crear nuevo cliente
✅ Editar datos
✅ Suspender/Activar
✅ Ver datos de uso
✅ Resetear contraseña admin
```

---

## 🎯 PATRONES

### Rutas del Módulo

| Ruta | Método | Archivo | Función |
|------|--------|---------|---------|
| `patrones` | GET | `patrones/index.php` | Listado de patrones |
| `patron_form` | GET/POST | `patrones/form.php` | Crear/editar |
| `patron_delete` | POST | `patrones/delete.php` | Eliminar |

### Funcionalidades

```
Campos:
├─ Código * (ej: PATRON-001)
├─ Nombre *
├─ Certificado (archivo/nro)
├─ Incertidumbre
├─ Rango de medida
├─ Puntos calibración
└─ Período validez

Puntos de Medida:
├─ Parámetro
├─ Valor estándar
├─ Unidad
├─ Tolerancia
└─ Aceptable (SI/NO)

Operaciones:
✅ Crear patrón
✅ Subir certificado ISO
✅ Definir puntos
✅ Usar en calibraciones
✅ Ver historial de uso
```

### APIs AJAX Patrones

```
GET /app/ajax/patron_puntos.php
└─ Ya documentado en sección CALIBRACIONES

POST /app/ajax/ajax_patron_cert_upload.php
├─ Parámetros: file, patron_id
└─ Almacena certificado

POST /app/ajax/ajax_patron_cert_delete.php
├─ Parámetros: cert_id
└─ Elimina certificado

GET /app/ajax/ajax_patron_cert_download.php
├─ Parámetros: cert_id
└─ Descarga

GET /app/ajax/ajax_patron_cert_preview.php
├─ Parámetros: cert_id
└─ Vista previa
```

---

## 🔩 COMPONENTES

### Rutas del Módulo

| Ruta | Método | Archivo | Función |
|------|--------|---------|---------|
| `componentes` | GET/POST | `componentes/componente_form.php` | Gestionar |
| `componente_delete` | POST | `componentes/delete.php` | Eliminar |

### Funcionalidades

```
Componentes:
├─ Partes que forman un activo
├─ Ej: Motor, Estructura, Panel de control

Campos:
├─ Nombre *
├─ Serie/Nro de parte
├─ Activo (FK)
├─ Categoría componente
├─ Proveedor
└─ Fecha instalación

Operaciones:
✅ Agregar componentes a activo
✅ Ver lista de componentes
✅ Editar datos
✅ Eliminar componente
✅ Historial de cambios
```

---

## 🚀 Acceso a Rutas

### Por Navegación (UI)
```
Usuario siempre accede a través del menú sidebar
ubicado en app/views/layout/sidebar.php
```

### Por URL Directa
```
http://localhost/geoactivos/public/index.php?route=RUTA
```

### Parámetros GET Comunes
```
id=123              → ID del recurso
activo_id=45        → FK activo
accion=editar       → Acción específica
tipo=preventivo     → Filtro o tipo
estado=activo       → Estado a filtrar
```

---

## 📊 Resumen de Rutas

| Total | Tipo | Cantidad |
|-------|------|----------|
| **Rutas** | Públicas | 3 |
|  | Privadas | 50+ |
|  | Total | 53+ |
| **APIs AJAX** | Activos | 7 |
|  | Mantenimientos | 3 |
|  | Calibraciones | 8 |
|  | Config | 1 |
|  | Patrones | 4 |
|  | Total | 25+ |
| **Módulos** | Funcionales | 10 |

---

**Documento Generado:** 3 de Marzo de 2026  
**Ruta Total Documentada:** 53+ rutas  
**APIs AJAX Documentadas:** 25+  
📍 **Estado:** ✅ Completo

# 🔄 FLUJOS Y DIAGRAMAS DE PROCESOS

**Documento:** Análisis visual de flujos y procesos  
**Fecha:** 3 de Marzo de 2026  
**Versión:** 1.0  

---

## 📊 Índice de Diagramas

1. [Flujo de Loguearse](#flujo-de-loguearse)
2. [Flujo de Creación de Activo](#flujo-de-creación-de-activo)
3. [Flujo de Mantenimiento](#flujo-de-mantenimiento)
4. [Flujo de Calibración](#flujo-de-calibración)
5. [Arquitectura de BD](#arquitectura-de-bd)
6. [Casos de Uso Principal](#casos-de-uso-principal)
7. [Flujo de Permisos](#flujo-de-permisos)
8. [Ciclo de Auditoría](#ciclo-de-auditoría)
9. [Arquitectura de Directorios](#arquitectura-de-directorios)
10. [Flujo AJAX](#flujo-ajax)

---

## 🔐 FLUJO DE LOGUEARSE

```
┌─────────────────────────────────────────────────────────────────┐
│                    USUARIO ACCEDE A LOGIN                       │
│          http://localhost/geoactivos/public/index.php           │
└────────────────────────────┬──────────────────────────────────┘
                             │
                      GET ?route=login
                             │
                ┌────────────▼──────────────────┐
                │  auth/login.php               │
                │  Muestra formulario HTML      │
                │  ├─ Email input               │
                │  ├─ Password input            │
                │  └─ Login button              │
                └────────────┬───────────────────┘
                             │
                      Usuario completa
                      email y password
                             │
                ┌────────────▼──────────────────┐
                │  POST /index.php?route=login  │
                │  Recibe email y password      │
                └────────────┬───────────────────┘
                             │
                ┌────────────▼──────────────────┐
                │  Auth::attempt($email, $pass) │
                │  Clase core/Auth.php          │
                └────────────┬───────────────────┘
                             │
        ┌────────────────────┼────────────────────┐
        │                    │                    │
        ▼                    ▼                    ▼
    NO EXISTE          EXISTE INACTIVO      EXISTE ACTIVO
    usuario o                                      │
    contraseña          └─ Devuelve err    ┌──────▼──────┐
    incorrecto              "Usuario       │ Verificar   │
        │                    inactivo"     │ contraseña  │
        │                                  │ con bcrypt  │
        └──────────────┐                   └──────┬──────┘
                       │                          │
                       │                    ┌─────┴────────┐
                       │                    │              │
                       │                    ▼              ▼
                       │                 ✅ CORRECTO    ❌ INCORRECTO
                       │                    │              │
                       │     ┌──────────────┘              │
                       │     │                             │
                       │     ▼                             │
                       │  Verificar tenant           ┌─────▼───────┐
                       │  estado = ACTIVO            │ Devuelve    │
                       │     │                        │ error       │
                       │     │                        │ "Usuario o  │
                       │     ▼                        │ contraseña  │
                       │ ¿Tenant está activo?        │ incorrectos"│
                       │     │                        └─────┬───────┘
                       │ ┌───┴────┐                        │
                       │ │        ▼                        │
                       │ │    NO → Devuelve error          │
                       │ │    "Cliente suspendido"         │
                       │ │                                 │
                       │ ▼                                 │
                       │ SÍ                                │
                       │     │                             │
                       ▼     ▼                             │
        ┌──────────────────────────────────┐  ┌──────────┴──────────┐
        │ Almacenar en $_SESSION['user']    │  │ Mostrar forma con   │
        │ ├─ id                             │  │ error en rojo       │
        │ ├─ nombre                         │  │ └─ Permanece en /   │
        │ ├─ email                          │   │    login
        │ ├─ tenant_id                      │  │
        │ ├─ rol_id                         │  │
        │ └─ rol_nombre                     │  │
        └────────────┬──────────────────────┘  │
                     │                         │
        ┌────────────▼────────────┐            │
        │ Auth::loadPerms(true)   │            │
        │ Carga permisos en       │            │
        │ $_SESSION['perms']      │            │
        └────────────┬────────────┘            │
                     │                        │
        ┌────────────▼────────────┐           │
        │ Redirir a /dashboard    │           │
        │ header('Location: ...')  │           │
        └────────────┬────────────┘           │
                     │                        │
                     │ ✅ ÉXITO              ❌ ERROR
                     │  │                     │
                     ▼  ▼                     ▼
              ┌──────────────────────────┐
              │ public/index.php         │
              │ ?route=dashboard         │
              │ Carga dashboard/         │
              │ index.php                │
              │                          │
              │ Muestra panel con datos  │
              └────────────┬─────────────┘
                           │
                    ✅ Usuario logueado
                       Sesión iniciada
```

---

## 🎁 FLUJO DE CREACIÓN DE ACTIVO

```
┌──────────────────────────────────────────────────────────┐
│  Usuario en /activos (VIEW)                              │
│  Sección: app/views/activos/index.php                    │
│  ├─ Tabla con listado actual                             │
│  └─ Botón "Nuevo" en esquina superior derecha            │
└────────────────────────┬─────────────────────────────────┘
                         │
                    CLICK en "Nuevo"
                    href=?route=activos_form
                         │
          ┌──────────────▼──────────────────┐
          │ GET ?route=activos_form         │
          │ public/index.php router         │
          │ ├─ Valida Auth::requirePerm()   │
          │ └─ Carga activos/form.php       │
          └──────────────┬───────────────────┘
                         │
          ┌──────────────▼──────────────────┐
          │ form.php (FORMULARIO EN BLANCO) │
          │ ├─ Campos vacíos:               │
          │ │  ├─ Código interno            │
          │ │  ├─ Nombre                    │
          │ │  ├─ Modelo                    │
          │ │  ├─ Serial                    │
          │ │  ├─ Categoría (SELECT)        │
          │ │  ├─ Marca (SELECT)            │
          │ │  ├─ Proveedor (SELECT)        │
          │ │  ├─ Área (SELECT)             │
          │ │  ├─ Fecha compra              │
          │ │  ├─ Fecha instalación         │
          │ │  ├─ Garantía hasta            │
          │ │  └─ Observaciones (TEXTAREA)  │
          │ ├─ AJAX Helper:                 │
          │ │  └─ next_codigo_activo.php    │
          │ │     Sugiere próximo código    │
          │ └─ Botón Guardar                │
          └──────────────┬───────────────────┘
                         │
                   Usuario completa
                   todos los datos
                         │
          ┌──────────────▼──────────────────┐
          │  POST form → form.php            │
          │  Recibe datos: [array]           │
          │  $_POST['codigo_interno']        │
          │  $_POST['nombre']                │
          │  ... todos los campos ...        │
          └──────────────┬───────────────────┘
                         │
          ┌──────────────▼──────────────────┐
          │ BACKEND: PROCESAMIENTO           │
          │ 1) Obtener tenant_id             │
          │    $tenantId = Auth::tenantId() │
          │ 2) Validar datos               │
          │    ├─ Código único?             │
          │    ├─ Campos obligatorios?      │
          │    ├─ Fechas válidas?           │
          │    └─ FK válidas? (categ, etc)  │
          └──────────────┬───────────────────┘
                         │
          ┌──────────────▼──────────────────┐
          │ ✅ VALIDACIONES PASADAS         │
          │ INSERT INTO activos             │
          │ VALUES (                        │
          │   NULL, -- id (auto)            │
          │   $tenantId, -- multi-tenant    │
          │   $categoria_id,                │
          │   $marca_id,                    │
          │   $area_id,                     │
          │   $codigo_interno,              │
          │   $nombre,                      │
          │   ... resto de valores ...      │
          │ )                               │
          └──────────────┬───────────────────┘
                         │
          ┌──────────────▼──────────────────┐
          │ INSERT AUDIT_LOG                │
          │ Registro quién creó             │
          │ ├─ usuario_id                   │
          │ ├─ tabla: 'activos'             │
          │ ├─ accion: 'INSERT'             │
          │ ├─ registro_id: [id nuevo]      │
          │ └─ cambios: JSON de valores     │
          └──────────────┬───────────────────┘
                         │
          ┌──────────────▼──────────────────┐
          │ REDIRECT 302                    │
          │ Location: ?route=activos        │
          │ (Redirecciona a listado)        │
          └──────────────┬───────────────────┘
                         │
          ┌──────────────▼──────────────────┐
          │ Recarga: activos/index.php      │
          │ SELECT * FROM activos           │
          │ WHERE tenant_id = :t            │
          │                                 │
          │ Tabla ahora muestra             │
          │ ✅ NUEVO ACTIVO en la lista     │
          └──────────────────────────────┘
```

---

## 🔧 FLUJO DE MANTENIMIENTO

```
┌───────────────────────────────────────────────────────┐
│  Usuario en app/views/activos/detalle.php             │
│  Viendo detalles de un activo específico              │
└────────────────────┬──────────────────────────────────┘
                     │
            SECCIÓN: Mantenimientos
            ├─ Historial de trabajos
            ├─ Próximos programados
            └─ Botón: "Nuevo Mantenimiento"
                     │
      ┌──────────────▼──────────────┐
      │ GET ?route=mantenimiento_    │
      │ form&activo_id=5             │
      │                              │
      │ Carga:                       │
      │ mantenimientos/form.php      │
      └──────────────┬───────────────┘
                     │
      ┌──────────────▼──────────────────────┐
      │  FORMULARIO MANTENIMIENTO            │
      │  (Selectores pre-cargados)           │
      │  ├─ Activo: [Auto-filled] (5)        │
      │  ├─ Tipo*: [SELECT]                  │
      │  │  ├─ PREVENTIVO (por programa)     │
      │  │  ├─ CORRECTIVO (falla)            │
      │  │  └─ PREDICTIVO (análisis futuro)  │
      │  ├─ Estado*: PROGRAMADO              │
      │  ├─ Prioridad*: [SELECT]             │
      │  │  ├─ BAJA                          │
      │  │  ├─ MEDIA                         │
      │  │  ├─ ALTA                          │
      │  │  └─ CRITICA                       │
      │  ├─ Falla (descrip.)                 │
      │  ├─ Actividades (detalles trabajo)   │
      │  ├─ Recomendaciones                  │
      │  ├─ Costo mano de obra               │
      │  ├─ Costo repuestos                  │
      │  ├─ Fechas:                          │
      │  │  ├─ Programada                    │
      │  │  ├─ Inicio                        │
      │  │  └─ Fin                           │
      │  └─ Botón Guardar                    │
      └──────────────┬───────────────────────┘
                     │
            Usuario completa y guarda
                     │
      ┌──────────────▼──────────────────────┐
      │  POST /mantenimientos/form.php       │
      │  Backend procesa:                    │
      │  1) Validar datos                   │
      │  2) Checkear tenant_id              │
      │  3) Verificar activo existe         │
      │  4) Guardar en BD                   │
      └──────────────┬───────────────────────┘
                     │
      ┌──────────────▼──────────────────────┐
      │  INSERT INTO mantenimientos          │
      │  VALUES (                            │
      │    NULL, -- id auto                  │
      │    $tenant_id,                       │
      │    $activo_id,                       │
      │    'PREVENTIVO', -- tipo             │
      │    'PROGRAMADO', -- estado inicial   │
      │    $fecha_prog, $fecha_inicio, ... │
      │  )                                   │
      └──────────────┬───────────────────────┘
                     │
      ┌──────────────▼──────────────────────┐
      │  INSERT INTO audit_log               │
      │  Registrar creación:                 │
      │  ├─ usuario_id: [Auth::userId()]    │
      │  ├─ tabla: 'mantenimientos'          │
      │  ├─ accion: 'INSERT'                 │
      │  └─ registro_id: [nuevo id]          │
      └──────────────┬───────────────────────┘
                     │
            CICLO DE VIDA DEL MANTENIMIENTO
                     │
      ┌──────────────▼──────────────────────┐
      │  Estado: PROGRAMADO                  │
      │  └─ Aparece en calendario           │
      │     (próximos a realizar)            │
      └──────────────┬───────────────────────┘
                     │
      ┌──────────────▼──────────────────────┐
      │  Técnico inicia trabajo              │
      │  UPDATE mantenimientos               │
      │  SET estado = 'EN_PROCESO',          │
      │      fecha_inicio = NOW()            │
      │  WHERE id = :id                      │
      └──────────────┬───────────────────────┘
                     │
      ┌──────────────▼──────────────────────┐
      │  Registrar:                          │
      │  ├─ Que se hizo (actividades)        │
      │  ├─ Repuestos utilizados             │
      │  ├─ Fotos de trabajo (AJAX upload)   │
      │  └─ Certificados de prueba           │
      └──────────────┬───────────────────────┘
                     │
      ┌──────────────▼──────────────────────┐
      │  Cerrar mantenimiento                │
      │  UPDATE mantenimientos               │
      │  SET estado = 'CERRADO',             │
      │      fecha_fin = NOW(),              │
      │      costo_mano_obra = X,            │
      │      costo_repuestos = Y             │
      └──────────────┬───────────────────────┘
                     │
      ┌──────────────▼──────────────────────┐
      │  Obtener Firmas:                     │
      │  ├─ Técnico responsable (AJAX)       │
      │  └─ Cliente/Responsable (AJAX)       │
      │  (Pad táctil o digital)              │
      └──────────────┬───────────────────────┘
                     │
      ┌──────────────▼──────────────────────┐
      │  Generar certificado de servicio     │
      │  (mantenimiento_print.php)           │
      │  Incluye:                            │
      │  ├─ Datos técnicos                   │
      │  ├─ Trabajo realizado                │
      │  ├─ Costos                           │
      │  ├─ Firmas                           │
      │  └─ QR (verificación pública opt.)   │
      │                                      │
      │  Puede: Imprimir / Descargar / Email │
      └──────────────┬───────────────────────┘
                     │
                 ✅ CERRADO
            Mantenimiento documentado
            Auditoría registrada
            Cliente tiene comprobante
```

---

## 📐 FLUJO DE CALIBRACIÓN

```
┌─────────────────────────────────────────────────┐
│  Usuario en calibraciones/index.php              │
│  Listado de calibraciones realizadas             │
│  Click en "Nueva Calibración"                    │
└──────────────────┬────────────────────────────┘
                   │
      ┌────────────▼──────────────┐
      │ GET ?route=calibracion_   │
      │ form                       │
      │                            │
      │ Carga:                     │
      │ calibraciones/form.php     │
      └────────────┬───────────────┘
                   │
      ┌────────────▼──────────────────────┐
      │  FORMULARIO CALIBRACIÓN            │
      │  SECCIÓN 1: Identificación         │
      │  ├─ Activo*: SELECT                │
      │  │  └─ SOLO mostrar calibrables    │
      │  │     (función: vw_activos_      │
      │  │      calibrables)               │
      │  ├─ Patrón*: SELECT                │
      │  │  └─ Referencia de medida        │
      │  └─ Técnico*: [Usuario actual]     │
      │                                    │
      │  SECCIÓN 2: Fechas                 │
      │  ├─ Fecha calibración* [HOY]       │
      │  ├─ Próxima calibración*           │
      │  │  └─ Auto: Fecha calibración     │
      │  │     + X meses (configurable)    │
      │  └─ Vigencia: (mostrada)           │
      │                                    │
      │  SECCIÓN 3: Certificado            │
      │  ├─ Nro. certificado*              │
      │  ├─ Archivo (PDF/IMG)              │
      │  ├─ Norma aplicada (ISO 17025)     │
      │  └─ Rango tolerancia               │
      │                                    │
      │  SECCIÓN 4: Puntos de Medida       │
      │  ├─ Tabla editable                 │
      │  ├─ AJAX: patron_puntos.php        │
      │  │  └─ Obtiene puntos del patrón   │
      │  ├─ Columnas:                      │
      │  │  ├─ Parámetro                   │
      │  │  ├─ Unidad                      │
      │  │  ├─ Valor estándar              │
      │  │  ├─ Valor medido                │
      │  │  ├─ Diferencia (auto calc)      │
      │  │  ├─ Tolerancia                  │
      │  │  └─ ✅/❌ (dentro de rango)     │
      │  └─ Botón: Agregar punto           │
      │                                    │
      │  SECCIÓN 5: Resultado Final        │
      │  ├─ Estado*:                       │
      │  │  ├─ CONFORME                    │
      │  │  │  └─ (Todos puntos OK)        │
      │  │  ├─ NO_CONFORME                 │
      │  │  │  └─ (Algún punto fuera)      │
      │  │  └─ EN_CALIBRACION              │
      │  │     └─ (Pendiente completar)    │
      │  └─ Observaciones                  │
      │                                    │
      │  └─ Botón Guardar                  │
      └────────────┬───────────────────────┘
                   │
            Usuario completa datos
            Carga certificado
            Completa puntos de medida
                   │
      ┌────────────▼───────────────────┐
      │  POST /calibracion_form.php     │
      │  Backend procesa:               │
      │  1) Validar datos               │
      │  2) require_activo_calibrable() │
      │  3) Verificar puntos            │
      │  4) Calcular conformidad        │
      └────────────┬───────────────────┘
                   │
      ┌────────────▼─────────────────────────┐
      │  INSERT INTO calibraciones           │
      │  VALUES (                            │
      │    NULL, -- id auto                  │
      │    $tenant_id,                       │
      │    $activo_id,                       │
      │    $patron_id,                       │
      │    $tecnico_id,                      │
      │    $fecha_calibracion,               │
      │    $fecha_proxima,                   │
      │    $estado, -- CONFORME / NO_CONF    │
      │    $nro_certificado                  │
      │  )                                   │
      └────────────┬─────────────────────────┘
                   │
      ┌────────────▼─────────────────────┐
      │  INSERT calibracion_puntos (x)    │
      │  Para cada punto:                 │
      │  ├─ calibracion_id                │
      │  ├─ parametro                     │
      │  ├─ valor_especificado            │
      │  ├─ valor_medido                  │
      │  ├─ diferencia (calc)             │
      │  └─ dentro_tolerancia (SI/NO)     │
      └────────────┬─────────────────────┘
                   │
      ┌────────────▼──────────────────┐
      │  INSERT audit_log              │
      │  Registro de creación          │
      └────────────┬──────────────────┘
                   │
      ┌────────────▼──────────────────────┐
      │  GENERAR CERTIFICADO PROFESIONAL   │
      │  calibracion_certificado_print.php │
      │  Documento PDF que incluye:        │
      │  ├─ Encabezado empresa             │
      │  ├─ Logo                           │
      │  ├─ Datos activo                   │
      │  ├─ Datos patrón                   │
      │  ├─ Tabla completa puntos          │
      │  ├─ Gráficos de conformidad        │
      │  ├─ Firma técnico (AJAX)           │
      │  ├─ Fecha y hora                   │
      │  ├─ Código QR único (público)      │
      │  └─ Observaciones técnicas         │
      └────────────┬──────────────────────┘
                   │
             ✅ CERTIFICADO LISTO
                   │
      ┌────────────▼──────────────────────┐
      │  OPCIONES para CLIENTE:            │
      │  ├─ 📥 Descargar PDF               │
      │  ├─ 🖨️ Imprimir                     │
      │  ├─ 📧 Enviar por email            │
      │  └─ 🌐 Enlace público (QR)         │
      │                                    │
      │  Enlace:                           │
      │  ?route=calibracion_verificar      │
      │  &token=[UNIQUE_TOKEN]             │
      │  => Esta ruta es PÚBLICA (sin login)
      └────────────────────────────────────┘
```

---

## 🗄️ ARQUITECTURA DE BD (Simplificada)

```
                    TENANTS (Raíz multi-tenant)
                           │ 1:N
         ┌─────────────────┼─────────────────┐
         │                 │                 │
         ▼                 ▼                 ▼
    USUARIOS           ROLES         CATEGORIAS_ACTIVO
    ├─ FK: role_id  ├─ N permisos      ├─ N activos
    │               └─ (rol_permisos) │
    │                                  │
    ▼                                  ▼
 SESIONES                          ACTIVOS (Núcleo)
                                  ├─ K: codigo_interno
                          ┌───────┼─────────────┬────────┐
                          │       │             │        │
                      Fotos   Adjuntos   Mantenimientos  │
                                             │           │
                                         Archivos        │
                                                         │
                                            CALIBRACIONES
                                            ├─ M: patrón
                                            ├─ Puntos
                                            └─ Certificados

     RELACIONES CLAVE:
        tenant_id ◄── Presente en TODAS las tablas
        FK cadena: tenant → usuarios/roles → permisos
        FK cadena: tenant → activos → mantenimientos/calibraciones
```

---

## 🎭 CASOS DE USO PRINCIPAL

### CASO 1: Registrar Nuevo Activo

```
Actor: Técnico/Administrador
Precondición: Logueado, permisos activos.edit

Flujo Principal:
  1. Va a /activos
  2. Haz click "Nuevo"
  3. Completa formulario:
     └─ Código (auto-generado opcionalmente)
     └─ Nombre SI
     └─ Categoría SI
     └─ Marca (opcional, puede añadirse)
     └─ Proveedor (opcional)
     └─ Fechas de compra e instalación
  4. Click "Guardar"
  5. Redirige a /activos viendo el nuevo en lista

Postcondición: 
  - Activo existe en BD
  - Auditoría registrada
  - Disponible para mantenimiento y calibración
```

---

### CASO 2: Programar Mantenimiento Preventivo

```
Actor: Supervisor/Técnico
Precondición: Activo existe, user permisos mantenimientos.edit

Flujo Principal:
  1. Ve activo en detalles
  2. Sección "Mantenimientos" → "Nuevo"
  3. Selecciona:
     └─ Tipo: PREVENTIVO
     └─ Fecha programada: 2026-03-15
     └─ Prioridad: MEDIA
     └─ Descripción: "Limpieza trimestral"
  4. Click "Guardar"
  5. Aparece en calendario como "Próximos"

Postcondición:
  - Mantenimiento visible en dashboard
  - Auditoría registrada
  - Puede editarse hasta ejecución
```

---

### CASO 3: Ejecutar Mantenimiento (Completo)

```
Actor: Técnico de campo
Precondición: Mantenimiento programado, status = PROGRAMADO

Flujo Principal:
  1. Accede a mantenimiento desde móvil/tablet
  2. Cambia estado a EN_PROCESO
  3. Durante trabajo:
     └─ Toma fotos (AJAX upload)
     └─ Registra actividades
     └─ Anota repuestos usados
     └─ Sube certificados de prueba
  4. Termina, cambia a CERRADO
  5. Completa costos:
     └─ Costo mano obra: $200
     └─ Costo repuestos: $150
     └─ Total: $350
  6. Obtiene firma cliente (AJAX signature pad)
  7. Genera PDF con todo

Postcondición:
  - Mantenimiento cerrado
  - Cliente tiene comprobante
  - Datos para auditoría
  - Activo listo para uso
```

---

### CASO 4: Certificar Calibración Técnica

```
Actor: Técnico certificado (ISO 17025)
Precondición: Activo requerido calibración, user permisos

Flujo Principal:
  1. Ve listado calibraciones pendientes
  2. Selecciona activo
  3. Carga formulario:
     └─ Patrón de referencia (ISO)
     └─ Puntos medibles (AJAX carga puntos del patrón)
     └─ Mide cada punto en laboratorio
     └─ Completa tabla:
        ├─ Valor especificado (de patrón)
        ├─ Valor medido (de equipo)
        ├─ Diferencia (auto calc)
        └─ Dentro tolerancia SI/NO
  4. Sube certificado PDF del patrón
  5. Click "Generar Certificado"
  6. Sistema crea documento profesional:
     └─ Foto técnica
     └─ Tabla de puntos
     └─ Gráficos
     └─ Norma ISO 17025
     └─ Firma técnico (campo firma pad)
     └─ QR público

Postcondición:
  - Certificado generado
  - Cliente accede por QR (sin login)
  - Válido por X meses
  - Auditoría completa
```

---

### CASO 5: Cliente Verifica Certificado (SIN LOGIN)

```
Actor: Cliente/Responsable (externo)
Precondición: Tiene código QR o enlace de certificado

Flujo Principal:
  1. Escanea QR con móvil
     Ruta: ?route=calibracion_verificar&token=ABC123
  2. No pide login
  3. Muestra:
     └─ Nro. certificado
     └─ Activo
     └─ Fecha calibración
     └─ Próxima calibración
     └─ Estado CONFORME/NO_CONFORME
     └─ Firma técnico
     └─ Tabla puntos medidos
  4. Puede descargar PDF
  5. Puede imprimir

Postcondición:
  - Cliente verifica estado sin login
  - Transparencia total
  - Auditoría del acceso registrada
```

---

## 🔐 FLUJO DE PERMISOS (RBAC)

```
        USUARIO INTENTA ACCEDER A RUTA
                    │
                    ▼
         ¿Ruta es pública?
                    │
         ┌──────────┴──────────┐
         │ SÍ                  │ NO
         │                     │
         ▼                     ▼
    SIN VALIDACIÓN    Auth::requireLogin()
         │                     │
         │            ┌────────┴────────┐
         │            │                 │
         │        ¿Sesión activa?       │
         │            │                 │
         │      ┌─────┴────────┐        │
         │      │ NO           │ SÍ     │
         │      │              │        │
         │      ▼              ▼        │
         │    ❌ Redirect   Init perf.  │
         │    login         $_SESSION   │
         │                  ['perms']   │
         │                  (Cache)     │
         │                     │        │
         │                     ▼        │
         │         Auth::requirePerm()  │
         │         ($permiso_nombre)    │
         │                     │        │
         │         ┌───────────┴───────┐
         │         │                   │
         │         ▼                   ▼
         │      SÍ TIENE          NO TIENE
         │      PERMISO            PERMISO
         │         │                   │
         │         ▼                   ▼
         │      CARGA VISTA       ERROR 403
         │                        Forbidden
         │                             │
         └──────────┬──────────┬───────┘
                    │          │
                    ▼          ▼
                  ✅ OK      ❌ DENIED
            (Muestra datos)  (Rechaza)


EJEMPLO: Ver activos
  └─ Ruta: ?route=activos
  └─ Permiso: 'activos.view'
  └─ Tabla: rol_permisos
     ├─ rol_id: 2 (TECNICO)
     ├─ permiso: 'activos.view'
     └─ (Si existe este pair → usuario del rol 2 puede ver)

EJEMPLO: Crear activo
  └─ Ruta: ?route=activos_form (POST)
  └─ Permiso: 'activos.edit'
  └─ Si user NO tiene → Error 403
```

---

## 📊 CICLO DE AUDITORÍA

```
CUALQUIER CAMBIO EN TABLA AUDITADA
         │
         ▼
    ┌─────────────────────────────────┐
    │  INSERT/UPDATE/DELETE            │
    │  en tabla: activos               │
    │  por: usuario_id = 5             │
    │  acción: UPDATE                  │
    │  registro_id: 123                │
    └─────────────┬─────────────────┘
                  │
         ┌────────▼─────────┐
         │ TRIGGER (si existe)
         │ O manual en código
         └────────┬─────────┘
                  │
         ┌────────▼──────────────────────┐
         │ INSERT INTO audit_log         │
         │ VALUES (                       │
         │   NULL, -- id auto             │
         │   $tenant_id,    -- cliente    │
         │   5,             -- usuario    │
         │   'activos',     -- tabla      │
         │   'UPDATE',      -- acción     │
         │   123,           -- id activo  │
         │   {              -- cambios    │
         │     'nombre': {               │
         │       'old': 'Motor A',       │
         │       'new': 'Motor B'        │
         │     }                         │
         │   },                          │
         │   NOW()          -- fecha     │
         │ )                             │
         └────────┬──────────────────────┘
                  │
         ┌────────▼──────────────────────┐
         │  AUDITORÍA REGISTRADA          │
         │  Usuario puede ver en:         │
         │  ├─ auditoria/audit_log.php    │
         │  │  (Todos los cambios)        │
         │  ├─ auditoria/activo_aud.php   │
         │  │  (De ese activo específico) │
         │  ├─ auditoria/usuario_aud.php  │
         │  │  (De ese usuario específico)│
         │  └─ auditoria/timeline.php     │
         │     (Orden cronológico)        │
         └────────┬──────────────────────┘
                  │
         ✅ TRAZABILIDAD COMPLETA
            - Quién hizo qué
            - Cuándo lo hizo
            - Qué valores cambió
            - Se puede auditar seguridad
```

---

## 📁 ARQUITECTURA DE DIRECTORIOS

```
geoactivos/
│
├── public/                    ← PUERTA DE ENTRADA
│   ├── index.php             ← Router central
│   ├── hash.php              ← Generador contraseñas
│   ├── seed_admin.php        ← Datos iniciales
│   ├── assets/
│   │   ├── css/
│   │   │   └── custom.css
│   │   └── js/
│   │       └── app.js (jQuery AJAX)
│   └── uploads/              ← Archivos usuario
│       ├── activos/
│       ├── firmas/
│       └── mantenimientos/
│
├── app/                       ← LÓGICA
│   ├── config/               ← Configuración
│   │   ├── config.php        ← Parámetros DB
│   │   └── db.php            ← Conexión PDO
│   │
│   ├── core/                 ← Núcleo
│   │   ├── Auth.php          ← Autenticación
│   │   └── Helpers.php       ← Funciones
│   │
│   ├── views/                ← PRESENTACIÓN
│   │   ├── layout/           ← Plantilla maestra
│   │   │   ├── header.php    ← HTML arriba
│   │   │   ├── sidebar.php   ← Menú lateral
│   │   │   └── footer.php    ← HTML abajo
│   │   ├── activos/          ← Módulo
│   │   │   ├── index.php
│   │   │   ├── form.php
│   │   │   ├── detalle.php
│   │   │   └── ...
│   │   ├── mantenimientos/
│   │   ├── calibraciones/
│   │   ├── usuarios/
│   │   └── ... (7 módulos más)
│   │
│   ├── ajax/                 ← API INTERNA
│   │   ├── act_*.php
│   │   ├── mant_*.php
│   │   ├── ajax_cal_*.php
│   │   └── ... (25 endpoints)
│   │
│   └── storage/              ← Almacenamien
│       └── firmas/
│
├── database/                 ← SQL
│   ├── geoactivos_schema.sql ← Crear tablas
│   └── geoactivos_clean.sql  ← Datos demo
│
└── Documentación:
    ├── ANALISIS_PROYECTO.md    ← Este análisis
    ├── RESUMEN_EJECUTIVO.md    ← Resumen
    ├── GUIA_RUTAS.md           ← Rutas
    └── FLUJOS.md               ← Flujos
```

---

## 🔄 FLUJO AJAX (Ej: Upload foto)

```
FRONTEND: activos/detalle.php
     │
     │ <input type="file" id="foto">
     │ <button onclick="uploadFoto()">
     │
     └─────▶ JavaScript/jQuery
              │
              │ $('#foto').change(function(e) {
              │     let file = e.target.files[0];
              │     let fd = new FormData();
              │     fd.append('archivo', file);
              │     fd.append('activo_id', 5);
              │
              └─────▶ $.ajax({
                          type: 'POST',
                          url: 'ajax/act_foto_upload.php',
                          data: fd,
                          success: function(res) {
                              console.log(res);
                              // JSON response
                          }
                      })

BACKEND: app/ajax/act_foto_upload.php
     │
     ├─ Auth::requireLogin()
     │  └─ Verifica sesión
     │
     ├─ $_FILES['archivo']
     │  ├─ Validar mime (jpg, png, jpeg)
     │  ├─ Validar tamaño < 5MB
     │  └─ Validar nombre
     │
     ├─ move_uploaded_file()
     │  └─ /public/uploads/activos/[nombre_hash].jpg
     │
     ├─ INSERT INTO activos_fotos
     │  ├─ activo_id
     │  ├─ ruta: /uploads/activos/...
     │  ├─ mime: image/jpeg
     │  └─ creado_en
     │
     ├─ INSERT INTO audit_log
     │
     └─ echo json_encode({
            'success': true,
            'id': 123,
            'path': '/uploads/activos/abc123.jpg'
        })

FRONTEND: Recibe JSON
     │
     └─────▶ Actualiza tabla HTML
            <tr><td><img src="/uploads/activos/abc123.jpg"></td>
                 <td><button onclick="deleteFoto(123)">X</button></td></tr>
```

---

## 📈 Tabla de Flujos

| Flujo | Actores | Inicio | Fin | Duración |
|-------|---------|--------|-----|----------|
| Login | Usuario | click login | Dashboard | < 5s |
| Crear Activo | Técnico | click "Nuevo" | Listado | < 30s |
| Mantenimiento | Técnico | Activo detalle | Cerrado | Horas |
| Calibración | Técnico | Nuevo | Certificado | 1-2h |
| Verificar (QR) | Cliente | Escanea QR | PDF | < 5s |
| Auditoría | Admin | click "Auditoría" | Log | < 10s |

---

**Documento Generado:** 3 de Marzo de 2026  
**Total de Diagramas:** 10  
**Estado:** ✅ Completo

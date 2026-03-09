<?php
session_start();

/* ===================== DEV ===================== */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ===================== CORE ===================== */
require_once __DIR__ . '/../app/core/Helpers.php';
require_once __DIR__ . '/../app/core/Auth.php';

/* ===================== ROUTE ===================== */
$route = $_GET['route'] ?? 'splash';

/* ===================== RUTAS PÚBLICAS (NO LOGIN) =====================
   - login / logout: autenticación
   - calibracion_verificar: página pública por token (QR sin login)
   Agregar aquí cualquier ruta que no requiera autenticación.
=================================================== */
$publicRoutes = [
  `splash`, // Página de bienvenida (sin login) — opcional
  'login',
  'logout',
  'calibracion_verificar',
];

/* ===================== AUTH ===================== */
// splash is a public welcome page
if ($route === 'splash') {
  require __DIR__ . '/../app/views/auth/splash.php';
  exit;
}

if ($route === 'login') {
  require __DIR__ . '/../app/views/auth/login.php';
  exit;
}

if ($route === 'logout') {
  Auth::logout();
  redirect('index.php?route=splash');
}

if ($route === 'set_lang') {
  $lang = $_GET['lang'] ?? 'es';
  $_SESSION['lang'] = $lang === 'en' ? 'en' : 'es';
  $back = $_GET['back'] ?? 'inicio';
  redirect('index.php?route=' . $back);
}

/* ===================== PROTECCIÓN GLOBAL =====================
   Solo pedimos login si NO es ruta pública.
=================================================== */
if (!in_array($route, $publicRoutes, true)) {
  Auth::requireLogin();
}

/* ===================== ROUTER ===================== */
switch ($route) {

  /* ─────────────────── INICIO / DASHBOARD ─────────────────── */
  case 'inicio':
  case 'dashboard':
    Auth::requirePerm('dashboard.view');
    require __DIR__ . '/../app/views/inicio/index.php';
    break;

  /* ─────────────────── EMPRESAS ─────────────────── */
  case 'empresa_ver':
    Auth::requirePerm('empresas.view');
    require __DIR__ . '/../app/views/empresas/detalle.php';
    break;

  /* ─────────────────── CALENDARIO ─────────────────── */
  case 'calendario':
    Auth::requirePerm('mantenimientos.view');
    require __DIR__ . '/../app/views/calendario/index.php';
    break;

  /* ─────────────────── AJAX: DASHBOARD STATS ─────────────────── */
  case 'ajax_dashboard_stats':
    header('Content-Type: application/json');
    $tid = Auth::tenantId();
    $st = db()->prepare("SELECT COUNT(*) c FROM activos WHERE tenant_id=:t");
    $st->execute([':t'=>$tid]);
    $activos = (int)($st->fetch()['c'] ?? 0);
    $st = db()->prepare("SELECT COUNT(*) c FROM mantenimientos WHERE tenant_id=:t AND estado IN ('PROGRAMADO','EN_PROCESO')");
    $st->execute([':t'=>$tid]);
    $pendientes = (int)($st->fetch()['c'] ?? 0);
    echo json_encode(['activos'=>$activos,'pendientes'=>$pendientes]);
    exit;

  case 'ajax_mantenimientos_calendario':
    header('Content-Type: application/json');
    $tid = Auth::tenantId();
    $isSuper = Auth::isSuperadmin();
    $filter = $_GET['filter'] ?? 'all';
    
    $sql = "SELECT m.id, m.tipo, m.estado, m.prioridad, m.fecha_programada, m.fecha_inicio, m.fecha_fin, m.falla_reportada, a.nombre as activo_nombre
            FROM mantenimientos m
            LEFT JOIN activos a ON a.id = m.activo_id
            WHERE 1=1";
    
    if (!$isSuper) {
        $sql .= " AND m.tenant_id = :tid";
    }
    
    if ($filter !== 'all') {
        $sql .= " AND m.estado = :filter";
    }
    
    $sql .= " ORDER BY m.fecha_programada ASC LIMIT 200";
    
    $st = db()->prepare($sql);
    $params = [];
    if (!$isSuper) {
        $params[':tid'] = $tid;
    }
    if ($filter !== 'all') {
        $params[':filter'] = $filter;
    }
    $st->execute($params);
    $mants = $st->fetchAll();
    
    $events = [];
    foreach ($mants as $m) {
        $title = '#' . $m['id'] . ' - ' . ($m['activo_nombre'] ?: 'Sin equipo');
        $events[] = [
            'id' => $m['id'],
            'title' => $title,
            'start' => $m['fecha_programada'],
            'end' => $m['fecha_fin'],
            'estado' => $m['estado'] ?? 'PENDIENTE',
            'tipo' => $m['tipo'],
            'prioridad' => $m['prioridad'],
            'fecha_programada' => $m['fecha_programada'],
            'fecha_inicio' => $m['fecha_inicio'],
            'fecha_fin' => $m['fecha_fin'],
            'falla_reportada' => $m['falla_reportada'],
            'equipo_nombre' => $m['activo_nombre']
        ];
    }
    
    echo json_encode($events);
    exit;

  /* ─────────────────── ACTIVOS ─────────────────── */
  case 'activos':
    Auth::requirePerm('activos.view');
    require __DIR__ . '/../app/views/activos/index.php';
    break;

  case 'activos_form':
    Auth::requirePerm('activos.edit');
    require __DIR__ . '/../app/views/activos/form.php';
    break;

  case 'importar_activos':
    Auth::requirePerm('activos.edit');
    require __DIR__ . '/../app/views/activos/importar.php';
    break;

  case 'descargar_plantilla_activos':
    Auth::requirePerm('activos.view');
    require __DIR__ . '/../app/views/activos/plantilla.php';
    break;

  case 'activo_detalle':
    Auth::requirePerm('activos.view');
    require __DIR__ . '/../app/views/activos/detalle.php';
    break;

  case 'activo_software':
    Auth::requirePerm('activos.edit');
    require __DIR__ . '/../app/views/activos/software.php';
    break;

  case 'activo_software_delete':
    Auth::requirePerm('activos.edit');
    require __DIR__ . '/../app/views/activos/software_delete.php';
    break;

  case 'activo_hoja_vida':
    Auth::requirePerm('activos.edit');
    require __DIR__ . '/../app/views/activos/activo_hoja_vida.php';
    break;

  case 'activo_hoja_vida_print':
    Auth::requirePerm('activos.edit');
    require __DIR__ . '/../app/views/activos/activo_hoja_vida_print.php';
    break;

  case 'activos_delete':
    require __DIR__ . '/../app/views/activos/activos_delete.php';
    break;

  case 'activos_eliminados':
    require __DIR__ . '/../app/views/activos/activos_eliminados.php';
    break;

  case 'activos_restore':
    require __DIR__ . '/../app/views/activos/activos_restore.php';
    break;

  case 'activos_purge':
    require __DIR__ . '/../app/views/activos/activos_purge.php';
    break;

  /* ─────────────────── AUDITORÍA ─────────────────── */
  case 'audit_log':
    require __DIR__ . '/../app/views/auditoria/audit_log.php';
    break;

  case 'activo_auditoria':
    require __DIR__ . '/../app/views/auditoria/activo_auditoria.php';
    break;

  case 'auditoria_usuario':
    require __DIR__ . '/../app/views/auditoria/auditoria_usuario.php';
    break;

  case 'mantenimiento_auditoria':
    require __DIR__ . '/../app/views/mantenimientos/mantenimiento_auditoria.php';
    break;

  /* ─────────────────── COMPONENTES ─────────────────── */
  case 'componente_form':
    require __DIR__ . '/../app/views/componentes/componente_form.php';
    break;

  case 'componente_delete':
    require __DIR__ . '/../app/actions/componentes/delete.php';
    break;

  /* ─────────────────── QR / ETIQUETAS ─────────────────── */
  case 'activo_qr_etiqueta':
    require __DIR__ . '/../app/views/activos/activo_qr_etiqueta.php';
    break;

  case 'activo_qr':
    $id = (int)($_GET['id'] ?? 0);
    header('Location: index.php?route=activo_hoja_vida&id=' . $id);
    exit;

  /* ─────────────────── MANTENIMIENTOS ─────────────────── */
  case 'mantenimientos':
    Auth::requirePerm('mantenimientos.view');
    require __DIR__ . '/../app/views/mantenimientos/index.php';
    break;

  case 'mantenimiento_form':
    Auth::requirePerm('mantenimientos.edit');
    require __DIR__ . '/../app/views/mantenimientos/form.php';
    break;

  case 'mantenimiento_ver':
    Auth::requirePerm('mantenimientos.view');
    require __DIR__ . '/../app/views/mantenimientos/detalle.php';
    break;

  case 'mantenimiento_log_add':
    Auth::requirePerm('mantenimientos.edit');
    require __DIR__ . '/../app/views/mantenimientos/log_add.php';
    break;

  case 'mantenimiento_detalle':
    Auth::requirePerm('mantenimientos.view');
    require __DIR__ . '/../app/views/mantenimientos/detalle.php';
    break;

  case 'mantenimiento_print':
    require __DIR__ . '/../app/views/mantenimientos/mantenimiento_print.php';
    break;

  /* ─────────────────── CALIBRACIONES ─────────────────── */
  case 'calibraciones':
    require_once __DIR__ . '/../app/views/calibraciones/index.php';
    exit;

  case 'calibracion_form':
    require_once __DIR__ . '/../app/views/calibraciones/form.php';
    exit;

  case 'calibracion_detalle':
    require_once __DIR__ . '/../app/views/calibraciones/detalle.php';
    exit;

  case 'calibracion_certificado':
    require __DIR__ . '/../app/views/calibraciones/certificado_print.php';
    break;

  case 'calibracion_certificado_edit':
    require __DIR__ . '/../app/views/calibraciones/certificado_edit.php';
    break;

  case 'calibracion_puntos':
    require __DIR__ . '/../app/views/calibraciones/puntos.php';
    break;

  case 'calibracion_punto_form':
    require __DIR__ . '/../app/views/calibraciones/punto_form.php';
    break;

  case 'calibracion_verificar':
    // Ruta pública (sin login) — acceso por token desde QR
    require_once __DIR__ . '/../app/views/calibraciones/verificar.php';
    exit;

  /* ─────────────────── PATRONES ─────────────────── */
  case 'patrones':
    require_once __DIR__ . '/../app/views/patrones/index.php';
    exit;

  case 'patron_form':
    require_once __DIR__ . '/../app/views/patrones/form.php';
    exit;

  case 'patron_delete':
    require_once __DIR__ . '/../app/views/patrones/delete.php';
    exit;

  case 'patron_puntos_ajax':
    require __DIR__ . '/../app/ajax/patron_puntos.php';
    exit;

  /* ─────────────────── CONFIGURACIÓN ─────────────────── */
  case 'categorias':
  case 'categoria_form':
  case 'marcas':
  case 'marca_form':
  case 'sedes':
  case 'sede_form':
  case 'areas':
  case 'area_form':
  case 'proveedores':
  case 'proveedor_form':
  case 'tipos_activo':
  case 'tipo_activo_form':
    Auth::requirePerm('config.view');
    require __DIR__ . '/../app/views/' . match($route) {
      'categorias'       => 'config/categorias/index.php',
      'categoria_form'   => 'config/categorias/form.php',
      'marcas'           => 'config/marcas/index.php',
      'marca_form'       => 'config/marcas/form.php',
      'sedes'            => 'config/sedes/index.php',
      'sede_form'        => 'config/sedes/form.php',
      'areas'            => 'config/areas/index.php',
      'area_form'        => 'config/areas/form.php',
      'proveedores'      => 'config/proveedores/index.php',
      'proveedor_form'   => 'config/proveedores/form.php',
      'tipos_activo'     => 'config/tipos_activo/index.php',
      'tipo_activo_form' => 'config/tipos_activo/form.php',
    };
    break;

  /* ─────────────────── EMPRESAS ─────────────────── */
  case 'empresas':
    Auth::requirePerm('empresas.view');
    require __DIR__ . '/../app/views/empresas/index.php';
    break;

  case 'empresa_form':
    Auth::requirePerm('empresas.view');
    require __DIR__ . '/../app/views/empresas/form.php';
    break;

  /* ─────────────────── USUARIOS ─────────────────── */
  case 'usuarios':
    Auth::requirePerm('usuarios.view');
    require __DIR__ . '/../app/views/usuarios/index.php';
    break;

  case 'usuario_form':
    Auth::requirePerm('usuarios.edit');
    require __DIR__ . '/../app/views/usuarios/form.php';
    break;

  /* ─────────────────── ROLES ─────────────────── */
  case 'roles':
    Auth::requirePerm('roles.view');
    require __DIR__ . '/../app/views/roles/index.php';
    break;

  case 'rol_form':
  case 'rol_delete':
  case 'rol_permisos':
    Auth::requirePerm('roles.edit');
    require __DIR__ . '/../app/views/roles/' . str_replace('rol_', '', $route) . '.php';
    break;

  /* ─────────────────── AJAX — ACTIVOS ─────────────────── */
  case 'ajax_next_codigo_activo':
    Auth::requirePerm('activos.edit');
    require __DIR__ . '/../app/ajax/next_codigo_activo.php';
    break;

  case 'ajax_tipo_reglas':
    Auth::requirePerm('config.view');
    require __DIR__ . '/../app/ajax/tipo_reglas.php';
    break;

  case 'ajax_act_foto_upload':
    Auth::requirePerm('activos.edit');
    require __DIR__ . '/../app/ajax/act_foto_upload.php';
    exit;

  case 'ajax_act_foto_delete':
    Auth::requirePerm('activos.edit');
    require_once __DIR__ . '/../app/ajax/act_foto_delete.php';
    exit;

  case 'ajax_act_adj_upload':
    Auth::requirePerm('activos.edit');
    require __DIR__ . '/../app/ajax/activos_adj_upload.php';
    break;

  case 'ajax_act_adj_download':
    Auth::requirePerm('activos.view');
    require __DIR__ . '/../app/ajax/act_adj_download.php';
    exit;

  case 'ajax_act_adj_preview':
    Auth::requirePerm('activos.view');
    require __DIR__ . '/../app/ajax/act_adj_preview.php';
    exit;

  case 'ajax_act_adj_delete':
    Auth::requirePerm('activos.edit');
    require __DIR__ . '/../app/ajax/activos_adj_delete.php';
    break;

  /* ─────────────────── AJAX — MANTENIMIENTOS ─────────────────── */
  case 'ajax_mant_adj_upload':
    Auth::requirePerm('mantenimientos.edit');
    require __DIR__ . '/../app/ajax/mant_adj_upload.php';
    break;

  case 'ajax_mant_adj_download':
    Auth::requirePerm('mantenimientos.view');
    require __DIR__ . '/../app/ajax/mant_adj_download.php';
    break;

  case 'ajax_mant_adj_delete':
    Auth::requirePerm('mantenimientos.edit');
    require __DIR__ . '/../app/ajax/mant_adj_delete.php';
    break;

  /* ─────────────────── AJAX — CALIBRACIONES ─────────────────── */
  case 'ajax_cal_punto_add':
    require_once __DIR__ . '/../app/ajax/ajax_cal_punto_add.php';
    exit;

  case 'ajax_cal_punto_delete':
    require_once __DIR__ . '/../app/ajax/ajax_cal_punto_delete.php';
    exit;

  case 'ajax_cal_patron_add':
    require_once __DIR__ . '/../app/ajax/ajax_cal_patron_add.php';
    exit;

  case 'ajax_cal_patron_delete':
    require_once __DIR__ . '/../app/ajax/ajax_cal_patron_delete.php';
    exit;

  case 'ajax_cal_cerrar':
    require_once __DIR__ . '/../app/ajax/ajax_cal_cerrar.php';
    exit;

  case 'ajax_cal_anular':
    require_once __DIR__ . '/../app/ajax/ajax_cal_anular.php';
    exit;

  case 'ajax_cal_adj_upload':
    require_once __DIR__ . '/../app/ajax/ajax_cal_adj_upload.php';
    exit;

  case 'ajax_cal_adj_preview':
    require_once __DIR__ . '/../app/ajax/ajax_cal_adj_preview.php';
    exit;

  case 'ajax_cal_adj_download':
    require_once __DIR__ . '/../app/ajax/ajax_cal_adj_download.php';
    exit;

  case 'ajax_cal_adj_delete':
    require_once __DIR__ . '/../app/ajax/ajax_cal_adj_delete.php';
    exit;

  /* ─────────────────── 404 ─────────────────── */
  default:
    http_response_code(404);
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>404</title></head><body style="font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#F5F8FA;"><div style="text-align:center;color:#4E6D8C;"><div style="font-size:4rem;font-weight:800;color:#0BA896;">404</div><div style="font-size:1.1rem;margin-top:8px;">Ruta no encontrada: <code>' . htmlspecialchars($route) . '</code></div><a href="index.php" style="display:inline-block;margin-top:20px;padding:10px 24px;background:#0BA896;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">Ir al inicio</a></div></body></html>';
    break;
}
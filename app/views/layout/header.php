<?php
// app/views/layout/header.php
require_once __DIR__ . '/../../core/Helpers.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../config/db.php';

Auth::requireLogin();

$route        = (string)($_GET['route'] ?? 'dashboard');
$userNombre   = (string)($_SESSION['user']['nombre'] ?? 'Usuario');
$userEmail    = (string)($_SESSION['user']['email'] ?? '');
$rolNombre    = (string)($_SESSION['rol_nombre'] ?? ($_SESSION['user']['rol_nombre'] ?? ''));
$tenantNombre = (string)($_SESSION['tenant']['nombre'] ?? ($_SESSION['user']['tenant_nombre'] ?? 'Cliente'));

$map = [
  'inicio'               => ['Inicio'],
  'dashboard'             => ['Inicio'],
  'activos'               => ['Activos'],
  'activos_form'          => ['Activos','Formulario'],
  'activo_detalle'        => ['Activos','Hoja de vida'],
  'mantenimientos'        => ['Mantenimientos'],
  'mantenimiento_form'    => ['Mantenimientos','Formulario'],
  'mantenimiento_detalle' => ['Mantenimientos','Detalle'],
  'mantenimiento_ver'     => ['Mantenimientos','Detalle'],
  'calibraciones'         => ['Calibraciones'],
  'calibracion_form'      => ['Calibraciones','Formulario'],
  'calibracion_detalle'   => ['Calibraciones','Detalle'],
  'patrones'              => ['Patrones'],
  'patron_form'           => ['Patrones','Formulario'],
  'audit_log'             => ['Auditoría'],
  'activo_auditoria'      => ['Auditoría','Detalle'],
  'calendario'           => ['Calendario'],
  'categorias'            => ['Configuración','Categorías'],
  'categoria_form'        => ['Configuración','Categorías','Formulario'],
  'marcas'                => ['Configuración','Marcas'],
  'marca_form'            => ['Configuración','Marcas','Formulario'],
  'sedes'                 => ['Configuración','Sedes'],
  'sede_form'             => ['Configuración','Sedes','Formulario'],
  'areas'                 => ['Configuración','Áreas'],
  'area_form'             => ['Configuración','Áreas','Formulario'],
  'proveedores'           => ['Configuración','Proveedores'],
  'proveedor_form'        => ['Configuración','Proveedores','Formulario'],
  'tipos_activo'          => ['Configuración','Tipos de activo'],
  'tipo_activo_form'      => ['Configuración','Tipos de activo','Formulario'],
  'empresas'              => ['Empresas'],
  'empresa_form'          => ['Empresas','Formulario'],
  'empresa_ver'          => ['Empresas','Detalle'],
  'usuarios'              => ['Administración','Usuarios'],
  'usuario_form'          => ['Administración','Usuarios','Formulario'],
  'roles'                 => ['Administración','Roles y permisos'],
  'rol_form'              => ['Administración','Roles y permisos','Formulario'],
  'rol_permisos'          => ['Administración','Roles y permisos','Permisos'],
];

$crumbs    = $map[$route] ?? ['Módulo'];
$pageTitle = end($crumbs);

$alarmCount = 0;
$alarms = [];
if (in_array($route, ['inicio', 'dashboard', 'mantenimientos', 'calendario'], true)) {
    $tid = Auth::tenantId();
    $isSuper = Auth::isSuperadmin();
    try {
        $sql = "SELECT m.id, m.fecha_programada, m.estado, a.nombre as activo_nombre, a.codigo as activo_codigo
                FROM mantenimientos m
                LEFT JOIN activos a ON a.id = m.activo_id
                WHERE m.estado = 'PROGRAMADO' AND m.fecha_programada IS NOT NULL";
        
        if (!$isSuper) {
            $sql .= " AND m.tenant_id = :tid";
        }
        
        $sql .= " AND DATEDIFF(m.fecha_programada, CURDATE()) BETWEEN 0 AND 7";
        $sql .= " ORDER BY m.fecha_programada ASC LIMIT 5";
        
        $st = db()->prepare($sql);
        if (!$isSuper) {
            $st->execute([':tid' => $tid]);
        } else {
            $st->execute();
        }
        $alarms = $st->fetchAll();
        $alarmCount = count($alarms);
    } catch (Exception $e) {
        $alarms = [];
        $alarmCount = 0;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> · GeoActivos</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(base_url()) ?>/assets/css/custom.css">

<style>
:root{
  --teal:#0BA896; --teal-dark:#077D6E; --teal-light:#E6F7F5; --teal-mid:#B2E8E3;
  --slate-900:#0F1D2E; --slate-800:#1A2E42; --slate-700:#2C4258; --slate-500:#4E6D8C;
  --slate-400:#7090AA; --slate-300:#9DB8CC; --slate-200:#C8DDE8; --slate-100:#E8F1F7;
  --slate-50:#F5F8FA; --white:#FFFFFF;
  --amber:#F59E0B; --blue:#3B82F6; --purple:#8B5CF6; --rose:#F43F5E; --green:#10B981;
  --ga-border:rgba(11,168,150,0.18);
  --ga-card:rgba(255,255,255,0.95);
  --sidebar-w:260px;
}
body,.wrapper,.content-wrapper{background:var(--slate-50) !important;color:var(--slate-900) !important;font-family:'Plus Jakarta Sans',sans-serif !important;}
.wrapper{padding-top:64px !important;padding-bottom:60px !important;}

/* Override AdminLTE sidebar - prevent movement */
body.sidebar-collapse .main-header,
body.sidebar-mini .main-header,
body.sidebar-collapse .main-footer,
body.sidebar-mini .main-footer,
body.sidebar-collapse .content-wrapper,
body.sidebar-mini .content-wrapper,
.main-header,
.main-footer,
.main-header.navbar-expand,
.main-header{left:0 !important;margin-left:0 !important;transform:none !important;transition:none !important;width:100% !important;}
body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;background-image:linear-gradient(rgba(11,168,150,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(11,168,150,0.03) 1px,transparent 1px);background-size:52px 52px;}

.main-header.navbar{background:rgba(255,255,255,0.95) !important;border-bottom:1px solid var(--slate-100) !important;box-shadow:0 2px 20px rgba(15,29,46,0.06) !important;height:64px;backdrop-filter:blur(16px);position:fixed !important;top:0 !important;left:0 !important;right:0 !important;width:100vw !important;z-index:1052 !important;margin:0 !important;transform:none !important;}
.main-header.navbar::after{content:'';position:absolute;bottom:0;left:10%;right:10%;height:1px;background:linear-gradient(90deg,transparent,var(--teal),var(--teal-mid),transparent);opacity:.5;}

.ga-nav-brand{display:flex;align-items:center;gap:10px;padding:4px 0;}
.ga-nav-brand-icon{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--teal),var(--teal-dark));display:flex;align-items:center;justify-content:center;position:relative;flex-shrink:0;box-shadow:0 3px 10px rgba(11,168,150,0.28);}
.ga-nav-brand-icon i{color:#fff;font-size:14px;}
.ga-nav-brand-texts{display:flex;flex-direction:column;line-height:1.1;}
.ga-nav-brand-main{font-family:'Sora',sans-serif;font-weight:700;font-size:1.05rem;letter-spacing:-0.2px;color:var(--slate-900);}
.ga-nav-brand-sub{font-size:.52rem;font-weight:600;letter-spacing:2.5px;text-transform:uppercase;color:var(--slate-400);}

.main-header .nav-link{color:var(--slate-500) !important;transition:color .2s !important;}
.main-header .nav-link:hover{color:var(--teal) !important;}

.ga-tenant-pill{display:inline-flex;align-items:center;gap:7px;background:var(--teal-light);border:1px solid var(--teal-mid);border-radius:100px;padding:5px 14px;font-size:.72rem;font-weight:600;letter-spacing:.5px;color:var(--slate-700);}
.ga-tenant-pill .tdot{width:6px;height:6px;border-radius:50%;background:var(--teal);box-shadow:0 0 6px var(--teal);animation:gaPulse 2s infinite;flex-shrink:0;}
@keyframes gaPulse{0%,100%{box-shadow:0 0 4px var(--teal)}50%{box-shadow:0 0 12px var(--teal)}}

.ga-user-chip{display:flex;align-items:center;gap:9px;padding:4px 10px 4px 4px;background:var(--slate-50);border:1px solid var(--slate-100);border-radius:100px;cursor:pointer;transition:all .25s;}
.ga-user-chip:hover{background:var(--teal-light);border-color:var(--teal-mid);}
.ga-user-avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--teal),var(--teal-dark));display:flex;align-items:center;justify-content:center;font-size:12px;color:#fff;flex-shrink:0;}
.ga-user-name{font-size:.78rem;font-weight:700;color:var(--slate-900);line-height:1;}
.ga-user-role{font-size:.62rem;color:var(--slate-400);line-height:1;margin-top:1px;}

.navbar .dropdown-menu{background:var(--white) !important;border:1px solid var(--slate-100) !important;border-radius:12px !important;box-shadow:0 20px 60px rgba(15,29,46,0.12) !important;padding:8px !important;min-width:220px;backdrop-filter:blur(16px);}
.navbar .dropdown-item{color:var(--slate-700) !important;border-radius:8px !important;padding:9px 12px !important;font-size:.82rem !important;transition:all .2s !important;display:flex;align-items:center;gap:9px;}
.navbar .dropdown-item:hover{background:var(--teal-light) !important;color:var(--teal-dark) !important;}
.navbar .dropdown-item.text-danger{color:var(--rose) !important;}
.navbar .dropdown-item.text-danger:hover{background:#FFF0F2 !important;color:var(--rose) !important;}
.navbar .dropdown-divider{border-color:var(--slate-100) !important;margin:6px 0 !important;}
.navbar .dropdown-item-text{color:var(--slate-400) !important;font-size:.75rem !important;padding:8px 12px !important;}
.navbar-badge{font-size:.55rem !important;}

.content-header{background:var(--white) !important;border-bottom:1px solid var(--slate-100) !important;padding:16px 24px !important;position:relative;}
.ga-page-title{font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:700;letter-spacing:-0.3px;color:var(--slate-900);line-height:1;margin-bottom:4px;}
.ga-breadcrumb{display:flex;align-items:center;gap:6px;list-style:none;padding:0;margin:0;font-size:.72rem;font-weight:500;}
.ga-breadcrumb li{color:var(--slate-400);}
.ga-breadcrumb li a{color:var(--slate-400);text-decoration:none;transition:color .2s;}
.ga-breadcrumb li a:hover{color:var(--teal);}
.ga-breadcrumb li.active{color:var(--teal);}
.ga-breadcrumb li+li::before{content:'›';margin-right:6px;opacity:.4;color:var(--slate-300);}

.content-wrapper{padding:20px 24px 100px !important;transition:margin-left 0s cubic-bezier(0.4,0,0.2,1),width 0s cubic-bezier(0.4,0,0.2,1);margin-left:0 !important;width:100%;}
.content{padding:0 !important;}
.container-fluid{padding:0 !important;}

.card{background:var(--white) !important;border:1px solid var(--slate-100) !important;border-radius:12px !important;color:var(--slate-900) !important;box-shadow:0 1px 4px rgba(15,29,46,0.05) !important;}
.card-header{background:var(--slate-50) !important;border-bottom:1px solid var(--slate-100) !important;color:var(--slate-900) !important;}
.card-title{font-family:'Sora',sans-serif !important;font-weight:700 !important;letter-spacing:0 !important;color:var(--slate-900) !important;font-size:1rem !important;}
.card-body{color:var(--slate-900) !important;}

.table{color:var(--slate-700) !important;}
.table thead th{background:var(--slate-50) !important;border-bottom:1px solid var(--slate-100) !important;color:var(--teal-dark) !important;font-size:.7rem !important;font-weight:700 !important;letter-spacing:0.5px !important;text-transform:uppercase !important;}
.table td,.table th{border-color:var(--slate-100) !important;vertical-align:middle !important;}
.table-striped tbody tr:nth-of-type(odd){background:rgba(11,168,150,0.02) !important;}
.table-hover tbody tr:hover{background:var(--teal-light) !important;}

.form-control,.form-select{background:var(--white) !important;border:1px solid var(--slate-200) !important;border-radius:9px !important;color:var(--slate-900) !important;font-family:'Plus Jakarta Sans',sans-serif !important;transition:all .3s !important;}
.form-control:focus,.form-select:focus{border-color:var(--teal) !important;background:var(--white) !important;box-shadow:0 0 0 3px rgba(11,168,150,0.12) !important;color:var(--slate-900) !important;}
.form-control::placeholder{color:var(--slate-300) !important;}
label,.col-form-label{font-size:.75rem !important;font-weight:600 !important;letter-spacing:0.5px !important;color:var(--slate-600) !important;margin-bottom:6px !important;}
select.form-control option{background:var(--white);color:var(--slate-900);}

.btn-primary{background:linear-gradient(135deg,var(--teal),var(--teal-dark)) !important;border:none !important;box-shadow:0 3px 12px rgba(11,168,150,0.30) !important;}
.btn-primary:hover{transform:translateY(-1px) !important;box-shadow:0 6px 18px rgba(11,168,150,0.40) !important;}
.btn-secondary{background:var(--slate-100) !important;border:1px solid var(--slate-200) !important;color:var(--slate-700) !important;}
.btn-warning{background:linear-gradient(135deg,var(--amber),#D97706) !important;border:none !important;color:#000 !important;}
.btn-danger{background:linear-gradient(135deg,var(--rose),#E11D48) !important;border:none !important;}
.btn-success{background:linear-gradient(135deg,var(--green),#059669) !important;border:none !important;color:#fff !important;}
.btn-info{background:linear-gradient(135deg,var(--teal),var(--teal-dark)) !important;border:none !important;color:#fff !important;}
.btn-outline-primary{border-color:var(--teal) !important;color:var(--teal) !important;}
.btn-outline-primary:hover{background:var(--teal) !important;color:#fff !important;}
.btn-outline-secondary{border-color:var(--slate-200) !important;color:var(--slate-600) !important;}
.btn-outline-secondary:hover{background:var(--slate-100) !important;color:var(--slate-900) !important;}
.btn{border-radius:9px !important;font-weight:600 !important;font-size:.82rem !important;transition:all .25s !important;}

.badge-success{background:var(--teal-light) !important;color:var(--teal-dark) !important;border:1px solid var(--teal-mid) !important;}
.badge-warning{background:#FFFBEB !important;color:#C07A00 !important;border:1px solid #FDE68A !important;}
.badge-danger{background:#FFF0F2 !important;color:#C81E3A !important;border:1px solid #FECDD3 !important;}
.badge-info{background:#ECFEFF !important;color:#0891B2 !important;border:1px solid #A5F3FC !important;}
.badge-secondary{background:var(--slate-100) !important;color:var(--slate-600) !important;border:1px solid var(--slate-200) !important;}
.badge{border-radius:6px !important;font-size:.6rem !important;font-weight:700 !important;letter-spacing:.5px !important;padding:3px 8px !important;}

.modal-content{background:var(--white) !important;border:1px solid var(--slate-100) !important;border-radius:16px !important;color:var(--slate-900) !important;}
.modal-header{border-bottom:1px solid var(--slate-100) !important;padding:18px 22px !important;}
.modal-title{font-family:'Sora',sans-serif !important;font-size:1.2rem !important;font-weight:700 !important;color:var(--slate-900) !important;}
.modal-footer{border-top:1px solid var(--slate-100) !important;padding:14px 22px !important;}
.modal-backdrop{background:rgba(15,29,46,0.5) !important;}
.close{color:var(--slate-400) !important;opacity:1 !important;}
.close:hover{color:var(--rose) !important;}

.alert{border-radius:10px !important;border:none !important;font-size:.85rem !important;}
.alert-success{background:var(--teal-light) !important;color:var(--teal-dark) !important;border-left:3px solid var(--teal) !important;}
.alert-warning{background:#FFFBEB !important;color:#C07A00 !important;border-left:3px solid var(--amber) !important;}
.alert-danger{background:#FFF0F2 !important;color:#C81E3A !important;border-left:3px solid var(--rose) !important;}
.alert-info{background:#ECFEFF !important;color:#0891B2 !important;border-left:3px solid #06B6D4 !important;}

.page-link{background:var(--white) !important;border-color:var(--slate-200) !important;color:var(--slate-700) !important;}
.page-link:hover{background:var(--teal-light) !important;color:var(--teal) !important;border-color:var(--teal) !important;}
.page-item.active .page-link{background:linear-gradient(135deg,var(--teal),var(--teal-dark)) !important;border-color:var(--teal) !important;color:#fff !important;}

.list-group-item{background:var(--white) !important;border-color:var(--slate-100) !important;color:var(--slate-700) !important;}
.list-group-item:hover{background:var(--teal-light) !important;}

.select2-container--default .select2-selection--single{background:var(--white) !important;border:1px solid var(--slate-200) !important;border-radius:9px !important;height:38px !important;}
.select2-dropdown{background:var(--white) !important;border:1px solid var(--slate-100) !important;border-radius:10px !important;}
.select2-results__option{color:var(--slate-700) !important;}
.select2-results__option--highlighted{background:var(--teal-light) !important;color:var(--teal-dark) !important;}

::-webkit-scrollbar{width:6px;height:6px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:rgba(11,168,150,0.2);border-radius:10px;}
::-webkit-scrollbar-thumb:hover{background:rgba(11,168,150,0.4);}

.text-muted{color:var(--slate-400) !important;}
.text-primary{color:var(--teal) !important;}
.text-success{color:var(--green) !important;}
.text-warning{color:var(--amber) !important;}
.text-danger{color:var(--rose) !important;}
.text-info{color:#06B6D4 !important;}
h1,h2,h3,h4,h5,h6{color:var(--slate-900) !important;}
p,span,div{color:inherit;}
a{color:var(--teal) !important;}
a:hover{color:var(--teal-dark) !important;}
hr{border-color:var(--slate-100) !important;}
</style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

<nav class="main-header navbar navbar-expand navbar-white">

  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link toggle-sidebar-btn" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-flex align-items-center ml-2">
      <div class="ga-nav-brand">
        <div class="ga-nav-brand-icon">
          <svg width="18" height="18" viewBox="0 0 48 48" fill="none">
            <defs><linearGradient id="nh1" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#fff" stop-opacity=".9"/><stop offset="100%" stop-color="#C2EDE9"/></linearGradient></defs>
            <polygon points="24,3 43,13.5 43,34.5 24,45 5,34.5 5,13.5" stroke="url(#nh1)" stroke-width="2.5" fill="none"/>
            <circle cx="24" cy="24" r="5" fill="url(#nh1)"/>
            <circle cx="24" cy="24" r="2" fill="rgba(7,125,110,0.8)"/>
            <line x1="24" y1="12" x2="24" y2="19" stroke="url(#nh1)" stroke-width="1.6" stroke-linecap="round"/>
            <line x1="24" y1="29" x2="24" y2="36" stroke="url(#nh1)" stroke-width="1.6" stroke-linecap="round"/>
            <line x1="13" y1="19" x2="19" y2="22" stroke="url(#nh1)" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="29" y1="26" x2="35" y2="29" stroke="url(#nh1)" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="35" y1="19" x2="29" y2="22" stroke="url(#nh1)" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="19" y1="26" x2="13" y2="29" stroke="url(#nh1)" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="ga-nav-brand-texts">
          <span class="ga-nav-brand-main">GeoActivos</span>
          <span class="ga-nav-brand-sub">Asset Management</span>
        </div>
        <span style="background:var(--teal-light);border:1px solid var(--teal-mid);border-radius:100px;padding:2px 8px;font-size:.55rem;font-weight:800;letter-spacing:2px;color:var(--teal);margin-left:4px;">PRO</span>
      </div>
    </li>
  </ul>

  <div class="navbar-nav mx-auto d-none d-md-flex">
    <div class="ga-tenant-pill">
      <span class="tdot"></span>
      <i class="fas fa-building" style="font-size:.65rem;opacity:.6;"></i>
      <?= e($tenantNombre) ?>
    </div>
  </div>

  <ul class="navbar-nav ml-auto align-items-center">
    <li class="nav-item">
      <a class="nav-link px-2" data-widget="fullscreen" href="#" title="Pantalla completa"><i class="fas fa-expand-arrows-alt" style="font-size:.85rem;"></i></a>
    </li>
    <li class="nav-item dropdown mx-1">
      <a class="nav-link px-2" data-toggle="dropdown" href="#"><i class="far fa-bell" style="font-size:.85rem;"></i><?php if($alarmCount > 0): ?><span class="badge badge-danger navbar-badge" style="font-size:.45rem;"><?= $alarmCount ?></span><?php endif; ?></a>
      <div class="dropdown-menu dropdown-menu-right">
        <div style="padding:12px 14px 8px;border-bottom:1px solid var(--slate-100);margin-bottom:6px;">
          <div style="font-family:'Sora',sans-serif;font-size:1rem;font-weight:700;color:var(--slate-900);">Notificaciones</div>
          <div style="font-size:.65rem;color:var(--slate-400);letter-spacing:.5px;"><?= $alarmCount ?> alerta<?= $alarmCount !== 1 ? 's' : '' ?> de mantenimiento</div>
        </div>
        <?php if(empty($alarms)): ?>
        <div style="padding:16px;text-align:center;color:var(--slate-400);font-size:.8rem;">Sin alertas pendientes</div>
        <?php else: ?>
          <?php foreach($alarms as $alarm): ?>
          <a class="dropdown-item" href="<?= e(base_url()) ?>/index.php?route=mantenimiento_ver&id=<?= (int)$alarm['id'] ?>">
            <i class="fas fa-tools" style="color:var(--amber);"></i>
            <div style="display:inline-block;vertical-align:middle;margin-left:8px;">
              <div style="font-size:.8rem;font-weight:600;color:var(--slate-800);"><?= e($alarm['activo_nombre'] ?: 'Sin equipo') ?></div>
              <div style="font-size:.65rem;color:var(--slate-400);">Vence: <?= e(date('d/m/Y', strtotime($alarm['fecha_programada']))) ?></div>
            </div>
          </a>
          <?php endforeach; ?>
        <?php endif; ?>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="<?= e(base_url()) ?>/index.php?route=calendario"><i class="fas fa-calendar-alt" style="color:var(--teal);"></i><span>Ver calendario</span></a>
      </div>
    </li>
    <li class="nav-item dropdown mx-1">
      <a class="nav-link px-2" data-toggle="dropdown" href="#" title="<?= get_lang() === 'es' ? 'Cambiar idioma' : 'Change language' ?>">
        <span style="font-size:.75rem;font-weight:700;color:var(--teal);"><?= strtoupper(get_lang()) ?></span>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <div style="padding:10px 14px;border-bottom:1px solid var(--slate-100);margin-bottom:4px;">
          <div style="font-size:.7rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--slate-400);"><?= get_lang() === 'es' ? 'Idioma' : 'Language' ?></div>
        </div>
        <?php $currentRoute = $_GET['route'] ?? 'inicio'; ?>
        <a class="dropdown-item" href="<?= e(base_url()) ?>/index.php?route=set_lang&lang=es&back=<?= e($currentRoute) ?>">
          <span class="<?= get_lang() === 'es' ? 'text-teal' : '' ?>" style="<?= get_lang() === 'es' ? 'font-weight:700;' : '' ?>">🇪🇸 Español</span>
        </a>
        <a class="dropdown-item" href="<?= e(base_url()) ?>/index.php?route=set_lang&lang=en&back=<?= e($currentRoute) ?>">
          <span class="<?= get_lang() === 'en' ? 'text-teal' : '' ?>" style="<?= get_lang() === 'en' ? 'font-weight:700;' : '' ?>">🇬🇧 English</span>
        </a>
      </div>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link p-1" data-toggle="dropdown" href="#">
        <div class="ga-user-chip">
          <div class="ga-user-avatar"><i class="fas fa-user" style="font-size:11px;"></i></div>
          <div class="d-none d-md-block">
            <div class="ga-user-name"><?= e($userNombre) ?></div>
            <div class="ga-user-role"><?= e($rolNombre ?: 'Usuario') ?></div>
          </div>
          <i class="fas fa-angle-down" style="font-size:.65rem;color:var(--slate-400);margin-right:2px;"></i>
        </div>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <div style="padding:12px 14px 10px;">
          <div style="font-weight:700;color:var(--slate-900);font-size:.9rem;"><?= e($userNombre) ?></div>
          <?php if($userEmail): ?><div style="font-size:.72rem;color:var(--slate-400);margin-top:1px;"><?= e($userEmail) ?></div><?php endif; ?>
          <div style="margin-top:6px;"><span style="background:var(--teal-light);border:1px solid var(--teal-mid);border-radius:100px;padding:2px 10px;font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--teal);"><?= e($rolNombre ?: 'USER') ?></span></div>
        </div>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="<?= e(base_url()) ?>/index.php?route=inicio"><i class="fas fa-chart-pie" style="color:var(--teal);width:16px;"></i> Inicio</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger" href="<?= e(base_url()) ?>/index.php?route=logout"><i class="fas fa-sign-out-alt" style="width:16px;"></i> Cerrar sesión</a>
      </div>
    </li>
  </ul>
</nav>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
        <div>
          <div class="ga-page-title"><?= e($pageTitle) ?></div>
            <ol class="ga-breadcrumb">
            <li><a href="<?= e(base_url()) ?>/index.php?route=inicio"><i class="fas fa-home"></i></a></li>
            <?php $last = count($crumbs)-1; foreach($crumbs as $i=>$c){ if($i===$last) echo '<li class="active">'.e($c).'</li>'; else echo '<li>'.e($c).'</li>'; } ?>
          </ol>
        </div>
        <div style="font-size:.65rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--slate-400);display:flex;align-items:center;gap:8px;">
          <span style="width:6px;height:6px;border-radius:50%;background:var(--teal);box-shadow:0 0 6px var(--teal);display:inline-block;animation:gaPulse 2s infinite;"></span>
          GeoActivos · Sistema estable
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">

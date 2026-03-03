<?php
// app/views/layout/header.php
require_once __DIR__ . '/../../core/Helpers.php';
require_once __DIR__ . '/../../core/Auth.php';

Auth::requireLogin();

$route        = (string)($_GET['route'] ?? 'dashboard');
$userNombre   = (string)($_SESSION['user']['nombre'] ?? 'Usuario');
$userEmail    = (string)($_SESSION['user']['email'] ?? '');
$rolNombre    = (string)($_SESSION['rol_nombre'] ?? ($_SESSION['user']['rol_nombre'] ?? ''));
$tenantNombre = (string)($_SESSION['tenant']['nombre'] ?? ($_SESSION['user']['tenant_nombre'] ?? 'Cliente'));

$map = [
  'dashboard'             => ['Dashboard'],
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
  'empresas'              => ['Administración','Empresas'],
  'empresa_form'          => ['Administración','Empresas','Formulario'],
  'usuarios'              => ['Administración','Usuarios'],
  'usuario_form'          => ['Administración','Usuarios','Formulario'],
  'roles'                 => ['Administración','Roles y permisos'],
  'rol_form'              => ['Administración','Roles y permisos','Formulario'],
  'rol_permisos'          => ['Administración','Roles y permisos','Permisos'],
];

$crumbs    = $map[$route] ?? ['Módulo'];
$pageTitle = end($crumbs);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> · GeoActivos</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(base_url()) ?>/assets/css/custom.css">

<style>
/* ═══════════════════════════════════════════
   GEOACTIVOS — GLOBAL DARK THEME
   Negro + Cyan · Consistent con Login
═══════════════════════════════════════════ */
:root{
  --ga-blue:#1a6fff;
  --ga-cyan:#00e5ff;
  --ga-teal:#00bfa5;
  --ga-dark:#060c1a;
  --ga-darker:#03060f;
  --ga-text:#f0f6ff;
  --ga-muted:#4e6d8c;
  --ga-green:#00e676;
  --ga-gold:#ffb300;
  --ga-danger:#ef4444;
  --ga-border:rgba(26,111,255,0.18);
  --ga-card:rgba(4,8,22,0.85);
  --sidebar-w:240px;
}

/* ── BODY / WRAPPER ── */
body,
.wrapper,
.content-wrapper{
  background:var(--ga-darker) !important;
  color:var(--ga-text) !important;
  font-family:'Space Grotesk',sans-serif !important;
}

.wrapper {
  padding-top: 56px !important;
}

/* Global grid overlay on body */
body::before{
  content:'';
  position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:
    linear-gradient(rgba(26,111,255,0.03) 1px,transparent 1px),
    linear-gradient(90deg,rgba(26,111,255,0.03) 1px,transparent 1px);
  background-size:52px 52px;
}

/* ── NAVBAR ── */
.main-header.navbar{
  background:rgba(3,6,15,0.97) !important;
  border-bottom:1px solid var(--ga-border) !important;
  box-shadow:0 2px 24px rgba(0,0,0,.5) !important;
  height:56px;
  backdrop-filter:blur(20px);
  position:fixed !important;
  top:0 !important;
  left:0 !important;
  right:0 !important;
  width:100vw !important;
  z-index:1052 !important;
  margin:0 !important;
}
.main-header.navbar::after{
  content:'';position:absolute;
  bottom:0;left:10%;right:10%;height:1px;
  background:linear-gradient(90deg,transparent,var(--ga-blue),var(--ga-cyan),transparent);
  opacity:.5;
}

/* Brand chip in navbar */
.ga-nav-brand{
  display:flex;align-items:center;gap:10px;
  padding:4px 0;
}
.ga-nav-brand-icon{
  width:34px;height:34px;border-radius:9px;
  background:linear-gradient(135deg,rgba(26,111,255,.3),rgba(0,229,255,.15));
  border:1px solid rgba(0,229,255,.2);
  display:flex;align-items:center;justify-content:center;
  position:relative;flex-shrink:0;
}
.ga-nav-brand-icon i{color:var(--ga-cyan);font-size:14px;}
.ga-nav-brand-texts{display:flex;flex-direction:column;line-height:1.1;}
.ga-nav-brand-main{
  font-family:'Bebas Neue',cursive;
  font-size:1.1rem;letter-spacing:2px;
  background:linear-gradient(90deg,#fff,var(--ga-cyan));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.ga-nav-brand-sub{
  font-size:.52rem;font-weight:600;
  letter-spacing:2.5px;text-transform:uppercase;
  color:var(--ga-muted);
}

/* Navbar icons */
.main-header .nav-link{
  color:rgba(160,200,240,.7) !important;
  transition:color .2s !important;
}
.main-header .nav-link:hover{color:var(--ga-cyan) !important;}

/* Tenant pill */
.ga-tenant-pill{
  display:inline-flex;align-items:center;gap:7px;
  background:rgba(0,229,255,.06);
  border:1px solid rgba(0,229,255,.15);
  border-radius:100px;padding:5px 14px;
  font-size:.72rem;font-weight:600;
  letter-spacing:.5px;color:rgba(180,220,255,.8);
}
.ga-tenant-pill .tdot{
  width:6px;height:6px;border-radius:50%;
  background:var(--ga-green);
  box-shadow:0 0 6px var(--ga-green);
  animation:gaPulse 2s infinite;flex-shrink:0;
}
@keyframes gaPulse{0%,100%{box-shadow:0 0 4px var(--ga-green)}50%{box-shadow:0 0 12px var(--ga-green)}}

/* User dropdown trigger */
.ga-user-chip{
  display:flex;align-items:center;gap:9px;
  padding:4px 10px 4px 4px;
  background:rgba(26,111,255,.07);
  border:1px solid rgba(26,111,255,.18);
  border-radius:100px;
  cursor:pointer;transition:all .25s;
}
.ga-user-chip:hover{background:rgba(26,111,255,.14);border-color:rgba(26,111,255,.35);}
.ga-user-avatar{
  width:30px;height:30px;border-radius:50%;
  background:linear-gradient(135deg,var(--ga-blue),#5b21b6);
  display:flex;align-items:center;justify-content:center;
  font-size:12px;color:#fff;flex-shrink:0;
}
.ga-user-name{
  font-size:.78rem;font-weight:700;
  color:#fff;line-height:1;
}
.ga-user-role{
  font-size:.62rem;color:var(--ga-muted);
  line-height:1;margin-top:1px;
}

/* Navbar dropdown */
.navbar .dropdown-menu{
  background:rgba(4,8,22,.97) !important;
  border:1px solid var(--ga-border) !important;
  border-radius:12px !important;
  box-shadow:0 20px 60px rgba(0,0,0,.6) !important;
  padding:8px !important;
  min-width:220px;
  backdrop-filter:blur(20px);
}
.navbar .dropdown-item{
  color:rgba(160,200,240,.8) !important;
  border-radius:8px !important;
  padding:9px 12px !important;
  font-size:.82rem !important;
  transition:all .2s !important;
  display:flex;align-items:center;gap:9px;
}
.navbar .dropdown-item:hover{
  background:rgba(26,111,255,.12) !important;
  color:#fff !important;
}
.navbar .dropdown-item.text-danger{color:rgba(248,113,113,.8) !important;}
.navbar .dropdown-item.text-danger:hover{background:rgba(239,68,68,.1) !important;color:#f87171 !important;}
.navbar .dropdown-divider{border-color:rgba(26,111,255,.1) !important;margin:6px 0 !important;}
.navbar .dropdown-item-text{
  color:rgba(140,170,210,.6) !important;
  font-size:.75rem !important;padding:8px 12px !important;
}
.navbar-badge{font-size:.55rem !important;}

/* ── CONTENT HEADER (breadcrumb bar) ── */
.content-header{
  background:rgba(3,6,15,.9) !important;
  border-bottom:1px solid rgba(26,111,255,.1) !important;
  padding:14px 20px !important;
  position:relative;
}
.ga-page-title{
  font-family:'Bebas Neue',cursive;
  font-size:1.6rem;letter-spacing:2px;
  background:linear-gradient(90deg,#fff,var(--ga-cyan));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
  line-height:1;margin-bottom:4px;
}
.ga-breadcrumb{
  display:flex;align-items:center;gap:6px;
  list-style:none;padding:0;margin:0;
  font-size:.7rem;font-weight:600;
  letter-spacing:.5px;text-transform:uppercase;
}
.ga-breadcrumb li{color:var(--ga-muted);}
.ga-breadcrumb li a{color:var(--ga-muted);text-decoration:none;transition:color .2s;}
.ga-breadcrumb li a:hover{color:var(--ga-cyan);}
.ga-breadcrumb li.active{color:var(--ga-cyan);}
.ga-breadcrumb li+li::before{content:'›';margin-right:6px;opacity:.4;}

/* ── CONTENT WRAPPER ── */
.content-wrapper{
  padding:20px !important;
  transition: margin-left 0s cubic-bezier(0.4,0,0.2,1), width 0s cubic-bezier(0.4,0,0.2,1);
  margin-left: 0 !important;
  width: 100%;
}
@media(max-width:768px){
  .content-wrapper{
    margin-left:0 !important;
    width:100%;
  }
}
body.sidebar-collapse .content-wrapper{
  margin-left: 0 !important;
  width: 100%;
}
@media(max-width:768px){
  body.sidebar-collapse .content-wrapper{
    margin-left:0 !important;
    width:100% !important;
  }
}
.content{padding:0 !important;}
.container-fluid{padding:0 !important;}

/* ── ALL CARDS in system ── */
.card{
  background:rgba(4,8,22,0.82) !important;
  border:1px solid var(--ga-border) !important;
  border-radius:12px !important;
  color:var(--ga-text) !important;
  box-shadow:0 4px 24px rgba(0,0,0,.3) !important;
}
.card-header{
  background:rgba(26,111,255,.05) !important;
  border-bottom:1px solid rgba(26,111,255,.1) !important;
  color:var(--ga-text) !important;
}
.card-title{
  font-family:'Bebas Neue',cursive !important;
  letter-spacing:1px !important;
  color:#fff !important;
  font-size:1rem !important;
}
.card-body{color:var(--ga-text) !important;}

/* ── TABLES ── */
.table{color:rgba(180,210,240,.85) !important;}
.table thead th{
  background:rgba(26,111,255,.08) !important;
  border-bottom:1px solid rgba(26,111,255,.2) !important;
  color:var(--ga-cyan) !important;
  font-size:.68rem !important;
  font-weight:700 !important;
  letter-spacing:2px !important;
  text-transform:uppercase !important;
}
.table td,.table th{
  border-color:rgba(26,111,255,.07) !important;
  vertical-align:middle !important;
}
.table-striped tbody tr:nth-of-type(odd){background:rgba(26,111,255,.03) !important;}
.table-hover tbody tr:hover{background:rgba(26,111,255,.07) !important;}

/* ── FORMS ── */
.form-control,.form-select{
  background:rgba(255,255,255,.04) !important;
  border:1px solid rgba(26,111,255,.2) !important;
  border-radius:9px !important;
  color:var(--ga-text) !important;
  font-family:'Space Grotesk',sans-serif !important;
  transition:all .3s !important;
}
.form-control:focus,.form-select:focus{
  border-color:var(--ga-blue) !important;
  background:rgba(26,111,255,.07) !important;
  box-shadow:0 0 0 3px rgba(26,111,255,.12) !important;
  color:#fff !important;
}
.form-control::placeholder{color:rgba(78,109,140,.6) !important;}
label,.col-form-label{
  font-size:.72rem !important;font-weight:700 !important;
  letter-spacing:1px !important;text-transform:uppercase !important;
  color:var(--ga-muted) !important;margin-bottom:5px !important;
}
select.form-control option{background:#060c1a;color:var(--ga-text);}

/* ── BUTTONS ── */
.btn-primary{background:linear-gradient(135deg,var(--ga-blue),#0050cc) !important;border:none !important;box-shadow:0 4px 16px rgba(26,111,255,.3) !important;}
.btn-primary:hover{transform:translateY(-1px) !important;box-shadow:0 8px 24px rgba(26,111,255,.45) !important;}
.btn-secondary{background:rgba(78,109,140,.2) !important;border:1px solid rgba(78,109,140,.3) !important;color:rgba(180,210,240,.8) !important;}
.btn-warning{background:linear-gradient(135deg,#f59e0b,#fbbf24) !important;border:none !important;color:#000 !important;}
.btn-danger{background:linear-gradient(135deg,#ef4444,#f87171) !important;border:none !important;}
.btn-success{background:linear-gradient(135deg,#00e676,#00c853) !important;border:none !important;color:#000 !important;}
.btn-info{background:linear-gradient(135deg,var(--ga-cyan),var(--ga-teal)) !important;border:none !important;color:#000 !important;}
.btn-outline-primary{border-color:rgba(26,111,255,.4) !important;color:rgba(160,200,255,.8) !important;}
.btn-outline-primary:hover{background:rgba(26,111,255,.12) !important;border-color:var(--ga-blue) !important;color:#fff !important;}
.btn-outline-secondary{border-color:rgba(78,109,140,.3) !important;color:rgba(140,170,210,.7) !important;}
.btn-outline-secondary:hover{background:rgba(78,109,140,.12) !important;color:#fff !important;}
.btn-outline-warning{border-color:rgba(255,179,0,.35) !important;color:rgba(255,200,80,.8) !important;}
.btn-outline-warning:hover{background:rgba(255,179,0,.1) !important;color:var(--ga-gold) !important;}
.btn-outline-info{border-color:rgba(0,229,255,.3) !important;color:rgba(0,229,255,.8) !important;}
.btn-outline-info:hover{background:rgba(0,229,255,.08) !important;color:var(--ga-cyan) !important;}
.btn{border-radius:9px !important;font-weight:600 !important;font-size:.82rem !important;transition:all .25s !important;}

/* ── BADGES ── */
.badge-success{background:rgba(0,230,118,.15) !important;color:#00e676 !important;border:1px solid rgba(0,230,118,.25) !important;}
.badge-warning{background:rgba(255,179,0,.15) !important;color:#ffb300 !important;border:1px solid rgba(255,179,0,.25) !important;}
.badge-danger {background:rgba(239,68,68,.15)  !important;color:#f87171 !important;border:1px solid rgba(239,68,68,.25) !important;}
.badge-info   {background:rgba(0,229,255,.12)   !important;color:#00e5ff !important;border:1px solid rgba(0,229,255,.2) !important;}
.badge-secondary{background:rgba(78,109,140,.15)!important;color:#94a3b8 !important;border:1px solid rgba(78,109,140,.2) !important;}
.badge{border-radius:6px !important;font-size:.6rem !important;font-weight:700 !important;letter-spacing:.8px !important;padding:3px 8px !important;}

/* ── MODALS ── */
.modal-content{
  background:rgba(4,8,22,.97) !important;
  border:1px solid var(--ga-border) !important;
  border-radius:16px !important;
  color:var(--ga-text) !important;
}
.modal-header{
  border-bottom:1px solid rgba(26,111,255,.12) !important;
  padding:18px 22px !important;
}
.modal-title{
  font-family:'Bebas Neue',cursive !important;
  font-size:1.3rem !important;letter-spacing:1.5px !important;color:#fff !important;
}
.modal-footer{border-top:1px solid rgba(26,111,255,.1) !important;padding:14px 22px !important;}
.modal-backdrop{background:rgba(3,6,15,.7) !important;}
.close{color:rgba(150,180,220,.6) !important;opacity:1 !important;}
.close:hover{color:#f87171 !important;}

/* ── ALERTS ── */
.alert{border-radius:10px !important;border:none !important;font-size:.85rem !important;}
.alert-success{background:rgba(0,230,118,.1) !important;color:#00e676 !important;border-left:3px solid #00e676 !important;}
.alert-warning{background:rgba(255,179,0,.1) !important;color:#ffb300 !important;border-left:3px solid #ffb300 !important;}
.alert-danger {background:rgba(239,68,68,.1) !important;color:#f87171 !important;border-left:3px solid #f87171 !important;}
.alert-info   {background:rgba(0,229,255,.08) !important;color:#00e5ff !important;border-left:3px solid #00e5ff !important;}

/* ── PAGINATION ── */
.page-link{background:rgba(4,8,22,.8) !important;border-color:rgba(26,111,255,.2) !important;color:rgba(160,200,240,.7) !important;}
.page-link:hover{background:rgba(26,111,255,.12) !important;color:var(--ga-cyan) !important;}
.page-item.active .page-link{background:var(--ga-blue) !important;border-color:var(--ga-blue) !important;color:#fff !important;}

/* ── LIST GROUPS ── */
.list-group-item{
  background:rgba(4,8,22,.7) !important;
  border-color:rgba(26,111,255,.1) !important;
  color:rgba(180,210,240,.8) !important;
}
.list-group-item:hover{background:rgba(26,111,255,.07) !important;}

/* ── SELECT2 / CUSTOM SELECT ── */
.select2-container--default .select2-selection--single{
  background:rgba(255,255,255,.04) !important;
  border:1px solid rgba(26,111,255,.2) !important;
  border-radius:9px !important;
  height:38px !important;
}
.select2-dropdown{
  background:rgba(4,8,22,.98) !important;
  border:1px solid rgba(26,111,255,.2) !important;
  border-radius:10px !important;
}
.select2-results__option{color:rgba(180,210,240,.8) !important;}
.select2-results__option--highlighted{background:rgba(26,111,255,.15) !important;color:#fff !important;}

/* ── SCROLLBAR ── */
::-webkit-scrollbar{width:5px;height:5px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:rgba(26,111,255,.3);border-radius:10px;}
::-webkit-scrollbar-thumb:hover{background:rgba(0,229,255,.4);}

/* ── TEXT HELPERS ── */
.text-muted{color:var(--ga-muted) !important;}
.text-primary{color:var(--ga-cyan) !important;}
.text-success{color:var(--ga-green) !important;}
.text-warning{color:var(--ga-gold) !important;}
.text-danger{color:#f87171 !important;}
.text-info{color:var(--ga-cyan) !important;}
h1,h2,h3,h4,h5,h6{color:#fff !important;}
p,span,div{color:inherit;}
a{color:var(--ga-cyan) !important;}
a:hover{color:#fff !important;}
hr{border-color:rgba(26,111,255,.1) !important;}
</style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

<!-- ══════════ NAVBAR ══════════ -->
<nav class="main-header navbar navbar-expand navbar-dark">

  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link toggle-sidebar-btn" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
    <li class="nav-item d-none d-sm-flex align-items-center ml-2">
      <div class="ga-nav-brand">
        <div class="ga-nav-brand-icon">
          <!-- SVG Logo -->
          <svg width="18" height="18" viewBox="0 0 48 48" fill="none">
            <defs>
              <linearGradient id="nh1" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#1a6fff"/><stop offset="100%" stop-color="#00e5ff"/>
              </linearGradient>
            </defs>
            <polygon points="24,2 42,12 42,36 24,46 6,36 6,12" stroke="url(#nh1)" stroke-width="2.5" fill="rgba(26,111,255,0.15)"/>
            <circle cx="24" cy="24" r="5" fill="url(#nh1)" opacity=".9"/>
            <circle cx="24" cy="24" r="2" fill="white" opacity=".9"/>
          </svg>
        </div>
        <div class="ga-nav-brand-texts">
          <span class="ga-nav-brand-main">GeoActivos</span>
          <span class="ga-nav-brand-sub">Asset Management</span>
        </div>
        <span style="background:rgba(0,229,255,.1);border:1px solid rgba(0,229,255,.2);border-radius:100px;padding:2px 8px;font-size:.55rem;font-weight:800;letter-spacing:2px;color:var(--ga-cyan);margin-left:4px;">PRO</span>
      </div>
    </li>
  </ul>

  <!-- Center: Tenant -->
  <div class="navbar-nav mx-auto d-none d-md-flex">
    <div class="ga-tenant-pill">
      <span class="tdot"></span>
      <i class="fas fa-building" style="font-size:.65rem;opacity:.6;"></i>
      <?= e($tenantNombre) ?>
    </div>
  </div>

  <ul class="navbar-nav ml-auto align-items-center">

    <!-- Fullscreen -->
    <li class="nav-item">
      <a class="nav-link px-2" data-widget="fullscreen" href="#" title="Pantalla completa">
        <i class="fas fa-expand-arrows-alt" style="font-size:.85rem;"></i>
      </a>
    </li>

    <!-- Notifications -->
    <li class="nav-item dropdown mx-1">
      <a class="nav-link px-2" data-toggle="dropdown" href="#">
        <i class="far fa-bell" style="font-size:.85rem;"></i>
        <span class="badge badge-warning navbar-badge" style="font-size:.45rem;">!</span>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <div style="padding:12px 14px 8px;border-bottom:1px solid rgba(26,111,255,.1);margin-bottom:6px;">
          <div style="font-family:'Bebas Neue',cursive;font-size:1rem;letter-spacing:1.5px;color:#fff;">Notificaciones</div>
          <div style="font-size:.65rem;color:var(--ga-muted);letter-spacing:.5px;">Sistema GeoActivos PRO</div>
        </div>
        <a class="dropdown-item" href="#">
          <i class="fas fa-clock" style="color:var(--ga-gold);"></i>
          <span>Alertas de mantenimiento próximamente</span>
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="<?= e(base_url()) ?>/index.php?route=dashboard">
          <i class="fas fa-home" style="color:var(--ga-cyan);"></i>
          <span>Ir al dashboard</span>
        </a>
      </div>
    </li>

    <!-- User -->
    <li class="nav-item dropdown">
      <a class="nav-link p-1" data-toggle="dropdown" href="#">
        <div class="ga-user-chip">
          <div class="ga-user-avatar"><i class="fas fa-user" style="font-size:11px;"></i></div>
          <div class="d-none d-md-block">
            <div class="ga-user-name"><?= e($userNombre) ?></div>
            <div class="ga-user-role"><?= e($rolNombre ?: 'Usuario') ?></div>
          </div>
          <i class="fas fa-angle-down" style="font-size:.65rem;color:var(--ga-muted);margin-right:2px;"></i>
        </div>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <div style="padding:12px 14px 10px;">
          <div style="font-weight:800;color:#fff;font-size:.9rem;"><?= e($userNombre) ?></div>
          <?php if($userEmail): ?>
            <div style="font-size:.72rem;color:var(--ga-muted);margin-top:1px;"><?= e($userEmail) ?></div>
          <?php endif; ?>
          <div style="margin-top:6px;">
            <span style="background:rgba(0,229,255,.1);border:1px solid rgba(0,229,255,.2);border-radius:100px;padding:2px 10px;font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ga-cyan);"><?= e($rolNombre ?: 'USER') ?></span>
          </div>
        </div>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="<?= e(base_url()) ?>/index.php?route=dashboard">
          <i class="fas fa-chart-pie" style="color:var(--ga-cyan);width:16px;"></i> Dashboard
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger" href="<?= e(base_url()) ?>/index.php?route=logout">
          <i class="fas fa-sign-out-alt" style="width:16px;"></i> Cerrar sesión
        </a>
      </div>
    </li>

  </ul>
</nav>

<!-- ══════════ CONTENT HEADER ══════════ -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
          <div>
            <div class="ga-page-title"><?= e($pageTitle) ?></div>
            <ol class="ga-breadcrumb">
              <li><a href="<?= e(base_url()) ?>/index.php?route=dashboard"><i class="fas fa-home"></i></a></li>
              <?php
                $last = count($crumbs)-1;
                foreach($crumbs as $i=>$c){
                  if($i===$last) echo '<li class="active">'.e($c).'</li>';
                  else echo '<li>'.e($c).'</li>';
                }
              ?>
            </ol>
          </div>
          <div style="font-size:.65rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--ga-muted);display:flex;align-items:center;gap:8px;">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--ga-green);box-shadow:0 0 6px var(--ga-green);display:inline-block;animation:gaPulse 2s infinite;"></span>
            GeSaProv Project Design · Sistema estable
          </div>
        </div>
      </div>
    </div>
    <section class="content">
      <div class="container-fluid">
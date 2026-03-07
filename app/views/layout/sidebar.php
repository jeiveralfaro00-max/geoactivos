<?php
// app/views/layout/sidebar.php
require_once __DIR__ . '/../../core/Helpers.php';
require_once __DIR__ . '/../../core/Auth.php';

$current = $_GET['route'] ?? 'dashboard';

function ga_active($routes, $cur){
  return in_array($cur,(array)$routes,true) ? 'active' : '';
}
function can_perm($code){
  $isSuper = (int)($_SESSION['user']['es_superadmin'] ?? 0);
  if($isSuper===1) return true;
  if(class_exists('Auth') && method_exists('Auth','can')) return Auth::can($code);
  return true;
}

$tenantNombre  = $_SESSION['user']['tenant_nombre'] ?? ($_SESSION['tenant']['nombre'] ?? 'Cliente');
$usuarioNombre = $_SESSION['user']['nombre'] ?? 'Administrador';
$rolNombre     = $_SESSION['rol_nombre'] ?? ($_SESSION['user']['rol_nombre'] ?? 'Usuario');

$activosRoutes   = ['activos','activos_form','activo_detalle'];
$papeleraRoutes  = ['activos_eliminados'];
$mantsRoutes     = ['mantenimientos','mantenimiento_form','mantenimiento_detalle','mantenimiento_ver'];
$calibRoutes     = ['calibraciones','calibracion_form','calibracion_detalle','calibracion_print'];
$patronesRoutes  = ['patrones','patron_form','patron_delete'];
$auditoriaRoutes = ['audit_log','activo_auditoria'];
$confRoutes      = ['categorias','categoria_form','marcas','marca_form','sedes','sede_form','areas','area_form','proveedores','proveedor_form','tipos_activo','tipo_activo_form'];
$secRoutes       = ['empresas','empresa_form','usuarios','usuario_form','roles','rol_form','rol_permisos','rol_delete'];

$showConfig   = can_perm('config.view') || can_perm('config.manage');
$showAudit    = can_perm('auditoria.view') || can_perm('dashboard.view');
$showSec      = can_perm('empresas.view')||can_perm('usuarios.view')||can_perm('roles.view');
$showCalib    = can_perm('calibraciones.view')||can_perm('calibraciones.edit');
$showPatrones = can_perm('patrones.view')||can_perm('patrones.edit');

$confInConf = in_array($current, $confRoutes, true);
$secInSec   = in_array($current, $secRoutes, true);
?>

<style>
/* ═══════════════════════════════════════════
   SIDEBAR DRAWER PRO — GeoActivos
═══════════════════════════════════════════ */
.main-sidebar,
.main-sidebar .sidebar {
  width: 280px !important;
  overflow: visible !important;
  transition: transform 0.35s cubic-bezier(0.4,0,0.2,1), box-shadow 0.35s ease;
}
.main-sidebar {
  background: #03060f !important;
  border-right: 1px solid rgba(26,111,255,0.15) !important;
  box-shadow: -4px 0 40px rgba(0,0,0,.6) !important;
  z-index: 1050 !important;
  overflow-y: auto !important;
  overflow-x: hidden !important;
  position: fixed;
  top: 0;
  left: 0;
  width: 280px;
  height: 100vh;
  transform: translateX(-280px);
}
.main-sidebar::-webkit-scrollbar {
  width: 6px;
}
.main-sidebar::-webkit-scrollbar-track {
  background: transparent;
}
.main-sidebar::-webkit-scrollbar-thumb {
  background: rgba(26,111,255,.3);
  border-radius: 10px;
}
.main-sidebar::-webkit-scrollbar-thumb:hover {
  background: rgba(26,111,255,.5);
}
/* Sidebar abierto */
body.sidebar-collapse .main-sidebar,
.main-sidebar.show {
  transform: translateX(0);
  box-shadow: 8px 0 40px rgba(0,0,0,.8);
}

/* Backdrop para cerrar sidebar */
.sidebar-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(3,6,15,.8);
  backdrop-filter: blur(4px);
  z-index: 1049;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.35s ease, visibility 0.35s ease;
  pointer-events: none;
}
body.sidebar-collapse .sidebar-backdrop,
.sidebar-backdrop.show {
  opacity: 1;
  visibility: visible;
  pointer-events: all;
}
.content-wrapper,
.main-footer {
  margin-left: 0 !important;
  transition: none;
}

/* ── BRAND ── */
.sb-brand {
  display: none !important;
}
body.sidebar-collapse .sb-brand { justify-content: flex-start; }
body.sidebar-collapse .sb-brand > *:not(.sb-brand-ico) { display: block; }
body.sidebar-collapse .sb-brand { padding-left: 10px; padding-right: 10px; }
.sb-brand::after {
  content: '';
  position: absolute;
  bottom: 0; left: 15%; right: 15%; height: 1px;
  background: linear-gradient(90deg,transparent,rgba(0,229,255,.35),transparent);
}
.sb-brand-ico {
  width: 38px; height: 38px;
  border-radius: 11px;
  background: linear-gradient(135deg,rgba(26,111,255,.25),rgba(0,229,255,.1));
  border: 1px solid rgba(0,229,255,.22);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 0 18px rgba(26,111,255,.2);
}
.sb-brand-main {
  font-family: 'Bebas Neue', cursive;
  font-size: 1.25rem; letter-spacing: 2.5px;
  background: linear-gradient(90deg,#fff,#00e5ff);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
  line-height: 1; display: block;
}
.sb-brand-sub {
  font-size: .5rem; font-weight: 700;
  letter-spacing: 2.5px; text-transform: uppercase;
  color: rgba(78,109,140,.65); display: block; margin-top: 2px;
}
.sb-pro-tag {
  margin-left: auto;
  background: rgba(0,229,255,.08);
  border: 1px solid rgba(0,229,255,.2);
  border-radius: 5px;
  padding: 2px 7px;
  font-size: .5rem; font-weight: 800;
  letter-spacing: 2px; text-transform: uppercase;
  color: #00e5ff; flex-shrink: 0;
}

/* ── USER PANEL ── */
.sb-user {
  display: none !important;
}
body.sidebar-collapse .sb-user { justify-content: flex-start; padding-left: 8px; padding-right: 8px; }
body.sidebar-collapse .sb-user > div:not(.sb-user-avatar) { display: block; }
.sb-user-avatar {
  width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
  background: linear-gradient(135deg,rgba(26,111,255,.3),rgba(91,33,182,.3));
  border: 1px solid rgba(0,229,255,.2);
  display: flex; align-items: center; justify-content: center;
  font-size: 15px; color: rgba(200,230,255,.8);
  box-shadow: 0 0 14px rgba(26,111,255,.15);
}
.sb-user-name {
  font-weight: 700; font-size: .82rem; color: #fff;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  max-width: 160px; display: block; line-height: 1.1;
}
.sb-user-role {
  font-size: .6rem; font-weight: 600;
  letter-spacing: .8px; text-transform: uppercase;
  color: rgba(78,109,140,.7); display: block; margin-top: 2px;
}
.sb-online {
  width: 5px; height: 5px; border-radius: 50%;
  background: #00e676; box-shadow: 0 0 6px #00e676;
  display: inline-block; margin-right: 4px;
  animation: sbPulse 2s infinite;
}
@keyframes sbPulse {
  0%,100%{box-shadow:0 0 4px #00e676}
  50%{box-shadow:0 0 10px #00e676,0 0 18px rgba(0,230,118,.3)}
}

/* ── NAV WRAPPER ── */
.sb-nav {
  padding: 8px 0 20px;
  overflow-y: auto; overflow-x: visible;
  height: 100%;
}
.sb-nav::-webkit-scrollbar { width: 3px; }
.sb-nav::-webkit-scrollbar-thumb { background: rgba(26,111,255,.2); border-radius: 10px; }

/* ── SECTION LABEL ── */
.sb-section {
  font-size: .55rem; font-weight: 800;
  letter-spacing: 3px; text-transform: uppercase;
  color: rgba(78,109,140,.4);
  padding: 12px 18px 5px;
  display: block;
  overflow: hidden;
  transition: opacity 0.3s ease, max-height 0.3s ease;
  max-height: 20px;
  opacity: 1;
}
body.sidebar-collapse .sb-section {
  opacity: 1;
  max-height: 20px;
  padding: 12px 18px 5px;
  margin: inherit;
}

/* ── NAV ITEM (normal) ── */
.sb-item {
  margin: 1px 8px;
  position: relative;
  transition: margin 0.3s ease;
}
body.sidebar-collapse .sb-item {
  margin: 1px 8px;
  width: auto;
}
.sb-link {
  display: flex; align-items: center; gap: 0;
  padding: 10px 12px;
  border-radius: 10px;
  color: rgba(130,170,215,.6) !important;
  font-size: .83rem; font-weight: 500;
  text-decoration: none !important;
  transition: all .22s ease;
  position: relative;
  cursor: pointer;
  border: 1px solid transparent;
  user-select: none;
}
.sb-link:hover {
  background: rgba(26,111,255,.09) !important;
  color: rgba(200,230,255,.9) !important;
  border-color: rgba(26,111,255,.15) !important;
}
.sb-link.active {
  background: linear-gradient(135deg,rgba(26,111,255,.2),rgba(0,229,255,.07)) !important;
  color: #fff !important;
  border-color: rgba(26,111,255,.3) !important;
  box-shadow: 0 2px 14px rgba(26,111,255,.14) !important;
}
.sb-link.active::before {
  content: '';
  position: absolute; left: 0; top: 22%; bottom: 22%;
  width: 3px; border-radius: 0 3px 3px 0;
  background: linear-gradient(to bottom,#1a6fff,#00e5ff);
}
.sb-link-ico {
  width: 32px; height: 32px;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: .82rem; flex-shrink: 0; margin-right: 10px;
  transition: all .22s;
  background: rgba(255,255,255,.04);
  color: rgba(120,160,210,.55);
}
body.sidebar-collapse .sb-link-ico {
  width: 32px;
  height: 32px;
  margin: 0 10px 0 0;
}
body.sidebar-collapse .sb-link {
  justify-content: flex-start !important;
  padding-left: 12px !important;
  padding-right: 12px !important;
}
.sb-link:hover .sb-link-ico {
  background: rgba(26,111,255,.15);
  color: #00e5ff;
}
.sb-link.active .sb-link-ico {
  background: rgba(26,111,255,.2);
  color: #00e5ff;
  box-shadow: 0 0 10px rgba(0,229,255,.2);
}
.sb-link-txt { flex: 1; line-height: 1; }
body.sidebar-collapse .sb-link-txt { display: block; }
.sb-badge {
  font-size: .48rem; font-weight: 800;
  letter-spacing: 1px; text-transform: uppercase;
  padding: 2px 6px; border-radius: 4px;
  margin-left: 6px; flex-shrink: 0;
  transition: opacity 0.3s ease;
  opacity: 1;
}
body.sidebar-collapse .sb-badge { display: block; }
.sb-badge.b-pro  { background: rgba(255,179,0,.15); color: #ffb300; border: 1px solid rgba(255,179,0,.25); }
.sb-badge.b-bio  { background: rgba(0,229,255,.1);  color: #00e5ff; border: 1px solid rgba(0,229,255,.2); }
.sb-badge.b-lab  { background: rgba(100,130,160,.12); color: #94a3b8; border: 1px solid rgba(100,130,160,.2); }
.sb-badge.b-inv  { background: rgba(0,229,255,.08); color: #00e5ff; border: 1px solid rgba(0,229,255,.15); }

/* ── DROPDOWN PARENT ── */
.sb-dropdown { position: relative; }
.sb-dropdown-arrow {
  margin-left: auto; font-size: .65rem;
  color: rgba(78,109,140,.5);
  transition: transform .3s ease, color .22s;
  flex-shrink: 0;
  opacity: 1;
}
body.sidebar-collapse .sb-dropdown-arrow { opacity: 1; width: auto; margin: inherit; }
.sb-dropdown.open > .sb-link .sb-dropdown-arrow {
  transform: rotate(90deg);
  color: #00e5ff;
}
.sb-dropdown.open > .sb-link {
  background: rgba(26,111,255,.1) !important;
  color: #fff !important;
  border-color: rgba(26,111,255,.2) !important;
}

/* ── SUBMENU — slide down ── */
.sb-submenu {
  overflow: hidden;
  max-height: 0;
  transition: max-height .35s cubic-bezier(.4,0,.2,1), opacity .25s ease;
  opacity: 0;
  margin: 2px 0 2px 8px;
}
body.sidebar-collapse .sb-submenu {
  display: block;
}
.sb-dropdown.open .sb-submenu {
  max-height: 400px;
  opacity: 1;
  display: block;
}
.sb-subitem { margin: 1px 0; }
.sb-sublink {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 12px 8px 14px;
  border-radius: 8px;
  color: rgba(110,150,200,.55) !important;
  font-size: .78rem; font-weight: 500;
  text-decoration: none !important;
  transition: all .2s;
  border: 1px solid transparent;
  position: relative;
}
.sb-sublink::before {
  content: '';
  width: 4px; height: 4px; border-radius: 50%;
  background: rgba(78,109,140,.4);
  flex-shrink: 0;
  transition: background .2s, box-shadow .2s;
}
.sb-sublink:hover {
  background: rgba(26,111,255,.08) !important;
  color: rgba(180,215,255,.85) !important;
  border-color: rgba(26,111,255,.12) !important;
}
.sb-sublink:hover::before {
  background: #00e5ff;
  box-shadow: 0 0 6px rgba(0,229,255,.5);
}
.sb-sublink.active {
  background: rgba(26,111,255,.1) !important;
  color: #00e5ff !important;
  border-color: rgba(0,229,255,.15) !important;
}
.sb-sublink.active::before {
  background: #00e5ff;
  box-shadow: 0 0 8px rgba(0,229,255,.6);
}

/* ── SEPARATOR ── */
.sb-sep {
  height: 1px;
  background: rgba(26,111,255,.07);
  margin: 7px 16px;
  transition: opacity 0.3s ease, margin 0.3s ease;
  opacity: 1;
}
body.sidebar-collapse .sb-sep {
  opacity: 1;
  margin: 7px 16px;
}

/* ── LOGOUT ── */
.sb-logout .sb-link {
  color: rgba(248,113,113,.55) !important;
}
.sb-logout .sb-link:hover {
  background: rgba(239,68,68,.08) !important;
  color: #f87171 !important;
  border-color: rgba(239,68,68,.15) !important;
}
.sb-logout .sb-link-ico {
  color: rgba(248,113,113,.55) !important;
}
.sb-logout .sb-link:hover .sb-link-ico {
  background: rgba(239,68,68,.15) !important;
  color: #f87171 !important;
}

/* ── FOOTER INFO ── */
.sb-footer {
  padding: 10px 18px 14px;
  font-size: .55rem; font-weight: 700;
  letter-spacing: 1.5px; text-transform: uppercase;
  color: rgba(78,109,140,.35); line-height: 1.8;
  border-top: 1px solid rgba(26,111,255,.07);
  margin-top: 6px;
  transition: opacity 0.3s ease, max-height 0.3s ease;
  max-height: 60px;
  opacity: 1;
  overflow: hidden;
}
body.sidebar-collapse .sb-footer {
  opacity: 1;
  max-height: 60px;
  padding: 10px 18px 14px;
  margin-top: 6px;
  border: inherit;
}

/* ── TOOLTIP on hover (for collapsed states / extra info) ── */
.sb-tooltip-wrap { position: relative; }
.sb-tooltip {
  position: absolute;
  left: calc(100% + 12px); top: 50%;
  transform: translateY(-50%);
  background: rgba(4,8,22,.97);
  border: 1px solid rgba(26,111,255,.25);
  border-radius: 8px;
  padding: 6px 12px;
  font-size: .72rem; font-weight: 600;
  color: rgba(180,215,255,.85);
  white-space: nowrap;
  pointer-events: none;
  opacity: 0;
  transition: opacity .18s, transform .18s;
  z-index: 9999;
  box-shadow: 0 8px 24px rgba(0,0,0,.5);
}
.sb-tooltip::before {
  content: '';
  position: absolute; right: 100%; top: 50%;
  transform: translateY(-50%);
  border: 5px solid transparent;
  border-right-color: rgba(26,111,255,.25);
}

/* ═══════════════════════════════════════════
   MEJORADAS CSS ANIMATIONS — Sidebar Pro
═══════════════════════════════════════════ */

/* Hover mejorado en items del menu */
.sb-link:hover {
  transform: translateX(2px) !important;
  box-shadow: inset 2px 0 0 rgba(26,111,255,.3) !important;
}

/* Animación para iconos en hover */
.sb-link:hover .sb-link-ico {
  transform: scale(1.1);
  color: #00e5ff !important;
}

/* Animación suave para badges */
.sb-badge {
  animation: badgePulse 2.5s ease-in-out infinite;
}

@keyframes badgePulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.65; }
}

/* Sublinks con mejor hover */
.sb-sublink:hover {
  transform: translateX(3px);
  background: rgba(26,111,255,.15) !important;
  color: rgba(200,230,255,.95) !important;
}

/* ═══════════════════════════════════════════
   QUICK ACCESS CARDS — Modal Content
═══════════════════════════════════════════ */

.ga-quick-access {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px;
  background: rgba(26,111,255,.05);
  border: 1px solid rgba(26,111,255,.15);
  border-radius: 10px;
  color: rgba(160,200,240,.8);
  text-decoration: none;
  transition: all .22s ease;
  cursor: pointer;
}

.ga-quick-access:hover {
  background: rgba(26,111,255,.12);
  border-color: rgba(26,111,255,.35);
  color: #fff;
  transform: translateX(3px);
  box-shadow: 0 4px 12px rgba(26,111,255,.15);
}

.ga-qa-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(26,111,255,.15);
  border-radius: 8px;
  color: #00e5ff;
  font-size: 18px;
  flex-shrink: 0;
  transition: all .22s ease;
}

.ga-quick-access:hover .ga-qa-icon {
  background: rgba(26,111,255,.25);
  transform: scale(1.1);
}

.ga-qa-content {
  flex: 1;
  min-width: 0;
}

.ga-qa-title {
  font-size: .78rem;
  font-weight: 700;
  color: #fff;
  margin-bottom: 2px;
  line-height: 1.1;
}

.ga-qa-desc {
  font-size: .65rem;
  color: rgba(100,140,180,.6);
  line-height: 1.2;
}

/* Responsive modal grid */
@media (max-width: 640px) {
  .ga-quick-access {
    padding: 12px;
  }

  .ga-modal[style*="520px"] .ga-modal-bd-inner > div {
    grid-template-columns: 1fr !important;
  }
}
</style>

<aside class="main-sidebar" style="position:fixed;top:0;left:0;height:100vh;">

  <!-- CLOSE BUTTON -->
  <div style="padding:10px 12px;display:flex;justify-content:flex-end;border-bottom:1px solid rgba(26,111,255,.1);">
    <button onclick="document.body.classList.remove('sidebar-collapse');document.querySelector('.main-sidebar').classList.remove('show');document.getElementById('sidebarBackdrop').classList.remove('show');document.body.style.overflow='';" style="background:rgba(26,111,255,.1);border:1px solid rgba(26,111,255,.2);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;color:rgba(150,180,220,.7);cursor:pointer;transition:all .2s;font-size:.85rem;padding:0;">
      <i class="fas fa-times"></i>
    </button>
  </div>

  <!-- BRAND -->
  <a href="<?= e(base_url()) ?>/index.php?route=dashboard" class="sb-brand">
    <div class="sb-brand-ico">
      <svg width="20" height="20" viewBox="0 0 48 48" fill="none">
        <defs>
          <linearGradient id="sbg" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#1a6fff"/><stop offset="100%" stop-color="#00e5ff"/>
          </linearGradient>
        </defs>
        <polygon points="24,2 42,12 42,36 24,46 6,36 6,12" stroke="url(#sbg)" stroke-width="2.5" fill="rgba(26,111,255,0.15)"/>
        <polygon points="24,10 35,16.5 35,31.5 24,38 13,31.5 13,16.5" stroke="url(#sbg)" stroke-width="1.2" fill="none" opacity=".5"/>
        <circle cx="24" cy="24" r="5" fill="url(#sbg)"/>
        <circle cx="24" cy="24" r="2" fill="white" opacity=".9"/>
      </svg>
    </div>
    <div>
      <span class="sb-brand-main">GeoActivos</span>
      <span class="sb-brand-sub">GeSaProv · Multi-Tenant</span>
    </div>
    <span class="sb-pro-tag">PRO</span>
  </a>

  <!-- USER -->
  <div class="sb-user">
    <div class="sb-user-avatar"><i class="fas fa-user"></i></div>
    <div style="min-width:0;">
      <span class="sb-user-name"><?= e($usuarioNombre) ?></span>
      <span class="sb-user-role">
        <span class="sb-online"></span><?= e($rolNombre ?: 'Usuario') ?>
      </span>
    </div>
  </div>

  <!-- NAV -->
  <div class="sb-nav">

    <!-- PRINCIPAL -->
    <span class="sb-section">Principal</span>

    <?php if(can_perm('dashboard.view')): ?>
    <div class="sb-item">
      <a href="<?= e(base_url()) ?>/index.php?route=dashboard"
         class="sb-link <?= ga_active('dashboard',$current) ?>">
        <span class="sb-link-ico"><i class="fas fa-chart-pie"></i></span>
        <span class="sb-link-txt">Dashboard</span>
      </a>
    </div>
    <?php endif; ?>

    <?php if(can_perm('activos.view')): ?>
    <div class="sb-item">
      <a href="<?= e(base_url()) ?>/index.php?route=activos"
         class="sb-link <?= ga_active($activosRoutes,$current) ?>">
        <span class="sb-link-ico"><i class="fas fa-laptop"></i></span>
        <span class="sb-link-txt">Activos</span>
        <span class="sb-badge b-inv">INV</span>
      </a>
    </div>
    <div class="sb-item">
      <a href="<?= e(base_url()) ?>/index.php?route=activos_eliminados"
         class="sb-link <?= ga_active($papeleraRoutes,$current) ?>">
        <span class="sb-link-ico"><i class="fas fa-trash-restore"></i></span>
        <span class="sb-link-txt">Papelera</span>
      </a>
    </div>
    <?php endif; ?>

    <!-- OPERACIÓN -->
    <span class="sb-section">Operación</span>

    <?php if(can_perm('mantenimientos.view')): ?>
    <div class="sb-item">
      <a href="<?= e(base_url()) ?>/index.php?route=mantenimientos"
         class="sb-link <?= ga_active($mantsRoutes,$current) ?>">
        <span class="sb-link-ico"><i class="fas fa-tools"></i></span>
        <span class="sb-link-txt">Mantenimientos</span>
        <span class="sb-badge b-pro">PRO</span>
      </a>
    </div>
    <?php endif; ?>

    <?php if($showCalib): ?>
    <div class="sb-item">
      <a href="<?= e(base_url()) ?>/index.php?route=calibraciones"
         class="sb-link <?= ga_active($calibRoutes,$current) ?>">
        <span class="sb-link-ico"><i class="fas fa-ruler-combined"></i></span>
        <span class="sb-link-txt">Calibraciones</span>
        <span class="sb-badge b-bio">BIO</span>
      </a>
    </div>
    <?php endif; ?>

    <?php if($showPatrones): ?>
    <div class="sb-item">
      <a href="<?= e(base_url()) ?>/index.php?route=patrones"
         class="sb-link <?= ga_active($patronesRoutes,$current) ?>">
        <span class="sb-link-ico"><i class="fas fa-balance-scale"></i></span>
        <span class="sb-link-txt">Patrones</span>
        <span class="sb-badge b-lab">LAB</span>
      </a>
    </div>
    <?php endif; ?>

    <?php if($showAudit): ?>
    <div class="sb-item">
      <a href="<?= e(base_url()) ?>/index.php?route=audit_log"
         class="sb-link <?= ga_active($auditoriaRoutes,$current) ?>">
        <span class="sb-link-ico"><i class="fas fa-clipboard-list"></i></span>
        <span class="sb-link-txt">Auditoría</span>
      </a>
    </div>
    <?php endif; ?>

    <!-- CONFIGURACIÓN -->
    <?php if($showConfig): ?>
    <span class="sb-section">Configuración</span>

    <div class="sb-item">
      <button onclick="gaModal('modConfig')" class="sb-link" style="cursor:pointer;border:none;background:none;padding:10px 12px;">
        <span class="sb-link-ico"><i class="fas fa-sliders-h"></i></span>
        <span class="sb-link-txt">Parámetros técnicos</span>
        <i class="fas fa-arrow-right" style="margin-left:auto;opacity:.4;font-size:.65rem;"></i>
      </button>
    </div>
    <?php endif; ?>

    <!-- ADMINISTRACIÓN -->
    <?php if($showSec): ?>
    <span class="sb-section">Administración</span>

    <div class="sb-item">
      <button onclick="gaModal('modAdmin')" class="sb-link" style="cursor:pointer;border:none;background:none;padding:10px 12px;">
        <span class="sb-link-ico"><i class="fas fa-user-shield"></i></span>
        <span class="sb-link-txt">Seguridad</span>
        <i class="fas fa-arrow-right" style="margin-left:auto;opacity:.4;font-size:.65rem;"></i>
      </button>
    </div>
    <?php endif; ?>

    <!-- LOGOUT -->
    <div class="sb-sep"></div>
    <div class="sb-item sb-logout">
      <a href="<?= e(base_url()) ?>/index.php?route=logout" class="sb-link">
        <span class="sb-link-ico"><i class="fas fa-sign-out-alt"></i></span>
        <span class="sb-link-txt">Cerrar sesión</span>
      </a>
    </div>

    <!-- VERSION -->
    <div class="sb-footer">
      GeoActivos PRO · v1.0<br>
      GeSaProv Project Design
    </div>

  </div>
</aside>

<!-- ════════════════════════════════════════════
     CONFIGURATION MODAL
════════════════════════════════════════════ -->
<div class="ga-modal-bd" id="modConfig">
  <div class="ga-modal" style="max-width:540px;">
    <div class="ga-modal-bar"></div>
    <div class="ga-modal-hd">
      <div class="ga-modal-ttl"><i class="fas fa-sliders-h mr-2" style="color:#00e5ff;"></i>Parámetros Técnicos</div>
      <button class="ga-modal-x" onclick="gaModalClose('modConfig')"><i class="fas fa-times"></i></button>
    </div>
    <div class="ga-modal-bd-inner">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <!-- Categorías -->
        <a href="<?= e(base_url()) ?>/index.php?route=categorias" class="ga-quick-access">
          <div class="ga-qa-icon"><i class="fas fa-tag"></i></div>
          <div class="ga-qa-content">
            <div class="ga-qa-title">Categorías</div>
            <div class="ga-qa-desc">Clasificación de activos</div>
          </div>
        </a>
        <!-- Marcas -->
        <a href="<?= e(base_url()) ?>/index.php?route=marcas" class="ga-quick-access">
          <div class="ga-qa-icon"><i class="fas fa-hammer"></i></div>
          <div class="ga-qa-content">
            <div class="ga-qa-title">Marcas</div>
            <div class="ga-qa-desc">Fabricantes/Proveedores</div>
          </div>
        </a>
        <!-- Tipos de Activo -->
        <a href="<?= e(base_url()) ?>/index.php?route=tipos_activo" class="ga-quick-access">
          <div class="ga-qa-icon"><i class="fas fa-boxes"></i></div>
          <div class="ga-qa-content">
            <div class="ga-qa-title">Tipos Activo</div>
            <div class="ga-qa-desc">Definición de tipos</div>
          </div>
        </a>
        <!-- Sedes -->
        <a href="<?= e(base_url()) ?>/index.php?route=sedes" class="ga-quick-access">
          <div class="ga-qa-icon"><i class="fas fa-map-marker-alt"></i></div>
          <div class="ga-qa-content">
            <div class="ga-qa-title">Sedes</div>
            <div class="ga-qa-desc">Ubicaciones físicas</div>
          </div>
        </a>
        <!-- Áreas -->
        <a href="<?= e(base_url()) ?>/index.php?route=areas" class="ga-quick-access">
          <div class="ga-qa-icon"><i class="fas fa-th-large"></i></div>
          <div class="ga-qa-content">
            <div class="ga-qa-title">Áreas</div>
            <div class="ga-qa-desc">Departamentos/Zonas</div>
          </div>
        </a>
        <!-- Proveedores -->
        <a href="<?= e(base_url()) ?>/index.php?route=proveedores" class="ga-quick-access">
          <div class="ga-qa-icon"><i class="fas fa-truck"></i></div>
          <div class="ga-qa-content">
            <div class="ga-qa-title">Proveedores</div>
            <div class="ga-qa-desc">Servicios y productos</div>
          </div>
        </a>
      </div>
    </div>
    <div class="ga-modal-ft">
      <button onclick="gaModalClose('modConfig')" class="dash-btn dash-btn-ghost">Cerrar</button>
    </div>
  </div>
</div>

<!-- ════════════════════════════════════════════
     ADMINISTRATION MODAL
════════════════════════════════════════════ -->
<div class="ga-modal-bd" id="modAdmin">
  <div class="ga-modal" style="max-width:480px;">
    <div class="ga-modal-bar"></div>
    <div class="ga-modal-hd">
      <div class="ga-modal-ttl"><i class="fas fa-user-shield mr-2" style="color:#00e5ff;"></i>Seguridad</div>
      <button class="ga-modal-x" onclick="gaModalClose('modAdmin')"><i class="fas fa-times"></i></button>
    </div>
    <div class="ga-modal-bd-inner">
      <div style="display:grid;grid-template-columns:1fr;gap:12px;">
        <!-- Empresas -->
        <a href="<?= e(base_url()) ?>/index.php?route=empresas" class="ga-quick-access">
          <div class="ga-qa-icon"><i class="fas fa-building"></i></div>
          <div class="ga-qa-content">
            <div class="ga-qa-title">Empresas</div>
            <div class="ga-qa-desc">Gestión de tenants/empresas</div>
          </div>
        </a>
        <!-- Usuarios -->
        <a href="<?= e(base_url()) ?>/index.php?route=usuarios" class="ga-quick-access">
          <div class="ga-qa-icon"><i class="fas fa-users"></i></div>
          <div class="ga-qa-content">
            <div class="ga-qa-title">Usuarios</div>
            <div class="ga-qa-desc">Usuarios del sistema</div>
          </div>
        </a>
        <!-- Roles y Permisos -->
        <a href="<?= e(base_url()) ?>/index.php?route=roles" class="ga-quick-access">
          <div class="ga-qa-icon"><i class="fas fa-lock"></i></div>
          <div class="ga-qa-content">
            <div class="ga-qa-title">Roles y Permisos</div>
            <div class="ga-qa-desc">Control de acceso (RBAC)</div>
          </div>
        </a>
      </div>
    </div>
    <div class="ga-modal-ft">
      <button onclick="gaModalClose('modAdmin')" class="dash-btn dash-btn-ghost">Cerrar</button>
    </div>
  </div>
</div>

<script>
// Funciones globales para modales
function gaModal(id) {
  const m = document.getElementById(id);
  if (m) {
    m.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
}

function gaModalClose(id) {
  const m = document.getElementById(id);
  if (m) {
    m.classList.remove('open');
    document.body.style.overflow = '';
  }
}

// Cerrar modal al hacer click en el backdrop
document.querySelectorAll('.ga-modal-bd').forEach(bd => {
  bd.addEventListener('click', e => {
    if (e.target === bd) gaModalClose(bd.id);
  });
});

// Cerrar modales con ESC
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.ga-modal-bd.open').forEach(m => gaModalClose(m.id));
  }
});

// Toggle sidebar dropdown
function sbToggle(id) {
  const el = document.getElementById(id);
  if (!el) return;

  // Close all other dropdowns
  document.querySelectorAll('.sb-dropdown.open').forEach(d => {
    if (d.id !== id) d.classList.remove('open');
  });

  el.classList.toggle('open');
}

// Auto-open active dropdown on page load
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.sb-dropdown').forEach(dd => {
    if (dd.querySelector('.sb-sublink.active')) {
      dd.classList.add('open');
    }
  });
});
</script>
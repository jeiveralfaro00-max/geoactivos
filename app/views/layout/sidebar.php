<?php
// app/views/layout/sidebar.php
require_once __DIR__ . '/../../core/Helpers.php';
require_once __DIR__ . '/../../core/Auth.php';

$current = $_GET['route'] ?? 'dashboard';

function ga_active($routes, $cur){ return in_array($cur,(array)$routes,true) ? 'active' : ''; }
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
.main-sidebar,.main-sidebar .sidebar{width:260px !important;overflow:visible !important;transition:transform 0.3s cubic-bezier(0.4,0,0.2,1),box-shadow 0.3s ease;}
.main-sidebar{background:var(--white) !important;border-right:1px solid var(--slate-100) !important;box-shadow:4px 0 20px rgba(15,29,46,0.06) !important;z-index:1051 !important;overflow-y:auto !important;overflow-x:hidden !important;position:fixed;top:64px !important;left:0;width:260px;height:calc(100vh - 64px) !important;transform:translateX(-260px);}
.main-sidebar::-webkit-scrollbar{width:5px;}
.main-sidebar::-webkit-scrollbar-track{background:transparent;}
.main-sidebar::-webkit-scrollbar-thumb{background:rgba(11,168,150,0.2);border-radius:10px;}
.main-sidebar::-webkit-scrollbar-thumb:hover{background:rgba(11,168,150,0.4);}
body.sidebar-collapse .main-sidebar,.main-sidebar.show{transform:translateX(0);box-shadow:8px 0 30px rgba(15,29,46,0.15);}
.sidebar-backdrop{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,29,46,0.3);backdrop-filter:blur(4px);z-index:1050;opacity:0;visibility:hidden;transition:opacity 0.3s ease,visibility 0.3s ease;pointer-events:none;}
body.sidebar-collapse .sidebar-backdrop,.sidebar-backdrop.show{opacity:1;visibility:visible;pointer-events:all;}
.content-wrapper,.main-footer{margin-left:0 !important;transition:none !important;}

.sb-nav{padding:16px 0 20px;overflow-y:auto;overflow-x:visible;height:100%;}
.sb-nav::-webkit-scrollbar{width:3px;}
.sb-nav::-webkit-scrollbar-thumb{background:rgba(11,168,150,0.15);border-radius:10px;}

.sb-section{font-size:.55rem;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;color:var(--slate-300);padding:16px 16px 6px;display:block;}
.sb-item{margin:2px 8px;position:relative;transition:margin 0.3s ease;}
.sb-link{display:flex;align-items:center;gap:0;padding:10px 12px;border-radius:10px;color:var(--slate-600) !important;font-size:.83rem;font-weight:500;text-decoration:none !important;transition:all .22s ease;position:relative;cursor:pointer;border:1px solid transparent;user-select:none;}
.sb-link:hover{background:var(--teal-light) !important;color:var(--teal-dark) !important;border-color:var(--teal-mid) !important;}
.sb-link.active{background:linear-gradient(135deg,var(--teal-light),rgba(11,168,150,0.08)) !important;color:var(--teal-dark) !important;border-color:var(--teal-mid) !important;box-shadow:0 2px 10px rgba(11,168,150,0.12) !important;}
.sb-link.active::before{content:'';position:absolute;left:0;top:22%;bottom:22%;width:3px;border-radius:0 3px 3px 0;background:linear-gradient(to bottom,var(--teal),var(--teal-dark));}
.sb-link-ico{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.82rem;flex-shrink:0;margin-right:10px;transition:all .22s;background:var(--slate-50);color:var(--slate-400);}
.sb-link:hover .sb-link-ico{background:var(--teal-light);color:var(--teal);}
.sb-link.active .sb-link-ico{background:var(--teal);color:#fff;box-shadow:0 2px 8px rgba(11,168,150,0.25);}
.sb-link-txt{flex:1;line-height:1;}
.sb-badge{font-size:.5rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:2px 6px;border-radius:4px;margin-left:6px;flex-shrink:0;}
.sb-badge.b-pro{background:#FFFBEB;color:#C07A00;border:1px solid #FDE68A;}
.sb-badge.b-bio{background:var(--teal-light);color:var(--teal);border:1px solid var(--teal-mid);}
.sb-badge.b-lab{background:var(--slate-50);color:var(--slate-500);border:1px solid var(--slate-100);}
.sb-badge.b-inv{background:#ECFDF5;color:#059669;border:1px solid #A7F3D0;}

.sb-dropdown{position:relative;}
.sb-dropdown-arrow{margin-left:auto;font-size:.65rem;color:var(--slate-300);transition:transform .3s ease,color .22s;flex-shrink:0;}
.sb-dropdown.open>.sb-link .sb-dropdown-arrow{transform:rotate(90deg);color:var(--teal);}
.sb-dropdown.open>.sb-link{background:var(--teal-light) !important;color:var(--teal-dark) !important;border-color:var(--teal-mid) !important;}

.sb-submenu{overflow:hidden;max-height:0;transition:max-height .35s cubic-bezier(.4,0,.2,1),opacity .25s ease;opacity:0;margin:2px 0 2px 8px;}
.sb-dropdown.open .sb-submenu{max-height:400px;opacity:1;display:block;}
.sb-subitem{margin:1px 0;}
.sb-sublink{display:flex;align-items:center;gap:8px;padding:8px 12px 8px 14px;border-radius:8px;color:var(--slate-500) !important;font-size:.78rem;font-weight:500;text-decoration:none !important;transition:all .2s;border:1px solid transparent;position:relative;}
.sb-sublink::before{content:'';width:4px;height:4px;border-radius:50%;background:var(--slate-300);flex-shrink:0;transition:background .2s,box-shadow .2s;}
.sb-sublink:hover{background:var(--teal-light) !important;color:var(--teal-dark) !important;border-color:var(--teal-mid) !important;}
.sb-sublink:hover::before{background:var(--teal);box-shadow:0 0 6px rgba(11,168,150,0.5);}
.sb-sublink.active{background:var(--teal-light) !important;color:var(--teal-dark) !important;border-color:var(--teal-mid) !important;}
.sb-sublink.active::before{background:var(--teal);box-shadow:0 0 8px rgba(11,168,150,0.6);}

.sb-sep{height:1px;background:var(--slate-100);margin:8px 16px;}

.sb-logout .sb-link{color:var(--rose) !important;}
.sb-logout .sb-link:hover{background:#FFF0F2 !important;color:#C81E3A !important;border-color:#FECDD3 !important;}
.sb-logout .sb-link-ico{color:var(--slate-400) !important;}
.sb-logout .sb-link:hover .sb-link-ico{background:#FFF0F2 !important;color:#C81E3A !important;}

.sb-footer{padding:12px 16px;font-size:.55rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--slate-300);border-top:1px solid var(--slate-100);margin-top:8px;}
</style>

<aside class="main-sidebar">

<div class="sb-nav" style="padding-top:12px;">
  <span class="sb-section">Principal</span>
  <?php if(can_perm('dashboard.view')): ?>
  <div class="sb-item">
    <a href="<?= e(base_url()) ?>/index.php?route=inicio" class="sb-link <?= ga_active(['inicio','dashboard'],$current) ?>">
      <span class="sb-link-ico"><i class="fas fa-home"></i></span>
      <span class="sb-link-txt">Inicio</span>
    </a>
  </div>
  <?php endif; ?>
  
  <?php if(Auth::isSuperadmin()): ?>
  <div class="sb-item">
    <a href="<?= e(base_url()) ?>/index.php?route=empresas" class="sb-link <?= ga_active(['empresas','empresa_form','empresa_ver'],$current) ?>">
      <span class="sb-link-ico"><i class="fas fa-building"></i></span>
      <span class="sb-link-txt">Empresas</span>
    </a>
  </div>
  <?php endif; ?>
  
  <?php if(can_perm('activos.view')): ?>
  <div class="sb-item">
    <a href="<?= e(base_url()) ?>/index.php?route=activos" class="sb-link <?= ga_active($activosRoutes,$current) ?>">
      <span class="sb-link-ico"><i class="fas fa-box"></i></span>
      <span class="sb-link-txt">Activos</span>
      <span class="sb-badge b-inv">INV</span>
    </a>
  </div>
  <div class="sb-item">
    <a href="<?= e(base_url()) ?>/index.php?route=activos_eliminados" class="sb-link <?= ga_active($papeleraRoutes,$current) ?>">
      <span class="sb-link-ico"><i class="fas fa-trash-restore"></i></span>
      <span class="sb-link-txt">Papelera</span>
    </a>
  </div>
  <?php endif; ?>

  <span class="sb-section">Operación</span>
  <?php if(can_perm('mantenimientos.view')): ?>
  <div class="sb-item">
    <a href="<?= e(base_url()) ?>/index.php?route=mantenimientos" class="sb-link <?= ga_active($mantsRoutes,$current) ?>">
      <span class="sb-link-ico"><i class="fas fa-tools"></i></span>
      <span class="sb-link-txt">Mantenimientos</span>
      <span class="sb-badge b-pro">PRO</span>
    </a>
  </div>
  <div class="sb-item">
    <a href="<?= e(base_url()) ?>/index.php?route=calendario" class="sb-link <?= ga_active('calendario',$current) ?>">
      <span class="sb-link-ico"><i class="fas fa-calendar-alt"></i></span>
      <span class="sb-link-txt">Calendario</span>
    </a>
  </div>
  <?php endif; ?>
  <?php if($showCalib): ?>
  <div class="sb-item">
    <a href="<?= e(base_url()) ?>/index.php?route=calibraciones" class="sb-link <?= ga_active($calibRoutes,$current) ?>">
      <span class="sb-link-ico"><i class="fas fa-ruler-combined"></i></span>
      <span class="sb-link-txt">Calibraciones</span>
      <span class="sb-badge b-bio">ISO</span>
    </a>
  </div>
  <?php endif; ?>
  <?php if($showPatrones): ?>
  <div class="sb-item">
    <a href="<?= e(base_url()) ?>/index.php?route=patrones" class="sb-link <?= ga_active($patronesRoutes,$current) ?>">
      <span class="sb-link-ico"><i class="fas fa-balance-scale"></i></span>
      <span class="sb-link-txt">Patrones</span>
    </a>
  </div>
  <?php endif; ?>
  <?php if($showAudit): ?>
  <div class="sb-item">
    <a href="<?= e(base_url()) ?>/index.php?route=audit_log" class="sb-link <?= ga_active($auditoriaRoutes,$current) ?>">
      <span class="sb-link-ico"><i class="fas fa-clipboard-list"></i></span>
      <span class="sb-link-txt">Auditoría</span>
    </a>
  </div>
  <?php endif; ?>

  <?php if($showConfig): ?>
  <span class="sb-section">Configuración</span>
  <div class="sb-item sb-dropdown <?= $confInConf ? 'open' : '' ?>" id="ddConf">
    <div class="sb-link <?= $confInConf ? 'active' : '' ?>" onclick="sbToggle('ddConf', event)" style="cursor:pointer;">
      <span class="sb-link-ico"><i class="fas fa-sliders-h"></i></span>
      <span class="sb-link-txt">Parámetros</span>
      <i class="fas fa-chevron-right sb-dropdown-arrow"></i>
    </div>
    <div class="sb-submenu">
      <?php $confItems=[['categorias',['categorias','categoria_form'],'Categorías'],['marcas',['marcas','marca_form'],'Marcas'],['tipos_activo',['tipos_activo','tipo_activo_form'],'Tipos'],['sedes',['sedes','sede_form'],'Sedes'],['areas',['areas','area_form'],'Áreas'],['proveedores',['proveedores','proveedor_form'],'Proveedores']]; foreach($confItems as [$r2,$ar,$lb]): ?>
      <div class="sb-subitem">
        <a href="<?= e(base_url()) ?>/index.php?route=<?= $r2 ?>" class="sb-sublink <?= in_array($current,$ar,true)?'active':'' ?>"><?= e($lb) ?></a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if($showSec): ?>
  <span class="sb-section">Administración</span>
  <div class="sb-item sb-dropdown <?= $secInSec ? 'open' : '' ?>" id="ddSec">
    <div class="sb-link <?= $secInSec ? 'active' : '' ?>" onclick="sbToggle('ddSec', event)" style="cursor:pointer;">
      <span class="sb-link-ico"><i class="fas fa-user-shield"></i></span>
      <span class="sb-link-txt">Seguridad</span>
      <i class="fas fa-chevron-right sb-dropdown-arrow"></i>
    </div>
    <div class="sb-submenu">
      <?php if(can_perm('empresas.view')||can_perm('empresas.edit')): ?>
      <div class="sb-subitem"><a href="<?= e(base_url()) ?>/index.php?route=empresas" class="sb-sublink <?= in_array($current,['empresas','empresa_form'],true)?'active':'' ?>">Empresas</a></div>
      <?php endif; ?>
      <?php if(can_perm('usuarios.view')||can_perm('usuarios.edit')): ?>
      <div class="sb-subitem"><a href="<?= e(base_url()) ?>/index.php?route=usuarios" class="sb-sublink <?= in_array($current,['usuarios','usuario_form'],true)?'active':'' ?>">Usuarios</a></div>
      <?php endif; ?>
      <?php if(can_perm('roles.view')||can_perm('roles.edit')): ?>
      <div class="sb-subitem"><a href="<?= e(base_url()) ?>/index.php?route=roles" class="sb-sublink <?= in_array($current,['roles','rol_form','rol_permisos'],true)?'active':'' ?>">Roles y permisos</a></div>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="sb-sep"></div>
  <div class="sb-item sb-logout">
    <a href="<?= e(base_url()) ?>/index.php?route=logout" class="sb-link">
      <span class="sb-link-ico"><i class="fas fa-sign-out-alt"></i></span>
      <span class="sb-link-txt">Cerrar sesión</span>
    </a>
  </div>

  <div class="sb-footer">GeoActivos v1.0 · GeSaProv</div>
</div>
</aside>

<script>
function sbToggle(id, event){
  if(event) event.preventDefault();
  event.stopPropagation();
  const el = document.getElementById(id);
  if(!el) return;
  document.querySelectorAll('.sb-dropdown.open').forEach(d => {
    if(d.id !== id) d.classList.remove('open');
  });
  el.classList.toggle('open');
}

document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('.sb-dropdown').forEach(dd => {
    if(dd.querySelector('.sb-sublink.active')) dd.classList.add('open');
  });

  document.addEventListener('click', function(e) {
    if(!e.target.closest('.sb-dropdown')) {
      document.querySelectorAll('.sb-dropdown.open').forEach(dd => {
        if(!dd.querySelector('.sb-sublink.active')) dd.classList.remove('open');
      });
    }
  });
});
</script>

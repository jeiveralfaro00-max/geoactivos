<?php
require_once __DIR__ . '/../../core/Helpers.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../config/db.php';

$tenantId = Auth::tenantId();

$st = db()->prepare("SELECT COUNT(*) c FROM activos WHERE tenant_id=:t");
$st->execute([':t'=>$tenantId]);
$totalActivos = (int)($st->fetch()['c'] ?? 0);

$st = db()->prepare("SELECT COUNT(*) c FROM mantenimientos WHERE tenant_id=:t AND estado IN ('PROGRAMADO','EN_PROCESO')");
$st->execute([':t'=>$tenantId]);
$totalMantPend = (int)($st->fetch()['c'] ?? 0);

$st = db()->prepare("SELECT COUNT(*) c FROM activos WHERE tenant_id=:t AND estado='EN_MANTENIMIENTO'");
$st->execute([':t'=>$tenantId]);
$totalActEnMant = (int)($st->fetch()['c'] ?? 0);

$st = db()->prepare("SELECT COUNT(*) c FROM activos WHERE tenant_id=:t AND estado='BAJA'");
$st->execute([':t'=>$tenantId]);
$totalActBaja = (int)($st->fetch()['c'] ?? 0);

$ultActivos = [];
try {
  $q = db()->prepare("SELECT id, codigo_interno, nombre, estado FROM activos WHERE tenant_id=:t ORDER BY id DESC LIMIT 5");
  $q->execute([':t'=>$tenantId]);
  $ultActivos = $q->fetchAll();
} catch(Exception $e){ $ultActivos = []; }

$ultMants = [];
try {
  $q2 = db()->prepare("
    SELECT m.id, m.tipo, m.estado, m.fecha_programada, m.fecha_inicio,
           a.codigo_interno, a.nombre AS activo_nombre
    FROM mantenimientos m
    INNER JOIN activos a ON a.id=m.activo_id AND a.tenant_id=m.tenant_id
    WHERE m.tenant_id=:t ORDER BY m.id DESC LIMIT 5
  ");
  $q2->execute([':t'=>$tenantId]);
  $ultMants = $q2->fetchAll();
} catch(Exception $e){ $ultMants = []; }

function bActivo($estado){
  $e=(string)$estado;
  if($e==='ACTIVO')           return ['c'=>'#00e676','b'=>'rgba(0,230,118,.15)','l'=>'ACTIVO'];
  if($e==='EN_MANTENIMIENTO') return ['c'=>'#ffb300','b'=>'rgba(255,179,0,.15)','l'=>'EN MANT.'];
  if($e==='BAJA')             return ['c'=>'#f87171','b'=>'rgba(239,68,68,.15)','l'=>'BAJA'];
  return ['c'=>'#94a3b8','b'=>'rgba(100,130,160,.12)','l'=>$e?:'-'];
}
function bMant($estado){
  $e=(string)$estado;
  if($e==='PROGRAMADO') return ['c'=>'#00e5ff','b'=>'rgba(0,229,255,.12)','l'=>'PROGRAMADO'];
  if($e==='EN_PROCESO') return ['c'=>'#ffb300','b'=>'rgba(255,179,0,.15)','l'=>'EN PROCESO'];
  if($e==='CERRADO')    return ['c'=>'#00e676','b'=>'rgba(0,230,118,.12)','l'=>'CERRADO'];
  if($e==='ANULADO')    return ['c'=>'#f87171','b'=>'rgba(239,68,68,.12)','l'=>'ANULADO'];
  return ['c'=>'#94a3b8','b'=>'rgba(100,130,160,.1)','l'=>$e?:'-'];
}
function fFecha($v){
  if(!$v) return '—';
  return substr((string)$v,0,10);
}

$tenantNombre  = $_SESSION['tenant']['nombre'] ?? ($_SESSION['user']['tenant_nombre'] ?? 'Cliente');
$usuarioNombre = $_SESSION['user']['nombre'] ?? 'Usuario';

$p1 = $totalActivos>0 ? 100 : 8;
$p2 = $totalActivos>0 ? min(100,(int)round(($totalMantPend/max(1,$totalActivos))*140)) : 10;
$p3 = $totalActivos>0 ? min(100,(int)round(($totalActEnMant/max(1,$totalActivos))*100)) : 6;
$p4 = $totalActivos>0 ? min(100,(int)round(($totalActBaja/max(1,$totalActivos))*100)) : 4;

require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/sidebar.php';
?>

<style>
/* ══════════════════════════════════
   DASHBOARD STYLES
══════════════════════════════════ */

/* Ticker */
.dash-ticker{
  display:flex;align-items:center;overflow:hidden;
  background:rgba(0,229,255,.04);
  border:1px solid rgba(0,229,255,.1);
  border-radius:10px;margin-bottom:20px;
}
.dash-ticker-lbl{
  flex-shrink:0;padding:9px 14px;
  background:rgba(26,111,255,.1);
  border-right:1px solid rgba(0,229,255,.1);
  font-size:.6rem;font-weight:800;
  letter-spacing:2px;text-transform:uppercase;color:#00e5ff;
  white-space:nowrap;
}
.dash-ticker-body{flex:1;overflow:hidden;padding:9px 14px;}
.dash-ticker-inner{
  display:inline-flex;gap:36px;
  animation:tickerMove 24s linear infinite;white-space:nowrap;
}
.dash-ticker-inner span{
  font-size:.72rem;color:rgba(140,180,220,.55);
  display:inline-flex;align-items:center;gap:5px;flex-shrink:0;
}
.dash-ticker-inner span i{color:#00e5ff;opacity:.6;}
@keyframes tickerMove{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}

/* Hero */
.dash-hero{
  position:relative;overflow:hidden;
  background:linear-gradient(110deg,rgba(3,6,15,.98) 0%,rgba(6,12,26,.94) 55%,rgba(0,40,100,.15) 100%);
  border:1px solid rgba(26,111,255,.2);
  border-radius:14px;padding:26px 28px;margin-bottom:20px;
}
.dash-hero::before{
  content:'';position:absolute;right:-40px;top:-40px;
  width:250px;height:250px;
  background:radial-gradient(circle,rgba(26,111,255,.14),transparent 70%);
  border-radius:50%;pointer-events:none;
}
.dash-hero-line{
  position:absolute;top:0;left:8%;right:8%;height:1px;
  background:linear-gradient(90deg,transparent,rgba(26,111,255,.5),rgba(0,229,255,.7),transparent);
  opacity:.7;
}
.dash-hero-title{
  font-family:'Bebas Neue',cursive;
  font-size:2rem;letter-spacing:2px;line-height:1;
  background:linear-gradient(90deg,#fff,#00e5ff);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
  margin-bottom:3px;
}
.dash-hero-sub{font-size:.78rem;color:rgba(140,180,220,.55);letter-spacing:.3px;}
.dash-pill{
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(0,229,255,.06);border:1px solid rgba(0,229,255,.15);
  border-radius:100px;padding:3px 11px;
  font-size:.62rem;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;
  color:#00e5ff;margin-bottom:8px;
}
.dash-pill .dp{width:5px;height:5px;border-radius:50%;background:#00e676;box-shadow:0 0 5px #00e676;animation:gaPulse 2s infinite;}

/* Hero buttons */
.dash-btn{
  display:inline-flex;align-items:center;gap:7px;
  border-radius:8px;padding:8px 16px;
  font-size:.75rem;font-weight:700;
  letter-spacing:.5px;text-transform:uppercase;
  transition:all .22s;text-decoration:none;border:none;cursor:pointer;
}
.dash-btn-primary{
  background:linear-gradient(135deg,#1a6fff,#0050cc);color:#fff;
  box-shadow:0 4px 16px rgba(26,111,255,.3);
}
.dash-btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 22px rgba(26,111,255,.45);color:#fff;text-decoration:none;}
.dash-btn-ghost{
  background:transparent;color:rgba(140,180,220,.7);
  border:1px solid rgba(26,111,255,.25) !important;
}
.dash-btn-ghost:hover{background:rgba(26,111,255,.1);border-color:rgba(26,111,255,.45) !important;color:#fff;text-decoration:none;transform:translateY(-1px);}
.dash-btn-warn{
  background:transparent;color:rgba(255,190,60,.8);
  border:1px solid rgba(255,179,0,.25) !important;
}
.dash-btn-warn:hover{background:rgba(255,179,0,.1);border-color:#ffb300 !important;color:#ffb300;text-decoration:none;transform:translateY(-1px);}

/* KPI Cards */
.dash-kpi{
  position:relative;overflow:hidden;
  background:rgba(4,8,22,.82);
  border:1px solid rgba(26,111,255,.15);
  border-radius:14px;padding:0;
  transition:all .28s;
  animation:dashUp .6s ease both;
}
.dash-kpi:hover{
  border-color:rgba(26,111,255,.4);
  transform:translateY(-4px);
  box-shadow:0 16px 48px rgba(26,111,255,.14);
}
.dash-kpi-top{height:2px;}
.dash-kpi.k1 .dash-kpi-top{background:linear-gradient(90deg,#1a6fff,#00e5ff);}
.dash-kpi.k2 .dash-kpi-top{background:linear-gradient(90deg,#f59e0b,#fbbf24);}
.dash-kpi.k3 .dash-kpi-top{background:linear-gradient(90deg,#00bfa5,#00e5ff);}
.dash-kpi.k4 .dash-kpi-top{background:linear-gradient(90deg,#ef4444,#f87171);}
.dash-kpi-inner{padding:20px 20px 16px;}
.dash-kpi-bg-ico{
  position:absolute;right:14px;top:18px;
  font-size:2.8rem;opacity:.07;
}
.dash-kpi-lbl{
  font-size:.65rem;font-weight:700;
  letter-spacing:2px;text-transform:uppercase;
  color:rgba(78,109,140,.8);margin-bottom:6px;
}
.dash-kpi-val{
  font-family:'Bebas Neue',cursive;
  font-size:3.4rem;line-height:1;letter-spacing:1px;color:#fff;
  margin-bottom:4px;
}
.dash-kpi.k1 .dash-kpi-val{color:#00e5ff;}
.dash-kpi.k2 .dash-kpi-val{color:#fbbf24;}
.dash-kpi.k3 .dash-kpi-val{color:#00bfa5;}
.dash-kpi.k4 .dash-kpi-val{color:#f87171;}
.dash-kpi-sub{font-size:.7rem;color:rgba(100,140,180,.5);margin-bottom:14px;}
.dash-kpi-bar{height:2px;background:rgba(255,255,255,.05);border-radius:10px;overflow:hidden;margin-bottom:12px;}
.dash-kpi-fill{height:100%;border-radius:10px;transition:width 1.2s cubic-bezier(.22,1,.36,1);}
.dash-kpi.k1 .dash-kpi-fill{background:linear-gradient(90deg,#1a6fff,#00e5ff);}
.dash-kpi.k2 .dash-kpi-fill{background:linear-gradient(90deg,#f59e0b,#fbbf24);}
.dash-kpi.k3 .dash-kpi-fill{background:linear-gradient(90deg,#00bfa5,#00e5ff);}
.dash-kpi.k4 .dash-kpi-fill{background:linear-gradient(90deg,#ef4444,#f87171);}
.dash-kpi-link{
  font-size:.65rem;font-weight:700;letter-spacing:1.5px;
  text-transform:uppercase;color:rgba(78,109,140,.7);
  text-decoration:none;transition:color .2s;
}
.dash-kpi-link:hover{color:#00e5ff;text-decoration:none;}

/* Section cards */
.dash-card{
  background:rgba(4,8,22,.78);
  border:1px solid rgba(26,111,255,.14);
  border-radius:14px;overflow:hidden;
  animation:dashUp .6s ease both;
}
.dash-card-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:14px 18px 12px;
  border-bottom:1px solid rgba(26,111,255,.09);
}
.dash-card-title{
  font-family:'Bebas Neue',cursive;
  font-size:1.05rem;letter-spacing:1.5px;
  color:#fff;display:flex;align-items:center;gap:7px;
}
.dash-card-title i{color:#00e5ff;font-size:.9rem;}
.dash-card-action{
  font-size:.62rem;font-weight:700;letter-spacing:1.5px;
  text-transform:uppercase;color:rgba(78,109,140,.6);
  text-decoration:none;transition:color .2s;
}
.dash-card-action:hover{color:#00e5ff;text-decoration:none;}

/* Quick action buttons */
.dash-qa{
  display:flex;align-items:center;gap:10px;
  padding:10px 14px;margin-bottom:7px;
  background:rgba(26,111,255,.04);
  border:1px solid rgba(26,111,255,.12);
  border-radius:9px;font-size:.78rem;font-weight:600;
  color:rgba(160,200,240,.7);text-decoration:none;
  transition:all .22s;
}
.dash-qa:hover{background:rgba(26,111,255,.1);border-color:rgba(26,111,255,.35);color:#fff;transform:translateX(3px);text-decoration:none;box-shadow:0 3px 12px rgba(26,111,255,.1);}
.dash-qa .qi{
  width:26px;height:26px;border-radius:7px;
  display:inline-flex;align-items:center;justify-content:center;
  font-size:.75rem;flex-shrink:0;
}
.dash-qa.qa-b .qi{background:rgba(26,111,255,.18);color:#00e5ff;}
.dash-qa.qa-g .qi{background:rgba(78,109,140,.18);color:#94a3b8;}
.dash-qa.qa-w .qi{background:rgba(255,179,0,.15);color:#ffb300;}
.dash-qa.qa-t .qi{background:rgba(0,191,165,.15);color:#00bfa5;}
.dash-qa .qa-arr{margin-left:auto;opacity:.3;font-size:.65rem;}

/* Mini stats row */
.dash-mini-stat{
  flex:1;border-radius:9px;padding:10px 10px;text-align:center;
}
.dash-mini-stat-val{font-family:'Bebas Neue',cursive;font-size:1.5rem;line-height:1;}
.dash-mini-stat-lbl{font-size:.55rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(78,109,140,.7);margin-top:2px;}

/* Pro box */
.dash-pro-box{
  margin-top:12px;padding:11px 13px;
  background:rgba(0,229,255,.03);
  border:1px solid rgba(0,229,255,.1);border-radius:9px;
}
.dash-pro-box-ttl{font-size:.62rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#00e5ff;margin-bottom:3px;}
.dash-pro-box-sub{font-size:.7rem;color:rgba(100,140,180,.45);}

/* List rows */
.dash-row{
  display:flex;align-items:center;justify-content:space-between;
  padding:11px 18px;
  border-bottom:1px solid rgba(26,111,255,.06);
  transition:background .18s;
}
.dash-row:last-child{border-bottom:none;}
.dash-row:hover{background:rgba(26,111,255,.04);}
.dash-code{font-family:'Bebas Neue',cursive;font-size:.95rem;letter-spacing:1px;color:#fff;line-height:1;}
.dash-name{font-size:.72rem;color:rgba(120,160,200,.55);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:185px;margin-top:2px;}
.dash-lnk{font-size:.62rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:rgba(78,109,140,.6);text-decoration:none;transition:color .2s;display:block;text-align:right;margin-top:3px;}
.dash-lnk:hover{color:#00e5ff;text-decoration:none;}
.dash-empty{padding:28px 20px;text-align:center;font-size:.78rem;color:rgba(78,109,140,.45);}

/* Inline badge */
.d-badge{
  display:inline-block;padding:2px 7px;border-radius:5px;
  font-size:.58rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;
  white-space:nowrap;
}

@keyframes dashUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
.dash-kpi:nth-child(1){animation-delay:.04s}
.dash-kpi:nth-child(2){animation-delay:.08s}
.dash-kpi:nth-child(3){animation-delay:.12s}
.dash-kpi:nth-child(4){animation-delay:.16s}

/* ── MODAL PRO ── */
.ga-modal-bd{
  position:fixed;inset:0;z-index:1060;
  background:rgba(3,6,15,.82);backdrop-filter:blur(6px);
  display:none;align-items:center;justify-content:center;padding:20px;
}
.ga-modal-bd.open{display:flex;animation:gaFdIn .2s ease;}
@keyframes gaFdIn{from{opacity:0}to{opacity:1}}
.ga-modal{
  background:rgba(4,8,22,.98);
  border:1px solid rgba(26,111,255,.22);
  border-radius:14px;width:100%;max-width:460px;
  overflow:hidden;
  animation:gaModIn .28s cubic-bezier(.22,1,.36,1);
  position:relative;
}
@keyframes gaModIn{from{opacity:0;transform:scale(.95) translateY(14px)}to{opacity:1;transform:scale(1) translateY(0)}}
.ga-modal-bar{height:2px;background:linear-gradient(90deg,#1a6fff,#00e5ff);}
.ga-modal-hd{
  display:flex;align-items:center;justify-content:space-between;
  padding:16px 20px 12px;
  border-bottom:1px solid rgba(26,111,255,.1);
}
.ga-modal-ttl{font-family:'Bebas Neue',cursive;font-size:1.2rem;letter-spacing:1.5px;color:#fff;}
.ga-modal-x{
  background:rgba(255,255,255,.05);border:none;
  width:26px;height:26px;border-radius:7px;
  color:rgba(140,170,210,.55);cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  transition:all .2s;font-size:.8rem;
}
.ga-modal-x:hover{background:rgba(239,68,68,.12);color:#f87171;}
.ga-modal-bd-inner{padding:18px 20px;}
.ga-modal-ft{padding:12px 20px 16px;display:flex;gap:8px;justify-content:flex-end;border-top:1px solid rgba(26,111,255,.08);}
</style>

<!-- ══════ TICKER ══════ -->
<div class="dash-ticker">
  <div class="dash-ticker-lbl"><i class="fas fa-satellite-dish mr-1"></i> Live</div>
  <div class="dash-ticker-body">
    <div class="dash-ticker-inner">
      <span><i class="fas fa-check-circle"></i> Sistema operativo · Todos los servicios activos</span>
      <span><i class="fas fa-laptop"></i> <?= (int)$totalActivos ?> activos en inventario</span>
      <span><i class="fas fa-tools"></i> <?= (int)$totalMantPend ?> mantenimientos pendientes</span>
      <span><i class="fas fa-building"></i> Tenant: <?= e($tenantNombre) ?> · Multi-Tenant</span>
      <span><i class="fas fa-shield-alt"></i> Auditoría activa · RBAC habilitado</span>
      <span><i class="fas fa-ruler-combined"></i> Calibraciones ISO disponible</span>
      <!-- duplicate for loop -->
      <span><i class="fas fa-check-circle"></i> Sistema operativo · Todos los servicios activos</span>
      <span><i class="fas fa-laptop"></i> <?= (int)$totalActivos ?> activos en inventario</span>
      <span><i class="fas fa-tools"></i> <?= (int)$totalMantPend ?> mantenimientos pendientes</span>
      <span><i class="fas fa-building"></i> Tenant: <?= e($tenantNombre) ?> · Multi-Tenant</span>
      <span><i class="fas fa-shield-alt"></i> Auditoría activa · RBAC habilitado</span>
      <span><i class="fas fa-ruler-combined"></i> Calibraciones ISO disponible</span>
    </div>
  </div>
</div>

<!-- ══════ HERO ══════ -->
<div class="dash-hero">
  <div class="dash-hero-line"></div>
  <div class="d-flex align-items-start align-items-md-center justify-content-between flex-wrap" style="gap:14px;">
    <div>
      <div class="dash-pill"><span class="dp"></span><i class="fas fa-building" style="font-size:.6rem;"></i> <?= e($tenantNombre) ?> · PRO</div>
      <div class="dash-hero-title">Dashboard</div>
      <div class="dash-hero-sub">Bienvenido, <strong style="color:rgba(200,230,255,.75);"><?= e($usuarioNombre) ?></strong> &nbsp;·&nbsp; Resumen operativo</div>
    </div>
    <div class="d-flex flex-wrap" style="gap:7px;">
      <a href="<?= e(base_url()) ?>/index.php?route=activos_form" class="dash-btn dash-btn-primary">
        <i class="fas fa-plus"></i> Nuevo activo
      </a>
      <a href="<?= e(base_url()) ?>/index.php?route=activos" class="dash-btn dash-btn-ghost">
        <i class="fas fa-th-list"></i> Activos
      </a>
      <a href="<?= e(base_url()) ?>/index.php?route=mantenimientos" class="dash-btn dash-btn-warn">
        <i class="fas fa-tools"></i> Mantenimientos
      </a>
      <button onclick="gaModal('modKpi')" class="dash-btn dash-btn-ghost" style="border-color:rgba(0,229,255,.2) !important;color:rgba(0,229,255,.6);">
        <i class="fas fa-chart-bar"></i> Resumen
      </button>
    </div>
  </div>
</div>

<!-- ══════ KPIs ══════ -->
<div class="row mb-3">
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="dash-kpi k1" onclick="gaModal('modKpi')" style="cursor:pointer;">
      <div class="dash-kpi-top"></div>
      <div class="dash-kpi-inner">
        <div class="dash-kpi-bg-ico"><i class="fas fa-laptop"></i></div>
        <div class="dash-kpi-lbl">Activos registrados</div>
        <div class="dash-kpi-val" data-count="<?= (int)$totalActivos ?>"><?= (int)$totalActivos ?></div>
        <div class="dash-kpi-sub">Inventario total</div>
        <div class="dash-kpi-bar"><div class="dash-kpi-fill" style="width:0%" data-w="<?= (int)$p1 ?>"></div></div>
        <a href="<?= e(base_url()) ?>/index.php?route=activos" class="dash-kpi-link" onclick="event.stopPropagation()">
          Ver listado <i class="fas fa-arrow-right ml-1"></i>
        </a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="dash-kpi k2" onclick="gaModal('modKpi')" style="cursor:pointer;">
      <div class="dash-kpi-top"></div>
      <div class="dash-kpi-inner">
        <div class="dash-kpi-bg-ico"><i class="fas fa-tools"></i></div>
        <div class="dash-kpi-lbl">Mantenimientos pendientes</div>
        <div class="dash-kpi-val" data-count="<?= (int)$totalMantPend ?>"><?= (int)$totalMantPend ?></div>
        <div class="dash-kpi-sub">Programado / En proceso</div>
        <div class="dash-kpi-bar"><div class="dash-kpi-fill" style="width:0%" data-w="<?= (int)$p2 ?>"></div></div>
        <a href="<?= e(base_url()) ?>/index.php?route=mantenimientos" class="dash-kpi-link" onclick="event.stopPropagation()">
          Ir al módulo <i class="fas fa-arrow-right ml-1"></i>
        </a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="dash-kpi k3" onclick="gaModal('modKpi')" style="cursor:pointer;">
      <div class="dash-kpi-top"></div>
      <div class="dash-kpi-inner">
        <div class="dash-kpi-bg-ico"><i class="fas fa-screwdriver"></i></div>
        <div class="dash-kpi-lbl">Activos en mantenimiento</div>
        <div class="dash-kpi-val" data-count="<?= (int)$totalActEnMant ?>"><?= (int)$totalActEnMant ?></div>
        <div class="dash-kpi-sub">Estado del activo</div>
        <div class="dash-kpi-bar"><div class="dash-kpi-fill" style="width:0%" data-w="<?= (int)$p3 ?>"></div></div>
        <a href="<?= e(base_url()) ?>/index.php?route=activos" class="dash-kpi-link" onclick="event.stopPropagation()">
          Ver activos <i class="fas fa-arrow-right ml-1"></i>
        </a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="dash-kpi k4" onclick="gaModal('modKpi')" style="cursor:pointer;">
      <div class="dash-kpi-top"></div>
      <div class="dash-kpi-inner">
        <div class="dash-kpi-bg-ico"><i class="fas fa-ban"></i></div>
        <div class="dash-kpi-lbl">Activos de baja</div>
        <div class="dash-kpi-val" data-count="<?= (int)$totalActBaja ?>"><?= (int)$totalActBaja ?></div>
        <div class="dash-kpi-sub">Fuera de servicio</div>
        <div class="dash-kpi-bar"><div class="dash-kpi-fill" style="width:0%" data-w="<?= (int)$p4 ?>"></div></div>
        <a href="<?= e(base_url()) ?>/index.php?route=activos" class="dash-kpi-link" onclick="event.stopPropagation()">
          Revisar <i class="fas fa-arrow-right ml-1"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- ══════ SECCIÓN INFERIOR ══════ -->
<div class="row">

  <!-- Accesos rápidos -->
  <div class="col-lg-4 mb-3">
    <div class="dash-card h-100">
      <div class="dash-card-header">
        <div class="dash-card-title"><i class="fas fa-bolt"></i> Accesos rápidos</div>
      </div>
      <div style="padding:14px 14px 0;">
        <a href="<?= e(base_url()) ?>/index.php?route=activos_form" class="dash-qa qa-b">
          <span class="qi"><i class="fas fa-plus"></i></span>
          <span>Registrar nuevo activo</span>
          <i class="fas fa-arrow-right qa-arr"></i>
        </a>
        <a href="<?= e(base_url()) ?>/index.php?route=activos" class="dash-qa qa-g">
          <span class="qi"><i class="fas fa-th-list"></i></span>
          <span>Ver todos los activos</span>
          <i class="fas fa-arrow-right qa-arr"></i>
        </a>
        <a href="<?= e(base_url()) ?>/index.php?route=mantenimientos" class="dash-qa qa-w">
          <span class="qi"><i class="fas fa-tools"></i></span>
          <span>Ir a mantenimientos</span>
          <i class="fas fa-arrow-right qa-arr"></i>
        </a>
        <a href="<?= e(base_url()) ?>/index.php?route=mantenimiento_form" class="dash-qa qa-t">
          <span class="qi"><i class="fas fa-plus-circle"></i></span>
          <span>Crear mantenimiento</span>
          <i class="fas fa-arrow-right qa-arr"></i>
        </a>
      </div>
      <div style="padding:12px 14px;">
        <div class="dash-pro-box">
          <div class="dash-pro-box-ttl"><i class="fas fa-shield-alt mr-1"></i> Operación PRO</div>
          <div class="dash-pro-box-sub">Auditoría activa · Eliminación segura · Multi-cliente</div>
        </div>
        <div class="d-flex mt-2" style="gap:7px;">
          <div class="dash-mini-stat" style="background:rgba(26,111,255,.06);border:1px solid rgba(26,111,255,.12);">
            <div class="dash-mini-stat-val" style="color:#00e5ff;"><?= (int)$totalActivos ?></div>
            <div class="dash-mini-stat-lbl">Activos</div>
          </div>
          <div class="dash-mini-stat" style="background:rgba(255,179,0,.05);border:1px solid rgba(255,179,0,.12);">
            <div class="dash-mini-stat-val" style="color:#fbbf24;"><?= (int)$totalMantPend ?></div>
            <div class="dash-mini-stat-lbl">Pendientes</div>
          </div>
          <div class="dash-mini-stat" style="background:rgba(239,68,68,.05);border:1px solid rgba(239,68,68,.1);">
            <div class="dash-mini-stat-val" style="color:#f87171;"><?= (int)$totalActBaja ?></div>
            <div class="dash-mini-stat-lbl">De baja</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Últimos activos -->
  <div class="col-lg-4 mb-3">
    <div class="dash-card h-100">
      <div class="dash-card-header">
        <div class="dash-card-title"><i class="fas fa-cube"></i> Últimos activos</div>
        <a href="<?= e(base_url()) ?>/index.php?route=activos" class="dash-card-action">Ver todos <i class="fas fa-arrow-right ml-1"></i></a>
      </div>
      <?php if(!$ultActivos): ?>
        <div class="dash-empty"><i class="fas fa-inbox fa-2x d-block mb-2" style="opacity:.2;"></i>Sin registros</div>
      <?php else: ?>
        <?php foreach($ultActivos as $a):
          $b = bActivo($a['estado'] ?? '');
        ?>
        <div class="dash-row">
          <div style="min-width:0;flex:1;">
            <div class="dash-code"><?= e($a['codigo_interno'] ?: ('ID #'.(int)$a['id'])) ?></div>
            <div class="dash-name"><?= e($a['nombre'] ?: '—') ?></div>
          </div>
          <div style="flex-shrink:0;margin-left:10px;text-align:right;">
            <span class="d-badge" style="color:<?= $b['c'] ?>;background:<?= $b['b'] ?>;border:1px solid <?= $b['c'] ?>22;"><?= $b['l'] ?></span>
            <a class="dash-lnk" href="<?= e(base_url()) ?>/index.php?route=activo_detalle&id=<?= (int)$a['id'] ?>">Ver <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Actividad reciente -->
  <div class="col-lg-4 mb-3">
    <div class="dash-card h-100">
      <div class="dash-card-header">
        <div class="dash-card-title"><i class="fas fa-history"></i> Actividad reciente</div>
        <a href="<?= e(base_url()) ?>/index.php?route=mantenimientos" class="dash-card-action">Ver todos <i class="fas fa-arrow-right ml-1"></i></a>
      </div>
      <?php if(!$ultMants): ?>
        <div class="dash-empty"><i class="fas fa-clipboard fa-2x d-block mb-2" style="opacity:.2;"></i>Sin mantenimientos</div>
      <?php else: ?>
        <?php foreach($ultMants as $m):
          $b2  = bMant($m['estado'] ?? '');
          $fecha = $m['fecha_inicio'] ?: ($m['fecha_programada'] ?: null);
          $mid = (int)($m['id'] ?? 0);
        ?>
        <div class="dash-row">
          <div style="min-width:0;flex:1;">
            <div class="dash-code" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:175px;">
              <?= e($m['codigo_interno'] ?? '—') ?> · <?= e($m['activo_nombre'] ?? 'Activo') ?>
            </div>
            <div class="dash-name"><?= e($m['tipo'] ?: '—') ?> · <?= e(fFecha($fecha)) ?></div>
          </div>
          <div style="flex-shrink:0;margin-left:10px;text-align:right;">
            <span class="d-badge" style="color:<?= $b2['c'] ?>;background:<?= $b2['b'] ?>;border:1px solid <?= $b2['c'] ?>22;"><?= $b2['l'] ?></span>
            <a class="dash-lnk" href="<?= e(base_url()) ?>/index.php?route=mantenimiento_detalle&id=<?= $mid ?>">Ver <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- ══════ MODAL: RESUMEN KPIs ══════ -->
<div class="ga-modal-bd" id="modKpi">
  <div class="ga-modal">
    <div class="ga-modal-bar"></div>
    <div class="ga-modal-hd">
      <div class="ga-modal-ttl"><i class="fas fa-chart-pie mr-2" style="color:#00e5ff;font-size:1rem;"></i> Resumen operativo</div>
      <button class="ga-modal-x" onclick="gaModalClose('modKpi')"><i class="fas fa-times"></i></button>
    </div>
    <div class="ga-modal-bd-inner">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div style="background:rgba(26,111,255,.07);border:1px solid rgba(26,111,255,.15);border-radius:10px;padding:14px;">
          <div style="font-size:.6rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:rgba(78,109,140,.7);margin-bottom:4px;">Activos totales</div>
          <div style="font-family:'Bebas Neue',cursive;font-size:2.5rem;color:#00e5ff;line-height:1;"><?= (int)$totalActivos ?></div>
          <div style="font-size:.68rem;color:rgba(100,140,180,.5);margin-top:2px;">Inventario registrado</div>
        </div>
        <div style="background:rgba(255,179,0,.06);border:1px solid rgba(255,179,0,.15);border-radius:10px;padding:14px;">
          <div style="font-size:.6rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:rgba(78,109,140,.7);margin-bottom:4px;">Mant. pendientes</div>
          <div style="font-family:'Bebas Neue',cursive;font-size:2.5rem;color:#fbbf24;line-height:1;"><?= (int)$totalMantPend ?></div>
          <div style="font-size:.68rem;color:rgba(100,140,180,.5);margin-top:2px;">Programado / En proceso</div>
        </div>
        <div style="background:rgba(0,191,165,.06);border:1px solid rgba(0,191,165,.15);border-radius:10px;padding:14px;">
          <div style="font-size:.6rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:rgba(78,109,140,.7);margin-bottom:4px;">En mantenimiento</div>
          <div style="font-family:'Bebas Neue',cursive;font-size:2.5rem;color:#00bfa5;line-height:1;"><?= (int)$totalActEnMant ?></div>
          <div style="font-size:.68rem;color:rgba(100,140,180,.5);margin-top:2px;">Estado del activo</div>
        </div>
        <div style="background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.14);border-radius:10px;padding:14px;">
          <div style="font-size:.6rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:rgba(78,109,140,.7);margin-bottom:4px;">De baja</div>
          <div style="font-family:'Bebas Neue',cursive;font-size:2.5rem;color:#f87171;line-height:1;"><?= (int)$totalActBaja ?></div>
          <div style="font-size:.68rem;color:rgba(100,140,180,.5);margin-top:2px;">Fuera de servicio</div>
        </div>
      </div>
    </div>
    <div class="ga-modal-ft">
      <button onclick="gaModalClose('modKpi')" class="dash-btn dash-btn-ghost">Cerrar</button>
      <a href="<?= e(base_url()) ?>/index.php?route=activos" class="dash-btn dash-btn-primary">Ver inventario</a>
    </div>
  </div>
</div>

<script>
// Counter animation
document.querySelectorAll('[data-count]').forEach(el => {
  const t = parseInt(el.dataset.count, 10) || 0;
  if (!t) return;
  let n = 0, step = t / (800 / 16);
  const iv = setInterval(() => {
    n += step;
    if (n >= t) { el.textContent = t; clearInterval(iv); return; }
    el.textContent = Math.floor(n);
  }, 16);
});

// Bar animation
setTimeout(() => {
  document.querySelectorAll('.dash-kpi-fill').forEach(el => {
    el.style.width = (el.dataset.w || 0) + '%';
  });
}, 200);

// Modal
function gaModal(id) {
  const m = document.getElementById(id);
  if (m) { m.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function gaModalClose(id) {
  const m = document.getElementById(id);
  if (m) { m.classList.remove('open'); document.body.style.overflow = ''; }
}
document.querySelectorAll('.ga-modal-bd').forEach(bd => {
  bd.addEventListener('click', e => { if (e.target === bd) gaModalClose(bd.id); });
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') document.querySelectorAll('.ga-modal-bd.open').forEach(m => gaModalClose(m.id));
});

// Ticker pause on hover
const tk = document.querySelector('.dash-ticker-inner');
if (tk) {
  tk.parentElement.addEventListener('mouseenter', () => tk.style.animationPlayState = 'paused');
  tk.parentElement.addEventListener('mouseleave', () => tk.style.animationPlayState = 'running');
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
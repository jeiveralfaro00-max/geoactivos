<?php
require_once __DIR__ . '/../../core/Helpers.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../config/db.php';

$tenantId = Auth::tenantId();
$isSuper = Auth::isSuperadmin();

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
try{
  $q = db()->prepare("SELECT id, codigo_interno, nombre, estado FROM activos WHERE tenant_id=:t ORDER BY id DESC LIMIT 5");
  $q->execute([':t'=>$tenantId]);
  $ultActivos = $q->fetchAll();
}catch(Exception $e){$ultActivos = [];}

$ultMants = [];
try{
  $q2 = db()->prepare("SELECT m.id, m.tipo, m.estado, m.fecha_programada, m.fecha_inicio, a.codigo_interno, a.nombre AS activo_nombre FROM mantenimientos m INNER JOIN activos a ON a.id=m.activo_id AND a.tenant_id=m.tenant_id WHERE m.tenant_id=:t ORDER BY m.id DESC LIMIT 5");
  $q2->execute([':t'=>$tenantId]);
  $ultMants = $q2->fetchAll();
}catch(Exception $e){$ultMants = [];}

$mantPorMes = [];
try{
  $q3 = db()->prepare("
    SELECT DATE_FORMAT(fecha_programada, '%Y-%m') as mes, tipo, COUNT(*) as total
    FROM mantenimientos 
    WHERE tenant_id=:t AND fecha_programada IS NOT NULL AND fecha_programada >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(fecha_programada, '%Y-%m'), tipo
    ORDER BY mes ASC
  ");
  $q3->execute([':t'=>$tenantId]);
  $mantPorMes = $q3->fetchAll();
}catch(Exception $e){$mantPorMes = [];}

$alarmas = [];
try{
  $q4 = db()->prepare("
    SELECT m.id, m.tipo, m.estado, m.fecha_programada, a.codigo_interno, a.nombre as activo_nombre,
    DATEDIFF(m.fecha_programada, CURDATE()) as dias_restantes
    FROM mantenimientos m
    INNER JOIN activos a ON a.id=m.activo_id AND a.tenant_id=m.tenant_id
    WHERE m.tenant_id=:t AND m.estado IN ('PROGRAMADO') AND m.fecha_programada IS NOT NULL
    AND m.fecha_programada BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY m.fecha_programada ASC
    LIMIT 10
  ");
  $q4->execute([':t'=>$tenantId]);
  $alarmas = $q4->fetchAll();
}catch(Exception $e){$alarmas = [];}

function bActivo($estado){
  $e=(string)$estado;
  if($e==='ACTIVO')return['c'=>'#059669','b'=>'#ECFDF5','l'=>'ACTIVO'];
  if($e==='EN_MANTENIMIENTO')return['c'=>'#C07A00','b'=>'#FFFBEB','l'=>'EN MANT.'];
  if($e==='BAJA')return['c'=>'#C81E3A','b'=>'#FFF0F2','l'=>'BAJA'];
  return['c'=>'#64748B','b'=>'#F1F5F9','l'=>$e?:'-'];
}
function bMant($estado){
  $e=(string)$estado;
  if($e==='PROGRAMADO')return['c'=>'#0891B2','b'=>'#ECFEFF','l'=>'PROGRAMADO'];
  if($e==='EN_PROCESO')return['c'=>'#C07A00','b'=>'#FFFBEB','l'=>'EN PROCESO'];
  if($e==='CERRADO')return['c'=>'#059669','b'=>'#ECFDF5','l'=>'CERRADO'];
  if($e==='ANULADO')return['c'=>'#C81E3A','b'=>'#FFF0F2','l'=>'ANULADO'];
  return['c'=>'#64748B','b'=>'#F1F5F9','l'=>$e?:'-'];
}
function fFecha($v){if(!$v)return'—';return substr((string)$v,0,10);}

$tenantNombre  = $_SESSION['tenant']['nombre'] ?? ($_SESSION['user']['tenant_nombre'] ?? 'Cliente');
$usuarioNombre = $_SESSION['user']['nombre'] ?? 'Usuario';

require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/sidebar.php';
?>

<style>
.dash-hero{position:relative;overflow:hidden;background:linear-gradient(135deg,var(--teal-light),#fff);border:1px solid var(--teal-mid);border-radius:16px;padding:28px 32px;margin-bottom:24px;}
.dash-hero::before{content:'';position:absolute;right:-60px;top:-60px;width:280px;height:280px;background:radial-gradient(circle,rgba(11,168,150,0.12),transparent 70%);border-radius:50%;pointer-events:none;}
.dash-hero-line{position:absolute;top:0;left:8%;right:8%;height:1px;background:linear-gradient(90deg,transparent,var(--teal),var(--teal-mid),transparent);opacity:.7;}
.dash-hero-title{font-family:'Sora',sans-serif;font-size:1.8rem;font-weight:700;letter-spacing:-0.5px;color:var(--slate-900);margin-bottom:4px;}
.dash-hero-sub{font-size:.85rem;color:var(--slate-500);}
.dash-pill{display:inline-flex;align-items:center;gap:6px;background:var(--white);border:1px solid var(--teal-mid);border-radius:100px;padding:4px 12px;font-size:.62rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--teal-dark);margin-bottom:8px;}
.dash-pill .dp{width:5px;height:5px;border-radius:50%;background:var(--teal);box-shadow:0 0 5px var(--teal);animation:gaPulse 2s infinite;}
@keyframes gaPulse{0%,100%{box-shadow:0 0 4px var(--teal)}50%{box-shadow:0 0 12px var(--teal)}}

.dash-btn{display:inline-flex;align-items:center;gap:7px;border-radius:8px;padding:8px 16px;font-size:.75rem;font-weight:600;letter-spacing:.5px;text-transform:uppercase;transition:all .22s;text-decoration:none;border:none;cursor:pointer;}
.dash-btn-primary{background:linear-gradient(135deg,var(--teal),var(--teal-dark));color:#fff;box-shadow:0 3px 12px rgba(11,168,150,0.30);}
.dash-btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(11,168,150,0.40);color:#fff;text-decoration:none;}
.dash-btn-ghost{background:var(--white);color:var(--slate-600);border:1px solid var(--slate-200) !important;}
.dash-btn-ghost:hover{background:var(--slate-50);border-color:var(--teal) !important;color:var(--teal-dark);text-decoration:none;transform:translateY(-1px);}
.dash-btn-warn{background:transparent;color:#C07A00;border:1px solid #FDE68A !important;}
.dash-btn-warn:hover{background:#FFFBEB;border-color:#C07A00 !important;color:#C07A00;text-decoration:none;transform:translateY(-1px);}
.dash-btn-alarm{background:linear-gradient(135deg,#EF4444,#DC2626);color:#fff;border:none !important;}
.dash-btn-alarm:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(239,68,68,0.40);color:#fff;text-decoration:none;}

.dash-kpi{position:relative;overflow:hidden;background:var(--white);border:1px solid var(--slate-100);border-radius:14px;transition:all .28s;animation:dashUp .6s ease both;}
.dash-kpi:hover{border-color:var(--teal-mid);transform:translateY(-4px);box-shadow:0 12px 32px rgba(11,168,150,0.12);}
.dash-kpi-top{height:3px;}
.dash-kpi.k1 .dash-kpi-top{background:linear-gradient(90deg,var(--teal),#06B6D4);}
.dash-kpi.k2 .dash-kpi-top{background:linear-gradient(90deg,#F59E0B,#FBBF24);}
.dash-kpi.k3 .dash-kpi-top{background:linear-gradient(90deg,#8B5CF6,#A78BFA);}
.dash-kpi.k4 .dash-kpi-top{background:linear-gradient(90deg,#EF4444,#F87171);}
.dash-kpi-inner{padding:20px;}
.dash-kpi-bg-ico{position:absolute;right:16px;top:16px;font-size:2.4rem;opacity:.08;}
.dash-kpi-lbl{font-size:.65rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--slate-400);margin-bottom:6px;}
.dash-kpi-val{font-family:'Sora',sans-serif;font-size:2.4rem;font-weight:700;color:var(--slate-900);line-height:1;margin-bottom:4px;}
.dash-kpi.k1 .dash-kpi-val{color:var(--teal-dark);}
.dash-kpi.k2 .dash-kpi-val{color:#D97706;}
.dash-kpi.k3 .dash-kpi-val{color:#7C3AED;}
.dash-kpi.k4 .dash-kpi-val{color:#DC2626;}
.dash-kpi-sub{font-size:.72rem;color:var(--slate-400);margin-bottom:14px;}
.dash-kpi-bar{height:3px;background:var(--slate-50);border-radius:10px;overflow:hidden;margin-bottom:12px;}
.dash-kpi-fill{height:100%;border-radius:10px;transition:width 1.2s cubic-bezier(.22,1,.36,1);}
.dash-kpi.k1 .dash-kpi-fill{background:linear-gradient(90deg,var(--teal),#06B6D4);}
.dash-kpi.k2 .dash-kpi-fill{background:linear-gradient(90deg,#F59E0B,#FBBF24);}
.dash-kpi.k3 .dash-kpi-fill{background:linear-gradient(90deg,#8B5CF6,#A78BFA);}
.dash-kpi.k4 .dash-kpi-fill{background:linear-gradient(90deg,#EF4444,#F87171);}
.dash-kpi-link{font-size:.65rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--slate-400);text-decoration:none;transition:color .2s;}
.dash-kpi-link:hover{color:var(--teal);text-decoration:none;}

.dash-card{background:var(--white);border:1px solid var(--slate-100);border-radius:14px;overflow:hidden;animation:dashUp .6s ease both;}
.dash-card-header{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--slate-50);}
.dash-card-title{font-family:'Sora',sans-serif;font-size:.95rem;font-weight:700;color:var(--slate-900);display:flex;align-items:center;gap:8px;}
.dash-card-title i{color:var(--teal);}
.dash-card-action{font-size:.62rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--slate-400);text-decoration:none;transition:color .2s;}
.dash-card-action:hover{color:var(--teal);text-decoration:none;}

.alarm-card{background:linear-gradient(135deg,#FEF2F2,#FFF1F2);border:1px solid #FECACA;border-radius:12px;padding:14px;margin-bottom:10px;transition:all .22s;}
.alarm-card:hover{transform:translateX(4px);box-shadow:0 4px 12px rgba(239,68,68,0.15);}
.alarm-icon{width:36px;height:36px;border-radius:10px;background:#FEF2F2;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
.alarm-content{flex:1;min-width:0;}
.alarm-title{font-size:.8rem;font-weight:700;color:#991B1B;margin-bottom:2px;}
.alarm-sub{font-size:.7rem;color:#DC2626;}
.alarm-days{font-size:.75rem;font-weight:700;padding:4px 10px;border-radius:6px;background:#FEE2E2;color:#DC2626;}

.dash-qa{display:flex;align-items:center;gap:10px;padding:12px 14px;margin-bottom:6px;background:var(--slate-50);border:1px solid var(--slate-100);border-radius:10px;font-size:.8rem;font-weight:500;color:var(--slate-600);text-decoration:none;transition:all .22s;}
.dash-qa:hover{background:var(--teal-light);border-color:var(--teal-mid);color:var(--teal-dark);transform:translateX(4px);text-decoration:none;}
.dash-qa .qi{width:28px;height:28px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;flex-shrink:0;}
.dash-qa.qa-b .qi{background:var(--teal-light);color:var(--teal-dark);}
.dash-qa.qa-g .qi{background:var(--slate-100);color:var(--slate-500);}
.dash-qa.qa-w .qi{background:#FFFBEB;color:#C07A00;}
.dash-qa.qa-t .qi{background:#ECFDF5;color:#059669;}
.dash-qa .qa-arr{margin-left:auto;opacity:.3;font-size:.65rem;}

.dash-mini-stat{flex:1;border-radius:10px;padding:12px;text-align:center;}
.dash-mini-stat-val{font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:700;}
.dash-mini-stat-lbl{font-size:.55rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--slate-400);margin-top:2px;}

.dash-pro-box{margin-top:14px;padding:12px 14px;background:var(--teal-light);border:1px solid var(--teal-mid);border-radius:10px;}
.dash-pro-box-ttl{font-size:.62rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--teal-dark);margin-bottom:2px;}
.dash-pro-box-sub{font-size:.72rem;color:var(--slate-500);}

.dash-row{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-bottom:1px solid var(--slate-50);transition:background .18s;}
.dash-row:last-child{border-bottom:none;}
.dash-row:hover{background:var(--slate-50);}
.dash-code{font-family:'Sora',sans-serif;font-size:.9rem;font-weight:600;color:var(--slate-900);line-height:1.2;}
.dash-name{font-size:.72rem;color:var(--slate-400);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;margin-top:2px;}
.dash-lnk{font-size:.62rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--slate-400);text-decoration:none;transition:color .2s;display:block;text-align:right;margin-top:3px;}
.dash-lnk:hover{color:var(--teal);text-decoration:none;}
.dash-empty{padding:28px 20px;text-align:center;font-size:.8rem;color:var(--slate-300);}

.d-badge{display:inline-block;padding:3px 8px;border-radius:6px;font-size:.58rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;white-space:nowrap;}

.chart-container{position:relative;height:200px;padding:10px 0;}
.chart-container canvas{width:100% !important;height:100% !important;}

@keyframes dashUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
.dash-kpi:nth-child(1){animation-delay:.04s}
.dash-kpi:nth-child(2){animation-delay:.08s}
.dash-kpi:nth-child(3){animation-delay:.12s}
.dash-kpi:nth-child(4){animation-delay:.16s}

.alarm-count{position:absolute;top:-4px;right:-4px;background:#EF4444;color:#fff;font-size:.6rem;font-weight:700;padding:2px 6px;border-radius:10px;min-width:18px;text-align:center;}
</style>

<?php if($isSuper): ?>
<div class="dash-hero">
  <div class="dash-hero-line"></div>
  <div class="d-flex align-items-start align-items-md-center justify-content-between flex-wrap" style="gap:14px;">
    <div>
      <div class="dash-pill"><span class="dp"></span><i class="fas fa-crown" style="font-size:.6rem;"></i> Administrador General</div>
      <div class="dash-hero-title">Panel de Control</div>
      <div class="dash-hero-sub">Bienvenido, <strong style="color:var(--slate-700);"><?= e($usuarioNombre) ?></strong> · Gestión de empresas</div>
    </div>
    <div class="d-flex flex-wrap" style="gap:7px;">
      <a href="<?= e(base_url()) ?>/index.php?route=empresas" class="dash-btn dash-btn-primary"><i class="fas fa-building"></i> Empresas</a>
      <a href="<?= e(base_url()) ?>/index.php?route=usuarios" class="dash-btn dash-btn-ghost"><i class="fas fa-users"></i> Usuarios</a>
    </div>
  </div>
</div>
<?php else: ?>
<div class="dash-hero">
  <div class="dash-hero-line"></div>
  <div class="d-flex align-items-start align-items-md-center justify-content-between flex-wrap" style="gap:14px;">
    <div>
      <div class="dash-pill"><span class="dp"></span><i class="fas fa-building" style="font-size:.6rem;"></i> <?= e($tenantNombre) ?></div>
      <div class="dash-hero-title">Inicio</div>
      <div class="dash-hero-sub">Bienvenido, <strong style="color:var(--slate-700);"><?= e($usuarioNombre) ?></strong> · Resumen operativo</div>
    </div>
    <div class="d-flex flex-wrap" style="gap:7px;">
      <a href="<?= e(base_url()) ?>/index.php?route=activos_form" class="dash-btn dash-btn-primary"><i class="fas fa-plus"></i> Nuevo activo</a>
      <a href="<?= e(base_url()) ?>/index.php?route=activos" class="dash-btn dash-btn-ghost"><i class="fas fa-th-list"></i> Activos</a>
      <a href="<?= e(base_url()) ?>/index.php?route=mantenimientos" class="dash-btn dash-btn-warn"><i class="fas fa-tools"></i> Mantenimientos</a>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if(!$isSuper): ?>
<div class="row mb-3">
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="dash-kpi k1">
      <div class="dash-kpi-top"></div>
      <div class="dash-kpi-inner">
        <div class="dash-kpi-bg-ico"><i class="fas fa-box"></i></div>
        <div class="dash-kpi-lbl">Activos registrados</div>
        <div class="dash-kpi-val" data-count="<?= (int)$totalActivos ?>"><?= (int)$totalActivos ?></div>
        <div class="dash-kpi-sub">Inventario total</div>
        <div class="dash-kpi-bar"><div class="dash-kpi-fill" style="width:<?= $totalActivos>0?min(100,($totalActivos/100)*80):0 ?>%"></div></div>
        <a href="<?= e(base_url()) ?>/index.php?route=activos" class="dash-kpi-link">Ver listado <i class="fas fa-arrow-right ml-1"></i></a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="dash-kpi k2">
      <div class="dash-kpi-top"></div>
      <div class="dash-kpi-inner">
        <div class="dash-kpi-bg-ico"><i class="fas fa-tools"></i></div>
        <div class="dash-kpi-lbl">Mant. pendientes</div>
        <div class="dash-kpi-val" data-count="<?= (int)$totalMantPend ?>"><?= (int)$totalMantPend ?></div>
        <div class="dash-kpi-sub">Programado / En proceso</div>
        <div class="dash-kpi-bar"><div class="dash-kpi-fill" style="width:<?= $totalMantPend>0?min(100,$totalMantPend*10):0 ?>%"></div></div>
        <a href="<?= e(base_url()) ?>/index.php?route=mantenimientos" class="dash-kpi-link">Ir al módulo <i class="fas fa-arrow-right ml-1"></i></a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="dash-kpi k3">
      <div class="dash-kpi-top"></div>
      <div class="dash-kpi-inner">
        <div class="dash-kpi-bg-ico"><i class="fas fa-ruler-combined"></i></div>
        <div class="dash-kpi-lbl">En mantenimiento</div>
        <div class="dash-kpi-val" data-count="<?= (int)$totalActEnMant ?>"><?= (int)$totalActEnMant ?></div>
        <div class="dash-kpi-sub">Estado del activo</div>
        <div class="dash-kpi-bar"><div class="dash-kpi-fill" style="width:<?= $totalActEnMant>0?min(100,$totalActEnMant*15):0 ?>%"></div></div>
        <a href="<?= e(base_url()) ?>/index.php?route=activos" class="dash-kpi-link">Ver activos <i class="fas fa-arrow-right ml-1"></i></a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="dash-kpi k4">
      <div class="dash-kpi-top"></div>
      <div class="dash-kpi-inner">
        <div class="dash-kpi-bg-ico"><i class="fas fa-ban"></i></div>
        <div class="dash-kpi-lbl">De baja</div>
        <div class="dash-kpi-val" data-count="<?= (int)$totalActBaja ?>"><?= (int)$totalActBaja ?></div>
        <div class="dash-kpi-sub">Fuera de servicio</div>
        <div class="dash-kpi-bar"><div class="dash-kpi-fill" style="width:<?= $totalActBaja>0?min(100,$totalActBaja*20):0 ?>%"></div></div>
        <a href="<?= e(base_url()) ?>/index.php?route=activos" class="dash-kpi-link">Revisar <i class="fas fa-arrow-right ml-1"></i></a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="row">
  <?php if(!$isSuper): ?>
  <div class="col-lg-8 mb-3">
    <div class="dash-card h-100">
      <div class="dash-card-header">
        <div class="dash-card-title"><i class="fas fa-chart-line"></i> Mantenimientos por Mes</div>
        <div style="display:flex;gap:12px;">
          <span style="font-size:.65rem;color:var(--slate-400);display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:var(--teal);"></span> Preventivo</span>
          <span style="font-size:.65rem;color:var(--slate-400);display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#8B5CF6;"></span> Correctivo</span>
        </div>
      </div>
      <div class="chart-container">
        <canvas id="mantChart"></canvas>
      </div>
    </div>
  </div>

  <div class="col-lg-4 mb-3">
    <div class="dash-card h-100">
      <div class="dash-card-header">
        <div class="dash-card-title">
          <i class="fas fa-bell" style="<?= count($alarmas)>0?'color:#EF4444 !important;':'' ?>"></i> Alarmas
          <?php if(count($alarmas)>0): ?><span class="alarm-count"><?= count($alarmas) ?></span><?php endif; ?>
        </div>
        <a href="<?= e(base_url()) ?>/index.php?route=mantenimientos" class="dash-card-action">Ver todos <i class="fas fa-arrow-right ml-1"></i></a>
      </div>
      <div style="padding:10px 14px;max-height:280px;overflow-y:auto;">
        <?php if(empty($alarmas)): ?>
          <div class="dash-empty"><i class="fas fa-check-circle fa-2x d-block mb-2" style="color:var(--teal);opacity:.5;"></i>Sin alarmas próximas</div>
        <?php else: ?>
          <?php foreach($alarmas as $al): ?>
          <div class="alarm-card" style="display:flex;align-items:center;gap:10px;">
            <div class="alarm-icon"><i class="fas fa-exclamation-triangle" style="color:#DC2626;"></i></div>
            <div class="alarm-content">
              <div class="alarm-title"><?= e($al['codigo_interno']) ?></div>
              <div class="alarm-sub"><?= e($al['activo_nombre']) ?></div>
            </div>
            <div class="alarm-days">
              <?php 
                $dias = (int)$al['dias_restantes'];
                if($dias <= 0) echo 'Vencido';
                elseif($dias == 1) echo 'Mañana';
                else echo $dias.' días';
              ?>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if($isSuper): ?>
  <div class="col-lg-12 mb-3">
    <div class="dash-card">
      <div class="dash-card-header">
        <div class="dash-card-title"><i class="fas fa-building"></i> Empresas Activas</div>
        <a href="<?= e(base_url()) ?>/index.php?route=empresas" class="dash-card-action">Administrar <i class="fas fa-arrow-right ml-1"></i></a>
      </div>
      <?php
      // Check which columns exist in tenants table
      $stCols = db()->prepare("SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'tenants'");
      $stCols->execute();
      $cols = array_column($stCols->fetchAll(), 'column_name');
      
      $colDepto = in_array('departamento', $cols) ? 'departamento' : 'NULL as departamento';
      $colLogo = in_array('logo', $cols) ? 'logo' : 'NULL as logo';
      
      $st = db()->prepare("SELECT id, nombre, nit, ciudad, $colDepto, $colLogo, estado FROM tenants WHERE estado='ACTIVO' ORDER BY nombre ASC LIMIT 6");
      $st->execute();
      $empresas = $st->fetchAll();
      ?>
      <div class="row g-3" style="padding:0 14px 14px;">
        <?php foreach($empresas as $emp): ?>
        <?php
        $tid = (int)$emp['id'];
        $stA = db()->prepare("SELECT COUNT(*) as c FROM activos WHERE tenant_id = :t");
        $stA->execute([':t' => $tid]);
        $totalAct = (int)($stA->fetch()['c'] ?? 0);
        ?>
        <div class="col-md-4 col-sm-6">
          <a href="<?= e(base_url()) ?>/index.php?route=empresa_ver&id=<?= (int)$emp['id'] ?>" style="text-decoration:none;">
            <div style="background:var(--white);border:1px solid var(--slate-100);border-radius:12px;padding:14px;transition:all .2s;height:100%;">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,var(--teal),var(--teal-dark));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  <?php if(!empty($emp['logo'])): ?>
                    <img src="<?= e($emp['logo']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
                  <?php else: ?>
                    <i class="fas fa-building" style="color:#fff;font-size:1rem;"></i>
                  <?php endif; ?>
                </div>
                <div style="flex:1;min-width:0;">
                  <div style="font-weight:700;font-size:.9rem;color:var(--slate-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($emp['nombre']) ?></div>
                  <div style="font-size:.7rem;color:var(--slate-400);"><?= e($emp['nit'] ?: 'Sin NIT') ?></div>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <div style="font-size:.75rem;color:var(--slate-500);">
                  <i class="fas fa-map-marker-alt" style="font-size:.65rem;"></i> <?= e($emp['ciudad'] ?: 'Sin ciudad') ?><?php if(!empty($emp['departamento'])): ?>, <?= e($emp['departamento']) ?><?php endif; ?>
                </div>
                <div style="font-size:.75rem;font-weight:600;color:var(--teal);"><?= $totalAct ?> equipos</div>
              </div>
            </div>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if(!$isSuper): ?>
  <div class="col-lg-4 mb-3">
    <div class="dash-card h-100">
      <div class="dash-card-header">
        <div class="dash-card-title"><i class="fas fa-bolt"></i> Accesos rápidos</div>
      </div>
      <div style="padding:14px 14px 0;">
        <a href="<?= e(base_url()) ?>/index.php?route=activos_form" class="dash-qa qa-b"><span class="qi"><i class="fas fa-plus"></i></span><span>Registrar activo</span><i class="fas fa-arrow-right qa-arr"></i></a>
        <a href="<?= e(base_url()) ?>/index.php?route=activos" class="dash-qa qa-g"><span class="qi"><i class="fas fa-th-list"></i></span><span>Ver activos</span><i class="fas fa-arrow-right qa-arr"></i></a>
        <a href="<?= e(base_url()) ?>/index.php?route=mantenimientos" class="dash-qa qa-w"><span class="qi"><i class="fas fa-tools"></i></span><span>Manteni-mientos</span><i class="fas fa-arrow-right qa-arr"></i></a>
        <a href="<?= e(base_url()) ?>/index.php?route=mantenimiento_form" class="dash-qa qa-t"><span class="qi"><i class="fas fa-plus-circle"></i></span><span>Nuevo mantenimiento</span><i class="fas fa-arrow-right qa-arr"></i></a>
        <a href="<?= e(base_url()) ?>/index.php?route=calendario" class="dash-qa qa-g"><span class="qi"><i class="fas fa-calendar"></i></span><span>Calendario</span><i class="fas fa-arrow-right qa-arr"></i></a>
      </div>
      <div style="padding:12px 14px 14px;">
        <div class="dash-pro-box">
          <div class="dash-pro-box-ttl"><i class="fas fa-shield-alt mr-1"></i> Operación PRO</div>
          <div class="dash-pro-box-sub">Auditoría · Multi-cliente · RBAC</div>
        </div>
        <div class="d-flex mt-3" style="gap:8px;">
          <div class="dash-mini-stat" style="background:var(--teal-light);border:1px solid var(--teal-mid);">
            <div class="dash-mini-stat-val" style="color:var(--teal-dark);"><?= (int)$totalActivos ?></div>
            <div class="dash-mini-stat-lbl">Activos</div>
          </div>
          <div class="dash-mini-stat" style="background:#FFFBEB;border:1px solid #FDE68A;">
            <div class="dash-mini-stat-val" style="color:#D97706;"><?= (int)$totalMantPend ?></div>
            <div class="dash-mini-stat-lbl">Pendientes</div>
          </div>
          <div class="dash-mini-stat" style="background:#FFF0F2;border:1px solid #FECDD3;">
            <div class="dash-mini-stat-val" style="color:#DC2626;"><?= (int)$totalActBaja ?></div>
            <div class="dash-mini-stat-lbl">De baja</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 mb-3">
    <div class="dash-card h-100">
      <div class="dash-card-header">
        <div class="dash-card-title"><i class="fas fa-box"></i> Últimos activos</div>
        <a href="<?= e(base_url()) ?>/index.php?route=activos" class="dash-card-action">Ver todos <i class="fas fa-arrow-right ml-1"></i></a>
      </div>
      <?php if(!$ultActivos): ?>
        <div class="dash-empty"><i class="fas fa-inbox fa-2x d-block mb-2" style="opacity:.2;"></i>Sin registros</div>
      <?php else: ?>
        <?php foreach($ultActivos as $a): $b=bActivo($a['estado']??''); ?>
        <div class="dash-row">
          <div style="min-width:0;flex:1;">
            <div class="dash-code"><?= e($a['codigo_interno']?:('ID #'.(int)$a['id'])) ?></div>
            <div class="dash-name"><?= e($a['nombre']?:'—') ?></div>
          </div>
          <div style="flex-shrink:0;margin-left:10px;text-align:right;">
            <span class="d-badge" style="color:<?= $b['c'] ?>;background:<?= $b['b'] ?>;"><?= $b['l'] ?></span>
            <a class="dash-lnk" href="<?= e(base_url()) ?>/index.php?route=activo_detalle&id=<?= (int)$a['id'] ?>">Ver <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-lg-4 mb-3">
    <div class="dash-card h-100">
      <div class="dash-card-header">
        <div class="dash-card-title"><i class="fas fa-history"></i> Actividad reciente</div>
        <a href="<?= e(base_url()) ?>/index.php?route=mantenimientos" class="dash-card-action">Ver todos <i class="fas fa-arrow-right ml-1"></i></a>
      </div>
      <?php if(!$ultMants): ?>
        <div class="dash-empty"><i class="fas fa-clipboard fa-2x d-block mb-2" style="opacity:.2;"></i>Sin mantenimientos</div>
      <?php else: ?>
        <?php foreach($ultMants as $m): $b2=bMant($m['estado']??''); $fecha=$m['fecha_inicio']?:($m['fecha_programada']?:null); ?>
        <div class="dash-row">
          <div style="min-width:0;flex:1;">
            <div class="dash-code" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:150px;"><?= e($m['codigo_interno']??'—') ?> · <?= e($m['activo_nombre']??'Activo') ?></div>
            <div class="dash-name"><?= e($m['tipo']?:'—') ?> · <?= e(fFecha($fecha)) ?></div>
          </div>
          <div style="flex-shrink:0;margin-left:10px;text-align:right;">
            <span class="d-badge" style="color:<?= $b2['c'] ?>;background:<?= $b2['b'] ?>;"><?= $b2['l'] ?></span>
            <a class="dash-lnk" href="<?= e(base_url()) ?>/index.php?route=mantenimiento_detalle&id=<?= (int)($m['id']??0) ?>">Ver <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php if(!$isSuper): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('mantChart');
  if(!ctx) return;
  
  const datosPreventivo = [0,0,0,0,0,0,0,0,0,0,0,0];
  const datosCorrectivo = [0,0,0,0,0,0,0,0,0,0,0,0];
  const labels = [];
  
  const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
  const fechaActual = new Date();
  for(let i = 11; i >= 0; i--) {
    const d = new Date(fechaActual.getFullYear(), fechaActual.getMonth() - i, 1);
    labels.push(meses[d.getMonth()] + ' ' + d.getFullYear().toString().substr(-2));
  }
  
  <?php foreach($mantPorMes as $m): ?>
  for(let i = 0; i < 12; i++) {
    if(labels.includes('<?= substr($m['mes'],5,2) ?>'.replace(/^(\d{2})$/, function(m){return['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'][parseInt(m[1])-1];}) + ' <?= substr($m['mes'],2,2) ?>')) {
      const idx = labels.findIndex(l => l.includes('<?= substr($m['mes'],5,2) ?>'));
      if(idx >= 0) {
        <?php if(strtoupper($m['tipo']) === 'PREVENTIVO'): ?>
        datosPreventivo[idx] = <?= (int)$m['total'] ?>;
        <?php else: ?>
        datosCorrectivo[idx] = <?= (int)$m['total'] ?>;
        <?php endif; ?>
      }
    }
  }
  <?php endforeach; ?>
  
  const mesesAjustados = [];
  for(let i = 11; i >= 0; i--) {
    const d = new Date(fechaActual.getFullYear(), fechaActual.getMonth() - i, 1);
    mesesAjustados.push(meses[d.getMonth()]);
  }
  
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: mesesAjustados,
      datasets: [
        {
          label: 'Preventivo',
          data: datosPreventivo,
          borderColor: '#0BA896',
          backgroundColor: 'rgba(11, 168, 150, 0.1)',
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#0BA896',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6
        },
        {
          label: 'Correctivo',
          data: datosCorrectivo,
          borderColor: '#8B5CF6',
          backgroundColor: 'rgba(139, 92, 246, 0.1)',
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#8B5CF6',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: '#9DB8CC', font: { size: 11 } }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(0,0,0,0.05)' },
          ticks: { 
            color: '#9DB8CC', 
            font: { size: 11 },
            stepSize: 1
          }
        }
      },
      interaction: {
        intersect: false,
        mode: 'index'
      }
    }
  });
  
  setInterval(function() {
    fetch('<?= e(base_url()) ?>/index.php?route=ajax_dashboard_stats')
      .then(r => r.json())
      .then(data => {
        if(data.activos !== undefined) {
          document.querySelectorAll('[data-count]').forEach(el => {
            const key = el.closest('.dash-kpi').querySelector('.dash-kpi-lbl').textContent.toLowerCase();
            if(key.includes('activo') && data.activos != el.textContent) {
              el.textContent = data.activos;
              animateValue(el, parseInt(el.textContent), data.activos, 500);
            }
            if(key.includes('pendiente') && data.pendientes != el.textContent) {
              el.textContent = data.pendientes;
              animateValue(el, parseInt(el.textContent), data.pendientes, 500);
            }
          });
        }
      })
      .catch(() => {});
  }, 30000);
  
  function animateValue(el, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
      if (!startTimestamp) startTimestamp = timestamp;
      const progress = Math.min((timestamp - startTimestamp) / duration, 1);
      el.textContent = Math.floor(progress * (end - start) + start);
      if (progress < 1) {
        window.requestAnimationFrame(step);
      }
    };
    window.requestAnimationFrame(step);
  }
});
</script>
<?php endif; ?>

<script>
document.querySelectorAll('[data-count]').forEach(el=>{
  const t=parseInt(el.dataset.count,10)||0;
  if(!t)return;
  let n=0,step=t/(500/16);
  const iv=setInterval(()=>{n+=step;if(n>=t){el.textContent=t;clearInterval(iv);return;}el.textContent=Math.floor(n);},16);
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>

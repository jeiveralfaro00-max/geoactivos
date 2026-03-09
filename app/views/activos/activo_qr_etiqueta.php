<?php
require_once __DIR__ . '/../../core/Helpers.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../config/db.php';

Auth::requireLogin();

$isSuper = Auth::isSuperadmin();
$tenantIdParam = (int)($_GET['tenant_id'] ?? 0);

if ($isSuper && $tenantIdParam > 0) {
    $tenantId = $tenantIdParam;
} else {
    $tenantId = Auth::tenantId();
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) { http_response_code(400); echo "ID inválido"; exit; }

$tenant = null;
try {
  $tq = db()->prepare("SELECT id, nombre, nit, email, telefono, direccion, ciudad, logo FROM tenants WHERE id=:t LIMIT 1");
  $tq->execute([':t'=>$tenantId]);
  $tenant = $tq->fetch();
} catch (Exception $e) { $tenant = null; }

function e2($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$st = db()->prepare("
  SELECT a.id, a.codigo_interno, a.nombre, a.estado, a.modelo, a.serial, a.placa, a.foto,
         c.nombre AS categoria,
         t.nombre AS tipo, t.codigo AS tipo_codigo,
         ar.nombre AS area, s.nombre AS sede
  FROM activos a
  INNER JOIN categorias_activo c ON c.id = a.categoria_id AND c.tenant_id = a.tenant_id
  LEFT JOIN tipos_activo t ON t.id = a.tipo_activo_id AND t.tenant_id = a.tenant_id
  LEFT JOIN areas ar ON ar.id = a.area_id AND ar.tenant_id = a.tenant_id
  LEFT JOIN sedes s ON s.id = ar.sede_id AND s.tenant_id = a.tenant_id
  WHERE a.id=:id AND a.tenant_id=:t
  LIMIT 1
");
$st->execute([':id'=>$id, ':t'=>$tenantId]);
$activo = $st->fetch();

if (!$activo) { http_response_code(404); echo "Activo no encontrado"; exit; }

$modo = ($_GET['to'] ?? 'hoja');
if ($modo === 'detalle') {
  $dest = base_url() . "/index.php?route=activo_detalle&id=".(int)$activo['id'];
} else {
  $dest = base_url() . "/index.php?route=activo_hoja_vida&id=".(int)$activo['id'];
}

$w = (int)($_GET['w'] ?? 80);
$h = (int)($_GET['h'] ?? 50);
if ($w < 40) $w = 40;
if ($h < 25) $h = 25;
if ($w > 120) $w = 120;
if ($h > 80) $h = 80;

$empresaNombre = $tenant && !empty($tenant['nombre']) ? (string)$tenant['nombre'] : 'GeoActivos';
$empresaNit    = $tenant && !empty($tenant['nit']) ? (string)$tenant['nit'] : '';
$empresaLogo   = $tenant && !empty($tenant['logo']) ? (string)$tenant['logo'] : '';

$ubic = '';
if (!empty($activo['sede'])) $ubic .= (string)$activo['sede'];
if (!empty($activo['area'])) $ubic .= ($ubic ? ' - ' : '') . (string)$activo['area'];
if ($ubic === '') $ubic = '—';

$codigo = (string)($activo['codigo_interno'] ?? '');
$nombre = (string)($activo['nombre'] ?? '');
$estado = (string)($activo['estado'] ?? '');
$serial = (string)($activo['serial'] ?? '');
$modelo = (string)($activo['modelo'] ?? '');
$categoria = (string)($activo['categoria'] ?? '');

function badge_estado($estado){
  $estado = (string)$estado;
  if ($estado === 'ACTIVO') return 'verde';
  if ($estado === 'EN_MANTENIMIENTO') return 'amarillo';
  if ($estado === 'BAJA') return 'rojo';
  return 'gris';
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Etiqueta QR · <?= e2($codigo) ?></title>
  <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
  <style>
    *{ box-sizing: border-box; margin: 0; padding: 0; }
    body{ font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f5; padding: 20px; }
    
    .no-print{ text-align: center; margin-bottom: 20px; }
    .no-print .btn{
      display: inline-block;
      padding: 10px 20px;
      margin: 0 5px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      border: none;
    }
    .no-print .btn-primary{ background: #0BA896; color: #fff; }
    .no-print .btn-secondary{ background: #64748B; color: #fff; }
    
    .label-container{
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
    }
    
    .label-card{
      width: <?= $w ?>mm;
      min-height: <?= $h ?>mm;
      background: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      display: flex;
      flex-direction: column;
    }
    
    .label-header{
      background: linear-gradient(135deg, #0BA896, #077D6E);
      color: #fff;
      padding: 6mm 8mm 4mm;
      display: flex;
      align-items: center;
      gap: 6mm;
    }
    
    .label-logo{
      width: 16mm;
      height: 16mm;
      border-radius: 4mm;
      background: rgba(255,255,255,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 8mm;
      flex-shrink: 0;
    }
    
    .label-logo img{
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 4mm;
    }
    
    .label-company{
      flex: 1;
      min-width: 0;
    }
    
    .label-company-name{
      font-size: 6mm;
      font-weight: 700;
      line-height: 1.1;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    .label-company-nit{
      font-size: 4mm;
      opacity: 0.9;
      margin-top: 1mm;
    }
    
    .label-body{
      padding: 5mm 6mm;
      display: flex;
      gap: 5mm;
      flex: 1;
    }
    
    .label-info{
      flex: 1;
      min-width: 0;
    }
    
    .label-code{
      font-size: 7mm;
      font-weight: 800;
      color: #0f172a;
      line-height: 1;
      margin-bottom: 2mm;
    }
    
    .label-name{
      font-size: 5mm;
      font-weight: 600;
      color: #334155;
      line-height: 1.2;
      margin-bottom: 3mm;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .label-meta{
      font-size: 3.5mm;
      color: #64748b;
      line-height: 1.4;
    }
    
    .label-meta strong{
      color: #475569;
    }
    
    .label-badge{
      display: inline-block;
      padding: 1mm 3mm;
      border-radius: 3mm;
      font-size: 3mm;
      font-weight: 700;
      text-transform: uppercase;
      margin-top: 2mm;
    }
    .label-badge.verde{ background: #d1fae5; color: #047857; }
    .label-badge.amarillo{ background: #fef3c7; color: #b45309; }
    .label-badge.rojo{ background: #fee2e2; color: #b91c1c; }
    .label-badge.gris{ background: #f1f5f9; color: #64748b; }
    
    .label-qr{
      width: 28mm;
      height: 28mm;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .label-qr img, .label-qr canvas{
      width: 100% !important;
      height: 100% !important;
    }
    
    .label-footer{
      background: #f8fafc;
      padding: 3mm 6mm;
      border-top: 1px solid #e2e8f0;
      font-size: 3mm;
      color: #94a3b8;
      text-align: center;
    }
    
    @media print{
      .no-print{ display: none !important; }
      body{ background: #fff; padding: 0; }
      .label-card{ box-shadow: none; border: 1px solid #ccc; }
      @page{ margin: 5mm; }
    }
  </style>
</head>
<body>

  <div class="no-print">
    <a class="btn btn-secondary" href="<?= e2(base_url()) ?>/index.php?route=activo_detalle&id=<?= (int)$activo['id'] ?>">← Volver</a>
    <button class="btn btn-primary" onclick="window.print()">🖨️ Imprimir / Guardar PDF</button>
  </div>

  <div class="label-container">
    <div class="label-card">
      <div class="label-header">
        <div class="label-logo">
          <?php if(!empty($empresaLogo)): ?>
            <img src="<?= e2($empresaLogo) ?>" alt="Logo">
          <?php else: ?>
            <?= substr($empresaNombre, 0, 2) ?>
          <?php endif; ?>
        </div>
        <div class="label-company">
          <div class="label-company-name"><?= e2($empresaNombre) ?></div>
          <?php if($empresaNit): ?>
          <div class="label-company-nit">NIT: <?= e2($empresaNit) ?></div>
          <?php endif; ?>
        </div>
      </div>
      
      <div class="label-body">
        <div class="label-info">
          <div class="label-code"><?= e2($codigo) ?></div>
          <div class="label-name"><?= e2($nombre) ?></div>
          
          <div class="label-meta">
            <?php if($serial): ?>
            <div><strong>Serie:</strong> <?= e2($serial) ?></div>
            <?php endif; ?>
            <?php if($modelo): ?>
            <div><strong>Modelo:</strong> <?= e2($modelo) ?></div>
            <?php endif; ?>
            <div><strong>Ubic:</strong> <?= e2($ubic) ?></div>
            <span class="label-badge <?= badge_estado($estado) ?>"><?= e2($estado) ?></span>
          </div>
        </div>
        
        <div class="label-qr" id="qrcode-<?= $id ?>"></div>
      </div>
      
      <div class="label-footer">
        Escanea para ver <?= $modo==='detalle' ? 'detalle' : 'hoja de vida' ?> · GeoActivos
      </div>
    </div>
  </div>

  <script>
    new QRCode(document.getElementById("qrcode-<?= $id ?>"), {
      text: "<?= e2($dest) ?>",
      width: 128,
      height: 128,
      colorDark : "#000000",
      colorLight : "#ffffff",
      correctLevel : QRCode.CorrectLevel.M
    });
  </script>

</body>
</html>

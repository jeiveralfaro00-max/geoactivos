<?php
require_once __DIR__ . '/../../core/Helpers.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../config/db.php';

Auth::requireLogin();

$tenantId = Auth::tenantId();
$isSuper  = Auth::isSuperadmin();

$q = trim((string)($_GET['q'] ?? ''));
$estado = trim((string)($_GET['estado'] ?? ''));

function table_columns($table) {
  $st = db()->prepare("
    SELECT column_name
    FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = :t
  ");
  $st->execute([':t'=>$table]);
  $cols = [];
  foreach ($st->fetchAll() as $r) $cols[] = $r['column_name'];
  return $cols;
}

$cols = table_columns('tenants');
$has = function($c) use ($cols){ return in_array($c, $cols, true); };

$col_id        = 'id';
$col_nombre    = $has('nombre') ? 'nombre' : null;
$col_nit       = $has('nit') ? 'nit' : null;
$col_email     = $has('email') ? 'email' : null;
$col_telefono  = $has('telefono') ? 'telefono' : null;
$col_ciudad    = $has('ciudad') ? 'ciudad' : null;
$col_departamento = $has('departamento') ? 'departamento' : null;
$col_estado    = $has('estado') ? 'estado' : null;
$col_creado_en = $has('creado_en') ? 'creado_en' : ($has('created_at') ? 'created_at' : null);

$where = [];
$params = [];

if (!$isSuper) {
  $where[] = "id = :tenant";
  $params[':tenant'] = $tenantId;
}

if ($q !== '') {
  $like = "%$q%";
  $tmp = [];
  if ($col_nombre) { $tmp[] = "$col_nombre LIKE :q"; }
  if ($col_nit) { $tmp[] = "$col_nit LIKE :q"; }
  if ($col_email) { $tmp[] = "$col_email LIKE :q"; }
  if ($col_ciudad) { $tmp[] = "$col_ciudad LIKE :q"; }
  if ($col_departamento) { $tmp[] = "$col_departamento LIKE :q"; }
  if ($tmp) {
    $where[] = "(" . implode(" OR ", $tmp) . ")";
    $params[':q'] = $like;
  }
}

if ($estado !== '' && $col_estado) {
  $where[] = "$col_estado = :estado";
  $params[':estado'] = $estado;
}

$sqlWhere = $where ? ("WHERE " . implode(" AND ", $where)) : "";

$col_logo = $has('logo') ? 'logo' : null;
$col_representante = $has('representante') ? 'representante' : null;

$select = [
  "id",
  ($col_nombre ? "$col_nombre AS nombre" : "NULL AS nombre"),
  ($col_nit ? "$col_nit AS nit" : "NULL AS nit"),
  ($col_email ? "$col_email AS email" : "NULL AS email"),
  ($col_telefono ? "$col_telefono AS telefono" : "NULL AS telefono"),
  ($col_ciudad ? "$col_ciudad AS ciudad" : "NULL AS ciudad"),
  ($col_departamento ? "$col_departamento AS departamento" : "NULL AS departamento"),
  ($col_logo ? "$col_logo AS logo" : "NULL AS logo"),
  ($col_representante ? "$col_representante AS representante" : "NULL AS representante"),
  ($col_estado ? "$col_estado AS estado" : "NULL AS estado"),
  ($col_creado_en ? "$col_creado_en AS creado_en" : "NULL AS creado_en"),
];

$st = db()->prepare("
  SELECT " . implode(", ", $select) . "
  FROM tenants
  $sqlWhere
  ORDER BY id DESC
  LIMIT 500
");
$st->execute($params);
$rows = $st->fetchAll();

require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/sidebar.php';
?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-building"></i> Empresas</h3>
    <div class="card-tools">
      <?php if ($isSuper): ?>
        <a class="btn btn-sm btn-primary" href="<?= e(base_url()) ?>/index.php?route=empresa_form">
          <i class="fas fa-plus"></i> Nueva empresa
        </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="card-body">

    <form class="mb-3" method="get" action="<?= e(base_url()) ?>/index.php">
      <input type="hidden" name="route" value="empresas">
      <div class="form-row">
        <div class="col-md-6 mb-2">
          <input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Buscar por nombre, NIT, email, ciudad...">
        </div>
        <div class="col-md-3 mb-2">
          <select class="form-control" name="estado">
            <option value="">-- Estado (todos) --</option>
            <option value="ACTIVO" <?= ($estado==='ACTIVO')?'selected':'' ?>>ACTIVO</option>
            <option value="INACTIVO" <?= ($estado==='INACTIVO')?'selected':'' ?>>INACTIVO</option>
          </select>
        </div>
        <div class="col-md-3 mb-2">
          <button class="btn btn-primary btn-block"><i class="fas fa-search"></i> Buscar</button>
        </div>
      </div>
    </form>

    <style>
    .company-card {
      background: var(--white);
      border: 1px solid var(--slate-100);
      border-radius: 16px;
      overflow: hidden;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      height: 100%;
    }
    .company-card:hover {
      border-color: var(--teal-mid);
      transform: translateY(-6px);
      box-shadow: 0 12px 40px rgba(11, 168, 150, 0.15);
    }
    .company-card-header {
      background: linear-gradient(135deg, var(--teal-light), rgba(11, 168, 150, 0.05));
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 16px;
      border-bottom: 1px solid var(--slate-50);
    }
    .company-card-logo {
      width: 60px;
      height: 60px;
      border-radius: 14px;
      background: linear-gradient(135deg, var(--teal), var(--teal-dark));
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      color: #fff;
      flex-shrink: 0;
      box-shadow: 0 4px 12px rgba(11, 168, 150, 0.3);
      overflow: hidden;
    }
    .company-card-logo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .company-card-title {
      font-family: 'Sora', sans-serif;
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--slate-900);
      line-height: 1.2;
    }
    .company-card-subtitle {
      font-size: 0.75rem;
      color: var(--slate-500);
      margin-top: 4px;
    }
    .company-card-badge {
      margin-left: auto;
      padding: 4px 10px;
      border-radius: 100px;
      font-size: 0.65rem;
      font-weight: 700;
      letter-spacing: 0.5px;
    }
    .company-card-badge.active {
      background: var(--teal-light);
      color: var(--teal-dark);
      border: 1px solid var(--teal-mid);
    }
    .company-card-badge.inactive {
      background: #FFF0F2;
      color: #C81E3A;
      border: 1px solid #FECDD3;
    }
    .company-card-body {
      padding: 16px 20px;
    }
    .company-card-info {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .company-card-info-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }
    .company-card-info-icon {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      background: var(--slate-50);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.8rem;
      color: var(--slate-500);
      flex-shrink: 0;
    }
    .company-card-info-text {
      flex: 1;
      min-width: 0;
    }
    .company-card-info-label {
      font-size: 0.65rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--slate-400);
    }
    .company-card-info-value {
      font-size: 0.85rem;
      color: var(--slate-800);
      font-weight: 500;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .company-card-stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-top: 16px;
      padding-top: 16px;
      border-top: 1px solid var(--slate-50);
    }
    .company-card-stat {
      text-align: center;
    }
    .company-card-stat-value {
      font-family: 'Sora', sans-serif;
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--teal-dark);
    }
    .company-card-stat-label {
      font-size: 0.6rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--slate-400);
      margin-top: 2px;
    }
    .company-card-footer {
      padding: 14px 20px;
      background: var(--slate-50);
      border-top: 1px solid var(--slate-100);
      display: flex;
      gap: 10px;
    }
    .company-card-btn {
      flex: 1;
      padding: 10px 14px;
      border-radius: 10px;
      font-size: 0.8rem;
      font-weight: 600;
      text-align: center;
      text-decoration: none;
      transition: all 0.2s;
      cursor: pointer;
    }
    .company-card-btn-primary {
      background: linear-gradient(135deg, var(--teal), var(--teal-dark));
      color: #fff;
      border: none;
    }
    .company-card-btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(11, 168, 150, 0.3);
      color: #fff;
    }
    .company-card-btn-secondary {
      background: var(--white);
      color: var(--slate-600);
      border: 1px solid var(--slate-200);
    }
    .company-card-btn-secondary:hover {
      background: var(--teal-light);
      color: var(--teal-dark);
      border-color: var(--teal-mid);
    }
    </style>

    <div class="row g-4">
      <?php if (!$rows): ?>
        <div class="col-12">
          <div class="text-center text-muted p-5">No hay empresas para mostrar.</div>
        </div>
      <?php endif; ?>

      <?php foreach ($rows as $r): ?>
        <?php
          $est = (string)($r['estado'] ?? '');
          $badgeClass = $est === 'ACTIVO' ? 'active' : 'inactive';
          
          // Get stats for this company
          $tid = (int)$r['id'];
          $stAct = db()->prepare("SELECT COUNT(*) as c FROM activos WHERE tenant_id = :t");
          $stAct->execute([':t' => $tid]);
          $totalActivos = (int)($stAct->fetch()['c'] ?? 0);
          
          $stAreas = db()->prepare("SELECT COUNT(*) as c FROM areas WHERE tenant_id = :t");
          $stAreas->execute([':t' => $tid]);
          $totalAreas = (int)($stAreas->fetch()['c'] ?? 0);
          
          $stUsers = db()->prepare("SELECT COUNT(*) as c FROM usuarios WHERE tenant_id = :t AND estado = 'ACTIVO'");
          $stUsers->execute([':t' => $tid]);
          $totalUsers = (int)($stUsers->fetch()['c'] ?? 0);
          
          $stMants = db()->prepare("SELECT COUNT(*) as c FROM mantenimientos WHERE tenant_id = :t");
          $stMants->execute([':t' => $tid]);
          $totalMants = (int)($stMants->fetch()['c'] ?? 0);
        ?>
        <div class="col-md-6 col-lg-4">
          <div class="company-card">
            <div class="company-card-header">
              <div class="company-card-logo">
                <?php if (!empty($r['logo'])): ?>
                  <img src="<?= e($r['logo']) ?>" alt="<?= e($r['nombre']) ?>">
                <?php else: ?>
                  <i class="fas fa-building"></i>
                <?php endif; ?>
              </div>
              <div>
                <div class="company-card-title"><?= e($r['nombre'] ?: 'Sin nombre') ?></div>
                <div class="company-card-subtitle"><?= e($r['nit'] ?: 'Sin NIT') ?></div>
              </div>
              <div class="company-card-badge <?= $badgeClass ?>">
                <?= $est ?: '—' ?>
              </div>
            </div>
            <div class="company-card-body">
              <div class="company-card-info">
                <?php if (!empty($r['representante'])): ?>
                <div class="company-card-info-item">
                  <div class="company-card-info-icon"><i class="fas fa-user-tie"></i></div>
                  <div class="company-card-info-text">
                    <div class="company-card-info-label">Representante</div>
                    <div class="company-card-info-value"><?= e($r['representante']) ?></div>
                  </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($r['ciudad']) || !empty($r['departamento'])): ?>
                <div class="company-card-info-item">
                  <div class="company-card-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                  <div class="company-card-info-text">
                    <div class="company-card-info-label">Ubicación</div>
                    <div class="company-card-info-value">
                      <?= e(trim(($r['ciudad'] ?? '') . ', ' . ($r['departamento'] ?? ''), ', ')) ?>
                    </div>
                  </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($r['email'])): ?>
                <div class="company-card-info-item">
                  <div class="company-card-info-icon"><i class="fas fa-envelope"></i></div>
                  <div class="company-card-info-text">
                    <div class="company-card-info-label">Correo</div>
                    <div class="company-card-info-value"><?= e($r['email']) ?></div>
                  </div>
                </div>
                <?php endif; ?>
              </div>
              <div class="company-card-stats">
                <div class="company-card-stat">
                  <div class="company-card-stat-value"><?= $totalActivos ?></div>
                  <div class="company-card-stat-label">Equipos</div>
                </div>
                <div class="company-card-stat">
                  <div class="company-card-stat-value"><?= $totalAreas ?></div>
                  <div class="company-card-stat-label">Áreas</div>
                </div>
                <div class="company-card-stat">
                  <div class="company-card-stat-value"><?= $totalUsers ?></div>
                  <div class="company-card-stat-label">Usuarios</div>
                </div>
              </div>
            </div>
            <div class="company-card-footer">
              <a href="<?= e(base_url()) ?>/index.php?route=empresa_ver&id=<?= (int)$r['id'] ?>" class="company-card-btn company-card-btn-primary">
                <i class="fas fa-eye"></i> Ver Empresa
              </a>
              <?php if ($isSuper): ?>
              <a href="<?= e(base_url()) ?>/index.php?route=empresa_form&id=<?= (int)$r['id'] ?>" class="company-card-btn company-card-btn-secondary">
                <i class="fas fa-edit"></i>
              </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (!$isSuper): ?>
      <div class="text-muted text-sm mt-2">
        * Estás viendo únicamente tu empresa (tenant).
      </div>
    <?php endif; ?>

  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>

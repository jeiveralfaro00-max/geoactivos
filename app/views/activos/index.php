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

function column_exists($table, $column) {
  $st = db()->prepare("SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c LIMIT 1");
  $st->execute([':t' => $table, ':c' => $column]);
  return (bool)$st->fetchColumn();
}

$hasSoftDelete = column_exists('activos', 'eliminado');

$sql = "
  SELECT 
    a.id,
    a.codigo_interno,
    a.nombre,
    a.hostname,
    a.usa_dhcp,
    a.ip_fija,
    a.mac,
    a.modelo,
    a.serial,
    a.estado,
    a.foto,
    a.area_id,
    c.nombre AS categoria,
    ta.nombre AS tipo_activo,
    ta.codigo AS tipo_codigo,
    m.nombre AS marca,
    p.nombre AS proveedor,
    ar.nombre AS area,
    s.nombre AS sede
  FROM activos a
  INNER JOIN categorias_activo c 
    ON c.id = a.categoria_id AND c.tenant_id = a.tenant_id
  LEFT JOIN tipos_activo ta
    ON ta.id = a.tipo_activo_id AND ta.tenant_id = a.tenant_id
  LEFT JOIN marcas m
    ON m.id = a.marca_id AND m.tenant_id = a.tenant_id
  LEFT JOIN proveedores p
    ON p.id = a.proveedor_id AND p.tenant_id = a.tenant_id
  LEFT JOIN areas ar
    ON ar.id = a.area_id AND ar.tenant_id = a.tenant_id
  LEFT JOIN sedes s
    ON s.id = ar.sede_id AND s.tenant_id = a.tenant_id
  WHERE a.tenant_id = :t
 ";

$params = [':t' => $tenantId];

$q = trim($_GET['q'] ?? '');
$categoriaId = (int)($_GET['categoria_id'] ?? 0);
$areaId = (int)($_GET['area_id'] ?? 0);
$estado = $_GET['estado'] ?? '';

if ($q !== '') {
    $sql .= " AND (a.nombre LIKE :q OR a.codigo_interno LIKE :q OR a.serial LIKE :q OR a.hostname LIKE :q) ";
    $params[':q'] = "%$q%";
}

if ($categoriaId > 0) {
    $sql .= " AND a.categoria_id = :cat ";
    $params[':cat'] = $categoriaId;
}

if ($areaId > 0) {
    $sql .= " AND a.area_id = :area ";
    $params[':area'] = $areaId;
}

if ($estado !== '') {
    $sql .= " AND a.estado = :est ";
    $params[':est'] = $estado;
}

if ($hasSoftDelete) {
  $sql .= " AND a.eliminado = 0 ";
}

$sql .= " ORDER BY a.id DESC LIMIT 300 ";

$st = db()->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll();

$categorias = [];
try {
    $st = db()->prepare("SELECT id, nombre FROM categorias_activo WHERE tenant_id = :t ORDER BY nombre ASC");
    $st->execute([':t' => $tenantId]);
    $categorias = $st->fetchAll();
} catch (Exception $e) {
    $categorias = [];
}

$areas = [];
try {
    $st = db()->prepare("SELECT id, nombre FROM areas WHERE tenant_id = :t ORDER BY nombre ASC");
    $st->execute([':t' => $tenantId]);
    $areas = $st->fetchAll();
} catch (Exception $e) {
    $areas = [];
}

require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/sidebar.php';
?>

<style>
.eq-card {
    background: var(--white);
    border: 1px solid var(--slate-100);
    border-radius: 12px;
    overflow: hidden;
    transition: all .2s;
}
.eq-card:hover {
    border-color: var(--teal-mid);
    box-shadow: 0 4px 12px rgba(11,168,150,0.1);
}
.eq-card-img {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    background: var(--slate-50);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}
.eq-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.eq-card-img i {
    font-size: 1.5rem;
    color: var(--slate-400);
}
.eq-card-body {
    padding: 14px;
}
.eq-card-title {
    font-weight: 600;
    color: var(--slate-900);
    font-size: .9rem;
    margin-bottom: 4px;
}
.eq-card-sub {
    font-size: .75rem;
    color: var(--slate-500);
}
.eq-card-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 100px;
    font-size: .6rem;
    font-weight: 700;
    text-transform: uppercase;
}
.eq-card-badge.ACTIVO { background: #D1FAE5; color: #047857; }
.eq-card-badge.EN_MANTENIMIENTO { background: #FEF3C7; color: #B45309; }
.eq-card-badge.BAJA { background: #FEE2E2; color: #B91C1C; }
.eq-actions {
    display: flex;
    gap: 6px;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid var(--slate-50);
}
.eq-action-btn {
    flex: 1;
    padding: 6px 8px;
    border-radius: 6px;
    font-size: .7rem;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    background: var(--slate-50);
    color: var(--slate-600);
    border: 1px solid var(--slate-100);
    transition: all .2s;
}
.eq-action-btn:hover {
    background: var(--teal-light);
    color: var(--teal-dark);
    border-color: var(--teal-mid);
}
.filter-bar {
    background: var(--white);
    border: 1px solid var(--slate-100);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
}
.filter-bar .form-control, .filter-bar select {
    border-radius: 8px;
    border: 1px solid var(--slate-200);
}
.filter-bar .btn {
    border-radius: 8px;
}
</style>

<div class="filter-bar">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0" style="font-weight:700;"><i class="fas fa-boxes" style="color:var(--teal);"></i> Equipos</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#importModal">
                <i class="fas fa-file-excel"></i> Importar Excel
            </button>
            <a class="btn btn-primary btn-sm" href="<?= e(base_url()) ?>/index.php?route=activos_form<?= isset($_GET['tenant_id']) ? '&tenant_id='.(int)$_GET['tenant_id'] : '' ?>">
                <i class="fas fa-plus"></i> Nuevo Equipo
            </a>
        </div>
    </div>
    <form method="get" class="w-100">
        <input type="hidden" name="route" value="activos">
        <?php if(isset($_GET['tenant_id'])): ?>
        <input type="hidden" name="tenant_id" value="<?= (int)$_GET['tenant_id'] ?>">
        <?php endif; ?>
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="text-sm font-weight600">Buscar</label>
                <input type="text" name="q" value="<?= e($q) ?>" class="form-control" placeholder="Nombre, código, serie, hostname...">
            </div>
            <div class="col-md-3">
                <label class="text-sm font-weight600">Categoría</label>
                <select name="categoria_id" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach($categorias as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= ($categoriaId === (int)$c['id']) ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="text-sm font-weight600">Área</label>
                <select name="area_id" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach($areas as $a): ?>
                    <option value="<?= (int)$a['id'] ?>" <?= ($areaId === (int)$a['id']) ? 'selected' : '' ?>><?= e($a['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="text-sm font-weight600">Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="ACTIVO" <?= ($estado === 'ACTIVO') ? 'selected' : '' ?>>ACTIVO</option>
                    <option value="EN_MANTENIMIENTO" <?= ($estado === 'EN_MANTENIMIENTO') ? 'selected' : '' ?>>EN_MANTENIMIENTO</option>
                    <option value="BAJA" <?= ($estado === 'BAJA') ? 'selected' : '' ?>>BAJA</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>
</div>

<div class="row g-3">
    <?php if(empty($rows)): ?>
    <div class="col-12">
        <div class="text-center text-muted p-5">No se encontraron equipos</div>
    </div>
    <?php endif; ?>
    
    <?php foreach($rows as $r): ?>
    <div class="col-md-6 col-lg-4">
        <div class="eq-card">
            <div class="d-flex p-3 gap-3">
                <div class="eq-card-img">
                    <?php if(!empty($r['foto'])): ?>
                    <img src="<?= e($r['foto']) ?>" alt="<?= e($r['nombre']) ?>">
                    <?php else: ?>
                    <i class="fas fa-box"></i>
                    <?php endif; ?>
                </div>
                <div class="flex: 1; min-width: 0;">
                    <div class="eq-card-title"><?= e($r['nombre']) ?></div>
                    <div class="eq-card-sub"><?= e($r['codigo_interno']) ?></div>
                    <div class="eq-card-sub"><?= e($r['serial'] ?: 'Sin serie') ?></div>
                    <span class="eq-card-badge <?= e($r['estado']) ?>"><?= e($r['estado']) ?></span>
                </div>
            </div>
            <div class="eq-card-body">
                <div class="eq-card-sub">
                    <i class="fas fa-folder"></i> <?= e($r['categoria'] ?: 'Sin categoría') ?><br>
                    <i class="fas fa-map-marker-alt"></i> <?= e($r['area'] ?: 'Sin área') ?>
                    <?php if(!empty($r['sede'])): ?> · <?= e($r['sede']) ?><?php endif; ?>
                </div>
                <div class="eq-actions">
                    <a href="<?= e(base_url()) ?>/index.php?route=activo_detalle&id=<?= (int)$r['id'] ?>" class="eq-action-btn" title="Hoja de vida"><i class="fas fa-file-alt"></i></a>
                    <a href="<?= e(base_url()) ?>/index.php?route=mantenimientos&activo_id=<?= (int)$r['id'] ?>" class="eq-action-btn" title="Mantenimientos"><i class="fas fa-tools"></i></a>
                    <a href="<?= e(base_url()) ?>/index.php?route=activo_anexos&id=<?= (int)$r['id'] ?>" class="eq-action-btn" title="Anexos"><i class="fas fa-paperclip"></i></a>
                    <a href="#" class="eq-action-btn" title="Mover"><i class="fas fa-exchange-alt"></i></a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--teal),var(--teal-dark));color:#fff;">
                <h5 class="modal-title"><i class="fas fa-file-excel"></i> Importar Equipos desde Excel</h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
            </div>
            <div class="modal-body">
                <form id="importForm" method="post" action="<?= e(base_url()) ?>/index.php?route=importar_activos" enctype="multipart/form-data">
                    <input type="hidden" name="tenant_id" value="<?= isset($_GET['tenant_id']) ? (int)$_GET['tenant_id'] : $tenantId ?>">
                    
                    <div class="form-group">
                        <label>Seleccionar archivo (CSV, XLS, XLSX)</label>
                        <input type="file" name="archivo" class="form-control" accept=".csv,.xls,.xlsx" required>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-download"></i> Descargar plantilla</h6>
                        <p class="mb-2">Descargue la plantilla y llene los datos siguiendo el formato:</p>
                        <a href="<?= e(base_url()) ?>/index.php?route=descargar_plantilla_activos" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download"></i> Descargar plantilla
                        </a>
                    </div>
                    
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Importar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php';

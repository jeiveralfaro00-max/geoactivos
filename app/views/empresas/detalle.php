<?php
require_once __DIR__ . '/../../core/Helpers.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../config/db.php';

Auth::requireLogin();
$isSuper = Auth::isSuperadmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    echo '<div class="alert alert-danger">Empresa no especificada.</div>';
    exit;
}

$tenantId = Auth::tenantId();
if (!$isSuper && $tenantId != $id) {
    echo '<div class="alert alert-danger">No tienes acceso a esta empresa.</div>';
    exit;
}

$st = db()->prepare("SELECT * FROM tenants WHERE id = :id LIMIT 1");
$st->execute([':id' => $id]);
$empresa = $st->fetch();

if (!$empresa) {
    echo '<div class="alert alert-danger">Empresa no encontrada.</div>';
    exit;
}

$st = db()->prepare("SELECT COUNT(*) c FROM activos WHERE tenant_id = :t");
$st->execute([':t' => $id]);
$totalActivos = (int)($st->fetch()['c'] ?? 0);

$st = db()->prepare("SELECT COUNT(*) c FROM usuarios WHERE tenant_id = :t AND estado = 'ACTIVO'");
$st->execute([':t' => $id]);
$totalUsuarios = (int)($st->fetch()['c'] ?? 0);

$st = db()->prepare("SELECT COUNT(*) c FROM mantenimientos WHERE tenant_id = :t");
$st->execute([':t' => $id]);
$totalMants = (int)($st->fetch()['c'] ?? 0);

$categorias = [];
try {
    $q = db()->prepare("
        SELECT c.nombre, COUNT(a.id) as total
        FROM categorias_activo c
        LEFT JOIN activos a ON a.categoria_id = c.id AND a.tenant_id = :t
        WHERE c.tenant_id = :t
        GROUP BY c.id, c.nombre
        ORDER BY total DESC
        LIMIT 8
    ");
    $q->execute([':t' => $id]);
    $categorias = $q->fetchAll();
} catch (Exception $e) {
    $categorias = [];
}

$categoriasEq = [];
try {
    $q = db()->prepare("
        SELECT c.id, c.nombre, COUNT(ac.id) as total
        FROM categorias_activo c
        LEFT JOIN activos ac ON ac.categoria_id = c.id AND ac.tenant_id = :t
        WHERE c.tenant_id = :t
        GROUP BY c.id, c.nombre
        ORDER BY c.nombre ASC
        LIMIT 20
    ");
    $q->execute([':t' => $id]);
    $categoriasEq = $q->fetchAll();
} catch (Exception $e) {
    $categoriasEq = [];
}

$totalEquipos = 0;
try {
    $q = db()->prepare("SELECT COUNT(*) as c FROM activos WHERE tenant_id = :t");
    $q->execute([':t' => $id]);
    $totalEquipos = (int)($q->fetch()['c'] ?? 0);
} catch (Exception $e) {
    $totalEquipos = 0;
}

$areas = [];
try {
    $q = db()->prepare("
        SELECT a.nombre, s.nombre as sede_nombre, COUNT(ac.id) as total
        FROM areas a
        LEFT JOIN activos ac ON ac.area_id = a.id AND ac.tenant_id = :t
        LEFT JOIN sedes s ON s.id = a.sede_id AND s.tenant_id = :t
        WHERE a.tenant_id = :t
        GROUP BY a.id, a.nombre, s.nombre
        ORDER BY total DESC
        LIMIT 10
    ");
    $q->execute([':t' => $id]);
    $areas = $q->fetchAll();
} catch (Exception $e) {
    $areas = [];
}

$usuarios = [];
try {
    $q = db()->prepare("
        SELECT id, nombre, email, cargo, estado
        FROM usuarios
        WHERE tenant_id = :t
        ORDER BY nombre ASC
        LIMIT 10
    ");
    $q->execute([':t' => $id]);
    $usuarios = $q->fetchAll();
} catch (Exception $e) {
    $usuarios = [];
}

$mantenimientos = [];
try {
    $q = db()->prepare("
        SELECT m.id, m.tipo, m.estado, m.fecha_programada, a.nombre as activo_nombre
        FROM mantenimientos m
        LEFT JOIN activos a ON a.id = m.activo_id
        WHERE m.tenant_id = :t
        ORDER BY m.id DESC
        LIMIT 8
    ");
    $q->execute([':t' => $id]);
    $mantenimientos = $q->fetchAll();
} catch (Exception $e) {
    $mantenimientos = [];
}

$calibraciones = [];
try {
    $q = db()->prepare("
        SELECT c.id, c.tipo, c.estado, c.fecha_programada, a.nombre as activo_nombre
        FROM calibraciones c
        LEFT JOIN activos a ON a.id = c.activo_id
        WHERE c.tenant_id = :t
        ORDER BY c.id DESC
        LIMIT 8
    ");
    $q->execute([':t' => $id]);
    $calibraciones = $q->fetchAll();
} catch (Exception $e) {
    $calibraciones = [];
}

$patrones = [];
try {
    $q = db()->prepare("
        SELECT p.id, p.nombre, p.codigo, p.estado, p.fecha_ultima_calibracion
        FROM patrones p
        WHERE p.tenant_id = :t
        ORDER BY p.id DESC
        LIMIT 8
    ");
    $q->execute([':t' => $id]);
    $patrones = $q->fetchAll();
} catch (Exception $e) {
    $patrones = [];
}

$auditorias = [];
try {
    $q = db()->prepare("
        SELECT a.id, a.tipo, a.fecha, a.estado, u.nombre as usuario_nombre
        FROM audit_log a
        LEFT JOIN usuarios u ON u.id = a.usuario_id
        WHERE a.tenant_id = :t
        ORDER BY a.id DESC
        LIMIT 8
    ");
    $q->execute([':t' => $id]);
    $auditorias = $q->fetchAll();
} catch (Exception $e) {
    $auditorias = [];
}

require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/sidebar.php';
?>

<style>
.company-hero {
    background: linear-gradient(135deg, var(--teal-light), #fff);
    border: 1px solid var(--teal-mid);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.company-hero::before {
    content: '';
    position: absolute;
    right: -40px;
    top: -40px;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(11,168,150,0.1), transparent 70%);
    border-radius: 50%;
}
.company-logo {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--teal), var(--teal-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #fff;
    box-shadow: 0 4px 16px rgba(11,168,150,0.3);
}
.company-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 16px;
}
.company-title {
    font-family: 'Sora', sans-serif;
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--slate-900);
    margin-bottom: 4px;
}
.company-subtitle {
    font-size: .85rem;
    color: var(--slate-500);
}
.company-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--teal-light);
    border: 1px solid var(--teal-mid);
    border-radius: 100px;
    padding: 4px 12px;
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--teal-dark);
}
.company-badge .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--teal);
    animation: pulse 2s infinite;
}

.info-card {
    background: var(--white);
    border: 1px solid var(--slate-100);
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 20px;
}
.info-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-bottom: 1px solid var(--slate-50);
    background: var(--slate-50);
}
.info-card-title {
    font-family: 'Sora', sans-serif;
    font-size: .95rem;
    font-weight: 700;
    color: var(--slate-900);
    display: flex;
    align-items: center;
    gap: 8px;
}
.info-card-title i {
    color: var(--teal);
}
.info-card-body {
    padding: 16px 18px;
}
.info-field {
    margin-bottom: 14px;
}
.info-field:last-child {
    margin-bottom: 0;
}
.info-label {
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--slate-400);
    margin-bottom: 4px;
}
.info-value {
    font-size: .9rem;
    color: var(--slate-800);
}

.cat-card {
    background: var(--white);
    border: 1px solid var(--slate-100);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    transition: all .22s;
    cursor: pointer;
}
.cat-card:hover {
    border-color: var(--teal-mid);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(11,168,150,0.1);
}
.cat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: var(--teal-light);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-bottom: 10px;
}
.cat-name {
    font-family: 'Sora', sans-serif;
    font-size: .9rem;
    font-weight: 600;
    color: var(--slate-900);
    margin-bottom: 4px;
}
.cat-count {
    font-size: .75rem;
    color: var(--teal);
    font-weight: 600;
}

.dep-card {
    background: var(--slate-50);
    border: 1px solid var(--slate-100);
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 8px;
    transition: all .22s;
}
.dep-card:hover {
    background: var(--teal-light);
    border-color: var(--teal-mid);
}
.dep-name {
    font-size: .85rem;
    font-weight: 600;
    color: var(--slate-800);
}
.dep-sede {
    font-size: .7rem;
    color: var(--slate-400);
}
.dep-count {
    font-size: .75rem;
    font-weight: 700;
    color: var(--teal);
}

.user-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid var(--slate-50);
}
.user-row:last-child {
    border-bottom: none;
}
.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--teal), var(--teal-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .8rem;
    color: #fff;
    font-weight: 600;
}
.user-info {
    flex: 1;
}
.user-name {
    font-size: .85rem;
    font-weight: 600;
    color: var(--slate-900);
}
.user-email {
    font-size: .7rem;
    color: var(--slate-400);
}
.user-badge {
    font-size: .55rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 100px;
}
.user-badge.active {
    background: var(--teal-light);
    color: var(--teal-dark);
}

.stat-card {
    background: var(--white);
    border: 1px solid var(--slate-100);
    border-radius: 12px;
    padding: 16px;
    text-align: center;
    transition: all .22s;
}
.stat-card:hover {
    border-color: var(--teal-mid);
    transform: translateY(-2px);
}
.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: var(--teal-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 1rem;
    color: var(--teal);
}
.stat-value {
    font-family: 'Sora', sans-serif;
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--slate-900);
}
.stat-label {
    font-size: .65rem;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--slate-400);
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: .75rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .22s;
}
.action-btn-primary {
    background: linear-gradient(135deg, var(--teal), var(--teal-dark));
    color: #fff;
}
.action-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(11,168,150,0.3);
    color: #fff;
}
.action-btn-secondary {
    background: var(--slate-100);
    color: var(--slate-600);
    border: 1px solid var(--slate-200);
}
.action-btn-secondary:hover {
    background: var(--teal-light);
    color: var(--teal-dark);
    border-color: var(--teal-mid);
}

@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(11,168,150,0.4); }
    50% { box-shadow: 0 0 0 6px rgba(11,168,150,0); }
}
</style>

<div class="company-hero">
    <div class="d-flex align-items-start gap-3">
        <div class="company-logo">
            <?php if(!empty($empresa['logo'])): ?>
                <img src="<?= e($empresa['logo']) ?>" alt="<?= e($empresa['nombre']) ?>">
            <?php else: ?>
                <i class="fas fa-building"></i>
            <?php endif; ?>
        </div>
        <div class="flex: 1;">
            <div class="company-badge">
                <span class="dot"></span>
                <?= e($empresa['estado'] ?? 'ACTIVO') ?>
            </div>
            <div class="company-title"><?= e($empresa['nombre']) ?></div>
            <div class="company-subtitle"><?= e($empresa['nit'] ? 'NIT: ' . $empresa['nit'] : 'Empresa') ?></div>
        </div>
        <div class="d-flex gap-2">
            <?php if($isSuper): ?>
            <a href="<?= e(base_url()) ?>/index.php?route=empresa_form&id=<?= (int)$empresa['id'] ?>" class="action-btn action-btn-secondary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <?php endif; ?>
            <a href="<?= e(base_url()) ?>/index.php?route=inicio" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-box"></i></div>
            <div class="stat-value"><?= $totalActivos ?></div>
            <div class="stat-label">Activos</div>
        </div>
    </div>
    <div class="col-lg-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-value"><?= $totalUsuarios ?></div>
            <div class="stat-label">Usuarios</div>
        </div>
    </div>
    <div class="col-lg-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-tools"></i></div>
            <div class="stat-value"><?= $totalMants ?></div>
            <div class="stat-label">Mantenimientos</div>
        </div>
    </div>
    <div class="col-lg-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-value"><?= count($areas) ?></div>
            <div class="stat-label">Áreas</div>
        </div>
    </div>
</div>

<style>
.cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
.cat-card { 
    background: var(--white); 
    border: 1px solid var(--slate-100); 
    border-radius: 12px; 
    padding: 16px; 
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
    display: block;
}
.cat-card:hover { border-color: var(--teal-mid); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(11,168,150,0.15); }
.cat-icon { 
    width: 44px; height: 44px; border-radius: 10px; 
    background: var(--teal-light); 
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 10px;
}
.cat-icon i { color: var(--teal); font-size: 1.1rem; }
.cat-name { font-weight: 600; color: var(--slate-900); font-size: .9rem; margin-bottom: 4px; }
.cat-count { font-size: .75rem; color: var(--slate-500); }
.cat-count span { font-weight: 700; color: var(--teal); }
</style>

<div class="op-section">
    <div class="op-header">
        <div class="op-title"><i class="fas fa-boxes"></i> Equipos por Categoría</div>
        <div class="d-flex gap-2">
            <a href="<?= e(base_url()) ?>/index.php?route=activos&tenant_id=<?= (int)$id ?>" class="op-link">Ver todos (<?= $totalEquipos ?>) <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
    <div class="cat-grid">
        <?php if(empty($categoriasEq)): ?>
            <div class="op-empty">Sin categorías registradas</div>
        <?php else: ?>
            <?php foreach($categoriasEq as $cat): ?>
            <a href="<?= e(base_url()) ?>/index.php?route=activos&tenant_id=<?= (int)$id ?>&categoria_id=<?= (int)$cat['id'] ?>" class="cat-card">
                <div class="cat-icon"><i class="fas fa-box"></i></div>
                <div class="cat-name"><?= e($cat['nombre'] ?? 'Sin nombre') ?></div>
                <div class="cat-count"><span><?= (int)$cat['total'] ?></span> equipos</div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-3">
        <div class="info-card">
            <div class="info-card-header">
                <div class="info-card-title"><i class="fas fa-building"></i> Información</div>
            </div>
            <div class="info-card-body">
                <?php if(!empty($empresa['representante'])): ?>
                <div class="info-field">
                    <div class="info-label">Representante</div>
                    <div class="info-value"><?= e($empresa['representante']) ?></div>
                </div>
                <?php endif; ?>
                <?php if(!empty($empresa['email'])): ?>
                <div class="info-field">
                    <div class="info-label">Correo</div>
                    <div class="info-value"><?= e($empresa['email']) ?></div>
                </div>
                <?php endif; ?>
                <?php if(!empty($empresa['telefono'])): ?>
                <div class="info-field">
                    <div class="info-label">Teléfono</div>
                    <div class="info-value"><?= e($empresa['telefono']) ?></div>
                </div>
                <?php endif; ?>
                <?php if(!empty($empresa['ciudad'])): ?>
                <div class="info-field">
                    <div class="info-label">Ciudad</div>
                    <div class="info-value"><?= e($empresa['ciudad']) ?></div>
                </div>
                <?php endif; ?>
                <?php if(!empty($empresa['departamento'])): ?>
                <div class="info-field">
                    <div class="info-label">Departamento</div>
                    <div class="info-value"><?= e($empresa['departamento']) ?></div>
                </div>
                <?php endif; ?>
                <?php if(!empty($empresa['direccion'])): ?>
                <div class="info-field">
                    <div class="info-label">Dirección</div>
                    <div class="info-value"><?= e($empresa['direccion']) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="info-card">
            <div class="info-card-header">
                <div class="info-card-title"><i class="fas fa-users"></i> Usuarios</div>
                <a href="<?= e(base_url()) ?>/index.php?route=usuarios&tenant_id=<?= (int)$empresa['id'] ?>" style="font-size:.7rem;color:var(--teal);">Ver todos</a>
            </div>
            <div class="info-card-body" style="max-height:250px;overflow-y:auto;">
                <?php if(empty($usuarios)): ?>
                    <div style="text-align:center;color:var(--slate-400);padding:20px;">Sin usuarios</div>
                <?php else: ?>
                    <?php foreach($usuarios as $u): ?>
                    <div class="user-row">
                        <div class="user-avatar"><?= strtoupper(substr($u['nombre'] ?? 'U', 0, 2)) ?></div>
                        <div class="user-info">
                            <div class="user-name"><?= e($u['nombre']) ?></div>
                            <div class="user-email"><?= e($u['email'] ?? 'Sin correo') ?></div>
                        </div>
                        <span class="user-badge <?= ($u['estado'] ?? '') === 'ACTIVO' ? 'active' : '' ?>"><?= e($u['estado'] ?? 'INACTIVO') ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="info-card">
            <div class="info-card-header">
                <div class="info-card-title"><i class="fas fa-tags"></i> Categorías de Equipos</div>
            </div>
            <div class="info-card-body">
                <?php if(empty($categorias)): ?>
                    <div style="text-align:center;color:var(--slate-400);padding:20px;">Sin categorías</div>
                <?php else: ?>
                    <?php foreach($categorias as $cat): ?>
                    <div class="cat-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="cat-name"><?= e($cat['nombre'] ?? 'Sin nombre') ?></div>
                                <div class="cat-count"><?= (int)$cat['total'] ?> equipos</div>
                            </div>
                            <div class="cat-icon">
                                <i class="fas fa-box" style="color:var(--teal);"></i>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="info-card">
            <div class="info-card-header">
                <div class="info-card-title"><i class="fas fa-sitemap"></i> Áreas / Dependencias</div>
            </div>
            <div class="info-card-body" style="max-height:400px;overflow-y:auto;">
                <?php if(empty($areas)): ?>
                    <div style="text-align:center;color:var(--slate-400);padding:20px;">Sin áreas</div>
                <?php else: ?>
                    <?php foreach($areas as $a): ?>
                    <div class="dep-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="dep-name"><?= e($a['nombre'] ?? 'Sin nombre') ?></div>
                                <div class="dep-sede"><?= e($a['sede_nombre'] ?? 'Sin sede') ?></div>
                            </div>
                            <div class="dep-count"><?= (int)$a['total'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.op-section { margin-bottom: 24px; }
.op-header { 
    background: linear-gradient(135deg, var(--teal-light), #fff); 
    border: 1px solid var(--teal-mid); 
    border-radius: 14px; 
    padding: 16px 20px; 
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.op-title { font-family: 'Sora', sans-serif; font-size: 1rem; font-weight: 700; color: var(--slate-900); display: flex; align-items: center; gap: 10px; }
.op-title i { color: var(--teal); }
.op-link { font-size: .75rem; font-weight: 600; color: var(--teal); text-decoration: none; }
.op-link:hover { color: var(--teal-dark); }
.op-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; }
.op-card { 
    background: var(--white); 
    border: 1px solid var(--slate-100); 
    border-radius: 12px; 
    padding: 14px; 
    transition: all .2s;
}
.op-card:hover { border-color: var(--teal-mid); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(11,168,150,0.1); }
.op-card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
.op-card-id { font-family: 'Sora', sans-serif; font-size: .85rem; font-weight: 700; color: var(--slate-900); }
.op-card-badge { font-size: .6rem; font-weight: 700; padding: 3px 8px; border-radius: 100px; }
.op-card-badge.programado { background: #ECFEFF; color: #0891B2; }
.op-card-badge.en_proceso { background: #FFFBEB; color: #C07A00; }
.op-card-badge.cerrado { background: #ECFDF5; color: #059669; }
.op-card-badge.anulado { background: #FFF0F2; color: #C81E3A; }
.op-card-badge.activo { background: var(--teal-light); color: var(--teal-dark); }
.op-card-badge.inactivo { background: #F1F5F9; color: #64748B; }
.op-card-info { font-size: .8rem; color: var(--slate-600); }
.op-card-date { font-size: .7rem; color: var(--slate-400); margin-top: 4px; }
.op-empty { text-align: center; color: var(--slate-400); padding: 20px; font-size: .85rem; }
</style>

<div class="op-section">
    <div class="op-header">
        <div class="op-title"><i class="fas fa-tools"></i> Mantenimientos</div>
        <a href="<?= e(base_url()) ?>/index.php?route=mantenimientos&tenant_id=<?= (int)$id ?>" class="op-link">Ver todos <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="op-grid">
        <?php if(empty($mantenimientos)): ?>
            <div class="op-empty">Sin mantenimientos registrados</div>
        <?php else: ?>
            <?php foreach($mantenimientos as $m): ?>
            <a href="<?= e(base_url()) ?>/index.php?route=mantenimiento_ver&id=<?= (int)$m['id'] ?>" class="op-card" style="text-decoration:none;">
                <div class="op-card-header">
                    <span class="op-card-id">#<?= (int)$m['id'] ?></span>
                    <span class="op-card-badge <?= strtolower(str_replace('_', '', $m['estado'] ?? '')) ?>"><?= e($m['estado'] ?? '—') ?></span>
                </div>
                <div class="op-card-info"><?= e($m['activo_nombre'] ?? 'Sin equipo') ?></div>
                <div class="op-card-date"><?= e($m['tipo'] ?? '') ?> · <?= e($m['fecha_programada'] ? date('d/m/Y', strtotime($m['fecha_programada'])) : 'Sin fecha') ?></div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="op-section">
    <div class="op-header">
        <div class="op-title"><i class="fas fa-ruler-combined"></i> Calibraciones</div>
        <a href="<?= e(base_url()) ?>/index.php?route=calibraciones&tenant_id=<?= (int)$id ?>" class="op-link">Ver todos <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="op-grid">
        <?php if(empty($calibraciones)): ?>
            <div class="op-empty">Sin calibraciones registradas</div>
        <?php else: ?>
            <?php foreach($calibraciones as $c): ?>
            <a href="<?= e(base_url()) ?>/index.php?route=calibracion_detalle&id=<?= (int)$c['id'] ?>" class="op-card" style="text-decoration:none;">
                <div class="op-card-header">
                    <span class="op-card-id">#<?= (int)$c['id'] ?></span>
                    <span class="op-card-badge <?= strtolower(str_replace('_', '', $c['estado'] ?? '')) ?>"><?= e($c['estado'] ?? '—') ?></span>
                </div>
                <div class="op-card-info"><?= e($c['activo_nombre'] ?? 'Sin equipo') ?></div>
                <div class="op-card-date"><?= e($c['tipo'] ?? '') ?> · <?= e($c['fecha_programada'] ? date('d/m/Y', strtotime($c['fecha_programada'])) : 'Sin fecha') ?></div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="op-section">
    <div class="op-header">
        <div class="op-title"><i class="fas fa-balance-scale"></i> Patrones</div>
        <a href="<?= e(base_url()) ?>/index.php?route=patrones&tenant_id=<?= (int)$id ?>" class="op-link">Ver todos <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="op-grid">
        <?php if(empty($patrones)): ?>
            <div class="op-empty">Sin patrones registrados</div>
        <?php else: ?>
            <?php foreach($patrones as $p): ?>
            <a href="<?= e(base_url()) ?>/index.php?route=patron_form&id=<?= (int)$p['id'] ?>" class="op-card" style="text-decoration:none;">
                <div class="op-card-header">
                    <span class="op-card-id"><?= e($p['codigo'] ?? '#'.(int)$p['id']) ?></span>
                    <span class="op-card-badge <?= ($p['estado'] ?? '') === 'ACTIVO' ? 'activo' : 'inactivo' ?>"><?= e($p['estado'] ?? '—') ?></span>
                </div>
                <div class="op-card-info"><?= e($p['nombre'] ?? 'Sin nombre') ?></div>
                <div class="op-card-date">Última calibración: <?= e($p['fecha_ultima_calibracion'] ? date('d/m/Y', strtotime($p['fecha_ultima_calibracion'])) : 'Sin fecha') ?></div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="op-section">
    <div class="op-header">
        <div class="op-title"><i class="fas fa-clipboard-check"></i> Auditoría</div>
        <a href="<?= e(base_url()) ?>/index.php?route=audit_log&tenant_id=<?= (int)$id ?>" class="op-link">Ver todos <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="op-grid">
        <?php if(empty($auditorias)): ?>
            <div class="op-empty">Sin registros de auditoría</div>
        <?php else: ?>
            <?php foreach($auditorias as $a): ?>
            <a href="<?= e(base_url()) ?>/index.php?route=activo_auditoria&id=<?= (int)$a['id'] ?>" class="op-card" style="text-decoration:none;">
                <div class="op-card-header">
                    <span class="op-card-id">#<?= (int)$a['id'] ?></span>
                    <span class="op-card-badge activo"><?= e($a['tipo'] ?? '—') ?></span>
                </div>
                <div class="op-card-info"><?= e($a['usuario_nombre'] ?? 'Sistema') ?></div>
                <div class="op-card-date"><?= e($a['fecha'] ? date('d/m/Y H:i', strtotime($a['fecha'])) : 'Sin fecha') ?></div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>

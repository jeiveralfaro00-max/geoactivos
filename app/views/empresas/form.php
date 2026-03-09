<?php
require_once __DIR__ . '/../../core/Helpers.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../config/db.php';

Auth::requireLogin();

$tenantId = Auth::tenantId();
$currentUser = $_SESSION['user'] ?? [];

/**
 * SUPERADMIN PRO (robusto):
 * - Detecta por rol_nombre (tu login lo guarda así)
 * - También revisa rol/perfil/tipo/role por compatibilidad legacy
 * - Opcional: si rol_id == 1 lo asumimos super (puedes ajustar)
 */
function is_superadmin_user($u){
  $candidatos = [
    (string)($u['rol_nombre'] ?? ''),   // <- TU CASO REAL
    (string)($u['rol'] ?? ''),
    (string)($u['perfil'] ?? ''),
    (string)($u['tipo'] ?? ''),
    (string)($u['role'] ?? ''),
  ];

  foreach ($candidatos as $r) {
    $rol = strtolower(trim($r));
    if ($rol === 'admin' || $rol === 'superadmin' || $rol === 'root') return true;
  }

  // Fallback opcional por rol_id (si tu rol #1 es el admin principal)
  $rolId = (int)($u['rol_id'] ?? 0);
  if ($rolId === 1) return true;

  return false;
}

$isSuper = is_superadmin_user($currentUser);

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

$col_nombre      = $has('nombre') ? 'nombre' : null;
$col_nit         = $has('nit') ? 'nit' : null;
$col_email       = $has('email') ? 'email' : null;
$col_telefono    = $has('telefono') ? 'telefono' : null;
$col_direccion   = $has('direccion') ? 'direccion' : null;
$col_ciudad      = $has('ciudad') ? 'ciudad' : null;
$col_departamento = $has('departamento') ? 'departamento' : null;
$col_representante = $has('representante') ? 'representante' : null;
$col_logo        = $has('logo') ? 'logo' : null;
$col_estado      = $has('estado') ? 'estado' : null;

$id = (int)($_GET['id'] ?? 0);
$error = '';

$data = [
  'nombre' => '',
  'nit' => '',
  'email' => '',
  'telefono' => '',
  'direccion' => '',
  'ciudad' => '',
  'departamento' => '',
  'representante' => '',
  'logo' => '',
  'estado' => 'ACTIVO'
];

if ($id > 0) {
  $select = [
    "id",
    ($col_nombre ? "$col_nombre AS nombre" : "NULL AS nombre"),
    ($col_nit ? "$col_nit AS nit" : "NULL AS nit"),
    ($col_email ? "$col_email AS email" : "NULL AS email"),
    ($col_telefono ? "$col_telefono AS telefono" : "NULL AS telefono"),
    ($col_direccion ? "$col_direccion AS direccion" : "NULL AS direccion"),
    ($col_ciudad ? "$col_ciudad AS ciudad" : "NULL AS ciudad"),
    ($col_departamento ? "$col_departamento AS departamento" : "NULL AS departamento"),
    ($col_representante ? "$col_representante AS representante" : "NULL AS representante"),
    ($col_logo ? "$col_logo AS logo" : "NULL AS logo"),
    ($col_estado ? "$col_estado AS estado" : "NULL AS estado"),
  ];

  $st = db()->prepare("SELECT ".implode(", ",$select)." FROM tenants WHERE id=:id LIMIT 1");
  $st->execute([':id'=>$id]);
  $row = $st->fetch();
  if (!$row) { http_response_code(404); echo "Empresa no encontrada"; exit; }

  $data = array_merge($data, $row);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $nombre      = trim((string)($_POST['nombre'] ?? ''));
  $nit         = trim((string)($_POST['nit'] ?? ''));
  $email       = trim((string)($_POST['email'] ?? ''));
  $telefono    = trim((string)($_POST['telefono'] ?? ''));
  $direccion   = trim((string)($_POST['direccion'] ?? ''));
  $ciudad      = trim((string)($_POST['ciudad'] ?? ''));
  $departamento = trim((string)($_POST['departamento'] ?? ''));
  $representante = trim((string)($_POST['representante'] ?? ''));
  $logo        = trim((string)($_POST['logo'] ?? ''));
  $estado      = trim((string)($_POST['estado'] ?? 'ACTIVO'));

  if ($col_nombre && $nombre === '') $error = 'El nombre de la empresa es obligatorio.';
  if ($col_estado && !in_array($estado, ['ACTIVO','INACTIVO'], true)) $estado = 'ACTIVO';

  if ($error === '') {

    $fields = [];
    $params = [];

    if ($col_nombre)      { $fields['nombre'] = $nombre; }
    if ($col_nit)         { $fields['nit'] = ($nit !== '' ? $nit : null); }
    if ($col_email)       { $fields['email'] = ($email !== '' ? $email : null); }
    if ($col_telefono)    { $fields['telefono'] = ($telefono !== '' ? $telefono : null); }
    if ($col_direccion)   { $fields['direccion'] = ($direccion !== '' ? $direccion : null); }
    if ($col_ciudad)      { $fields['ciudad'] = ($ciudad !== '' ? $ciudad : null); }
    if ($col_departamento) { $fields['departamento'] = ($departamento !== '' ? $departamento : null); }
    if ($col_representante) { $fields['representante'] = ($representante !== '' ? $representante : null); }
    
    // Handle logo upload
    if ($col_logo) {
        $logoUrl = trim((string)($_POST['logo'] ?? ''));
        if (!empty($_FILES['logo_file']['tmp_name']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/logos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION);
            $filename = 'logo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $targetPath = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $targetPath)) {
                $logoUrl = base_url() . '/uploads/logos/' . $filename;
            }
        }
        if ($logoUrl !== '') {
            $fields['logo'] = $logoUrl;
        }
    }
    
    if ($col_estado)      { $fields['estado'] = $estado; }

    if ($id > 0) {

      $sets = [];
      foreach ($fields as $k=>$v) {
        $sets[] = "`$k` = :$k";
        $params[":$k"] = $v;
      }
      $params[':id'] = $id;

      $sql = "UPDATE tenants SET ".implode(", ",$sets)." WHERE id=:id";
      $up = db()->prepare($sql);
      $up->execute($params);

    } else {

      // crear solo si es superadmin
      if (!$isSuper) { http_response_code(403); echo "No autorizado."; exit; }

      $colsIns = [];
      $valsIns = [];
      foreach ($fields as $k=>$v) {
        $colsIns[] = "`$k`";
        $valsIns[] = ":$k";
        $params[":$k"] = $v;
      }

      $sql = "INSERT INTO tenants (".implode(", ",$colsIns).") VALUES (".implode(", ",$valsIns).")";
      $ins = db()->prepare($sql);
      $ins->execute($params);
    }

    redirect('index.php?route=empresas');
  }

  $data = [
    'nombre'=>$nombre,
    'nit'=>$nit,
    'email'=>$email,
    'telefono'=>$telefono,
    'direccion'=>$direccion,
    'ciudad'=>$ciudad,
    'departamento'=>$departamento,
    'representante'=>$representante,
    'logo'=>$logo,
    'estado'=>$estado
  ];
}

require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/sidebar.php';
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-building"></i> <?= ($id>0?'Editar':'Nueva') ?> empresa</h3>
    <div class="card-tools">
      <a class="btn btn-sm btn-secondary" href="<?= e(base_url()) ?>/index.php?route=empresas">
        <i class="fas fa-arrow-left"></i> Volver
      </a>
    </div>
  </div>

  <div class="card-body">

    <?php if ($error): ?>
      <div class="alert alert-danger text-sm"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (!$isSuper && $id > 0): ?>
      <div class="alert alert-info text-sm">
        Solo puedes editar tu propia empresa.
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="form-row">

        <?php if ($col_nombre): ?>
        <div class="form-group col-md-6">
          <label>Nombre *</label>
          <input class="form-control" name="nombre" value="<?= e($data['nombre'] ?? '') ?>" required>
        </div>
        <?php endif; ?>

        <?php if ($col_nit): ?>
        <div class="form-group col-md-6">
          <label>NIT</label>
          <input class="form-control" name="nit" value="<?= e($data['nit'] ?? '') ?>">
        </div>
        <?php endif; ?>

      </div>

      <div class="form-row">

        <?php if ($col_email): ?>
        <div class="form-group col-md-6">
          <label>Email</label>
          <input class="form-control" name="email" value="<?= e($data['email'] ?? '') ?>">
        </div>
        <?php endif; ?>

        <?php if ($col_telefono): ?>
        <div class="form-group col-md-6">
          <label>Teléfono</label>
          <input class="form-control" name="telefono" value="<?= e($data['telefono'] ?? '') ?>">
        </div>
        <?php endif; ?>

      </div>

      <div class="form-row">

        <?php if ($col_direccion): ?>
        <div class="form-group col-md-6">
          <label>Dirección</label>
          <input class="form-control" name="direccion" value="<?= e($data['direccion'] ?? '') ?>">
        </div>
        <?php endif; ?>

        <?php if ($col_ciudad): ?>
        <div class="form-group col-md-3">
          <label>Ciudad</label>
          <input class="form-control" name="ciudad" value="<?= e($data['ciudad'] ?? '') ?>">
        </div>
        <?php endif; ?>

        <?php if ($col_departamento): ?>
        <div class="form-group col-md-3">
          <label>Departamento</label>
          <input class="form-control" name="departamento" value="<?= e($data['departamento'] ?? '') ?>">
        </div>
        <?php endif; ?>

      </div>

      <div class="form-row">

        <?php if ($col_representante): ?>
        <div class="form-group col-md-6">
          <label>Representante Legal</label>
          <input class="form-control" name="representante" value="<?= e($data['representante'] ?? '') ?>">
        </div>
        <?php endif; ?>

        <?php if ($col_logo): ?>
        <div class="form-group col-md-6">
          <label>Logo de la Empresa</label>
          <?php if (!empty($data['logo'])): ?>
          <div class="mb-2">
            <img src="<?= e($data['logo']) ?>" alt="Logo" style="max-width:120px;max-height:80px;border-radius:8px;border:1px solid var(--slate-200);">
            <div style="font-size:.7rem;color:var(--slate-500);margin-top:4px;">Actual</div>
          </div>
          <?php endif; ?>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="logoFile" name="logo_file" accept="image/*">
            <label class="custom-file-label" for="logoFile">Seleccionar imagen...</label>
          </div>
          <input type="text" class="form-control mt-2" name="logo" value="<?= e($data['logo'] ?? '') ?>" placeholder="O ingresa URL del logo">
          <small class="form-text text-muted">Sube una imagen o ingresa una URL</small>
        </div>
        <?php endif; ?>

      </div>

      <?php if ($col_estado): ?>
      <div class="form-group">
        <label>Estado</label>
        <select class="form-control" name="estado">
          <option value="ACTIVO" <?= (($data['estado'] ?? '')==='ACTIVO')?'selected':'' ?>>ACTIVO</option>
          <option value="INACTIVO" <?= (($data['estado'] ?? '')==='INACTIVO')?'selected':'' ?>>INACTIVO</option>
        </select>
      </div>
      <?php endif; ?>

      <button class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
      <a class="btn btn-secondary" href="<?= e(base_url()) ?>/index.php?route=empresas">Cancelar</a>
    </form>

  </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>

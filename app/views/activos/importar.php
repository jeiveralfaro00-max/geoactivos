<?php
require_once __DIR__ . '/../../core/Helpers.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../config/db.php';

Auth::requireLogin();

$tenantId = isset($_POST['tenant_id']) ? (int)$_POST['tenant_id'] : Auth::tenantId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['archivo']['tmp_name'])) {
    $file = $_FILES['archivo'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if ($ext !== 'csv') {
        $_SESSION['error'] = 'Para importar use un archivo CSV';
        redirect('index.php?route=activos&tenant_id=' . $tenantId);
    }
    
    $handle = fopen($file['tmp_name'], 'r');
    $rows = [];
    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
        $rows[] = $row;
    }
    fclose($handle);
    
    $imported = 0;
    $errors = [];
    
    array_shift($rows);
    
    foreach ($rows as $i => $row) {
        if (count($row) < 2) continue;
        
        $codigo = trim($row[0] ?? '');
        $nombre = trim($row[1] ?? '');
        $categoriaNombre = trim($row[2] ?? '');
        $marcaNombre = trim($row[3] ?? '');
        $modelo = trim($row[4] ?? '');
        $serial = trim($row[5] ?? '');
        $placa = trim($row[6] ?? '');
        $areaNombre = trim($row[7] ?? '');
        $estado = trim($row[8] ?? 'ACTIVO');
        
        if (empty($codigo) || empty($nombre)) {
            $errors[] = "Fila " . ($i + 2) . ": Código o nombre vacío";
            continue;
        }
        
        $categoriaId = null;
        if (!empty($categoriaNombre)) {
            $st = db()->prepare("SELECT id FROM categorias_activo WHERE tenant_id = :t AND nombre = :n LIMIT 1");
            $st->execute([':t' => $tenantId, ':n' => $categoriaNombre]);
            $cat = $st->fetch();
            if ($cat) {
                $categoriaId = $cat['id'];
            } else {
                $ins = db()->prepare("INSERT INTO categorias_activo (tenant_id, nombre) VALUES (:t, :n)");
                $ins->execute([':t' => $tenantId, ':n' => $categoriaNombre]);
                $categoriaId = db()->lastInsertId();
            }
        }
        
        $marcaId = null;
        if (!empty($marcaNombre)) {
            $st = db()->prepare("SELECT id FROM marcas WHERE tenant_id = :t AND nombre = :n LIMIT 1");
            $st->execute([':t' => $tenantId, ':n' => $marcaNombre]);
            $mar = $st->fetch();
            if ($mar) {
                $marcaId = $mar['id'];
            } else {
                $ins = db()->prepare("INSERT INTO marcas (tenant_id, nombre) VALUES (:t, :n)");
                $ins->execute([':t' => $tenantId, ':n' => $marcaNombre]);
                $marcaId = db()->lastInsertId();
            }
        }
        
        $areaId = null;
        if (!empty($areaNombre)) {
            $st = db()->prepare("SELECT id FROM areas WHERE tenant_id = :t AND nombre = :n LIMIT 1");
            $st->execute([':t' => $tenantId, ':n' => $areaNombre]);
            $ar = $st->fetch();
            if ($ar) {
                $areaId = $ar['id'];
            } else {
                $ins = db()->prepare("INSERT INTO areas (tenant_id, nombre) VALUES (:t, :n)");
                $ins->execute([':t' => $tenantId, ':n' => $areaNombre]);
                $areaId = db()->lastInsertId();
            }
        }
        
        if (!$categoriaId) {
            $errors[] = "Fila " . ($i + 2) . ": Categoría requerida";
            continue;
        }
        
        try {
            $st = db()->prepare("INSERT INTO activos (tenant_id, categoria_id, marca_id, area_id, codigo_interno, nombre, modelo, serial, placa, estado) VALUES (:t, :cat, :mar, :area, :cod, :nom, :mod, :ser, :pla, :est)");
            $st->execute([
                ':t' => $tenantId,
                ':cat' => $categoriaId,
                ':mar' => $marcaId,
                ':area' => $areaId,
                ':cod' => $codigo,
                ':nom' => $nombre,
                ':mod' => $modelo ?: null,
                ':ser' => $serial ?: null,
                ':pla' => $placa ?: null,
                ':est' => in_array($estado, ['ACTIVO', 'EN_MANTENIMIENTO', 'BAJA']) ? $estado : 'ACTIVO'
            ]);
            $imported++;
        } catch (Exception $e) {
            $errors[] = "Fila " . ($i + 2) . ": " . $e->getMessage();
        }
    }
    
    $_SESSION['success'] = "Se importaron $imported equipos correctamente.";
    if (!empty($errors)) {
        $_SESSION['warning'] = implode("<br>", array_slice($errors, 0, 10));
    }
    
    redirect('index.php?route=activos&tenant_id=' . $tenantId);
}

<?php
require_once __DIR__ . '/../config/db.php';

function e($str) {
  return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

/**
 * base_url:
 * - Si config.php trae base_url lo respeta
 * - Si no, lo calcula como: scheme://host + directorio de /public
 */
function base_url() {
  $configFile = __DIR__ . '/../config/config.php';
  if (is_file($configFile)) {
    $config = require $configFile;
    if (!empty($config['base_url'])) return rtrim($config['base_url'], '/');
  }

  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

  // SCRIPT_NAME suele ser /geoactivos/public/index.php
  $script = $_SERVER['SCRIPT_NAME'] ?? '';
  $dir = str_replace('\\', '/', dirname($script));

  // si dirname devuelve ".", lo dejamos en vacío
  if ($dir === '.' || $dir === '/') $dir = '';

  // base quedaría .../geoactivos/public
  return rtrim($scheme . '://' . $host . $dir, '/');
}

function redirect($path) {
  header('Location: ' . base_url() . '/' . ltrim($path, '/'));
  exit;
}

/**
 * Retorna true si el activo es calibrable (según vw_activos_calibrables).
 */
function activo_es_calibrable($tenantId, $activoId) {
  $tenantId = (int)$tenantId;
  $activoId = (int)$activoId;
  if ($tenantId <= 0 || $activoId <= 0) return false;

  $st = db()->prepare("
    SELECT requiere_calibracion_eff
    FROM vw_activos_calibrables
    WHERE tenant_id = :t AND id = :a
    LIMIT 1
  ");
  $st->execute([':t'=>$tenantId, ':a'=>$activoId]);
  $r = $st->fetch();
  return ($r && (int)$r['requiere_calibracion_eff'] === 1);
}

/**
 * Guard reusable: corta con 403 si NO es calibrable.
 * Úsalo SOLO dentro de rutas/acciones de calibración.
 */
function require_activo_calibrable($tenantId, $activoId, $msg = 'Este activo no está marcado para calibración.') {
  if (!activo_es_calibrable($tenantId, $activoId)) {
    http_response_code(403);
    echo $msg;
    exit;
  }
}

/* ===================== IDIOMA / LANGUAGE ===================== */
function get_lang() {
  return $_SESSION['lang'] ?? 'es';
}

function t($key) {
  static $translations = null;
  if ($translations === null) {
    $lang = get_lang();
    $translations = [
      'es' => [
        'inicio' => 'Inicio',
        'empresas' => 'Empresas',
        'ver_empresa' => 'Ver Empresa',
        'activos' => 'Activos',
        'mantenimientos' => 'Mantenimientos',
        'calibraciones' => 'Calibraciones',
        'patrones' => 'Patrones',
        'auditoria' => 'Auditoría',
        'usuarios' => 'Usuarios',
        'configuracion' => 'Configuración',
        'categorias' => 'Categorías',
        'marcas' => 'Marcas',
        'sedes' => 'Sedes',
        'areas' => 'Áreas',
        'proveedores' => 'Proveedores',
        'tipos_activo' => 'Tipos de activo',
        'roles' => 'Roles y permisos',
        'bienvenido' => 'Bienvenido',
        'empresas_activas' => 'Empresas Activas',
        'administrador' => 'Administrador General',
        'resumen_operativo' => 'Resumen operativo',
        'accesos_rapidos' => 'Accesos rápidos',
        'registrar_activo' => 'Registrar activo',
        'ver_activos' => 'Ver activos',
        'nuevo_mantenimiento' => 'Nuevo mantenimiento',
        'calendario' => 'Calendario',
        'informacion' => 'Información',
        'representante' => 'Representante',
        'correo' => 'Correo',
        'telefono' => 'Teléfono',
        'direccion' => 'Dirección',
        'ciudad' => 'Ciudad',
        'departamento' => 'Departamento',
        'ubicacion' => 'Ubicación',
        'dependencias' => 'Dependencias',
        'subdependencias' => 'Subdependencias',
        'equipos' => 'Equipos',
        'usuarios' => 'Usuarios',
        'categorias_equipos' => 'Categorías de Equipos',
        'areas_dependencias' => 'Áreas / Dependencias',
        'total_activos' => 'Total Activos',
        'total_usuarios' => 'Total Usuarios',
        'total_mantenimientos' => 'Total Mantenimientos',
        'ultimos_activos' => 'Últimos Activos',
        'ultimos_mantenimientos' => 'Últimos Mantenimientos',
        'alarmas' => 'Alarmas',
        'mantenimiento_proximo' => 'Mantenimiento Próximo',
        'dias_restantes' => 'días restantes',
        'cerrar_sesion' => 'Cerrar sesión',
        'idioma' => 'Idioma',
        'cambiar_idioma' => 'Cambiar idioma',
        'buscar' => 'Buscar',
        'nueva_empresa' => 'Nueva empresa',
        'editar' => 'Editar',
        'eliminar' => 'Eliminar',
        'guardar' => 'Guardar',
        'cancelar' => 'Cancelar',
        'volver' => 'Volver',
        'ver_todos' => 'Ver todos',
        'sin_datos' => 'Sin datos',
        'no_hay_empresas' => 'No hay empresas para mostrar',
        'estado' => 'Estado',
        'creado' => 'Creado',
        'nit' => 'NIT',
        'activo' => 'ACTIVO',
        'inactivo' => 'INACTIVO',
        'operaciones' => 'Operaciones',
        'detalle_empresa' => 'Detalle de Empresa',
        'english' => 'Inglés',
        'spanish' => 'Español',
      ],
      'en' => [
        'inicio' => 'Home',
        'empresas' => 'Companies',
        'ver_empresa' => 'View Company',
        'activos' => 'Assets',
        'mantenimientos' => 'Maintenance',
        'calibraciones' => 'Calibrations',
        'patrones' => 'Patterns',
        'auditoria' => 'Audit',
        'usuarios' => 'Users',
        'configuracion' => 'Settings',
        'categorias' => 'Categories',
        'marcas' => 'Brands',
        'sedes' => 'Facilities',
        'areas' => 'Areas',
        'proveedores' => 'Suppliers',
        'tipos_activo' => 'Asset Types',
        'roles' => 'Roles & Permissions',
        'bienvenido' => 'Welcome',
        'empresas_activas' => 'Active Companies',
        'administrador' => 'General Administrator',
        'resumen_operativo' => 'Operational Summary',
        'accesos_rapidos' => 'Quick Access',
        'registrar_activo' => 'Register Asset',
        'ver_activos' => 'View Assets',
        'nuevo_mantenimiento' => 'New Maintenance',
        'calendario' => 'Calendar',
        'informacion' => 'Information',
        'representative' => 'Representative',
        'correo' => 'Email',
        'telefono' => 'Phone',
        'direccion' => 'Address',
        'ciudad' => 'City',
        'departamento' => 'Department',
        'ubicacion' => 'Location',
        'dependencias' => 'Dependencies',
        'subdependencias' => 'Sub-dependencies',
        'equipos' => 'Equipment',
        'usuarios' => 'Users',
        'categorias_equipos' => 'Equipment Categories',
        'areas_dependencies' => 'Areas / Dependencies',
        'total_activos' => 'Total Assets',
        'total_usuarios' => 'Total Users',
        'total_mantenimientos' => 'Total Maintenance',
        'ultimos_activos' => 'Latest Assets',
        'ultimos_mantenimientos' => 'Latest Maintenance',
        'alarmas' => 'Alerts',
        'mantenimiento_proximo' => 'Upcoming Maintenance',
        'dias_restantes' => 'days remaining',
        'cerrar_sesion' => 'Logout',
        'idioma' => 'Language',
        'cambiar_idioma' => 'Change language',
        'buscar' => 'Search',
        'nueva_empresa' => 'New Company',
        'editar' => 'Edit',
        'eliminar' => 'Delete',
        'guardar' => 'Save',
        'cancelar' => 'Cancel',
        'volver' => 'Back',
        'ver_todos' => 'View all',
        'sin_datos' => 'No data',
        'no_hay_empresas' => 'No companies to display',
        'estado' => 'Status',
        'creado' => 'Created',
        'nit' => 'NIT',
        'activo' => 'ACTIVE',
        'inactivo' => 'INACTIVE',
        'operaciones' => 'Operations',
        'detalle_empresa' => 'Company Details',
        'english' => 'English',
        'spanish' => 'Spanish',
      ]
    ];
  }
  $lang = get_lang();
  return $translations[$lang][$key] ?? $key;
}

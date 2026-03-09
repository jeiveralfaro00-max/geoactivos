<?php
require_once __DIR__ . '/../../core/Helpers.php';
require_once __DIR__ . '/../../core/Auth.php';

Auth::requireLogin();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="plantilla_equipos_geoactivos.csv"');

$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, [
    'CODIGO_INTERNO',
    'NOMBRE_EQUIPO',
    'CATEGORIA',
    'MARCA',
    'MODELO',
    'NUMERO_SERIE',
    'PLACA_INVENTARIO',
    'AREA_DEPENDENCIA',
    'SEDE',
    'PROVEEDOR',
    'FECHA_COMPRA',
    'FECHA_INSTALACION',
    'GARANTIA_HASTA',
    'ESTADO'
], ',');

fputcsv($output, [
    'EQ-001',
    'Computador Dell Inspiron 15',
    'Equipos de Computo',
    'Dell',
    'Inspiron 15 3520',
    'SN12345ABC',
    'INV-001',
    'Oficina Sistemas',
    'Sede Principal',
    'Tecnologia SA',
    '2024-01-15',
    '2024-01-20',
    '2025-01-20',
    'ACTIVO'
], ',');

fputcsv($output, [
    'BM-001',
    'Bomba de Infusion Volumetrica',
    'Equipos Biomedicos',
    'Baxter',
    'Colleague Twin',
    'BM789012XYZ',
    '',
    'UCI Adulto',
    'Hospital Central',
    'Medica SA',
    '2023-06-10',
    '2023-06-15',
    '2024-06-15',
    'ACTIVO'
], ',');

fputcsv($output, [
    'SEG-001',
    'Camara de Seguridad IP',
    'Equipos de Seguridad',
    'Hikvision',
    'DS-2CD2043G2-I',
    'HK345678DEF',
    '',
    'Entrada Principal',
    'Edificio Norte',
    'Seguridad Cces',
    '2024-02-01',
    '2024-02-05',
    '2026-02-05',
    'ACTIVO'
], ',');

fputcsv($output, [
    'LAB-001',
    'Analizador de Hematologia',
    'Equipos de Laboratorio',
    'Sysmex',
    'XN-350',
    'SY789012GHI',
    'LAB-001',
    'Laboratorio Clinico',
    'Sede Principal',
    'Diagnosticos SA',
    '2023-03-20',
    '2023-03-25',
    '2025-03-25',
    'ACTIVO'
], ',');

fputcsv($output, [
    'MOB-001',
    'Escritorio Ejecutivo',
    'Mobiliario',
    'Steelcase',
    'Think 71',
    '',
    'MOB-001',
    'Gerencia',
    'Sede Principal',
    'Oficina Plus',
    '2022-11-01',
    '2022-11-05',
    '2027-11-05',
    'ACTIVO'
], ',');

fclose($output);
exit;

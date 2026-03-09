-- ==========================================
-- MIGRACIÓN SIMPLE PARA GEOACTIVOS
-- Copia y ejecuta cada línea QUE NO DÉ ERROR
-- Si una da error de "Duplicate column", ¡no importa! ya existe
-- ==========================================

-- Si hay error, ejecuta solo las que faltan:

-- 1. Agregar departamento (si ya existe, dará error pero no pasa nada)
ALTER TABLE tenants ADD COLUMN departamento VARCHAR(100) DEFAULT NULL AFTER ciudad;

-- 2. Agregar representante
ALTER TABLE tenants ADD COLUMN representante VARCHAR(150) DEFAULT NULL AFTER departamento;

-- 3. Agregar logo
ALTER TABLE tenants ADD COLUMN logo VARCHAR(255) DEFAULT NULL AFTER representante;

-- 4. Agregar foto a equipos
ALTER TABLE activos ADD COLUMN foto VARCHAR(255) DEFAULT NULL AFTER placa;

-- 5. Agregar subdependencias
ALTER TABLE areas ADD COLUMN area_parent_id INT DEFAULT NULL AFTER sede_id;

-- 6. Actualizar estado (si aplica)
UPDATE tenants SET estado = 'INACTIVO' WHERE estado = 'SUSPENDIDO';

SELECT 'Listo!' AS mensaje;

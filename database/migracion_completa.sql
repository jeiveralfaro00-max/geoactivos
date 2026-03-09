-- ==========================================
-- MIGRACIÓN COMPLETA PARA GEOACTIVOS
-- Ejecutar TODO este archivo en phpMyAdmin
-- ==========================================

SET NAMES utf8mb4;

-- 1. Verificar y agregar columna 'departamento' a tenants
SET @db_name = DATABASE();
SET @col_dept = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'tenants' AND COLUMN_NAME = 'departamento');
IF @col_dept = 0 THEN
    ALTER TABLE tenants ADD COLUMN departamento VARCHAR(100) DEFAULT NULL AFTER ciudad;
END IF;

-- 2. Verificar y agregar columna 'representante' a tenants
SET @col_rep = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'tenants' AND COLUMN_NAME = 'representante');
IF @col_rep = 0 THEN
    ALTER TABLE tenants ADD COLUMN representante VARCHAR(150) DEFAULT NULL AFTER departamento;
END IF;

-- 3. Verificar y agregar columna 'logo' a tenants
SET @col_logo = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'tenants' AND COLUMN_NAME = 'logo');
IF @col_logo = 0 THEN
    ALTER TABLE tenants ADD COLUMN logo VARCHAR(255) DEFAULT NULL AFTER representante;
END IF;

-- 4. Verificar y agregar columna 'foto' a activos
SET @col_foto = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'activos' AND COLUMN_NAME = 'foto');
IF @col_foto = 0 THEN
    ALTER TABLE activos ADD COLUMN foto VARCHAR(255) DEFAULT NULL AFTER placa;
END IF;

-- 5. Verificar y agregar columna 'area_parent_id' a areas (subdependencias)
SET @col_parent = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'areas' AND COLUMN_NAME = 'area_parent_id');
IF @col_parent = 0 THEN
    ALTER TABLE areas ADD COLUMN area_parent_id INT DEFAULT NULL AFTER sede_id;
END IF;

-- 6. Si tenant tiene estado 'SUSPENDIDO', cambiar a 'INACTIVO'
UPDATE tenants SET estado = 'INACTIVO' WHERE estado = 'SUSPENDIDO';

SELECT 'Migración completada exitosamente' AS resultado;

-- Actualizaciones de base de datos en el mes de febrero de 2022
-- Por: Edwin Fajardo
-- 2022-02-07
ALTER TABLE `user_sessions` CHANGE `branch_id` `branch_id` INT(11) NULL COMMENT 'Sucursal en la que inició sesión';
-- Nahutech Local Test
-- Teleinf Local test

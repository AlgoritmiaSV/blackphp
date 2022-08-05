-- Nahutech Local Test
-- 2022-08-04
CREATE TABLE `app_options` ( `option_id` INT NOT NULL AUTO_INCREMENT COMMENT 'Llave primaria' , `option_key` VARCHAR(32) NOT NULL COMMENT 'Clave de la opción' , `option_description` TINYTEXT NOT NULL COMMENT 'Descripción de la opción' , `module_id` INT NULL COMMENT 'Módulo en el que aplica la opción' , PRIMARY KEY (`option_id`)) ENGINE = InnoDB COMMENT = 'Opciones de la aplicación, configurables por entidad';
DROP VIEW `entity_options`;
CREATE TABLE `entity_options` ( `eoption_id` INT NOT NULL AUTO_INCREMENT COMMENT 'Llave primaria' , `entity_id` INT NOT NULL COMMENT 'ID de la entidad' , `option_id` INT NOT NULL COMMENT 'ID de la opción' , `option_value` TINYINT NOT NULL COMMENT 'Valor de la opción' , `creation_user` INT NOT NULL , `creation_time` DATETIME NOT NULL , `edition_user` INT NOT NULL , `edition_time` DATETIME NOT NULL , `status` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`eoption_id`)) ENGINE = InnoDB COMMENT = 'Valores configurados en cada entidad';
ALTER TABLE `app_options` CHANGE `module_id` `module_id` INT NULL DEFAULT NULL COMMENT 'Módulo en el que se realiza la configuración';
ALTER TABLE `app_options` ADD `default_value` TINYINT NOT NULL COMMENT 'Valor por defecto de la opción' AFTER `module_id`;
ALTER TABLE `app_options` ADD CONSTRAINT `option_module` FOREIGN KEY (`module_id`) REFERENCES `app_modules`(`module_id`) ON DELETE CASCADE ON UPDATE CASCADE;
DELIMITER $$
CREATE TRIGGER `AppOptionAfterInsert` AFTER INSERT ON `app_options` FOR EACH ROW INSERT INTO entity_options SELECT NULL, entity_id, NEW.option_id, NEW.default_value, 0, NOW(), 0, NOW(), 1 FROM entities $$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `EntityAfterInsert` AFTER INSERT ON `entities` FOR EACH ROW INSERT INTO entity_options SELECT NULL, NEW.entity_id, option_id, default_value, 0, now(), 0, now(), 1 FROM app_options $$
DELIMITER ;
ALTER TABLE `entity_options` ADD CONSTRAINT `eoption_entity` FOREIGN KEY (`entity_id`) REFERENCES `entities`(`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `entity_options` ADD CONSTRAINT `eoption_option` FOREIGN KEY (`option_id`) REFERENCES `app_options`(`option_id`) ON DELETE CASCADE ON UPDATE CASCADE;
-- Teleinf Local test

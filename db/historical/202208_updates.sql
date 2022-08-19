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
-- 2022-08-06
ALTER ALGORITHM = UNDEFINED VIEW `available_modules` AS select `m`.*,`um`.`access_type`,`em`.`entity_id`,`u`.`user_id`,`em`.`module_order` from `entity_modules` `em` join `app_modules` `m` join `user_modules` `um` join `users` `u` where `m`.`module_id` = `em`.`module_id` and `em`.`status` = 1 and `um`.`module_id` = `m`.`module_id` and `um`.`status` = 1 and `u`.`entity_id` = `em`.`entity_id` AND `u`.`user_id` = `um`.`user_id`;
ALTER TABLE `user_modules` DROP INDEX `unique_access`;
DELETE FROM `user_modules` WHERE `umodule_id` IN(SELECT DISTINCT `t1`.`umodule_id` FROM `user_modules` AS `t1`, `user_modules` AS `t2` WHERE `t1`.`module_id` = `t2`.`module_id` AND `t1`.`user_id` = `t2`.`user_id` AND `t1`.`umodule_id` > `t2`.`umodule_id`);
ALTER TABLE `user_modules` ADD UNIQUE `unique_user_module` (`module_id`, `user_id`);
DELETE FROM `user_methods` WHERE `umethod_id` IN(SELECT DISTINCT `t1`.`umethod_id` FROM `user_methods` AS `t1`, `user_methods` AS `t2` WHERE `t1`.`method_id` = `t2`.`method_id` AND `t1`.`user_id` = `t2`.`user_id` AND `t1`.`umethod_id` > `t2`.`umethod_id`);
ALTER TABLE `user_methods` ADD UNIQUE `unique_user_method` (`user_id`, `method_id`);
-- 2022-08-11
ALTER TABLE `app_options` ADD UNIQUE `unique_key_module` (`option_key`, `module_id`);
-- 2022-08-17
ALTER TABLE `app_options` ADD `option_type` TINYINT NOT NULL DEFAULT '1' COMMENT 'Tipo de variable: 1: Booleana; 2: Valor' AFTER `option_id`;
-- 2022-08-18
ALTER TABLE `app_options` CHANGE `default_value` `default_value` VARCHAR(255) NOT NULL COMMENT 'Valor por defecto de la opción';
ALTER TABLE `entity_options` CHANGE `option_value` `option_value` VARCHAR(255) NOT NULL COMMENT 'Valor de la opción';
-- Nahutech Local Test
-- Teleinf Local test

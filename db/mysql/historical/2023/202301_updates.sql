-- Actualizaciones de base de datos en el mes de octubre de 2022
-- Por: Edwin Fajardo
-- 2023-01-03
INSERT INTO `app_methods` (`method_id`, `module_id`, `method_name`, `method_url`, `method_icon`, `method_description`, `default_order`, `status`) VALUES (NULL, '1', 'Trash', 'Trash', 'trash', 'Deleted elements', '127', '1');
-- 2023-01-04
INSERT INTO `app_modules` (`module_id`, `module_name`, `module_url`, `module_icon`, `module_key`, `module_description`, `default_order`, `status`) VALUES (NULL, 'Tools', 'Tools', 'tools', 'T', '', '126', '1');
UPDATE `app_methods` SET `module_id` = '2', `default_order` = '1' WHERE `app_methods`.`method_id` = 5;
-- Eliminación de app_actions
ALTER TABLE `user_logs` DROP FOREIGN KEY `log_action`;
UPDATE `user_logs` SET `action_id`= 1 WHERE `action_id` BETWEEN 1 AND 4;
UPDATE `user_logs` SET `action_id`= 2 WHERE `action_id` BETWEEN 1 AND 6;
UPDATE `user_logs` SET `action_id`= 3 WHERE `action_id` BETWEEN 7 AND 9;
DROP TABLE `app_actions`;
ALTER TABLE `app_elements` DROP `element_number`;
ALTER TABLE `app_elements` ADD `deletable` TINYINT NOT NULL COMMENT 'El elemento se puede eliminar' AFTER `method_name`, ADD `table_name` VARCHAR(64) NOT NULL COMMENT 'Nombre de la tabla' AFTER `deletable`;
-- /Eliminación de app_actions
-- 2023-01-05
ALTER TABLE `app_elements` ADD `singular_name` VARCHAR(32) NOT NULL COMMENT 'Nombre singular del elemento' AFTER `element_name`;
UPDATE `app_elements` SET `element_name` = 'Entity data', `singular_name` = 'entity data', `method_name` = 'Entity', `table_name` = 'entities' WHERE `app_elements`.`element_id` = 1;
UPDATE `app_elements` SET `element_key` = 'users', `element_name` = 'Users', `singular_name` = 'user', `method_name` = 'UserDetails', `deletable` = '1', `table_name` = 'users' WHERE `app_elements`.`element_id` = 2;
UPDATE `app_elements` SET `element_name` = 'Preferences', `singular_name` = 'preferences', `method_name` = 'Preferences', `table_name` = 'entity_options' WHERE `app_elements`.`element_id` = 3;
TRUNCATE TABLE `user_logs`;
-- 2023-01-08
ALTER TABLE `app_elements` CHANGE `element_key` `element_key` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Clave del elemento';
-- 2023-01-14
ALTER TABLE `entities` DROP `sys_name`;
ALTER TABLE `entities` ADD `app_name` VARCHAR(32) NOT NULL DEFAULT 'BlackPHP' COMMENT 'Nombre de la App para instalación como PWA' AFTER `entity_subdomain`;
-- inabve:blackphp
-- teleinf:blackphp

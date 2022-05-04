-- 2022-04-16
ALTER TABLE `app_modules` ADD `default_order` TINYINT NOT NULL COMMENT 'Orden por defecto' AFTER `module_description`;
ALTER TABLE `app_methods` ADD `default_order` TINYINT NOT NULL COMMENT 'Orden por defecto' AFTER `method_description`;
INSERT INTO `app_themes` (`theme_id`, `theme_name`, `theme_url`) VALUES (NULL, 'Negro - Menú lateral', 'black'), (NULL, 'Verde - Menú lateral', 'green'), (NULL, 'Azul - Menú superior', 'blue_top');
UPDATE `app_themes` SET `theme_name` = 'Azul - Menú lateral' WHERE `app_themes`.`theme_id` = 1;
-- 2022-04-28
UPDATE `app_modules` SET `default_order` = '127' WHERE `app_modules`.`module_id` = 1;
ALTER TABLE `app_modules` ADD `status` TINYINT NOT NULL DEFAULT '1' COMMENT 'Estado 0:inactivo, 1:activo' AFTER `default_order`;
ALTER TABLE `app_methods` ADD `status` TINYINT NOT NULL DEFAULT '1' COMMENT 'Estado 0:inactivo, 1:activo' AFTER `default_order`;
-- 2022-04-29
UPDATE `app_methods` SET `default_order`=`method_id` WHERE 1;
-- Nahutech Local Test
-- Initial, GitHub
-- Teleinf Local test

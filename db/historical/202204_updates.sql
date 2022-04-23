-- Nahutech Local Test
-- 2022-04-16
ALTER TABLE `app_modules` ADD `default_order` TINYINT NOT NULL COMMENT 'Orden por defecto' AFTER `module_description`;
ALTER TABLE `app_methods` ADD `default_order` TINYINT NOT NULL COMMENT 'Orden por defecto' AFTER `method_description`;
INSERT INTO `app_themes` (`theme_id`, `theme_name`, `theme_url`) VALUES (NULL, 'Negro - Menú lateral', 'black'), (NULL, 'Verde - Menú lateral', 'green'), (NULL, 'Azul - Menú superior', 'blue_top');
UPDATE `app_themes` SET `theme_name` = 'Azul - Menú lateral' WHERE `app_themes`.`theme_id` = 1;
-- Teleinf Local test

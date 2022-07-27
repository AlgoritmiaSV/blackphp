-- 2022-07-17
ALTER TABLE `entity_methods` CHANGE `cmethod_id` `emethod_id` INT NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';
ALTER TABLE `entity_modules` CHANGE `cmodule_id` `emodule_id` INT NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';
-- 2022-07-25
-- NOT AN UPDATE
INSERT INTO `user_methods` SELECT NULL, `u`.`user_id`, `em`.`method_id`, 255, 0, now(), 0, now(), 1 FROM `entity_methods` AS `em`, `users` AS `u`, `user_modules` AS `um`, `app_methods` AS `m` WHERE `um`.`user_id` = `u`.`user_id` AND `em`.`method_id` = `m`.`method_id` AND `m`.`module_id` = `um`.`module_id` AND `em`.`entity_id` = `u`.`entity_id`;
-- /NOT AN UPDATE
-- Nahutech Local Test
-- Teleinf Local test

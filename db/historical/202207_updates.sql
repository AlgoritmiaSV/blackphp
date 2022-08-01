-- 2022-07-17
ALTER TABLE `entity_methods` CHANGE `cmethod_id` `emethod_id` INT NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';
ALTER TABLE `entity_modules` CHANGE `cmodule_id` `emodule_id` INT NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';
-- 2022-07-25
-- NOT AN UPDATE
INSERT INTO `user_methods` SELECT NULL, `u`.`user_id`, `em`.`method_id`, 255, 0, now(), 0, now(), 1 FROM `entity_methods` AS `em`, `users` AS `u`, `user_modules` AS `um`, `app_methods` AS `m` WHERE `um`.`user_id` = `u`.`user_id` AND `em`.`method_id` = `m`.`method_id` AND `m`.`module_id` = `um`.`module_id` AND `em`.`entity_id` = `u`.`entity_id`;
-- /NOT AN UPDATE
CREATE ALGORITHM = UNDEFINED VIEW `available_methods` AS select `am`.`method_id` AS `method_id`,`am`.`module_id` AS `module_id`,`am`.`method_name` AS `method_name`,`am`.`method_url` AS `method_url`,`am`.`method_icon` AS `method_icon`,`am`.`method_description` AS `method_description`,`am`.`default_order` AS `default_order`,`am`.`status` AS `status`,`im`.`method_order` AS `method_order`,`am`.`method_id` AS `id`,`am`.`method_name` AS `label`,`im`.`entity_id`,`um`.`user_id` from `app_methods` `am` join `user_methods` `um` join `entity_methods` `im` where `um`.`method_id` = `am`.`method_id` and `um`.`status` = 1 and `im`.`method_id` = `am`.`method_id` and `im`.`status` = 1;
-- 2022-07-30
CREATE ALGORITHM = UNDEFINED VIEW `entity_options` AS select entity_id FROM entities;
CREATE ALGORITHM = UNDEFINED VIEW `available_modules` AS select `m`.*,`um`.`access_type`,`em`.`entity_id`,`u`.`user_id` from `entity_modules` `em` join `app_modules` `m` join `user_modules` `um` join `users` `u` where `m`.`module_id` = `em`.`module_id` and `em`.`status` = 1 and `um`.`module_id` = `m`.`module_id` and `um`.`status` = 1 and `u`.`entity_id` = `em`.`entity_id` AND `u`.`user_id` = `um`.`user_id`;
ALTER ALGORITHM = UNDEFINED VIEW `available_methods` AS SELECT `am`.*,`im`.`method_order`,`am`.`method_id` AS `id`,`am`.`method_name` AS `label`,`im`.`entity_id`,`um`.`user_id` from `app_methods` `am` join `user_methods` `um` join `entity_methods` `im` join `users` `u` where `um`.`method_id` = `am`.`method_id` and `um`.`status` = 1 and `im`.`method_id` = `am`.`method_id` and `im`.`status` = 1 AND `u`.`entity_id` = `im`.`entity_id` AND `u`.`user_id` = `um`.`user_id`;
-- Nahutech Local Test
-- Teleinf Local test

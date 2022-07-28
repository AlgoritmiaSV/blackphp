-- 2022-07-17
ALTER TABLE `entity_methods` CHANGE `cmethod_id` `emethod_id` INT NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';
ALTER TABLE `entity_modules` CHANGE `cmodule_id` `emodule_id` INT NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';
-- 2022-07-25
-- NOT AN UPDATE
INSERT INTO `user_methods` SELECT NULL, `u`.`user_id`, `em`.`method_id`, 255, 0, now(), 0, now(), 1 FROM `entity_methods` AS `em`, `users` AS `u`, `user_modules` AS `um`, `app_methods` AS `m` WHERE `um`.`user_id` = `u`.`user_id` AND `em`.`method_id` = `m`.`method_id` AND `m`.`module_id` = `um`.`module_id` AND `em`.`entity_id` = `u`.`entity_id`;
-- /NOT AN UPDATE
CREATE ALGORITHM = UNDEFINED VIEW `available_methods` AS select `am`.`method_id` AS `method_id`,`am`.`module_id` AS `module_id`,`am`.`method_name` AS `method_name`,`am`.`method_url` AS `method_url`,`am`.`method_icon` AS `method_icon`,`am`.`method_description` AS `method_description`,`am`.`default_order` AS `default_order`,`am`.`status` AS `status`,`im`.`method_order` AS `method_order`,`am`.`method_id` AS `id`,`am`.`method_name` AS `label`,`im`.`entity_id`,`um`.`user_id` from `app_methods` `am` join `user_methods` `um` join `entity_methods` `im` where `um`.`method_id` = `am`.`method_id` and `um`.`status` = 1 and `im`.`method_id` = `am`.`method_id` and `im`.`status` = 1;
-- Nahutech Local Test
-- Teleinf Local test

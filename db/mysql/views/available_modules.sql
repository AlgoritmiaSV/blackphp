DROP VIEW IF EXISTS `available_modules`;
CREATE VIEW `available_modules` AS
SELECT `m`.*,
	`em`.`entity_id`,
	`r`.`role_id`,
	`em`.`module_order`
FROM `entity_modules` `em`
	LEFT JOIN `app_modules` AS `m` ON `m`.`module_id` = `em`.`module_id`
	LEFT JOIN `role_modules` AS `rm` ON `rm`.`module_id` = `m`.`module_id` AND `rm`.`status` = 1
	LEFT JOIN `roles` AS `r` ON `r`.`entity_id` = `em`.`entity_id` AND `r`.`role_id` = `rm`.`role_id`
WHERE `em`.`status` = 1
ORDER BY `module_order`;

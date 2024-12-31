DROP VIEW IF EXISTS `available_methods`;
CREATE VIEW `available_methods` AS
SELECT `am`.*,
	`im`.`method_order`,
	`am`.`method_id` AS `id`,
	`am`.`method_name` AS `label`,
	`im`.`entity_id`,
	`rm`.`role_id`
FROM `role_methods` `rm`
	LEFT JOIN `app_methods` AS `am` ON `rm`.`method_id` = `am`.`method_id`
	LEFT JOIN `roles` AS `r` ON `r`.`role_id` = `rm`.`role_id`
	LEFT JOIN `entity_methods` AS `im` ON `im`.`method_id` = `am`.`method_id` AND `r`.`entity_id` = `im`.`entity_id`
WHERE `rm`.`status` = 1
	AND `im`.`status` = 1;

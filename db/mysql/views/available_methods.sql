CREATE VIEW `available_methods` AS
SELECT `am`.*,
	`im`.`method_order`,
	`am`.`method_id` AS `id`,
	`am`.`method_name` AS `label`,
	`im`.`entity_id`,
	`um`.`user_id`
FROM `user_methods` `um`
	LEFT JOIN `app_methods` `am` ON `um`.`method_id` = `am`.`method_id`
	LEFT JOIN `users` `u` ON `u`.`user_id` = `um`.`user_id`
	LEFT JOIN `entity_methods` `im` ON `im`.`method_id` = `am`.`method_id` AND `u`.`entity_id` = `im`.`entity_id`
WHERE `um`.`status` = 1
	AND `im`.`status` = 1;
ALTER TABLE `app_modules` DROP `module_key`;

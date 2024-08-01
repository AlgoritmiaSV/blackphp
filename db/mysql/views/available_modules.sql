CREATE VIEW `available_modules` AS
SELECT `m`.*,
	`em`.`entity_id`,
	`u`.`user_id`,
	`em`.`module_order`
FROM `entity_modules` `em`
	LEFT JOIN `app_modules` `m` ON `m`.`module_id` = `em`.`module_id`
	LEFT JOIN `user_modules` `um` ON `um`.`module_id` = `m`.`module_id` AND `um`.`status` = 1
	LEFT JOIN `users` `u` ON `u`.`entity_id` = `em`.`entity_id` AND `u`.`user_id` = `um`.`user_id`
WHERE `em`.`status` = 1
ORDER BY `module_order`;

CREATE VIEW `user_data` AS
SELECT `u`.*,
	`ls`.`last_login`,
	`r`.`role_name`
FROM `users` AS `u`
LEFT JOIN `roles` AS `r` ON `u`.`role_id` = `r`.`role_id`
LEFT JOIN (
	SELECT `user_id`, MAX(`date_time`) AS `last_login`
	FROM `user_sessions`
	GROUP BY `user_id`) AS `ls` ON `ls`.`user_id` = `u`.`user_id`
WHERE `u`.`status` = 1;

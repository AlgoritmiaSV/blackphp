DROP TRIGGER IF EXISTS `EntityAfterInsert`;
DELIMITER $$
CREATE TRIGGER `EntityAfterInsert`
AFTER INSERT ON `entities`
FOR EACH ROW
INSERT INTO `entity_options`
SELECT NULL,
	NEW.`entity_id`,
	`option_id`,
	`default_value`,
	0,
	now()
FROM `app_options` $$
DELIMITER ;

DROP TRIGGER IF EXISTS `AppOptionAfterInsert`;
DELIMITER $$
CREATE TRIGGER `AppOptionAfterInsert`
AFTER INSERT ON `app_options`
FOR EACH ROW
INSERT INTO `entity_options`
SELECT NULL,
	`entity_id`,
	NEW.`option_id`,
	NEW.`default_value`,
	0,
	NOW()
FROM `entities` $$
DELIMITER ;

-- Main catalogs
TRUNCATE TABLE `app_catalogs`;
INSERT INTO `app_catalogs`(`table_name`, `field_name`, `field_value`, `description`)
SELECT `c`.`TABLE_NAME`, `c`.`COLUMN_NAME`, `v`.* FROM `COLUMNS` AS `c`
	LEFT JOIN `TABLES` AS `t` ON `t`.`TABLE_SCHEMA` = `c`.`TABLE_SCHEMA` AND `t`.`TABLE_NAME` = `c`.`TABLE_NAME`
	LEFT JOIN (
		SELECT 0 AS `value`, 'Deleted' AS `text`
		UNION
		SELECT 1 AS `value`, 'Active' AS `text`
	) AS `v` ON 1
WHERE `c`.`TABLE_SCHEMA` LIKE 'negkit'
	AND `c`.`COLUMN_NAME` LIKE 'status'
	AND `t`.`TABLE_TYPE` LIKE 'BASE TABLE';
-- Additional catalogs
INSERT INTO `app_catalogs` VALUES ('users','status',2,'Locked') ON DUPLICATE KEY UPDATE `description` = 'Locked';

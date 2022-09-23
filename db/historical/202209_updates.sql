-- 2022-09-22
ALTER TABLE `app_modules` ADD `module_icon` VARCHAR(32) NOT NULL COMMENT 'Ícono del módulo en el menú' AFTER `module_url`;
UPDATE `app_modules` SET `module_icon` = LOWER(`module_url`);
DROP VIEW available_modules;
CREATE VIEW available_modules AS
SELECT m.*,
	um.access_type,
	em.entity_id,
	u.user_id,
	em.module_order
FROM entity_modules em,
	app_modules m,
	user_modules um,
	users u
WHERE m.module_id = em.module_id
	AND em.status = 1
	AND um.module_id = m.module_id
	AND um.status = 1
	AND u.entity_id = em.entity_id
	AND u.user_id = um.user_id;
-- Nahutech
-- Teleinf

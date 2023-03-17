-- Actualizaciones de base de datos en el mes de marzo de 2023
-- Por: Edwin Fajardo
-- 2023-03-16
ALTER TABLE `app_elements` ADD `is_creatable` TINYINT NOT NULL COMMENT 'Se pueden crear nuevos elementos' AFTER `method_name`, ADD `is_updatable` TINYINT NOT NULL COMMENT 'Se pueden modificar los elementos' AFTER `is_creatable`;
ALTER TABLE `app_elements` CHANGE `deletable` `is_deletable` TINYINT NOT NULL COMMENT 'Se pueden eliminar los elementos';
UPDATE `app_elements` SET `is_updatable` = '1' WHERE `app_elements`.`element_id` = 1;
UPDATE `app_elements` SET `is_creatable` = '1', `is_updatable` = '1' WHERE `app_elements`.`element_id` = 2;
UPDATE `app_elements` SET `is_updatable` = '1' WHERE `app_elements`.`element_id` = 3;
ALTER TABLE `app_methods` ADD `element_id` TINYINT NULL COMMENT 'Elemento al que requiere permisos' AFTER `default_order`, ADD `permissions` TINYINT NOT NULL COMMENT 'Tipo de permisos requeridos' AFTER `element_id`;
-- Estructura para los roles
CREATE TABLE `roles` (`role_id` INT NOT NULL AUTO_INCREMENT COMMENT 'Llave primaria' , `entity_id` INT NOT NULL COMMENT 'ID de la entidad' , `role_name` VARCHAR(64) NOT NULL COMMENT 'Nombre del rol' , `creation_user` INT NOT NULL , `creation_time` DATETIME NOT NULL , `edition_user` INT NOT NULL , `edition_time` DATETIME NOT NULL , `status` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`role_id`)) ENGINE = InnoDB COMMENT = 'Roles para configuración de permisos';
INSERT INTO `app_elements` (`element_id`, `element_key`, `element_name`, `singular_name`, `element_gender`, `unique_element`, `module_id`, `method_name`, `is_creatable`, `is_updatable`, `is_deletable`, `table_name`) VALUES (NULL, 'roles', 'Roles', 'role', 'M', '0', '1', 'RoleDetails', '1', '1', '1', 'roles');
CREATE TABLE `role_elements` (`role_element_id` INT NOT NULL AUTO_INCREMENT COMMENT 'Llave primaria' , `role_id` INT NOT NULL COMMENT 'ID del rol' , `element_id` INT NOT NULL COMMENT 'ID del elemento' , `permissions` TINYINT NOT NULL DEFAULT '8' COMMENT 'Permisos (Leer, crear, editar, eliminar)' , `creation_user` INT NOT NULL , `creation_time` INT NOT NULL , `edition_user` INT NOT NULL , `edition_time` INT NOT NULL , `status` TINYINT NOT NULL DEFAULT '1' , PRIMARY KEY (`role_element_id`)) ENGINE = InnoDB COMMENT = 'Cada uno de los permisos de un rol';
ALTER TABLE `role_elements` CHANGE `element_id` `element_id` SMALLINT NOT NULL COMMENT 'ID del elemento';
ALTER TABLE `role_elements` ADD CONSTRAINT `role_element_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `role_elements` ADD CONSTRAINT `role_element_element` FOREIGN KEY (`element_id`) REFERENCES `app_elements`(`element_id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `users` ADD `role_id` INT NULL COMMENT 'ID del rol' AFTER `locale`;
ALTER TABLE `users` ADD CONSTRAINT `user_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `roles` ADD CONSTRAINT `role_entity` FOREIGN KEY (`entity_id`) REFERENCES `entities`(`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;
-- Fin de estructura para roles
-- inabve:blackphp
-- teleinf:blackphp

-- MariaDB dump 10.19  Distrib 10.11.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: blackphp
-- ------------------------------------------------------
-- Server version	10.11.6-MariaDB-0+deb12u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `app_elements`
--

DROP TABLE IF EXISTS `app_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_elements` (
  `element_id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `element_key` varchar(32) NOT NULL COMMENT 'Clave del elemento',
  `element_name` varchar(32) NOT NULL COMMENT 'Nombre del elemento',
  `singular_name` varchar(32) NOT NULL COMMENT 'Nombre singular del elemento',
  `element_gender` char(1) NOT NULL COMMENT 'M: Masculino, F: Femenino',
  `unique_element` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Es un elemento único',
  `module_id` int(11) NOT NULL COMMENT 'ID del módulo',
  `method_name` varchar(32) NOT NULL COMMENT 'Nombre del método para ver detalle',
  `is_creatable` tinyint(4) NOT NULL COMMENT 'Se pueden crear nuevos elementos',
  `is_updatable` tinyint(4) NOT NULL COMMENT 'Se pueden modificar los elementos',
  `is_deletable` tinyint(4) NOT NULL COMMENT 'Se pueden eliminar los elementos',
  `table_name` varchar(64) NOT NULL COMMENT 'Nombre de la tabla',
  PRIMARY KEY (`element_id`),
  UNIQUE KEY `element_key` (`element_key`),
  KEY `element_method` (`module_id`),
  CONSTRAINT `element_module` FOREIGN KEY (`module_id`) REFERENCES `app_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Elementos de la aplicación para actividad del usuario';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `app_installers`
--

DROP TABLE IF EXISTS `app_installers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_installers` (
  `installer_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `installer_nickname` varchar(32) NOT NULL COMMENT 'Usuario',
  `installer_password` char(60) NOT NULL COMMENT 'Resumen de contraseña',
  `installer_name` varchar(128) NOT NULL COMMENT 'Nombre del instalador',
  `creation_time` datetime NOT NULL COMMENT 'Hora y fecha de creación',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Eliminado, inactivo, activo',
  PRIMARY KEY (`installer_id`),
  UNIQUE KEY `unique_nickname` (`installer_nickname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Instaladores del sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `app_locales`
--

DROP TABLE IF EXISTS `app_locales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_locales` (
  `locale_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Llave primaria de idioma regional',
  `language_code` char(2) NOT NULL COMMENT 'Código de idioma en ISO 639-1',
  `locale_code` char(5) NOT NULL COMMENT 'Código de idioma regional',
  `locale_name` varchar(32) NOT NULL COMMENT 'Nombre del idioma',
  PRIMARY KEY (`locale_id`),
  UNIQUE KEY `locale_code` (`locale_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Idiomas regionales soportados por la aplicación';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `app_methods`
--

DROP TABLE IF EXISTS `app_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_methods` (
  `method_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `module_id` int(11) NOT NULL COMMENT 'ID del módulo',
  `method_name` varchar(32) NOT NULL COMMENT 'Nombre del método',
  `method_url` varchar(32) NOT NULL COMMENT 'URL del método (Nombre de la función PHP)',
  `method_icon` varchar(32) NOT NULL COMMENT 'Ícono del método en el menú',
  `default_order` tinyint(4) NOT NULL COMMENT 'Orden por defecto',
  `element_id` smallint(6) DEFAULT NULL COMMENT 'Elemento al que requiere permisos',
  `permissions` tinyint(4) NOT NULL COMMENT 'Tipo de permisos requeridos',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Estado 0:inactivo, 1:activo',
  PRIMARY KEY (`method_id`),
  KEY `module_id` (`module_id`),
  KEY `method_element` (`element_id`),
  CONSTRAINT `method_element` FOREIGN KEY (`element_id`) REFERENCES `app_elements` (`element_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `method_module` FOREIGN KEY (`module_id`) REFERENCES `app_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Todos los métodos disponibles en el sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `app_modules`
--

DROP TABLE IF EXISTS `app_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_modules` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `module_name` varchar(32) NOT NULL COMMENT 'Nombre del módulo',
  `module_url` varchar(32) NOT NULL COMMENT 'URL del módulo',
  `module_icon` varchar(32) NOT NULL COMMENT 'Ícono del módulo en el menú',
  `default_order` tinyint(4) NOT NULL COMMENT 'Orden por defecto',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Estado 0:inactivo, 1:activo',
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Todos los módulos disponibles en el sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `app_option_values`
--

DROP TABLE IF EXISTS `app_option_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_option_values` (
  `option_value_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Llave primaria',
  `option_id` int(11) NOT NULL COMMENT 'ID de la opción',
  `value_key` varchar(32) NOT NULL COMMENT 'Clave del valor',
  `value_label` tinytext NOT NULL COMMENT 'Etiqueta del valor',
  PRIMARY KEY (`option_value_id`),
  UNIQUE KEY `unique_option_value_key` (`option_id`,`value_key`),
  CONSTRAINT `option_value_option` FOREIGN KEY (`option_id`) REFERENCES `app_options` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Valores posibles para selectores en las preferencias';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `app_options`
--

DROP TABLE IF EXISTS `app_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Llave primaria',
  `option_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Tipo de variable: 1: Booleana; 2: Valor',
  `option_key` varchar(32) NOT NULL COMMENT 'Clave de la opción',
  `option_description` tinytext NOT NULL COMMENT 'Descripción de la opción',
  `module_id` int(11) DEFAULT NULL COMMENT 'Módulo en el que se realiza la configuración',
  `default_value` varchar(255) NOT NULL COMMENT 'Valor por defecto de la opción',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `unique_key_module` (`option_key`,`module_id`),
  KEY `option_module` (`module_id`),
  CONSTRAINT `option_module` FOREIGN KEY (`module_id`) REFERENCES `app_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Opciones de la aplicación, configurables por entidad';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `AppOptionAfterInsert` AFTER INSERT ON `app_options` FOR EACH ROW INSERT INTO entity_options SELECT NULL, entity_id, NEW.option_id, NEW.default_value, 0, NOW(), 0, NOW(), 1 FROM entities */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `app_themes`
--

DROP TABLE IF EXISTS `app_themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_themes` (
  `theme_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `theme_name` varchar(32) NOT NULL COMMENT 'Nombre del tema',
  `theme_url` varchar(16) NOT NULL COMMENT 'Nombre de la carpeta pública',
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Temas (estilos) del sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `available_methods`
--

DROP TABLE IF EXISTS `available_methods`;
/*!50001 DROP VIEW IF EXISTS `available_methods`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `available_methods` AS SELECT
 1 AS `method_id`,
  1 AS `module_id`,
  1 AS `method_name`,
  1 AS `method_url`,
  1 AS `method_icon`,
  1 AS `default_order`,
  1 AS `element_id`,
  1 AS `permissions`,
  1 AS `status`,
  1 AS `method_order`,
  1 AS `id`,
  1 AS `label`,
  1 AS `entity_id`,
  1 AS `user_id` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `available_modules`
--

DROP TABLE IF EXISTS `available_modules`;
/*!50001 DROP VIEW IF EXISTS `available_modules`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `available_modules` AS SELECT
 1 AS `module_id`,
  1 AS `module_name`,
  1 AS `module_url`,
  1 AS `module_icon`,
  1 AS `default_order`,
  1 AS `status`,
  1 AS `entity_id`,
  1 AS `user_id`,
  1 AS `module_order` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `browsers`
--

DROP TABLE IF EXISTS `browsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `browsers` (
  `browser_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `user_agent` varchar(255) NOT NULL COMMENT 'Cadena completa User Agent enviada por el navegador',
  `browser_name` varchar(16) NOT NULL COMMENT 'Nombre del navegador',
  `browser_version` varchar(16) NOT NULL COMMENT 'Versión del navegador',
  `platform` varchar(16) NOT NULL COMMENT 'Sistema operativo',
  `creation_user` int(11) NOT NULL COMMENT 'Primer usuario que lo registra',
  `creation_time` datetime NOT NULL COMMENT 'Hora y fecha de registro',
  PRIMARY KEY (`browser_id`),
  UNIQUE KEY `user_agent` (`user_agent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Navegadores con los que se ha accedido';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entities`
--

DROP TABLE IF EXISTS `entities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `entity_name` varchar(64) NOT NULL COMMENT 'Nombre de la empresa',
  `entity_slogan` varchar(128) NOT NULL COMMENT 'Eslogan de la empresa',
  `admin_role` int(11) DEFAULT NULL COMMENT 'Rol administrador',
  `admin_user` int(11) DEFAULT NULL COMMENT 'Usuario principal (Superadministrador)',
  `entity_date` date NOT NULL COMMENT 'Fecha actual de operaciones (En caso que difiera del sistema)',
  `entity_begin` date NOT NULL COMMENT 'Fecha de inicio de las operaciones',
  `entity_subdomain` varchar(32) DEFAULT NULL COMMENT 'Subdominio (Para funcionamiento en línea)',
  `app_name` varchar(32) NOT NULL DEFAULT 'BlackPHP' COMMENT 'Nombre de la App para instalación como PWA',
  `default_locale` char(5) DEFAULT NULL COMMENT 'Idioma por defecto de la entidad',
  `creation_installer` int(11) DEFAULT NULL COMMENT 'ID del usuario que instaló el sistema',
  `creation_time` datetime NOT NULL,
  `edition_installer` int(11) DEFAULT NULL,
  `installer_edition_time` datetime NOT NULL,
  `edition_user` int(11) DEFAULT NULL,
  `user_edition_time` datetime DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `comp_subdomain` (`entity_subdomain`),
  KEY `company_creator` (`creation_installer`),
  KEY `company_editor` (`edition_installer`),
  KEY `entity_role` (`admin_role`),
  CONSTRAINT `entity_installer_creator` FOREIGN KEY (`creation_installer`) REFERENCES `app_installers` (`installer_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `entity_installer_editor` FOREIGN KEY (`edition_installer`) REFERENCES `app_installers` (`installer_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `entity_role` FOREIGN KEY (`admin_role`) REFERENCES `roles` (`role_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Negocios, empresas y compañías que utilizarán el sistema';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 */ /*!50003 TRIGGER `EntityAfterInsert` AFTER INSERT ON `entities` FOR EACH ROW INSERT INTO entity_options SELECT NULL, NEW.entity_id, option_id, default_value, 0, now(), 0, now(), 1 FROM app_options */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `entity_methods`
--

DROP TABLE IF EXISTS `entity_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entity_methods` (
  `emethod_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `entity_id` int(11) NOT NULL COMMENT 'ID de la empresa',
  `method_id` int(11) NOT NULL COMMENT 'ID del método',
  `method_order` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Orden en el que aoparecerá el método en el menú',
  `creation_time` datetime NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`emethod_id`),
  UNIQUE KEY `comp_method` (`entity_id`,`method_id`),
  KEY `method_id` (`method_id`),
  CONSTRAINT `cmethod_company` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cmethod_method` FOREIGN KEY (`method_id`) REFERENCES `app_methods` (`method_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Métodos habilitados para cada empresa';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entity_modules`
--

DROP TABLE IF EXISTS `entity_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entity_modules` (
  `emodule_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `entity_id` int(11) NOT NULL COMMENT 'ID de la empresa',
  `module_id` int(11) NOT NULL COMMENT 'ID del módulo',
  `module_order` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Ubicación del módulo en el menú',
  `creation_time` datetime NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`emodule_id`),
  KEY `comp_id` (`entity_id`),
  KEY `module_id` (`module_id`),
  CONSTRAINT `cmodule_company` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cmodule_module` FOREIGN KEY (`module_id`) REFERENCES `app_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Módulos habilitados para cada empresa';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entity_options`
--

DROP TABLE IF EXISTS `entity_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entity_options` (
  `eoption_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Llave primaria',
  `entity_id` int(11) NOT NULL COMMENT 'ID de la entidad',
  `option_id` int(11) NOT NULL COMMENT 'ID de la opción',
  `option_value` varchar(255) NOT NULL COMMENT 'Valor de la opción',
  `creation_user` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  `edition_user` int(11) NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`eoption_id`),
  KEY `eoption_entity` (`entity_id`),
  KEY `eoption_option` (`option_id`),
  CONSTRAINT `eoption_entity` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `eoption_option` FOREIGN KEY (`option_id`) REFERENCES `app_options` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Valores configurados en cada entidad';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login_attemps`
--

DROP TABLE IF EXISTS `login_attemps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attemps` (
  `attemp_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Llave primaria',
  `user_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `date_time` datetime NOT NULL COMMENT 'Hora y fecha',
  `browser_id` int(11) NOT NULL COMMENT 'ID del navegador',
  `ip_address` varchar(15) NOT NULL COMMENT 'Dirección IP',
  PRIMARY KEY (`attemp_id`),
  KEY `login_attemps_user` (`user_id`),
  CONSTRAINT `login_attemps_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Intentos fallidos de inicio de sesión';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_elements`
--

DROP TABLE IF EXISTS `role_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_elements` (
  `role_element_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Llave primaria',
  `role_id` int(11) NOT NULL COMMENT 'ID del rol',
  `element_id` smallint(6) NOT NULL COMMENT 'ID del elemento',
  `permissions` tinyint(4) NOT NULL DEFAULT 8 COMMENT 'Permisos (Leer, crear, editar, eliminar)',
  `creation_user` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  `edition_user` int(11) NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`role_element_id`),
  UNIQUE KEY `unique_role_element` (`role_id`,`element_id`),
  KEY `role_element_element` (`element_id`),
  CONSTRAINT `role_element_element` FOREIGN KEY (`element_id`) REFERENCES `app_elements` (`element_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role_element_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Cada uno de los permisos de un rol';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Llave primaria',
  `entity_id` int(11) NOT NULL COMMENT 'ID de la entidad',
  `role_name` varchar(64) NOT NULL COMMENT 'Nombre del rol',
  `creation_user` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  `edition_user` int(11) NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`role_id`),
  KEY `role_entity` (`entity_id`),
  CONSTRAINT `role_entity` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Roles para configuración de permisos';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `user_data`
--

DROP TABLE IF EXISTS `user_data`;
/*!50001 DROP VIEW IF EXISTS `user_data`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `user_data` AS SELECT
 1 AS `user_id`,
  1 AS `entity_id`,
  1 AS `user_name`,
  1 AS `nickname`,
  1 AS `email`,
  1 AS `password`,
  1 AS `password_hash`,
  1 AS `theme_id`,
  1 AS `locale`,
  1 AS `role_id`,
  1 AS `creation_user`,
  1 AS `creation_time`,
  1 AS `edition_user`,
  1 AS `edition_time`,
  1 AS `status`,
  1 AS `last_login`,
  1 AS `role_name` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user_logs`
--

DROP TABLE IF EXISTS `user_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `entity_id` int(11) NOT NULL COMMENT 'ID de la entidad',
  `user_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `element_id` smallint(6) NOT NULL COMMENT 'ID del tipo de elemento',
  `action_id` tinyint(4) NOT NULL COMMENT 'Acción realizada',
  `date_time` datetime NOT NULL COMMENT 'Hora y fecha',
  `element_link` int(11) DEFAULT NULL COMMENT 'Enlace al elemento en cuestión',
  PRIMARY KEY (`log_id`),
  KEY `log_user` (`user_id`),
  KEY `log_element` (`element_id`),
  KEY `log_action` (`action_id`),
  KEY `log_entity` (`entity_id`),
  CONSTRAINT `log_element` FOREIGN KEY (`element_id`) REFERENCES `app_elements` (`element_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_entity` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Registro de actividades del usuario';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_methods`
--

DROP TABLE IF EXISTS `user_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_methods` (
  `umethod_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `user_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `method_id` int(11) NOT NULL COMMENT 'ID del método',
  `creation_user` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  `edition_user` int(11) NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`umethod_id`),
  UNIQUE KEY `unique_user_method` (`user_id`,`method_id`),
  KEY `umethod_method` (`method_id`),
  KEY `umethod_user` (`user_id`),
  CONSTRAINT `umethod_method` FOREIGN KEY (`method_id`) REFERENCES `app_methods` (`method_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `umethod_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Acceso de los usuarios a los métodos de la aplicación';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_modules`
--

DROP TABLE IF EXISTS `user_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_modules` (
  `umodule_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `module_id` int(11) NOT NULL COMMENT 'ID del módulo',
  `user_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `creation_user` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  `edition_user` int(11) NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`umodule_id`),
  UNIQUE KEY `unique_user_module` (`module_id`,`user_id`),
  KEY `module_id` (`module_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `umodule_module` FOREIGN KEY (`module_id`) REFERENCES `app_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `umodule_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Acceso a los usaurios por módulo';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_recovery`
--

DROP TABLE IF EXISTS `user_recovery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_recovery` (
  `urecovery_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `user_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `urecovery_code` char(32) NOT NULL COMMENT 'Código de recuperación',
  `expiration_time` datetime NOT NULL COMMENT 'Fecha y hora de vencimiento',
  `creation_user` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  `edition_user` int(11) NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`urecovery_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `urecovery_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Recuperación de cuentas de usuario';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_sessions` (
  `usession_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `user_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `branch_id` int(11) DEFAULT NULL COMMENT 'Sucursal en la que inició sesión',
  `ip_address` varchar(15) NOT NULL COMMENT 'Dirección IP desde donde se conecta',
  `browser_id` int(11) DEFAULT NULL COMMENT 'Navegador que usa',
  `date_time` datetime NOT NULL COMMENT 'Fecha y hora',
  PRIMARY KEY (`usession_id`),
  KEY `user_id` (`user_id`),
  KEY `browser_id` (`browser_id`),
  KEY `usession_branch` (`branch_id`),
  CONSTRAINT `usession_browser` FOREIGN KEY (`browser_id`) REFERENCES `browsers` (`browser_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `usession_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Registro de sesiones del usuario';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla',
  `entity_id` int(11) NOT NULL COMMENT 'ID de la empresa',
  `user_name` varchar(64) NOT NULL COMMENT 'Nombre completo del usuario',
  `nickname` varchar(32) DEFAULT NULL COMMENT 'Usuario para inicio de sesión',
  `email` varchar(64) DEFAULT NULL COMMENT 'Correo electrónico',
  `password` char(32) NOT NULL COMMENT 'Contraseña',
  `password_hash` char(60) NOT NULL COMMENT 'Hash de la contraseña',
  `theme_id` int(11) DEFAULT 1 COMMENT 'Tema de visualización del usuario',
  `locale` char(5) DEFAULT NULL COMMENT 'Idioma del usuario',
  `role_id` int(11) DEFAULT NULL COMMENT 'ID del rol',
  `creation_user` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  `edition_user` int(11) NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `entity_nickname` (`entity_id`,`nickname`,`status`) USING BTREE,
  UNIQUE KEY `entity_email` (`entity_id`,`email`,`status`) USING BTREE,
  KEY `theme_id` (`theme_id`),
  KEY `user_role` (`role_id`),
  CONSTRAINT `user_company` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_theme` FOREIGN KEY (`theme_id`) REFERENCES `app_themes` (`theme_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Usuarios';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `available_methods`
--

/*!50001 DROP VIEW IF EXISTS `available_methods`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 */
/*!50001 VIEW `available_methods` AS select `am`.`method_id` AS `method_id`,`am`.`module_id` AS `module_id`,`am`.`method_name` AS `method_name`,`am`.`method_url` AS `method_url`,`am`.`method_icon` AS `method_icon`,`am`.`default_order` AS `default_order`,`am`.`element_id` AS `element_id`,`am`.`permissions` AS `permissions`,`am`.`status` AS `status`,`im`.`method_order` AS `method_order`,`am`.`method_id` AS `id`,`am`.`method_name` AS `label`,`im`.`entity_id` AS `entity_id`,`um`.`user_id` AS `user_id` from (((`user_methods` `um` left join `app_methods` `am` on(`um`.`method_id` = `am`.`method_id`)) left join `users` `u` on(`u`.`user_id` = `um`.`user_id`)) left join `entity_methods` `im` on(`im`.`method_id` = `am`.`method_id` and `u`.`entity_id` = `im`.`entity_id`)) where `um`.`status` = 1 and `im`.`status` = 1 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `available_modules`
--

/*!50001 DROP VIEW IF EXISTS `available_modules`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 */
/*!50001 VIEW `available_modules` AS select `m`.`module_id` AS `module_id`,`m`.`module_name` AS `module_name`,`m`.`module_url` AS `module_url`,`m`.`module_icon` AS `module_icon`,`m`.`default_order` AS `default_order`,`m`.`status` AS `status`,`em`.`entity_id` AS `entity_id`,`u`.`user_id` AS `user_id`,`em`.`module_order` AS `module_order` from (((`entity_modules` `em` left join `app_modules` `m` on(`m`.`module_id` = `em`.`module_id`)) left join `user_modules` `um` on(`um`.`module_id` = `m`.`module_id` and `um`.`status` = 1)) left join `users` `u` on(`u`.`entity_id` = `em`.`entity_id` and `u`.`user_id` = `um`.`user_id`)) where `em`.`status` = 1 order by `em`.`module_order` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `user_data`
--

/*!50001 DROP VIEW IF EXISTS `user_data`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 */
/*!50001 VIEW `user_data` AS select `u`.`user_id` AS `user_id`,`u`.`entity_id` AS `entity_id`,`u`.`user_name` AS `user_name`,`u`.`nickname` AS `nickname`,`u`.`email` AS `email`,`u`.`password` AS `password`,`u`.`password_hash` AS `password_hash`,`u`.`theme_id` AS `theme_id`,`u`.`locale` AS `locale`,`u`.`role_id` AS `role_id`,`u`.`creation_user` AS `creation_user`,`u`.`creation_time` AS `creation_time`,`u`.`edition_user` AS `edition_user`,`u`.`edition_time` AS `edition_time`,`u`.`status` AS `status`,`ls`.`last_login` AS `last_login`,`r`.`role_name` AS `role_name` from ((`users` `u` left join `roles` `r` on(`u`.`role_id` = `r`.`role_id`)) left join (select `user_sessions`.`user_id` AS `user_id`,max(`user_sessions`.`date_time`) AS `last_login` from `user_sessions` group by `user_sessions`.`user_id`) `ls` on(`ls`.`user_id` = `u`.`user_id`)) where `u`.`status` = 1 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed

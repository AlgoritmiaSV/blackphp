-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 08, 2022 at 03:06 AM
-- Server version: 10.3.34-MariaDB-0+deb10u1
-- PHP Version: 8.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `blackphp`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_actions`
--

CREATE TABLE `app_actions` (
  `action_id` tinyint(4) NOT NULL COMMENT 'ID del registro',
  `action_key` varchar(16) NOT NULL COMMENT 'Clave de la acción',
  `infinitive_verb` varchar(16) NOT NULL COMMENT 'Verbo en infinitivo',
  `past_verb` varchar(16) NOT NULL COMMENT 'Verbo en pasado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Acciones a realizar sobre los diferentes elementos';

--
-- Dumping data for table `app_actions`
--

INSERT INTO `app_actions` (`action_id`, `action_key`, `infinitive_verb`, `past_verb`) VALUES
(1, 'create', 'Crear', 'Creó'),
(2, 'register', 'Registrar', 'Registró'),
(3, 'add', 'Agregar', 'Agregó'),
(4, 'made', 'Realizar', 'Realizó'),
(5, 'edit', 'Editar', 'Editó'),
(6, 'modify', 'Modificar', 'Modificó'),
(7, 'delete', 'Eliminar', 'Eliminó'),
(8, 'erase', 'Borrar', 'Borró'),
(9, 'null', 'Anular', 'Anuló');

-- --------------------------------------------------------

--
-- Table structure for table `app_elements`
--

CREATE TABLE `app_elements` (
  `element_id` smallint(6) NOT NULL COMMENT 'ID de la tabla',
  `element_key` varchar(16) NOT NULL COMMENT 'Clave del elemento',
  `element_name` varchar(32) NOT NULL COMMENT 'Nombre del elemento',
  `element_gender` char(1) NOT NULL COMMENT 'M: Masculino, F: Femenino',
  `element_number` char(1) NOT NULL COMMENT 'S: Singular, P: Plural',
  `unique_element` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Es un elemento único',
  `module_id` int(11) NOT NULL COMMENT 'ID del módulo',
  `method_name` varchar(32) NOT NULL COMMENT 'Nombre del método para ver detalle'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Elementos de la aplicación para actividad del usuario';

--
-- Dumping data for table `app_elements`
--

INSERT INTO `app_elements` (`element_id`, `element_key`, `element_name`, `element_gender`, `element_number`, `unique_element`, `module_id`, `method_name`) VALUES
(1, 'entity_data', 'Datos del negocio', 'M', 'P', 1, 1, 'Datos'),
(2, 'user', 'Usuario', 'M', 'S', 0, 1, 'DetalleUsuario'),
(3, 'preferences', 'Preferencias', 'F', 'P', 1, 1, 'Preferencias');

-- --------------------------------------------------------

--
-- Table structure for table `app_installers`
--

CREATE TABLE `app_installers` (
  `installer_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `installer_nickname` varchar(32) NOT NULL COMMENT 'Usuario',
  `installer_password` char(32) NOT NULL COMMENT 'Resumen de contraseña',
  `installer_name` varchar(128) NOT NULL COMMENT 'Nombre del instalador',
  `installer_phone` varchar(16) NOT NULL COMMENT 'Teléfono',
  `installer_email` varchar(64) NOT NULL COMMENT 'Correo electrónico',
  `creation_time` datetime NOT NULL COMMENT 'Hora y fecha de creación',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Eliminado, inactivo, activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Instaladores del sistema';

--
-- Dumping data for table `app_installers`
--

INSERT INTO `app_installers` (`installer_id`, `installer_nickname`, `installer_password`, `installer_name`, `installer_phone`, `installer_email`, `creation_time`, `status`) VALUES
(1, 'fajardo', 'a7c88c95c93cd74525a3a434930526a5', 'Edwin Fajardo', '77197466', 'contacto@edwinfajardo.com', '2022-02-06 09:24:41', 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_methods`
--

CREATE TABLE `app_methods` (
  `method_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `module_id` int(11) NOT NULL COMMENT 'ID del módulo',
  `method_name` varchar(32) NOT NULL COMMENT 'Nombre del método',
  `method_url` varchar(32) NOT NULL COMMENT 'URL del método (Nombre de la función PHP)',
  `method_icon` varchar(32) NOT NULL COMMENT 'Ícono del método en el menú',
  `method_description` tinytext NOT NULL COMMENT 'Descripción del método',
  `default_order` tinyint(4) NOT NULL COMMENT 'Orden por defecto',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Estado 0:inactivo, 1:activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Todos los métodos disponibles en el sistema';

--
-- Dumping data for table `app_methods`
--

INSERT INTO `app_methods` (`method_id`, `module_id`, `method_name`, `method_url`, `method_icon`, `method_description`, `default_order`, `status`) VALUES
(1, 1, 'Entity', 'Entity', 'store_info', 'Allows you to configure the general information of the business/company', 1, 1),
(2, 1, 'Users', 'Users', 'manage_users', 'Allows you to manage users and permissions for each user', 2, 1),
(3, 1, 'Preferences', 'Preferences', 'preferences', 'Allows to set and modify optional system parameters in the company', 3, 1),
(4, 1, 'About BlackPHP', 'About', 'info', 'Shows system information: Version, contact and technical support', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_modules`
--

CREATE TABLE `app_modules` (
  `module_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `module_name` varchar(32) NOT NULL COMMENT 'Nombre del módulo',
  `module_url` varchar(32) NOT NULL COMMENT 'URL del módulo',
  `module_key` char(1) NOT NULL COMMENT 'Tecla de acceso rápido',
  `module_html` varchar(32) NOT NULL COMMENT 'Nombre en formato HTML',
  `module_description` tinytext NOT NULL COMMENT 'Descripción del módulo',
  `default_order` tinyint(4) NOT NULL COMMENT 'Orden por defecto',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Estado 0:inactivo, 1:activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Todos los módulos disponibles en el sistema';

--
-- Dumping data for table `app_modules`
--

INSERT INTO `app_modules` (`module_id`, `module_name`, `module_url`, `module_key`, `module_html`, `module_description`, `default_order`, `status`) VALUES
(1, 'Settings', 'Settings', 'A', 'Settings', 'Settings', 127, 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_themes`
--

CREATE TABLE `app_themes` (
  `theme_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `theme_name` varchar(32) NOT NULL COMMENT 'Nombre del tema',
  `theme_url` varchar(16) NOT NULL COMMENT 'Nombre de la carpeta pública'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Temas (estilos) del sistema';

--
-- Dumping data for table `app_themes`
--

INSERT INTO `app_themes` (`theme_id`, `theme_name`, `theme_url`) VALUES
(1, 'Blue - Lateral menu', 'blackphp'),
(2, 'Black - Lateral menu', 'black'),
(3, 'Green - Lateral menu', 'green'),
(4, 'Blue - Top menu', 'blue_top');

-- --------------------------------------------------------

--
-- Table structure for table `browsers`
--

CREATE TABLE `browsers` (
  `browser_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `user_agent` varchar(255) NOT NULL COMMENT 'Cadena completa User Agent enviada por el navegador',
  `browser_name` varchar(16) NOT NULL COMMENT 'Nombre del navegador',
  `browser_version` varchar(16) NOT NULL COMMENT 'Versión del navegador',
  `platform` varchar(16) NOT NULL COMMENT 'Sistema operativo',
  `creation_user` int(11) NOT NULL COMMENT 'Primer usuario que lo registra',
  `creation_time` datetime NOT NULL COMMENT 'Hora y fecha de registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Navegadores con los que se ha accedido';

-- --------------------------------------------------------

--
-- Table structure for table `entities`
--

CREATE TABLE `entities` (
  `entity_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `entity_name` varchar(64) NOT NULL COMMENT 'Nombre de la empresa',
  `entity_slogan` varchar(128) NOT NULL COMMENT 'Eslogan de la empresa',
  `admin_user` int(11) DEFAULT NULL COMMENT 'Usuario principal (Superadministrador)',
  `entity_date` date NOT NULL COMMENT 'Fecha actual de operaciones (En caso que difiera del sistema)',
  `entity_begin` date NOT NULL COMMENT 'Fecha de inicio de las operaciones',
  `entity_subdomain` varchar(32) DEFAULT NULL COMMENT 'Subdominio (Para funcionamiento en línea)',
  `sys_name` varchar(32) NOT NULL COMMENT 'Nombre de la distribución del sistema',
  `creation_installer` int(11) DEFAULT NULL COMMENT 'ID del usuario que instaló el sistema',
  `creation_time` datetime NOT NULL,
  `edition_installer` int(11) DEFAULT NULL,
  `installer_edition_time` datetime NOT NULL,
  `edition_user` int(11) DEFAULT NULL,
  `user_edition_time` datetime DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Negocios, empresas y compañías que utilizarán el sistema';

-- --------------------------------------------------------

--
-- Table structure for table `entity_methods`
--

CREATE TABLE `entity_methods` (
  `cmethod_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `entity_id` int(11) NOT NULL COMMENT 'ID de la empresa',
  `method_id` int(11) NOT NULL COMMENT 'ID del método',
  `method_order` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Orden en el que aoparecerá el método en el menú',
  `creation_time` datetime NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Métodos habilitados para cada empresa';

-- --------------------------------------------------------

--
-- Table structure for table `entity_modules`
--

CREATE TABLE `entity_modules` (
  `cmodule_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `entity_id` int(11) NOT NULL COMMENT 'ID de la empresa',
  `module_id` int(11) NOT NULL COMMENT 'ID del módulo',
  `module_order` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Ubicación del módulo en el menú',
  `creation_time` datetime NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Módulos habilitados para cada empresa';

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `entity_id` int(11) NOT NULL COMMENT 'ID de la empresa',
  `user_name` varchar(64) NOT NULL COMMENT 'Nombre completo del usuario',
  `nickname` varchar(32) DEFAULT NULL COMMENT 'Usuario para inicio de sesión',
  `email` varchar(64) DEFAULT NULL COMMENT 'Correo electrónico',
  `password` char(32) NOT NULL COMMENT 'Contraseña',
  `theme_id` int(11) DEFAULT 1 COMMENT 'Tema de visualización del usuario',
  `creation_user` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  `edition_user` int(11) NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Usuarios';

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `log_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `user_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `element_id` smallint(6) NOT NULL COMMENT 'ID del tipo de elemento',
  `action_id` tinyint(4) NOT NULL COMMENT 'Acción realizada',
  `date_time` datetime NOT NULL COMMENT 'Hora y fecha',
  `element_link` int(11) NOT NULL COMMENT 'Enlace al elemento en cuestión'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Registro de actividades del usuario';

-- --------------------------------------------------------

--
-- Table structure for table `user_methods`
--

CREATE TABLE `user_methods` (
  `umethod_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `user_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `method_id` int(11) NOT NULL COMMENT 'ID del método',
  `access_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 255 COMMENT 'Tipo de acceso',
  `creation_user` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  `edition_user` int(11) NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Acceso de los usuarios a los métodos de la aplicación';

-- --------------------------------------------------------

--
-- Table structure for table `user_modules`
--

CREATE TABLE `user_modules` (
  `umodule_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `module_id` int(11) NOT NULL COMMENT 'ID del módulo',
  `user_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `access_type` int(11) DEFAULT NULL COMMENT 'Tipo de acceso al módulo',
  `creation_user` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  `edition_user` int(11) NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Acceso a los usaurios por módulo';

-- --------------------------------------------------------

--
-- Table structure for table `user_recovery`
--

CREATE TABLE `user_recovery` (
  `urecovery_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `user_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `urecovery_code` char(32) NOT NULL COMMENT 'Código de recuperación',
  `expiration_time` datetime NOT NULL COMMENT 'Fecha y hora de vencimiento',
  `creation_user` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  `edition_user` int(11) NOT NULL,
  `edition_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Recuperación de cuentas de usuario';

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `usession_id` int(11) NOT NULL COMMENT 'ID de la tabla',
  `user_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `branch_id` int(11) DEFAULT NULL COMMENT 'Sucursal en la que inició sesión',
  `ip_address` varchar(15) NOT NULL COMMENT 'Dirección IP desde donde se conecta',
  `browser_id` int(11) DEFAULT NULL COMMENT 'Navegador que usa',
  `date_time` datetime NOT NULL COMMENT 'Fecha y hora'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Registro de sesiones del usuario';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_actions`
--
ALTER TABLE `app_actions`
  ADD PRIMARY KEY (`action_id`),
  ADD UNIQUE KEY `action_key` (`action_key`);

--
-- Indexes for table `app_elements`
--
ALTER TABLE `app_elements`
  ADD PRIMARY KEY (`element_id`),
  ADD UNIQUE KEY `element_key` (`element_key`),
  ADD KEY `element_method` (`module_id`);

--
-- Indexes for table `app_installers`
--
ALTER TABLE `app_installers`
  ADD PRIMARY KEY (`installer_id`);

--
-- Indexes for table `app_methods`
--
ALTER TABLE `app_methods`
  ADD PRIMARY KEY (`method_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `app_modules`
--
ALTER TABLE `app_modules`
  ADD PRIMARY KEY (`module_id`);

--
-- Indexes for table `app_themes`
--
ALTER TABLE `app_themes`
  ADD PRIMARY KEY (`theme_id`);

--
-- Indexes for table `browsers`
--
ALTER TABLE `browsers`
  ADD PRIMARY KEY (`browser_id`),
  ADD UNIQUE KEY `user_agent` (`user_agent`);

--
-- Indexes for table `entities`
--
ALTER TABLE `entities`
  ADD PRIMARY KEY (`entity_id`),
  ADD UNIQUE KEY `comp_subdomain` (`entity_subdomain`),
  ADD KEY `company_creator` (`creation_installer`),
  ADD KEY `company_editor` (`edition_installer`);

--
-- Indexes for table `entity_methods`
--
ALTER TABLE `entity_methods`
  ADD PRIMARY KEY (`cmethod_id`),
  ADD UNIQUE KEY `comp_method` (`entity_id`,`method_id`),
  ADD KEY `method_id` (`method_id`);

--
-- Indexes for table `entity_modules`
--
ALTER TABLE `entity_modules`
  ADD PRIMARY KEY (`cmodule_id`),
  ADD KEY `comp_id` (`entity_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `comp_nickname` (`entity_id`,`nickname`),
  ADD UNIQUE KEY `comp_email` (`entity_id`,`email`),
  ADD KEY `theme_id` (`theme_id`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `log_user` (`user_id`),
  ADD KEY `log_element` (`element_id`),
  ADD KEY `log_action` (`action_id`);

--
-- Indexes for table `user_methods`
--
ALTER TABLE `user_methods`
  ADD PRIMARY KEY (`umethod_id`),
  ADD KEY `umethod_method` (`method_id`),
  ADD KEY `umethod_user` (`user_id`);

--
-- Indexes for table `user_modules`
--
ALTER TABLE `user_modules`
  ADD PRIMARY KEY (`umodule_id`),
  ADD UNIQUE KEY `unique_access` (`umodule_id`,`module_id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_recovery`
--
ALTER TABLE `user_recovery`
  ADD PRIMARY KEY (`urecovery_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`usession_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `browser_id` (`browser_id`),
  ADD KEY `usession_branch` (`branch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_actions`
--
ALTER TABLE `app_actions`
  MODIFY `action_id` tinyint(4) NOT NULL AUTO_INCREMENT COMMENT 'ID del registro', AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `app_elements`
--
ALTER TABLE `app_elements`
  MODIFY `element_id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `app_installers`
--
ALTER TABLE `app_installers`
  MODIFY `installer_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `app_methods`
--
ALTER TABLE `app_methods`
  MODIFY `method_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla', AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `app_modules`
--
ALTER TABLE `app_modules`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `app_themes`
--
ALTER TABLE `app_themes`
  MODIFY `theme_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla', AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `browsers`
--
ALTER TABLE `browsers`
  MODIFY `browser_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';

--
-- AUTO_INCREMENT for table `entities`
--
ALTER TABLE `entities`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';

--
-- AUTO_INCREMENT for table `entity_methods`
--
ALTER TABLE `entity_methods`
  MODIFY `cmethod_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';

--
-- AUTO_INCREMENT for table `entity_modules`
--
ALTER TABLE `entity_modules`
  MODIFY `cmodule_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';

--
-- AUTO_INCREMENT for table `user_methods`
--
ALTER TABLE `user_methods`
  MODIFY `umethod_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';

--
-- AUTO_INCREMENT for table `user_modules`
--
ALTER TABLE `user_modules`
  MODIFY `umodule_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';

--
-- AUTO_INCREMENT for table `user_recovery`
--
ALTER TABLE `user_recovery`
  MODIFY `urecovery_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `usession_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID de la tabla';

--
-- Constraints for dumped tables
--

--
-- Constraints for table `app_elements`
--
ALTER TABLE `app_elements`
  ADD CONSTRAINT `element_module` FOREIGN KEY (`module_id`) REFERENCES `app_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `app_methods`
--
ALTER TABLE `app_methods`
  ADD CONSTRAINT `method_module` FOREIGN KEY (`module_id`) REFERENCES `app_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `entities`
--
ALTER TABLE `entities`
  ADD CONSTRAINT `company_creator` FOREIGN KEY (`creation_installer`) REFERENCES `app_installers` (`installer_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `company_editor` FOREIGN KEY (`edition_installer`) REFERENCES `app_installers` (`installer_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `entity_methods`
--
ALTER TABLE `entity_methods`
  ADD CONSTRAINT `cmethod_company` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cmethod_method` FOREIGN KEY (`method_id`) REFERENCES `app_methods` (`method_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `entity_modules`
--
ALTER TABLE `entity_modules`
  ADD CONSTRAINT `cmodule_company` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cmodule_module` FOREIGN KEY (`module_id`) REFERENCES `app_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `user_company` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_theme` FOREIGN KEY (`theme_id`) REFERENCES `app_themes` (`theme_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD CONSTRAINT `log_action` FOREIGN KEY (`action_id`) REFERENCES `app_actions` (`action_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `log_element` FOREIGN KEY (`element_id`) REFERENCES `app_elements` (`element_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_methods`
--
ALTER TABLE `user_methods`
  ADD CONSTRAINT `umethod_method` FOREIGN KEY (`method_id`) REFERENCES `app_methods` (`method_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `umethod_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_modules`
--
ALTER TABLE `user_modules`
  ADD CONSTRAINT `umodule_module` FOREIGN KEY (`module_id`) REFERENCES `app_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `umodule_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_recovery`
--
ALTER TABLE `user_recovery`
  ADD CONSTRAINT `urecovery_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `usession_browser` FOREIGN KEY (`browser_id`) REFERENCES `browsers` (`browser_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `usession_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

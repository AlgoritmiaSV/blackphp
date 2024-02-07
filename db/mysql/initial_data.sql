-- MariaDB dump 10.19  Distrib 10.11.4-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: blackphp
-- ------------------------------------------------------
-- Server version	10.11.4-MariaDB-1~deb12u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `app_elements`
--

LOCK TABLES `app_elements` WRITE;
/*!40000 ALTER TABLE `app_elements` DISABLE KEYS */;
INSERT INTO `app_elements` VALUES
(1,'entityData','Entity data','entity data','M',1,1,'Entity',0,1,0,'entities'),
(2,'users','Users','user','M',0,1,'UserDetails',1,1,1,'users'),
(3,'preferences','Preferences','preferences','F',1,1,'Preferences',0,1,0,'entity_options'),
(4,'roles','Roles','role','M',0,1,'RoleDetails',1,1,1,'roles'),
(5,'trash','Trash','trash','F',1,2,'Trash',0,0,0,''),
(6,'logs','Activity log','activity log','M',1,2,'',0,0,0,'');
/*!40000 ALTER TABLE `app_elements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `app_installers`
--

LOCK TABLES `app_installers` WRITE;
/*!40000 ALTER TABLE `app_installers` DISABLE KEYS */;
INSERT INTO `app_installers` VALUES
(1,'admin','$2y$10$9LarkKwQGYEAPBxnrCkB/.YvyCBqEwbasYZK/vPrTjY1NmPWb0qlW','Instalador','(503) 77197466','contacto@edwinfajardo.com','2023-04-04 08:05:24',1);
/*!40000 ALTER TABLE `app_installers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `app_locales`
--

LOCK TABLES `app_locales` WRITE;
/*!40000 ALTER TABLE `app_locales` DISABLE KEYS */;
INSERT INTO `app_locales` VALUES
(1,'en','en_US','English'),
(2,'es','es_ES','Spanish'),
(3,'it','it_IT','Italian');
/*!40000 ALTER TABLE `app_locales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `app_methods`
--

LOCK TABLES `app_methods` WRITE;
/*!40000 ALTER TABLE `app_methods` DISABLE KEYS */;
INSERT INTO `app_methods` VALUES
(1,1,'Entity','Entity','store_info','Allows you to configure the general information of the business/company',1,1,8,1),
(2,1,'Users','Users','manage_users','Allows you to manage users and permissions for each user',3,2,8,1),
(3,1,'Preferences','Preferences','preferences','Allows to set and modify optional system parameters in the company',2,3,8,1),
(4,1,'About BlackPHP','About','info','Shows system information: Version, contact and technical support',127,NULL,0,1),
(5,2,'Trash','Trash','trash','Deleted elements',1,5,8,1),
(6,1,'Roles','Roles','roles','Allows to manage roles and permissions for each role',4,4,8,1),
(7,2,'Activity log','ActivityLog','activity_log','Activity log on each element for each user',2,6,8,1);
/*!40000 ALTER TABLE `app_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `app_modules`
--

LOCK TABLES `app_modules` WRITE;
/*!40000 ALTER TABLE `app_modules` DISABLE KEYS */;
INSERT INTO `app_modules` VALUES
(1,'Settings','Settings','settings','A','Settings',127,1),
(2,'Tools','Tools','tools','T','',126,1);
/*!40000 ALTER TABLE `app_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `app_option_values`
--

LOCK TABLES `app_option_values` WRITE;
/*!40000 ALTER TABLE `app_option_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_option_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `app_options`
--

LOCK TABLES `app_options` WRITE;
/*!40000 ALTER TABLE `app_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `app_themes`
--

LOCK TABLES `app_themes` WRITE;
/*!40000 ALTER TABLE `app_themes` DISABLE KEYS */;
INSERT INTO `app_themes` VALUES
(1,'Blue - Lateral menu','blackphp'),
(2,'Black - Lateral menu','black'),
(3,'Green - Lateral menu','green'),
(4,'Blue - Top menu','blue_top'),
(5,'White - Without lateral menu','white');
/*!40000 ALTER TABLE `app_themes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed

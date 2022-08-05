-- MySQL dump 10.19  Distrib 10.3.34-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: blackphp
-- ------------------------------------------------------
-- Server version	10.3.34-MariaDB-0+deb10u1

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
-- Dumping data for table `app_actions`
--

LOCK TABLES `app_actions` WRITE;
/*!40000 ALTER TABLE `app_actions` DISABLE KEYS */;
INSERT INTO `app_actions` VALUES (1,'create','Crear','Creó'),(2,'register','Registrar','Registró'),(3,'add','Agregar','Agregó'),(4,'made','Realizar','Realizó'),(5,'edit','Editar','Editó'),(6,'modify','Modificar','Modificó'),(7,'delete','Eliminar','Eliminó'),(8,'erase','Borrar','Borró'),(9,'null','Anular','Anuló');
/*!40000 ALTER TABLE `app_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `app_elements`
--

LOCK TABLES `app_elements` WRITE;
/*!40000 ALTER TABLE `app_elements` DISABLE KEYS */;
INSERT INTO `app_elements` VALUES (1,'entity_data','Datos del negocio','M','P',1,1,'Datos'),(2,'user','Usuario','M','S',0,1,'DetalleUsuario'),(3,'preferences','Preferencias','F','P',1,1,'Preferencias');
/*!40000 ALTER TABLE `app_elements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `app_installers`
--

LOCK TABLES `app_installers` WRITE;
/*!40000 ALTER TABLE `app_installers` DISABLE KEYS */;
INSERT INTO `app_installers` VALUES (1,'fajardo','a7c88c95c93cd74525a3a434930526a5','Edwin Fajardo','77197466','contacto@edwinfajardo.com','2022-02-06 09:24:41',1);
/*!40000 ALTER TABLE `app_installers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `app_methods`
--

LOCK TABLES `app_methods` WRITE;
/*!40000 ALTER TABLE `app_methods` DISABLE KEYS */;
INSERT INTO `app_methods` VALUES (1,1,'Entity','Entity','store_info','Allows you to configure the general information of the business/company',1,1),(2,1,'Users','Users','manage_users','Allows you to manage users and permissions for each user',2,1),(3,1,'Preferences','Preferences','preferences','Allows to set and modify optional system parameters in the company',3,1),(4,1,'About BlackPHP','About','info','Shows system information: Version, contact and technical support',4,1);
/*!40000 ALTER TABLE `app_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `app_modules`
--

LOCK TABLES `app_modules` WRITE;
/*!40000 ALTER TABLE `app_modules` DISABLE KEYS */;
INSERT INTO `app_modules` VALUES (1,'Settings','Settings','A','Settings',127,1);
/*!40000 ALTER TABLE `app_modules` ENABLE KEYS */;
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
INSERT INTO `app_themes` VALUES (1,'Blue - Lateral menu','blackphp'),(2,'Black - Lateral menu','black'),(3,'Green - Lateral menu','green'),(4,'Blue - Top menu','blue_top'),(5,'White - Without lateral menu','white');
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

-- MySQL dump 10.13  Distrib 5.7.20, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: laravel-shop1
-- ------------------------------------------------------
-- Server version	5.7.20-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `admin_menu`
--

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
INSERT INTO `admin_menu` VALUES (1,0,1,'Index','fa-bar-chart','/',NULL,NULL),(2,0,6,'Admin','fa-tasks','',NULL,'2018-12-17 12:31:54'),(3,2,7,'Users','fa-users','auth/users',NULL,'2018-12-17 12:31:54'),(4,2,8,'Roles','fa-user','auth/roles',NULL,'2018-12-17 12:31:54'),(5,2,9,'Permission','fa-ban','auth/permissions',NULL,'2018-12-17 12:31:54'),(6,2,10,'Menu','fa-bars','auth/menu',NULL,'2018-12-17 12:31:54'),(7,2,11,'Operation log','fa-history','auth/logs',NULL,'2018-12-17 12:31:54'),(8,0,2,'User Management','fa-bars','/users','2018-10-11 01:03:08','2018-10-11 01:03:59'),(10,0,3,'Product Manage','fa-cubes','/products','2018-10-20 00:23:53','2018-10-20 00:24:21'),(11,0,12,'Order Management','fa-dollar','/orders','2018-11-25 09:48:04','2018-12-17 12:31:54'),(12,0,13,'Coupon Manage','fa-tags','/coupon_codes','2018-12-07 11:13:25','2018-12-17 12:31:54'),(13,0,14,'Category Manage','fa-bars','/categories','2018-12-13 11:08:07','2018-12-17 12:31:54'),(14,10,4,'Crowd-funding Product','fa-flag-checkered','/crowdfunding_products','2018-12-17 12:25:01','2018-12-17 12:38:05'),(15,10,5,'Common Product','fa-cubes','/products','2018-12-17 12:31:44','2018-12-17 12:31:54');
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_permissions`
--

LOCK TABLES `admin_permissions` WRITE;
/*!40000 ALTER TABLE `admin_permissions` DISABLE KEYS */;
INSERT INTO `admin_permissions` VALUES (1,'All permission','*','','*',NULL,NULL),(2,'Dashboard','dashboard','GET','/',NULL,NULL),(3,'Login','auth.login','','/auth/login\r\n/auth/logout',NULL,NULL),(4,'User setting','auth.setting','GET,PUT','/auth/setting',NULL,NULL),(5,'Auth management','auth.management','','/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs',NULL,NULL),(6,'Users Management','users','','/users*','2018-10-11 01:14:57','2018-10-11 01:14:57'),(7,'Orders Management','orders','','/orders*','2018-12-04 10:41:43','2018-12-04 10:41:43'),(8,'Products Management','products','','/products*','2018-12-10 11:38:57','2018-12-10 11:38:57'),(10,'Coupon Management','coupon_codes','','/coupon_codes*','2018-12-10 11:40:38','2018-12-10 11:40:38');
/*!40000 ALTER TABLE `admin_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_menu`
--

LOCK TABLES `admin_role_menu` WRITE;
/*!40000 ALTER TABLE `admin_role_menu` DISABLE KEYS */;
INSERT INTO `admin_role_menu` VALUES (1,2,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_permissions`
--

LOCK TABLES `admin_role_permissions` WRITE;
/*!40000 ALTER TABLE `admin_role_permissions` DISABLE KEYS */;
INSERT INTO `admin_role_permissions` VALUES (1,1,NULL,NULL),(2,2,NULL,NULL),(2,3,NULL,NULL),(2,4,NULL,NULL),(2,6,NULL,NULL),(2,7,NULL,NULL),(2,8,NULL,NULL),(2,10,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_users`
--

LOCK TABLES `admin_role_users` WRITE;
/*!40000 ALTER TABLE `admin_role_users` DISABLE KEYS */;
INSERT INTO `admin_role_users` VALUES (1,1,NULL,NULL),(2,2,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_roles`
--

LOCK TABLES `admin_roles` WRITE;
/*!40000 ALTER TABLE `admin_roles` DISABLE KEYS */;
INSERT INTO `admin_roles` VALUES (1,'Administrator','administrator','2018-10-11 00:26:36','2018-10-11 00:26:36'),(2,'ops','operator','2018-10-11 01:16:13','2018-10-11 01:16:13');
/*!40000 ALTER TABLE `admin_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_user_permissions`
--

LOCK TABLES `admin_user_permissions` WRITE;
/*!40000 ALTER TABLE `admin_user_permissions` DISABLE KEYS */;
INSERT INTO `admin_user_permissions` VALUES (1,1,NULL,NULL);
/*!40000 ALTER TABLE `admin_user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','$2y$10$ak1.qT9.vJ0WPjTHLRTjfOp0bnQkaaryC.m4vkD4M7Hh4qoADsC/u','Administrator','images/IMG20170707221046.jpg','1h24NSPmPBl17F2bg9isV8WLJMA4elHztXP15zN3MydYvtiAqOGsCzeBzIK9','2018-10-11 00:26:36','2018-11-10 02:38:52'),(2,'operator','$2y$10$pNnz6RSfv3af/5a7xeL9kOW.WRJjSefrBhGXuw8LkLv4vdwesE9ui','ops',NULL,NULL,'2018-10-11 01:21:54','2018-10-11 01:21:54');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-12-17 12:49:45

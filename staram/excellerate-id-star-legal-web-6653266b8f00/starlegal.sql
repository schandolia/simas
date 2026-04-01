-- MySQL dump 10.16  Distrib 10.2.10-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: starlegal
-- ------------------------------------------------------
-- Server version	10.2.10-MariaDB

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
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `req_id` bigint(20) unsigned NOT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `assignee_id` bigint(20) unsigned DEFAULT NULL,
  `assigner_id` bigint(20) unsigned DEFAULT NULL,
  `comments` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignments_req_id_foreign` (`req_id`),
  KEY `assignments_status_id_foreign` (`status_id`),
  KEY `assignments_assignee_id_foreign` (`assignee_id`),
  KEY `assignments_assigner_id_foreign` (`assigner_id`),
  CONSTRAINT `assignments_assignee_id_foreign` FOREIGN KEY (`assignee_id`) REFERENCES `users` (`id`),
  CONSTRAINT `assignments_assigner_id_foreign` FOREIGN KEY (`assigner_id`) REFERENCES `users` (`id`),
  CONSTRAINT `assignments_req_id_foreign` FOREIGN KEY (`req_id`) REFERENCES `doc_request` (`id`),
  CONSTRAINT `assignments_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `rights` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignments`
--

LOCK TABLES `assignments` WRITE;
/*!40000 ALTER TABLE `assignments` DISABLE KEYS */;
INSERT INTO `assignments` VALUES (101,27,1,20,NULL,NULL,'2020-02-04 20:58:09','2020-02-04 20:58:09'),(102,28,1,18,NULL,NULL,'2020-05-26 00:37:22','2020-05-26 00:37:22'),(103,29,1,25,NULL,NULL,'2020-08-27 04:03:17','2020-08-27 04:03:17'),(104,29,2,22,NULL,'<p>bagus gas</p>','2020-08-27 04:04:52','2020-08-27 04:04:52'),(105,29,5,24,NULL,'<p>tolong dikerjakan dengan cepat</p>','2020-08-27 04:06:02','2020-08-27 04:06:02'),(106,29,6,18,NULL,'Jalsion has finalize the request','2020-08-27 04:07:09','2020-08-27 04:07:09'),(107,29,7,24,18,'<p><br></p>','2020-08-27 04:14:24','2020-08-27 04:14:24');
/*!40000 ALTER TABLE `assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `req_id` bigint(20) unsigned NOT NULL,
  `kind` enum('KIND_AKTA','KIND_NPWP','KIND_TDP','KIND_KTP','KIND_PROPOSAL','KIND_OTHER') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'KIND_OTHER',
  `filename` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attachments_req_id_foreign` (`req_id`),
  CONSTRAINT `attachments_req_id_foreign` FOREIGN KEY (`req_id`) REFERENCES `doc_request` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachments`
--

LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_approval`
--

DROP TABLE IF EXISTS `doc_approval`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_approval` (
  `req_id` bigint(20) unsigned NOT NULL,
  `ceo_approved` tinyint(1) DEFAULT NULL,
  `cfo_approved` tinyint(1) DEFAULT NULL,
  `bu_approved` tinyint(1) DEFAULT NULL,
  `legal_approved` tinyint(1) DEFAULT NULL,
  KEY `doc_approval_req_id_foreign` (`req_id`),
  CONSTRAINT `doc_approval_req_id_foreign` FOREIGN KEY (`req_id`) REFERENCES `doc_request` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_approval`
--

LOCK TABLES `doc_approval` WRITE;
/*!40000 ALTER TABLE `doc_approval` DISABLE KEYS */;
INSERT INTO `doc_approval` VALUES (27,NULL,NULL,0,0),(28,NULL,NULL,0,0),(29,NULL,NULL,1,1);
/*!40000 ALTER TABLE `doc_approval` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_request`
--

DROP TABLE IF EXISTS `doc_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_request` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `doc_type` int(10) unsigned NOT NULL,
  `approval_type` enum('REQUEST','REVIEW') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'REQUEST',
  `proposed_by` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proposed_date` date DEFAULT NULL,
  `purpose` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parties` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commercial_terms` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_value` decimal(17,0) DEFAULT NULL,
  `late_payment_toleration` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition_precedent` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `termination_terms` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_terms` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delay_penalty` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guarantee` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agreement_terms` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `status` int(10) unsigned NOT NULL DEFAULT 1,
  `nextStatus` int(10) unsigned DEFAULT NULL,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `last_owner_id` bigint(20) unsigned DEFAULT NULL,
  `requester_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `doc_request_doc_type_foreign` (`doc_type`),
  KEY `doc_request_status_foreign` (`status`),
  KEY `doc_request_nextstatus_foreign` (`nextStatus`),
  KEY `doc_request_owner_id_foreign` (`owner_id`),
  KEY `doc_request_last_owner_id_foreign` (`last_owner_id`),
  KEY `doc_request_requester_id_foreign` (`requester_id`),
  CONSTRAINT `doc_request_doc_type_foreign` FOREIGN KEY (`doc_type`) REFERENCES `doc_type` (`id`),
  CONSTRAINT `doc_request_last_owner_id_foreign` FOREIGN KEY (`last_owner_id`) REFERENCES `users` (`id`),
  CONSTRAINT `doc_request_nextstatus_foreign` FOREIGN KEY (`nextStatus`) REFERENCES `rights` (`id`),
  CONSTRAINT `doc_request_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`),
  CONSTRAINT `doc_request_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`),
  CONSTRAINT `doc_request_status_foreign` FOREIGN KEY (`status`) REFERENCES `rights` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_request`
--

LOCK TABLES `doc_request` WRITE;
/*!40000 ALTER TABLE `doc_request` DISABLE KEYS */;
INSERT INTO `doc_request` VALUES (27,4,'REQUEST',NULL,NULL,'Purpose of the agreement','The Parties','<p><br></p>','Transaction',0,'Toleration of late payment','condition precedent','termination terms','term of payment','term of delay','Guarantee','Term of agreement',1,1,2,NULL,NULL,20,'2020-02-04 20:58:09','2020-02-04 20:58:09'),(28,1,'REQUEST',NULL,NULL,'disclosure agreement','Kancil - Kellton','<p><br></p>',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,2,NULL,NULL,18,'2020-05-26 00:37:22','2020-05-26 00:37:22'),(29,1,'REQUEST',NULL,NULL,'NDA aja','siapa aja','<p><br></p>','a',0,'c','d','c','d','e','r','t',1,7,NULL,18,24,25,'2020-08-27 04:03:17','2020-08-27 04:14:24');
/*!40000 ALTER TABLE `doc_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_request_notifs`
--

DROP TABLE IF EXISTS `doc_request_notifs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_request_notifs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `req_id` bigint(20) unsigned NOT NULL,
  `type` enum('TYPE_REQUEST','TYPE_REVIEW','TYPE_COMPLETED') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `doc_request_notifs_user_id_foreign` (`user_id`),
  KEY `doc_request_notifs_req_id_foreign` (`req_id`),
  CONSTRAINT `doc_request_notifs_req_id_foreign` FOREIGN KEY (`req_id`) REFERENCES `doc_request` (`id`),
  CONSTRAINT `doc_request_notifs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=210 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_request_notifs`
--

LOCK TABLES `doc_request_notifs` WRITE;
/*!40000 ALTER TABLE `doc_request_notifs` DISABLE KEYS */;
INSERT INTO `doc_request_notifs` VALUES (191,19,27,'TYPE_REQUEST'),(193,19,28,'TYPE_REQUEST'),(195,19,29,'TYPE_REQUEST'),(196,21,29,'TYPE_REQUEST'),(198,23,29,'TYPE_REQUEST'),(199,24,29,'TYPE_REQUEST'),(200,18,29,'TYPE_REQUEST'),(202,18,29,'TYPE_COMPLETED'),(203,19,29,'TYPE_COMPLETED'),(204,21,29,'TYPE_COMPLETED'),(205,22,29,'TYPE_COMPLETED'),(206,23,29,'TYPE_COMPLETED'),(207,24,29,'TYPE_COMPLETED'),(208,20,29,'TYPE_COMPLETED'),(209,26,29,'TYPE_COMPLETED');
/*!40000 ALTER TABLE `doc_request_notifs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `doc_request_view`
--

DROP TABLE IF EXISTS `doc_request_view`;
/*!50001 DROP VIEW IF EXISTS `doc_request_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `doc_request_view` (
  `id` tinyint NOT NULL,
  `doc_type` tinyint NOT NULL,
  `approval_type` tinyint NOT NULL,
  `purpose` tinyint NOT NULL,
  `proposed_by` tinyint NOT NULL,
  `proposed_date` tinyint NOT NULL,
  `parties` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `commercial_terms` tinyint NOT NULL,
  `transaction_value` tinyint NOT NULL,
  `late_payment_toleration` tinyint NOT NULL,
  `condition_precedent` tinyint NOT NULL,
  `termination_terms` tinyint NOT NULL,
  `payment_terms` tinyint NOT NULL,
  `delay_penalty` tinyint NOT NULL,
  `guarantee` tinyint NOT NULL,
  `agreement_terms` tinyint NOT NULL,
  `is_active` tinyint NOT NULL,
  `status_id` tinyint NOT NULL,
  `status_name` tinyint NOT NULL,
  `next_status_id` tinyint NOT NULL,
  `next_status_name` tinyint NOT NULL,
  `ceo_approved` tinyint NOT NULL,
  `cfo_approved` tinyint NOT NULL,
  `bu_approved` tinyint NOT NULL,
  `legal_approved` tinyint NOT NULL,
  `owner_id` tinyint NOT NULL,
  `owner_name` tinyint NOT NULL,
  `owner_avatar` tinyint NOT NULL,
  `last_owner_id` tinyint NOT NULL,
  `l_owner_name` tinyint NOT NULL,
  `l_owner_avatar` tinyint NOT NULL,
  `requester_id` tinyint NOT NULL,
  `requester_name` tinyint NOT NULL,
  `requester_avatar` tinyint NOT NULL,
  `created_at` tinyint NOT NULL,
  `updated_at` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `doc_share`
--

DROP TABLE IF EXISTS `doc_share`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_share` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `folder_id` bigint(20) unsigned DEFAULT NULL,
  `doc_name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `doc_type` int(10) unsigned NOT NULL,
  `agreement_date` date DEFAULT NULL,
  `agreement_number` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parties` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expire_date` date NOT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `submitter_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `doc_share_folder_id_foreign` (`folder_id`),
  KEY `doc_share_doc_type_foreign` (`doc_type`),
  KEY `doc_share_submitter_id_foreign` (`submitter_id`),
  CONSTRAINT `doc_share_doc_type_foreign` FOREIGN KEY (`doc_type`) REFERENCES `doc_type` (`id`),
  CONSTRAINT `doc_share_folder_id_foreign` FOREIGN KEY (`folder_id`) REFERENCES `folder_share` (`id`),
  CONSTRAINT `doc_share_submitter_id_foreign` FOREIGN KEY (`submitter_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_share`
--

LOCK TABLES `doc_share` WRITE;
/*!40000 ALTER TABLE `doc_share` DISABLE KEYS */;
/*!40000 ALTER TABLE `doc_share` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `doc_share_view`
--

DROP TABLE IF EXISTS `doc_share_view`;
/*!50001 DROP VIEW IF EXISTS `doc_share_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `doc_share_view` (
  `type` tinyint NOT NULL,
  `doc_id` tinyint NOT NULL,
  `folder_id` tinyint NOT NULL,
  `user_id` tinyint NOT NULL,
  `doc_name` tinyint NOT NULL,
  `submitter_id` tinyint NOT NULL,
  `company_name` tinyint NOT NULL,
  `doc_type` tinyint NOT NULL,
  `agreement_date` tinyint NOT NULL,
  `agreement_number` tinyint NOT NULL,
  `parties` tinyint NOT NULL,
  `expire_date` tinyint NOT NULL,
  `remark` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `attachment` tinyint NOT NULL,
  `date_creation` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `doc_type`
--

DROP TABLE IF EXISTS `doc_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `approval_path` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `sla_min` int(10) unsigned NOT NULL,
  `sla_max` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_type`
--

LOCK TABLES `doc_type` WRITE;
/*!40000 ALTER TABLE `doc_type` DISABLE KEYS */;
INSERT INTO `doc_type` VALUES (1,'NDA','Duis at velit eu est congue elementum. In hac habitasse platea dictumst.','[\"1\",\"2\",\"5\",\"6\",\"7\"]',1,1,'2019-10-24 12:45:21','2019-10-24 12:45:21'),(2,'Term Sheet','Cras pellentesque volutpat dui. Maecenas tristique, est et tempus semper, est quam pharetra magna, ac consequat metus sapien ut nunc. ','[\"1\",\"3\",\"2\",\"5\",\"6\",\"7\"]',1,2,'2019-10-24 12:45:21','2019-10-24 12:45:21'),(3,'MoU','Aenean lectus. Pellentesque eget nunc. Donec quis orci eget orci vehicula condimentum.','[\"1\",\"2\",\"5\",\"6\",\"7\"]',1,2,'2019-10-24 12:45:21','2019-10-24 12:45:21'),(4,'Agreement','Proin interdum mauris non ligula pellentesque ultrices. Phasellus id sapien in sapien iaculis congue.','[\"1\",\"2\",\"5\",\"6\",\"7\"]',1,7,'2019-10-24 12:45:21','2019-10-24 12:45:21'),(11,'Others','This document type is for other type of Document','[\"1\",\"2\",\"5\",\"6\",\"7\"]',1,7,'2019-11-21 17:00:00','2019-11-21 17:00:00');
/*!40000 ALTER TABLE `doc_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_user`
--

DROP TABLE IF EXISTS `doc_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `doc_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `doc_user_doc_id_foreign` (`doc_id`),
  KEY `doc_user_user_id_foreign` (`user_id`),
  CONSTRAINT `doc_user_doc_id_foreign` FOREIGN KEY (`doc_id`) REFERENCES `doc_share` (`id`),
  CONSTRAINT `doc_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_user`
--

LOCK TABLES `doc_user` WRITE;
/*!40000 ALTER TABLE `doc_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `doc_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `folder_share`
--

DROP TABLE IF EXISTS `folder_share`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `folder_share` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `folder_id` bigint(20) unsigned DEFAULT NULL,
  `folder_name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `folder_share_folder_id_foreign` (`folder_id`),
  KEY `folder_share_creator_id_foreign` (`creator_id`),
  CONSTRAINT `folder_share_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`),
  CONSTRAINT `folder_share_folder_id_foreign` FOREIGN KEY (`folder_id`) REFERENCES `folder_share` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `folder_share`
--

LOCK TABLES `folder_share` WRITE;
/*!40000 ALTER TABLE `folder_share` DISABLE KEYS */;
/*!40000 ALTER TABLE `folder_share` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `folder_share_view`
--

DROP TABLE IF EXISTS `folder_share_view`;
/*!50001 DROP VIEW IF EXISTS `folder_share_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `folder_share_view` (
  `type` tinyint NOT NULL,
  `doc_id` tinyint NOT NULL,
  `folder_id` tinyint NOT NULL,
  `user_id` tinyint NOT NULL,
  `doc_name` tinyint NOT NULL,
  `submitter_id` tinyint NOT NULL,
  `company_name` tinyint NOT NULL,
  `doc_type` tinyint NOT NULL,
  `agreement_date` tinyint NOT NULL,
  `agreement_number` tinyint NOT NULL,
  `parties` tinyint NOT NULL,
  `expire_date` tinyint NOT NULL,
  `remark` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `attachment` tinyint NOT NULL,
  `date_creation` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=365 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (347,'2014_10_10_134357_create_roles_table',1),(348,'2014_10_10_221329_create_rights_table',1),(349,'2014_10_11_221433_create_role_rights_table',1),(350,'2014_10_12_000000_create_users_table',1),(351,'2014_10_12_100000_create_password_resets_table',1),(352,'2019_08_19_000000_create_failed_jobs_table',1),(353,'2019_10_02_105420_create_doc_type_table',1),(354,'2019_10_02_105756_create_doc_request_table',1),(355,'2019_10_02_123747_create_doc_share_table',1),(356,'2019_10_02_124623_create_folder_share_table',1),(357,'2019_10_02_125249_create_doc_user_table',1),(358,'2019_10_14_023516_create_attachments_table',1),(359,'2019_10_14_132743_create_assignments_table',1),(360,'2019_10_17_145742_create_request_submission_table',1),(361,'2019_10_19_071500_create_doc_approval_table',1),(362,'2019_10_20_130701_view_assignment',1),(363,'2019_10_20_154336_create_request_submission_audit_table',1),(364,'2019_10_23_171053_create_doc_request_notifs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request_submission`
--

DROP TABLE IF EXISTS `request_submission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_submission` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `req_id` bigint(20) unsigned NOT NULL,
  `submitter_id` bigint(20) unsigned NOT NULL,
  `date` date DEFAULT NULL,
  `agreement_number` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parties` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_objective` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_period` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nominal_transaction` decimal(17,0) DEFAULT NULL,
  `terms` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_name` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_path` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('STATE_NOT_DONE','STATE_DONE','STATE_APPROVED','STATE_REJECTED','STATE_TOBE_REVISE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'STATE_NOT_DONE',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_submission_req_id_foreign` (`req_id`),
  KEY `request_submission_submitter_id_foreign` (`submitter_id`),
  CONSTRAINT `request_submission_req_id_foreign` FOREIGN KEY (`req_id`) REFERENCES `doc_request` (`id`),
  CONSTRAINT `request_submission_submitter_id_foreign` FOREIGN KEY (`submitter_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request_submission`
--

LOCK TABLES `request_submission` WRITE;
/*!40000 ALTER TABLE `request_submission` DISABLE KEYS */;
INSERT INTO `request_submission` VALUES (18,29,24,'2020-08-27',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'_318_K_Pdt_2009.pdf','storage/request-submission/iXboZWuFmKsPJgDOOjn5YzY9pYniqGRBSeTs5vXF.pdf','STATE_APPROVED',NULL,'1','2020-08-27 04:07:09','2020-08-27 04:14:24');
/*!40000 ALTER TABLE `request_submission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request_submission_audit`
--

DROP TABLE IF EXISTS `request_submission_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_submission_audit` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `submitter_id` bigint(20) unsigned NOT NULL,
  `req_id` bigint(20) unsigned NOT NULL,
  `date` date DEFAULT NULL,
  `agreement_number` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parties` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_objective` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_period` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nominal_transaction` decimal(17,2) DEFAULT NULL,
  `terms` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_name` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_path` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('STATE_NOT_DONE','STATE_DONE','STATE_APPROVED','STATE_REJECTED','STATE_TOBE_REVISE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'STATE_NOT_DONE',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_submission_audit_submitter_id_foreign` (`submitter_id`),
  KEY `request_submission_audit_req_id_foreign` (`req_id`),
  CONSTRAINT `request_submission_audit_req_id_foreign` FOREIGN KEY (`req_id`) REFERENCES `doc_request` (`id`),
  CONSTRAINT `request_submission_audit_submitter_id_foreign` FOREIGN KEY (`submitter_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request_submission_audit`
--

LOCK TABLES `request_submission_audit` WRITE;
/*!40000 ALTER TABLE `request_submission_audit` DISABLE KEYS */;
INSERT INTO `request_submission_audit` VALUES (18,18,29,'2020-08-27',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'_318_K_Pdt_2009.pdf','storage/request-submission/iXboZWuFmKsPJgDOOjn5YzY9pYniqGRBSeTs5vXF.pdf','STATE_DONE',NULL,'1','2020-08-27 04:07:09','2020-08-27 04:07:09');
/*!40000 ALTER TABLE `request_submission_audit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rights`
--

DROP TABLE IF EXISTS `rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rights` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `right_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approval_col` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rights`
--

LOCK TABLES `rights` WRITE;
/*!40000 ALTER TABLE `rights` DISABLE KEYS */;
INSERT INTO `rights` VALUES (1,'Submit','This action can be done by user',NULL),(2,'BU Head Approved','This action can be done by BU Head','bu_approved'),(3,'CFO Approved','This action can be done by CFO','cfo_approved'),(4,'CEO Approved','This action can be done by CEO','ceo_approved'),(5,'Assign','This action can be done by Legal Head','legal_approved'),(6,'Process','This action can be done by PIC Legal',NULL),(7,'Final Approval','This action can be done by Legal Head',NULL),(8,'Hold','This action can be done by all approver kind users',NULL),(9,'Activate','This action can be done by all approver kind users',NULL),(10,'Reject','This action can be done by all approver kind users',NULL),(11,'Request Revise','This action provided for user to revise completed document',NULL),(12,'Reject Request Revise','This action provided for Legal Head to reject Request of Revise completed document',NULL),(13,'Approve Request Revise','This action provided for Legal Head to approve Request of Revise completed document',NULL);
/*!40000 ALTER TABLE `rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_rights`
--

DROP TABLE IF EXISTS `role_rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_rights` (
  `role_id` int(10) unsigned NOT NULL,
  `right_id` int(10) unsigned NOT NULL,
  KEY `role_rights_role_id_foreign` (`role_id`),
  KEY `role_rights_right_id_foreign` (`right_id`),
  CONSTRAINT `role_rights_right_id_foreign` FOREIGN KEY (`right_id`) REFERENCES `rights` (`id`),
  CONSTRAINT `role_rights_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_rights`
--

LOCK TABLES `role_rights` WRITE;
/*!40000 ALTER TABLE `role_rights` DISABLE KEYS */;
INSERT INTO `role_rights` VALUES (1,1),(2,6),(3,2),(4,5),(4,7),(5,3),(6,4);
/*!40000 ALTER TABLE `role_rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kind` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'User','USER'),(2,'Legal PIC','LEGAL'),(3,'BU Head','APPROVER'),(4,'Legal Head','APPROVER'),(5,'CFO','APPROVER'),(6,'CEO','APPROVER'),(7,'Admin','ADMIN');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supervisor_id` bigint(20) unsigned DEFAULT NULL,
  `role_id` int(10) unsigned NOT NULL DEFAULT 0,
  `avatar` varchar(600) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logged_in` tinyint(1) NOT NULL DEFAULT 0,
  `last_login` timestamp NULL DEFAULT NULL,
  `buhead_id` bigint(20) unsigned DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','Admin','admin@starlegal.id',NULL,7,'storage/avatar/tc18UZSGcDtTwaVjtlmH4QKrulqkJKFR6Qj1nuBP.jpeg',NULL,'$2y$10$fcuyAcnKDgd4Lp1Bb3IJ0eWL3d6IQrZHc5.JsfG3E4wKj6q3HsGqu',1,'2021-04-19 05:14:27',NULL,'0cQUY6ubnB5cDF3SGhgxkdtHLAj7poKbSvkrOuUuKYZQHffRaAReafg9R6W7','2019-10-30 10:25:33','2021-04-19 05:14:27'),(18,'Jalsion','Jalsion','jalsion.sandjaya@kawancicil.co.id',NULL,7,'assets/images/avatars/users/24.png',NULL,'$2y$10$RAVbTSuMSSEVspBtxE9euO/6hKh0KRcQbgkLAFu8xL68Zn5Lwinoy',1,'2021-04-08 23:11:00',NULL,NULL,'2020-02-04 00:37:50','2021-04-09 00:28:15'),(19,'Aldira','Aldira','jalsions@yahoo.com',NULL,3,'assets/images/avatars/users/1.png',NULL,'$2y$10$Dqfu4BhSDZBESOl/biMxbuqjATxjFrnS.lbE6a.N.QCNT9B.lDpuy',0,NULL,NULL,NULL,'2020-02-04 19:52:59','2020-02-04 19:53:19'),(20,'User','User','user@starcapital.co.id',NULL,5,'assets/images/avatars/users/50.png',NULL,'$2y$10$Plkbk2SorFgreKRf1Opfc.LIZ6rjZLubMrulgMSLjdpXBjSSGC6dy',0,'2020-08-27 04:10:45',NULL,NULL,'2020-02-04 20:53:03','2020-08-27 04:11:50'),(21,'Kawan Cicil','Kawan Cicil','sjalsion@gmail.com',NULL,3,'assets/images/avatars/users/22.png',NULL,'$2y$10$p5sTIsWIcNILljRpHJH9nePfIZs8xAODaOtFhQwm6.hncl/qWazou',0,NULL,NULL,NULL,'2020-08-27 01:14:46','2020-08-27 01:15:04'),(22,'Nadya W','Nadya W','nadya.winanda@starcapital.co.id',NULL,2,'assets/images/avatars/users/46.png',NULL,'$2y$10$DwVzhPvKj56Cj0piZGCgM.O5qKO.4eLWztzqq0nmO23umAuq2d9/2',0,'2020-08-27 04:09:30',NULL,NULL,'2020-08-27 03:59:18','2021-04-09 01:26:36'),(23,'Nadya S','Nadya S','nadya.sihombing@lifewithsun.om',NULL,4,'assets/images/avatars/users/5.png',NULL,'$2y$10$UGliCmpOixUFvjML8Of43.zTogr4xNanUh7VFERy0C8mSLuKxYYJW',0,NULL,NULL,NULL,'2020-08-27 03:59:52','2020-08-27 04:01:07'),(24,'Nadya S','Nadya S','nadya.sihombing@lifewithsun.com',NULL,2,'assets/images/avatars/users/49.png',NULL,'$2y$10$G6Tl5XjSTfK/VU2HMirUhO/W.BY//Qkor4CzArIy3H/MZb9BLhP7W',0,'2020-08-27 04:12:55',NULL,'V4rZJeAsmHhkkGxzv0kfQpG7JgVzAdHZ0x0CRPK9RAZL74jCZo5BuL1OQNZo','2020-08-27 04:00:09','2021-04-09 01:26:03'),(25,'Jalsion','Jalsion','jalsion.sandjaya@kreditratingindonesia.co.id',NULL,1,'assets/images/avatars/users/46.png',NULL,'$2y$10$QVhUrQh82/0cYK9tGmYRmuODnSpoicA0fqrSKxabTXlRLgocElpom',0,'2021-04-05 02:51:21',19,'qMr3QAEabxYeRKYrkltJE13QTI4AG8T558WTTbEsm9EQR6NwAV5vwtxAqxsw','2020-08-27 04:00:33','2021-04-09 04:58:45'),(26,'CEO','CEO','jalsion@yahoo.com',NULL,6,'assets/images/avatars/users/70.png',NULL,'$2y$10$xGBejtFOynqyApqZ2gQNEOS9X9jTPKH.GJiW1VwxDqkGRjoSYsqv.',0,NULL,NULL,NULL,'2020-08-27 04:12:25','2020-08-27 04:12:44'),(27,'psheila','patricia sheila','folderone988@gmail.com',NULL,1,'assets/images/avatars/users/12.png',NULL,'$2y$10$y5prx08P8sRzhJdRJdNroeTNKrh.OFqcYjwV1OgO1qpjNbFZhfj52',0,NULL,21,NULL,'2021-04-09 00:21:56','2021-04-09 00:34:16');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `doc_request_view`
--

/*!50001 DROP TABLE IF EXISTS `doc_request_view`*/;
/*!50001 DROP VIEW IF EXISTS `doc_request_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `doc_request_view` AS (select `doc_request`.`id` AS `id`,`doc_type`.`type` AS `doc_type`,`doc_request`.`approval_type` AS `approval_type`,`doc_request`.`purpose` AS `purpose`,`doc_request`.`proposed_by` AS `proposed_by`,`doc_request`.`proposed_date` AS `proposed_date`,`doc_request`.`parties` AS `parties`,`doc_request`.`description` AS `description`,`doc_request`.`commercial_terms` AS `commercial_terms`,`doc_request`.`transaction_value` AS `transaction_value`,`doc_request`.`late_payment_toleration` AS `late_payment_toleration`,`doc_request`.`condition_precedent` AS `condition_precedent`,`doc_request`.`termination_terms` AS `termination_terms`,`doc_request`.`payment_terms` AS `payment_terms`,`doc_request`.`delay_penalty` AS `delay_penalty`,`doc_request`.`guarantee` AS `guarantee`,`doc_request`.`agreement_terms` AS `agreement_terms`,`doc_request`.`isActive` AS `is_active`,`doc_request`.`status` AS `status_id`,`status`.`right_name` AS `status_name`,`doc_request`.`nextStatus` AS `next_status_id`,`next_status`.`right_name` AS `next_status_name`,`doc_approval`.`ceo_approved` AS `ceo_approved`,`doc_approval`.`cfo_approved` AS `cfo_approved`,`doc_approval`.`bu_approved` AS `bu_approved`,`doc_approval`.`legal_approved` AS `legal_approved`,`doc_request`.`owner_id` AS `owner_id`,`owner`.`fullname` AS `owner_name`,`owner`.`avatar` AS `owner_avatar`,`doc_request`.`last_owner_id` AS `last_owner_id`,`last_owner`.`fullname` AS `l_owner_name`,`last_owner`.`avatar` AS `l_owner_avatar`,`doc_request`.`requester_id` AS `requester_id`,`requester`.`fullname` AS `requester_name`,`requester`.`avatar` AS `requester_avatar`,`doc_request`.`created_at` AS `created_at`,`doc_request`.`updated_at` AS `updated_at` from (((((((`doc_request` join `doc_type` on(`doc_type`.`id` = `doc_request`.`doc_type`)) join `users` `requester` on(`requester`.`id` = `doc_request`.`requester_id`)) join `rights` `status` on(`status`.`id` = `doc_request`.`status`)) join `doc_approval` on(`doc_approval`.`req_id` = `doc_request`.`id`)) left join `rights` `next_status` on(`next_status`.`id` = `doc_request`.`nextStatus`)) left join `users` `owner` on(`owner`.`id` = `doc_request`.`owner_id`)) left join `users` `last_owner` on(`last_owner`.`id` = `doc_request`.`last_owner_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `doc_share_view`
--

/*!50001 DROP TABLE IF EXISTS `doc_share_view`*/;
/*!50001 DROP VIEW IF EXISTS `doc_share_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `doc_share_view` AS (select 'DOC' AS `type`,`doc_share`.`id` AS `doc_id`,`doc_share`.`folder_id` AS `folder_id`,`doc_user`.`user_id` AS `user_id`,`doc_share`.`doc_name` AS `doc_name`,`doc_share`.`submitter_id` AS `submitter_id`,`doc_share`.`company_name` AS `company_name`,`doc_share`.`doc_type` AS `doc_type`,`doc_share`.`agreement_date` AS `agreement_date`,`doc_share`.`agreement_number` AS `agreement_number`,`doc_share`.`parties` AS `parties`,`doc_share`.`expire_date` AS `expire_date`,`doc_share`.`remark` AS `remark`,`doc_share`.`description` AS `description`,`doc_share`.`attachment` AS `attachment`,`doc_share`.`created_at` AS `date_creation` from (`doc_share` left join `doc_user` on(`doc_share`.`id` = `doc_user`.`doc_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `folder_share_view`
--

/*!50001 DROP TABLE IF EXISTS `folder_share_view`*/;
/*!50001 DROP VIEW IF EXISTS `folder_share_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `folder_share_view` AS (select 'FOLDER' AS `type`,`folder_share`.`id` AS `doc_id`,`folder_share`.`folder_id` AS `folder_id`,NULL AS `user_id`,`folder_share`.`folder_name` AS `doc_name`,`folder_share`.`creator_id` AS `submitter_id`,NULL AS `company_name`,NULL AS `doc_type`,NULL AS `agreement_date`,NULL AS `agreement_number`,NULL AS `parties`,NULL AS `expire_date`,NULL AS `remark`,`folder_share`.`description` AS `description`,NULL AS `attachment`,`folder_share`.`created_at` AS `date_creation` from `folder_share`) */;
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

-- Dump completed on 2021-09-28 13:41:22

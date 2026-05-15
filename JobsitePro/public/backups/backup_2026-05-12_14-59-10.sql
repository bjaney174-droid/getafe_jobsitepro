-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: getafe_jobsite_pro
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `admin_logs`
--

DROP TABLE IF EXISTS `admin_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_logs_ibfk_1` (`admin_id`),
  CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_logs`
--

LOCK TABLES `admin_logs` WRITE;
/*!40000 ALTER TABLE `admin_logs` DISABLE KEYS */;
INSERT INTO `admin_logs` VALUES (1,1,'logout','User logged out','2026-05-09 13:30:46'),(3,3,'login','User logged in: jobseeker@test.com','2026-05-09 13:32:57'),(4,3,'logout','User logged out','2026-05-09 13:37:08'),(6,4,'login','User logged in: employer@test.com','2026-05-09 13:39:48'),(7,4,'post_job','Posted job: Junior Web Developer','2026-05-09 13:43:04'),(8,4,'logout','User logged out','2026-05-09 13:43:41'),(9,4,'login','User logged in: employer@test.com','2026-05-09 13:45:11'),(10,4,'logout','User logged out','2026-05-09 13:48:08'),(11,1,'login','User logged in: admin@getafejobsite.com','2026-05-09 13:50:56'),(12,1,'logout','User logged out','2026-05-09 13:52:06'),(13,3,'login','User logged in: jobseeker@test.com','2026-05-09 13:52:52'),(14,3,'apply_job','User applied for job ID: 1','2026-05-09 13:55:04'),(15,3,'logout','User logged out','2026-05-09 13:55:58'),(16,4,'login','User logged in: employer@test.com','2026-05-09 13:56:28'),(17,4,'application_status_updated','Application ID: 1, Status: approved','2026-05-09 13:57:39'),(18,4,'logout','User logged out','2026-05-09 13:58:41'),(19,3,'login','User logged in: jobseeker@test.com','2026-05-09 13:59:17'),(20,3,'logout','User logged out','2026-05-09 14:00:45'),(21,3,'login','User logged in: jobseeker@test.com','2026-05-09 22:47:50'),(22,3,'login','User logged in: jobseeker@test.com','2026-05-10 12:38:48'),(23,3,'logout','User logged out','2026-05-10 12:40:44'),(24,4,'login','User logged in: employer@test.com','2026-05-10 12:42:09'),(25,4,'logout','User logged out','2026-05-10 12:45:43'),(26,3,'login','User logged in: jobseeker@test.com','2026-05-10 12:59:28'),(27,3,'logout','User logged out','2026-05-10 13:42:54'),(28,4,'login','User logged in: employer@test.com','2026-05-10 13:43:43'),(29,4,'edit_job','Edited job ID: 1','2026-05-10 13:54:16'),(30,4,'logout','User logged out','2026-05-10 14:07:53'),(31,1,'login','User logged in: admin@getafejobsite.com','2026-05-10 14:09:22'),(32,1,'backup_database','Database backed up: backup_2026-05-10_16-52-14.sql','2026-05-10 14:52:14'),(33,1,'backup_database','Database backed up: backup_2026-05-10_16-53-23.sql','2026-05-10 14:53:23'),(34,1,'backup_database','Database backed up: backup_2026-05-10_17-11-16.sql','2026-05-10 15:11:16'),(35,1,'logout','User logged out','2026-05-10 15:12:26'),(36,3,'login','User logged in: jobseeker@test.com','2026-05-10 15:12:58'),(37,3,'logout','User logged out','2026-05-10 15:14:20'),(38,4,'login','User logged in: employer@test.com','2026-05-10 15:14:44'),(39,4,'logout','User logged out','2026-05-10 15:16:18'),(40,1,'login','User logged in: admin@getafejobsite.com','2026-05-10 15:16:39'),(41,1,'logout','User logged out','2026-05-10 15:17:11'),(42,3,'login','User logged in: jobseeker@test.com','2026-05-10 15:17:47'),(43,3,'logout','User logged out','2026-05-10 15:35:55'),(44,4,'login','User logged in: employer@test.com','2026-05-10 15:36:30'),(45,4,'logout','User logged out','2026-05-10 15:48:13'),(46,1,'login','User logged in: admin@getafejobsite.com','2026-05-10 15:48:41'),(47,1,'logout','User logged out','2026-05-10 15:49:08'),(48,1,'login','User logged in: admin@getafejobsite.com','2026-05-10 16:04:17'),(49,1,'logout','User logged out','2026-05-10 16:28:00'),(50,1,'login','User logged in: admin@getafejobsite.com','2026-05-10 16:29:23'),(51,1,'logout','User logged out','2026-05-10 16:50:47'),(52,3,'login','User logged in: jobseeker@test.com','2026-05-10 16:51:17'),(53,3,'logout','User logged out','2026-05-10 16:51:44'),(54,4,'login','User logged in: employer@test.com','2026-05-10 16:52:14'),(55,4,'logout','User logged out','2026-05-10 16:52:40'),(56,3,'login','User logged in: jobseeker@test.com','2026-05-11 05:42:41'),(57,3,'logout','User logged out','2026-05-11 05:44:48'),(58,4,'login','User logged in: employer@test.com','2026-05-11 05:45:10'),(59,4,'logout','User logged out','2026-05-11 05:46:26'),(60,1,'login','User logged in: admin@getafejobsite.com','2026-05-11 05:46:52'),(61,1,'backup_database','Database backed up: backup_2026-05-11_07-48-50.sql','2026-05-11 05:48:51'),(62,1,'logout','User logged out','2026-05-11 05:49:09'),(63,3,'login','User logged in: jobseeker@test.com','2026-05-11 05:49:33'),(64,3,'logout','User logged out','2026-05-11 06:31:35'),(65,1,'login','User logged in: admin@getafejobsite.com','2026-05-11 06:32:00'),(66,1,'logout','User logged out','2026-05-11 06:59:47'),(67,4,'login','User logged in: employer@test.com','2026-05-11 07:00:24'),(68,4,'logout','User logged out','2026-05-11 07:06:15'),(69,1,'login','User logged in: admin@getafejobsite.com','2026-05-11 07:06:37'),(70,4,'login','User logged in: employer@test.com','2026-05-11 12:07:36'),(71,4,'request_featured','Requested feature for job ID: 1','2026-05-11 12:11:36'),(72,4,'logout','User logged out','2026-05-11 12:12:05'),(73,1,'login','User logged in: admin@getafejobsite.com','2026-05-11 12:12:40'),(74,1,'approve_featured','Approved featured job ID: 1','2026-05-11 12:13:32'),(75,1,'logout','User logged out','2026-05-11 12:13:47'),(76,1,'login','User logged in: admin@getafejobsite.com','2026-05-11 12:18:22'),(77,1,'logout','User logged out','2026-05-11 12:31:58'),(78,4,'login','User logged in: employer@test.com','2026-05-11 12:32:22'),(79,4,'edit_job','Edited job ID: 1','2026-05-11 12:32:40'),(80,4,'post_job','Posted job: Sales Staff','2026-05-11 12:34:10'),(81,4,'request_featured','Requested feature for job ID: 2','2026-05-11 12:34:22'),(82,4,'logout','User logged out','2026-05-11 12:34:51'),(83,1,'login','User logged in: admin@getafejobsite.com','2026-05-11 12:35:43'),(84,1,'backup_database','Database backed up: backup_2026-05-11_14-41-07.sql','2026-05-11 12:41:07'),(85,1,'backup_database','Database backed up: backup_2026-05-11_14-41-07.sql','2026-05-11 12:41:07'),(86,1,'logout','User logged out','2026-05-11 12:46:26'),(87,4,'login','User logged in: employer@test.com','2026-05-11 12:46:45'),(88,4,'request_featured','Requested feature for job ID: 2','2026-05-11 13:03:41'),(89,4,'request_featured','Requested feature for job ID: 2','2026-05-11 13:26:16'),(90,4,'logout','User logged out','2026-05-11 13:26:24'),(91,1,'login','User logged in: admin@getafejobsite.com','2026-05-11 13:26:44'),(92,1,'logout','User logged out','2026-05-11 14:18:43'),(93,4,'login','User logged in: employer@test.com','2026-05-11 14:19:32'),(94,4,'request_featured','Requested feature for job ID: 2','2026-05-11 14:19:50'),(95,4,'logout','User logged out','2026-05-11 14:19:58'),(96,1,'login','User logged in: admin@getafejobsite.com','2026-05-11 14:20:25'),(97,1,'approve_featured','Approved featured job ID: 2 | Payment: GCash | Ref: 487621873576','2026-05-11 14:21:30'),(98,1,'logout','User logged out','2026-05-11 14:21:51'),(99,4,'login','User logged in: employer@test.com','2026-05-11 14:22:24'),(100,4,'logout','User logged out','2026-05-11 14:23:00'),(101,1,'login','User logged in: admin@getafejobsite.com','2026-05-11 22:12:18'),(102,1,'logout','User logged out','2026-05-11 22:26:26'),(103,4,'login','User logged in: employer@test.com','2026-05-11 22:26:44'),(104,4,'logout','User logged out','2026-05-11 22:31:47'),(105,3,'login','User logged in: jobseeker@test.com','2026-05-11 22:32:05'),(106,3,'logout','User logged out','2026-05-11 22:33:50'),(107,4,'login','User logged in: employer@test.com','2026-05-12 01:06:05'),(108,4,'logout','User logged out','2026-05-12 01:08:00'),(109,1,'login','User logged in: admin@getafejobsite.com','2026-05-12 01:08:24'),(110,1,'backup_database','Database backed up: backup_2026-05-12_03-10-57.sql','2026-05-12 01:10:58'),(111,1,'logout','User logged out','2026-05-12 01:11:13'),(112,3,'login','User logged in: jobseeker@test.com','2026-05-12 01:16:00'),(113,3,'logout','User logged out','2026-05-12 01:17:48'),(114,1,'login','User logged in: admin@getafejobsite.com','2026-05-12 01:18:54'),(115,1,'logout','User logged out','2026-05-12 01:22:55'),(116,4,'login','User logged in: employer@test.com','2026-05-12 03:20:08'),(117,4,'delete_job','Deleted job ID: 2','2026-05-12 03:20:18'),(118,4,'delete_job','Deleted job ID: 1','2026-05-12 03:20:24'),(119,4,'logout','User logged out','2026-05-12 03:20:55'),(120,3,'login','User logged in: jobseeker@test.com','2026-05-12 03:21:26'),(121,3,'logout','User logged out','2026-05-12 03:22:02'),(122,1,'login','User logged in: admin@getafejobsite.com','2026-05-12 03:22:29'),(123,1,'logout','User logged out','2026-05-12 03:23:39'),(124,1,'login','User logged in: admin@getafejobsite.com','2026-05-12 12:39:36'),(125,1,'update_settings','Site settings updated','2026-05-12 12:44:34'),(126,1,'logout','User logged out','2026-05-12 12:44:46'),(127,3,'login','User logged in: jobseeker@test.com','2026-05-12 12:45:42'),(128,3,'logout','User logged out','2026-05-12 12:55:46'),(129,1,'login','User logged in: admin@getafejobsite.com','2026-05-12 12:56:33'),(130,1,'update_settings','Site settings updated','2026-05-12 12:58:09'),(131,1,'backup_database','Database backed up: backup_2026-05-12_14-59-09.sql','2026-05-12 12:59:09');
/*!40000 ALTER TABLE `admin_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','reviewed','approved','rejected','withdrawn') DEFAULT 'pending',
  `cover_letter` longtext DEFAULT NULL,
  `resume_url` varchar(255) DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_application` (`job_id`,`user_id`),
  KEY `applications_ibfk_2` (`user_id`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applications`
--

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_reply` longtext DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_views`
--

DROP TABLE IF EXISTS `job_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `viewed_date` date NOT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_job_user_day` (`job_id`,`user_id`,`viewed_date`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_views`
--

LOCK TABLES `job_views` WRITE;
/*!40000 ALTER TABLE `job_views` DISABLE KEYS */;
INSERT INTO `job_views` VALUES (1,1,3,'2026-05-10','2026-05-10 13:40:49'),(4,1,4,'2026-05-10','2026-05-10 13:44:40'),(7,1,1,'2026-05-10','2026-05-10 14:58:50'),(10,1,3,'2026-05-11','2026-05-11 05:44:09'),(13,1,1,'2026-05-11','2026-05-11 12:18:22'),(15,2,3,'2026-05-12','2026-05-11 22:32:12');
/*!40000 ALTER TABLE `job_views` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `category` varchar(100) NOT NULL,
  `company` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT 'Getafe, Bohol',
  `salary_min` int(11) DEFAULT NULL,
  `salary_max` int(11) DEFAULT NULL,
  `job_type` enum('full-time','part-time','contract','temporary','internship') NOT NULL,
  `experience_level` enum('entry','mid','senior','executive') DEFAULT 'mid',
  `requirements` longtext DEFAULT NULL,
  `benefits` longtext DEFAULT NULL,
  `posted_by` int(11) NOT NULL,
  `applications_count` int(11) DEFAULT 0,
  `views_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `featured_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `featured_paid` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_job_type` (`job_type`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_created_at` (`created_at`),
  KEY `jobs_ibfk_1` (`posted_by`),
  KEY `idx_job_featured` (`is_featured`,`featured_until`),
  CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reviews_ibfk_1` (`job_id`),
  KEY `reviews_ibfk_2` (`user_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saved_jobs`
--

DROP TABLE IF EXISTS `saved_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saved_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_saved` (`user_id`,`job_id`),
  KEY `saved_jobs_ibfk_2` (`job_id`),
  CONSTRAINT `saved_jobs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `saved_jobs_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saved_jobs`
--

LOCK TABLES `saved_jobs` WRITE;
/*!40000 ALTER TABLE `saved_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `saved_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) DEFAULT NULL,
  `setting_value` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_name','Getafe Jobsite','2026-05-09 13:07:52','2026-05-12 12:58:09'),(2,'site_description','Find your dream job in Getafe, Bohol, Philippines','2026-05-09 13:07:52','2026-05-12 12:58:09'),(3,'site_email','info@getafejobsite.com','2026-05-09 13:07:52','2026-05-12 12:58:09'),(4,'contact_phone','+63 970 191 8626','2026-05-09 13:07:52','2026-05-12 12:58:09'),(5,'address','Getafe, Bohol, Philippines 6334','2026-05-09 13:07:52','2026-05-12 12:58:09'),(6,'featured_job_price','100','2026-05-09 13:07:52','2026-05-12 12:58:09'),(7,'max_applications_per_day','10','2026-05-09 13:07:52','2026-05-12 12:58:09'),(8,'facebook_url','https://facebook.com/yourpage','2026-05-10 14:02:51','2026-05-12 12:58:09'),(9,'twitter_url','https://twitter.com/yourhandle','2026-05-10 14:02:51','2026-05-12 12:58:09'),(10,'linkedin_url','https://linkedin.com/in/yourprofile','2026-05-10 14:02:51','2026-05-12 12:58:09');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `testimonials_ibfk_1` (`user_id`),
  CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonials`
--

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `processed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`),
  KEY `idx_user_transactions` (`user_id`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('jobseeker','employer','admin') NOT NULL DEFAULT 'jobseeker',
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(255) DEFAULT 'Getafe, Bohol',
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `skills` varchar(500) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `company_info` text DEFAULT NULL,
  `company_website` varchar(255) DEFAULT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_user_type` (`user_type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','Getafe','admin@getafejobsite.com','$2y$10$h1B6DuRYpLnCUEVbKFvRceAaAhWzpKVeKGXMem26IsNeNQCuOtfBG','admin','09701918626','Getafe, Bohol','uploads/avatars/avatar_1_1778423819.jpg','','','','','',NULL,1,'active','2026-05-09 13:07:52','2026-05-10 14:36:59'),(3,'job','seeker','jobseeker@test.com','$2y$12$4FKyRGBwCqGXVbx.4SdJluLCfTbIdu3cnnsC1kGQtkY4Dg/KVsiTK','jobseeker','09701918626','Getafe, Bohol','uploads/avatars/avatar_3_1778556116.jpg','I am a passionate Full-Stack Developer with experience in building responsive web applications using PHP, JavaScript, and MySQL. I enjoy solving complex problems and am currently focused on improving user experiences through clean, efficient code.','PHP, JavaScript, MySQL, HTML5, CSS3, Git, Bootstrap, Web Development','','','',NULL,0,'active','2026-05-09 13:31:40','2026-05-12 03:21:56'),(4,'em','ployer','employer@test.com','$2y$12$apCjdOg48VjP.0v4kAnPHOYJhRBxOqnyJuQCOBbQ.5k56..gt1awi','employer','09701918626','Getafe, Bohol','uploads/avatars/avatar_4_1778556049.jpg','','','Getafe Creative Studio','We are a leading digital solutions provider based in Getafe, Bohol. Our mission is to empower local businesses by providing high-quality web development, graphic design, and digital marketing services. We pride ourselves on a collaborative work environment and our commitment to community growth.','https://getafecreatives.com.ph',NULL,0,'active','2026-05-09 13:38:51','2026-05-12 03:20:49');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-12 20:59:10

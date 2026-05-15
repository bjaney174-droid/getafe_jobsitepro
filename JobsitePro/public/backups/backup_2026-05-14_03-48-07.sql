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
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_logs`
--

LOCK TABLES `admin_logs` WRITE;
/*!40000 ALTER TABLE `admin_logs` DISABLE KEYS */;
INSERT INTO `admin_logs` VALUES (133,1,'clear_logs','Admin logs cleared','2026-05-12 13:03:15'),(134,1,'logout','User logged out','2026-05-12 13:06:52'),(135,4,'login','User logged in: employer@test.com','2026-05-12 13:07:11'),(136,4,'post_job','Posted job: Junior Web Developer','2026-05-12 13:11:53'),(137,4,'edit_job','Edited job ID: 3','2026-05-12 13:21:38'),(138,4,'logout','User logged out','2026-05-12 14:03:16'),(139,1,'login','User logged in: admin@getafejobsite.com','2026-05-12 14:03:38'),(140,1,'logout','User logged out','2026-05-12 14:04:35'),(141,4,'login','User logged in: employer@test.com','2026-05-12 14:14:20'),(142,4,'logout','User logged out','2026-05-12 14:29:50'),(143,1,'login','User logged in: admin@getafejobsite.com','2026-05-12 14:30:13'),(144,1,'logout','User logged out','2026-05-12 14:30:55'),(145,3,'login','User logged in: jobseeker@test.com','2026-05-12 15:20:56'),(146,3,'logout','User logged out','2026-05-12 15:23:03'),(147,4,'login','User logged in: employer@test.com','2026-05-12 15:23:31'),(148,4,'logout','User logged out','2026-05-12 15:26:20'),(149,1,'login','User logged in: admin@getafejobsite.com','2026-05-12 15:26:44'),(150,1,'logout','User logged out','2026-05-12 15:33:50'),(151,3,'login','User logged in: jobseeker@test.com','2026-05-12 15:34:08'),(152,3,'apply_job','User applied for job ID: 3','2026-05-12 15:35:35'),(153,3,'logout','User logged out','2026-05-12 15:35:40'),(154,4,'login','User logged in: employer@test.com','2026-05-12 15:36:02'),(155,4,'login','User logged in: employer@test.com','2026-05-13 04:15:58'),(156,4,'login','User logged in: employer@test.com','2026-05-14 01:38:59'),(157,4,'logout','User logged out','2026-05-14 01:42:34'),(158,3,'login','User logged in: jobseeker@test.com','2026-05-14 01:42:55'),(159,3,'logout','User logged out','2026-05-14 01:43:47'),(160,4,'login','User logged in: employer@test.com','2026-05-14 01:44:08'),(161,4,'request_featured','Requested feature for job ID: 3','2026-05-14 01:44:18'),(162,4,'logout','User logged out','2026-05-14 01:44:34'),(163,1,'login','User logged in: admin@getafejobsite.com','2026-05-14 01:44:57'),(164,1,'approve_featured','Approved featured job ID: 3 for 30 days','2026-05-14 01:47:35');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applications`
--

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
INSERT INTO `applications` VALUES (2,3,3,'pending','fefef',NULL,'2026-05-12 15:35:33','2026-05-12 15:35:33');
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
  `ticket_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
INSERT INTO `contact_messages` VALUES (4,'em ployer','employer@test.com','posting a job','how should i pay in featuring my job?','','2026-05-12 13:08:32',NULL,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_views`
--

LOCK TABLES `job_views` WRITE;
/*!40000 ALTER TABLE `job_views` DISABLE KEYS */;
INSERT INTO `job_views` VALUES (1,1,3,'2026-05-10','2026-05-10 13:40:49'),(4,1,4,'2026-05-10','2026-05-10 13:44:40'),(7,1,1,'2026-05-10','2026-05-10 14:58:50'),(10,1,3,'2026-05-11','2026-05-11 05:44:09'),(13,1,1,'2026-05-11','2026-05-11 12:18:22'),(15,2,3,'2026-05-12','2026-05-11 22:32:12'),(19,3,4,'2026-05-12','2026-05-12 13:12:04'),(23,3,3,'2026-05-12','2026-05-12 15:34:10'),(25,3,4,'2026-05-14','2026-05-14 01:39:32'),(27,3,3,'2026-05-14','2026-05-14 01:43:19');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (3,'Junior Web Developer','We are looking for a motivated Junior Web Developer to join our growing team. You will be responsible for building and maintaining user-friendly websites, collaborating with designers, and troubleshooting front-end and back-end issues. This is a great opportunity to grow your skills in a supportive environment.','IT','Getafe Tech Solutions','Getafe, Bohol (Remote / On-site Hybrid)',20000,40000,'full-time','entry','Bachelor&#039;s degree in Computer Science or a related field\r\nProficiency in HTML, CSS, and JavaScript\r\nBasic understanding of PHP and MySQL\r\nStrong problem-solving skills\r\nWillingness to learn new technologies','HMO / Health Insurance\r\nPaid vacation and sick leave\r\nMonthly Internet allowance\r\nYearly performance bonus\r\nProfessional certification support',4,1,4,1,1,'2026-06-13 03:47:35','2026-05-12 13:11:51','2026-05-14 01:47:35',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
-- Table structure for table `support_replies`
--

DROP TABLE IF EXISTS `support_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('user','admin') NOT NULL,
  `message` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `sender_id` (`sender_id`),
  CONSTRAINT `support_replies_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`),
  CONSTRAINT `support_replies_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_replies`
--

LOCK TABLES `support_replies` WRITE;
/*!40000 ALTER TABLE `support_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(500) NOT NULL,
  `message` longtext NOT NULL,
  `status` enum('open','in_progress','closed') DEFAULT 'open',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_id` (`ticket_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (6,4,3,500.00,'featured_job','completed','gcash',NULL,'','2026-05-14 09:44:18','2026-05-14 09:47:35');
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
INSERT INTO `users` VALUES (1,'Admin','Getafe','admin@getafejobsite.com','$2y$10$h1B6DuRYpLnCUEVbKFvRceAaAhWzpKVeKGXMem26IsNeNQCuOtfBG','admin','09701918626','Getafe, Bohol','uploads/avatars/avatar_1_1778723154.jpg','','','','','',NULL,1,'active','2026-05-09 13:07:52','2026-05-14 01:45:54'),(3,'job','seeker','jobseeker@test.com','$2y$12$4FKyRGBwCqGXVbx.4SdJluLCfTbIdu3cnnsC1kGQtkY4Dg/KVsiTK','jobseeker','09701918626','Getafe, Bohol','uploads/avatars/avatar_3_1778556116.jpg','I am a passionate Full-Stack Developer with experience in building responsive web applications using PHP, JavaScript, and MySQL. I enjoy solving complex problems and am currently focused on improving user experiences through clean, efficient code.','PHP, JavaScript, MySQL, HTML5, CSS3, Git, Bootstrap, Web Development','','','',NULL,0,'active','2026-05-09 13:31:40','2026-05-12 03:21:56'),(4,'Mary Joy','Torrefiel','employer@test.com','$2y$12$apCjdOg48VjP.0v4kAnPHOYJhRBxOqnyJuQCOBbQ.5k56..gt1awi','employer','+632 970 191 8626','Tulang, Getafe, Bohol','uploads/avatars/avatar_4_1778592623.jpg','','','Getafe Creative Studio','We are a leading digital solutions provider based in Getafe, Bohol. Our mission is to empower local businesses by providing high-quality web development, graphic design, and digital marketing services. We pride ourselves on a collaborative work environment and our commitment to community growth.','https://getafecreatives.com.ph',NULL,0,'active','2026-05-09 13:38:51','2026-05-12 13:30:23');
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

-- Dump completed on 2026-05-14  9:48:07

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2026 at 05:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `getafe_jobsite_pro`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action`, `details`, `created_at`) VALUES
(133, 1, 'clear_logs', 'Admin logs cleared', '2026-05-12 13:03:15'),
(134, 1, 'logout', 'User logged out', '2026-05-12 13:06:52'),
(135, 4, 'login', 'User logged in: employer@test.com', '2026-05-12 13:07:11'),
(136, 4, 'post_job', 'Posted job: Junior Web Developer', '2026-05-12 13:11:53'),
(137, 4, 'edit_job', 'Edited job ID: 3', '2026-05-12 13:21:38'),
(138, 4, 'logout', 'User logged out', '2026-05-12 14:03:16'),
(139, 1, 'login', 'User logged in: admin@getafejobsite.com', '2026-05-12 14:03:38'),
(140, 1, 'logout', 'User logged out', '2026-05-12 14:04:35'),
(141, 4, 'login', 'User logged in: employer@test.com', '2026-05-12 14:14:20'),
(142, 4, 'logout', 'User logged out', '2026-05-12 14:29:50'),
(143, 1, 'login', 'User logged in: admin@getafejobsite.com', '2026-05-12 14:30:13'),
(144, 1, 'logout', 'User logged out', '2026-05-12 14:30:55');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','reviewed','approved','rejected','withdrawn') DEFAULT 'pending',
  `cover_letter` longtext DEFAULT NULL,
  `resume_url` varchar(255) DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_reply` longtext DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `ticket_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `status`, `created_at`, `admin_reply`, `replied_at`, `ticket_id`) VALUES
(4, 'em ployer', 'employer@test.com', 'posting a job', 'how should i pay in featuring my job?', 'unread', '2026-05-12 13:08:32', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
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
  `featured_paid` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `description`, `category`, `company`, `location`, `salary_min`, `salary_max`, `job_type`, `experience_level`, `requirements`, `benefits`, `posted_by`, `applications_count`, `views_count`, `is_active`, `is_featured`, `featured_until`, `created_at`, `updated_at`, `featured_paid`) VALUES
(3, 'Junior Web Developer', 'We are looking for a motivated Junior Web Developer to join our growing team. You will be responsible for building and maintaining user-friendly websites, collaborating with designers, and troubleshooting front-end and back-end issues. This is a great opportunity to grow your skills in a supportive environment.', 'IT', 'Getafe Tech Solutions', 'Getafe, Bohol (Remote / On-site Hybrid)', 20000, 40000, 'full-time', 'entry', 'Bachelor&#039;s degree in Computer Science or a related field\r\nProficiency in HTML, CSS, and JavaScript\r\nBasic understanding of PHP and MySQL\r\nStrong problem-solving skills\r\nWillingness to learn new technologies', 'HMO / Health Insurance\r\nPaid vacation and sick leave\r\nMonthly Internet allowance\r\nYearly performance bonus\r\nProfessional certification support', 4, 0, 1, 1, 0, NULL, '2026-05-12 13:11:51', '2026-05-12 13:21:38', 0);

-- --------------------------------------------------------

--
-- Table structure for table `job_views`
--

CREATE TABLE `job_views` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `viewed_date` date NOT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_views`
--

INSERT INTO `job_views` (`id`, `job_id`, `user_id`, `viewed_date`, `viewed_at`) VALUES
(1, 1, 3, '2026-05-10', '2026-05-10 13:40:49'),
(4, 1, 4, '2026-05-10', '2026-05-10 13:44:40'),
(7, 1, 1, '2026-05-10', '2026-05-10 14:58:50'),
(10, 1, 3, '2026-05-11', '2026-05-11 05:44:09'),
(13, 1, 1, '2026-05-11', '2026-05-11 12:18:22'),
(15, 2, 3, '2026-05-12', '2026-05-11 22:32:12'),
(19, 3, 4, '2026-05-12', '2026-05-12 13:12:04');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_jobs`
--

CREATE TABLE `saved_jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) DEFAULT NULL,
  `setting_value` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Getafe Jobsite', '2026-05-09 13:07:52', '2026-05-12 12:58:09'),
(2, 'site_description', 'Find your dream job in Getafe, Bohol, Philippines', '2026-05-09 13:07:52', '2026-05-12 12:58:09'),
(3, 'site_email', 'info@getafejobsite.com', '2026-05-09 13:07:52', '2026-05-12 12:58:09'),
(4, 'contact_phone', '+63 970 191 8626', '2026-05-09 13:07:52', '2026-05-12 12:58:09'),
(5, 'address', 'Getafe, Bohol, Philippines 6334', '2026-05-09 13:07:52', '2026-05-12 12:58:09'),
(6, 'featured_job_price', '100', '2026-05-09 13:07:52', '2026-05-12 12:58:09'),
(7, 'max_applications_per_day', '10', '2026-05-09 13:07:52', '2026-05-12 12:58:09'),
(8, 'facebook_url', 'https://facebook.com/yourpage', '2026-05-10 14:02:51', '2026-05-12 12:58:09'),
(9, 'twitter_url', 'https://twitter.com/yourhandle', '2026-05-10 14:02:51', '2026-05-12 12:58:09'),
(10, 'linkedin_url', 'https://linkedin.com/in/yourprofile', '2026-05-10 14:02:51', '2026-05-12 12:58:09');

-- --------------------------------------------------------

--
-- Table structure for table `support_replies`
--

CREATE TABLE `support_replies` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('user','admin') NOT NULL,
  `message` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `ticket_id` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(500) NOT NULL,
  `message` longtext NOT NULL,
  `status` enum('open','in_progress','closed') DEFAULT 'open',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `processed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `user_type`, `phone`, `location`, `avatar`, `bio`, `skills`, `company`, `company_info`, `company_website`, `company_logo`, `verified`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Getafe', 'admin@getafejobsite.com', '$2y$10$h1B6DuRYpLnCUEVbKFvRceAaAhWzpKVeKGXMem26IsNeNQCuOtfBG', 'admin', '09701918626', 'Getafe, Bohol', 'uploads/avatars/avatar_1_1778423819.jpg', '', '', '', '', '', NULL, 1, 'active', '2026-05-09 13:07:52', '2026-05-10 14:36:59'),
(3, 'job', 'seeker', 'jobseeker@test.com', '$2y$12$4FKyRGBwCqGXVbx.4SdJluLCfTbIdu3cnnsC1kGQtkY4Dg/KVsiTK', 'jobseeker', '09701918626', 'Getafe, Bohol', 'uploads/avatars/avatar_3_1778556116.jpg', 'I am a passionate Full-Stack Developer with experience in building responsive web applications using PHP, JavaScript, and MySQL. I enjoy solving complex problems and am currently focused on improving user experiences through clean, efficient code.', 'PHP, JavaScript, MySQL, HTML5, CSS3, Git, Bootstrap, Web Development', '', '', '', NULL, 0, 'active', '2026-05-09 13:31:40', '2026-05-12 03:21:56'),
(4, 'Mary Joy', 'Torrefiel', 'employer@test.com', '$2y$12$apCjdOg48VjP.0v4kAnPHOYJhRBxOqnyJuQCOBbQ.5k56..gt1awi', 'employer', '+632 970 191 8626', 'Tulang, Getafe, Bohol', 'uploads/avatars/avatar_4_1778592623.jpg', '', '', 'Getafe Creative Studio', 'We are a leading digital solutions provider based in Getafe, Bohol. Our mission is to empower local businesses by providing high-quality web development, graphic design, and digital marketing services. We pride ourselves on a collaborative work environment and our commitment to community growth.', 'https://getafecreatives.com.ph', NULL, 0, 'active', '2026-05-09 13:38:51', '2026-05-12 13:30:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_logs_ibfk_1` (`admin_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_application` (`job_id`,`user_id`),
  ADD KEY `applications_ibfk_2` (`user_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_job_type` (`job_type`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `jobs_ibfk_1` (`posted_by`),
  ADD KEY `idx_job_featured` (`is_featured`,`featured_until`);

--
-- Indexes for table `job_views`
--
ALTER TABLE `job_views`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_job_user_day` (`job_id`,`user_id`,`viewed_date`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_ibfk_1` (`job_id`),
  ADD KEY `reviews_ibfk_2` (`user_id`);

--
-- Indexes for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_saved` (`user_id`,`job_id`),
  ADD KEY `saved_jobs_ibfk_2` (`job_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `support_replies`
--
ALTER TABLE `support_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `testimonials_ibfk_1` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `idx_user_transactions` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_user_type` (`user_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `job_views`
--
ALTER TABLE `job_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `support_replies`
--
ALTER TABLE `support_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD CONSTRAINT `saved_jobs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_jobs_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_replies`
--
ALTER TABLE `support_replies`
  ADD CONSTRAINT `support_replies_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`),
  ADD CONSTRAINT `support_replies_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

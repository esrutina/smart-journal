-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2026 at 03:37 PM
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
-- Database: `smart_journal`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entries`
--

CREATE TABLE `entries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `mood` varchar(20) DEFAULT 'neutral',
  `mood_intensity` int(11) DEFAULT 5,
  `categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`categories`)),
  `photo_path` varchar(255) DEFAULT NULL,
  `is_favorite` tinyint(1) DEFAULT 0,
  `is_archived` tinyint(1) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `word_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `entries`
--

INSERT INTO `entries` (`id`, `user_id`, `title`, `content`, `mood`, `mood_intensity`, `categories`, `photo_path`, `is_favorite`, `is_archived`, `is_deleted`, `deleted_at`, `word_count`, `created_at`, `updated_at`) VALUES
(1, 1, 'greetiing', 'hi my name is esru&nbsp;', 'happy', 6, '[\"personal\"]', NULL, 1, 0, 0, NULL, 6, '2026-07-03 21:31:10', '2026-07-03 21:33:00'),
(2, 1, 'new day', 'what if i had milion&nbsp;&nbsp;', 'calm', 3, '[\"ideas\"]', NULL, 1, 0, 0, NULL, 7, '2026-07-03 21:32:21', '2026-07-03 21:55:33'),
(3, 1, 'new day', 'what if i had milion&nbsp;&nbsp;', 'happy', 5, '[]', NULL, 1, 0, 0, NULL, 7, '2026-07-03 21:54:57', '2026-07-03 21:58:17'),
(4, 1, 'movie', '<h2><b><i><u>effnfv fffffff ffff fffff gfcdex4ex5cnv xebcvn7bm8n,9</u></i></b>m&nbsp; hhhhhhhhhhhhhhh&nbsp;&nbsp;<b style=\"font-size: 1.5rem;\"><i><u>effnfv fffffff ffff fffff gfcdex4ex5cnv xebcvn7bm8n,9</u></i></b><span style=\"font-size: 1.5rem;\">m&nbsp; hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhkkkkhkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff&nbsp;</span><b style=\"font-size: 1.5rem;\"><i><u>effnfv fffffff ffff fffff gfcdex4ex5cnv xebcvn7bm8n,9</u></i></b><span style=\"font-size: 1.5rem;\">m&nbsp; hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhkkkkhkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk&nbsp;</span><b style=\"font-size: 1.5rem;\"><i><u>effnfv fffffff ffff fffff gfcdex4ex5cnv xebcvn7bm8n,9</u></i></b><span style=\"font-size: 1.5rem;\">m&nbsp; hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhkkkkhkkkk&nbsp;</span><b style=\"font-size: 1.5rem;\"><i><u>effnfv fffffff ffff fffff gfcdex4ex5cnv xebcvn7bm8n,9</u></i></b><span style=\"font-size: 1.5rem;\">m&nbsp; hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhkkkkhkkkkkkkkkkkkkkkkk&nbsp;</span><b style=\"font-size: 1.5rem;\"><i><u>effnfv fffffff ffff fffff gfcdex4ex5cnv xebcvn7bm8n,9</u></i></b><span style=\"font-size: 1.5rem;\">m&nbsp; hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhkkkkhkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffvvvvvvvvvvvvvvvv&nbsp;</span><b style=\"font-size: 1.5rem;\"><i><u>effnfv fffffff ffff fffff gfcdex4ex5cnv xebcvn7bm8n,9</u></i></b><span style=\"font-size: 1.5rem;\">m&nbsp; hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhkkkkhkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffvvvvvvvvvvvvvvvv&nbsp;</span><b style=\"font-size: 1.5rem;\"><i><u>effnfv fffffff ffff fffff gfcdex4ex5cnv xebcvn7bm8n,9</u></i></b><span style=\"font-size: 1.5rem;\">m&nbsp; hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhkkkkhkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvccccccccccccccccccccccccccccccccccccccccccccccccc&nbsp;</span><span style=\"font-size: 1.5rem;\">vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvccccccccccccccccccccccccccccccccccccccccccccccccc&nbsp;</span><span style=\"font-size: 1.5rem;\">vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvccccccccccccccccccccccccccccccccccccccccccccccccc&nbsp;</span><span style=\"font-size: 1.5rem;\">kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvccccccccccccccccccccccccccccccccccccccccccccccccc&nbsp;</span><span style=\"font-size: 1.5rem;\">kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvccccccccccccccccccccccccccccccccccccccccccccccccc&nbsp;</span><span style=\"font-size: 1.5rem;\">kkkkkkkkffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvccccccccccccccccccccccccccccccccccccccccccccccccc&nbsp;</span><span style=\"font-size: 1.5rem;\">vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvccccccccccccccccccccccccccccccccccccccccccccccccc&nbsp;</span><span style=\"font-size: 1.5rem;\">hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhkkkkhkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvccccccccccccccccccccccccccccccccccccccccccccccccc</span></h2>', 'tired', 7, '[\"ideas\"]', 'uploads/6a4830f2477c9.jpg', 1, 0, 0, NULL, 126, '2026-07-03 22:00:18', '2026-07-04 07:59:46');

-- --------------------------------------------------------

--
-- Table structure for table `streaks`
--

CREATE TABLE `streaks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `current_streak` int(11) DEFAULT 0,
  `longest_streak` int(11) DEFAULT 0,
  `last_entry_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `streaks`
--

INSERT INTO `streaks` (`id`, `user_id`, `current_streak`, `longest_streak`, `last_entry_date`) VALUES
(1, 1, 1, 0, '2026-07-03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT 'default.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `full_name`, `password_hash`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 'esru', 'esraelenyew@gmail.com', 'esrael enyew', '$2y$10$EjC45wVLxSOAG9nz0HMGm.DSiL1Uy/cUWbBJpGQ8ZxMYPHRFxg64i', 'default.png', '2026-07-03 21:08:38', '2026-07-03 21:08:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `entry_id` (`entry_id`);

--
-- Indexes for table `entries`
--
ALTER TABLE `entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_created` (`user_id`,`created_at`),
  ADD KEY `idx_mood` (`mood`),
  ADD KEY `idx_favorite` (`is_favorite`),
  ADD KEY `idx_archived` (`is_archived`);

--
-- Indexes for table `streaks`
--
ALTER TABLE `streaks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entries`
--
ALTER TABLE `entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `streaks`
--
ALTER TABLE `streaks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_log_ibfk_2` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `entries`
--
ALTER TABLE `entries`
  ADD CONSTRAINT `entries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `streaks`
--
ALTER TABLE `streaks`
  ADD CONSTRAINT `streaks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

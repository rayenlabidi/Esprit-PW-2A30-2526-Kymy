-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2026 at 08:24 PM
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
-- Database: `workify_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `publication_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_init` varchar(5) NOT NULL,
  `user_avatar` varchar(50) DEFAULT 'av-blue',
  `comment` text NOT NULL,
  `likes` int(11) DEFAULT 0,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `publication_id`, `user_name`, `user_init`, `user_avatar`, `comment`, `likes`, `parent_id`, `created_at`) VALUES
(6, 8, 'You', 'YO', 'av-blue', 'HEYYYY', 0, NULL, '2026-04-23 08:30:03'),
(7, 7, 'You', 'YO', 'av-blue', 'heyy', 0, NULL, '2026-04-23 09:01:49'),
(8, 8, 'You', 'YO', 'av-blue', 'hhhhhhhhhhhh', 0, NULL, '2026-04-23 09:10:00'),
(9, 8, 'You', 'YO', 'av-blue', 'ggg', 0, 6, '2026-04-23 09:10:11'),
(10, 9, 'You', 'YO', 'av-blue', 'hahahahahaha', 1, NULL, '2026-04-25 11:24:41'),
(11, 9, 'You', 'YO', 'av-blue', 'hahahahahaha', 0, 10, '2026-04-25 11:24:50'),
(13, 8, 'Sarah K.', 'YO', 'av-blue', 'hh', 0, 6, '2026-04-25 16:44:16'),
(14, 7, 'You', 'YO', 'av-blue', 'dddd', 0, 7, '2026-04-25 16:45:44'),
(15, 9, 'You', 'YO', 'av-blue', 'heyy', 0, 10, '2026-04-25 16:57:10'),
(16, 9, 'You', 'YO', 'av-blue', 'kkk', 0, 10, '2026-04-25 16:57:21');

-- --------------------------------------------------------

--
-- Table structure for table `comment_likes`
--

CREATE TABLE `comment_likes` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment_likes`
--

INSERT INTO `comment_likes` (`id`, `comment_id`, `user_id`, `created_at`) VALUES
(5, 10, 'sarah_k', '2026-04-25 16:44:04');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` varchar(50) NOT NULL,
  `receiver_id` varchar(50) NOT NULL,
  `sender_name` varchar(100) NOT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `sender_init` varchar(5) NOT NULL,
  `receiver_init` varchar(5) NOT NULL,
  `sender_avatar` varchar(50) DEFAULT 'av-blue',
  `receiver_avatar` varchar(50) DEFAULT 'av-blue',
  `publication_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_flagged` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `sender_name`, `receiver_name`, `sender_init`, `receiver_init`, `sender_avatar`, `receiver_avatar`, `publication_id`, `content`, `is_read`, `created_at`, `updated_at`) VALUES
(4, 'james_o', 'current_user', 'James Ortega', 'You', 'JO', 'YO', 'av-blue', 'av-blue', NULL, 'Hey! Just checking in on the project status.', 1, '2026-04-22 18:38:45', '2026-04-22 19:12:48'),
(5, 'current_user', 'james_o', 'You', 'James Ortega', 'YO', 'JO', 'av-blue', 'av-blue', NULL, 'Almost done! Will send by tomorrow.', 1, '2026-04-22 18:38:45', '2026-04-22 18:38:45'),
(25, 'leo_c', 'sarah_k', 'Leo Chen', 'Sarah K.', 'LC', 'SK', 'av-teal', 'av-blue', NULL, 'heyyyyyyyyyyyyyyyyyy', 1, '2026-04-25 13:25:20', '2026-04-25 13:27:02'),
(26, 'sarah_k', 'leo_c', 'Sarah K.', 'Leo Chen', 'SK', 'LC', 'av-blue', 'av-teal', NULL, 'hello there', 1, '2026-04-25 13:27:06', '2026-04-25 13:27:29');

-- --------------------------------------------------------

--
-- Table structure for table `publication`
--

CREATE TABLE `publication` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) DEFAULT 'current_user',
  `user_name` varchar(100) NOT NULL,
  `user_init` varchar(5) NOT NULL,
  `user_role` enum('Freelancer','Client') DEFAULT 'Freelancer',
  `user_avatar` varchar(50) DEFAULT 'av-blue',
  `content` text NOT NULL,
  `has_image` tinyint(1) DEFAULT 0,
  `image_url` LONGTEXT DEFAULT NULL,
  `likes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publication`
--

INSERT INTO `publication` (`id`, `user_id`, `user_name`, `user_init`, `user_role`, `user_avatar`, `content`, `has_image`, `image_url`, `likes`, `created_at`, `updated_at`) VALUES
(3, 'other_user', 'Leo Chen', 'LC', 'Client', 'av-teal', 'We\'re hiring a senior React developer for a 3-month contract starting next month. Remote, competitive rate, interesting product in the logistics space.\r\n\r\nDrop your portfolio in the comments or DM me directly 👇\r\n\r\n#Hiring #ReactJS #Remote #Freelance', 0, NULL, 0, '2026-04-22 18:38:26', '2026-04-25 16:56:24'),
(4, 'current_user', 'You', 'YO', 'Freelancer', 'av-blue', 'Welcome to Workify! Share your thoughts, projects, and opportunities with the community.', 0, NULL, 0, '2026-04-22 18:38:26', '2026-04-25 16:56:01'),
(5, 'admin', 'mouhamed', 'MH', 'Client', 'av-purple', 'ssssssssssssssssssssssss', 0, '', 0, '2026-04-22 23:52:14', '2026-04-25 16:56:01'),
(7, 'admin', 'deli', 'DL', 'Freelancer', 'av-teal', 'fddddddddddddddddddd', 0, '', 1, '2026-04-23 00:09:09', '2026-04-25 16:45:40'),
(8, 'admin', 'OMAR', 'OM', 'Client', 'av-green', 'ASLEMA ENA OMAR', 0, '', 2, '2026-04-23 08:19:02', '2026-04-25 16:57:25'),
(9, 'admin', 'raghed', 'RH', 'Client', 'av-pink', 'aslema ena raghed lmoghta', 0, '', 2, '2026-04-25 11:24:33', '2026-04-25 16:57:04');

-- --------------------------------------------------------

--
-- Table structure for table `publication_likes`
--

CREATE TABLE `publication_likes` (
  `id` int(11) NOT NULL,
  `publication_id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publication_likes`
--

INSERT INTO `publication_likes` (`id`, `publication_id`, `user_id`, `created_at`) VALUES
(3, 8, 'sarah_k', '2026-04-25 16:43:13'),
(5, 9, 'sarah_k', '2026-04-25 16:43:20'),
(7, 7, 'current_user', '2026-04-25 16:45:40'),
(11, 9, 'current_user', '2026-04-25 16:57:04'),
(12, 8, 'current_user', '2026-04-25 16:57:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `init` varchar(5) NOT NULL,
  `avatar` varchar(50) DEFAULT 'av-blue',
  `role` enum('Freelancer','Client') DEFAULT 'Freelancer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `name`, `init`, `avatar`, `role`, `created_at`) VALUES
(1, 'sarah_k', 'Sarah K.', 'SK', 'av-blue', 'Freelancer', '2026-04-22 18:39:17'),
(2, 'james_o', 'James Ortega', 'JO', 'av-green', 'Freelancer', '2026-04-22 18:39:17'),
(3, 'priya_n', 'Priya N.', 'PN', 'av-purple', 'Freelancer', '2026-04-22 18:39:17'),
(4, 'marcus_l', 'Marcus L.', 'ML', 'av-orange', 'Client', '2026-04-22 18:39:17'),
(5, 'aisha_t', 'Aisha T.', 'AT', 'av-pink', 'Freelancer', '2026-04-22 18:39:17'),
(6, 'leo_c', 'Leo Chen', 'LC', 'av-teal', 'Client', '2026-04-22 18:39:17'),
(7, 'moadh', 'KOLAB', 'MO', 'av-purple', 'Client', '2026-04-22 19:13:54'),
(12, 'raghed', 'ftouhi', 'RH', 'av-pink', 'Client', '2026-04-25 11:25:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publication_id` (`publication_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_comment_like` (`comment_id`,`user_id`),
  ADD KEY `idx_user_comment_likes` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conversation` (`sender_id`,`receiver_id`),
  ADD KEY `idx_receiver_read` (`receiver_id`,`is_read`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_receiver` (`receiver_id`),
  ADD KEY `publication_id` (`publication_id`),
  ADD KEY `idx_is_read` (`is_read`);

--
-- Indexes for table `publication`
--
ALTER TABLE `publication`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publication_likes`
--
ALTER TABLE `publication_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`publication_id`,`user_id`),
  ADD KEY `idx_user_likes` (`user_id`),
  ADD KEY `idx_publication_likes` (`publication_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `comment_likes`
--
ALTER TABLE `comment_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `publication`
--
ALTER TABLE `publication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `publication_likes`
--
ALTER TABLE `publication_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD CONSTRAINT `comment_likes_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_publication` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `publication_likes`
--
ALTER TABLE `publication_likes`
  ADD CONSTRAINT `publication_likes_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

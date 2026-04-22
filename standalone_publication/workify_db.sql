-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2026 at 09:37 PM
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
(1, 1, 'James Ortega', 'JO', 'av-green', 'Great work Sarah! The dashboard looks amazing!', 6, NULL, '2026-04-22 18:38:26'),
(2, 1, 'Priya N.', 'PN', 'av-purple', 'Love the design system!', 3, NULL, '2026-04-22 18:38:26'),
(3, 2, 'Sarah K.', 'SK', 'av-blue', 'This is so true! I need to start doing this.', 7, NULL, '2026-04-22 18:38:26'),
(4, 1, 'You', 'YO', 'av-blue', 'sss', 1, NULL, '2026-04-22 18:47:52');

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
  `content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `sender_name`, `receiver_name`, `sender_init`, `receiver_init`, `sender_avatar`, `receiver_avatar`, `content`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 'current_user', 'sarah_k', 'You', 'Sarah K.', 'YO', 'SK', 'av-blue', 'av-blue', 'Hi Sarah! I saw your portfolio, amazing work!', 1, '2026-04-22 18:38:45', '2026-04-22 18:38:45'),
(2, 'sarah_k', 'current_user', 'Sarah K.', 'You', 'SK', 'YO', 'av-blue', 'av-blue', 'Thank you so much! I really appreciate that.', 0, '2026-04-22 18:38:45', '2026-04-22 18:38:45'),
(3, 'current_user', 'sarah_k', 'You', 'Sarah K.', 'YO', 'SK', 'av-blue', 'av-blue', 'Would you be interested in a collaboration?', 1, '2026-04-22 18:38:45', '2026-04-22 18:38:45'),
(4, 'james_o', 'current_user', 'James Ortega', 'You', 'JO', 'YO', 'av-blue', 'av-blue', 'Hey! Just checking in on the project status.', 1, '2026-04-22 18:38:45', '2026-04-22 19:12:48'),
(5, 'current_user', 'james_o', 'You', 'James Ortega', 'YO', 'JO', 'av-blue', 'av-blue', 'Almost done! Will send by tomorrow.', 1, '2026-04-22 18:38:45', '2026-04-22 18:38:45'),
(8, 'current_user', 'moadh', 'You', 'KOLAB', 'YO', 'MO', 'av-blue', 'av-purple', 'HEYYYYY', 0, '2026-04-22 19:14:08', '2026-04-22 19:14:08');

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
  `image_url` varchar(500) DEFAULT NULL,
  `likes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publication`
--

INSERT INTO `publication` (`id`, `user_id`, `user_name`, `user_init`, `user_role`, `user_avatar`, `content`, `has_image`, `image_url`, `likes`, `created_at`, `updated_at`) VALUES
(1, 'current_user', 'Sarah K.', 'SK', 'Freelancer', 'av-blue', 'Just wrapped up a full dashboard redesign for a fintech startup 🎉\r\n\r\nWe went from a cluttered 12-screen mess to a clean, data-focused interface. Biggest lesson: whitespace is not wasted space — it\'s breathing room for the user.\r\n\r\n#UIDesign #Figma #Freelance', 0, NULL, 50, '2026-04-22 18:38:26', '2026-04-22 19:12:42'),
(2, 'current_user', 'James Ortega', 'JO', 'Freelancer', 'av-green', 'Hot take: most freelancers undercharge not because they lack confidence, but because they haven\'t tracked what their work actually generates for clients.\r\n\r\nI started sending ROI summaries after every project. My rates went up 40% within 6 months.\r\n\r\n#Freelance #Pricing #Business', 0, NULL, 112, '2026-04-22 18:38:26', '2026-04-22 18:38:26'),
(3, 'other_user', 'Leo Chen', 'LC', 'Client', 'av-teal', 'We\'re hiring a senior React developer for a 3-month contract starting next month. Remote, competitive rate, interesting product in the logistics space.\r\n\r\nDrop your portfolio in the comments or DM me directly 👇\r\n\r\n#Hiring #ReactJS #Remote #Freelance', 0, NULL, 67, '2026-04-22 18:38:26', '2026-04-22 18:38:26'),
(4, 'current_user', 'You', 'YO', 'Freelancer', 'av-blue', 'Welcome to Workify! Share your thoughts, projects, and opportunities with the community.', 0, NULL, 5, '2026-04-22 18:38:26', '2026-04-22 18:38:26');

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
(7, 'moadh', 'KOLAB', 'MO', 'av-purple', 'Client', '2026-04-22 19:13:54');

-- --------------------------------------------------------

--
-- Table structure for table `user_likes`
--

CREATE TABLE `user_likes` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `publication_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conversation` (`sender_id`,`receiver_id`),
  ADD KEY `idx_receiver_read` (`receiver_id`,`is_read`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_receiver` (`receiver_id`);

--
-- Indexes for table `publication`
--
ALTER TABLE `publication`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `user_likes`
--
ALTER TABLE `user_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`publication_id`),
  ADD KEY `publication_id` (`publication_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `publication`
--
ALTER TABLE `publication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_likes`
--
ALTER TABLE `user_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- Constraints for table `user_likes`
--
ALTER TABLE `user_likes`
  ADD CONSTRAINT `user_likes_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2026 at 03:14 AM
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
  `image_url` varchar(255) DEFAULT NULL,
  `likes` int(11) DEFAULT 0,
  `comments` text DEFAULT NULL,
  `comments_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publication`
--

INSERT INTO `publication` (`id`, `user_id`, `user_name`, `user_init`, `user_role`, `user_avatar`, `content`, `has_image`, `image_url`, `likes`, `comments`, `comments_count`, `created_at`, `updated_at`) VALUES
(3, 'other_user', 'Leo Chen', 'LC', 'Client', 'av-teal', 'We\'re hiring a senior React developer for a 3-month contract starting next month. Remote, competitive rate, interesting product in the logistics space.\r\n\r\nDrop your portfolio in the comments or DM me directly 👇\r\n\r\n#Hiring #ReactJS #Remote #Freelance', 0, NULL, 66, NULL, 0, '2026-04-15 00:44:55', '2026-04-15 01:13:22'),
(4, 'current_user', 'You', 'YO', 'Freelancer', 'av-blue', 'koussay is here', 0, NULL, 1, NULL, 0, '2026-04-15 00:54:24', '2026-04-15 01:13:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `publication`
--
ALTER TABLE `publication`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `publication`
--
ALTER TABLE `publication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

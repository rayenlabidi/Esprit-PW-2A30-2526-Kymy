-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2026 at 02:04 AM
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
-- Table structure for table `candidatures`
--

CREATE TABLE `candidatures` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `cover_letter` text NOT NULL,
  `status` enum('pending','reviewed','accepted','rejected') NOT NULL DEFAULT 'pending',
  `applied_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `candidatures`
--

INSERT INTO `candidatures` (`id`, `user_id`, `job_id`, `cover_letter`, `status`, `applied_at`) VALUES
(1, 2, 1, 'Je peux prendre en charge le projet Workify, integrer les modules et optimiser le rendu pour une demo professeur.', 'reviewed', '2026-04-16 00:07:15');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `scope` enum('all','formation','job') NOT NULL DEFAULT 'all',
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `scope`, `description`) VALUES
(1, 'Developpement Web', 'developpement-web', 'all', 'Frontend, backend et full stack'),
(2, 'UI UX Design', 'ui-ux-design', 'formation', 'Parcours design et prototypage'),
(3, 'Marketing Digital', 'marketing-digital', 'all', 'SEO, paid media et social media'),
(4, 'Data & IA', 'data-ia', 'formation', 'Analyse de donnees et intelligence artificielle'),
(5, 'Support Client', 'support-client', 'job', 'Experience client et assistance'),
(6, 'Product Management', 'product-management', 'job', 'Pilotage produit et delivery');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `publication_id`, `user_name`, `user_init`, `user_avatar`, `comment`, `likes`, `parent_id`, `created_at`) VALUES
(6, 5, 'You', 'YO', 'av-blue', 'heyyyyyyyyy', 0, NULL, '2026-04-15 23:10:06');

-- --------------------------------------------------------

--
-- Table structure for table `formations`
--

CREATE TABLE `formations` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `description` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `level` enum('Beginner','Intermediate','Advanced') NOT NULL DEFAULT 'Beginner',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duration_hours` int(11) NOT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `creator_id` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `formations`
--

INSERT INTO `formations` (`id`, `title`, `description`, `category_id`, `level`, `price`, `duration_hours`, `status`, `creator_id`, `image_url`, `tags`, `created_at`) VALUES
(1, 'Bootcamp Full Stack Laravel', 'Un parcours intensif pour maitriser MVC, MySQL, authentification et dashboards dans une logique de projet concret.', 1, 'Intermediate', 149.00, 42, 'published', 2, 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=1200&q=80', 'php,mysql,laravel,mvc', '2026-04-16 00:07:15'),
(2, 'Masterclass UI UX pour plateformes marketplace', 'Apprenez a creer des interfaces professionnelles pour une application inspiree de Fiverr et Upwork.', 2, 'Advanced', 119.00, 28, 'published', 2, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80', 'ui,ux,figma,marketplace', '2026-04-16 00:07:15'),
(3, 'Introduction a la Data analyse', 'Formation accessible pour debuter avec les tableaux de bord, les KPIs et la lecture des donnees.', 4, 'Beginner', 89.00, 18, 'draft', 1, 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80', 'data,analytics,kpi', '2026-04-16 00:07:15');

-- --------------------------------------------------------

--
-- Table structure for table `inscriptions`
--

CREATE TABLE `inscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL,
  `enrolled_at` datetime NOT NULL DEFAULT current_timestamp(),
  `progress` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inscriptions`
--

INSERT INTO `inscriptions` (`id`, `user_id`, `formation_id`, `enrolled_at`, `progress`) VALUES
(1, 2, 1, '2026-04-16 00:07:15', 35),
(2, 2, 2, '2026-04-16 00:07:15', 10);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `description` text NOT NULL,
  `budget` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `location` varchar(120) NOT NULL,
  `is_remote` tinyint(1) NOT NULL DEFAULT 0,
  `job_type` enum('Freelance','Full-time','Stage','Part-time') NOT NULL DEFAULT 'Freelance',
  `status` enum('open','draft','closed') NOT NULL DEFAULT 'open',
  `publisher_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `description`, `budget`, `category_id`, `location`, `is_remote`, `job_type`, `status`, `publisher_id`, `created_at`) VALUES
(1, 'Developpeur PHP MVC pour plateforme locale', 'Nous cherchons un freelancer capable de finaliser un projet PHP MVC avec sessions, CRUD et validations JS.', 900.00, 1, 'Tunis', 1, 'Freelance', 'open', 3, '2026-04-16 00:07:15'),
(2, 'UX Designer pour espace formation premium', 'Mission sur une interface moderne pour une section de catalogue de formations avec cartes, filtres et details.', 650.00, 6, 'Sousse', 1, 'Part-time', 'open', 3, '2026-04-16 00:07:15'),
(3, 'Assistant marketing junior', 'Suivi de campagnes digitales et production de contenu pour une startup locale.', 550.00, 3, 'Remote', 1, 'Stage', 'draft', 1, '2026-04-16 00:07:15');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `publication`
--

INSERT INTO `publication` (`id`, `user_id`, `user_name`, `user_init`, `user_role`, `user_avatar`, `content`, `has_image`, `image_url`, `likes`, `created_at`, `updated_at`) VALUES
(3, 'other_user', 'Leo Chen', 'LC', 'Client', 'av-teal', 'We\'re hiring a senior React developer for a 3-month contract starting next month. Remote, competitive rate, interesting product in the logistics space.\r\n\r\nDrop your portfolio in the comments or DM me directly 👇\r\n\r\n#Hiring #ReactJS #Remote #Freelance', 0, NULL, 67, '2026-04-15 23:07:15', '2026-04-15 23:07:15'),
(5, 'current_user', 'You', 'YO', 'Freelancer', 'av-blue', 'hello im new here', 0, NULL, 1, '2026-04-15 23:08:00', '2026-04-15 23:08:01'),
(7, 'admin', 'mouhamed', 'MH', 'Client', 'av-orange', ',,,,,,,,hhhhhhhhhhhhhhhhh', 0, NULL, 0, '2026-04-15 23:37:35', '2026-04-15 23:37:35'),
(8, 'current_user', 'You', 'YO', 'Freelancer', 'av-blue', 'heyyyyyyyyyy', 0, '', 1, '2026-04-15 23:40:30', '2026-04-16 00:02:02'),
(10, 'current_user', 'You', 'YO', 'Freelancer', 'av-blue', 'ddddddddddddddd', 0, '/workify/standalone_publication/uploads/69e0273a0803a.png', 0, '2026-04-16 00:03:06', '2026-04-16 00:03:06');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `slug` varchar(40) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Admin', 'admin', 'Gere toute la plateforme'),
(2, 'Freelancer', 'freelancer', 'Suit des formations et postule aux jobs'),
(3, 'Boss', 'boss', 'Publie des jobs et recrute des freelances');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password` varchar(255) NOT NULL,
  `headline` varchar(150) NOT NULL,
  `bio` text NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `status` enum('active','pending','blocked') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `role_id`, `first_name`, `last_name`, `email`, `password`, `headline`, `bio`, `avatar_url`, `status`, `created_at`) VALUES
(1, 1, 'Admin', 'Workify', 'admin@workify.com', '$2y$10$7ALOQvIWzngQAJ/eN3NsS.7HpVWVUVLlxv7KblJL4McnOLEJIKus6', 'Platform administrator', 'Compte admin pour tester toute la plateforme et gerer chaque module.', '', 'active', '2026-04-16 00:07:15'),
(2, 2, 'Sami', 'Freelancer', 'freelancer@workify.com', '$2y$10$8zrsqRyUqyEqdh3xLvEOW.wNgPVfdGPdSFThS54XdcyVY4Oc3b/JO', 'Front-end freelancer', 'Freelancer de demo pour tester les inscriptions et candidatures.', '', 'active', '2026-04-16 00:07:15'),
(3, 3, 'Lina', 'Boss', 'boss@workify.com', '$2y$10$HLpAbAB5hkZFjJmYnlsqNeBUQS186KVB.uhsU8RBO5LyC0WLyzBai', 'Talent recruiter', 'Boss de demo pour publier des jobs et recruter des profils.', '', 'active', '2026-04-16 00:07:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidatures`
--
ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_candidature` (`user_id`,`job_id`),
  ADD KEY `fk_candidatures_job` (`job_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publication_id` (`publication_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `formations`
--
ALTER TABLE `formations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_formations_category` (`category_id`),
  ADD KEY `fk_formations_creator` (`creator_id`);

--
-- Indexes for table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_inscription` (`user_id`,`formation_id`),
  ADD KEY `fk_inscriptions_formation` (`formation_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jobs_category` (`category_id`),
  ADD KEY `fk_jobs_publisher` (`publisher_id`);

--
-- Indexes for table `publication`
--
ALTER TABLE `publication`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_utilisateurs_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `formations`
--
ALTER TABLE `formations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inscriptions`
--
ALTER TABLE `inscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `publication`
--
ALTER TABLE `publication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `fk_candidatures_job` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_candidatures_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `formations`
--
ALTER TABLE `formations`
  ADD CONSTRAINT `fk_formations_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_formations_creator` FOREIGN KEY (`creator_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD CONSTRAINT `fk_inscriptions_formation` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscriptions_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `fk_jobs_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jobs_publisher` FOREIGN KEY (`publisher_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `fk_utilisateurs_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

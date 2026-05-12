-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: WORKIFY
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
-- Current Database: `WORKIFY`
--

/*!40000 DROP DATABASE IF EXISTS `WORKIFY`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `WORKIFY` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `WORKIFY`;

--
-- Table structure for table `apprenant`
--

DROP TABLE IF EXISTS `apprenant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apprenant` (
  `id_apprenant` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(30) NOT NULL,
  PRIMARY KEY (`id_apprenant`),
  UNIQUE KEY `unique_apprenant_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apprenant`
--

LOCK TABLES `apprenant` WRITE;
/*!40000 ALTER TABLE `apprenant` DISABLE KEYS */;
INSERT INTO `apprenant` VALUES (1,'Membre Test','membre.test@workify.tn','22123456'),(2,'Etudiant Workify','etudiant@workify.tn','55123456'),(3,'azeaze azezae','rayanlabidi.rl@icloud.com','541113353');
/*!40000 ALTER TABLE `apprenant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `candidatures`
--

DROP TABLE IF EXISTS `candidatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidatures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `cover_letter` text NOT NULL,
  `cv_url` varchar(255) DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `status` enum('pending','reviewed','accepted','rejected') NOT NULL DEFAULT 'pending',
  `applied_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_candidature` (`user_id`,`job_id`),
  KEY `fk_candidatures_job` (`job_id`),
  CONSTRAINT `fk_candidatures_job` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_candidatures_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidatures`
--

LOCK TABLES `candidatures` WRITE;
/*!40000 ALTER TABLE `candidatures` DISABLE KEYS */;
INSERT INTO `candidatures` VALUES (1,2,1,'Je peux prendre en charge le projet Workify, integrer les modules et optimiser le rendu pour une demo professeur.',NULL,NULL,'reviewed','2026-05-12 14:45:45');
/*!40000 ALTER TABLE `candidatures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorie_formation`
--

DROP TABLE IF EXISTS `categorie_formation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorie_formation` (
  `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
  `nom_categorie` varchar(100) NOT NULL,
  `description_categorie` text DEFAULT NULL,
  PRIMARY KEY (`id_categorie`),
  UNIQUE KEY `unique_nom_categorie` (`nom_categorie`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorie_formation`
--

LOCK TABLES `categorie_formation` WRITE;
/*!40000 ALTER TABLE `categorie_formation` DISABLE KEYS */;
INSERT INTO `categorie_formation` VALUES (1,'Developpement web','Formations PHP, HTML, CSS et JavaScript'),(2,'Base de donnees','Formations MySQL, jointures et conception relationnelle'),(3,'Design UI UX','Formations interfaces, experience utilisateur et prototypage'),(4,'Marketing digital','Formations reseaux sociaux et communication digitale');
/*!40000 ALTER TABLE `categorie_formation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `scope` enum('all','formation','job') NOT NULL DEFAULT 'all',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Developpement Web','developpement-web','all','Frontend, backend et full stack'),(2,'UI UX Design','ui-ux-design','all','Parcours design et prototypage'),(3,'Marketing Digital','marketing-digital','all','SEO, paid media et social media'),(4,'Support Client','support-client','job','Experience client et assistance'),(5,'Product Management','product-management','job','Pilotage produit et delivery');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment_likes`
--

DROP TABLE IF EXISTS `comment_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_comment_like` (`comment_id`,`user_id`),
  KEY `idx_user_comment_likes` (`user_id`),
  CONSTRAINT `comment_likes_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment_likes`
--

LOCK TABLES `comment_likes` WRITE;
/*!40000 ALTER TABLE `comment_likes` DISABLE KEYS */;
INSERT INTO `comment_likes` VALUES (5,10,'sarah_k','2026-04-25 16:44:04');
/*!40000 ALTER TABLE `comment_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_init` varchar(5) NOT NULL,
  `user_avatar` varchar(50) DEFAULT 'av-blue',
  `comment` text NOT NULL,
  `likes` int(11) DEFAULT 0,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `publication_id` (`publication_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (6,8,'You','YO','av-blue','HEYYYY',0,NULL,'2026-04-23 08:30:03'),(7,7,'You','YO','av-blue','heyy',0,NULL,'2026-04-23 09:01:49'),(8,8,'You','YO','av-blue','hhhhhhhhhhhh',0,NULL,'2026-04-23 09:10:00'),(9,8,'You','YO','av-blue','ggg',0,6,'2026-04-23 09:10:11'),(10,9,'You','YO','av-blue','hahahahahaha',1,NULL,'2026-04-25 11:24:41'),(11,9,'You','YO','av-blue','hahahahahaha',0,10,'2026-04-25 11:24:50'),(13,8,'Sarah K.','YO','av-blue','hh',0,6,'2026-04-25 16:44:16'),(14,7,'You','YO','av-blue','dddd',0,7,'2026-04-25 16:45:44'),(15,9,'You','YO','av-blue','heyy',0,10,'2026-04-25 16:57:10'),(16,9,'You','YO','av-blue','kkk',0,10,'2026-04-25 16:57:21');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `subject` varchar(160) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','archived') NOT NULL DEFAULT 'new',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `formateur`
--

DROP TABLE IF EXISTS `formateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formateur` (
  `id_formateur` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `specialite` varchar(120) NOT NULL,
  PRIMARY KEY (`id_formateur`),
  UNIQUE KEY `unique_formateur_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formateur`
--

LOCK TABLES `formateur` WRITE;
/*!40000 ALTER TABLE `formateur` DISABLE KEYS */;
INSERT INTO `formateur` VALUES (1,'Rayen Labidi','rayen.formateur@workify.tn','PHP MVC et PDO'),(2,'Sarra Mansouri','sarra.design@workify.tn','UI UX Design'),(3,'Youssef Ben Ali','youssef.data@workify.tn','MySQL et data');
/*!40000 ALTER TABLE `formateur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `formation`
--

DROP TABLE IF EXISTS `formation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formation` (
  `id_formation` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `duree` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `niveau` varchar(30) NOT NULL,
  `statut` varchar(30) NOT NULL DEFAULT 'planifiee',
  `mode` varchar(30) NOT NULL DEFAULT 'Presentiel',
  `places` int(11) NOT NULL DEFAULT 20,
  `id_categorie` int(11) NOT NULL,
  `id_formateur` int(11) NOT NULL,
  PRIMARY KEY (`id_formation`),
  KEY `fk_formation_categorie` (`id_categorie`),
  KEY `fk_formation_formateur` (`id_formateur`),
  CONSTRAINT `fk_formation_categorie` FOREIGN KEY (`id_categorie`) REFERENCES `categorie_formation` (`id_categorie`) ON UPDATE CASCADE,
  CONSTRAINT `fk_formation_formateur` FOREIGN KEY (`id_formateur`) REFERENCES `formateur` (`id_formateur`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formation`
--

LOCK TABLES `formation` WRITE;
/*!40000 ALTER TABLE `formation` DISABLE KEYS */;
INSERT INTO `formation` VALUES (1,'PHP MVC avec PDO','Objectif general : construire une application MVC simple avec PHP, OOP, PDO, validations JS et structure professor-friendly.','2026-05-02','2026-05-08',18,120.00,'Intermediaire','planifiee','Hybride',24,1,1),(2,'MySQL et jointures','Formation pratique sur les cles primaires, cles etrangeres, relations one-to-many et entites de jointure many-to-many.','2026-05-12','2026-05-14',9,80.00,'Debutant','planifiee','Presentiel',18,2,3),(3,'UI Workify Blue White','Ateliers pour creer des interfaces modernes et coherentes avec sidebar, header, cards et tables lisibles.','2026-05-20','2026-05-22',12,95.00,'Debutant','en_cours','En ligne',30,3,2);
/*!40000 ALTER TABLE `formation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inscription_formation`
--

DROP TABLE IF EXISTS `inscription_formation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inscription_formation` (
  `id_apprenant` int(11) NOT NULL,
  `id_formation` int(11) NOT NULL,
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  `statut` varchar(30) NOT NULL DEFAULT 'en_attente',
  PRIMARY KEY (`id_apprenant`,`id_formation`),
  KEY `fk_inscription_formation` (`id_formation`),
  CONSTRAINT `fk_inscription_apprenant` FOREIGN KEY (`id_apprenant`) REFERENCES `apprenant` (`id_apprenant`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_inscription_formation` FOREIGN KEY (`id_formation`) REFERENCES `formation` (`id_formation`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscription_formation`
--

LOCK TABLES `inscription_formation` WRITE;
/*!40000 ALTER TABLE `inscription_formation` DISABLE KEYS */;
INSERT INTO `inscription_formation` VALUES (1,1,'2026-05-12 14:45:45','acceptee'),(2,1,'2026-05-12 14:45:45','en_attente'),(2,2,'2026-05-12 14:45:45','en_attente'),(3,2,'2026-05-12 18:19:24','en_attente');
/*!40000 ALTER TABLE `inscription_formation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(180) NOT NULL,
  `description` text NOT NULL,
  `budget` decimal(10,2) NOT NULL DEFAULT 0.00,
  `category_id` int(11) NOT NULL,
  `location` varchar(120) NOT NULL,
  `is_remote` tinyint(1) NOT NULL DEFAULT 0,
  `job_type` enum('Freelance','Full-time','Stage','Part-time') NOT NULL DEFAULT 'Freelance',
  `status` enum('open','draft','closed') NOT NULL DEFAULT 'open',
  `publisher_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_jobs_category` (`category_id`),
  KEY `fk_jobs_publisher` (`publisher_id`),
  CONSTRAINT `fk_jobs_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_jobs_publisher` FOREIGN KEY (`publisher_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'Developpeur PHP MVC pour plateforme locale','Nous cherchons un freelancer capable de finaliser un projet PHP MVC avec sessions, CRUD, jointures et validations JS.',900.00,1,'Tunis',1,'Freelance','open',3,'2026-05-12 14:45:45'),(2,'UX Designer pour espace formation premium','Mission sur une interface moderne pour une section de catalogue de formations avec cartes, filtres et details.',650.00,2,'Sousse',1,'Part-time','open',3,'2026-05-12 14:45:45'),(3,'Assistant marketing junior','Suivi de campagnes digitales et production de contenu pour une startup locale.',550.00,3,'Remote',1,'Stage','draft',1,'2026-05-12 14:45:45');
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_conversation` (`sender_id`,`receiver_id`),
  KEY `idx_receiver_read` (`receiver_id`,`is_read`),
  KEY `idx_sender` (`sender_id`),
  KEY `idx_receiver` (`receiver_id`),
  KEY `publication_id` (`publication_id`),
  KEY `idx_is_read` (`is_read`),
  CONSTRAINT `messages_ibfk_publication` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (4,'james_o','current_user','James Ortega','You','JO','YO','av-blue','av-blue',NULL,'Hey! Just checking in on the project status.',1,0,'2026-04-22 18:38:45','2026-04-22 19:12:48'),(5,'current_user','james_o','You','James Ortega','YO','JO','av-blue','av-blue',NULL,'Almost done! Will send by tomorrow.',1,0,'2026-04-22 18:38:45','2026-04-22 18:38:45'),(25,'leo_c','sarah_k','Leo Chen','Sarah K.','LC','SK','av-teal','av-blue',NULL,'heyyyyyyyyyyyyyyyyyy',1,0,'2026-04-25 13:25:20','2026-04-25 13:27:02'),(26,'sarah_k','leo_c','Sarah K.','Leo Chen','SK','LC','av-blue','av-teal',NULL,'hello there',1,0,'2026-04-25 13:27:06','2026-04-25 13:27:29');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publication`
--

DROP TABLE IF EXISTS `publication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publication` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) DEFAULT 'current_user',
  `user_name` varchar(100) NOT NULL,
  `user_init` varchar(5) NOT NULL,
  `user_role` enum('Freelancer','Client') DEFAULT 'Freelancer',
  `user_avatar` varchar(50) DEFAULT 'av-blue',
  `content` text NOT NULL,
  `has_image` tinyint(1) DEFAULT 0,
  `image_url` longtext DEFAULT NULL,
  `likes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publication`
--

LOCK TABLES `publication` WRITE;
/*!40000 ALTER TABLE `publication` DISABLE KEYS */;
INSERT INTO `publication` VALUES (3,'other_user','Leo Chen','LC','Client','av-teal','We\'re hiring a senior React developer for a 3-month contract starting next month. Remote, competitive rate, interesting product in the logistics space.\r\n\r\nDrop your portfolio in the comments or DM me directly 👇\r\n\r\n#Hiring #ReactJS #Remote #Freelance',0,NULL,0,'2026-04-22 18:38:26','2026-04-25 16:56:24'),(4,'current_user','You','YO','Freelancer','av-blue','Welcome to Workify! Share your thoughts, projects, and opportunities with the community.',0,NULL,0,'2026-04-22 18:38:26','2026-04-25 16:56:01'),(5,'admin','mouhamed','MH','Client','av-purple','ssssssssssssssssssssssss',0,'',0,'2026-04-22 23:52:14','2026-04-25 16:56:01'),(7,'admin','deli','DL','Freelancer','av-teal','fddddddddddddddddddd',0,'',1,'2026-04-23 00:09:09','2026-04-25 16:45:40'),(8,'admin','OMAR','OM','Client','av-green','ASLEMA ENA OMAR',0,'',2,'2026-04-23 08:19:02','2026-04-25 16:57:25'),(9,'admin','raghed','RH','Client','av-pink','aslema ena raghed lmoghta',0,'',2,'2026-04-25 11:24:33','2026-04-25 16:57:04');
/*!40000 ALTER TABLE `publication` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publication_likes`
--

DROP TABLE IF EXISTS `publication_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publication_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`publication_id`,`user_id`),
  KEY `idx_user_likes` (`user_id`),
  KEY `idx_publication_likes` (`publication_id`),
  CONSTRAINT `publication_likes_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publication_likes`
--

LOCK TABLES `publication_likes` WRITE;
/*!40000 ALTER TABLE `publication_likes` DISABLE KEYS */;
INSERT INTO `publication_likes` VALUES (3,8,'sarah_k','2026-04-25 16:43:13'),(5,9,'sarah_k','2026-04-25 16:43:20'),(7,7,'current_user','2026-04-25 16:45:40'),(11,9,'current_user','2026-04-25 16:57:04'),(12,8,'current_user','2026-04-25 16:57:25');
/*!40000 ALTER TABLE `publication_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `slug` varchar(40) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','admin','Gere toute la plateforme'),(2,'Freelancer','freelancer','Suit les formations et postule aux jobs'),(3,'Boss','boss','Publie des jobs et recrute des freelances');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `init` varchar(5) NOT NULL,
  `avatar` varchar(50) DEFAULT 'av-blue',
  `role` enum('Freelancer','Client') DEFAULT 'Freelancer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'sarah_k','Sarah K.','SK','av-blue','Freelancer','2026-04-22 18:39:17'),(2,'james_o','James Ortega','JO','av-green','Freelancer','2026-04-22 18:39:17'),(3,'priya_n','Priya N.','PN','av-purple','Freelancer','2026-04-22 18:39:17'),(4,'marcus_l','Marcus L.','ML','av-orange','Client','2026-04-22 18:39:17'),(5,'aisha_t','Aisha T.','AT','av-pink','Freelancer','2026-04-22 18:39:17'),(6,'leo_c','Leo Chen','LC','av-teal','Client','2026-04-22 18:39:17'),(7,'moadh','KOLAB','MO','av-purple','Client','2026-04-22 19:13:54'),(12,'raghed','ftouhi','RH','av-pink','Client','2026-04-25 11:25:47');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `headline` varchar(150) NOT NULL,
  `bio` text NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `status` enum('active','pending','blocked') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_utilisateurs_role` (`role_id`),
  CONSTRAINT `fk_utilisateurs_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurs`
--

LOCK TABLES `utilisateurs` WRITE;
/*!40000 ALTER TABLE `utilisateurs` DISABLE KEYS */;
INSERT INTO `utilisateurs` VALUES (1,1,'Equipe','Workify','admin@workify.com','22822870','$2y$10$7ALOQvIWzngQAJ/eN3NsS.7HpVWVUVLlxv7KblJL4McnOLEJIKus6','Responsable espace prive','Compte de gestion pour suivre les modules Workify.','','active','2026-05-12 14:45:45'),(2,2,'Sami','Freelancer','freelancer@workify.com','20606058','$2y$10$8zrsqRyUqyEqdh3xLvEOW.wNgPVfdGPdSFThS54XdcyVY4Oc3b/JO','Front-end freelancer','Freelancer de demo pour tester les candidatures.','','active','2026-05-12 14:45:45'),(3,3,'Lina','Boss','boss@workify.com','55123456','$2y$10$HLpAbAB5hkZFjJmYnlsqNeBUQS186KVB.uhsU8RBO5LyC0WLyzBai','Talent recruiter','Boss de demo pour publier des jobs et recruter des profils.','','active','2026-05-12 14:45:45'),(4,1,'rayen','laabidi','rayanlabidi.rl@gmail.com','52574198','$2y$10$A3GRoO40P2DnuTDGFBY6keYCphIYWke1Eo.PccJKEGxcDFzRw3TT6','test','zklmfslkfjsldfnslkjfnsdklf',NULL,'active','2026-05-12 14:46:36'),(6,2,'azeaze','azezae','rayanlabidi.rl@icloud.com','541113353','$2y$10$wYihqIK4alJiqgJ0FkMv3.u1vrrDyGKbefge3l1Ir.x/Somvwplwu','zefdzedz','Compte cree depuis l inscription publique Workify.',NULL,'active','2026-05-12 18:18:32');
/*!40000 ALTER TABLE `utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'WORKIFY'
--

--
-- Dumping routines for database 'WORKIFY'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-12 19:05:49

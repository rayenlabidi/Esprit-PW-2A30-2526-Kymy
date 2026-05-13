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

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `WORKIFY` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `WORKIFY`;

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
INSERT INTO `comments` VALUES (6,8,'Sami Freelancer','SF','av-blue','Je suis interesse, je peux envoyer mon portfolio aujourd hui.',0,NULL,'2026-04-23 08:30:03'),(7,7,'Lina Boss','LB','av-blue','Merci pour le partage, votre experience front-end est interessante.',0,NULL,'2026-04-23 09:01:49'),(8,8,'Equipe Workify','EW','av-blue','Pensez a completer votre profil avant de postuler.',0,NULL,'2026-04-23 09:10:00'),(9,8,'Aziz Messaoud','AM','av-blue','Je peux aider sur la partie SQL si besoin.',0,6,'2026-04-23 09:10:11'),(10,9,'Sami Freelancer','SF','av-blue','Bonne idee, je vais ajouter mes disponibilites.',1,NULL,'2026-04-25 11:24:41'),(11,9,'Lina Boss','LB','av-blue','Un portfolio clair fait vraiment la difference.',0,10,'2026-04-25 11:24:50'),(13,8,'Equipe Workify','EW','av-blue','Publication utile pour les nouveaux membres.',0,6,'2026-04-25 16:44:16'),(14,7,'Aziz Messaoud','AM','av-blue','Je confirme, les projets MVC sont tres demandes.',0,7,'2026-04-25 16:45:44'),(15,9,'Sami Freelancer','SF','av-blue','Disponible pour echanger cette semaine.',0,10,'2026-04-25 16:57:10'),(16,9,'Lina Boss','LB','av-blue','Merci, je vous contacte en message prive.',0,10,'2026-04-25 16:57:21');
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
-- Table structure for table `event_categories`
--

DROP TABLE IF EXISTS `event_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_event_categories_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_categories`
--

LOCK TABLES `event_categories` WRITE;
/*!40000 ALTER TABLE `event_categories` DISABLE KEYS */;
INSERT INTO `event_categories` VALUES (1,'Intelligence Artificielle & ML','Evenements dedies a l IA generative, machine learning, LLMs, automatisation intelligente et ethique de l IA.','2026-05-12 23:16:33'),(2,'Cybersecurite & Souverainete','Ateliers et conferences sur la securite des systemes, pentest, zero-trust, RGPD et souverainete numerique.','2026-05-12 23:16:33'),(3,'Web3 & Blockchain','Meetups autour de la DeFi, NFTs, smart contracts, DAOs et economie decentralisee.','2026-05-12 23:16:33'),(4,'DevOps & Cloud Native','Sessions sur Kubernetes, GitOps, infrastructure as code, observabilite et architectures cloud-native.','2026-05-12 23:16:33'),(5,'Realite Augmentee & Metavers','Demos, hackathons et conferences XR, AR, VR, MR, spatial computing et plateformes metavers.','2026-05-12 23:16:33'),(6,'Biotech & HealthTech','Evenements a l intersection de la biologie, la medecine et la technologie: genomique, telemedecine et IA medicale.','2026-05-12 23:16:33'),(7,'GreenTech & Tech Durable','Conferences sur la tech au service de l environnement, la sobriete numerique et les energies renouvelables.','2026-05-12 23:16:33'),(8,'Product & UX Design','Ateliers design thinking, prototypage, recherche utilisateur et strategie produit.','2026-05-12 23:16:33'),(9,'FinTech & InsurTech','Conferences sur la finance numerique, open banking, paiements, neo-banques et assurance technologique.','2026-05-12 23:16:33'),(10,'Robotique & IoT Industriel','Evenements sur la robotique collaborative, IIoT, industrie 4.0 et jumeaux numeriques.','2026-05-12 23:16:33'),(11,'No-Code & Citizen Development','Ateliers pour creer des applications sans code avec Bubble, Webflow, Make et democratiser le developpement.','2026-05-12 23:16:33'),(12,'Quantum Computing','Seminaires et workshops sur informatique quantique, ses algorithmes et ses applications futures.','2026-05-12 23:16:33');
/*!40000 ALTER TABLE `event_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(180) NOT NULL,
  `description` text NOT NULL,
  `event_date` datetime NOT NULL,
  `location` varchar(255) NOT NULL,
  `is_online` tinyint(1) NOT NULL DEFAULT 0,
  `max_participants` int(11) NOT NULL DEFAULT 50,
  `status` enum('upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  `organizer_id` int(11) NOT NULL,
  `event_category_id` int(11) DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_events_date` (`event_date`),
  KEY `idx_events_status` (`status`),
  KEY `idx_events_organizer` (`organizer_id`),
  KEY `idx_events_category` (`event_category_id`),
  CONSTRAINT `fk_events_category` FOREIGN KEY (`event_category_id`) REFERENCES `event_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_events_organizer` FOREIGN KEY (`organizer_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'Workify AI Career Night','Session pratique pour comprendre comment utiliser l IA dans les candidatures, portfolios et missions freelance.','2026-06-02 18:00:00','Esprit, Ariana',0,80,'upcoming',3,1,'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1200&q=80',36.8997000,10.1899000,'2026-05-13 16:54:43','2026-05-13 16:54:43'),(2,'UX Portfolio Sprint','Atelier intensif pour transformer un projet etudiant en portfolio clair, moderne et presentable aux clients.','2026-06-10 10:00:00','Online Workshop',1,120,'upcoming',3,8,'https://images.unsplash.com/photo-1559028012-481c04fa702d?auto=format&fit=crop&w=1200&q=80',NULL,NULL,'2026-05-13 16:54:43','2026-05-13 16:54:43'),(3,'Cloud DevOps Meetup','Rencontre autour du deploiement, des environnements de test et des bonnes pratiques pour livrer plus vite.','2026-06-18 15:30:00','Technopole El Ghazala',0,60,'upcoming',3,4,'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1200&q=80',36.8951000,10.1885000,'2026-05-13 16:54:43','2026-05-13 16:54:43');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
  `image_url` longtext DEFAULT NULL,
  PRIMARY KEY (`id_formation`),
  KEY `fk_formation_categorie` (`id_categorie`),
  KEY `fk_formation_formateur` (`id_formateur`),
  CONSTRAINT `fk_formation_categorie` FOREIGN KEY (`id_categorie`) REFERENCES `categorie_formation` (`id_categorie`) ON UPDATE CASCADE,
  CONSTRAINT `fk_formation_formateur` FOREIGN KEY (`id_formateur`) REFERENCES `formateur` (`id_formateur`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formation`
--

LOCK TABLES `formation` WRITE;
/*!40000 ALTER TABLE `formation` DISABLE KEYS */;
INSERT INTO `formation` VALUES (1,'PHP MVC avec PDO','Objectif general : construire une application MVC simple avec PHP, OOP, PDO, validations JS et structure professor-friendly.','2026-05-02','2026-05-08',18,120.00,'Intermediaire','planifiee','Hybride',24,1,1,'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1200&q=80'),(2,'MySQL et jointures','Formation pratique sur les cles primaires, cles etrangeres, relations one-to-many et entites de jointure many-to-many.','2026-05-12','2026-05-14',9,80.00,'Debutant','planifiee','Presentiel',18,2,3,'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1200&q=80'),(3,'UI Workify Blue White','Ateliers pour creer des interfaces modernes et coherentes avec sidebar, header, cards et tables lisibles.','2026-05-20','2026-05-22',12,95.00,'Debutant','en_cours','En ligne',30,3,2,'https://images.unsplash.com/photo-1559028012-481c04fa702d?auto=format&fit=crop&w=1200&q=80');
/*!40000 ALTER TABLE `formation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inscription_formation`
--

DROP TABLE IF EXISTS `inscription_formation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inscription_formation` (
  `user_id` int(11) NOT NULL,
  `id_formation` int(11) NOT NULL,
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  `statut` varchar(30) NOT NULL DEFAULT 'en_attente',
  PRIMARY KEY (`user_id`,`id_formation`),
  KEY `fk_inscription_formation` (`id_formation`),
  CONSTRAINT `fk_inscription_formation` FOREIGN KEY (`id_formation`) REFERENCES `formation` (`id_formation`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_inscription_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscription_formation`
--

LOCK TABLES `inscription_formation` WRITE;
/*!40000 ALTER TABLE `inscription_formation` DISABLE KEYS */;
INSERT INTO `inscription_formation` VALUES (1,3,'2026-05-13 17:24:15','en_attente'),(6,2,'2026-05-12 18:19:24','en_attente'),(8,1,'2026-05-12 22:25:05','en_attente'),(8,2,'2026-05-12 22:25:01','en_attente'),(8,3,'2026-05-12 22:24:55','en_attente'),(9,1,'2026-05-12 14:45:45','acceptee'),(10,1,'2026-05-12 14:45:45','en_attente'),(10,2,'2026-05-12 14:45:45','en_attente');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'Developpeur PHP MVC pour plateforme locale','Nous cherchons un freelancer capable de finaliser un projet PHP MVC avec sessions, CRUD, jointures et validations JS.',900.00,1,'Tunis',1,'Freelance','open',3,'2026-05-12 14:45:45'),(2,'UX Designer pour espace formation premium','Mission sur une interface moderne pour une section de catalogue de formations avec cartes, filtres et details.',650.00,2,'Sousse',1,'Part-time','open',3,'2026-05-12 14:45:45'),(3,'Assistant marketing junior','Suivi de campagnes digitales et production de contenu pour une startup locale.',550.00,3,'Remote',1,'Stage','draft',1,'2026-05-12 14:45:45'),(4,'sdfsfsd','sdfsddfqdsfqd qdf sdf qdfqf qdfqdfq df qds fqdf',989456.00,5,'qqdsfqs fq',0,'Full-time','open',12,'2026-05-13 17:33:51');
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
INSERT INTO `messages` VALUES (4,'3','2','Lina Boss','Sami Freelancer','LB','SF','av-blue','av-blue',NULL,'Bonjour Sami, votre profil correspond a notre mission React. Etes-vous disponible cette semaine ?',1,0,'2026-04-22 18:38:45','2026-05-13 11:30:23'),(5,'2','3','Sami Freelancer','Lina Boss','SF','LB','av-blue','av-blue',NULL,'Bonjour Lina, oui je suis disponible. Je peux envoyer mon portfolio et un court planning ce soir.',1,0,'2026-04-22 18:38:45','2026-05-13 11:30:23'),(25,'8','2','Aziz Messaoud','Sami Freelancer','AM','SF','av-teal','av-blue',NULL,'Salut Sami, tu peux me partager ton retour sur la structure MVC utilisee dans ton dernier projet ?',1,0,'2026-04-25 13:25:20','2026-05-13 11:30:23'),(26,'2','8','Sami Freelancer','Aziz Messaoud','SF','AM','av-blue','av-teal',NULL,'Bien sur Aziz, je te prepare un exemple propre avec modeles, controleurs et vues separes.',1,0,'2026-04-25 13:27:06','2026-05-13 11:30:23');
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
INSERT INTO `publication` VALUES (3,'3','Lina Boss','LB','Client','av-teal','Nous cherchons un developpeur React disponible pour une mission de 3 mois. Le projet touche a la logistique, au suivi client et a la qualite des interfaces. Les profils avec portfolio peuvent nous contacter directement.',0,NULL,0,'2026-04-22 18:38:26','2026-05-13 11:30:23'),(4,'2','Sami Freelancer','SF','Freelancer','av-blue','Je viens de terminer une interface responsive pour un tableau de bord client. Disponible cette semaine pour des missions front-end, integration PHP MVC et amelioration UX.',0,NULL,0,'2026-04-22 18:38:26','2026-05-13 11:30:23'),(5,'1','Equipe Workify','EW','Client','av-purple','Bienvenue sur le feed Workify. Partagez vos opportunites, vos questions et vos disponibilites avec la communaute.',0,NULL,0,'2026-04-22 23:52:14','2026-05-13 11:30:23'),(7,'8','Aziz Messaoud','AM','Freelancer','av-teal','Disponible pour aider sur des formations web, creation de supports et correction de bugs PHP. Je peux aussi accompagner les apprenants sur SQL et MVC.',0,NULL,1,'2026-04-23 00:09:09','2026-05-13 11:30:23'),(8,'3','Lina Boss','LB','Client','av-green','Nous preparons une mission UI pour une startup locale. Besoin d un profil capable de transformer des maquettes simples en pages propres et animees.',0,NULL,2,'2026-04-23 08:19:02','2026-05-13 11:30:23'),(9,'2','Sami Freelancer','SF','Freelancer','av-pink','Conseil du jour: pour une candidature Workify, ajoutez un message court, un lien portfolio et une disponibilite claire. Cela aide les clients a repondre plus vite.',0,NULL,2,'2026-04-25 11:24:33','2026-05-13 11:30:23');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','admin','Gere toute la plateforme'),(2,'Freelancer','freelancer','Suit les formations et postule aux jobs'),(3,'Boss','boss','Publie des jobs et recrute des freelances'),(4,'Enterprise','enterprise','Publie et gere les evenements professionnels');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurs`
--

LOCK TABLES `utilisateurs` WRITE;
/*!40000 ALTER TABLE `utilisateurs` DISABLE KEYS */;
INSERT INTO `utilisateurs` VALUES (1,1,'Equipe','Workify','admin@workify.com','22822870','$2y$10$7ALOQvIWzngQAJ/eN3NsS.7HpVWVUVLlxv7KblJL4McnOLEJIKus6','Responsable espace prive','Compte de gestion pour suivre les modules Workify.','','active','2026-05-12 14:45:45'),(2,2,'Sami','Freelancer','freelancer@workify.com','20606058','$2y$10$8zrsqRyUqyEqdh3xLvEOW.wNgPVfdGPdSFThS54XdcyVY4Oc3b/JO','Front-end freelancer','Freelancer de demo pour tester les candidatures.','','active','2026-05-12 14:45:45'),(3,3,'Lina','Boss','boss@workify.com','55123456','$2y$10$HLpAbAB5hkZFjJmYnlsqNeBUQS186KVB.uhsU8RBO5LyC0WLyzBai','Talent recruiter','Boss de demo pour publier des jobs et recruter des profils.','','active','2026-05-12 14:45:45'),(4,1,'rayen','laabidi','rayanlabidi.rl@gmail.com','52574198','$2y$10$A3GRoO40P2DnuTDGFBY6keYCphIYWke1Eo.PccJKEGxcDFzRw3TT6','test','zklmfslkfjsldfnslkjfnsdklf',NULL,'active','2026-05-12 14:46:36'),(6,2,'azeaze','azezae','rayanlabidi.rl@icloud.com','541113353','$2y$10$CWYVE8MrPBA6bxkI/A1ACeA0bIabbkscgBahFjsq1fDObve7Hsr8y','zefdzedz','Compte cree depuis l inscription publique Workify.',NULL,'active','2026-05-12 18:18:32'),(7,2,'wassim','byk','wassoubenyakhlef@gmail.com','13451531323','$2y$10$4572QhNJiTVTZMpYVlQLpOhyxNLYIii4HoNlgNMYK7PEU/Kt35lzq','4545646545','Compte cree depuis l inscription publique Workify.',NULL,'active','2026-05-12 22:21:20'),(8,2,'aziz','messaoud','messaoudaziz900@gmail.com','456431325','$2y$10$I2snUNrn0YU5rNLkcFjEQOYpR4YDbbOJigtEUyQYmqwt/rYD2JlDC','Talent Workify','Compte cree depuis l inscription publique Workify.',NULL,'active','2026-05-12 22:24:04'),(9,2,'Membre','Test','membre.test@workify.tn','22123456','$2y$10$8zrsqRyUqyEqdh3xLvEOW.wNgPVfdGPdSFThS54XdcyVY4Oc3b/JO','Apprenant Workify','Compte migre depuis les inscriptions formation.','','active','2026-05-12 22:52:32'),(10,2,'Etudiant','Workify','etudiant@workify.tn','55123456','$2y$10$8zrsqRyUqyEqdh3xLvEOW.wNgPVfdGPdSFThS54XdcyVY4Oc3b/JO','Apprenant Workify','Compte migre depuis les inscriptions formation.','','active','2026-05-12 22:52:32'),(12,3,'azeaze','azeaze','azer@ty.com','4546541651','$2y$10$SbAQVGsDzdUb.WVuTRGfhukYqK.iE197e6WFnXv6nQ.QL3RWoogkG','azeazeaz','Compte cree depuis l inscription publique Workify.',NULL,'active','2026-05-13 17:33:02');
/*!40000 ALTER TABLE `utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-13 17:56:13

--
-- Table structure for table `event_registrations`
--

DROP TABLE IF EXISTS `event_registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_event_user` (`event_id`,`user_id`),
  KEY `fk_event_registrations_user` (`user_id`),
  CONSTRAINT `fk_event_registrations_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_event_registrations_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_registrations`
--

LOCK TABLES `event_registrations` WRITE;
/*!40000 ALTER TABLE `event_registrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_registrations` ENABLE KEYS */;
UNLOCK TABLES;
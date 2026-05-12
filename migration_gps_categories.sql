-- ============================================================
-- Workify migration: events GPS columns + advanced categories
-- Database: WORKIFY
-- ============================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS event_categories (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  description TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_event_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS events (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  event_date DATETIME NOT NULL,
  location VARCHAR(255) NOT NULL,
  is_online TINYINT(1) NOT NULL DEFAULT 0,
  max_participants INT(11) NOT NULL DEFAULT 50,
  status ENUM('upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  organizer_id INT(11) NOT NULL,
  event_category_id INT(11) DEFAULT NULL,
  image_url TEXT DEFAULT NULL,
  latitude DECIMAL(10,7) NULL DEFAULT NULL,
  longitude DECIMAL(10,7) NULL DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_events_date (event_date),
  KEY idx_events_status (status),
  KEY idx_events_organizer (organizer_id),
  KEY idx_events_category (event_category_id),
  CONSTRAINT fk_events_organizer FOREIGN KEY (organizer_id) REFERENCES utilisateurs (id) ON DELETE CASCADE,
  CONSTRAINT fk_events_category FOREIGN KEY (event_category_id) REFERENCES event_categories (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE events
  ADD COLUMN IF NOT EXISTS latitude DECIMAL(10,7) NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS longitude DECIMAL(10,7) NULL DEFAULT NULL;

INSERT INTO event_categories (name, description)
SELECT 'Intelligence Artificielle & ML', 'Evenements dedies a l IA generative, machine learning, LLMs, automatisation intelligente et ethique de l IA.'
WHERE NOT EXISTS (SELECT 1 FROM event_categories WHERE name = 'Intelligence Artificielle & ML');

INSERT INTO event_categories (name, description)
SELECT 'Cybersecurite & Souverainete', 'Ateliers et conferences sur la securite des systemes, pentest, zero-trust, RGPD et souverainete numerique.'
WHERE NOT EXISTS (SELECT 1 FROM event_categories WHERE name = 'Cybersecurite & Souverainete');

INSERT INTO event_categories (name, description)
SELECT 'Web3 & Blockchain', 'Meetups autour de la DeFi, NFTs, smart contracts, DAOs et economie decentralisee.'
WHERE NOT EXISTS (SELECT 1 FROM event_categories WHERE name = 'Web3 & Blockchain');

INSERT INTO event_categories (name, description)
SELECT 'DevOps & Cloud Native', 'Sessions sur Kubernetes, GitOps, infrastructure as code, observabilite et architectures cloud-native.'
WHERE NOT EXISTS (SELECT 1 FROM event_categories WHERE name = 'DevOps & Cloud Native');

INSERT INTO event_categories (name, description)
SELECT 'Realite Augmentee & Metavers', 'Demos, hackathons et conferences XR, AR, VR, MR, spatial computing et plateformes metavers.'
WHERE NOT EXISTS (SELECT 1 FROM event_categories WHERE name = 'Realite Augmentee & Metavers');

INSERT INTO event_categories (name, description)
SELECT 'Biotech & HealthTech', 'Evenements a l intersection de la biologie, la medecine et la technologie: genomique, telemedecine et IA medicale.'
WHERE NOT EXISTS (SELECT 1 FROM event_categories WHERE name = 'Biotech & HealthTech');

INSERT INTO event_categories (name, description)
SELECT 'GreenTech & Tech Durable', 'Conferences sur la tech au service de l environnement, la sobriete numerique et les energies renouvelables.'
WHERE NOT EXISTS (SELECT 1 FROM event_categories WHERE name = 'GreenTech & Tech Durable');

INSERT INTO event_categories (name, description)
SELECT 'Product & UX Design', 'Ateliers design thinking, prototypage, recherche utilisateur et strategie produit.'
WHERE NOT EXISTS (SELECT 1 FROM event_categories WHERE name = 'Product & UX Design');

INSERT INTO event_categories (name, description)
SELECT 'FinTech & InsurTech', 'Conferences sur la finance numerique, open banking, paiements, neo-banques et assurance technologique.'
WHERE NOT EXISTS (SELECT 1 FROM event_categories WHERE name = 'FinTech & InsurTech');

INSERT INTO event_categories (name, description)
SELECT 'Robotique & IoT Industriel', 'Evenements sur la robotique collaborative, IIoT, industrie 4.0 et jumeaux numeriques.'
WHERE NOT EXISTS (SELECT 1 FROM event_categories WHERE name = 'Robotique & IoT Industriel');

INSERT INTO event_categories (name, description)
SELECT 'No-Code & Citizen Development', 'Ateliers pour creer des applications sans code avec Bubble, Webflow, Make et democratiser le developpement.'
WHERE NOT EXISTS (SELECT 1 FROM event_categories WHERE name = 'No-Code & Citizen Development');

INSERT INTO event_categories (name, description)
SELECT 'Quantum Computing', 'Seminaires et workshops sur informatique quantique, ses algorithmes et ses applications futures.'
WHERE NOT EXISTS (SELECT 1 FROM event_categories WHERE name = 'Quantum Computing');

CREATE DATABASE IF NOT EXISTS `2a30` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `2a30`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS candidatures;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS utilisateurs;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS inscription_formation;
DROP TABLE IF EXISTS apprenant;
DROP TABLE IF EXISTS formation;
DROP TABLE IF EXISTS formateur;
DROP TABLE IF EXISTS categorie_formation;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE categorie_formation (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    nom_categorie VARCHAR(100) NOT NULL,
    description_categorie TEXT NULL,
    UNIQUE KEY unique_nom_categorie (nom_categorie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE formateur (
    id_formateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    specialite VARCHAR(120) NOT NULL,
    UNIQUE KEY unique_formateur_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE formation (
    id_formation INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    duree INT NOT NULL,
    prix DECIMAL(10, 2) NOT NULL DEFAULT 0,
    niveau VARCHAR(30) NOT NULL,
    statut VARCHAR(30) NOT NULL DEFAULT 'planifiee',
    mode VARCHAR(30) NOT NULL DEFAULT 'Presentiel',
    places INT NOT NULL DEFAULT 20,
    id_categorie INT NOT NULL,
    id_formateur INT NOT NULL,
    CONSTRAINT fk_formation_categorie
        FOREIGN KEY (id_categorie)
        REFERENCES categorie_formation(id_categorie)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_formation_formateur
        FOREIGN KEY (id_formateur)
        REFERENCES formateur(id_formateur)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE apprenant (
    id_apprenant INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telephone VARCHAR(30) NOT NULL,
    UNIQUE KEY unique_apprenant_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE inscription_formation (
    id_apprenant INT NOT NULL,
    id_formation INT NOT NULL,
    date_inscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(30) NOT NULL DEFAULT 'en_attente',
    PRIMARY KEY (id_apprenant, id_formation),
    CONSTRAINT fk_inscription_apprenant
        FOREIGN KEY (id_apprenant)
        REFERENCES apprenant(id_apprenant)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_inscription_formation
        FOREIGN KEY (id_formation)
        REFERENCES formation(id_formation)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO categorie_formation (id_categorie, nom_categorie, description_categorie) VALUES
(1, 'Developpement web', 'Formations PHP, HTML, CSS et JavaScript'),
(2, 'Base de donnees', 'Formations MySQL, jointures et conception relationnelle'),
(3, 'Design UI UX', 'Formations interfaces, experience utilisateur et prototypage'),
(4, 'Marketing digital', 'Formations reseaux sociaux et communication digitale');

INSERT INTO formateur (id_formateur, nom, email, specialite) VALUES
(1, 'Rayen Labidi', 'rayen.formateur@workify.tn', 'PHP MVC et PDO'),
(2, 'Sarra Mansouri', 'sarra.design@workify.tn', 'UI UX Design'),
(3, 'Youssef Ben Ali', 'youssef.data@workify.tn', 'MySQL et data');

INSERT INTO formation (id_formation, titre, description, date_debut, date_fin, duree, prix, niveau, statut, mode, places, id_categorie, id_formateur) VALUES
(1, 'PHP MVC avec PDO', 'Objectif general : construire une application MVC simple avec PHP, OOP, PDO, validations JS et structure professor-friendly.', '2026-05-02', '2026-05-08', 18, 120.00, 'Intermediaire', 'planifiee', 'Hybride', 24, 1, 1),
(2, 'MySQL et jointures', 'Formation pratique sur les cles primaires, cles etrangeres, relations one-to-many et entites de jointure many-to-many.', '2026-05-12', '2026-05-14', 9, 80.00, 'Debutant', 'planifiee', 'Presentiel', 18, 2, 3),
(3, 'UI Workify Blue White', 'Ateliers pour creer des interfaces modernes et coherentes avec sidebar, header, cards et tables lisibles.', '2026-05-20', '2026-05-22', 12, 95.00, 'Debutant', 'en_cours', 'En ligne', 30, 3, 2);

INSERT INTO apprenant (id_apprenant, nom, email, telephone) VALUES
(1, 'Membre Test', 'membre.test@workify.tn', '22123456'),
(2, 'Etudiant Workify', 'etudiant@workify.tn', '55123456');

INSERT INTO inscription_formation (id_apprenant, id_formation, statut) VALUES
(1, 1, 'acceptee'),
(2, 1, 'en_attente'),
(2, 2, 'en_attente');

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL,
    slug VARCHAR(40) NOT NULL UNIQUE,
    description VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    phone VARCHAR(40) NULL,
    password VARCHAR(255) NOT NULL,
    headline VARCHAR(150) NOT NULL,
    bio TEXT NOT NULL,
    avatar_url VARCHAR(255) NULL,
    status ENUM('active', 'pending', 'blocked') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_utilisateurs_role
        FOREIGN KEY (role_id)
        REFERENCES roles(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    scope ENUM('all', 'formation', 'job') NOT NULL DEFAULT 'all',
    description VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    budget DECIMAL(10, 2) NOT NULL DEFAULT 0,
    category_id INT NOT NULL,
    location VARCHAR(120) NOT NULL,
    is_remote TINYINT(1) NOT NULL DEFAULT 0,
    job_type ENUM('Freelance', 'Full-time', 'Stage', 'Part-time') NOT NULL DEFAULT 'Freelance',
    status ENUM('open', 'draft', 'closed') NOT NULL DEFAULT 'open',
    publisher_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_jobs_category
        FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_jobs_publisher
        FOREIGN KEY (publisher_id)
        REFERENCES utilisateurs(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE candidatures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    job_id INT NOT NULL,
    cover_letter TEXT NOT NULL,
    cv_url VARCHAR(255) NULL,
    photo_url VARCHAR(255) NULL,
    status ENUM('pending', 'reviewed', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
    applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_candidature (user_id, job_id),
    CONSTRAINT fk_candidatures_user
        FOREIGN KEY (user_id)
        REFERENCES utilisateurs(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_candidatures_job
        FOREIGN KEY (job_id)
        REFERENCES jobs(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(160) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'archived') NOT NULL DEFAULT 'new',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO roles (id, name, slug, description) VALUES
(1, 'Admin', 'admin', 'Gere toute la plateforme'),
(2, 'Freelancer', 'freelancer', 'Suit les formations et postule aux jobs'),
(3, 'Boss', 'boss', 'Publie des jobs et recrute des freelances');

INSERT INTO utilisateurs (id, role_id, first_name, last_name, email, phone, password, headline, bio, avatar_url, status) VALUES
(1, 1, 'Equipe', 'Workify', 'admin@workify.com', '22822870', '$2y$10$7ALOQvIWzngQAJ/eN3NsS.7HpVWVUVLlxv7KblJL4McnOLEJIKus6', 'Responsable espace prive', 'Compte de gestion pour suivre les modules Workify.', '', 'active'),
(2, 2, 'Sami', 'Freelancer', 'freelancer@workify.com', '20606058', '$2y$10$8zrsqRyUqyEqdh3xLvEOW.wNgPVfdGPdSFThS54XdcyVY4Oc3b/JO', 'Front-end freelancer', 'Freelancer de demo pour tester les candidatures.', '', 'active'),
(3, 3, 'Lina', 'Boss', 'boss@workify.com', '55123456', '$2y$10$HLpAbAB5hkZFjJmYnlsqNeBUQS186KVB.uhsU8RBO5LyC0WLyzBai', 'Talent recruiter', 'Boss de demo pour publier des jobs et recruter des profils.', '', 'active');

INSERT INTO categories (id, name, slug, scope, description) VALUES
(1, 'Developpement Web', 'developpement-web', 'all', 'Frontend, backend et full stack'),
(2, 'UI UX Design', 'ui-ux-design', 'all', 'Parcours design et prototypage'),
(3, 'Marketing Digital', 'marketing-digital', 'all', 'SEO, paid media et social media'),
(4, 'Support Client', 'support-client', 'job', 'Experience client et assistance'),
(5, 'Product Management', 'product-management', 'job', 'Pilotage produit et delivery');

INSERT INTO jobs (id, title, description, budget, category_id, location, is_remote, job_type, status, publisher_id) VALUES
(1, 'Developpeur PHP MVC pour plateforme locale', 'Nous cherchons un freelancer capable de finaliser un projet PHP MVC avec sessions, CRUD, jointures et validations JS.', 900.00, 1, 'Tunis', 1, 'Freelance', 'open', 3),
(2, 'UX Designer pour espace formation premium', 'Mission sur une interface moderne pour une section de catalogue de formations avec cartes, filtres et details.', 650.00, 2, 'Sousse', 1, 'Part-time', 'open', 3),
(3, 'Assistant marketing junior', 'Suivi de campagnes digitales et production de contenu pour une startup locale.', 550.00, 3, 'Remote', 1, 'Stage', 'draft', 1);

INSERT INTO candidatures (id, user_id, job_id, cover_letter, status) VALUES
(1, 2, 1, 'Je peux prendre en charge le projet Workify, integrer les modules et optimiser le rendu pour une demo professeur.', 'reviewed');

INSERT INTO contact_messages (id, full_name, email, subject, message, status) VALUES
(1, 'Nour Ben Salem', 'nour@example.com', 'Besoin d un formateur', 'Je souhaite organiser une session PHP MVC pour mon equipe.', 'new');

CREATE DATABASE IF NOT EXISTS `2a30` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `2a30`;

SET FOREIGN_KEY_CHECKS = 0;
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

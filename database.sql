CREATE DATABASE IF NOT EXISTS workify_group_db;
USE workify_group_db;

-- ==========================================
-- TASK 1: GESTION DES FORMATIONS (Your Task)
-- ==========================================

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS formations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    duree INT NOT NULL COMMENT 'duration in hours',
    id_categorie INT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    -- JOINTURE / RELATION: Links formations to categories
    FOREIGN KEY (id_categorie) REFERENCES categories(id) ON DELETE SET NULL
);

INSERT INTO categories (nom, description) VALUES 
('Développement Web', 'Cours sur la programmation web, frontend et backend.'),
('Design', 'Cours sur le design UI/UX, création graphique.'),
('Marketing', 'Digital marketing, SEO, SEA.');


-- ==========================================
-- TASK 2: GESTION DES UTILISATEURS (Friend's Task)
-- ==========================================

CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_role VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    id_role INT,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    -- JOINTURE / RELATION: Links utilisateurs to roles
    FOREIGN KEY (id_role) REFERENCES roles(id) ON DELETE SET NULL
);

INSERT INTO roles (nom_role) VALUES 
('Administrateur'),
('Formateur'),
('Etudiant');

-- Inserting default user for testing the login screen
-- The password is "password" (using simple hash for the example)
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, id_role) VALUES 
('Doe', 'John', 'admin@workify.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 1);

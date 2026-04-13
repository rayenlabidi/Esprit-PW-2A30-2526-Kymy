CREATE DATABASE IF NOT EXISTS workify_group_db;
USE workify_group_db;

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
    FOREIGN KEY (id_categorie) REFERENCES categories(id) ON DELETE SET NULL
);

INSERT INTO categories (nom, description) VALUES 
('Développement Web', 'Cours sur la programmation web, frontend et backend.'),
('Design', 'Cours sur le design UI/UX, création graphique.'),
('Marketing', 'Digital marketing, SEO, SEA.');

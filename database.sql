DROP DATABASE IF EXISTS workify_utilisateurs_db;
CREATE DATABASE workify_utilisateurs_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE workify_utilisateurs_db;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL,
    slug VARCHAR(40) NOT NULL UNIQUE,
    description VARCHAR(255) NULL
);

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
    CONSTRAINT fk_utilisateurs_role FOREIGN KEY (role_id) REFERENCES roles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_password_resets_token_hash (token_hash),
    INDEX idx_password_resets_user_id (user_id),
    CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

INSERT INTO roles (name, slug, description) VALUES
('Admin', 'admin', 'Gère toute la gestion des utilisateurs'),
('Freelancer', 'freelancer', 'Utilisateur freelance'),
('Boss', 'boss', 'Utilisateur recruteur');

INSERT INTO utilisateurs (role_id, first_name, last_name, email, password, headline, bio, avatar_url, status) VALUES
(1, 'Admin', 'Users', 'admin@workify.com', '$2y$10$7ALOQvIWzngQAJ/eN3NsS.7HpVWVUVLlxv7KblJL4McnOLEJIKus6', 'Platform administrator', 'Compte admin pour tester le module de gestion des utilisateurs.', '', 'active'),
(2, 'Sami', 'Freelancer', 'freelancer@workify.com', '$2y$10$8zrsqRyUqyEqdh3xLvEOW.wNgPVfdGPdSFThS54XdcyVY4Oc3b/JO', 'Front-end freelancer', 'Utilisateur de démonstration pour le rôle freelancer.', '', 'active'),
(3, 'Lina', 'Boss', 'boss@workify.com', '$2y$10$HLpAbAB5hkZFjJmYnlsqNeBUQS186KVB.uhsU8RBO5LyC0WLyzBai', 'Talent recruiter', 'Utilisateur de démonstration pour le rôle boss.', '', 'active');

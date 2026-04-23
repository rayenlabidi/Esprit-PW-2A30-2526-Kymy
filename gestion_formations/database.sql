DROP DATABASE IF EXISTS workify_formations_db;
CREATE DATABASE workify_formations_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE workify_formations_db;

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
    password VARCHAR(255) NOT NULL,
    headline VARCHAR(150) NOT NULL,
    bio TEXT NOT NULL,
    avatar_url VARCHAR(255) NULL,
    status ENUM('active', 'pending', 'blocked') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_utilisateurs_role FOREIGN KEY (role_id) REFERENCES roles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    scope ENUM('formation', 'all') NOT NULL DEFAULT 'formation',
    description VARCHAR(255) NULL
);

CREATE TABLE formations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    category_id INT NOT NULL,
    level ENUM('Beginner', 'Intermediate', 'Advanced') NOT NULL DEFAULT 'Beginner',
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    duration_hours INT NOT NULL,
    status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
    creator_id INT NOT NULL,
    image_url VARCHAR(255) NULL,
    tags VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_formations_category FOREIGN KEY (category_id) REFERENCES categories(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_formations_creator FOREIGN KEY (creator_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    formation_id INT NOT NULL,
    enrolled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    progress INT NOT NULL DEFAULT 0,
    UNIQUE KEY uniq_inscription (user_id, formation_id),
    CONSTRAINT fk_inscriptions_user FOREIGN KEY (user_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_inscriptions_formation FOREIGN KEY (formation_id) REFERENCES formations(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

INSERT INTO roles (name, slug, description) VALUES
('Admin', 'admin', 'Gere tout le module formations'),
('Freelancer', 'freelancer', 'Consulte les formations et s inscrit');

INSERT INTO utilisateurs (role_id, first_name, last_name, email, password, headline, bio, avatar_url, status) VALUES
(1, 'Admin', 'Workify', 'admin@workify.com', '$2y$10$7ALOQvIWzngQAJ/eN3NsS.7HpVWVUVLlxv7KblJL4McnOLEJIKus6', 'Responsable formation', 'Compte admin pour gerer le catalogue de formations.', '', 'active'),
(2, 'Sami', 'Freelancer', 'freelancer@workify.com', '$2y$10$8zrsqRyUqyEqdh3xLvEOW.wNgPVfdGPdSFThS54XdcyVY4Oc3b/JO', 'Apprenant digital', 'Compte freelancer pour tester les inscriptions et la consultation des formations.', '', 'active');

INSERT INTO categories (name, slug, scope, description) VALUES
('Developpement Web', 'developpement-web', 'formation', 'Frontend, backend et full stack'),
('UI UX Design', 'ui-ux-design', 'formation', 'Parcours design et prototypage'),
('Data & IA', 'data-ia', 'formation', 'Analyse de donnees et intelligence artificielle');

INSERT INTO formations (title, description, category_id, level, price, duration_hours, status, creator_id, image_url, tags) VALUES
('Bootcamp Full Stack Laravel', 'Un parcours intensif pour maitriser MVC, MySQL, authentification et tableaux de bord dans une logique de projet concret.', 1, 'Intermediate', 149.00, 42, 'published', 2, 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=1200&q=80', 'php,mysql,laravel,mvc'),
('Masterclass UI UX pour plateformes marketplace', 'Apprenez a creer des interfaces professionnelles pour une application inspiree de Fiverr et Upwork.', 2, 'Advanced', 119.00, 28, 'published', 2, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80', 'ui,ux,figma,marketplace'),
('Introduction a la Data analyse', 'Formation accessible pour debuter avec les tableaux de bord, les KPIs et la lecture des donnees.', 3, 'Beginner', 89.00, 18, 'draft', 1, 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80', 'data,analytics,kpi');

INSERT INTO inscriptions (user_id, formation_id, progress) VALUES
(2, 1, 35),
(2, 2, 10);

DROP DATABASE IF EXISTS workify_group_db;
CREATE DATABASE workify_group_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE workify_group_db;

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
    scope ENUM('all', 'formation', 'job') NOT NULL DEFAULT 'all',
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

CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    budget DECIMAL(10,2) NOT NULL,
    category_id INT NOT NULL,
    location VARCHAR(120) NOT NULL,
    is_remote TINYINT(1) NOT NULL DEFAULT 0,
    job_type ENUM('Freelance', 'Full-time', 'Stage', 'Part-time') NOT NULL DEFAULT 'Freelance',
    status ENUM('open', 'draft', 'closed') NOT NULL DEFAULT 'open',
    publisher_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_jobs_category FOREIGN KEY (category_id) REFERENCES categories(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_jobs_publisher FOREIGN KEY (publisher_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE candidatures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    job_id INT NOT NULL,
    cover_letter TEXT NOT NULL,
    status ENUM('pending', 'reviewed', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
    applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_candidature (user_id, job_id),
    CONSTRAINT fk_candidatures_user FOREIGN KEY (user_id) REFERENCES utilisateurs(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_candidatures_job FOREIGN KEY (job_id) REFERENCES jobs(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

INSERT INTO roles (name, slug, description) VALUES
('Admin', 'admin', 'Gere toute la plateforme'),
('Freelancer', 'freelancer', 'Suit des formations et postule aux jobs'),
('Boss', 'boss', 'Publie des jobs et recrute des freelances');

INSERT INTO utilisateurs (role_id, first_name, last_name, email, password, headline, bio, avatar_url, status) VALUES
(1, 'Admin', 'Workify', 'admin@workify.com', '$2y$10$7ALOQvIWzngQAJ/eN3NsS.7HpVWVUVLlxv7KblJL4McnOLEJIKus6', 'Platform administrator', 'Compte admin pour tester toute la plateforme et gerer chaque module.', '', 'active'),
(2, 'Sami', 'Freelancer', 'freelancer@workify.com', '$2y$10$8zrsqRyUqyEqdh3xLvEOW.wNgPVfdGPdSFThS54XdcyVY4Oc3b/JO', 'Front-end freelancer', 'Freelancer de demo pour tester les inscriptions et candidatures.', '', 'active'),
(3, 'Lina', 'Boss', 'boss@workify.com', '$2y$10$HLpAbAB5hkZFjJmYnlsqNeBUQS186KVB.uhsU8RBO5LyC0WLyzBai', 'Talent recruiter', 'Boss de demo pour publier des jobs et recruter des profils.', '', 'active');

INSERT INTO categories (name, slug, scope, description) VALUES
('Developpement Web', 'developpement-web', 'all', 'Frontend, backend et full stack'),
('UI UX Design', 'ui-ux-design', 'formation', 'Parcours design et prototypage'),
('Marketing Digital', 'marketing-digital', 'all', 'SEO, paid media et social media'),
('Data & IA', 'data-ia', 'formation', 'Analyse de donnees et intelligence artificielle'),
('Support Client', 'support-client', 'job', 'Experience client et assistance'),
('Product Management', 'product-management', 'job', 'Pilotage produit et delivery');

INSERT INTO formations (title, description, category_id, level, price, duration_hours, status, creator_id, image_url, tags) VALUES
('Bootcamp Full Stack Laravel', 'Un parcours intensif pour maitriser MVC, MySQL, authentification et dashboards dans une logique de projet concret.', 1, 'Intermediate', 149.00, 42, 'published', 2, 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=1200&q=80', 'php,mysql,laravel,mvc'),
('Masterclass UI UX pour plateformes marketplace', 'Apprenez a creer des interfaces professionnelles pour une application inspiree de Fiverr et Upwork.', 2, 'Advanced', 119.00, 28, 'published', 2, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80', 'ui,ux,figma,marketplace'),
('Introduction a la Data analyse', 'Formation accessible pour debuter avec les tableaux de bord, les KPIs et la lecture des donnees.', 4, 'Beginner', 89.00, 18, 'draft', 1, 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80', 'data,analytics,kpi');

INSERT INTO inscriptions (user_id, formation_id, progress) VALUES
(2, 1, 35),
(2, 2, 10);

INSERT INTO jobs (title, description, budget, category_id, location, is_remote, job_type, status, publisher_id) VALUES
('Developpeur PHP MVC pour plateforme locale', 'Nous cherchons un freelancer capable de finaliser un projet PHP MVC avec sessions, CRUD et validations JS.', 900.00, 1, 'Tunis', 1, 'Freelance', 'open', 3),
('UX Designer pour espace formation premium', 'Mission sur une interface moderne pour une section de catalogue de formations avec cartes, filtres et details.', 650.00, 6, 'Sousse', 1, 'Part-time', 'open', 3),
('Assistant marketing junior', 'Suivi de campagnes digitales et production de contenu pour une startup locale.', 550.00, 3, 'Remote', 1, 'Stage', 'draft', 1);

INSERT INTO candidatures (user_id, job_id, cover_letter, status) VALUES
(2, 1, 'Je peux prendre en charge le projet Workify, integrer les modules et optimiser le rendu pour une demo professeur.', 'reviewed');

CREATE TABLE publication (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) DEFAULT 'current_user',
    user_name VARCHAR(100) NOT NULL,
    user_init VARCHAR(5) NOT NULL,
    user_role ENUM('Freelancer', 'Client') DEFAULT 'Freelancer',
    user_avatar VARCHAR(50) DEFAULT 'av-blue',
    content TEXT NOT NULL,
    has_image TINYINT(1) DEFAULT 0,
    image_url VARCHAR(255) DEFAULT NULL,
    likes INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE comments (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    publication_id INT(11) NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    user_init VARCHAR(5) NOT NULL,
    user_avatar VARCHAR(50) DEFAULT 'av-blue',
    comment TEXT NOT NULL,
    likes INT(11) DEFAULT 0,
    parent_id INT(11) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (publication_id) REFERENCES publication(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
);

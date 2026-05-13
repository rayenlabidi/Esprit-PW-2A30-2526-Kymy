-- ============================================================
-- Workify — Migration : GPS + Métiers Innovants Avancés
-- ============================================================

-- 1. Ajouter les colonnes GPS à la table events
ALTER TABLE events
    ADD COLUMN IF NOT EXISTS latitude  DECIMAL(10,7) NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS longitude DECIMAL(10,7) NULL DEFAULT NULL;

-- 2. Ajouter les nouvelles catégories de métiers innovants avancés
INSERT INTO event_categories (name, description) VALUES
  ('Intelligence Artificielle & ML',     'Événements dédiés à l''IA générative, machine learning, LLMs, automatisation intelligente et éthique de l''IA.'),
  ('Cybersécurité & Souveraineté',       'Ateliers et conférences sur la sécurité des systèmes, pentest, zero-trust, RGPD et souveraineté numérique.'),
  ('Web3 & Blockchain',                  'Meetups autour de la DeFi, NFTs, smart contracts, DAOs et l''économie décentralisée.'),
  ('DevOps & Cloud Native',              'Sessions sur Kubernetes, GitOps, infrastructure as code, observabilité et architectures cloud-native.'),
  ('Réalité Augmentée & Métavers',       'Démos, hackathons et conférences XR (AR/VR/MR), spatial computing et plateformes métavers.'),
  ('Biotech & HealthTech',               'Événements à l''intersection de la biologie, la médecine et la technologie : génomique, télémedecine, IA médicale.'),
  ('GreenTech & Tech Durable',           'Conférences sur la tech au service de l''environnement, la sobriété numérique et les énergies renouvelables.'),
  ('Product & UX Design',                'Ateliers design thinking, prototypage, recherche utilisateur et stratégie produit.'),
  ('FinTech & InsurTech',                'Conférences sur la finance numérique, open banking, paiements, néo-banques et assurance technologique.'),
  ('Robotique & IoT Industriel',         'Événements sur la robotique collaborative, l''IIoT, l''industrie 4.0 et les jumeaux numériques.'),
  ('No-Code & Citizen Development',      'Ateliers pour créer des applications sans code (Bubble, Webflow, Make) et démocratiser le développement.'),
  ('Quantum Computing',                  'Séminaires et workshops sur l''informatique quantique, ses algorithmes et ses applications futures.');

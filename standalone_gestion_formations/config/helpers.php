<?php

if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
}

function lang(): string
{
    return $_SESSION['lang'] ?? 'fr';
}

function t(string $key): string
{
    static $translations = [
        'fr' => [
            'nav.home' => 'Accueil',
            'nav.formations' => 'Formations',
            'nav.jobs' => 'Jobs',
            'nav.users' => 'Utilisateurs',
            'nav.login' => 'Log in',
            'nav.logout' => 'Déconnexion',
            'nav.register' => 'Créer un compte',
            'nav.language' => 'Langue',
            'dashboard.eyebrow' => 'Plateforme intégrée formation + recrutement',
            'dashboard.title' => 'Connectez-vous aux opportunités sur Workify.',
            'dashboard.copy' => 'Que vous vouliez recruter des freelances ou trouver votre prochain projet, Workify réunit utilisateurs, formations et jobs dans une seule application MVC prête pour votre démonstration.',
            'dashboard.post_job' => 'Publier un job',
            'dashboard.browse_jobs' => 'Parcourir les jobs',
            'dashboard.users' => 'Utilisateurs',
            'dashboard.formations' => 'Formations',
            'dashboard.jobs' => 'Jobs',
            'dashboard.strong_module' => 'Module fort',
            'dashboard.featured_formations' => 'Formations mises en avant',
            'dashboard.marketplace' => 'Marketplace',
            'dashboard.recent_jobs' => 'Jobs récents',
            'common.view_all' => 'Voir tout',
            'auth.login_title' => 'Connectez-vous',
            'auth.login_copy' => 'Accédez à votre espace admin, freelancer ou boss pour gérer formations, jobs et candidatures.',
            'auth.email' => 'Email',
            'auth.password' => 'Mot de passe',
            'auth.no_account' => 'Pas encore de compte ?',
            'auth.register_title' => 'Créer un compte',
            'auth.register_copy' => 'Choisissez votre rôle pour suivre des formations, publier des jobs ou administrer la plateforme.',
            'auth.role' => 'Rôle',
            'auth.choose_role' => 'Choisir un rôle',
            'auth.first_name' => 'Prénom',
            'auth.last_name' => 'Nom',
            'auth.headline' => 'Titre professionnel',
            'auth.photo_url' => 'URL de la photo',
            'auth.bio' => 'Bio',
            'auth.create_my_account' => 'Créer mon compte',
            'users.profile' => 'Profil',
            'users.edit' => 'Modifier',
            'users.delete' => 'Supprimer',
            'formations.module' => 'Module premium',
            'formations.title' => 'Gestion des formations',
            'formations.subtitle' => 'Le module le plus valorisé du projet : recherche, filtres, détails, inscriptions et vue carte moderne.',
            'formations.new' => 'Nouvelle formation',
            'formations.catalog' => 'Catalogue',
            'formations.published' => 'Publiées',
            'formations.enrollments' => 'Inscriptions',
            'formations.average_price' => 'Prix moyen',
            'formations.search_placeholder' => 'Rechercher par titre, description, tags',
            'formations.all_categories' => 'Toutes les catégories',
            'formations.all_levels' => 'Tous les niveaux',
            'formations.all_statuses' => 'Tous les statuts',
            'formations.max_price' => 'Prix max',
            'formations.filter' => 'Filtrer',
            'formations.level' => 'Niveau',
            'formations.duration' => 'Durée',
            'formations.price' => 'Prix',
            'formations.learners' => 'Inscrits',
            'formations.created_by' => 'Créée par',
            'formations.details' => 'Détails',
            'formations.no_results' => 'Aucune formation ne correspond aux filtres.',
            'formations.try_other' => 'Essayez une autre catégorie ou ajoutez votre première formation premium.',
            'formations.add' => 'Ajouter une formation',
            'formations.edit_title' => 'Modifier la formation',
            'formations.back' => 'Retour',
            'formations.label_title' => 'Titre',
            'formations.label_description' => 'Description',
            'formations.label_category' => 'Catégorie',
            'formations.choose' => 'Choisir',
            'formations.label_price' => 'Prix',
            'formations.label_duration' => 'Durée (heures)',
            'formations.label_status' => 'Statut',
            'formations.label_creator' => 'Formateur / créateur',
            'formations.label_image' => 'Image / miniature URL',
            'formations.label_tags' => 'Tags',
            'formations.publish' => 'Publier la formation',
            'formations.save' => 'Enregistrer les changements',
            'formations.details_title' => 'Détails formation',
            'formations.trainer' => 'Formateur',
            'formations.tags' => 'Tags',
            'formations.created_on' => 'Créée le',
            'formations.enroll' => 'S inscrire à cette formation',
            'formations.already_enrolled' => 'Vous êtes déjà inscrit',
            'formations.back_catalog' => 'Retour au catalogue',
        ],
        'en' => [
            'nav.home' => 'Home',
            'nav.formations' => 'Training',
            'nav.jobs' => 'Jobs',
            'nav.users' => 'Users',
            'nav.login' => 'Log in',
            'nav.logout' => 'Log out',
            'nav.register' => 'Create account',
            'nav.language' => 'Language',
            'dashboard.eyebrow' => 'Integrated training + hiring platform',
            'dashboard.title' => 'Connect with opportunities on Workify.',
            'dashboard.copy' => 'Whether you want to hire freelancers or find your next project, Workify brings users, training, and jobs together in one MVC application ready for your demo.',
            'dashboard.post_job' => 'Post a job',
            'dashboard.browse_jobs' => 'Browse jobs',
            'dashboard.users' => 'Users',
            'dashboard.formations' => 'Training',
            'dashboard.jobs' => 'Jobs',
            'dashboard.strong_module' => 'Strongest module',
            'dashboard.featured_formations' => 'Featured training',
            'dashboard.marketplace' => 'Marketplace',
            'dashboard.recent_jobs' => 'Latest jobs',
            'common.view_all' => 'View all',
            'auth.login_title' => 'Welcome back',
            'auth.login_copy' => 'Enter your email and password to access your account.',
            'auth.email' => 'Email',
            'auth.password' => 'Password',
            'auth.no_account' => "Don't have an account?",
            'auth.register_title' => 'Create account',
            'auth.register_copy' => 'Choose your role to follow training, publish jobs, or manage the platform.',
            'auth.role' => 'Role',
            'auth.choose_role' => 'Choose a role',
            'auth.first_name' => 'First name',
            'auth.last_name' => 'Last name',
            'auth.headline' => 'Professional title',
            'auth.photo_url' => 'Photo URL',
            'auth.bio' => 'Bio',
            'auth.create_my_account' => 'Create my account',
            'users.profile' => 'Profile',
            'users.edit' => 'Edit',
            'users.delete' => 'Delete',
            'formations.module' => 'Premium module',
            'formations.title' => 'Training management',
            'formations.subtitle' => 'The strongest module in the project: search, filters, details, enrollments, and a modern card layout.',
            'formations.new' => 'New training',
            'formations.catalog' => 'Catalog',
            'formations.published' => 'Published',
            'formations.enrollments' => 'Enrollments',
            'formations.average_price' => 'Average price',
            'formations.search_placeholder' => 'Search by title, description, tags',
            'formations.all_categories' => 'All categories',
            'formations.all_levels' => 'All levels',
            'formations.all_statuses' => 'All statuses',
            'formations.max_price' => 'Max price',
            'formations.filter' => 'Filter',
            'formations.level' => 'Level',
            'formations.duration' => 'Duration',
            'formations.price' => 'Price',
            'formations.learners' => 'Enrolled',
            'formations.created_by' => 'Created by',
            'formations.details' => 'Details',
            'formations.no_results' => 'No training matches the selected filters.',
            'formations.try_other' => 'Try another category or add your first premium training.',
            'formations.add' => 'Add training',
            'formations.edit_title' => 'Edit training',
            'formations.back' => 'Back',
            'formations.label_title' => 'Title',
            'formations.label_description' => 'Description',
            'formations.label_category' => 'Category',
            'formations.choose' => 'Choose',
            'formations.label_price' => 'Price',
            'formations.label_duration' => 'Duration (hours)',
            'formations.label_status' => 'Status',
            'formations.label_creator' => 'Trainer / creator',
            'formations.label_image' => 'Image / thumbnail URL',
            'formations.label_tags' => 'Tags',
            'formations.publish' => 'Publish training',
            'formations.save' => 'Save changes',
            'formations.details_title' => 'Training details',
            'formations.trainer' => 'Trainer',
            'formations.tags' => 'Tags',
            'formations.created_on' => 'Created on',
            'formations.enroll' => 'Enroll in this training',
            'formations.already_enrolled' => 'You are already enrolled',
            'formations.back_catalog' => 'Back to catalog',
        ],
    ];

    return $translations[lang()][$key] ?? $translations['fr'][$key] ?? $key;
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(array $params = []): string
{
    $params = array_merge(['lang' => lang()], $params);
    return 'index.php' . ($params ? '?' . http_build_query($params) : '');
}

function redirect(array $params = []): void
{
    header('Location: ' . url($params));
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash_messages(): array
{
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

function auth_user(): ?array
{
    return $_SESSION['auth_user'] ?? null;
}

function is_logged_in(): bool
{
    return auth_user() !== null;
}

function user_role(): ?string
{
    return auth_user()['role_slug'] ?? null;
}

function has_role(array $roles): bool
{
    return in_array(user_role(), $roles, true);
}

function require_auth(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Connectez-vous pour acceder a cette page.');
        redirect(['module' => 'auth', 'action' => 'login']);
    }
}

function require_roles(array $roles): void
{
    require_auth();

    if (!has_role($roles)) {
        set_flash('error', 'Vous n avez pas l autorisation pour cette action.');
        redirect(['module' => 'dashboard', 'action' => 'index']);
    }
}

function current_module(): string
{
    return $_GET['module'] ?? 'dashboard';
}

function current_action(): string
{
    return $_GET['action'] ?? 'index';
}

function is_active_module(string $module): bool
{
    return current_module() === $module;
}

function format_currency($value): string
{
    return number_format((float) $value, 2, '.', ' ') . ' TND';
}

function format_date(?string $value): string
{
    if (!$value) {
        return '-';
    }

    $timestamp = strtotime($value);
    return $timestamp ? date('d/m/Y', $timestamp) : $value;
}

function status_badge_class(string $status): string
{
    return match ($status) {
        'published', 'active', 'open' => 'badge-success',
        'draft', 'pending' => 'badge-warning',
        'closed', 'archived', 'cancelled' => 'badge-danger',
        default => 'badge-neutral',
    };
}

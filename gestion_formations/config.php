<?php

class config
{
    private static ?PDO $pdo = null;
    private static string $host = '127.0.0.1';
    private static string $port = '3306';
    private static string $user = 'root';
    private static string $password = '';
    private static string $database = 'workify_formations_db';

    public static function getConnexion(): ?PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        try {
            self::$pdo = new PDO(
                'mysql:host=' . self::$host . ';port=' . self::$port . ';dbname=' . self::$database . ';charset=utf8mb4',
                self::$user,
                self::$password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $exception) {
            self::$pdo = null;
        }

        return self::$pdo;
    }
}

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
            'nav.catalog' => 'FrontOffice',
            'nav.backoffice' => 'BackOffice',
            'nav.login' => 'Se connecter',
            'nav.logout' => 'Deconnexion',
            'nav.register' => 'Creer un compte',
            'auth.login_title' => 'Connexion',
            'auth.login_copy' => 'Accedez a votre espace pour consulter ou gerer les formations.',
            'auth.register_title' => 'Inscription',
            'auth.register_copy' => 'Creez un compte pour acceder au module formations.',
            'auth.email' => 'Email',
            'auth.password' => 'Mot de passe',
            'auth.no_account' => 'Pas encore de compte ?',
            'auth.role' => 'Role',
            'auth.choose_role' => 'Choisir un role',
            'auth.first_name' => 'Prenom',
            'auth.last_name' => 'Nom',
            'auth.headline' => 'Titre professionnel',
            'auth.photo_url' => 'URL de la photo',
            'auth.bio' => 'Bio',
            'auth.create_my_account' => 'Creer mon compte',
            'users.edit' => 'Modifier',
            'users.delete' => 'Supprimer',
            'formations.module' => 'Gestion des formations',
            'formations.frontoffice_title' => 'Catalogue des formations',
            'formations.frontoffice_copy' => 'FrontOffice du module formations avec jointure categories, filtres et page details.',
            'formations.backoffice_title' => 'BackOffice des formations',
            'formations.backoffice_copy' => 'Espace de gestion avec statistiques, tableau CRUD et suivi des inscriptions.',
            'formations.new' => 'Nouvelle formation',
            'formations.manage' => 'Gerer les formations',
            'formations.front' => 'Voir le front office',
            'formations.catalog' => 'Catalogue',
            'formations.published' => 'Publiees',
            'formations.enrollments' => 'Inscriptions',
            'formations.average_price' => 'Prix moyen',
            'formations.search_placeholder' => 'Rechercher par titre, description ou tags',
            'formations.all_categories' => 'Toutes les categories',
            'formations.all_levels' => 'Tous les niveaux',
            'formations.all_statuses' => 'Tous les statuts',
            'formations.max_price' => 'Prix max',
            'formations.filter' => 'Filtrer',
            'formations.level' => 'Niveau',
            'formations.duration' => 'Duree',
            'formations.price' => 'Prix',
            'formations.learners' => 'Inscrits',
            'formations.created_by' => 'Creee par',
            'formations.details' => 'Details',
            'formations.no_results' => 'Aucune formation ne correspond aux filtres.',
            'formations.try_other' => 'Essayez une autre categorie ou ajoutez une nouvelle formation.',
            'formations.add' => 'Ajouter une formation',
            'formations.edit_title' => 'Modifier la formation',
            'formations.back' => 'Retour',
            'formations.label_title' => 'Titre',
            'formations.label_description' => 'Description',
            'formations.label_category' => 'Categorie',
            'formations.choose' => 'Choisir',
            'formations.label_price' => 'Prix',
            'formations.label_duration' => 'Duree (heures)',
            'formations.label_status' => 'Statut',
            'formations.label_creator' => 'Formateur / createur',
            'formations.label_image' => 'Image URL',
            'formations.label_tags' => 'Tags',
            'formations.publish' => 'Publier la formation',
            'formations.save' => 'Enregistrer les changements',
            'formations.details_title' => 'Details formation',
            'formations.trainer' => 'Formateur',
            'formations.tags' => 'Tags',
            'formations.created_on' => 'Creee le',
            'formations.enroll' => 'S inscrire a cette formation',
            'formations.already_enrolled' => 'Vous etes deja inscrit',
            'formations.back_catalog' => 'Retour au catalogue',
            'formations.jointure' => 'Jointure categories / formations',
            'formations.table' => 'Tableau CRUD des formations',
            'formations.latest_enrollments' => 'Dernieres inscriptions',
            'formations.no_enrollments' => 'Aucune inscription pour le moment.',
            'formations.created_at' => 'Date',
        ],
        'en' => [
            'nav.catalog' => 'FrontOffice',
            'nav.backoffice' => 'BackOffice',
            'nav.login' => 'Log in',
            'nav.logout' => 'Log out',
            'nav.register' => 'Create account',
            'auth.login_title' => 'Login',
            'auth.login_copy' => 'Access your space to browse or manage training.',
            'auth.register_title' => 'Register',
            'auth.register_copy' => 'Create an account to use the training module.',
            'auth.email' => 'Email',
            'auth.password' => 'Password',
            'auth.no_account' => "Don't have an account?",
            'auth.role' => 'Role',
            'auth.choose_role' => 'Choose a role',
            'auth.first_name' => 'First name',
            'auth.last_name' => 'Last name',
            'auth.headline' => 'Professional title',
            'auth.photo_url' => 'Photo URL',
            'auth.bio' => 'Bio',
            'auth.create_my_account' => 'Create my account',
            'users.edit' => 'Edit',
            'users.delete' => 'Delete',
            'formations.module' => 'Training management',
            'formations.frontoffice_title' => 'Training catalog',
            'formations.frontoffice_copy' => 'FrontOffice for the training module with category join, filters, and details page.',
            'formations.backoffice_title' => 'Training back office',
            'formations.backoffice_copy' => 'Management area with statistics, CRUD table, and enrollment tracking.',
            'formations.new' => 'New training',
            'formations.manage' => 'Manage training',
            'formations.front' => 'View front office',
            'formations.catalog' => 'Catalog',
            'formations.published' => 'Published',
            'formations.enrollments' => 'Enrollments',
            'formations.average_price' => 'Average price',
            'formations.search_placeholder' => 'Search by title, description, or tags',
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
            'formations.try_other' => 'Try another category or add a new training.',
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
            'formations.label_image' => 'Image URL',
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
            'formations.jointure' => 'Category / training join',
            'formations.table' => 'Training CRUD table',
            'formations.latest_enrollments' => 'Latest enrollments',
            'formations.no_enrollments' => 'No enrollments yet.',
            'formations.created_at' => 'Date',
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
    $_SESSION['flash_messages'][] = ['type' => $type, 'message' => $message];
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
        redirect(['module' => 'formations', 'action' => 'backoffice']);
    }
}

function current_module(): string
{
    return $_GET['module'] ?? 'formations';
}

function current_action(): string
{
    return $_GET['action'] ?? 'index';
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

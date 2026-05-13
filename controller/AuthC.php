<?php
require_once __DIR__ . '/../config.php';

class AuthC
{
    public static function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function attempt($email, $password)
    {
        self::startSession();

        $sql = 'SELECT u.*, r.slug AS role_slug, r.name AS role_name
                FROM utilisateurs u
                INNER JOIN roles r ON u.role_id = r.id
                WHERE u.email = :email AND u.status = "active"
                LIMIT 1';
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['email' => trim($email)]);
        $user = $query->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role_slug'];

        return $user;
    }

    public static function logout()
    {
        self::startSession();
        session_unset();
        session_destroy();
    }

    public static function isLoggedIn()
    {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin()
    {
        self::startSession();
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public static function currentUserName()
    {
        self::startSession();
        return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
    }

    public static function currentUserId()
    {
        self::startSession();
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
    }

    public static function currentUserEmail()
    {
        self::startSession();
        return isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
    }

    public static function currentUserRole()
    {
        self::startSession();
        return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
    }

    public static function hasRole($roles)
    {
        $roles = array_map('strtolower', (array) $roles);
        return in_array(strtolower((string) self::currentUserRole()), $roles, true);
    }    public static function requireAdmin()
    {
        self::startSession();

        if (!self::isAdmin()) {
            $_SESSION['redirect_after_login'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'BackDashboardC.php';
            header('Location: AuthController.php?action=login');
            exit;
        }
    }
}
?>

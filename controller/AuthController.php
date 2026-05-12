<?php
require_once __DIR__ . '/AuthC.php';

class AuthController
{
    public function handleRequest()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'login';

        if ($action === 'logout') {
            $this->logout();
        } else {
            $this->login();
        }
    }

    private function login()
    {
        AuthC::startSession();
        $office = 'front';
        $activeModule = '';
        $pageTitle = 'Connexion Admin';
        $error = '';

        if (AuthC::isAdmin()) {
            header('Location: BackDashboardC.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = AuthC::attempt($_POST['email'] ?? '', $_POST['password'] ?? '');

            if ($user && $user['role_slug'] === 'admin') {
                $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'BackDashboardC.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            }

            AuthC::logout();
            $error = 'Acces refuse. Seuls les comptes admin peuvent ouvrir le BackOffice.';
        }

        include __DIR__ . '/../view/users/login.php';
    }

    private function logout()
    {
        AuthC::logout();
        header('Location: HomeC.php');
        exit;
    }
}

$controller = new AuthController();
$controller->handleRequest();
?>

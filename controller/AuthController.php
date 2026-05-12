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
        $pageTitle = 'Connexion';
        $error = '';
        $captcha = $this->captcha();

        if (AuthC::isLoggedIn()) {
            header('Location: HomeC.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->captchaValide($_POST['captcha_answer'] ?? '')) {
                $error = 'Verification incorrecte. Essayez avec le nouveau code.';
                $captcha = $this->refreshCaptcha();
                include __DIR__ . '/../view/users/login.php';
                return;
            }

            $user = AuthC::attempt($_POST['email'] ?? '', $_POST['password'] ?? '');

            if ($user) {
                if ($user['role_slug'] === 'admin') {
                    $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'BackDashboardC.php';
                    unset($_SESSION['redirect_after_login']);
                    header('Location: ' . $redirect);
                    exit;
                }

                unset($_SESSION['redirect_after_login']);
                header('Location: HomeC.php');
                exit;
            }

            $error = 'Email ou mot de passe incorrect.';
            $captcha = $this->refreshCaptcha();
        }

        include __DIR__ . '/../view/users/login.php';
    }

    private function logout()
    {
        AuthC::logout();
        header('Location: HomeC.php');
        exit;
    }

    private function captcha()
    {
        if (!isset($_SESSION['captcha_question'], $_SESSION['captcha_answer'])) {
            return $this->refreshCaptcha();
        }

        return $_SESSION['captcha_question'];
    }

    private function refreshCaptcha()
    {
        $a = random_int(2, 9);
        $b = random_int(2, 9);
        $_SESSION['captcha_question'] = $a . ' + ' . $b;
        $_SESSION['captcha_answer'] = (string) ($a + $b);

        return $_SESSION['captcha_question'];
    }

    private function captchaValide($answer)
    {
        $valid = isset($_SESSION['captcha_answer']) && trim((string) $answer) === (string) $_SESSION['captcha_answer'];
        unset($_SESSION['captcha_question'], $_SESSION['captcha_answer']);

        return $valid;
    }
}

$controller = new AuthController();
$controller->handleRequest();
?>

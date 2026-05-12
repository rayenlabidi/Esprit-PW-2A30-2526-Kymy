<?php
/*
 * Controleur d'authentification.
 * Il prepare les pages login/reset et gere la session de connexion.
 */
include_once __DIR__ . '/AuthC.php';
include_once __DIR__ . '/MailC.php';
include_once __DIR__ . '/../lib/CaptchaService.php';

class AuthController
{
    private $auth;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->auth = new AuthC();
    }

    public function prepareLoginPage()
    {
        $error = '';

        if (isset($_GET['captcha_refresh'])) {
            CaptchaService::refreshChallenge();
            header('Location: login.php');
            exit();
        }

        $captcha = CaptchaService::getChallenge();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'])) {
            if (!CaptchaService::validate($_POST)) {
                $error = 'Captcha incorrect. Reessayez avec le nouveau defi affiche.';
                $captcha = CaptchaService::refreshChallenge();
            } else {
                $user = $this->auth->login($_POST['email'], $_POST['password']);

                if ($user) {
                    CaptchaService::clear();

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_role'] = strtolower(trim($user['email'])) === 'admin@workify.com' ? 'admin' : 'user';

                    header('Location: listeUtilisateurs.php');
                    exit();
                }

                $error = 'Email ou mot de passe incorrect.';
                $captcha = CaptchaService::refreshChallenge();
            }
        }

        return ['error' => $error, 'captcha' => $captcha];
    }

    public function prepareForgotPasswordPage()
    {
        $success = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_email'])) {
            $reset = $this->auth->createPasswordResetToken($_POST['reset_email']);
            $success = "Si cette adresse existe, un e-mail de reinitialisation vient d'etre envoye.";

            if ($reset) {
                $resetLink = $this->resetUrl($reset['token']);
                $mailSent = MailC::send(
                    $reset['email'],
                    'Reinitialisation de votre mot de passe Workify',
                    "Bonjour " . ($reset['first_name'] ?: '') . ",\n\n"
                    . "Vous avez demande la reinitialisation de votre mot de passe Workify.\n"
                    . "Cliquez sur ce lien pour choisir un nouveau mot de passe :\n"
                    . $resetLink . "\n\n"
                    . "Ce lien expire dans 1 heure. Si vous n'etes pas a l'origine de cette demande, ignorez cet e-mail.\n\n"
                    . "Workify"
                );

                if (!$mailSent) {
                    $success = '';
                    $error = "Le serveur n'a pas pu envoyer l'e-mail de reinitialisation. " . MailC::getLastError();
                }
            }
        }

        return ['success' => $success, 'error' => $error];
    }

    public function prepareResetPasswordPage()
    {
        $error = '';
        $token = $_GET['token'] ?? ($_POST['token'] ?? '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'], $_POST['new_password_confirm'])) {
            $password = $_POST['new_password'];
            $passwordConfirm = $_POST['new_password_confirm'];

            if (strlen($password) < 8) {
                $error = 'Le nouveau mot de passe doit contenir au moins 8 caracteres.';
            } elseif ($password !== $passwordConfirm) {
                $error = 'Les deux mots de passe ne correspondent pas.';
            } elseif ($this->auth->resetPassword($token, $password)) {
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'title' => 'Mot de passe modifie',
                    'message' => 'Votre mot de passe a ete reinitialise. Vous pouvez maintenant vous connecter.'
                ];
                header('Location: login.php');
                exit();
            } else {
                $error = 'Le lien de reinitialisation est invalide ou expire.';
            }
        }

        return [
            'error' => $error,
            'token' => $token,
            'resetData' => $token !== '' ? $this->auth->getPasswordResetByToken($token) : false
        ];
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit();
    }

    private function resetUrl($token)
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $currentPath = strtok($_SERVER['REQUEST_URI'] ?? '/standalone_gestion_utilisateurs/view/forgot_password.php', '?');
        $basePath = rtrim(dirname($currentPath), '/\\');

        return $scheme . '://' . $host . $basePath . '/reset_password.php?token=' . urlencode($token);
    }

}

?>

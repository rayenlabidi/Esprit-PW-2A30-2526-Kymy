<?php
require_once __DIR__ . '/AuthC.php';
require_once __DIR__ . '/UtilisateurModel.php';
require_once __DIR__ . '/Mailer.php';

class AuthController
{
    public function handleRequest()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'login';

        if ($action === 'logout') {
            $this->logout();
        } elseif ($action === 'forgot') {
            $this->forgotPassword();
        } elseif ($action === 'signup') {
            $this->signup();
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
        $recaptchaSiteKey = WORKIFY_RECAPTCHA_SITE_KEY;

        if (!empty($_GET['redirect'])) {
            $_SESSION['redirect_after_login'] = $this->frontRedirect($_GET['redirect']);
        }

        if (AuthC::isLoggedIn()) {
            header('Location: HomeC.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->recaptchaValide($_POST['g-recaptcha-response'] ?? '')) {
                $error = 'Veuillez confirmer la verification reCAPTCHA.';
                include __DIR__ . '/../view/users/login.php';
                return;
            }

            $user = AuthC::attempt($_POST['email'] ?? '', $_POST['password'] ?? '');

            if ($user) {
                $storedRedirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : '';
                unset($_SESSION['redirect_after_login']);

                if ($user['role_slug'] === 'admin') {
                    $redirect = $storedRedirect !== '' ? $storedRedirect : 'BackDashboardC.php';
                    header('Location: ' . $redirect);
                    exit;
                }

                $redirect = $this->frontRedirect($storedRedirect);
                header('Location: ' . $redirect);
                exit;
            }

            $error = 'Email ou mot de passe incorrect.';
        }

        include __DIR__ . '/../view/users/login.php';
    }

    private function forgotPassword()
    {
        AuthC::startSession();
        $office = 'front';
        $activeModule = '';
        $pageTitle = 'Mot de passe oublie';
        $error = '';
        $successMessage = '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $recaptchaSiteKey = WORKIFY_RECAPTCHA_SITE_KEY;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->recaptchaValide($_POST['g-recaptcha-response'] ?? '')) {
                $error = 'Veuillez confirmer la verification reCAPTCHA.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Veuillez saisir un email valide.';
            } else {
                $utilisateurModel = new UtilisateurModel();
                $user = $utilisateurModel->getUtilisateurByEmail($email);

                if (!$user || $user['status'] !== 'active') {
                    $successMessage = 'Si ce compte existe, un message de recuperation sera envoye.';
                } else {
                    $temporaryPassword = $this->temporaryPassword();
                    $mailSent = $this->sendResetMail($email, $user, $temporaryPassword);

                    if ($mailSent) {
                        $utilisateurModel->updatePassword((int) $user['id'], $temporaryPassword);
                        $successMessage = 'Un nouveau mot de passe temporaire a ete envoye a votre email.';
                    } else {
                        $error = 'Le service email n a pas pu envoyer le message. Verifiez la configuration Gmail.';
                    }
                }
            }
        }

        include __DIR__ . '/../view/users/forgot_password.php';
    }

    private function signup()
    {
        AuthC::startSession();
        $office = 'front';
        $activeModule = '';
        $pageTitle = 'Inscription';
        $error = '';
        $successMessage = '';
        $recaptchaSiteKey = WORKIFY_RECAPTCHA_SITE_KEY;
        $formData = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'role' => trim($_POST['role'] ?? 'freelancer'),
            'headline' => trim($_POST['headline'] ?? '')
        ];

        if (AuthC::isLoggedIn()) {
            header('Location: HomeC.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->recaptchaValide($_POST['g-recaptcha-response'] ?? '')) {
                $error = 'Veuillez confirmer la verification reCAPTCHA.';
            } else {
                $utilisateurModel = new UtilisateurModel();
                $errors = $this->validerInscription($formData, $_POST['password'] ?? '', $_POST['password_confirm'] ?? '', $utilisateurModel);

                if (empty($errors)) {
                    $roleId = $utilisateurModel->getRoleIdBySlug($formData['role']);
                    $headline = $formData['headline'] !== '' ? $formData['headline'] : ($formData['role'] === 'boss' ? 'Porteur de projet' : 'Talent Workify');
                    $bio = 'Compte cree depuis l inscription publique Workify.';
                    $user = new utilisateur(
                        $roleId,
                        $this->clean($formData['first_name']),
                        $this->clean($formData['last_name']),
                        $this->clean($formData['email']),
                        $this->clean($formData['phone']),
                        trim($_POST['password']),
                        $this->clean($headline),
                        $bio,
                        'active'
                    );
                    $utilisateurModel->addUtilisateur($user);
                    $successMessage = 'Compte cree avec succes. Vous pouvez maintenant vous connecter.';
                    $formData = ['first_name' => '', 'last_name' => '', 'email' => '', 'phone' => '', 'role' => 'freelancer', 'headline' => ''];
                } else {
                    $error = implode(' ', $errors);
                }
            }
        }

        include __DIR__ . '/../view/users/signup.php';
    }

    private function sendResetMail($email, $user, $temporaryPassword)
    {
        $name = trim($user['first_name'] . ' ' . $user['last_name']);
        $safeName = htmlspecialchars($name, ENT_QUOTES);
        $safePassword = htmlspecialchars($temporaryPassword, ENT_QUOTES);
        $html = '<p>Bonjour ' . $safeName . ',</p>'
            . '<p>Voici votre nouveau mot de passe temporaire Workify:</p>'
            . '<p><strong>' . $safePassword . '</strong></p>'
            . '<p>Connectez-vous puis changez ce mot de passe depuis votre espace.</p>';
        $text = "Bonjour " . $name . ",\n\n"
            . "Voici votre nouveau mot de passe temporaire Workify: " . $temporaryPassword . "\n\n"
            . "Connectez-vous puis changez ce mot de passe depuis votre espace.";

        $mailer = new WorkifyMailer();
        return $mailer->send($email, 'Recuperation de votre compte Workify', $html, $text);
    }

    private function validerInscription($data, $password, $confirmPassword, $utilisateurModel)
    {
        $errors = [];
        $allowedRoles = ['freelancer', 'boss'];

        if (strlen($data['first_name']) < 2) {
            $errors[] = 'Le prenom doit contenir au moins 2 caracteres.';
        }

        if (strlen($data['last_name']) < 2) {
            $errors[] = 'Le nom doit contenir au moins 2 caracteres.';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Veuillez saisir un email valide.';
        } elseif ($utilisateurModel->emailExiste($data['email'])) {
            $errors[] = 'Cet email est deja utilise.';
        }

        if (!in_array($data['role'], $allowedRoles)) {
            $errors[] = 'Veuillez choisir un type de compte valide.';
        } elseif ($utilisateurModel->getRoleIdBySlug($data['role']) <= 0) {
            $errors[] = 'Le type de compte choisi est indisponible.';
        }

        if (strlen(trim($password)) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caracteres.';
        }

        if (trim($password) !== trim($confirmPassword)) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        return $errors;
    }

    private function clean($value)
    {
        return trim(strip_tags($value));
    }

    private function temporaryPassword()
    {
        return 'Workify-' . substr(bin2hex(random_bytes(4)), 0, 8);
    }

    private function logout()
    {
        AuthC::logout();
        header('Location: HomeC.php');
        exit;
    }

    private function recaptchaValide($token)
    {
        $token = trim((string) $token);
        if ($token === '' || WORKIFY_RECAPTCHA_SECRET_KEY === '') {
            return false;
        }

        $payload = http_build_query([
            'secret' => WORKIFY_RECAPTCHA_SECRET_KEY,
            'response' => $token,
            'remoteip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''
        ]);

        $response = $this->postRecaptcha($payload);
        if ($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        return is_array($data) && !empty($data['success']);
    }

    private function postRecaptcha($payload)
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
                'timeout' => 8
            ]
        ]);

        $response = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        if ($response !== false) {
            return $response;
        }

        if (!function_exists('curl_init')) {
            return false;
        }

        $curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 8);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    private function frontRedirect($redirect)
    {
        if ($redirect === '') {
            return 'HomeC.php';
        }

        if (strpos($redirect, '://') !== false || strpos($redirect, '//') === 0) {
            return 'HomeC.php';
        }

        $blocked = ['BackDashboardC.php', 'UtilisateurC.php', 'office=back'];
        foreach ($blocked as $needle) {
            if (strpos($redirect, $needle) !== false) {
                return 'HomeC.php';
            }
        }

        return $redirect;
    }
}

$controller = new AuthController();
$controller->handleRequest();
?>

<?php
include_once __DIR__ . '/../Model/utilisateur.php';
include_once __DIR__ . '/UtilisateurC.php';
include_once __DIR__ . '/MailC.php';

class UtilisateurController
{
    private $uc;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->uc = new UtilisateurC();
    }

    public function prepareListPage()
    {
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'date_desc';

        return [
            'search' => $search,
            'sort' => $sort,
            'liste' => $this->uc->ListeUtilisateurs($search, $sort)
        ];
    }

    public function prepareAddPage()
    {
        $this->requireAdmin('Seuls les administrateurs peuvent ajouter des utilisateurs.');
        return ['roles' => $this->uc->ListeRoles()];
    }

    public function add()
    {
        $this->requireAdmin('Seuls les administrateurs peuvent ajouter des utilisateurs.');

        if (!$this->hasRequiredPost(['role_id', 'first_name', 'last_name', 'email', 'password', 'headline', 'bio', 'status'])) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'title' => 'Parametres manquants',
                'message' => 'Tous les champs obligatoires doivent etre remplis.'
            ];
            $this->redirect('../view/ajoutUtilisateur.php');
        }

        $user = new utilisateur(
            (int)$_POST['role_id'],
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            $_POST['phone'] ?? '',
            $_POST['password'],
            $_POST['headline'],
            $_POST['bio'],
            $_POST['status']
        );

        $created = $this->uc->AddUtilisateur($user);
        if (!$created) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'title' => 'Creation impossible',
                'message' => 'Le nouvel utilisateur n\'a pas pu etre ajoute.'
            ];
            $this->redirect('../view/listeUtilisateurs.php');
        }

        $fullName = trim($user->getFirstName() . ' ' . $user->getLastName());
        $sent = MailC::send(
            $user->getEmail(),
            'Votre compte Workify a ete cree',
            "Bonjour " . ($fullName ?: '') . ",\n\n"
            . "Votre compte Workify vient d'etre cree.\n\n"
            . "Email de connexion : " . $user->getEmail() . "\n"
            . "Mot de passe temporaire : " . $_POST['password'] . "\n\n"
            . "Vous pouvez maintenant vous connecter depuis la page Workify.\n"
            . "Pour votre securite, pensez a modifier votre mot de passe apres connexion.\n\n"
            . "Cordialement,\nWorkify"
        );

        $_SESSION['flash'] = $this->actionFlash(
            'Utilisateur cree',
            'Le nouvel utilisateur a ete ajoute avec succes.',
            $sent,
            'Un email de creation de compte a ete envoye a l\'utilisateur.',
            'Le compte a ete cree, mais l\'email n\'a pas pu etre envoye. ' . MailC::getLastError()
        );

        $this->redirect('../view/listeUtilisateurs.php');
    }

    public function prepareUpdatePage()
    {
        $this->requireAdmin('Seuls les administrateurs peuvent modifier des utilisateurs.');
        $userToForm = isset($_GET['id']) ? $this->uc->RecupererUtilisateur($_GET['id']) : null;

        return [
            'roles' => $this->uc->ListeRoles(),
            'userToForm' => $userToForm
        ];
    }

    public function update()
    {
        $this->requireAdmin('Seuls les administrateurs peuvent modifier des utilisateurs.');

        if (!$this->hasRequiredPost(['id', 'role_id', 'first_name', 'last_name', 'email'])) {
            $this->redirect('../view/listeUtilisateurs.php');
        }

        $oldUser = $this->uc->RecupererUtilisateur($_POST['id']);
        $user = new utilisateur(
            (int)$_POST['role_id'],
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            $_POST['phone'] ?? '',
            $_POST['password'] ?? '',
            $_POST['headline'] ?? '',
            $_POST['bio'] ?? '',
            $_POST['status'] ?? 'active'
        );

        $updated = $this->uc->UpdateUtilisateur($user, $_POST['id']);
        if (!$updated) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'title' => 'Modification impossible',
                'message' => 'Les informations de l\'utilisateur n\'ont pas pu etre mises a jour.'
            ];
            $this->redirect('../view/listeUtilisateurs.php');
        }

        $recipient = !empty($_POST['email']) ? $_POST['email'] : ($oldUser['email'] ?? '');
        $fullName = trim(($_POST['first_name'] ?? '') . ' ' . ($_POST['last_name'] ?? ''));
        $passwordLine = !empty($_POST['password']) ? "\nVotre mot de passe a egalement ete modifie par l'administrateur.\n" : '';
        $sent = false;

        if (!empty($recipient)) {
            $sent = MailC::send(
                $recipient,
                'Votre profil Workify a ete modifie',
                "Bonjour " . ($fullName ?: '') . ",\n\n"
                . "Vos informations de profil Workify ont ete modifiees par l'administrateur.\n"
                . $passwordLine . "\n"
                . "Si vous n'etes pas a l'origine de cette demande, contactez l'administrateur.\n\n"
                . "Cordialement,\nWorkify"
            );
        }

        $mailError = !empty($recipient) ? MailC::getLastError() : 'Aucune adresse email valide pour cet utilisateur.';
        $_SESSION['flash'] = $this->actionFlash(
            'Profil mis a jour',
            'Les informations de l\'utilisateur ont ete mises a jour avec succes.',
            $sent,
            'Un email de notification a ete envoye a l\'utilisateur.',
            'Le profil a ete modifie, mais l\'email n\'a pas pu etre envoye. ' . $mailError
        );

        $this->redirect('../view/listeUtilisateurs.php');
    }

    public function delete()
    {
        $this->requireAdmin();
        $u = isset($_GET['id']) ? $this->uc->RecupererUtilisateur($_GET['id']) : null;
        $sent = false;

        if (!empty($u) && !empty($u['email'])) {
            $fullName = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
            $sent = MailC::send(
                $u['email'],
                'Votre compte Workify a ete supprime',
                "Bonjour " . ($fullName ?: '') . ",\n\n"
                . "Votre compte Workify a ete supprime par l'administrateur.\n"
                . "Vous ne pouvez plus vous connecter avec ce compte.\n\n"
                . "Cordialement,\nWorkify"
            );
        }
        $mailError = !empty($u['email']) ? MailC::getLastError() : 'Aucune adresse email valide pour cet utilisateur.';

        if (isset($_GET['id']) && $this->uc->DeleteUtilisateur($_GET['id'])) {
            $_SESSION['flash'] = $this->actionFlash(
                'Utilisateur supprime',
                !empty($u) ? ('Utilisateur supprime: ' . ($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) : 'Utilisateur supprime avec succes.',
                $sent,
                'Un email de suppression de compte a ete envoye a l\'utilisateur.',
                'L\'utilisateur a ete supprime, mais l\'email n\'a pas pu etre envoye. ' . $mailError,
                'warning'
            );
        }

        $this->redirect('../view/listeUtilisateurs.php');
    }

    public function prepareStatsPage()
    {
        $this->requireAdmin('Seuls les administrateurs peuvent voir les statistiques.');
        return ['stats' => $this->uc->GetStats()];
    }

    public function uploadAvatar()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('../view/login.php');
        }

        if (($_SESSION['user_role'] ?? '') !== 'user') {
            $_SESSION['flash'] = ['type' => 'danger', 'title' => 'Acces refuse', 'message' => 'Seul un utilisateur peut modifier sa photo de profil.'];
            $this->redirect('../view/listeUtilisateurs.php');
        }

        $result = $this->validateAvatarUpload();
        if ($result['error']) {
            $_SESSION['flash'] = $result['flash'];
            $this->redirect('../view/listeUtilisateurs.php');
        }

        $this->uc->UpdateAvatar((int)$_SESSION['user_id'], $result['filename']);
        $_SESSION['flash'] = ['type' => 'success', 'title' => 'Photo mise a jour', 'message' => 'Votre photo de profil a ete mise a jour.'];
        $this->redirect('../view/listeUtilisateurs.php');
    }

    public function exportPdf()
    {
        $this->requireAdmin();
        include_once __DIR__ . '/../lib/SimplePdf.php';

        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'date_desc';
        $liste = $this->uc->ListeUtilisateurs($search, $sort);

        $rows = [];
        foreach ($liste as $u) {
            $id = (string)($u['id'] ?? '');
            $name = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
            $rows[] = ['field' => 'Utilisateur', 'value' => $name . ($id !== '' ? (' (#' . $id . ')') : '')];
            $rows[] = ['field' => 'Role', 'value' => (string)($u['role_name'] ?? '')];
            $rows[] = ['field' => 'Statut', 'value' => (string)($u['status'] ?? '')];
            $rows[] = ['field' => 'Email', 'value' => (string)($u['email'] ?? '')];
            $rows[] = ['field' => 'Telephone', 'value' => (string)($u['phone'] ?? '')];
            $rows[] = ['field' => 'Headline', 'value' => (string)($u['headline'] ?? '')];
            $rows[] = ['field' => 'Bio', 'value' => (string)($u['bio'] ?? '')];
            $rows[] = ['field' => 'Avatar', 'value' => (string)($u['avatar_url'] ?? '')];
            $rows[] = ['field' => 'Cree le', 'value' => (string)($u['created_at'] ?? '')];
            $rows[] = ['field' => '', 'value' => ''];
        }

        $pdf = new SimplePdf();
        $pdf->addTablePage(
            'Fiches utilisateurs (details)',
            ['Genere le: ' . date('Y-m-d H:i'), 'Filtres: search="' . (string)$search . '" | sort="' . (string)$sort . '"'],
            [['label' => 'Champ', 'key' => 'field', 'w' => 0.25], ['label' => 'Valeur', 'key' => 'value', 'w' => 0.75]],
            $rows
        );
        $pdf->output('utilisateurs.pdf');
    }

    private function requireAdmin($message = 'Acces refuse.')
    {
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            $_SESSION['flash'] = ['type' => 'danger', 'title' => 'Acces refuse', 'message' => $message];
            $this->redirect('../view/listeUtilisateurs.php');
        }
    }

    private function hasRequiredPost($fields)
    {
        foreach ($fields as $field) {
            if (!isset($_POST[$field])) {
                return false;
            }
        }
        return true;
    }

    private function actionFlash($mainTitle, $mainMessage, $mailSent, $mailOk, $mailKo, $mainType = 'success')
    {
        return [
            ['type' => $mainType, 'title' => $mainTitle, 'message' => $mainMessage],
            ['type' => $mailSent ? 'success' : 'warning', 'title' => $mailSent ? 'Email envoye' : 'Email non envoye', 'message' => $mailSent ? $mailOk : $mailKo]
        ];
    }

    private function validateAvatarUpload()
    {
        if (!isset($_FILES['avatar']) || !is_array($_FILES['avatar'])) {
            return ['error' => true, 'flash' => ['type' => 'danger', 'title' => 'Upload invalide', 'message' => 'Aucun fichier recu.']];
        }

        $file = $_FILES['avatar'];
        if (!empty($file['error']) || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['error' => true, 'flash' => ['type' => 'danger', 'title' => 'Upload echoue', 'message' => 'Fichier invalide.']];
        }

        if (!empty($file['size']) && (int)$file['size'] > 2 * 1024 * 1024) {
            return ['error' => true, 'flash' => ['type' => 'warning', 'title' => 'Fichier trop volumineux', 'message' => 'Taille maximale: 2 MB.']];
        }

        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            return ['error' => true, 'flash' => ['type' => 'warning', 'title' => 'Format non supporte', 'message' => 'Formats acceptes: JPG, PNG, WEBP.']];
        }

        $assetsDir = realpath(__DIR__ . '/../assets');
        if ($assetsDir === false) {
            return ['error' => true, 'flash' => ['type' => 'danger', 'title' => 'Erreur serveur', 'message' => 'Dossier assets introuvable.']];
        }

        $avatarsDir = $assetsDir . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'avatars';
        if (!is_dir($avatarsDir)) {
            @mkdir($avatarsDir, 0755, true);
        }
        if (!is_dir($avatarsDir)) {
            return ['error' => true, 'flash' => ['type' => 'danger', 'title' => 'Erreur serveur', 'message' => 'Impossible de creer le dossier d\'upload.']];
        }

        $safeName = 'avatar_' . (int)$_SESSION['user_id'] . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
        if (!@move_uploaded_file($file['tmp_name'], $avatarsDir . DIRECTORY_SEPARATOR . $safeName)) {
            return ['error' => true, 'flash' => ['type' => 'danger', 'title' => 'Upload echoue', 'message' => 'Impossible de sauvegarder l\'image.']];
        }

        return ['error' => false, 'filename' => $safeName];
    }

    private function redirect($url)
    {
        header('Location: ' . $url);
        exit();
    }
}

?>

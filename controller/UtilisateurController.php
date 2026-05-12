<?php
/*
 * Controleur principal des utilisateurs.
 * Il gere les actions admin, prepare les donnees des vues et declenche les emails.
 */
include_once __DIR__ . '/../Model/utilisateur.php';
include_once __DIR__ . '/UtilisateurC.php';
include_once __DIR__ . '/MailC.php';
include_once __DIR__ . '/../lib/UserQrService.php';

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
        $liste = $this->uc->ListeUtilisateurs($search, $sort);

        return [
            'search' => $search,
            'sort' => $sort,
            'liste' => array_map([UserQrService::class, 'enrichUser'], $liste)
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
        // Creation des deux versions du mail: texte simple et HTML.
        $mail = $this->buildAccountCreatedMail($fullName, $user->getEmail(), $_POST['password']);
        $sent = MailC::send(
            $user->getEmail(),
            'Votre compte Workify a ete cree',
            $mail['text'],
            $mail['html']
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

    public function prepareProfilePage()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('../view/login.php');
        }

        $profileId = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_SESSION['user_id'];
        if (($_SESSION['user_role'] ?? '') !== 'admin' && $profileId !== (int)$_SESSION['user_id']) {
            $_SESSION['flash'] = ['type' => 'danger', 'title' => 'Acces refuse', 'message' => 'Vous ne pouvez consulter que votre profil.'];
            $this->redirect('../view/listeUtilisateurs.php');
        }

        return [
            'profile' => $this->uc->RecupererUtilisateur($profileId)
        ];
    }

    public function update()
    {
        $this->requireAdmin('Seuls les administrateurs peuvent modifier des utilisateurs.');

        if (!$this->hasRequiredPost(['id', 'role_id', 'first_name', 'last_name', 'email'])) {
            $this->redirect('../view/listeUtilisateurs.php');
        }

        $oldUser = $this->uc->RecupererUtilisateur($_POST['id']);
        // On detecte les champs modifies avant l'enregistrement.
        $changes = $this->buildUserChanges($oldUser, $_POST);
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
        $sent = false;

        if (!empty($recipient)) {
            // Le mail de modification mentionne seulement les champs modifies.
            $mail = $this->buildProfileUpdatedMail($fullName, !empty($_POST['password']), $changes);
            $sent = MailC::send(
                $recipient,
                'Votre profil Workify a ete modifie',
                $mail['text'],
                $mail['html']
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
            // Le mail est prepare avant suppression, car apres suppression on perd les infos utilisateur.
            $mail = $this->buildAccountDeletedMail($fullName);
            $sent = MailC::send(
                $u['email'],
                'Votre compte Workify a ete supprime',
                $mail['text'],
                $mail['html']
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

    private function buildAccountCreatedMail($fullName, $email, $temporaryPassword)
    {
        // Mail envoye quand l'admin cree un nouvel utilisateur.
        $name = $fullName ?: 'utilisateur';
        $text = "Bonjour " . $name . ",\n\n"
            . "Votre compte Workify vient d'etre cree.\n\n"
            . "Email de connexion : " . $email . "\n"
            . "Mot de passe temporaire : " . $temporaryPassword . "\n\n"
            . "Pour votre securite, modifiez votre mot de passe apres votre premiere connexion.\n\n"
            . "Cordialement,\nWorkify";

        return [
            'text' => $text,
            'html' => $this->mailLayout(
                'Bienvenue sur Workify',
                'Votre compte est pret',
                'Bonjour ' . $name . ', votre acces Workify vient d etre cree par un administrateur.',
                [
                    ['label' => 'Email de connexion', 'value' => $email],
                    ['label' => 'Mot de passe temporaire', 'value' => $temporaryPassword]
                ],
                'Pour votre securite, changez ce mot de passe apres votre premiere connexion.'
            )
        ];
    }

    private function buildProfileUpdatedMail($fullName, $passwordChanged, array $changes)
    {
        // Mail envoye quand l'admin modifie un profil.
        $name = $fullName ?: 'utilisateur';
        $notice = $passwordChanged
            ? 'Votre mot de passe a egalement ete modifie par l administrateur.'
            : 'Vos informations de profil ont ete mises a jour.';
        $details = [
            ['label' => 'Statut', 'value' => 'Profil mis a jour'],
            ['label' => 'Securite', 'value' => $notice]
        ];
        $changeLines = '';

        if (!empty($changes)) {
            foreach ($changes as $change) {
                // On affiche seulement le nom du champ modifie, pas l'ancienne/nouvelle valeur.
                $details[] = [
                    'label' => 'Champ modifie',
                    'value' => $change['label']
                ];
                $changeLines .= '- ' . $change['label'] . "\n";
            }
        } else {
            $changeLines = '- Aucun changement detaille detecte.' . "\n";
        }

        $text = "Bonjour " . $name . ",\n\n"
            . "Vos informations de profil Workify ont ete modifiees par l'administrateur.\n"
            . ($passwordChanged ? "\nVotre mot de passe a egalement ete modifie par l'administrateur.\n" : "\n")
            . "Changements effectues :\n"
            . $changeLines . "\n"
            . "Si vous n'etes pas a l'origine de cette demande, contactez l'administrateur.\n\n"
            . "Cordialement,\nWorkify";

        return [
            'text' => $text,
            'html' => $this->mailLayout(
                'Mise a jour du profil',
                'Votre profil a ete modifie',
                'Bonjour ' . $name . ', une modification a ete appliquee a votre profil Workify.',
                $details,
                'Si vous n etes pas a l origine de cette demande, contactez rapidement votre administrateur.'
            )
        ];
    }

    private function buildUserChanges($oldUser, array $post)
    {
        // Compare les anciennes valeurs avec les nouvelles valeurs du formulaire.
        if (empty($oldUser) || !is_array($oldUser)) {
            return [];
        }

        $roleNames = $this->roleNamesById();
        $fields = [
            'role_id' => 'Role',
            'first_name' => 'Prenom',
            'last_name' => 'Nom',
            'email' => 'Email',
            'phone' => 'Telephone',
            'headline' => 'Titre',
            'bio' => 'Bio',
            'status' => 'Statut'
        ];
        $changes = [];

        foreach ($fields as $field => $label) {
            $oldValue = $oldUser[$field] ?? '';
            $newValue = $post[$field] ?? '';

            // Pour le role, on compare les noms lisibles plutot que les IDs.
            if ($field === 'role_id') {
                $oldValue = $roleNames[(int)$oldValue] ?? ($oldUser['role_name'] ?? ('Role #' . $oldValue));
                $newValue = $roleNames[(int)$newValue] ?? ('Role #' . $newValue);
            }

            if ($this->normalizeMailValue($oldValue) !== $this->normalizeMailValue($newValue)) {
                $changes[] = [
                    'label' => $label,
                    'old' => $this->formatMailValue($oldValue),
                    'new' => $this->formatMailValue($newValue)
                ];
            }
        }

        // Pour la securite, on ne met jamais le mot de passe dans le mail.
        if (!empty($post['password'])) {
            $changes[] = [
                'label' => 'Mot de passe',
                'old' => 'Ancien mot de passe',
                'new' => 'Modifie'
            ];
        }

        return $changes;
    }

    private function roleNamesById()
    {
        $roles = [];

        foreach ($this->uc->ListeRoles() as $role) {
            $roles[(int)$role['id']] = (string)$role['name'];
        }

        return $roles;
    }

    private function normalizeMailValue($value)
    {
        return trim(preg_replace('/\s+/', ' ', (string)$value));
    }

    private function formatMailValue($value)
    {
        $value = $this->normalizeMailValue($value);
        if ($value === '') {
            return 'Non renseigne';
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($value, 'UTF-8') > 80 ? mb_substr($value, 0, 77, 'UTF-8') . '...' : $value;
        }

        return strlen($value) > 80 ? substr($value, 0, 77) . '...' : $value;
    }

    private function buildAccountDeletedMail($fullName)
    {
        // Mail envoye quand un compte est supprime par l'admin.
        $name = $fullName ?: 'utilisateur';
        $text = "Bonjour " . $name . ",\n\n"
            . "Votre compte Workify a ete supprime par l'administrateur.\n"
            . "Vous ne pouvez plus vous connecter avec ce compte.\n\n"
            . "Cordialement,\nWorkify";

        return [
            'text' => $text,
            'html' => $this->mailLayout(
                'Compte supprime',
                'Votre acces a ete retire',
                'Bonjour ' . $name . ', votre compte Workify a ete supprime par un administrateur.',
                [
                    ['label' => 'Statut du compte', 'value' => 'Supprime'],
                    ['label' => 'Connexion', 'value' => 'Acces desactive']
                ],
                'Vous ne pouvez plus vous connecter avec ce compte.'
            )
        ];
    }

    private function mailLayout($preheader, $title, $intro, array $details, $footerNote)
    {
        // Template HTML commun a tous les emails Workify.
        $detailRows = '';
        foreach ($details as $detail) {
            $detailRows .= '<tr>'
                . '<td style="padding:12px 16px;border-bottom:1px solid #e8edf5;color:#64748b;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.02em;">' . $this->e($detail['label']) . '</td>'
                . '<td style="padding:12px 16px;border-bottom:1px solid #e8edf5;color:#0f172a;font-size:14px;font-weight:700;text-align:right;">' . $this->e($detail['value']) . '</td>'
                . '</tr>';
        }

        return '<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>'
            . '<body style="margin:0;padding:0;background:#eef2f7;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">'
            . '<span style="display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;overflow:hidden;">' . $this->e($preheader) . '</span>'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef2f7;padding:28px 12px;"><tr><td align="center">'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 18px 45px rgba(15,23,42,.12);">'
            . '<tr><td style="padding:28px 32px;background:#0f4c81;color:#ffffff;">'
            . '<div style="font-size:24px;font-weight:800;letter-spacing:.02em;">Workify</div>'
            . '<div style="margin-top:8px;font-size:14px;opacity:.88;">Gestion professionnelle des utilisateurs</div>'
            . '</td></tr>'
            . '<tr><td style="padding:30px 32px 18px;">'
            . '<h1 style="margin:0 0 12px;font-size:24px;line-height:1.25;color:#0f172a;">' . $this->e($title) . '</h1>'
            . '<p style="margin:0;color:#475569;font-size:15px;line-height:1.65;">' . $this->e($intro) . '</p>'
            . '</td></tr>'
            . '<tr><td style="padding:0 32px 22px;">'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e8edf5;border-radius:12px;overflow:hidden;background:#f8fafc;">'
            . $detailRows
            . '</table>'
            . '</td></tr>'
            . '<tr><td style="padding:0 32px 30px;">'
            . '<div style="border-left:4px solid #0f4c81;background:#f1f7ff;border-radius:10px;padding:14px 16px;color:#334155;font-size:14px;line-height:1.6;">' . $this->e($footerNote) . '</div>'
            . '</td></tr>'
            . '<tr><td style="padding:18px 32px;background:#f8fafc;border-top:1px solid #e8edf5;color:#64748b;font-size:12px;line-height:1.6;">'
            . 'Cet email est automatique. Merci de ne pas y repondre directement.<br>Workify - Module de gestion des utilisateurs'
            . '</td></tr>'
            . '</table></td></tr></table></body></html>';
    }

    private function e($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
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

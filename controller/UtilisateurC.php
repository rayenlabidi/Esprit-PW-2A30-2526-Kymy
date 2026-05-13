<?php
require_once __DIR__ . '/AuthC.php';
require_once __DIR__ . '/../Model/utilisateur.php';
require_once __DIR__ . '/UtilisateurModel.php';

class UtilisateurC
{
    private $utilisateurModel;

    public function __construct()
    {
        AuthC::requireAdmin();
        $this->utilisateurModel = new UtilisateurModel();
    }

    public function handleRequest()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';

        if ($action === 'add') {
            $this->ajouter();
        } elseif ($action === 'edit') {
            $this->modifier();
        } elseif ($action === 'delete') {
            $this->supprimer();
        } else {
            $this->liste();
        }
    }

    private function liste()
    {
        $office = 'back';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $role = isset($_GET['role']) ? trim($_GET['role']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';

        $liste = $this->utilisateurModel->listeUtilisateurs($search, $role, $status);
        $roles = $this->utilisateurModel->listeRoles();
        $statistiques = $this->utilisateurModel->statistiquesUtilisateurs();

        include __DIR__ . '/../view/users/list.php';
    }

    private function ajouter()
    {
        $office = 'back';
        $roles = $this->utilisateurModel->listeRoles();
        $errors = [];
        $formData = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = $_POST;
            $errors = $this->validerUtilisateur($_POST, 0, true);

            if (empty($errors)) {
                $avatarUrl = $this->sauverAvatar('avatar_file');
                $utilisateur = $this->construireUtilisateur($_POST, true, $avatarUrl);
                $this->utilisateurModel->addUtilisateur($utilisateur);
                header('Location: UtilisateurC.php?action=list');
                exit;
            }
        }

        include __DIR__ . '/../view/users/form.php';
    }

    private function modifier()
    {
        $office = 'back';
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $utilisateur = $this->utilisateurModel->getUtilisateurById($id);

        if (!$utilisateur) {
            die('Utilisateur introuvable.');
        }

        $roles = $this->utilisateurModel->listeRoles();
        $errors = [];
        $formData = $utilisateur;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = $_POST;
            $formData['id'] = $id;
            $errors = $this->validerUtilisateur($_POST, $id, false);

            if (empty($errors)) {
                $avatarUrl = $this->sauverAvatar('avatar_file');
                if ($avatarUrl === '' && isset($utilisateur['avatar_url'])) {
                    $avatarUrl = $utilisateur['avatar_url'];
                }
                $utilisateurObjet = $this->construireUtilisateur($_POST, false, $avatarUrl);
                $this->utilisateurModel->updateUtilisateur($utilisateurObjet, $id);
                header('Location: UtilisateurC.php?action=list');
                exit;
            }
        }

        include __DIR__ . '/../view/users/form.php';
    }

    private function supprimer()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id > 0 && (!isset($_SESSION['user_id']) || $id !== (int) $_SESSION['user_id'])) {
            $this->utilisateurModel->deleteUtilisateur($id);
        }

        header('Location: UtilisateurC.php?action=list');
        exit;
    }

    private function construireUtilisateur($data, $passwordRequired = true, $avatarUrl = '')
    {
        return new utilisateur(
            (int) $data['role_id'],
            trim($data['first_name']),
            trim($data['last_name']),
            trim($data['email']),
            isset($data['phone']) ? trim($data['phone']) : '',
            isset($data['password']) ? trim($data['password']) : '',
            trim($data['headline']),
            trim($data['bio']),
            trim($data['status']),
            $avatarUrl
        );
    }

    private function validerUtilisateur($data, $ignoreId = 0, $passwordRequired = true)
    {
        $errors = [];

        if (!isset($data['role_id']) || (int) $data['role_id'] <= 0) {
            $errors[] = 'Veuillez choisir un role.';
        }

        if (!isset($data['first_name']) || strlen(trim($data['first_name'])) < 2) {
            $errors[] = 'Le prenom doit contenir au moins 2 caracteres.';
        }

        if (!isset($data['last_name']) || strlen(trim($data['last_name'])) < 2) {
            $errors[] = 'Le nom doit contenir au moins 2 caracteres.';
        }

        if (!isset($data['email']) || !filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Veuillez saisir un email valide.';
        } elseif ($this->utilisateurModel->emailExiste(trim($data['email']), $ignoreId)) {
            $errors[] = 'Cet email est deja utilise.';
        }

        if ($passwordRequired && (!isset($data['password']) || strlen(trim($data['password'])) < 6)) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caracteres.';
        }

        if (!$passwordRequired && isset($data['password']) && trim($data['password']) !== '' && strlen(trim($data['password'])) < 6) {
            $errors[] = 'Le nouveau mot de passe doit contenir au moins 6 caracteres.';
        }

        if (!isset($data['headline']) || strlen(trim($data['headline'])) < 3) {
            $errors[] = 'Le titre doit contenir au moins 3 caracteres.';
        }

        if (!isset($data['bio']) || strlen(trim($data['bio'])) < 10) {
            $errors[] = 'La bio doit contenir au moins 10 caracteres.';
        }

        if (!isset($data['status']) || !in_array($data['status'], ['active', 'pending', 'blocked'])) {
            $errors[] = 'Veuillez choisir un statut valide.';
        }

        return $errors;
    }

    private function sauverAvatar($fieldName)
    {
        if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return '';
        }

        $extension = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            return '';
        }

        $uploadDir = __DIR__ . '/../uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = 'profile_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadDir . $fileName)) {
            return 'uploads/profiles/' . $fileName;
        }

        return '';
    }
}

$controller = new UtilisateurC();
$controller->handleRequest();
?>

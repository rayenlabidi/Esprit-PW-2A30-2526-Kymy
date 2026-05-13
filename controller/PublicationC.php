<?php
require_once __DIR__ . '/../Model/publication.php';
require_once __DIR__ . '/PublicationModel.php';
require_once __DIR__ . '/UtilisateurModel.php';
require_once __DIR__ . '/AuthC.php';

class PublicationC
{
    private $publicationModel;
    private $utilisateurModel;

    public function __construct()
    {
        $this->publicationModel = new PublicationModel();
        $this->utilisateurModel = new UtilisateurModel();
    }

    public function handleRequest()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';

        if ($action === 'add') {
            $this->ajouter();
        } elseif ($action === 'like') {
            $this->aimer();
        } elseif ($action === 'comment') {
            $this->commenter();
        } elseif ($action === 'delete') {
            $this->supprimer();
        } else {
            $this->liste();
        }
    }

    private function office()
    {
        return (isset($_GET['office']) && $_GET['office'] === 'back') ? 'back' : 'front';
    }

    private function liste()
    {
        $office = $this->office();

        if ($office === 'back') {
            AuthC::requireAdmin();
        } else {
            $this->requireLogin();
        }

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $publications = $this->publicationModel->listePublications($search);
        $commentaires = [];
        foreach ($publications as $publicationItem) {
            $commentaires[(int) $publicationItem['id']] = $this->publicationModel->listeCommentaires((int) $publicationItem['id']);
        }
        $likedPublications = ($office === 'front' && AuthC::isLoggedIn())
            ? $this->publicationModel->likedPublicationIds(AuthC::currentUserId())
            : [];
        $statistiques = $this->publicationModel->statistiquesPublications();
        $errors = isset($_SESSION['publication_errors']) ? $_SESSION['publication_errors'] : [];
        unset($_SESSION['publication_errors']);

        include __DIR__ . '/../view/publications/list.php';
    }

    private function ajouter()
    {
        $office = $this->office();
        if ($office === 'back') {
            AuthC::requireAdmin();
        } else {
            $this->requireLogin();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: PublicationC.php?office=' . $office . '&action=list');
            exit;
        }

        $content = trim($_POST['content'] ?? '');
        $imageUrl = trim($_POST['image_url'] ?? '');

        if (strlen($content) < 5) {
            $_SESSION['publication_errors'] = ['La publication doit contenir au moins 5 caracteres.'];
            header('Location: PublicationC.php?office=' . $office . '&action=list');
            exit;
        }

        $user = $this->currentUser();
        $payload = $this->userPayload($user);
        $publication = new publication(
            $payload['id'],
            $payload['name'],
            $payload['init'],
            $payload['role'],
            $payload['avatar'],
            $this->clean($content),
            $this->cleanUrl($imageUrl)
        );

        $this->publicationModel->addPublication($publication);
        header('Location: PublicationC.php?office=' . $office . '&action=list');
        exit;
    }

    private function commenter()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $publicationId = isset($_POST['publication_id']) ? (int) $_POST['publication_id'] : 0;
            $comment = trim($_POST['comment'] ?? '');
            $publication = $this->publicationModel->getPublicationById($publicationId);

            if ($publication && strlen($comment) >= 2) {
                $payload = $this->userPayload($this->currentUser());
                $this->publicationModel->addCommentaire(
                    $publicationId,
                    $payload['name'],
                    $payload['init'],
                    $payload['avatar'],
                    $this->clean($comment)
                );
            }
        }

        header('Location: PublicationC.php?office=front&action=list');
        exit;
    }

    private function aimer()
    {
        $this->requireLogin();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id > 0 && $this->publicationModel->getPublicationById($id)) {
            $this->publicationModel->toggleLike($id, AuthC::currentUserId());
        }

        header('Location: PublicationC.php?office=front&action=list#publication-' . $id);
        exit;
    }

    private function supprimer()
    {
        $office = $this->office();
        if ($office === 'back') {
            AuthC::requireAdmin();
        } else {
            $this->requireLogin();
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $publication = $this->publicationModel->getPublicationById($id);

        if ($publication) {
            $isOwner = (string) $publication['user_id'] === (string) AuthC::currentUserId();
            if ($office === 'back' || AuthC::isAdmin() || $isOwner) {
                $this->publicationModel->deletePublication($id);
            }
        }

        header('Location: PublicationC.php?office=' . $office . '&action=list');
        exit;
    }

    private function requireLogin()
    {
        AuthC::startSession();
        if (!AuthC::isLoggedIn()) {
            $_SESSION['redirect_after_login'] = 'PublicationC.php?office=front&action=list';
            header('Location: AuthController.php?action=login');
            exit;
        }
    }

    private function currentUser()
    {
        $user = $this->utilisateurModel->getUtilisateurById(AuthC::currentUserId());
        if (!$user) {
            AuthC::logout();
            header('Location: AuthController.php?action=login');
            exit;
        }
        return $user;
    }

    private function userPayload($user)
    {
        $firstName = trim((string) $user['first_name']);
        $lastName = trim((string) $user['last_name']);
        $name = trim($firstName . ' ' . $lastName);
        $initials = '';

        if ($firstName !== '') {
            $initials .= strtoupper(substr($firstName, 0, 1));
        }
        if ($lastName !== '') {
            $initials .= strtoupper(substr($lastName, 0, 1));
        }

        $avatars = ['av-blue', 'av-green', 'av-orange', 'av-purple', 'av-pink', 'av-teal'];

        return [
            'id' => (string) $user['id'],
            'name' => $name !== '' ? $name : $user['email'],
            'init' => $initials !== '' ? $initials : 'WK',
            'role' => $user['role_slug'] === 'boss' ? 'Client' : 'Freelancer',
            'avatar' => $avatars[((int) $user['id']) % count($avatars)]
        ];
    }

    private function clean($value)
    {
        return trim(strip_tags($value));
    }

    private function cleanUrl($value)
    {
        $value = trim($value);
        return filter_var($value, FILTER_VALIDATE_URL) ? $value : '';
    }
}

$controller = new PublicationC();
$controller->handleRequest();
?>

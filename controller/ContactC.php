<?php
require_once __DIR__ . '/AuthC.php';
require_once __DIR__ . '/ContactModel.php';

class ContactC
{
    private $contactModel;

    public function __construct()
    {
        $this->contactModel = new ContactModel();
    }

    public function handleRequest()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'send';

        if ($action === 'list') {
            $this->liste();
        } elseif ($action === 'status') {
            $this->changerStatut();
        } elseif ($action === 'delete') {
            $this->supprimer();
        } else {
            $this->envoyer();
        }
    }

    private function envoyer()
    {
        AuthC::startSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: HomeC.php#contact');
            exit;
        }

        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'subject' => trim($_POST['subject'] ?? ''),
            'message' => trim($_POST['message'] ?? '')
        ];

        $errors = $this->validerMessage($data);

        if (!empty($errors)) {
            $_SESSION['contact_errors'] = $errors;
            $_SESSION['contact_old'] = $data;
            header('Location: HomeC.php#contact');
            exit;
        }

        $this->contactModel->addMessage(
            $this->clean($data['full_name']),
            $this->clean($data['email']),
            $this->clean($data['subject']),
            $this->clean($data['message'])
        );

        $_SESSION['contact_success'] = 'Votre message a ete envoye. L equipe Workify vous repondra rapidement.';
        unset($_SESSION['contact_old'], $_SESSION['contact_errors']);
        header('Location: HomeC.php#contact');
        exit;
    }

    private function liste()
    {
        AuthC::requireAdmin();
        $office = 'back';
        $activeModule = 'contacts';
        $pageTitle = 'Messages Contact';
        $messages = $this->contactModel->listMessages();
        include __DIR__ . '/../view/contact/list.php';
    }

    private function changerStatut()
    {
        AuthC::requireAdmin();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';

        if ($id > 0 && in_array($status, ['new', 'read', 'archived'])) {
            $this->contactModel->updateStatus($id, $status);
        }

        header('Location: ContactC.php?action=list');
        exit;
    }

    private function supprimer()
    {
        AuthC::requireAdmin();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id > 0) {
            $this->contactModel->deleteMessage($id);
        }

        header('Location: ContactC.php?action=list');
        exit;
    }

    private function validerMessage($data)
    {
        $errors = [];

        if (strlen($data['full_name']) < 3) {
            $errors[] = 'Le nom doit contenir au moins 3 caracteres.';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Veuillez saisir un email valide.';
        }

        if (strlen($data['subject']) < 4) {
            $errors[] = 'Le sujet doit contenir au moins 4 caracteres.';
        }

        if (strlen($data['message']) < 20) {
            $errors[] = 'Le message doit contenir au moins 20 caracteres.';
        }

        return $errors;
    }

    private function clean($value)
    {
        return trim(strip_tags($value));
    }
}

$controller = new ContactC();
$controller->handleRequest();
?>

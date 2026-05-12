<?php
require_once __DIR__ . '/../Model/message.php';
require_once __DIR__ . '/MessageModel.php';
require_once __DIR__ . '/UtilisateurModel.php';
require_once __DIR__ . '/AuthC.php';

class MessageC
{
    private $messageModel;
    private $utilisateurModel;

    public function __construct()
    {
        $this->messageModel = new MessageModel();
        $this->utilisateurModel = new UtilisateurModel();
    }

    public function handleRequest()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        $office = $this->office();

        if ($action === 'send') {
            $this->envoyer();
        } elseif ($action === 'delete') {
            $this->supprimer();
        } else {
            $office === 'back' ? $this->admin() : $this->liste();
        }
    }

    private function office()
    {
        return (isset($_GET['office']) && $_GET['office'] === 'back') ? 'back' : 'front';
    }

    private function liste()
    {
        $this->requireLogin();

        $office = 'front';
        $currentUser = $this->currentUser();
        $contacts = $this->messageModel->listeUtilisateursContactables((int) $currentUser['id']);
        $with = isset($_GET['with']) ? (int) $_GET['with'] : 0;

        if ($with <= 0 && !empty($contacts)) {
            $with = (int) $contacts[0]['id'];
        }

        $selectedUser = $with > 0 ? $this->utilisateurModel->getUtilisateurById($with) : null;
        $messages = $selectedUser ? $this->messageModel->listeMessages((int) $currentUser['id'], (int) $selectedUser['id']) : [];
        $errors = isset($_SESSION['message_errors']) ? $_SESSION['message_errors'] : [];
        unset($_SESSION['message_errors']);

        include __DIR__ . '/../view/messages/list.php';
    }

    private function admin()
    {
        AuthC::requireAdmin();
        $office = 'back';
        $messages = $this->messageModel->listeMessagesAdmin();
        include __DIR__ . '/../view/messages/admin.php';
    }

    private function envoyer()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: MessageC.php?office=front&action=list');
            exit;
        }

        $receiverId = isset($_POST['receiver_id']) ? (int) $_POST['receiver_id'] : 0;
        $content = trim($_POST['content'] ?? '');
        $currentUser = $this->currentUser();
        $receiver = $receiverId > 0 ? $this->utilisateurModel->getUtilisateurById($receiverId) : null;

        if (!$receiver || strlen($content) < 2) {
            $_SESSION['message_errors'] = ['Choisissez un destinataire et ecrivez un message valide.'];
            header('Location: MessageC.php?office=front&action=list');
            exit;
        }

        $senderPayload = $this->userPayload($currentUser);
        $receiverPayload = $this->userPayload($receiver);
        $message = new message(
            $senderPayload['id'],
            $receiverPayload['id'],
            $senderPayload['name'],
            $receiverPayload['name'],
            $senderPayload['init'],
            $receiverPayload['init'],
            $senderPayload['avatar'],
            $receiverPayload['avatar'],
            $this->clean($content),
            null
        );

        $this->messageModel->addMessage($message);
        header('Location: MessageC.php?office=front&action=list&with=' . (int) $receiver['id']);
        exit;
    }

    private function supprimer()
    {
        $office = $this->office();
        AuthC::requireAdmin();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id > 0) {
            $this->messageModel->deleteMessage($id);
        }

        header('Location: MessageC.php?office=' . $office . '&action=list');
        exit;
    }

    private function requireLogin()
    {
        AuthC::startSession();
        if (!AuthC::isLoggedIn()) {
            $_SESSION['redirect_after_login'] = 'MessageC.php?office=front&action=list';
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
            'avatar' => $avatars[((int) $user['id']) % count($avatars)]
        ];
    }

    private function clean($value)
    {
        return trim(strip_tags($value));
    }
}

$controller = new MessageC();
$controller->handleRequest();
?>

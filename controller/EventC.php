<?php
require_once __DIR__ . '/AuthC.php';
require_once __DIR__ . '/EventModel.php';

class EventC
{
    private $eventModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
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
        } elseif ($action === 'detail') {
            $this->detail();
        } elseif ($action === 'calendar') {
            $this->calendar();
        } elseif ($action === 'calendar_json') {
            $this->calendarJson();
        } elseif ($action === 'categories') {
            $this->categories();
        } elseif ($action === 'add_category') {
            $this->ajouterCategorie();
        } elseif ($action === 'edit_category') {
            $this->modifierCategorie();
        } elseif ($action === 'delete_category') {
            $this->supprimerCategorie();
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
        $this->authorize($office);

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $category = isset($_GET['category']) ? trim($_GET['category']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'date_desc';

        $events = $this->eventModel->listeEvents($search, $category, $status, $sort);
        $categories = $this->eventModel->listeCategories();
        $statistiques = $this->eventModel->statistiquesEvents();

        include __DIR__ . '/../view/events/list.php';
    }

    private function detail()
    {
        $office = $this->office();
        $this->authorize($office);

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $event = $this->eventModel->getEventById($id);

        if (!$event) {
            die('Evenement introuvable.');
        }

        $isRegistered = $this->eventModel->isRegistered($id, AuthC::currentUserId());
        include __DIR__ . '/../view/events/detail.php';
    }

    private function ajouter()
    {
        $this->authorize('back');
        $office = 'back';
        $categories = $this->eventModel->listeCategories();
        $errors = [];
        $formData = [
            'status' => 'upcoming',
            'max_participants' => 50,
            'organizer_id' => AuthC::currentUserId()
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = $_POST;
            $errors = $this->validerEvent($_POST);

            if (empty($errors)) {
                $payload = $this->buildPayload($_POST);
                $this->eventModel->addEvent($payload);
                header('Location: EventC.php?office=back&action=list');
                exit;
            }
        }

        include __DIR__ . '/../view/events/form.php';
    }

    private function modifier()
    {
        $this->authorize('back');
        $office = 'back';
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $event = $this->eventModel->getEventById($id);

        if (!$event) {
            die('Evenement introuvable.');
        }

        $categories = $this->eventModel->listeCategories();
        $errors = [];
        $formData = $event;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = $_POST;
            $formData['id'] = $id;
            $errors = $this->validerEvent($_POST);

            if (empty($errors)) {
                $payload = $this->buildPayload($_POST, $event);
                $this->eventModel->updateEvent($id, $payload);
                header('Location: EventC.php?office=back&action=list');
                exit;
            }
        }

        include __DIR__ . '/../view/events/form.php';
    }

    private function supprimer()
    {
        $this->authorize('back');
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id > 0) {
            $this->eventModel->deleteEvent($id);
        }

        header('Location: EventC.php?office=back&action=list');
        exit;
    }

    private function calendar()
    {
        $office = $this->office();
        $this->authorize($office);
        $categories = $this->eventModel->listeCategories();
        include __DIR__ . '/../view/events/calendar.php';
    }

    private function calendarJson()
    {
        $office = $this->office();
        $this->authorize($office);
        $events = [];

        foreach ($this->eventModel->calendarEvents() as $event) {
            $color = $this->categoryColor((string) ($event['category_name'] ?? ''), (int) $event['event_category_id']);
            $events[] = [
                'id' => (int) $event['id'],
                'title' => $event['title'],
                'date' => date('Y-m-d', strtotime($event['event_date'])),
                'time' => date('H:i', strtotime($event['event_date'])),
                'url' => 'EventC.php?office=' . $office . '&action=detail&id=' . (int) $event['id'],
                'color' => $color,
                'category' => $event['category_name'] ?: 'Autre',
                'location' => !empty($event['is_online']) ? 'En ligne' : $event['location'],
                'status' => $event['status']
            ];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($events);
        exit;
    }

    private function categories()
    {
        $this->authorize('back');
        $office = 'back';
        $categories = $this->eventModel->listeCategories();
        include __DIR__ . '/../view/events/categories.php';
    }

    private function ajouterCategorie()
    {
        $this->authorize('back');
        $office = 'back';
        $errors = [];
        $formData = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = $_POST;
            $errors = $this->validerCategorie($_POST);

            if (empty($errors)) {
                $this->eventModel->addCategory($_POST);
                header('Location: EventC.php?office=back&action=categories');
                exit;
            }
        }

        include __DIR__ . '/../view/events/category_form.php';
    }

    private function modifierCategorie()
    {
        $this->authorize('back');
        $office = 'back';
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $category = $this->eventModel->getCategoryById($id);

        if (!$category) {
            die('Categorie introuvable.');
        }

        $errors = [];
        $formData = $category;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = $_POST;
            $errors = $this->validerCategorie($_POST);

            if (empty($errors)) {
                $this->eventModel->updateCategory($id, $_POST);
                header('Location: EventC.php?office=back&action=categories');
                exit;
            }
        }

        include __DIR__ . '/../view/events/category_form.php';
    }

    private function supprimerCategorie()
    {
        $this->authorize('back');
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id > 0) {
            $this->eventModel->deleteCategory($id);
        }

        header('Location: EventC.php?office=back&action=categories');
        exit;
    }

    private function register()
    {
        AuthC::startSession();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!AuthC::hasRole('freelancer')) {
            $_SESSION['event_flash'] = 'Connectez-vous avec un compte freelancer pour participer a cet evenement.';
            header('Location: EventC.php?office=front&action=detail&id=' . $id);
            exit;
        }
        $this->eventModel->registerForEvent($id, AuthC::currentUserId());
        $_SESSION['event_flash'] = 'Votre participation a ete enregistree.';
        header('Location: EventC.php?office=front&action=detail&id=' . $id);
        exit;
    }    private function authorize($office)
    {
        AuthC::startSession();

        if ($office === 'back') {
            if (!AuthC::hasRole(['admin', 'boss', 'enterprise', 'entreprise'])) {
                $_SESSION['redirect_after_login'] = 'EventC.php?office=back&action=list';
                header('Location: AuthController.php?action=login');
                exit;
            }
            return;
        }


        return;

    }

    private function buildPayload($data, $existing = [])
    {
        $imageUrl = $this->sauverImage('image_file');
        if ($imageUrl === '') {
            $imageUrl = isset($data['image_url']) && trim($data['image_url']) !== ''
                ? trim($data['image_url'])
                : (isset($existing['image_url']) ? $existing['image_url'] : $this->fallbackImage($data['title']));
        }

        return [
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'event_date' => trim($data['event_date']),
            'location' => trim($data['location']),
            'is_online' => isset($data['is_online']) ? 1 : 0,
            'max_participants' => (int) $data['max_participants'],
            'status' => trim($data['status']),
            'organizer_id' => AuthC::currentUserId() > 0 ? AuthC::currentUserId() : 1,
            'category_id' => (int) $data['category_id'],
            'image_url' => $imageUrl,
            'latitude' => isset($data['latitude']) ? trim($data['latitude']) : '',
            'longitude' => isset($data['longitude']) ? trim($data['longitude']) : ''
        ];
    }

    private function validerEvent($data)
    {
        $errors = [];
        $statuses = ['upcoming', 'ongoing', 'completed', 'cancelled'];

        if (!isset($data['title']) || strlen(trim($data['title'])) < 3) {
            $errors[] = 'Le titre doit contenir au moins 3 caracteres.';
        }

        if (!isset($data['description']) || strlen(trim($data['description'])) < 10) {
            $errors[] = 'La description doit contenir au moins 10 caracteres.';
        }

        if (!isset($data['event_date']) || trim($data['event_date']) === '') {
            $errors[] = 'La date de l evenement est obligatoire.';
        }

        if (!isset($data['location']) || strlen(trim($data['location'])) < 2) {
            $errors[] = 'La localisation doit contenir au moins 2 caracteres.';
        }

        if (!isset($data['max_participants']) || (int) $data['max_participants'] <= 0) {
            $errors[] = 'Le nombre de participants doit etre positif.';
        }

        if (!isset($data['status']) || !in_array($data['status'], $statuses, true)) {
            $errors[] = 'Veuillez choisir un statut valide.';
        }

        if (!isset($data['category_id']) || (int) $data['category_id'] <= 0) {
            $errors[] = 'Veuillez choisir une categorie.';
        }

        return $errors;
    }

    private function validerCategorie($data)
    {
        $errors = [];

        if (!isset($data['name']) || strlen(trim($data['name'])) < 3) {
            $errors[] = 'Le nom de categorie doit contenir au moins 3 caracteres.';
        }

        if (!isset($data['description']) || strlen(trim($data['description'])) < 8) {
            $errors[] = 'La description doit contenir au moins 8 caracteres.';
        }

        return $errors;
    }

    private function sauverImage($fieldName)
    {
        if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return '';
        }

        $extension = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return '';
        }

        $uploadDir = __DIR__ . '/../uploads/events/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = 'event_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadDir . $fileName)) {
            return 'uploads/events/' . $fileName;
        }

        return '';
    }

    private function fallbackImage($title)
    {
        $text = strtolower((string) $title);
        if (strpos($text, 'cyber') !== false) {
            return 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1200&q=80';
        }
        if (strpos($text, 'design') !== false || strpos($text, 'ux') !== false) {
            return 'https://images.unsplash.com/photo-1559028012-481c04fa702d?auto=format&fit=crop&w=1200&q=80';
        }
        return 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1200&q=80';
    }

    private function categoryColor($category, $categoryId)
    {
        $palette = [
            'intelligence' => '#2563eb',
            'ai' => '#2563eb',
            'cyber' => '#ef4444',
            'cloud' => '#f97316',
            'devops' => '#f97316',
            'blockchain' => '#f59e0b',
            'web3' => '#f59e0b',
            'ux' => '#16a34a',
            'design' => '#16a34a',
            'green' => '#0f766e',
            'robot' => '#0891b2',
            'iot' => '#0891b2'
        ];
        $fallbacks = ['#2563eb', '#16a34a', '#f97316', '#0891b2', '#9333ea', '#f59e0b'];
        $lower = strtolower($category);

        foreach ($palette as $keyword => $color) {
            if (strpos($lower, $keyword) !== false) {
                return $color;
            }
        }

        return $fallbacks[$categoryId % count($fallbacks)];
    }
}

$controller = new EventC();
$controller->handleRequest();
?>

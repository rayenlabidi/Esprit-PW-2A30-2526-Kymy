<?php

require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Formation.php';
require_once __DIR__ . '/../models/Enrollment.php';

class FormationController extends BaseController
{
    public function index(): void
    {
        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'category_id' => $_GET['category_id'] ?? '',
            'level' => $_GET['level'] ?? '',
            'status' => $_GET['status'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
        ];

        $formations = [];
        $categories = [];
        $stats = ['total' => 0, 'published' => 0, 'enrollments' => 0, 'average_price' => 0];

        if ($this->db) {
            $formationModel = new Formation($this->db);
            $categoryModel = new Category($this->db);
            $formations = $formationModel->all($filters);
            $categories = $categoryModel->all('formation');
            $stats = $formationModel->stats();
        }

        $this->render('formations/index', compact('formations', 'categories', 'filters', 'stats'));
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $formation = null;
        $isEnrolled = false;

        if ($this->db) {
            $formationModel = new Formation($this->db);
            $enrollmentModel = new Enrollment($this->db);
            $formation = $formationModel->find($id);

            if ($formation && is_logged_in()) {
                $isEnrolled = $enrollmentModel->isEnrolled(auth_user()['id'], $id);
            }
        }

        if (!$formation) {
            set_flash('error', 'Formation introuvable.');
            redirect(['module' => 'formations', 'action' => 'index']);
        }

        $this->render('formations/show', compact('formation', 'isEnrolled'));
    }

    public function create(): void
    {
        require_roles(['admin', 'freelancer']);
        $this->handleForm('create');
    }

    public function edit(): void
    {
        require_roles(['admin', 'freelancer']);
        $this->handleForm('edit');
    }

    public function delete(): void
    {
        require_roles(['admin', 'freelancer']);
        $id = (int) ($_GET['id'] ?? 0);

        if ($this->db && $id > 0) {
            $formationModel = new Formation($this->db);
            $formation = $formationModel->find($id);

            if (!$formation) {
                set_flash('error', 'Formation introuvable.');
                redirect(['module' => 'formations', 'action' => 'index']);
            }

            if (!has_role(['admin']) && (int) $formation['creator_id'] !== (int) auth_user()['id']) {
                set_flash('error', 'Vous pouvez modifier uniquement vos propres formations.');
                redirect(['module' => 'formations', 'action' => 'index']);
            }

            $formationModel->delete($id);
            set_flash('success', 'Formation supprimee avec succes.');
        }

        redirect(['module' => 'formations', 'action' => 'index']);
    }

    public function enroll(): void
    {
        require_roles(['freelancer', 'admin']);

        $formationId = (int) ($_GET['id'] ?? 0);

        if ($this->db && $formationId > 0) {
            $formationModel = new Formation($this->db);
            $formation = $formationModel->find($formationId);

            if ($formation) {
                $enrollmentModel = new Enrollment($this->db);
                $enrollmentModel->enroll((int) auth_user()['id'], $formationId);
                set_flash('success', 'Inscription effectuee avec succes.');
                redirect(['module' => 'formations', 'action' => 'show', 'id' => $formationId]);
            }
        }

        set_flash('error', 'Impossible d effectuer l inscription.');
        redirect(['module' => 'formations', 'action' => 'index']);
    }

    private function handleForm(string $mode): void
    {
        $formationId = (int) ($_GET['id'] ?? 0);
        $formation = null;
        $categories = [];
        $creators = [];

        if ($this->db) {
            $formationModel = new Formation($this->db);
            $categoryModel = new Category($this->db);
            $userModel = new User($this->db);

            $categories = $categoryModel->all('formation');
            $creators = array_filter(
                $userModel->all(),
                fn ($user) => in_array($user['role_slug'], ['admin', 'freelancer'], true)
            );

            if ($mode === 'edit') {
                $formation = $formationModel->find($formationId);

                if (!$formation) {
                    set_flash('error', 'Formation introuvable.');
                    redirect(['module' => 'formations', 'action' => 'index']);
                }

                if (!has_role(['admin']) && (int) $formation['creator_id'] !== (int) auth_user()['id']) {
                    set_flash('error', 'Vous pouvez modifier uniquement vos propres formations.');
                    redirect(['module' => 'formations', 'action' => 'index']);
                }
            }

            if (is_post()) {
                $payload = [
                    'title' => $_POST['title'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'category_id' => $_POST['category_id'] ?? null,
                    'level' => $_POST['level'] ?? 'Beginner',
                    'price' => $_POST['price'] ?? 0,
                    'duration_hours' => $_POST['duration_hours'] ?? 0,
                    'status' => $_POST['status'] ?? 'draft',
                    'creator_id' => has_role(['admin']) ? ($_POST['creator_id'] ?? auth_user()['id']) : auth_user()['id'],
                    'image_url' => $_POST['image_url'] ?? '',
                    'tags' => $_POST['tags'] ?? '',
                ];

                if ($mode === 'create') {
                    $formationModel->create($payload);
                    set_flash('success', 'Formation creee avec succes.');
                } else {
                    $formationModel->update($formationId, $payload);
                    set_flash('success', 'Formation mise a jour avec succes.');
                }

                redirect(['module' => 'formations', 'action' => 'index']);
            }
        }

        $this->render('formations/form', compact('mode', 'formation', 'categories', 'creators'));
    }
}

<?php

require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Job.php';
require_once __DIR__ . '/../models/Application.php';

class JobController extends BaseController
{
    public function index(): void
    {
        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'category_id' => $_GET['category_id'] ?? '',
            'job_type' => $_GET['job_type'] ?? '',
            'status' => $_GET['status'] ?? '',
            'remote_only' => $_GET['remote_only'] ?? '',
        ];

        $jobs = [];
        $categories = [];
        $stats = ['total' => 0, 'open' => 0, 'applications' => 0, 'average_budget' => 0];

        if ($this->db) {
            $jobModel = new Job($this->db);
            $categoryModel = new Category($this->db);
            $jobs = $jobModel->all($filters);
            $categories = $categoryModel->all('job');
            $stats = $jobModel->stats();
        }

        $this->render('jobs/index', compact('jobs', 'categories', 'filters', 'stats'));
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $job = null;
        $hasApplied = false;

        if ($this->db) {
            $jobModel = new Job($this->db);
            $applicationModel = new Application($this->db);
            $job = $jobModel->find($id);

            if ($job && is_logged_in()) {
                $hasApplied = $applicationModel->hasApplied(auth_user()['id'], $id);
            }
        }

        if (!$job) {
            set_flash('error', 'Job introuvable.');
            redirect(['module' => 'jobs', 'action' => 'index']);
        }

        $this->render('jobs/show', compact('job', 'hasApplied'));
    }

    public function create(): void
    {
        require_roles(['admin', 'boss']);
        $this->handleForm('create');
    }

    public function edit(): void
    {
        require_roles(['admin', 'boss']);
        $this->handleForm('edit');
    }

    public function delete(): void
    {
        require_roles(['admin', 'boss']);
        $id = (int) ($_GET['id'] ?? 0);

        if ($this->db && $id > 0) {
            $jobModel = new Job($this->db);
            $job = $jobModel->find($id);

            if (!$job) {
                set_flash('error', 'Job introuvable.');
                redirect(['module' => 'jobs', 'action' => 'index']);
            }

            if (!has_role(['admin']) && (int) $job['publisher_id'] !== (int) auth_user()['id']) {
                set_flash('error', 'Vous pouvez modifier uniquement vos propres jobs.');
                redirect(['module' => 'jobs', 'action' => 'index']);
            }

            $jobModel->delete($id);
            set_flash('success', 'Job supprime avec succes.');
        }

        redirect(['module' => 'jobs', 'action' => 'index']);
    }

    public function apply(): void
    {
        require_roles(['freelancer', 'admin']);
        $jobId = (int) ($_GET['id'] ?? 0);

        if ($this->db && $jobId > 0 && is_post()) {
            $applicationModel = new Application($this->db);
            $applicationModel->apply((int) auth_user()['id'], $jobId, $_POST['cover_letter'] ?? '');
            set_flash('success', 'Votre candidature a ete envoyee.');
            redirect(['module' => 'jobs', 'action' => 'show', 'id' => $jobId]);
        }

        set_flash('error', 'Impossible d envoyer la candidature.');
        redirect(['module' => 'jobs', 'action' => 'index']);
    }

    private function handleForm(string $mode): void
    {
        $jobId = (int) ($_GET['id'] ?? 0);
        $job = null;
        $categories = [];
        $publishers = [];

        if ($this->db) {
            $jobModel = new Job($this->db);
            $categoryModel = new Category($this->db);
            $userModel = new User($this->db);

            $categories = $categoryModel->all('job');
            $publishers = array_filter(
                $userModel->all(),
                fn ($user) => in_array($user['role_slug'], ['admin', 'boss'], true)
            );

            if ($mode === 'edit') {
                $job = $jobModel->find($jobId);

                if (!$job) {
                    set_flash('error', 'Job introuvable.');
                    redirect(['module' => 'jobs', 'action' => 'index']);
                }

                if (!has_role(['admin']) && (int) $job['publisher_id'] !== (int) auth_user()['id']) {
                    set_flash('error', 'Vous pouvez modifier uniquement vos propres jobs.');
                    redirect(['module' => 'jobs', 'action' => 'index']);
                }
            }

            if (is_post()) {
                $payload = [
                    'title' => $_POST['title'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'budget' => $_POST['budget'] ?? 0,
                    'category_id' => $_POST['category_id'] ?? null,
                    'location' => $_POST['location'] ?? '',
                    'is_remote' => $_POST['is_remote'] ?? 0,
                    'job_type' => $_POST['job_type'] ?? 'Freelance',
                    'status' => $_POST['status'] ?? 'open',
                    'publisher_id' => has_role(['admin']) ? ($_POST['publisher_id'] ?? auth_user()['id']) : auth_user()['id'],
                ];

                if ($mode === 'create') {
                    $jobModel->create($payload);
                    set_flash('success', 'Job cree avec succes.');
                } else {
                    $jobModel->update($jobId, $payload);
                    set_flash('success', 'Job mis a jour avec succes.');
                }

                redirect(['module' => 'jobs', 'action' => 'index']);
            }
        }

        $this->render('jobs/form', compact('mode', 'job', 'categories', 'publishers'));
    }
}

<?php
require_once __DIR__ . '/../Model/job.php';
require_once __DIR__ . '/../Model/candidature.php';
require_once __DIR__ . '/JobModel.php';
require_once __DIR__ . '/ApplicationModel.php';
require_once __DIR__ . '/AuthC.php';

class JobC
{
    private $jobModel;
    private $applicationModel;

    public function __construct()
    {
        $this->jobModel = new JobModel();
        $this->applicationModel = new ApplicationModel();
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
        } elseif ($action === 'apply') {
            $this->postuler();
        } elseif ($action === 'applications') {
            $this->listeCandidatures();
        } elseif ($action === 'status') {
            $this->changerStatutCandidature();
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
        }

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $idCategorie = isset($_GET['id_categorie']) ? trim($_GET['id_categorie']) : '';
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';
        $statut = isset($_GET['statut']) ? trim($_GET['statut']) : '';
        $remoteOnly = isset($_GET['remote']) ? trim($_GET['remote']) : '';
        $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'date_desc';

        $jobs = $this->jobModel->listeJobs($search, $idCategorie, $type, $statut, $remoteOnly, $sort);
        $categories = $this->jobModel->listeCategories();
        $statistiques = $this->jobModel->statistiquesJobs();

        include __DIR__ . '/../view/jobs/list.php';
    }

    private function ajouter()
    {
        AuthC::requireAdmin();
        $office = 'back';
        $categories = $this->jobModel->listeCategories();
        $publishers = $this->jobModel->listePublishers();
        $errors = [];
        $formData = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = $_POST;
            $errors = $this->validerJob($_POST);

            if (empty($errors)) {
                $job = $this->construireJob($_POST);
                $this->jobModel->addJob($job);
                header('Location: JobC.php?office=back&action=list');
                exit;
            }
        }

        include __DIR__ . '/../view/jobs/form.php';
    }

    private function modifier()
    {
        AuthC::requireAdmin();
        $office = 'back';
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $job = $this->jobModel->getJobById($id);

        if (!$job) {
            die('Job introuvable.');
        }

        $categories = $this->jobModel->listeCategories();
        $publishers = $this->jobModel->listePublishers();
        $errors = [];
        $formData = $job;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = $_POST;
            $formData['id'] = $id;
            $errors = $this->validerJob($_POST);

            if (empty($errors)) {
                $jobObjet = $this->construireJob($_POST);
                $this->jobModel->updateJob($jobObjet, $id);
                header('Location: JobC.php?office=back&action=list');
                exit;
            }
        }

        include __DIR__ . '/../view/jobs/form.php';
    }

    private function supprimer()
    {
        AuthC::requireAdmin();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id > 0) {
            $this->jobModel->deleteJob($id);
        }

        header('Location: JobC.php?office=back&action=list');
        exit;
    }

    private function detail()
    {
        $office = $this->office();
        if ($office === 'back') {
            AuthC::requireAdmin();
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $job = $this->jobModel->getJobById($id);
        $candidatures = $office === 'back' ? $this->applicationModel->listeCandidatures($id) : [];
        $errors = [];
        $successMessage = '';

        if (!$job) {
            die('Job introuvable.');
        }

        include __DIR__ . '/../view/jobs/detail.php';
    }

    private function postuler()
    {
        $office = 'front';
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $job = $this->jobModel->getJobById($id);
        $candidatures = [];
        $errors = [];
        $successMessage = '';

        if (!$job) {
            die('Job introuvable.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validerCandidature($_POST);
            $cvUrl = $this->sauverFichier('cv_file', 'cv', ['pdf']);
            $photoUrl = $this->sauverFichier('photo_file', 'photo', ['jpg', 'jpeg', 'png', 'webp']);

            if (empty($errors)) {
                $utilisateur = $this->applicationModel->getUtilisateurByEmail(trim($_POST['email']));
                if ($utilisateur) {
                    $idUtilisateur = (int) $utilisateur['id'];
                } else {
                    $idUtilisateur = (int) $this->applicationModel->addFreelancer(trim($_POST['nom']), trim($_POST['email']));
                }

                if ($this->applicationModel->candidatureExiste($idUtilisateur, $id)) {
                    $errors[] = 'Vous avez deja postule a ce job.';
                } else {
                    $candidature = new candidature($idUtilisateur, $id, trim($_POST['message']), $cvUrl, $photoUrl, 'pending');
                    $this->applicationModel->addCandidature($candidature);
                    $job = $this->jobModel->getJobById($id);
                    $successMessage = 'Votre candidature a ete envoyee.';
                }
            }
        }

        include __DIR__ . '/../view/jobs/detail.php';
    }

    private function listeCandidatures()
    {
        AuthC::requireAdmin();
        $office = 'back';
        $candidatures = $this->applicationModel->listeCandidatures();
        include __DIR__ . '/../view/jobs/applications.php';
    }

    private function changerStatutCandidature()
    {
        AuthC::requireAdmin();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $statut = isset($_GET['statut']) ? trim($_GET['statut']) : '';
        $candidature = $this->applicationModel->getCandidatureById($id);

        if ($id > 0 && in_array($statut, ['pending', 'reviewed', 'accepted', 'rejected'])) {
            $this->applicationModel->updateStatut($id, $statut);
        }

        if ($candidature) {
            header('Location: JobC.php?office=back&action=detail&id=' . (int) $candidature['job_id']);
        } else {
            header('Location: JobC.php?office=back&action=applications');
        }
        exit;
    }

    private function construireJob($data)
    {
        $publisherId = isset($data['id_publisher']) && (int) $data['id_publisher'] > 0
            ? (int) $data['id_publisher']
            : $this->jobModel->getDefaultPublisherId();

        return new job(
            trim($data['titre']),
            trim($data['description']),
            (float) $data['budget'],
            (int) $data['id_categorie'],
            trim($data['localisation']),
            isset($data['remote']) ? 1 : 0,
            trim($data['type']),
            trim($data['statut']),
            $publisherId
        );
    }

    private function validerJob($data)
    {
        $errors = [];
        $types = ['Freelance', 'Full-time', 'Stage', 'Part-time'];
        $statuts = ['open', 'draft', 'closed'];

        if (!isset($data['titre']) || strlen(trim($data['titre'])) < 4) {
            $errors[] = 'Le titre doit contenir au moins 4 caracteres.';
        }

        if (!isset($data['description']) || strlen(trim($data['description'])) < 20) {
            $errors[] = 'La description doit contenir au moins 20 caracteres.';
        }

        if (!isset($data['budget']) || !is_numeric($data['budget']) || (float) $data['budget'] < 0) {
            $errors[] = 'Le budget doit etre un nombre positif ou egal a zero.';
        }

        if (!isset($data['id_categorie']) || (int) $data['id_categorie'] <= 0) {
            $errors[] = 'Veuillez choisir une categorie.';
        }

        if (!isset($data['localisation']) || strlen(trim($data['localisation'])) < 2) {
            $errors[] = 'La localisation doit contenir au moins 2 caracteres.';
        }

        if (!isset($data['type']) || !in_array($data['type'], $types)) {
            $errors[] = 'Veuillez choisir un type valide.';
        }

        if (!isset($data['statut']) || !in_array($data['statut'], $statuts)) {
            $errors[] = 'Veuillez choisir un statut valide.';
        }

        return $errors;
    }

    private function validerCandidature($data)
    {
        $errors = [];

        if (!isset($data['nom']) || strlen(trim($data['nom'])) < 3) {
            $errors[] = 'Le nom doit contenir au moins 3 caracteres.';
        }

        if (!isset($data['email']) || !filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Veuillez saisir un email valide.';
        }

        if (!isset($data['message']) || strlen(trim($data['message'])) < 20) {
            $errors[] = 'Le message doit contenir au moins 20 caracteres.';
        }

        return $errors;
    }

    private function sauverFichier($fieldName, $prefix, $allowedExtensions)
    {
        if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $extension = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            return null;
        }

        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES[$fieldName]['name']));
        $fileName = time() . '_' . $prefix . '_' . $safeName;

        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadDir . $fileName)) {
            return 'uploads/' . $fileName;
        }

        return null;
    }
}

$jobController = new JobC();
$jobController->handleRequest();
?>

<?php
require_once __DIR__ . '/FormationModel.php';
require_once __DIR__ . '/JobModel.php';
require_once __DIR__ . '/UtilisateurModel.php';
require_once __DIR__ . '/AuthC.php';

class BackDashboardC
{
    public function index()
    {
        AuthC::requireAdmin();

        $formationModel = new FormationModel();
        $jobModel = new JobModel();
        $utilisateurModel = new UtilisateurModel();

        $stats = [
            'formations' => $formationModel->countFormations(),
            'jobs' => $jobModel->countJobs(),
            'users' => $utilisateurModel->countUtilisateurs()
        ];

        include __DIR__ . '/../view/back/dashboard.php';
    }
}

$controller = new BackDashboardC();
$controller->index();
?>

<?php
require_once __DIR__ . '/FormationModel.php';
require_once __DIR__ . '/JobModel.php';

class BackDashboardC
{
    public function index()
    {
        $formationModel = new FormationModel();
        $jobModel = new JobModel();

        $stats = [
            'formations' => $formationModel->countFormations(),
            'jobs' => $jobModel->countJobs()
        ];

        include __DIR__ . '/../view/back/dashboard.php';
    }
}

$controller = new BackDashboardC();
$controller->index();
?>

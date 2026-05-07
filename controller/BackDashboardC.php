<?php
require_once __DIR__ . '/FormationModel.php';

class BackDashboardC
{
    public function index()
    {
        $formationModel = new FormationModel();

        $stats = [
            'formations' => $formationModel->countFormations()
        ];

        include __DIR__ . '/../view/back/dashboard.php';
    }
}

$controller = new BackDashboardC();
$controller->index();
?>

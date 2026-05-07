<?php
require_once __DIR__ . '/FormationModel.php';

class HomeC
{
    public function index()
    {
        $formationModel = new FormationModel();

        $stats = [
            'formations' => $formationModel->countFormations()
        ];

        include __DIR__ . '/../view/front/home.php';
    }
}

$controller = new HomeC();
$controller->index();
?>

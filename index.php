<?php
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

require_once 'controllers/FormationController.php';

$controller = new FormationController();

switch($action) {
    case 'index':
        $controller->index();
        break;
    case 'create':
        $controller->create();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'delete':
        $controller->delete();
        break;
    default:
        $controller->index();
        break;
}
?>

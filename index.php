<?php
session_start();

$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'formation';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch($controllerName) {
    case 'utilisateur':
        require_once 'controllers/UtilisateurController.php';
        $controller = new UtilisateurController();
        break;
    case 'auth':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        break;
    case 'formation':
    default:
        require_once 'controllers/FormationController.php';
        $controller = new FormationController();
        break;
}

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    $controller->index();
}
?>

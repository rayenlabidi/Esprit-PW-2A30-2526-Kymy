<?php
session_start();

require_once __DIR__ . '/config.php';

require_once __DIR__ . '/CONTROLLER/BaseController.php';
require_once __DIR__ . '/CONTROLLER/AuthController.php';
require_once __DIR__ . '/CONTROLLER/FormationController.php';

$module = $_GET['module'] ?? 'formations';
$action = $_GET['action'] ?? 'index';

$controllerMap = [
    'auth' => AuthController::class,
    'formations' => FormationController::class,
];

if (!isset($controllerMap[$module])) {
    $module = 'formations';
}

$controllerClass = $controllerMap[$module];
$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    $action = 'index';
}

$controller->$action();

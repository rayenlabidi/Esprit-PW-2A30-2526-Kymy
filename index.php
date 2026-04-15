<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

require_once __DIR__ . '/controllers/BaseController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/FormationController.php';
require_once __DIR__ . '/controllers/JobController.php';

$module = $_GET['module'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

$controllerMap = [
    'dashboard' => DashboardController::class,
    'auth' => AuthController::class,
    'users' => UserController::class,
    'formations' => FormationController::class,
    'jobs' => JobController::class,
];

if (!isset($controllerMap[$module])) {
    $module = 'dashboard';
}

$controllerClass = $controllerMap[$module];
$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    $action = 'index';
}

$controller->$action();

<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

require_once __DIR__ . '/controllers/BaseController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/JobController.php';

$module = $_GET['module'] ?? 'jobs';
$action = $_GET['action'] ?? 'index';

$controllerMap = [
    'auth' => AuthController::class,
    'jobs' => JobController::class,
];

if (!isset($controllerMap[$module])) {
    $module = 'jobs';
}

$controllerClass = $controllerMap[$module];
$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    $action = 'index';
}

$controller->$action();

<?php
// admin/index.php — Admin Front Controller v2

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/Model/Event.php';
require_once BASE_PATH . '/Model/EventCategory.php';
require_once BASE_PATH . '/Controller/AdminEventController.php';
require_once BASE_PATH . '/Controller/AdminCategoryController.php';
require_once BASE_PATH . '/Controller/AdminDashboardController.php';

session_start();

$module = $_GET['module'] ?? 'events';

switch ($module) {
    case 'dashboard':
        (new AdminDashboardController())->handleRequest();
        break;
    case 'categories':
        (new AdminCategoryController())->handleRequest();
        break;
    default:
        (new AdminEventController())->handleRequest();
        break;
}

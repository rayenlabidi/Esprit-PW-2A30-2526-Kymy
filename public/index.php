<?php
// public/index.php — User Front Controller v2

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/Model/Event.php';
require_once BASE_PATH . '/Model/EventCategory.php';
require_once BASE_PATH . '/Controller/UserEventController.php';

session_start();

(new UserEventController())->handleRequest();

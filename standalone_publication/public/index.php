<?php
$request = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

switch(true) {
    case strpos($request, '/admin') !== false:
        if(strpos($request, 'edit') !== false) {
            include __DIR__ . '/../views/back/admin.php';
        } else {
            include __DIR__ . '/../views/back/admin.php';
        }
        break;
    default:
        include __DIR__ . '/../views/front/publications.php';
        break;
}
?>
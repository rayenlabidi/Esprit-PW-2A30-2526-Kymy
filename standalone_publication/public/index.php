<?php
$request = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

switch(true) {
    case strpos($request, '/admin') !== false:
        if(strpos($request, 'edit') !== false) {
            include __DIR__ . '/../view/back/editPublication.php';
        } else {
            include __DIR__ . '/../view/back/adminPublications.php';
        }
        break;
    default:
        include __DIR__ . '/../view/front/publications.php';
        break;
}
?>
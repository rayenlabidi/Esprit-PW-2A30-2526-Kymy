<?php
/*
 * Routeur des actions utilisateur.
 * Il recoit action=add/update/delete/etc. et appelle le controleur correspondant.
 */
include_once __DIR__ . '/UtilisateurController.php';

$controller = new UtilisateurController();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $controller->add();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'upload-avatar':
        $controller->uploadAvatar();
        break;
    case 'export-pdf':
        $controller->exportPdf();
        break;
    default:
        header('Location: ../view/listeUtilisateurs.php');
        exit();
}

?>

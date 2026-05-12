<?php
// Endpoint de deconnexion: appelle le controleur d'authentification.
include_once '../controller/AuthController.php';
(new AuthController())->logout();
?>

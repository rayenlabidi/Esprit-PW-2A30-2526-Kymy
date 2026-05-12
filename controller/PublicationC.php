<?php
/**
 * PublicationC.php - Routes to standalone_publication module
 * The full CRUD and AJAX implementation lives in standalone_publication/controllers/PublicationC.php
 */

$office = (isset($_GET['office']) && $_GET['office'] === 'back') ? 'back' : 'front';

if ($office === 'back') {
    // Admin panel - redirect to standalone_publication admin
    header('Location: ../standalone_publication/views/back/admin.php');
    exit;
} else {
    // Front office - redirect to standalone_publication publications page
    header('Location: ../standalone_publication/views/front/publications.php');
    exit;
}
?>

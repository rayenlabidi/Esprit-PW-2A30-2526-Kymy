<?php
require_once __DIR__ . '/AuthC.php';

class PublicationC
{
    public function handleRequest()
    {
        $office = (isset($_GET['office']) && $_GET['office'] === 'back') ? 'back' : 'front';

        if ($office === 'back') {
            AuthC::requireAdmin();
        }

        include __DIR__ . '/../view/publications/list.php';
    }
}

$controller = new PublicationC();
$controller->handleRequest();
?>

<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/formateur.php';

class FormateurModel
{
    public function listeFormateurs()
    {
        $db = config::getConnexion();
        try {
            $query = $db->query('SELECT * FROM formateur ORDER BY nom ASC');
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>

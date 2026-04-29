<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/categorieFormation.php';

class CategorieFormationModel
{
    public function listeCategories()
    {
        $db = config::getConnexion();
        try {
            $query = $db->query('SELECT * FROM categorie_formation ORDER BY nom_categorie ASC');
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getCategorieById($idCategorie)
    {
        $sql = 'SELECT * FROM categorie_formation WHERE id_categorie = :id_categorie';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_categorie' => $idCategorie
            ]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>

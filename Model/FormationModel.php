<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/formation.php';

class FormationModel
{
    public function listeFormations($search = '', $idCategorie = '', $statut = '', $niveau = '')
    {
        $sql = 'SELECT f.*, c.nom_categorie, fo.nom AS nom_formateur, fo.specialite,
                       (SELECT COUNT(*) FROM inscription_formation i WHERE i.id_formation = f.id_formation) AS total_inscrits
                FROM formation f
                INNER JOIN categorie_formation c ON f.id_categorie = c.id_categorie
                INNER JOIN formateur fo ON f.id_formateur = fo.id_formateur
                WHERE 1 = 1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (f.titre LIKE :search OR f.description LIKE :search OR f.niveau LIKE :search OR fo.nom LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        if ($idCategorie !== '') {
            $sql .= ' AND f.id_categorie = :id_categorie';
            $params['id_categorie'] = $idCategorie;
        }

        if ($statut !== '') {
            $sql .= ' AND f.statut = :statut';
            $params['statut'] = $statut;
        }

        if ($niveau !== '') {
            $sql .= ' AND f.niveau = :niveau';
            $params['niveau'] = $niveau;
        }

        $sql .= ' ORDER BY f.date_debut DESC, f.id_formation DESC';

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getFormationById($id)
    {
        $sql = 'SELECT f.*, c.nom_categorie, fo.nom AS nom_formateur, fo.email AS email_formateur, fo.specialite,
                       (SELECT COUNT(*) FROM inscription_formation i WHERE i.id_formation = f.id_formation) AS total_inscrits
                FROM formation f
                INNER JOIN categorie_formation c ON f.id_categorie = c.id_categorie
                INNER JOIN formateur fo ON f.id_formateur = fo.id_formateur
                WHERE f.id_formation = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function addFormation($formation)
    {
        $sql = 'INSERT INTO formation
                (titre, description, date_debut, date_fin, duree, prix, niveau, statut, mode, places, id_categorie, id_formateur)
                VALUES
                (:titre, :description, :date_debut, :date_fin, :duree, :prix, :niveau, :statut, :mode, :places, :id_categorie, :id_formateur)';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'titre' => $formation->getTitre(),
                'description' => $formation->getDescription(),
                'date_debut' => $formation->getDateDebut(),
                'date_fin' => $formation->getDateFin(),
                'duree' => $formation->getDuree(),
                'prix' => $formation->getPrix(),
                'niveau' => $formation->getNiveau(),
                'statut' => $formation->getStatut(),
                'mode' => $formation->getMode(),
                'places' => $formation->getPlaces(),
                'id_categorie' => $formation->getIdCategorie(),
                'id_formateur' => $formation->getIdFormateur()
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function updateFormation($formation, $id)
    {
        $sql = 'UPDATE formation SET
                    titre = :titre,
                    description = :description,
                    date_debut = :date_debut,
                    date_fin = :date_fin,
                    duree = :duree,
                    prix = :prix,
                    niveau = :niveau,
                    statut = :statut,
                    mode = :mode,
                    places = :places,
                    id_categorie = :id_categorie,
                    id_formateur = :id_formateur
                WHERE id_formation = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'titre' => $formation->getTitre(),
                'description' => $formation->getDescription(),
                'date_debut' => $formation->getDateDebut(),
                'date_fin' => $formation->getDateFin(),
                'duree' => $formation->getDuree(),
                'prix' => $formation->getPrix(),
                'niveau' => $formation->getNiveau(),
                'statut' => $formation->getStatut(),
                'mode' => $formation->getMode(),
                'places' => $formation->getPlaces(),
                'id_categorie' => $formation->getIdCategorie(),
                'id_formateur' => $formation->getIdFormateur(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteFormation($id)
    {
        $sql = 'DELETE FROM formation WHERE id_formation = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function statistiquesFormations()
    {
        $sql = "SELECT
                    COUNT(*) AS total,
                    IFNULL(SUM(CASE WHEN statut = 'planifiee' THEN 1 ELSE 0 END), 0) AS planifiees,
                    IFNULL(SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END), 0) AS en_cours,
                    IFNULL(SUM(CASE WHEN statut = 'terminee' THEN 1 ELSE 0 END), 0) AS terminees,
                    (SELECT COUNT(*) FROM inscription_formation) AS inscriptions
                FROM formation";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $stats = $query->fetch();
            if (!$stats) {
                return [
                    'total' => 0,
                    'planifiees' => 0,
                    'en_cours' => 0,
                    'terminees' => 0,
                    'inscriptions' => 0
                ];
            }
            return $stats;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function countFormations()
    {
        $db = config::getConnexion();
        $query = $db->query('SELECT COUNT(*) AS total FROM formation');
        $row = $query->fetch();
        return $row ? (int) $row['total'] : 0;
    }
}
?>

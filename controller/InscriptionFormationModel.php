<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/inscriptionFormation.php';

class InscriptionFormationModel
{
    public function addInscription($inscription)
    {
        $sql = 'INSERT INTO inscription_formation (user_id, id_formation, statut)
                VALUES (:user_id, :id_formation, :statut)';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'user_id' => $inscription->getUserId(),
                'id_formation' => $inscription->getIdFormation(),
                'statut' => $inscription->getStatut()
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function inscriptionExiste($userId, $idFormation)
    {
        $sql = 'SELECT COUNT(*) AS total
                FROM inscription_formation
                WHERE user_id = :user_id AND id_formation = :id_formation';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'user_id' => $userId,
                'id_formation' => $idFormation
            ]);
            $row = $query->fetch();
            return $row && (int) $row['total'] > 0;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function listeInscriptions()
    {
        $sql = 'SELECT i.*,
                       CONCAT(u.first_name, " ", u.last_name) AS nom_apprenant,
                       u.email,
                       u.phone AS telephone,
                       f.titre AS titre_formation, fo.nom AS nom_formateur
                FROM inscription_formation i
                INNER JOIN utilisateurs u ON i.user_id = u.id
                INNER JOIN formation f ON i.id_formation = f.id_formation
                INNER JOIN formateur fo ON f.id_formateur = fo.id_formateur
                ORDER BY i.date_inscription DESC';
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>

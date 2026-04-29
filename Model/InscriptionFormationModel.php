<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/inscriptionFormation.php';

class InscriptionFormationModel
{
    public function addInscription($inscription)
    {
        $sql = 'INSERT INTO inscription_formation (id_apprenant, id_formation, statut)
                VALUES (:id_apprenant, :id_formation, :statut)';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_apprenant' => $inscription->getIdApprenant(),
                'id_formation' => $inscription->getIdFormation(),
                'statut' => $inscription->getStatut()
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function inscriptionExiste($idApprenant, $idFormation)
    {
        $sql = 'SELECT COUNT(*) AS total
                FROM inscription_formation
                WHERE id_apprenant = :id_apprenant AND id_formation = :id_formation';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_apprenant' => $idApprenant,
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
        $sql = 'SELECT i.*, a.nom AS nom_apprenant, a.email, a.telephone,
                       f.titre AS titre_formation, fo.nom AS nom_formateur
                FROM inscription_formation i
                INNER JOIN apprenant a ON i.id_apprenant = a.id_apprenant
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

<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/apprenant.php';

class ApprenantModel
{
    public function getApprenantByEmail($email)
    {
        $sql = 'SELECT * FROM apprenant WHERE email = :email';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['email' => $email]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function addApprenant($apprenant)
    {
        $sql = 'INSERT INTO apprenant (nom, email, telephone)
                VALUES (:nom, :email, :telephone)';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $apprenant->getNom(),
                'email' => $apprenant->getEmail(),
                'telephone' => $apprenant->getTelephone()
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>

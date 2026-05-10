<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/candidature.php';

class ApplicationModel
{
    public function addCandidature($candidature)
    {
        $sql = 'INSERT INTO candidatures (user_id, job_id, cover_letter, cv_url, photo_url, status)
                VALUES (:user_id, :job_id, :cover_letter, :cv_url, :photo_url, :status)';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'user_id' => $candidature->getIdUtilisateur(),
                'job_id' => $candidature->getIdJob(),
                'cover_letter' => $candidature->getMessage(),
                'cv_url' => $candidature->getCvUrl(),
                'photo_url' => $candidature->getPhotoUrl(),
                'status' => $candidature->getStatut()
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function candidatureExiste($idUtilisateur, $idJob)
    {
        $sql = 'SELECT COUNT(*) AS total
                FROM candidatures
                WHERE user_id = :user_id AND job_id = :job_id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'user_id' => $idUtilisateur,
                'job_id' => $idJob
            ]);
            $row = $query->fetch();
            return $row && (int) $row['total'] > 0;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getUtilisateurByEmail($email)
    {
        $sql = 'SELECT * FROM utilisateurs WHERE email = :email';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['email' => $email]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function addFreelancer($nomComplet, $email)
    {
        $roleId = $this->getRoleId('freelancer');
        $parts = preg_split('/\s+/', trim($nomComplet), 2);
        $firstName = isset($parts[0]) ? $parts[0] : 'Freelancer';
        $lastName = isset($parts[1]) ? $parts[1] : 'Workify';

        $sql = 'INSERT INTO utilisateurs (role_id, first_name, last_name, email, password, headline, bio, status)
                VALUES (:role_id, :first_name, :last_name, :email, :password, :headline, :bio, "active")';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'role_id' => $roleId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => password_hash('workify123', PASSWORD_DEFAULT),
                'headline' => 'Candidat Workify',
                'bio' => 'Profil cree automatiquement depuis le formulaire de candidature.'
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function listeCandidatures($idJob = '')
    {
        $sql = 'SELECT ca.*, j.title AS titre_job, u.first_name, u.last_name, u.email
                FROM candidatures ca
                INNER JOIN jobs j ON ca.job_id = j.id
                INNER JOIN utilisateurs u ON ca.user_id = u.id
                WHERE 1 = 1';
        $params = [];

        if ($idJob !== '') {
            $sql .= ' AND ca.job_id = :job_id';
            $params['job_id'] = $idJob;
        }

        $sql .= ' ORDER BY ca.applied_at DESC';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function updateStatut($id, $statut)
    {
        $sql = 'UPDATE candidatures SET status = :status WHERE id = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'status' => $statut,
                'id' => $id
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getCandidatureById($id)
    {
        $sql = 'SELECT * FROM candidatures WHERE id = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    private function getRoleId($slug)
    {
        $sql = 'SELECT id FROM roles WHERE slug = :slug LIMIT 1';
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['slug' => $slug]);
        $row = $query->fetch();

        if ($row) {
            return (int) $row['id'];
        }

        return 2;
    }
}
?>

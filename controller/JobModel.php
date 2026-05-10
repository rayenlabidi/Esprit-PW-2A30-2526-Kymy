<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/job.php';

class JobModel
{
    public function listeJobs($search = '', $idCategorie = '', $type = '', $statut = '', $remoteOnly = '', $sort = 'date_desc')
    {
        $sql = 'SELECT j.*, c.name AS nom_categorie,
                       CONCAT(u.first_name, " ", u.last_name) AS nom_publisher,
                       (SELECT COUNT(*) FROM candidatures ca WHERE ca.job_id = j.id) AS total_candidatures
                FROM jobs j
                INNER JOIN categories c ON j.category_id = c.id
                INNER JOIN utilisateurs u ON j.publisher_id = u.id
                WHERE 1 = 1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (j.title LIKE :search OR j.description LIKE :search OR j.location LIKE :search OR c.name LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        if ($idCategorie !== '') {
            $sql .= ' AND j.category_id = :category_id';
            $params['category_id'] = $idCategorie;
        }

        if ($type !== '') {
            $sql .= ' AND j.job_type = :job_type';
            $params['job_type'] = $type;
        }

        if ($statut !== '') {
            $sql .= ' AND j.status = :status';
            $params['status'] = $statut;
        }

        if ($remoteOnly === '1') {
            $sql .= ' AND j.is_remote = 1';
        }

        if ($sort === 'budget_asc') {
            $sql .= ' ORDER BY j.budget ASC';
        } elseif ($sort === 'budget_desc') {
            $sql .= ' ORDER BY j.budget DESC';
        } elseif ($sort === 'date_asc') {
            $sql .= ' ORDER BY j.created_at ASC';
        } else {
            $sql .= ' ORDER BY j.created_at DESC, j.id DESC';
        }

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getJobById($id)
    {
        $sql = 'SELECT j.*, c.name AS nom_categorie,
                       CONCAT(u.first_name, " ", u.last_name) AS nom_publisher,
                       u.email AS email_publisher,
                       (SELECT COUNT(*) FROM candidatures ca WHERE ca.job_id = j.id) AS total_candidatures
                FROM jobs j
                INNER JOIN categories c ON j.category_id = c.id
                INNER JOIN utilisateurs u ON j.publisher_id = u.id
                WHERE j.id = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function addJob($job)
    {
        $sql = 'INSERT INTO jobs
                (title, description, budget, category_id, location, is_remote, job_type, status, publisher_id)
                VALUES
                (:title, :description, :budget, :category_id, :location, :is_remote, :job_type, :status, :publisher_id)';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'title' => $job->getTitre(),
                'description' => $job->getDescription(),
                'budget' => $job->getBudget(),
                'category_id' => $job->getIdCategorie(),
                'location' => $job->getLocalisation(),
                'is_remote' => $job->getRemote(),
                'job_type' => $job->getType(),
                'status' => $job->getStatut(),
                'publisher_id' => $job->getIdPublisher()
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function updateJob($job, $id)
    {
        $sql = 'UPDATE jobs SET
                    title = :title,
                    description = :description,
                    budget = :budget,
                    category_id = :category_id,
                    location = :location,
                    is_remote = :is_remote,
                    job_type = :job_type,
                    status = :status,
                    publisher_id = :publisher_id
                WHERE id = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'title' => $job->getTitre(),
                'description' => $job->getDescription(),
                'budget' => $job->getBudget(),
                'category_id' => $job->getIdCategorie(),
                'location' => $job->getLocalisation(),
                'is_remote' => $job->getRemote(),
                'job_type' => $job->getType(),
                'status' => $job->getStatut(),
                'publisher_id' => $job->getIdPublisher(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteJob($id)
    {
        $sql = 'DELETE FROM jobs WHERE id = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function listeCategories()
    {
        $db = config::getConnexion();
        try {
            $query = $db->query("SELECT * FROM categories WHERE scope IN ('all', 'job') ORDER BY name ASC");
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function listePublishers()
    {
        $sql = "SELECT u.*
                FROM utilisateurs u
                INNER JOIN roles r ON u.role_id = r.id
                WHERE r.slug IN ('admin', 'boss')
                ORDER BY u.first_name ASC, u.last_name ASC";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getDefaultPublisherId()
    {
        $sql = "SELECT u.id
                FROM utilisateurs u
                INNER JOIN roles r ON u.role_id = r.id
                WHERE r.slug IN ('boss', 'admin')
                ORDER BY FIELD(r.slug, 'boss', 'admin'), u.id
                LIMIT 1";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $row = $query->fetch();
            return $row ? (int) $row['id'] : 0;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function statistiquesJobs()
    {
        $sql = "SELECT
                    COUNT(*) AS total,
                    IFNULL(SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END), 0) AS ouverts,
                    IFNULL(SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END), 0) AS brouillons,
                    IFNULL(AVG(budget), 0) AS budget_moyen,
                    (SELECT COUNT(*) FROM candidatures) AS candidatures
                FROM jobs";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $stats = $query->fetch();
            if (!$stats) {
                return [
                    'total' => 0,
                    'ouverts' => 0,
                    'brouillons' => 0,
                    'budget_moyen' => 0,
                    'candidatures' => 0
                ];
            }
            return $stats;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function countJobs()
    {
        $db = config::getConnexion();
        $query = $db->query('SELECT COUNT(*) AS total FROM jobs');
        $row = $query->fetch();
        return $row ? (int) $row['total'] : 0;
    }
}
?>

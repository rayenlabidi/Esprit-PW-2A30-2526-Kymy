<?php

class Job
{
    public function __construct(private PDO $db)
    {
    }

    public function all(array $filters = []): array
    {
        $sql = 'SELECT j.*, c.name AS category_name,
                       u.first_name, u.last_name,
                       COUNT(a.id) AS application_count
                FROM jobs j
                LEFT JOIN categories c ON c.id = j.category_id
                LEFT JOIN utilisateurs u ON u.id = j.publisher_id
                LEFT JOIN candidatures a ON a.job_id = j.id
                WHERE 1=1';
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= ' AND (j.title LIKE :search OR j.description LIKE :search OR j.location LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['category_id'])) {
            $sql .= ' AND j.category_id = :category_id';
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['job_type'])) {
            $sql .= ' AND j.job_type = :job_type';
            $params['job_type'] = $filters['job_type'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND j.status = :status';
            $params['status'] = $filters['status'];
        }

        if (($filters['remote_only'] ?? '') === '1') {
            $sql .= ' AND j.is_remote = 1';
        }

        $sql .= ' GROUP BY j.id, c.name, u.first_name, u.last_name ';

        $sort = $filters['sort'] ?? 'date_desc';
        if ($sort === 'budget_asc') {
            $sql .= ' ORDER BY j.budget ASC';
        } elseif ($sort === 'budget_desc') {
            $sql .= ' ORDER BY j.budget DESC';
        } elseif ($sort === 'date_asc') {
            $sql .= ' ORDER BY j.created_at ASC';
        } else {
            $sql .= ' ORDER BY j.created_at DESC';
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function featured(int $limit = 3): array
    {
        $statement = $this->db->prepare(
            'SELECT j.id, j.title, j.budget, j.job_type, j.location, j.is_remote
             FROM jobs j
             WHERE j.status = "open"
             ORDER BY j.created_at DESC
             LIMIT :limit'
        );
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sql = 'SELECT j.*, c.name AS category_name,
                       u.first_name, u.last_name, u.email AS publisher_email,
                       COUNT(a.id) AS application_count
                FROM jobs j
                LEFT JOIN categories c ON c.id = j.category_id
                LEFT JOIN utilisateurs u ON u.id = j.publisher_id
                LEFT JOIN candidatures a ON a.job_id = j.id
                WHERE j.id = :id
                GROUP BY j.id, c.name, u.first_name, u.last_name, u.email
                LIMIT 1';
        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO jobs
                (title, description, budget, category_id, location, is_remote, job_type, status, publisher_id)
                VALUES
                (:title, :description, :budget, :category_id, :location, :is_remote, :job_type, :status, :publisher_id)';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'budget' => $data['budget'],
            'category_id' => $data['category_id'],
            'location' => trim($data['location']),
            'is_remote' => !empty($data['is_remote']) ? 1 : 0,
            'job_type' => $data['job_type'],
            'status' => $data['status'],
            'publisher_id' => $data['publisher_id'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE jobs
                SET title = :title,
                    description = :description,
                    budget = :budget,
                    category_id = :category_id,
                    location = :location,
                    is_remote = :is_remote,
                    job_type = :job_type,
                    status = :status,
                    publisher_id = :publisher_id
                WHERE id = :id';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'id' => $id,
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'budget' => $data['budget'],
            'category_id' => $data['category_id'],
            'location' => trim($data['location']),
            'is_remote' => !empty($data['is_remote']) ? 1 : 0,
            'job_type' => $data['job_type'],
            'status' => $data['status'],
            'publisher_id' => $data['publisher_id'],
        ]);
    }

    public function delete(int $id): void
    {
        $statement = $this->db->prepare('DELETE FROM jobs WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function stats(): array
    {
        return [
            'total' => (int) $this->db->query('SELECT COUNT(*) FROM jobs')->fetchColumn(),
            'open' => (int) $this->db->query('SELECT COUNT(*) FROM jobs WHERE status = "open"')->fetchColumn(),
            'applications' => (int) $this->db->query('SELECT COUNT(*) FROM candidatures')->fetchColumn(),
            'average_budget' => (float) $this->db->query('SELECT COALESCE(AVG(budget), 0) FROM jobs')->fetchColumn(),
        ];
    }

    public function getScoredRecommendations(int $jobId, int $categoryId, string $jobTitle): array
    {
        $sqlFreelancers = "SELECT u.id, u.first_name, u.last_name, u.headline, u.bio, u.avatar_url 
                           FROM utilisateurs u
                           INNER JOIN roles r ON r.id = u.role_id
                           WHERE r.slug = 'freelancer'
                             AND u.id NOT IN (SELECT user_id FROM candidatures WHERE job_id = :job_id)";
        $stmt = $this->db->prepare($sqlFreelancers);
        $stmt->execute(['job_id' => $jobId]);
        $freelancers = $stmt->fetchAll();

        $recommendations = [];
        $stopWords = ['de', 'pour', 'le', 'la', 'les', 'un', 'une', 'des', 'et', 'en', 'a', 'au', 'aux', 'dans', 'sur'];
        $words = explode(' ', strtolower(trim($jobTitle)));
        $keywords = array_filter($words, fn($w) => strlen($w) > 2 && !in_array($w, $stopWords));

        foreach ($freelancers as $f) {
            $score = 0;

            $sqlFormations = "SELECT MAX(i.progress) as max_progress, f.title
                              FROM inscriptions i
                              INNER JOIN formations f ON f.id = i.formation_id
                              WHERE i.user_id = :user_id AND f.category_id = :category_id
                              GROUP BY f.title LIMIT 1";
            $stmtForm = $this->db->prepare($sqlFormations);
            $stmtForm->execute(['user_id' => $f['id'], 'category_id' => $categoryId]);
            $formationData = $stmtForm->fetch();
            
            $formationTitle = '';
            $progress = 0;
            if ($formationData) {
                $progress = (int) $formationData['max_progress'];
                $formationTitle = $formationData['title'];
                if ($progress > 0) {
                    $score += min(40, ($progress / 100) * 40);
                }
            }

            $sqlExp = "SELECT COUNT(*) 
                       FROM candidatures c
                       INNER JOIN jobs j ON j.id = c.job_id
                       WHERE c.user_id = :user_id 
                         AND c.status = 'accepted' 
                         AND j.category_id = :category_id";
            $stmtExp = $this->db->prepare($sqlExp);
            $stmtExp->execute(['user_id' => $f['id'], 'category_id' => $categoryId]);
            $acceptedCount = (int) $stmtExp->fetchColumn();
            $score += min(30, $acceptedCount * 15);

            $textToSearch = strtolower($f['headline'] . ' ' . $f['bio']);
            $semanticScore = 0;
            foreach ($keywords as $kw) {
                if (strpos($textToSearch, $kw) !== false) {
                    $semanticScore += 10;
                }
            }
            $score += min(30, $semanticScore);

            if ($score > 0) {
                $f['match_score'] = round($score);
                $f['formation_title'] = $formationTitle;
                $f['progress'] = $progress;
                $recommendations[] = $f;
            }
        }

        usort($recommendations, fn($a, $b) => $b['match_score'] <=> $a['match_score']);

        return array_slice($recommendations, 0, 5);
    }
}

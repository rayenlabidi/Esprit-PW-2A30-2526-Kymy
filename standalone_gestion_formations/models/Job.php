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

        $sql .= ' GROUP BY j.id, c.name, u.first_name, u.last_name
                  ORDER BY j.created_at DESC';

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
}

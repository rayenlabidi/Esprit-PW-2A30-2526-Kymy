<?php

class Application
{
    public function __construct(private PDO $db)
    {
    }

    public function apply(int $userId, int $jobId, string $coverLetter, ?string $cvUrl = null, ?string $photoUrl = null): void
    {
        $sql = 'INSERT IGNORE INTO candidatures (user_id, job_id, cover_letter, cv_url, photo_url, status)
                VALUES (:user_id, :job_id, :cover_letter, :cv_url, :photo_url, "pending")';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'user_id' => $userId,
            'job_id' => $jobId,
            'cover_letter' => trim($coverLetter),
            'cv_url' => $cvUrl,
            'photo_url' => $photoUrl,
        ]);
    }

    public function forJob(int $jobId): array
    {
        $sql = 'SELECT a.*, u.first_name, u.last_name, u.email, u.avatar_url 
                FROM candidatures a
                INNER JOIN utilisateurs u ON u.id = a.user_id
                WHERE a.job_id = :job_id
                ORDER BY a.applied_at DESC';
        $statement = $this->db->prepare($sql);
        $statement->execute(['job_id' => $jobId]);
        return $statement->fetchAll();
    }

    public function hasApplied(int $userId, int $jobId): bool
    {
        $statement = $this->db->prepare('SELECT id FROM candidatures WHERE user_id = :user_id AND job_id = :job_id LIMIT 1');
        $statement->execute([
            'user_id' => $userId,
            'job_id' => $jobId,
        ]);
        return (bool) $statement->fetch();
    }

    public function forUser(int $userId, array $filters = []): array
    {
        $sql = 'SELECT a.applied_at, a.status, j.title, j.job_type
                FROM candidatures a
                INNER JOIN jobs j ON j.id = a.job_id
                WHERE a.user_id = :user_id';
        
        $params = ['user_id' => $userId];

        if (!empty($filters['search'])) {
            $sql .= ' AND j.title LIKE :search';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sort = $filters['sort'] ?? 'date_desc';
        if ($sort === 'date_asc') {
            $sql .= ' ORDER BY a.applied_at ASC';
        } else {
            $sql .= ' ORDER BY a.applied_at DESC';
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM candidatures WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function updateStatus(int $id, string $status): void
    {
        $statement = $this->db->prepare('UPDATE candidatures SET status = :status WHERE id = :id');
        $statement->execute(['status' => $status, 'id' => $id]);
    }
}

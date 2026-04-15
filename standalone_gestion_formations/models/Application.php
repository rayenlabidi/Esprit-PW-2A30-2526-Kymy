<?php

class Application
{
    public function __construct(private PDO $db)
    {
    }

    public function apply(int $userId, int $jobId, string $coverLetter): void
    {
        $sql = 'INSERT IGNORE INTO candidatures (user_id, job_id, cover_letter, status)
                VALUES (:user_id, :job_id, :cover_letter, "pending")';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'user_id' => $userId,
            'job_id' => $jobId,
            'cover_letter' => trim($coverLetter),
        ]);
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

    public function forUser(int $userId): array
    {
        $sql = 'SELECT a.applied_at, a.status, j.title, j.job_type
                FROM candidatures a
                INNER JOIN jobs j ON j.id = a.job_id
                WHERE a.user_id = :user_id
                ORDER BY a.applied_at DESC';
        $statement = $this->db->prepare($sql);
        $statement->execute(['user_id' => $userId]);
        return $statement->fetchAll();
    }
}

<?php

class Enrollment
{
    public function __construct(private PDO $db)
    {
    }

    public function enroll(int $userId, int $formationId): void
    {
        $sql = 'INSERT IGNORE INTO inscriptions (user_id, formation_id) VALUES (:user_id, :formation_id)';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'user_id' => $userId,
            'formation_id' => $formationId,
        ]);
    }

    public function isEnrolled(int $userId, int $formationId): bool
    {
        $statement = $this->db->prepare('SELECT id FROM inscriptions WHERE user_id = :user_id AND formation_id = :formation_id LIMIT 1');
        $statement->execute([
            'user_id' => $userId,
            'formation_id' => $formationId,
        ]);
        return (bool) $statement->fetch();
    }

    public function forUser(int $userId): array
    {
        $sql = 'SELECT i.enrolled_at, i.progress, f.title, f.level
                FROM inscriptions i
                INNER JOIN formations f ON f.id = i.formation_id
                WHERE i.user_id = :user_id
                ORDER BY i.enrolled_at DESC';
        $statement = $this->db->prepare($sql);
        $statement->execute(['user_id' => $userId]);
        return $statement->fetchAll();
    }
}

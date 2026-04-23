<?php

class EnrollmentDataController
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

    public function latest(int $limit = 5): array
    {
        $sql = 'SELECT i.enrolled_at, i.progress, f.title,
                       u.first_name, u.last_name
                FROM inscriptions i
                INNER JOIN formations f ON f.id = i.formation_id
                INNER JOIN utilisateurs u ON u.id = i.user_id
                ORDER BY i.enrolled_at DESC
                LIMIT :limit';
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }
}

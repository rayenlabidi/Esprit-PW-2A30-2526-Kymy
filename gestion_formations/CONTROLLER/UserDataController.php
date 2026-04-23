<?php

class UserDataController
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        $sql = 'SELECT u.id, u.first_name, u.last_name, u.email, u.status, u.headline, u.created_at,
                       r.name AS role_name, r.slug AS role_slug
                FROM utilisateurs u
                INNER JOIN roles r ON r.id = u.role_id
                ORDER BY u.created_at DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sql = 'SELECT u.*, r.name AS role_name, r.slug AS role_slug
                FROM utilisateurs u
                INNER JOIN roles r ON r.id = u.role_id
                WHERE u.id = :id
                LIMIT 1';
        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT u.*, r.name AS role_name, r.slug AS role_slug
                FROM utilisateurs u
                INNER JOIN roles r ON r.id = u.role_id
                WHERE u.email = :email
                LIMIT 1';
        $statement = $this->db->prepare($sql);
        $statement->execute(['email' => $email]);
        return $statement->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO utilisateurs (role_id, first_name, last_name, email, password, headline, bio, avatar_url, status)
                VALUES (:role_id, :first_name, :last_name, :email, :password, :headline, :bio, :avatar_url, :status)';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'role_id' => $data['role_id'],
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'email' => trim($data['email']),
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'headline' => trim($data['headline'] ?? ''),
            'bio' => trim($data['bio'] ?? ''),
            'avatar_url' => trim($data['avatar_url'] ?? ''),
            'status' => $data['status'] ?? 'active',
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }

        return $user;
    }
}

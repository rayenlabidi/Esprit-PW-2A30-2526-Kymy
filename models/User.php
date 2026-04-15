<?php

class User
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

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
    }

    public function roleDistribution(): array
    {
        $sql = 'SELECT r.name, r.slug, COUNT(u.id) AS total
                FROM roles r
                LEFT JOIN utilisateurs u ON u.role_id = r.id
                GROUP BY r.id, r.name, r.slug
                ORDER BY r.id';
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

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE utilisateurs
                SET role_id = :role_id,
                    first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    headline = :headline,
                    bio = :bio,
                    avatar_url = :avatar_url,
                    status = :status' .
                (!empty($data['password']) ? ', password = :password' : '') .
                ' WHERE id = :id';

        $params = [
            'id' => $id,
            'role_id' => $data['role_id'],
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'email' => trim($data['email']),
            'headline' => trim($data['headline'] ?? ''),
            'bio' => trim($data['bio'] ?? ''),
            'avatar_url' => trim($data['avatar_url'] ?? ''),
            'status' => $data['status'] ?? 'active',
        ];

        if (!empty($data['password'])) {
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($params);
    }

    public function delete(int $id): void
    {
        $statement = $this->db->prepare('DELETE FROM utilisateurs WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user['password'])) {
            return null;
        }

        return $user;
    }
}

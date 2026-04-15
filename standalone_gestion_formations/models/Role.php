<?php

class Role
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        $statement = $this->db->query('SELECT id, name, slug, description FROM roles ORDER BY id ASC');
        return $statement->fetchAll();
    }

    public function bySlug(string $slug): ?array
    {
        $statement = $this->db->prepare('SELECT id, name, slug, description FROM roles WHERE slug = :slug LIMIT 1');
        $statement->execute(['slug' => $slug]);
        return $statement->fetch() ?: null;
    }
}

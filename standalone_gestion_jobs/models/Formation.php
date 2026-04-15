<?php

class Formation
{
    public function __construct(private PDO $db)
    {
    }

    public function all(array $filters = []): array
    {
        $sql = 'SELECT f.*, c.name AS category_name,
                       u.first_name, u.last_name,
                       COUNT(i.id) AS enrolled_count
                FROM formations f
                LEFT JOIN categories c ON c.id = f.category_id
                LEFT JOIN utilisateurs u ON u.id = f.creator_id
                LEFT JOIN inscriptions i ON i.formation_id = f.id
                WHERE 1=1';
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= ' AND (f.title LIKE :search OR f.description LIKE :search OR f.tags LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['category_id'])) {
            $sql .= ' AND f.category_id = :category_id';
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['level'])) {
            $sql .= ' AND f.level = :level';
            $params['level'] = $filters['level'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND f.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= ' AND f.price <= :max_price';
            $params['max_price'] = $filters['max_price'];
        }

        $sql .= ' GROUP BY f.id, c.name, u.first_name, u.last_name
                  ORDER BY f.created_at DESC';

        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function featured(int $limit = 3): array
    {
        $statement = $this->db->prepare(
            'SELECT f.id, f.title, f.level, f.price, f.image_url, c.name AS category_name
             FROM formations f
             LEFT JOIN categories c ON c.id = f.category_id
             WHERE f.status = "published"
             ORDER BY f.created_at DESC
             LIMIT :limit'
        );
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sql = 'SELECT f.*, c.name AS category_name,
                       u.first_name, u.last_name, u.email AS creator_email,
                       COUNT(i.id) AS enrolled_count
                FROM formations f
                LEFT JOIN categories c ON c.id = f.category_id
                LEFT JOIN utilisateurs u ON u.id = f.creator_id
                LEFT JOIN inscriptions i ON i.formation_id = f.id
                WHERE f.id = :id
                GROUP BY f.id, c.name, u.first_name, u.last_name, u.email
                LIMIT 1';
        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO formations
                (title, description, category_id, level, price, duration_hours, status, creator_id, image_url, tags)
                VALUES
                (:title, :description, :category_id, :level, :price, :duration_hours, :status, :creator_id, :image_url, :tags)';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'category_id' => $data['category_id'],
            'level' => $data['level'],
            'price' => $data['price'],
            'duration_hours' => $data['duration_hours'],
            'status' => $data['status'],
            'creator_id' => $data['creator_id'],
            'image_url' => trim($data['image_url'] ?? ''),
            'tags' => trim($data['tags'] ?? ''),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE formations
                SET title = :title,
                    description = :description,
                    category_id = :category_id,
                    level = :level,
                    price = :price,
                    duration_hours = :duration_hours,
                    status = :status,
                    creator_id = :creator_id,
                    image_url = :image_url,
                    tags = :tags
                WHERE id = :id';
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'id' => $id,
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'category_id' => $data['category_id'],
            'level' => $data['level'],
            'price' => $data['price'],
            'duration_hours' => $data['duration_hours'],
            'status' => $data['status'],
            'creator_id' => $data['creator_id'],
            'image_url' => trim($data['image_url'] ?? ''),
            'tags' => trim($data['tags'] ?? ''),
        ]);
    }

    public function delete(int $id): void
    {
        $statement = $this->db->prepare('DELETE FROM formations WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function stats(): array
    {
        return [
            'total' => (int) $this->db->query('SELECT COUNT(*) FROM formations')->fetchColumn(),
            'published' => (int) $this->db->query('SELECT COUNT(*) FROM formations WHERE status = "published"')->fetchColumn(),
            'enrollments' => (int) $this->db->query('SELECT COUNT(*) FROM inscriptions')->fetchColumn(),
            'average_price' => (float) $this->db->query('SELECT COALESCE(AVG(price), 0) FROM formations')->fetchColumn(),
        ];
    }
}

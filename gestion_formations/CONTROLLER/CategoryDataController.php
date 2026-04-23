<?php

class CategoryDataController
{
    public function __construct(private PDO $db)
    {
    }

    public function all(?string $scope = null): array
    {
        if ($scope === null) {
            return $this->db->query('SELECT id, name, slug, scope, description FROM categories ORDER BY name ASC')->fetchAll();
        }

        $statement = $this->db->prepare('SELECT id, name, slug, scope, description FROM categories WHERE scope IN (:scope, "all") ORDER BY name ASC');
        $statement->execute(['scope' => $scope]);
        return $statement->fetchAll();
    }
}

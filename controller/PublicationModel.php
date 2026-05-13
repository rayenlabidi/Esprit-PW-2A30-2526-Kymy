<?php
require_once __DIR__ . '/../config.php';

class PublicationModel
{
    public function listePublications($search = '')
    {
        $sql = 'SELECT p.*,
                       COUNT(DISTINCT c.id) AS comments_count,
                       COUNT(DISTINCT l.id) AS likes_count
                FROM publication p
                LEFT JOIN comments c ON c.publication_id = p.id
                LEFT JOIN publication_likes l ON l.publication_id = p.id
                WHERE 1 = 1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (p.content LIKE :search OR p.user_name LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' GROUP BY p.id ORDER BY p.created_at DESC, p.id DESC';

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getPublicationById($id)
    {
        $sql = 'SELECT * FROM publication WHERE id = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => (int) $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function addPublication($publication)
    {
        $sql = 'INSERT INTO publication
                (user_id, user_name, user_init, user_role, user_avatar, content, has_image, image_url, likes)
                VALUES
                (:user_id, :user_name, :user_init, :user_role, :user_avatar, :content, :has_image, :image_url, 0)';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'user_id' => $publication->getUserId(),
                'user_name' => $publication->getUserName(),
                'user_init' => $publication->getUserInit(),
                'user_role' => $publication->getUserRole(),
                'user_avatar' => $publication->getUserAvatar(),
                'content' => $publication->getContent(),
                'has_image' => $publication->getImageUrl() !== '' ? 1 : 0,
                'image_url' => $publication->getImageUrl()
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function deletePublication($id)
    {
        $sql = 'DELETE FROM publication WHERE id = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => (int) $id]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function listeCommentaires($publicationId)
    {
        $sql = 'SELECT *
                FROM comments
                WHERE publication_id = :publication_id
                ORDER BY created_at ASC, id ASC';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['publication_id' => (int) $publicationId]);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function commentairesParPublication()
    {
        $sql = 'SELECT publication_id, COUNT(*) AS total
                FROM comments
                GROUP BY publication_id';
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $rows = $query->fetchAll();
            $map = [];
            foreach ($rows as $row) {
                $map[(int) $row['publication_id']] = (int) $row['total'];
            }
            return $map;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function addCommentaire($publicationId, $userName, $userInit, $userAvatar, $comment)
    {
        $sql = 'INSERT INTO comments (publication_id, user_name, user_init, user_avatar, comment, likes)
                VALUES (:publication_id, :user_name, :user_init, :user_avatar, :comment, 0)';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'publication_id' => (int) $publicationId,
                'user_name' => $userName,
                'user_init' => $userInit,
                'user_avatar' => $userAvatar,
                'comment' => $comment
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function likedPublicationIds($userId)
    {
        $sql = 'SELECT publication_id FROM publication_likes WHERE user_id = :user_id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['user_id' => (string) $userId]);
            $rows = $query->fetchAll();
            $ids = [];
            foreach ($rows as $row) {
                $ids[(int) $row['publication_id']] = true;
            }
            return $ids;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function toggleLike($publicationId, $userId)
    {
        $db = config::getConnexion();

        try {
            $db->beginTransaction();
            $check = $db->prepare('SELECT id FROM publication_likes WHERE publication_id = :publication_id AND user_id = :user_id LIMIT 1');
            $check->execute([
                'publication_id' => (int) $publicationId,
                'user_id' => (string) $userId
            ]);
            $existing = $check->fetch();

            if ($existing) {
                $delete = $db->prepare('DELETE FROM publication_likes WHERE id = :id');
                $delete->execute(['id' => (int) $existing['id']]);
                $update = $db->prepare('UPDATE publication SET likes = GREATEST(likes - 1, 0) WHERE id = :id');
                $update->execute(['id' => (int) $publicationId]);
                $db->commit();
                return false;
            }

            $insert = $db->prepare('INSERT INTO publication_likes (publication_id, user_id) VALUES (:publication_id, :user_id)');
            $insert->execute([
                'publication_id' => (int) $publicationId,
                'user_id' => (string) $userId
            ]);
            $update = $db->prepare('UPDATE publication SET likes = likes + 1 WHERE id = :id');
            $update->execute(['id' => (int) $publicationId]);
            $db->commit();
            return true;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function statistiquesPublications()
    {
        $db = config::getConnexion();
        try {
            $stats = $db->query('SELECT COUNT(*) AS total FROM publication')->fetch();
            $likes = $db->query('SELECT COUNT(*) AS total FROM publication_likes')->fetch();
            $comments = $db->query('SELECT COUNT(*) AS total FROM comments')->fetch();
            return [
                'total' => $stats ? (int) $stats['total'] : 0,
                'likes' => $likes ? (int) $likes['total'] : 0,
                'comments' => $comments ? (int) $comments['total'] : 0
            ];
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function countPublications()
    {
        $db = config::getConnexion();
        $query = $db->query('SELECT COUNT(*) AS total FROM publication');
        $row = $query->fetch();
        return $row ? (int) $row['total'] : 0;
    }
}
?>

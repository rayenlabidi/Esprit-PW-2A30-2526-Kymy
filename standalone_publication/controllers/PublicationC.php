<?php
include_once __DIR__ . '/../config/config.php';
include_once __DIR__ . '/../models/publication.php';
include_once __DIR__ . '/../models/comment.php';

class PublicationC
{
    // ==================== PUBLICATIONS ====================
    
    public function ListePublications($keyword = '', $sort = 'newest')
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM publication WHERE 1=1";
            $params = [];
            
            if (!empty($keyword)) {
                $sql .= " AND (content LIKE :keyword OR user_name LIKE :keyword)";
                $params['keyword'] = '%' . $keyword . '%';
            }
            
            if ($sort === 'most_liked') {
                $sql .= " ORDER BY likes DESC";
            } else if ($sort === 'oldest') {
                $sql .= " ORDER BY created_at ASC";
            } else {
                $sql .= " ORDER BY created_at DESC";
            }
            
            $query = $db->prepare($sql);
            $query->execute($params);
            return $query->fetchAll();
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function ListePublicationsByUser($user_id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM publication WHERE user_id = :user_id ORDER BY created_at DESC";
            $query = $db->prepare($sql);
            $query->execute(['user_id' => $user_id]);
            return $query->fetchAll();
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function GetPublicationById($id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM publication WHERE id = :id";
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function AddPublication($p)
    {
        $sql = "INSERT INTO publication (user_id, user_name, user_init, user_role, user_avatar, content, image_url, likes) 
                VALUES (:user_id, :user_name, :user_init, :user_role, :user_avatar, :content, :image_url, 0)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'user_id'    => $p->getUserId(),
                'user_name'  => $p->getUserName(),
                'user_init'  => $p->getUserInit(),
                'user_role'  => $p->getUserRole(),
                'user_avatar'=> $p->getUserAvatar(),
                'content'    => $p->getContent(),
                'image_url'  => $p->getImageUrl()
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            error_log('Erreur: ' . $e->getMessage());
            return false;
        }
    }

    public function DeletePublication($id, $user_id)
    {
        $sql = "DELETE FROM publication WHERE id = :id AND user_id = :user_id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        $req->bindValue(':user_id', $user_id);
        try {
            $req->execute();
            return $req->rowCount() > 0;
        } catch (Exception $e) {
            error_log('DeletePublication error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Admin delete - bypasses user_id ownership check
     */
    public function DeletePublicationAdmin($id)
    {
        $sql = "DELETE FROM publication WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
            return $req->rowCount() > 0;
        } catch (Exception $e) {
            error_log('DeletePublicationAdmin error: ' . $e->getMessage());
            return false;
        }
    }

    public function UpdatePublication($p, $id, $user_id)
    {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE publication SET 
                    content = :content
                WHERE id = :id AND user_id = :user_id'
            );
            return $query->execute([
                'content' => $p->getContent(),
                'id'      => $id,
                'user_id' => $user_id
            ]);
        } catch (Exception $e) {
            echo "Erreur: " . $e->getMessage();
            return false;
        }
    }

    // ==================== SMART LIKE SYSTEM ====================
    
    /**
     * Toggle like on a publication (smart like - can only like once)
     * @param int $publication_id The publication ID
     * @param string $user_id The user ID liking the publication
     * @return array ['liked' => bool, 'total_likes' => int]
     */
    public function TogglePublicationLike($publication_id, $user_id)
    {
        $db = config::getConnexion();
        try {
            // Check if user already liked this publication
            $checkSql = "SELECT id FROM publication_likes WHERE publication_id = :pub_id AND user_id = :user_id";
            $checkQuery = $db->prepare($checkSql);
            $checkQuery->execute(['pub_id' => $publication_id, 'user_id' => $user_id]);
            $existing = $checkQuery->fetch();
            
            if ($existing) {
                // User already liked - remove like (UNLIKE)
                $deleteSql = "DELETE FROM publication_likes WHERE publication_id = :pub_id AND user_id = :user_id";
                $deleteQuery = $db->prepare($deleteSql);
                $deleteQuery->execute(['pub_id' => $publication_id, 'user_id' => $user_id]);
                
                // Count first (MySQL does not allow subquery on the same table being updated)
                $countSql = "SELECT COUNT(*) as cnt FROM publication_likes WHERE publication_id = :pub_id";
                $countQuery = $db->prepare($countSql);
                $countQuery->execute(['pub_id' => $publication_id]);
                $newCount = (int)$countQuery->fetch()['cnt'];
                
                // Then update
                $updateSql = "UPDATE publication SET likes = :cnt WHERE id = :pub_id";
                $updateQuery = $db->prepare($updateSql);
                $updateQuery->execute(['cnt' => $newCount, 'pub_id' => $publication_id]);
                
                return ['liked' => false, 'total_likes' => $newCount];
            } else {
                // User hasn't liked - add like
                $insertSql = "INSERT INTO publication_likes (publication_id, user_id) VALUES (:pub_id, :user_id)";
                $insertQuery = $db->prepare($insertSql);
                $insertQuery->execute(['pub_id' => $publication_id, 'user_id' => $user_id]);
                
                // Count first (MySQL does not allow subquery on the same table being updated)
                $countSql = "SELECT COUNT(*) as cnt FROM publication_likes WHERE publication_id = :pub_id";
                $countQuery = $db->prepare($countSql);
                $countQuery->execute(['pub_id' => $publication_id]);
                $newCount = (int)$countQuery->fetch()['cnt'];
                
                // Then update
                $updateSql = "UPDATE publication SET likes = :cnt WHERE id = :pub_id";
                $updateQuery = $db->prepare($updateSql);
                $updateQuery->execute(['cnt' => $newCount, 'pub_id' => $publication_id]);
                
                return ['liked' => true, 'total_likes' => $newCount];
            }
        } catch (Exception $e) {
            error_log("TogglePublicationLike error: " . $e->getMessage());
            return ['liked' => false, 'total_likes' => 0, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Check if a user has liked a publication
     */
    public function HasUserLikedPublication($publication_id, $user_id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT id FROM publication_likes WHERE publication_id = :pub_id AND user_id = :user_id";
            $query = $db->prepare($sql);
            $query->execute(['pub_id' => $publication_id, 'user_id' => $user_id]);
            return $query->fetch() !== false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get like statuses for multiple publications
     */
    public function GetUserPublicationLikes($user_id, $publication_ids = [])
    {
        if (empty($publication_ids)) return [];
        
        $db = config::getConnexion();
        try {
            $placeholders = implode(',', array_fill(0, count($publication_ids), '?'));
            $sql = "SELECT publication_id FROM publication_likes WHERE user_id = ? AND publication_id IN ($placeholders)";
            $params = array_merge([$user_id], $publication_ids);
            $query = $db->prepare($sql);
            $query->execute($params);
            $results = $query->fetchAll();
            return array_column($results, 'publication_id');
        } catch (Exception $e) {
            return [];
        }
    }
    
    // ==================== COMMENT LIKES (Smart Like) ====================
    
    /**
     * Toggle like on a comment (smart like - can only like once)
     */
    public function ToggleCommentLike($comment_id, $user_id)
    {
        $db = config::getConnexion();
        try {
            // Check if user already liked this comment
            $checkSql = "SELECT id FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id";
            $checkQuery = $db->prepare($checkSql);
            $checkQuery->execute(['comment_id' => $comment_id, 'user_id' => $user_id]);
            $existing = $checkQuery->fetch();
            
            if ($existing) {
                // Remove like
                $deleteSql = "DELETE FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id";
                $deleteQuery = $db->prepare($deleteSql);
                $deleteQuery->execute(['comment_id' => $comment_id, 'user_id' => $user_id]);
                
                // Count first (MySQL does not allow subquery on the same table being updated)
                $countSql = "SELECT COUNT(*) as cnt FROM comment_likes WHERE comment_id = :comment_id";
                $countQuery = $db->prepare($countSql);
                $countQuery->execute(['comment_id' => $comment_id]);
                $newCount = (int)$countQuery->fetch()['cnt'];
                
                // Then update
                $updateSql = "UPDATE comments SET likes = :cnt WHERE id = :comment_id";
                $updateQuery = $db->prepare($updateSql);
                $updateQuery->execute(['cnt' => $newCount, 'comment_id' => $comment_id]);
                
                return ['liked' => false, 'total_likes' => $newCount];
            } else {
                // Add like
                $insertSql = "INSERT INTO comment_likes (comment_id, user_id) VALUES (:comment_id, :user_id)";
                $insertQuery = $db->prepare($insertSql);
                $insertQuery->execute(['comment_id' => $comment_id, 'user_id' => $user_id]);
                
                // Count first (MySQL does not allow subquery on the same table being updated)
                $countSql = "SELECT COUNT(*) as cnt FROM comment_likes WHERE comment_id = :comment_id";
                $countQuery = $db->prepare($countSql);
                $countQuery->execute(['comment_id' => $comment_id]);
                $newCount = (int)$countQuery->fetch()['cnt'];
                
                // Then update
                $updateSql = "UPDATE comments SET likes = :cnt WHERE id = :comment_id";
                $updateQuery = $db->prepare($updateSql);
                $updateQuery->execute(['cnt' => $newCount, 'comment_id' => $comment_id]);
                
                return ['liked' => true, 'total_likes' => $newCount];
            }
        } catch (Exception $e) {
            error_log("ToggleCommentLike error: " . $e->getMessage());
            return ['liked' => false, 'total_likes' => 0, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Check if user has liked a comment
     */
    public function HasUserLikedComment($comment_id, $user_id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT id FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id";
            $query = $db->prepare($sql);
            $query->execute(['comment_id' => $comment_id, 'user_id' => $user_id]);
            return $query->fetch() !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    // ==================== COMMENTS ====================

    public function ListeComments($publication_id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM comments WHERE publication_id = :publication_id AND (parent_id IS NULL OR parent_id = 0) ORDER BY created_at ASC";
            $query = $db->prepare($sql);
            $query->execute(['publication_id' => $publication_id]);
            $comments = $query->fetchAll();
            
            foreach ($comments as &$comment) {
                $sql2 = "SELECT * FROM comments WHERE parent_id = :parent_id ORDER BY created_at ASC";
                $query2 = $db->prepare($sql2);
                $query2->execute(['parent_id' => $comment['id']]);
                $comment['replies'] = $query2->fetchAll();
            }
            
            return $comments;
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function AddComment($c)
    {
        $sql = "INSERT INTO comments (publication_id, user_name, user_init, user_avatar, comment, parent_id, likes) 
                VALUES (:publication_id, :user_name, :user_init, :user_avatar, :comment, :parent_id, 0)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            return $query->execute([
                'publication_id' => $c->getPublicationId(),
                'user_name'      => $c->getUserName(),
                'user_init'      => $c->getUserInit(),
                'user_avatar'    => $c->getUserAvatar(),
                'comment'        => $c->getComment(),
                'parent_id'      => $c->getParentId()
            ]);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }

    public function DeleteComment($comment_id)
    {
        $db = config::getConnexion();
        try {
            // First delete any child replies
            $sqlReplies = "DELETE FROM comments WHERE parent_id = :id";
            $reqReplies = $db->prepare($sqlReplies);
            $reqReplies->bindValue(':id', $comment_id);
            $reqReplies->execute();

            // Then delete the comment itself
            $sql = "DELETE FROM comments WHERE id = :id";
            $req = $db->prepare($sql);
            $req->bindValue(':id', $comment_id);
            $req->execute();
            return $req->rowCount() > 0;
        } catch (Exception $e) {
            error_log('DeleteComment error: ' . $e->getMessage());
            return false;
        }
    }

    public function EditComment($comment_id, $comment, $user_name)
    {
        try {
            $db = config::getConnexion();
            $query = $db->prepare('UPDATE comments SET comment = :comment WHERE id = :id AND user_name = :user_name');
            return $query->execute([
                'comment'   => $comment,
                'id'        => $comment_id,
                'user_name' => $user_name
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function ListeAllComments()
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT c.*, p.user_name as post_author 
                    FROM comments c 
                    JOIN publication p ON c.publication_id = p.id 
                    ORDER BY c.created_at DESC";
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // ==================== IMAGE UPLOAD ====================

    public function UploadImage($file)
    {
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed.'];
        }
        
        if ($file['size'] > 5000000) {
            return ['success' => false, 'error' => 'File is too large. Max 5MB.'];
        }
        
        $imageData = file_get_contents($file['tmp_name']);
        if ($imageData === false) {
            return ['success' => false, 'error' => 'Failed to read image file.'];
        }
        
        $base64 = base64_encode($imageData);
        $mime = $file['type'];
        $base64String = 'data:' . $mime . ';base64,' . $base64;
        
        return ['success' => true, 'filename' => $base64String];
    }
}

// ==================== AJAX HANDLER ====================
// Run AJAX handler unless the including page has its own handler (e.g., admin.php)
if (!defined('ADMIN_AJAX_HANDLER') && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');
    
    $controller = new PublicationC();
    $action = $_POST['action'] ?? '';
    $response = ['success' => false];

    switch ($action) {
        case 'create':
            $pub = new publication(
                $_POST['user_name'],
                $_POST['user_init'],
                $_POST['user_role'],
                $_POST['user_avatar'],
                $_POST['content'],
                $_POST['image_url'] ?? ''
            );
            $pub->setUserId($_POST['user_id']);
            $result = $controller->AddPublication($pub);
            $response = ['success' => $result];
            break;
            
        case 'update':
            $pub = new publication('', '', '', '', $_POST['content']);
            $result = $controller->UpdatePublication($pub, $_POST['id'], $_POST['user_id']);
            $response = ['success' => $result];
            break;
            
        case 'delete':
            $user_id = $_POST['user_id'] ?? null;
            if (!$user_id) {
                $post = $controller->GetPublicationById($_POST['id']);
                if ($post) {
                    $user_id = $post['user_id'];
                }
            }
            $result = false;
            if ($user_id) {
                $result = $controller->DeletePublication($_POST['id'], $user_id);
            }
            $response = ['success' => $result];
            break;
            
        // ========== SMART LIKE ACTIONS ==========
        case 'toggle_like':
            $result = $controller->TogglePublicationLike($_POST['publication_id'], $_POST['user_id']);
            $response = ['success' => true, 'liked' => $result['liked'], 'total_likes' => $result['total_likes']];
            if (isset($result['error'])) {
                $response['success'] = false;
                $response['error'] = $result['error'];
            }
            break;
            
        case 'toggle_comment_like':
            $result = $controller->ToggleCommentLike($_POST['comment_id'], $_POST['user_id']);
            $response = ['success' => true, 'liked' => $result['liked'], 'total_likes' => $result['total_likes']];
            if (isset($result['error'])) {
                $response['success'] = false;
                $response['error'] = $result['error'];
            }
            break;
            
        case 'check_like_status':
            $liked = $controller->HasUserLikedPublication($_POST['publication_id'], $_POST['user_id']);
            $response = ['success' => true, 'liked' => $liked];
            break;
            
        case 'get_user_likes':
            $publication_ids = isset($_POST['publication_ids']) ? json_decode($_POST['publication_ids'], true) : [];
            $liked_ids = $controller->GetUserPublicationLikes($_POST['user_id'], $publication_ids);
            $response = ['success' => true, 'liked_publications' => $liked_ids];
            break;
            
        // ========== COMMENT ACTIONS ==========
        case 'get_comments':
            $comments = $controller->ListeComments($_POST['publication_id']);
            $response = ['success' => true, 'comments' => $comments];
            break;
            
        case 'add_comment':
            $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] ? (int)$_POST['parent_id'] : null;
            $comment = new comment(
                $_POST['publication_id'],
                $_POST['user_name'],
                $_POST['user_init'],
                $_POST['user_avatar'],
                $_POST['comment'],
                $parent_id
            );
            $result = $controller->AddComment($comment);
            $response = ['success' => $result];
            break;
            
        case 'delete_comment':
            $result = $controller->DeleteComment($_POST['comment_id']);
            $response = ['success' => $result];
            break;
            
        case 'edit_comment':
            $result = $controller->EditComment($_POST['comment_id'], $_POST['comment'], $_POST['user_name']);
            $response = ['success' => $result];
            break;
            
        case 'get_user_posts':
            $posts = $controller->ListePublicationsByUser($_POST['user_id']);
            $response = ['success' => true, 'posts' => $posts];
            break;
            
        case 'get_publications':
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
            $sort = isset($_POST['sort']) ? $_POST['sort'] : 'newest';
            $posts = $controller->ListePublications($keyword, $sort);
            $response = ['success' => true, 'posts' => $posts];
            break;
            
        case 'upload_image':
            if (isset($_FILES['image'])) {
                $response = $controller->UploadImage($_FILES['image']);
            } else {
                $response = ['success' => false, 'error' => 'No image uploaded'];
            }
            break;
            
        default:
            $response = ['success' => false, 'error' => 'Unknown action: ' . $action];
            break;
    }

    echo json_encode($response);
    exit;
}
?>
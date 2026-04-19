<?php
include_once __DIR__ . '/../config/config.php';
include_once __DIR__ . '/../models/publication.php';
include_once __DIR__ . '/../models/comment.php';

class PublicationC
{
    // ==================== PUBLICATION METHODS ====================
    
    public function ListePublications()
    {
        $db = config::getConnexion();
        try {
            $liste = $db->query('SELECT * FROM publication ORDER BY created_at DESC');
            return $liste->fetchAll();
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
            echo 'Erreur: ' . $e->getMessage();
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
            return $req->execute();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
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

    public function UpdateLikes($id, $likes)
    {
        try {
            $db = config::getConnexion();
            $likes = max(0, (int)$likes);
            $query = $db->prepare('UPDATE publication SET likes = :likes WHERE id = :id');
            $query->execute(['likes' => $likes, 'id' => $id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function GetPublicationById($id)
    {
        $db = config::getConnexion();
        try {
            $query = $db->prepare('SELECT * FROM publication WHERE id = :id');
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // ==================== COMMENT METHODS ====================

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

    public function UpdateCommentLikes($comment_id, $likes)
    {
        try {
            $db = config::getConnexion();
            $likes = max(0, (int)$likes);
            $query = $db->prepare('UPDATE comments SET likes = :likes WHERE id = :id');
            return $query->execute(['likes' => $likes, 'id' => $comment_id]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function DeleteComment($comment_id)
    {
        $sql = "DELETE FROM comments WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $comment_id);
        try {
            return $req->execute();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
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
        $target_dir = __DIR__ . '/../uploads/';
        
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed.'];
        }
        
        if ($file['size'] > 5000000) {
            return ['success' => false, 'error' => 'File is too large. Max 5MB.'];
        }
        
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return ['success' => true, 'filename' => '/workify/standalone_publication/uploads/' . $new_filename];
        }
        
        return ['success' => false, 'error' => 'Failed to upload image'];
    }
}

// ==================== AJAX HANDLER ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
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
            $result = $controller->DeletePublication($_POST['id'], $_POST['user_id']);
            $response = ['success' => $result];
            break;
            
        case 'update_likes':
            $result = $controller->UpdateLikes($_POST['id'], $_POST['likes']);
            $response = ['success' => $result, 'likes' => $_POST['likes']];
            break;
            
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
            
        case 'update_comment_likes':
            $result = $controller->UpdateCommentLikes($_POST['comment_id'], $_POST['likes']);
            $response = ['success' => $result, 'likes' => $_POST['likes']];
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
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Publication.php';

class PublicationController {
    private $publication;

    public function __construct() {
        $this->publication = new Publication();
    }

    public function getAll() {
        return $this->publication->getAll();
    }

    public function getOne($id) {
        return $this->publication->getById($id);
    }

    public function create($data) {
        $errors = $this->validateData($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $result = $this->publication->create($data);
        return ['success' => $result];
    }

    public function update($id, $content, $user_id) {
        if (empty($content)) {
            return ['success' => false, 'errors' => ['Content cannot be empty']];
        }
        if (strlen($content) < 5) {
            return ['success' => false, 'errors' => ['Content must be at least 5 characters']];
        }
        if (strlen($content) > 5000) {
            return ['success' => false, 'errors' => ['Content cannot exceed 5000 characters']];
        }
        
        $result = $this->publication->update($id, $content, $user_id);
        if ($result) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => ['You can only edit your own posts']];
        }
    }

    public function delete($id, $user_id) {
        $result = $this->publication->delete($id, $user_id);
        if ($result) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => ['You can only delete your own posts']];
        }
    }

    public function updateLikes($id, $likes) {
        $currentPost = $this->publication->getById($id);
        $currentLikes = $currentPost ? (int)$currentPost['likes'] : 0;
        
        $difference = (int)$likes - $currentLikes;
        
        if (abs($difference) !== 1) {
            if ($difference > 0) {
                $likes = $currentLikes + 1;
            } else if ($difference < 0) {
                $likes = $currentLikes - 1;
            } else {
                $likes = $currentLikes;
            }
        }
        
        $likes = max(0, (int)$likes);
        
        $result = $this->publication->updateLikes($id, $likes);
        return ['success' => $result, 'likes' => $likes];
    }

    public function addComment($publication_id, $user_name, $user_init, $user_avatar, $comment) {
        if (empty($comment) || strlen($comment) < 2) {
            return ['success' => false, 'error' => 'Comment must be at least 2 characters'];
        }
        
        $result = $this->publication->addComment($publication_id, $user_name, $user_init, $user_avatar, $comment);
        return ['success' => $result];
    }
    
    public function getComments($publication_id) {
        return $this->publication->getComments($publication_id);
    }

    private function validateData($data) {
        $errors = [];
        
        if (empty($data['user_name']) || strlen($data['user_name']) < 2) {
            $errors[] = 'Name must be at least 2 characters';
        }
        if (empty($data['user_init']) || strlen($data['user_init']) > 5) {
            $errors[] = 'Initials must be 1-5 characters';
        }
        if (empty($data['content'])) {
            $errors[] = 'Content cannot be empty';
        } elseif (strlen($data['content']) < 5) {
            $errors[] = 'Content must be at least 5 characters';
        } elseif (strlen($data['content']) > 5000) {
            $errors[] = 'Content cannot exceed 5000 characters';
        }
        
        return $errors;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');
    
    $controller = new PublicationController();
    $action = $_POST['action'] ?? '';
    $response = ['success' => false];

    switch ($action) {
        case 'create':
            if (!isset($_POST['user_id'])) {
                $_POST['user_id'] = 'current_user';
            }
            $response = $controller->create($_POST);
            break;
            
        case 'update':
            $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 'current_user';
            $response = $controller->update($_POST['id'], $_POST['content'], $user_id);
            break;
            
        case 'delete':
            $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 'admin';
            $response = $controller->delete($_POST['id'], $user_id);
            break;
            
        case 'update_likes':
            $response = $controller->updateLikes($_POST['id'], $_POST['likes']);
            break;
            
        case 'add_comment':
            $response = $controller->addComment(
                $_POST['publication_id'],
                $_POST['user_name'],
                $_POST['user_init'],
                $_POST['user_avatar'],
                $_POST['comment']
            );
            break;
            
        case 'get_comments':
            $comments = $controller->getComments($_POST['publication_id']);
            $response = ['success' => true, 'comments' => $comments];
            break;
            
        default:
            $response = ['success' => false, 'error' => 'Unknown action: ' . $action];
            break;
    }

    echo json_encode($response);
    exit;
}
?>
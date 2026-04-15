<?php
require_once __DIR__ . '/../config/database.php';

class Publication {
    private $conn;
    private $table = "publication";
    private $commentsTable = "comments";

    public function __construct() {
        $this->conn = Database::getConnexion();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, user_name, user_init, user_role, user_avatar, content) 
                  VALUES 
                  (:user_id, :user_name, :user_init, :user_role, :user_avatar, :content)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':user_name', $data['user_name']);
        $stmt->bindParam(':user_init', $data['user_init']);
        $stmt->bindParam(':user_role', $data['user_role']);
        $stmt->bindParam(':user_avatar', $data['user_avatar']);
        $stmt->bindParam(':content', $data['content']);
        
        return $stmt->execute();
    }

    public function update($id, $content, $user_id) {
        $query = "UPDATE " . $this->table . " SET content = :content WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    public function delete($id, $user_id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    public function updateLikes($id, $likes) {
        $likes = max(0, (int)$likes);
        $query = "UPDATE " . $this->table . " SET likes = :likes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':likes', $likes, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getComments($publication_id) {
        $query = "SELECT * FROM " . $this->commentsTable . " 
                  WHERE publication_id = :publication_id AND parent_id IS NULL 
                  ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':publication_id', $publication_id, PDO::PARAM_INT);
        $stmt->execute();
        $comments = $stmt->fetchAll();
        
        foreach ($comments as &$comment) {
            $query2 = "SELECT * FROM " . $this->commentsTable . " 
                       WHERE parent_id = :parent_id ORDER BY created_at ASC";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(':parent_id', $comment['id'], PDO::PARAM_INT);
            $stmt2->execute();
            $comment['replies'] = $stmt2->fetchAll();
        }
        
        return $comments;
    }

    public function addComment($publication_id, $user_name, $user_init, $user_avatar, $comment, $parent_id = null) {
        $query = "INSERT INTO " . $this->commentsTable . " 
                  (publication_id, user_name, user_init, user_avatar, comment, parent_id) 
                  VALUES 
                  (:publication_id, :user_name, :user_init, :user_avatar, :comment, :parent_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':publication_id', $publication_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_name', $user_name);
        $stmt->bindParam(':user_init', $user_init);
        $stmt->bindParam(':user_avatar', $user_avatar);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':parent_id', $parent_id);
        return $stmt->execute();
    }
    
    public function updateCommentLikes($comment_id, $likes) {
        $likes = max(0, (int)$likes);
        $query = "UPDATE " . $this->commentsTable . " SET likes = :likes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':likes', $likes, PDO::PARAM_INT);
        $stmt->bindParam(':id', $comment_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function deleteComment($comment_id) {
        $query = "DELETE FROM " . $this->commentsTable . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $comment_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function editComment($comment_id, $comment, $user_name) {
        $query = "UPDATE " . $this->commentsTable . " SET comment = :comment WHERE id = :id AND user_name = :user_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':id', $comment_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_name', $user_name);
        return $stmt->execute();
    }
    
    public function getAllComments() {
        $query = "SELECT c.*, p.user_name as post_author 
                  FROM " . $this->commentsTable . " c 
                  JOIN " . $this->table . " p ON c.publication_id = p.id 
                  ORDER BY c.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
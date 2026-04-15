<?php
require_once __DIR__ . '/../config/database.php';

class Publication {
    private $conn;
    private $table = "publication";

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
        $query = "UPDATE " . $this->table . " SET likes = :likes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':likes', $likes, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function addComment($publication_id, $user_name, $user_init, $user_avatar, $comment) {
        $query = "SELECT comments, comments_count FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $publication_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        
        $currentComments = $result['comments'];
        $currentCount = (int)$result['comments_count'];
        
        $newComment = $user_name . " (" . $user_init . "): " . $comment;
        $updatedComments = $currentComments ? $currentComments . "\n" . $newComment : $newComment;
        $newCount = $currentCount + 1;
        
        $updateQuery = "UPDATE " . $this->table . " 
                        SET comments = :comments, comments_count = :comments_count 
                        WHERE id = :id";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bindParam(':comments', $updatedComments);
        $updateStmt->bindParam(':comments_count', $newCount, PDO::PARAM_INT);
        $updateStmt->bindParam(':id', $publication_id, PDO::PARAM_INT);
        
        return $updateStmt->execute();
    }
    
    public function getComments($publication_id) {
        $query = "SELECT comments, comments_count FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $publication_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        
        $commentsArray = [];
        if ($result && $result['comments']) {
            $commentLines = explode("\n", $result['comments']);
            foreach ($commentLines as $line) {
                if (preg_match('/^(.+?)\s\((.+?)\):\s(.+)$/', $line, $matches)) {
                    $commentsArray[] = [
                        'user' => $matches[1],
                        'init' => $matches[2],
                        'comment' => $matches[3]
                    ];
                } elseif (preg_match('/^(.+?): (.+)$/', $line, $matches)) {
                    $commentsArray[] = [
                        'user' => $matches[1],
                        'init' => substr($matches[1], 0, 2),
                        'comment' => $matches[2]
                    ];
                }
            }
        }
        
        return [
            'count' => $result ? (int)$result['comments_count'] : 0,
            'comments' => $commentsArray
        ];
    }
}
?>
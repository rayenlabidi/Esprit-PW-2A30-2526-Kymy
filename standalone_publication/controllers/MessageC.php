<?php
include_once __DIR__ . '/../config/config.php';
include_once __DIR__ . '/../models/message.php';

class MessageC
{
    public function ListeConversations($user_id)
    {
        $db = config::getConnexion();
        try {
            // Fixed query - properly handles conversation listing
            $sql = "SELECT 
                        DISTINCT other_user_id,
                        other_user_name,
                        other_user_init,
                        other_user_avatar,
                        last_message,
                        last_time,
                        unread_count
                    FROM (
                        SELECT 
                            CASE 
                                WHEN sender_id = :user_id THEN receiver_id
                                ELSE sender_id
                            END as other_user_id,
                            CASE 
                                WHEN sender_id = :user_id THEN receiver_name
                                ELSE sender_name
                            END as other_user_name,
                            CASE 
                                WHEN sender_id = :user_id THEN receiver_init
                                ELSE sender_init
                            END as other_user_init,
                            CASE 
                                WHEN sender_id = :user_id THEN receiver_avatar
                                ELSE sender_avatar
                            END as other_user_avatar,
                            (
                                SELECT content FROM messages m2 
                                WHERE (m2.sender_id = :user_id AND m2.receiver_id = other_user_id)
                                    OR (m2.sender_id = other_user_id AND m2.receiver_id = :user_id)
                                ORDER BY m2.created_at DESC LIMIT 1
                            ) as last_message,
                            (
                                SELECT created_at FROM messages m2 
                                WHERE (m2.sender_id = :user_id AND m2.receiver_id = other_user_id)
                                    OR (m2.sender_id = other_user_id AND m2.receiver_id = :user_id)
                                ORDER BY m2.created_at DESC LIMIT 1
                            ) as last_time,
                            (
                                SELECT COUNT(*) FROM messages 
                                WHERE receiver_id = :user_id AND sender_id = other_user_id AND is_read = 0
                            ) as unread_count,
                            messages.created_at as msg_created_at
                        FROM messages 
                        WHERE sender_id = :user_id OR receiver_id = :user_id
                    ) as conv
                    ORDER BY last_time DESC";
            
            $query = $db->prepare($sql);
            $query->execute(['user_id' => $user_id]);
            $results = $query->fetchAll();
            
            // Filter out null other_user_id (shouldn't happen but just in case)
            return array_filter($results, function($conv) {
                return !empty($conv['other_user_id']);
            });
        } catch (Exception $e) {
            error_log("ListeConversations error: " . $e->getMessage());
            return [];
        }
    }
    
    public function ListeMessages($user_id, $other_user_id)
    {
        $db = config::getConnexion();
        try {
            // First mark messages as read
            $updateSql = "UPDATE messages SET is_read = 1 
                          WHERE receiver_id = :user_id AND sender_id = :other_user_id";
            $updateQuery = $db->prepare($updateSql);
            $updateQuery->execute([
                'user_id' => $user_id,
                'other_user_id' => $other_user_id
            ]);
            
            // Then fetch all messages
            $sql = "SELECT * FROM messages 
                    WHERE (sender_id = :user_id AND receiver_id = :other_user_id)
                       OR (sender_id = :other_user_id AND receiver_id = :user_id)
                    ORDER BY created_at ASC";
            $query = $db->prepare($sql);
            $query->execute([
                'user_id' => $user_id,
                'other_user_id' => $other_user_id
            ]);
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log("ListeMessages error: " . $e->getMessage());
            return [];
        }
    }
    
    public function AddMessage($m)
    {
        $sql = "INSERT INTO messages (sender_id, receiver_id, sender_name, receiver_name, sender_init, receiver_init, sender_avatar, receiver_avatar, content, is_read) 
                VALUES (:sender_id, :receiver_id, :sender_name, :receiver_name, :sender_init, :receiver_init, :sender_avatar, :receiver_avatar, :content, 0)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            return $query->execute([
                'sender_id' => $m->getSenderId(),
                'receiver_id' => $m->getReceiverId(),
                'sender_name' => $m->getSenderName(),
                'receiver_name' => $m->getReceiverName(),
                'sender_init' => $m->getSenderInit(),
                'receiver_init' => $m->getReceiverInit(),
                'sender_avatar' => $m->getSenderAvatar(),
                'receiver_avatar' => $m->getReceiverAvatar(),
                'content' => $m->getContent()
            ]);
        } catch (Exception $e) {
            error_log("AddMessage error: " . $e->getMessage());
            return false;
        }
    }
    
    public function DeleteMessage($id, $sender_id)
    {
        $sql = "DELETE FROM messages WHERE id = :id AND sender_id = :sender_id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        $req->bindValue(':sender_id', $sender_id);
        try {
            return $req->execute();
        } catch (Exception $e) {
            error_log("DeleteMessage error: " . $e->getMessage());
            return false;
        }
    }
    
    public function DeleteConversation($user_id, $other_user_id)
    {
        $sql = "DELETE FROM messages 
                WHERE (sender_id = :user_id AND receiver_id = :other_user_id)
                   OR (sender_id = :other_user_id AND receiver_id = :user_id)";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':user_id', $user_id);
        $req->bindValue(':other_user_id', $other_user_id);
        try {
            return $req->execute();
        } catch (Exception $e) {
            error_log("DeleteConversation error: " . $e->getMessage());
            return false;
        }
    }
    
    public function EditMessage($id, $content, $sender_id)
    {
        try {
            $db = config::getConnexion();
            $query = $db->prepare('UPDATE messages SET content = :content WHERE id = :id AND sender_id = :sender_id');
            return $query->execute([
                'content' => $content,
                'id' => $id,
                'sender_id' => $sender_id
            ]);
        } catch (Exception $e) {
            error_log("EditMessage error: " . $e->getMessage());
            return false;
        }
    }
    
    public function GetUnreadCount($user_id)
    {
        $db = config::getConnexion();
        try {
            $query = $db->prepare('SELECT COUNT(*) as total FROM messages WHERE receiver_id = :user_id AND is_read = 0');
            $query->execute(['user_id' => $user_id]);
            $result = $query->fetch();
            return $result['total'];
        } catch (Exception $e) {
            error_log("GetUnreadCount error: " . $e->getMessage());
            return 0;
        }
    }
    
    public function GetAllUsers()
    {
        $db = config::getConnexion();
        try {
            $query = $db->prepare('SELECT * FROM users ORDER BY name ASC');
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log("GetAllUsers error: " . $e->getMessage());
            return [];
        }
    }
    
    public function GetUserById($user_id)
    {
        $db = config::getConnexion();
        try {
            $query = $db->prepare('SELECT * FROM users WHERE user_id = :user_id');
            $query->execute(['user_id' => $user_id]);
            return $query->fetch();
        } catch (Exception $e) {
            error_log("GetUserById error: " . $e->getMessage());
            return null;
        }
    }
    
    public function AddUser($user_id, $name, $init, $avatar, $role)
    {
        $sql = "INSERT INTO users (user_id, name, init, avatar, role) 
                VALUES (:user_id, :name, :init, :avatar, :role)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            return $query->execute([
                'user_id' => $user_id,
                'name' => $name,
                'init' => $init,
                'avatar' => $avatar,
                'role' => $role
            ]);
        } catch (Exception $e) {
            error_log("AddUser error: " . $e->getMessage());
            return false;
        }
    }
    
    public function GetAllMessagesAdmin()
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM messages ORDER BY created_at DESC";
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log("GetAllMessagesAdmin error: " . $e->getMessage());
            return [];
        }
    }
    
    public function DeleteMessageAdmin($id)
    {
        $sql = "DELETE FROM messages WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            return $req->execute();
        } catch (Exception $e) {
            error_log("DeleteMessageAdmin error: " . $e->getMessage());
            return false;
        }
    }
    
    public function EditMessageAdmin($id, $content)
    {
        try {
            $db = config::getConnexion();
            $query = $db->prepare('UPDATE messages SET content = :content WHERE id = :id');
            return $query->execute([
                'content' => $content,
                'id' => $id
            ]);
        } catch (Exception $e) {
            error_log("EditMessageAdmin error: " . $e->getMessage());
            return false;
        }
    }
}

// AJAX Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');
    
    $controller = new MessageC();
    $action = $_POST['action'] ?? '';
    $response = ['success' => false];

    switch ($action) {
        case 'get_conversations':
            $conversations = $controller->ListeConversations($_POST['user_id']);
            $response = ['success' => true, 'conversations' => array_values($conversations)];
            break;
            
        case 'get_messages':
            $messages = $controller->ListeMessages($_POST['user_id'], $_POST['other_user_id']);
            $response = ['success' => true, 'messages' => $messages];
            break;
            
        case 'send_message':
            if (empty($_POST['content']) || strlen(trim($_POST['content'])) < 1) {
                $response = ['success' => false, 'error' => 'Message cannot be empty'];
                break;
            }
            $msg = new message(
                $_POST['sender_id'],
                $_POST['receiver_id'],
                $_POST['sender_name'],
                $_POST['receiver_name'],
                $_POST['sender_init'],
                $_POST['receiver_init'],
                trim($_POST['content']),
                $_POST['sender_avatar'] ?? 'av-blue',
                $_POST['receiver_avatar'] ?? 'av-blue'
            );
            $result = $controller->AddMessage($msg);
            $response = ['success' => $result];
            break;
            
        case 'delete_message':
            $result = $controller->DeleteMessage($_POST['message_id'], $_POST['sender_id']);
            $response = ['success' => $result];
            break;
            
        case 'delete_conversation':
            $result = $controller->DeleteConversation($_POST['user_id'], $_POST['other_user_id']);
            $response = ['success' => $result];
            break;
            
        case 'edit_message':
            if (empty($_POST['content']) || strlen(trim($_POST['content'])) < 1) {
                $response = ['success' => false, 'error' => 'Message cannot be empty'];
                break;
            }
            $result = $controller->EditMessage($_POST['message_id'], trim($_POST['content']), $_POST['sender_id']);
            $response = ['success' => $result];
            break;
            
        case 'get_unread_count':
            $count = $controller->GetUnreadCount($_POST['user_id']);
            $response = ['success' => true, 'count' => $count];
            break;
            
        case 'get_all_users':
            $users = $controller->GetAllUsers();
            $response = ['success' => true, 'users' => $users];
            break;
            
        case 'add_user':
            if (empty($_POST['user_id']) || empty($_POST['name']) || empty($_POST['init'])) {
                $response = ['success' => false, 'error' => 'All fields are required'];
                break;
            }
            $result = $controller->AddUser(
                $_POST['user_id'],
                $_POST['name'],
                $_POST['init'],
                $_POST['avatar'],
                $_POST['role']
            );
            $response = ['success' => $result];
            break;
            
        case 'get_user':
            $user = $controller->GetUserById($_POST['user_id']);
            $response = ['success' => true, 'user' => $user];
            break;
            
        case 'get_all_messages_admin':
            $messages = $controller->GetAllMessagesAdmin();
            $response = ['success' => true, 'messages' => $messages];
            break;
            
        case 'delete_message_admin':
            $result = $controller->DeleteMessageAdmin($_POST['message_id']);
            $response = ['success' => $result];
            break;
            
        case 'edit_message_admin':
            if (empty($_POST['content']) || strlen(trim($_POST['content'])) < 1) {
                $response = ['success' => false, 'error' => 'Message cannot be empty'];
                break;
            }
            $result = $controller->EditMessageAdmin($_POST['message_id'], trim($_POST['content']));
            $response = ['success' => $result];
            break;
            
        default:
            $response = ['success' => false, 'error' => 'Unknown action: ' . $action];
            break;
    }

    echo json_encode($response);
    exit;
}
?>
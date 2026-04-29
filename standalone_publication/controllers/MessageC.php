<?php
include_once __DIR__ . '/../config/config.php';
include_once __DIR__ . '/../models/message.php';

class MessageC
{
    // ==================== CONVERSATIONS ====================
    
    public function ListeConversations($user_id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT 
                        DISTINCT other_user_id,
                        other_user_name,
                        other_user_init,
                        other_user_avatar,
                        last_message,
                        last_time,
                        unread_count,
                        p.id as related_publication_id,
                        p.content as related_publication_content,
                        p.image_url as related_publication_image
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
                            m.publication_id,
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
                            m.created_at as msg_created_at
                        FROM messages m
                        WHERE m.sender_id = :user_id OR m.receiver_id = :user_id
                    ) as conv
                    LEFT JOIN publication p ON conv.publication_id = p.id
                    ORDER BY last_time DESC";
            
            $query = $db->prepare($sql);
            $query->execute(['user_id' => $user_id]);
            $results = $query->fetchAll();
            
            return array_filter($results, function($conv) {
                return !empty($conv['other_user_id']);
            });
        } catch (Exception $e) {
            error_log("ListeConversations error: " . $e->getMessage());
            return [];
        }
    }
    
    // ==================== MESSAGES WITH READ SYSTEM ====================
    
    /**
     * Get messages between two users and mark received messages as read
     */
    public function ListeMessages($user_id, $other_user_id)
    {
        $db = config::getConnexion();
        try {
            // Mark all unread messages from the other user as read
            $updateSql = "UPDATE messages SET is_read = 1 
                          WHERE receiver_id = :user_id AND sender_id = :other_user_id AND is_read = 0";
            $updateQuery = $db->prepare($updateSql);
            $updateQuery->execute([
                'user_id' => $user_id,
                'other_user_id' => $other_user_id
            ]);
            
            // Get all messages
            $sql = "SELECT 
                        m.*,
                        p.id as publication_id,
                        p.content as publication_content,
                        p.user_name as publication_author,
                        p.image_url as publication_image,
                        p.likes as publication_likes
                    FROM messages m
                    LEFT JOIN publication p ON m.publication_id = p.id
                    WHERE (m.sender_id = :user_id AND m.receiver_id = :other_user_id)
                       OR (m.sender_id = :other_user_id AND m.receiver_id = :user_id)
                    ORDER BY m.created_at ASC";
            
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
    
    /**
     * Mark specific messages as read
     */
    public function MarkMessagesAsRead($user_id, $other_user_id)
    {
        $db = config::getConnexion();
        try {
            $sql = "UPDATE messages SET is_read = 1 
                    WHERE receiver_id = :user_id AND sender_id = :other_user_id AND is_read = 0";
            $query = $db->prepare($sql);
            return $query->execute([
                'user_id' => $user_id,
                'other_user_id' => $other_user_id
            ]);
        } catch (Exception $e) {
            error_log("MarkMessagesAsRead error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mark single message as read
     */
    public function MarkMessageAsRead($message_id, $receiver_id)
    {
        $db = config::getConnexion();
        try {
            $sql = "UPDATE messages SET is_read = 1 WHERE id = :id AND receiver_id = :receiver_id";
            $query = $db->prepare($sql);
            return $query->execute(['id' => $message_id, 'receiver_id' => $receiver_id]);
        } catch (Exception $e) {
            error_log("MarkMessageAsRead error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get unread messages count for a user
     */
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
    
    /**
     * Get unread messages grouped by sender
     */
    public function GetUnreadMessagesBySender($user_id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT 
                        sender_id, 
                        sender_name, 
                        sender_init, 
                        sender_avatar,
                        COUNT(*) as unread_count,
                        MAX(created_at) as last_message_time
                    FROM messages 
                    WHERE receiver_id = :user_id AND is_read = 0
                    GROUP BY sender_id, sender_name, sender_init, sender_avatar
                    ORDER BY last_message_time DESC";
            $query = $db->prepare($sql);
            $query->execute(['user_id' => $user_id]);
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log("GetUnreadMessagesBySender error: " . $e->getMessage());
            return [];
        }
    }
    
    // ==================== MESSAGES BY PUBLICATION ====================
    
    public function GetMessagesByPublication($publication_id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT 
                        m.*,
                        u.name as user_name,
                        u.init as user_init,
                        u.avatar as user_avatar,
                        p.content as publication_content,
                        p.user_name as publication_author_name
                    FROM messages m
                    INNER JOIN users u ON m.sender_id = u.user_id
                    INNER JOIN publication p ON m.publication_id = p.id
                    WHERE m.publication_id = :publication_id
                    ORDER BY m.created_at DESC";
            
            $query = $db->prepare($sql);
            $query->execute(['publication_id' => $publication_id]);
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log("GetMessagesByPublication error: " . $e->getMessage());
            return [];
        }
    }
    
    public function GetConversationWithPublication($user_id, $other_user_id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT 
                        m.*,
                        p.id as related_post_id,
                        p.content as related_post_content,
                        p.image_url as related_post_image,
                        p.created_at as post_created_at,
                        COUNT(DISTINCT m2.id) as total_messages_in_conversation
                    FROM messages m
                    LEFT JOIN publication p ON m.publication_id = p.id
                    LEFT JOIN messages m2 ON (
                        (m2.sender_id = m.sender_id AND m2.receiver_id = m.receiver_id)
                        OR (m2.sender_id = m.receiver_id AND m2.receiver_id = m.sender_id)
                    )
                    WHERE (m.sender_id = :user_id AND m.receiver_id = :other_user_id)
                       OR (m.sender_id = :other_user_id AND m.receiver_id = :user_id)
                    GROUP BY m.id
                    ORDER BY m.created_at DESC";
            
            $query = $db->prepare($sql);
            $query->execute([
                'user_id' => $user_id,
                'other_user_id' => $other_user_id
            ]);
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log("GetConversationWithPublication error: " . $e->getMessage());
            return [];
        }
    }
    
    // ==================== STATS ====================
    
    public function GetUserMessageStats($user_id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT 
                        u.user_id,
                        u.name,
                        COUNT(DISTINCT m.id) as total_messages_sent,
                        COUNT(DISTINCT CASE WHEN m.receiver_id = :user_id THEN m.sender_id END) as unique_conversations,
                        COUNT(DISTINCT m.publication_id) as publications_discussed,
                        AVG(LENGTH(m.content)) as avg_message_length,
                        SUM(CASE WHEN m.is_read = 0 AND m.receiver_id = :user_id THEN 1 ELSE 0 END) as unread_count
                    FROM users u
                    LEFT JOIN messages m ON u.user_id = m.sender_id OR u.user_id = m.receiver_id
                    WHERE u.user_id = :user_id
                    GROUP BY u.user_id";
            
            $query = $db->prepare($sql);
            $query->execute(['user_id' => $user_id]);
            return $query->fetch();
        } catch (Exception $e) {
            error_log("GetUserMessageStats error: " . $e->getMessage());
            return null;
        }
    }
    
    public function GetPopularPublicationsFromMessages()
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT 
                        p.id,
                        p.content,
                        p.user_name as author,
                        p.likes,
                        COUNT(m.id) as message_count,
                        COUNT(DISTINCT m.sender_id) as unique_senders,
                        MAX(m.created_at) as last_discussed
                    FROM publication p
                    INNER JOIN messages m ON p.id = m.publication_id
                    GROUP BY p.id
                    HAVING message_count > 0
                    ORDER BY message_count DESC, last_discussed DESC
                    LIMIT 10";
            
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log("GetPopularPublicationsFromMessages error: " . $e->getMessage());
            return [];
        }
    }
    
    // ==================== CRUD OPERATIONS ====================
    
    public function AddMessageWithPublication($m, $publication_id = null)
    {
        // Bad words filter
        $bad_words = ['badword1', 'badword2', 'spam', 'scam', 'abuse'];
        $content_lower = strtolower($m->getContent());
        $is_flagged = 0;
        foreach ($bad_words as $word) {
            if (strpos($content_lower, $word) !== false) {
                $is_flagged = 1;
                break;
            }
        }

        $sql = "INSERT INTO messages (sender_id, receiver_id, sender_name, receiver_name, 
                sender_init, receiver_init, sender_avatar, receiver_avatar, publication_id, content, is_read, is_flagged) 
                VALUES (:sender_id, :receiver_id, :sender_name, :receiver_name, 
                :sender_init, :receiver_init, :sender_avatar, :receiver_avatar, :publication_id, :content, 0, :is_flagged)";
        
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
                'publication_id' => $publication_id,
                'content' => $m->getContent(),
                'is_flagged' => $is_flagged
            ]);
        } catch (Exception $e) {
            error_log("AddMessageWithPublication error: " . $e->getMessage());
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
    
    // ==================== USER MANAGEMENT ====================
    
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
    
    // ==================== ADMIN FUNCTIONS ====================
    
    public function GetAllMessagesAdmin()
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT 
                        m.*,
                        p.id as pub_id,
                        p.content as pub_content,
                        p.user_name as pub_author
                    FROM messages m
                    LEFT JOIN publication p ON m.publication_id = p.id
                    ORDER BY m.created_at DESC";
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

    public function UnflagMessage($id)
    {
        try {
            $db = config::getConnexion();
            $query = $db->prepare('UPDATE messages SET is_flagged = 0 WHERE id = :id');
            return $query->execute(['id' => $id]);
        } catch (Exception $e) {
            error_log("UnflagMessage error: " . $e->getMessage());
            return false;
        }
    }
}

// AJAX Handler - skip if the including page has its own handler (e.g., admin pages)
if (!defined('ADMIN_AJAX_HANDLER') && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
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
            
        case 'mark_read':
            $result = $controller->MarkMessagesAsRead($_POST['user_id'], $_POST['other_user_id']);
            $response = ['success' => $result];
            break;
            
        case 'mark_single_read':
            $result = $controller->MarkMessageAsRead($_POST['message_id'], $_POST['receiver_id']);
            $response = ['success' => $result];
            break;
            
        case 'get_unread_by_sender':
            $unreadData = $controller->GetUnreadMessagesBySender($_POST['user_id']);
            $response = ['success' => true, 'unreadData' => $unreadData];
            break;
            
        case 'get_messages_by_publication':
            $messages = $controller->GetMessagesByPublication($_POST['publication_id']);
            $response = ['success' => true, 'messages' => $messages];
            break;
            
        case 'get_conversation_with_publication':
            $conversation = $controller->GetConversationWithPublication($_POST['user_id'], $_POST['other_user_id']);
            $response = ['success' => true, 'conversation' => $conversation];
            break;
            
        case 'get_user_stats':
            $stats = $controller->GetUserMessageStats($_POST['user_id']);
            $response = ['success' => true, 'stats' => $stats];
            break;
            
        case 'get_popular_publications':
            $publications = $controller->GetPopularPublicationsFromMessages();
            $response = ['success' => true, 'publications' => $publications];
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
            $publication_id = $_POST['publication_id'] ?? null;
            $result = $controller->AddMessageWithPublication($msg, $publication_id);
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
            
        case 'unflag_message':
            $result = $controller->UnflagMessage($_POST['message_id']);
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
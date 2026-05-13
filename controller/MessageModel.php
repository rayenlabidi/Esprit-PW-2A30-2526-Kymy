<?php
require_once __DIR__ . '/../config.php';

class MessageModel
{
    public function listeUtilisateursContactables($currentUserId)
    {
        $sql = 'SELECT u.*, r.slug AS role_slug, r.name AS role_name
                FROM utilisateurs u
                INNER JOIN roles r ON u.role_id = r.id
                WHERE u.status = "active" AND u.id != :id
                ORDER BY u.first_name ASC, u.last_name ASC';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => (int) $currentUserId]);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function listeMessages($currentUserId, $otherUserId)
    {
        $sql = 'SELECT *
                FROM messages
                WHERE (sender_id = :current_sender AND receiver_id = :other_receiver)
                   OR (sender_id = :other_sender AND receiver_id = :current_receiver)
                ORDER BY created_at ASC, id ASC';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'current_sender' => (string) $currentUserId,
                'other_receiver' => (string) $otherUserId,
                'other_sender' => (string) $otherUserId,
                'current_receiver' => (string) $currentUserId
            ]);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function addMessage($message)
    {
        $sql = 'INSERT INTO messages
                (sender_id, receiver_id, sender_name, receiver_name, sender_init, receiver_init, sender_avatar, receiver_avatar, publication_id, content, is_read, is_flagged)
                VALUES
                (:sender_id, :receiver_id, :sender_name, :receiver_name, :sender_init, :receiver_init, :sender_avatar, :receiver_avatar, :publication_id, :content, 0, 0)';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'sender_id' => $message->getSenderId(),
                'receiver_id' => $message->getReceiverId(),
                'sender_name' => $message->getSenderName(),
                'receiver_name' => $message->getReceiverName(),
                'sender_init' => $message->getSenderInit(),
                'receiver_init' => $message->getReceiverInit(),
                'sender_avatar' => $message->getSenderAvatar(),
                'receiver_avatar' => $message->getReceiverAvatar(),
                'publication_id' => $message->getPublicationId(),
                'content' => $message->getContent()
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function listeMessagesAdmin()
    {
        $sql = 'SELECT *
                FROM messages
                ORDER BY created_at DESC, id DESC';
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteMessage($id)
    {
        $sql = 'DELETE FROM messages WHERE id = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => (int) $id]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function countMessages()
    {
        $db = config::getConnexion();
        $query = $db->query('SELECT COUNT(*) AS total FROM messages');
        $row = $query->fetch();
        return $row ? (int) $row['total'] : 0;
    }
}
?>

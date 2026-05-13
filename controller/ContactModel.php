<?php
require_once __DIR__ . '/../config.php';

class ContactModel
{
    public function addMessage($fullName, $email, $subject, $message)
    {
        $sql = 'INSERT INTO contact_messages (full_name, email, subject, message)
                VALUES (:full_name, :email, :subject, :message)';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'full_name' => $fullName,
                'email' => $email,
                'subject' => $subject,
                'message' => $message
            ]);
            return (int) $db->lastInsertId();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function listMessages()
    {
        $db = config::getConnexion();
        try {
            $query = $db->query('SELECT * FROM contact_messages ORDER BY created_at DESC, id DESC');
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function updateStatus($id, $status)
    {
        $sql = 'UPDATE contact_messages SET status = :status WHERE id = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'status' => $status,
                'id' => (int) $id
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteMessage($id)
    {
        $sql = 'DELETE FROM contact_messages WHERE id = :id';
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
        $query = $db->query('SELECT COUNT(*) AS total FROM contact_messages');
        $row = $query->fetch();
        return $row ? (int) $row['total'] : 0;
    }
}
?>

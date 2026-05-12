<?php
/*
 * Modele d'authentification.
 * Il contient les requetes SQL pour login et reinitialisation du mot de passe.
 */
include_once __DIR__ . "/../config.php";

class AuthC
{
    private function ensurePasswordResetTable($db)
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token_hash VARCHAR(255) NOT NULL,
                expires_at DATETIME NOT NULL,
                used_at DATETIME NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_password_resets_token_hash (token_hash),
                INDEX idx_password_resets_user_id (user_id),
                CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES utilisateurs(id)
                    ON UPDATE CASCADE ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function login($email, $password)
    {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("SELECT u.*, r.name AS role_name FROM utilisateurs u LEFT JOIN roles r ON r.id = u.role_id WHERE u.email = :email");
            $query->execute(['email' => $email]);
            $user = $query->fetch();

            if ($user && password_verify($password, $user['password'])) {
                return $user; // Succès
            }
            return false; // Échec
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function createPasswordResetToken($email)
    {
        $db = config::getConnexion();
        try {
            $this->ensurePasswordResetTable($db);

            $query = $db->prepare("SELECT id, email, first_name FROM utilisateurs WHERE email = :email LIMIT 1");
            $query->execute(['email' => trim((string)$email)]);
            $user = $query->fetch();

            if (!$user) {
                return null;
            }

            $db->prepare("UPDATE password_resets SET used_at = NOW() WHERE user_id = :user_id AND used_at IS NULL")
                ->execute(['user_id' => $user['id']]);

            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);

            $insert = $db->prepare("
                INSERT INTO password_resets (user_id, token_hash, expires_at)
                VALUES (:user_id, :token_hash, DATE_ADD(NOW(), INTERVAL 1 HOUR))
            ");
            $insert->execute([
                'user_id' => $user['id'],
                'token_hash' => $tokenHash
            ]);

            return [
                'token' => $token,
                'email' => $user['email'],
                'first_name' => $user['first_name']
            ];
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getPasswordResetByToken($token)
    {
        $db = config::getConnexion();
        try {
            $this->ensurePasswordResetTable($db);

            $token = trim((string)$token);
            if ($token === '') {
                return false;
            }

            $query = $db->prepare("
                SELECT pr.*, u.email, u.first_name
                FROM password_resets pr
                INNER JOIN utilisateurs u ON u.id = pr.user_id
                WHERE pr.token_hash = :token_hash
                  AND pr.used_at IS NULL
                  AND pr.expires_at >= NOW()
                LIMIT 1
            ");
            $query->execute(['token_hash' => hash('sha256', $token)]);

            return $query->fetch() ?: false;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function resetPassword($token, $password)
    {
        $db = config::getConnexion();
        try {
            $reset = $this->getPasswordResetByToken($token);
            if (!$reset) {
                return false;
            }

            $db->beginTransaction();

            $updatePassword = $db->prepare("UPDATE utilisateurs SET password = :password WHERE id = :user_id");
            $updatePassword->execute([
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'user_id' => $reset['user_id']
            ]);

            $markUsed = $db->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = :id");
            $markUsed->execute(['id' => $reset['id']]);

            $db->commit();

            return true;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>

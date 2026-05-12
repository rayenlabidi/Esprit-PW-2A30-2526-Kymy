<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/utilisateur.php';

class UtilisateurModel
{
    public function listeUtilisateurs($search = '', $role = '', $status = '')
    {
        $sql = 'SELECT u.*, r.name AS role_name, r.slug AS role_slug
                FROM utilisateurs u
                INNER JOIN roles r ON u.role_id = r.id
                WHERE 1 = 1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search OR u.headline LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        if ($role !== '') {
            $sql .= ' AND r.slug = :role';
            $params['role'] = $role;
        }

        if ($status !== '') {
            $sql .= ' AND u.status = :status';
            $params['status'] = $status;
        }

        $sql .= ' ORDER BY u.created_at DESC, u.id DESC';

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function listeRoles()
    {
        $db = config::getConnexion();
        try {
            $query = $db->query('SELECT * FROM roles ORDER BY id ASC');
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getUtilisateurById($id)
    {
        $sql = 'SELECT u.*, r.name AS role_name, r.slug AS role_slug
                FROM utilisateurs u
                INNER JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function emailExiste($email, $ignoreId = 0)
    {
        $sql = 'SELECT COUNT(*) AS total FROM utilisateurs WHERE email = :email';
        $params = ['email' => $email];

        if ((int) $ignoreId > 0) {
            $sql .= ' AND id != :id';
            $params['id'] = (int) $ignoreId;
        }

        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute($params);
        $row = $query->fetch();
        return $row && (int) $row['total'] > 0;
    }

    public function addUtilisateur($utilisateur)
    {
        $sql = 'INSERT INTO utilisateurs
                (role_id, first_name, last_name, email, phone, password, headline, bio, status)
                VALUES
                (:role_id, :first_name, :last_name, :email, :phone, :password, :headline, :bio, :status)';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'role_id' => $utilisateur->getRoleId(),
                'first_name' => $utilisateur->getFirstName(),
                'last_name' => $utilisateur->getLastName(),
                'email' => $utilisateur->getEmail(),
                'phone' => $utilisateur->getPhone(),
                'password' => password_hash($utilisateur->getPassword(), PASSWORD_DEFAULT),
                'headline' => $utilisateur->getHeadline(),
                'bio' => $utilisateur->getBio(),
                'status' => $utilisateur->getStatus()
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function updateUtilisateur($utilisateur, $id)
    {
        $params = [
            'role_id' => $utilisateur->getRoleId(),
            'first_name' => $utilisateur->getFirstName(),
            'last_name' => $utilisateur->getLastName(),
            'email' => $utilisateur->getEmail(),
            'phone' => $utilisateur->getPhone(),
            'headline' => $utilisateur->getHeadline(),
            'bio' => $utilisateur->getBio(),
            'status' => $utilisateur->getStatus(),
            'id' => $id
        ];

        $passwordSql = '';
        if ($utilisateur->getPassword() !== '') {
            $passwordSql = ', password = :password';
            $params['password'] = password_hash($utilisateur->getPassword(), PASSWORD_DEFAULT);
        }

        $sql = 'UPDATE utilisateurs SET
                    role_id = :role_id,
                    first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    phone = :phone,
                    headline = :headline,
                    bio = :bio,
                    status = :status' . $passwordSql . '
                WHERE id = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteUtilisateur($id)
    {
        $sql = 'DELETE FROM utilisateurs WHERE id = :id';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function statistiquesUtilisateurs()
    {
        $sql = "SELECT
                    COUNT(*) AS total,
                    IFNULL(SUM(CASE WHEN r.slug = 'admin' THEN 1 ELSE 0 END), 0) AS admins,
                    IFNULL(SUM(CASE WHEN r.slug = 'freelancer' THEN 1 ELSE 0 END), 0) AS freelancers,
                    IFNULL(SUM(CASE WHEN r.slug = 'boss' THEN 1 ELSE 0 END), 0) AS boss
                FROM utilisateurs u
                INNER JOIN roles r ON u.role_id = r.id";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $stats = $query->fetch();
            return $stats ? $stats : ['total' => 0, 'admins' => 0, 'freelancers' => 0, 'boss' => 0];
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function countUtilisateurs()
    {
        $db = config::getConnexion();
        $query = $db->query('SELECT COUNT(*) AS total FROM utilisateurs');
        $row = $query->fetch();
        return $row ? (int) $row['total'] : 0;
    }
}
?>

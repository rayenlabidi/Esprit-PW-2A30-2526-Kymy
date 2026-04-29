<?php
include_once __DIR__ . "/../config.php";

class UtilisateurC
{
    public function ListeUtilisateurs($search = '', $sort = 'date_desc')
    {
        $db = config::getConnexion();
        try {
            $query = 'SELECT u.*, r.name AS role_name FROM utilisateurs u LEFT JOIN roles r ON r.id = u.role_id';
            $params = [];

            if (!empty($search)) {
                $query .= ' WHERE u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search OR u.id LIKE :search';
                $params['search'] = '%' . $search . '%';
            }

            switch ($sort) {
                case 'name_asc':
                    $query .= ' ORDER BY u.first_name ASC, u.last_name ASC';
                    break;
                case 'name_desc':
                    $query .= ' ORDER BY u.first_name DESC, u.last_name DESC';
                    break;
                case 'date_asc':
                    $query .= ' ORDER BY u.created_at ASC';
                    break;
                case 'role':
                    $query .= ' ORDER BY r.name ASC, u.first_name ASC';
                    break;
                case 'date_desc':
                default:
                    $query .= ' ORDER BY u.created_at DESC';
                    break;
            }

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            // On retourne un tableau associatif plutôt que le PDOStatement pour faciliter la manipulation
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function RecupererUtilisateur($id)
    {
        $sql = "SELECT * FROM utilisateurs WHERE id= :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function ListeRoles()
    {
        $db = config::getConnexion();
        try {
            $liste = $db->query('SELECT * FROM roles ORDER BY id');
            return $liste;
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function AddUtilisateur($u)
    {
        $sql = "INSERT INTO utilisateurs (role_id, first_name, last_name, email, phone, password, headline, bio, status) 
                VALUES (:role_id, :first_name, :last_name, :email, :phone, :password, :headline, :bio, :status)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'role_id'    => $u->getRoleId(),
                'first_name' => $u->getFirstName(),
                'last_name'  => $u->getLastName(),
                'email'      => $u->getEmail(),
                'phone'      => $u->getPhone(),
                'password'   => password_hash($u->getPassword(), PASSWORD_DEFAULT),
                'headline'   => $u->getHeadline(),
                'bio'        => $u->getBio(),
                'status'     => $u->getStatus()
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }

    public function DeleteUtilisateur($id)
    {
        $sql = "DELETE FROM utilisateurs WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
            return true;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function UpdateUtilisateur($u, $id)
    {
        try {
            $db = config::getConnexion();
            
            if (empty($u->getPassword())) {
                $query = $db->prepare(
                    'UPDATE utilisateurs SET 
                        role_id = :role_id,
                        first_name = :first_name, 
                        last_name = :last_name, 
                        email = :email,
                        phone = :phone,
                        headline = :headline,
                        bio = :bio,
                        status = :status
                    WHERE id = :id'
                );
                $query->execute([
                    'role_id'    => $u->getRoleId(),
                    'first_name' => $u->getFirstName(),
                    'last_name'  => $u->getLastName(),
                    'email'      => $u->getEmail(),
                    'phone'      => $u->getPhone(),
                    'headline'   => $u->getHeadline(),
                    'bio'        => $u->getBio(),
                    'status'     => $u->getStatus(),
                    'id'         => $id
                ]);
                return true;
            } else {
                $query = $db->prepare(
                    'UPDATE utilisateurs SET 
                        role_id = :role_id,
                        first_name = :first_name, 
                        last_name = :last_name, 
                        email = :email,
                        phone = :phone,
                        password = :password,
                        headline = :headline,
                        bio = :bio,
                        status = :status
                    WHERE id = :id'
                );
                $query->execute([
                    'role_id'    => $u->getRoleId(),
                    'first_name' => $u->getFirstName(),
                    'last_name'  => $u->getLastName(),
                    'email'      => $u->getEmail(),
                    'phone'      => $u->getPhone(),
                    'password'   => password_hash($u->getPassword(), PASSWORD_DEFAULT),
                    'headline'   => $u->getHeadline(),
                    'bio'        => $u->getBio(),
                    'status'     => $u->getStatus(),
                    'id'         => $id
                ]);
                return true;
            }
        } catch (Exception $e) {
            echo "Erreur: " . $e->getMessage();
            return false;
        }
    }

    public function GetStats()
    {
        $db = config::getConnexion();
        try {
            $totalUsers = (int)$db->query('SELECT COUNT(*) AS c FROM utilisateurs')->fetchColumn();

            $byRoleStmt = $db->query(
                'SELECT r.name AS role_name, COUNT(*) AS c
                 FROM utilisateurs u
                 INNER JOIN roles r ON r.id = u.role_id
                 GROUP BY r.id, r.name
                 ORDER BY c DESC, r.name ASC'
            );
            $byRole = $byRoleStmt->fetchAll(PDO::FETCH_ASSOC);

            $byStatusStmt = $db->query(
                'SELECT u.status AS status, COUNT(*) AS c
                 FROM utilisateurs u
                 GROUP BY u.status
                 ORDER BY c DESC, u.status ASC'
            );
            $byStatus = $byStatusStmt->fetchAll(PDO::FETCH_ASSOC);

            $createdLast7 = (int)$db->query(
                "SELECT COUNT(*) AS c FROM utilisateurs WHERE created_at >= (NOW() - INTERVAL 7 DAY)"
            )->fetchColumn();

            $createdLast30 = (int)$db->query(
                "SELECT COUNT(*) AS c FROM utilisateurs WHERE created_at >= (NOW() - INTERVAL 30 DAY)"
            )->fetchColumn();

            $latestStmt = $db->query(
                'SELECT u.id, u.first_name, u.last_name, u.email, u.created_at, r.name AS role_name, u.status
                 FROM utilisateurs u
                 LEFT JOIN roles r ON r.id = u.role_id
                 ORDER BY u.created_at DESC
                 LIMIT 5'
            );
            $latestUsers = $latestStmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'totalUsers' => $totalUsers,
                'byRole' => $byRole,
                'byStatus' => $byStatus,
                'createdLast7' => $createdLast7,
                'createdLast30' => $createdLast30,
                'latestUsers' => $latestUsers
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public function UpdateAvatar($id, $avatarFilename)
    {
        $sql = "UPDATE utilisateurs SET avatar_url = :avatar_url WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'avatar_url' => $avatarFilename,
                'id' => $id
            ]);
        } catch (Exception $e) {
            echo "Erreur: " . $e->getMessage();
        }
    }
}
?>

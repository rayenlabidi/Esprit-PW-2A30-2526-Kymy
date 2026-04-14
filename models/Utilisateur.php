<?php
class Utilisateur {
    private $conn;
    private $table_name = "utilisateurs";

    public $id;
    public $nom;
    public $prenom;
    public $email;
    public $mot_de_passe;
    public $id_role;
    public $role_nom;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT u.id, u.nom, u.prenom, u.email, u.date_inscription, r.nom_role as role_nom 
                  FROM " . $this->table_name . " u
                  LEFT JOIN roles r ON u.id_role = r.id
                  ORDER BY u.date_inscription DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT id, nom, prenom, email, id_role FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->nom = $row['nom'];
            $this->prenom = $row['prenom'];
            $this->email = $row['email'];
            $this->id_role = $row['id_role'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nom=:nom, prenom=:prenom, email=:email, mot_de_passe=:mot_de_passe, id_role=:id_role";
        $stmt = $this->conn->prepare($query);

        $this->nom=htmlspecialchars(strip_tags($this->nom));
        $this->prenom=htmlspecialchars(strip_tags($this->prenom));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->id_role=htmlspecialchars(strip_tags($this->id_role));
        
        // Basic hash for demonstration
        $hashed_password = hash('sha256', $this->mot_de_passe);

        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":prenom", $this->prenom);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":mot_de_passe", $hashed_password);
        $stmt->bindParam(":id_role", $this->id_role);

        if($stmt->execute()) return true;
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nom=:nom, prenom=:prenom, email=:email, id_role=:id_role WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->nom=htmlspecialchars(strip_tags($this->nom));
        $this->prenom=htmlspecialchars(strip_tags($this->prenom));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->id_role=htmlspecialchars(strip_tags($this->id_role));
        $this->id=htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":prenom", $this->prenom);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id_role", $this->id_role);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) return true;
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) return true;
        return false;
    }
    
    public function login() {
        $query = "SELECT id, nom, prenom, mot_de_passe FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $hashed_password = hash('sha256', $this->mot_de_passe);
            if ($hashed_password === $row['mot_de_passe']) {
                $this->id = $row['id'];
                $this->nom = $row['nom'];
                $this->prenom = $row['prenom'];
                return true;
            }
        }
        return false;
    }
}
?>

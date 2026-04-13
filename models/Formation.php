<?php
class Formation {
    private $conn;
    private $table_name = "formations";

    public $id;
    public $titre;
    public $description;
    public $prix;
    public $duree;
    public $id_categorie;
    public $date_creation;
    public $categorie_nom;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT f.id, f.titre, f.description, f.prix, f.duree, f.date_creation, c.nom as categorie_nom 
                  FROM " . $this->table_name . " f
                  LEFT JOIN categories c ON f.id_categorie = c.id
                  ORDER BY f.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT id, titre, description, prix, duree, id_categorie FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->titre = $row['titre'];
            $this->description = $row['description'];
            $this->prix = $row['prix'];
            $this->duree = $row['duree'];
            $this->id_categorie = $row['id_categorie'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET titre=:titre, description=:description, prix=:prix, duree=:duree, id_categorie=:id_categorie";
        $stmt = $this->conn->prepare($query);

        $this->titre=htmlspecialchars(strip_tags($this->titre));
        $this->description=htmlspecialchars(strip_tags($this->description));
        $this->prix=htmlspecialchars(strip_tags($this->prix));
        $this->duree=htmlspecialchars(strip_tags($this->duree));
        $this->id_categorie=htmlspecialchars(strip_tags($this->id_categorie));

        $stmt->bindParam(":titre", $this->titre);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":prix", $this->prix);
        $stmt->bindParam(":duree", $this->duree);
        $stmt->bindParam(":id_categorie", $this->id_categorie);

        if($stmt->execute()) return true;
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET titre=:titre, description=:description, prix=:prix, duree=:duree, id_categorie=:id_categorie WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->titre=htmlspecialchars(strip_tags($this->titre));
        $this->description=htmlspecialchars(strip_tags($this->description));
        $this->prix=htmlspecialchars(strip_tags($this->prix));
        $this->duree=htmlspecialchars(strip_tags($this->duree));
        $this->id_categorie=htmlspecialchars(strip_tags($this->id_categorie));
        $this->id=htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":titre", $this->titre);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":prix", $this->prix);
        $stmt->bindParam(":duree", $this->duree);
        $stmt->bindParam(":id_categorie", $this->id_categorie);
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
}
?>

<?php
class Role {
    private $conn;
    private $table_name = "roles";

    public $id;
    public $nom_role;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT id, nom_role FROM " . $this->table_name . " ORDER BY nom_role ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>

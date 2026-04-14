<?php
require_once 'config/database.php';
require_once 'models/Utilisateur.php';
require_once 'models/Role.php';

class UtilisateurController {
    private $db;
    private $utilisateur;
    private $role;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->utilisateur = new Utilisateur($this->db);
        $this->role = new Role($this->db);
    }

    public function index() {
        $stmt = $this->utilisateur->read();
        $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require 'views/utilisateurs/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->utilisateur->nom = $_POST['nom'];
            $this->utilisateur->prenom = $_POST['prenom'];
            $this->utilisateur->email = $_POST['email'];
            $this->utilisateur->mot_de_passe = $_POST['mot_de_passe'];
            $this->utilisateur->id_role = $_POST['id_role'] ?: null;

            if($this->utilisateur->create()) {
                header("Location: index.php?controller=utilisateur&action=index");
                exit;
            }
        }
        
        $role_stmt = $this->role->read();
        $roles = $role_stmt->fetchAll(PDO::FETCH_ASSOC);
        require 'views/utilisateurs/create.php';
    }

    public function edit() {
        $this->utilisateur->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->utilisateur->nom = $_POST['nom'];
            $this->utilisateur->prenom = $_POST['prenom'];
            $this->utilisateur->email = $_POST['email'];
            $this->utilisateur->id_role = $_POST['id_role'] ?: null;
            // Note: Password update is usually handled separately for security

            if($this->utilisateur->update()) {
                header("Location: index.php?controller=utilisateur&action=index");
                exit;
            }
        }

        $this->utilisateur->readOne();
        $role_stmt = $this->role->read();
        $roles = $role_stmt->fetchAll(PDO::FETCH_ASSOC);
        require 'views/utilisateurs/edit.php';
    }

    public function delete() {
        $this->utilisateur->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
        if($this->utilisateur->delete()) {
            header("Location: index.php?controller=utilisateur&action=index");
            exit;
        }
    }
}
?>

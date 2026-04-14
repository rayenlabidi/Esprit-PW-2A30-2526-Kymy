<?php
require_once 'config/database.php';
require_once 'models/Utilisateur.php';

class AuthController {
    private $db;
    private $utilisateur;

    public function __construct() {
        // Just in case it's not started somewhere else
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $database = new Database();
        $this->db = $database->getConnection();
        $this->utilisateur = new Utilisateur($this->db);
    }
    
    public function register() {
        $error = "";
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->utilisateur->nom = $_POST['nom'];
            $this->utilisateur->prenom = $_POST['prenom'];
            $this->utilisateur->email = $_POST['email'];
            $this->utilisateur->mot_de_passe = $_POST['password'];
            $this->utilisateur->id_role = 3; // Etudiant by default

            if($this->utilisateur->create()) {
                header("Location: index.php?controller=auth&action=login");
                exit;
            } else {
                $error = "Erreur lors de la création du compte.";
            }
        }
        
        require 'views/auth/register.php';
    }

    public function login() {
        $error = "";
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->utilisateur->email = $_POST['email'];
            $this->utilisateur->mot_de_passe = $_POST['password'];

            if($this->utilisateur->login()) {
                $_SESSION['user_id'] = $this->utilisateur->id;
                $_SESSION['user_name'] = $this->utilisateur->prenom . ' ' . $this->utilisateur->nom;
                
                header("Location: index.php");
                exit;
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        }
        
        require 'views/auth/login.php';
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: index.php?controller=auth&action=login");
        exit;
    }
}
?>

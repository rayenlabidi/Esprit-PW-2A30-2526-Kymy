<?php
require_once 'config/database.php';
require_once 'models/Formation.php';
require_once 'models/Category.php';

class FormationController {
    private $db;
    private $formation;
    private $category;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->formation = new Formation($this->db);
        $this->category = new Category($this->db);
    }

    public function index() {
        $stmt = $this->formation->read();
        $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require 'views/formations/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->formation->titre = $_POST['titre'];
            $this->formation->description = $_POST['description'];
            $this->formation->prix = $_POST['prix'];
            $this->formation->duree = $_POST['duree'];
            $this->formation->id_categorie = $_POST['id_categorie'] ?: null;

            if($this->formation->create()) {
                header("Location: index.php");
                exit;
            }
        }
        
        $cat_stmt = $this->category->read();
        $categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
        require 'views/formations/create.php';
    }

    public function edit() {
        $this->formation->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->formation->titre = $_POST['titre'];
            $this->formation->description = $_POST['description'];
            $this->formation->prix = $_POST['prix'];
            $this->formation->duree = $_POST['duree'];
            $this->formation->id_categorie = $_POST['id_categorie'] ?: null;

            if($this->formation->update()) {
                header("Location: index.php");
                exit;
            }
        }

        $this->formation->readOne();
        $cat_stmt = $this->category->read();
        $categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
        require 'views/formations/edit.php';
    }

    public function delete() {
        $this->formation->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
        if($this->formation->delete()) {
            header("Location: index.php");
            exit;
        }
    }
}
?>

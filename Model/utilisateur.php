<?php
/*
 * Modele utilisateur.
 * Cette classe represente les donnees d'un utilisateur dans l'application MVC.
 */
class utilisateur
{
    // Proprietes correspondant aux champs principaux de la table utilisateurs.
    private $id;
    private $role_id;
    private $first_name;
    private $last_name;
    private $email;
    private $phone;
    private $password;
    private $headline;
    private $bio;
    private $status;

    // Constructeur appele quand on cree ou modifie un utilisateur.
    public function __construct(int $role_id, string $first_name, string $last_name, string $email, string $phone, string $password, string $headline, string $bio, string $status)
    {
        $this->role_id = $role_id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->phone = $phone;
        $this->password = $password;
        $this->headline = $headline;
        $this->bio = $bio;
        $this->status = $status;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getRoleId() { return $this->role_id; }
    public function setRoleId($role_id) { $this->role_id = $role_id; }

    public function getFirstName() { return $this->first_name; }
    public function setFirstName($first_name) { $this->first_name = $first_name; }

    public function getLastName() { return $this->last_name; }
    public function setLastName($last_name) { $this->last_name = $last_name; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getPhone() { return $this->phone; }
    public function setPhone($phone) { $this->phone = $phone; }

    public function getPassword() { return $this->password; }
    public function setPassword($password) { $this->password = $password; }

    public function getHeadline() { return $this->headline; }
    public function setHeadline($headline) { $this->headline = $headline; }

    public function getBio() { return $this->bio; }
    public function setBio($bio) { $this->bio = $bio; }

    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }
}
?>

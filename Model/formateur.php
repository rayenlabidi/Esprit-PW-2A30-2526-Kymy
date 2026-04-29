<?php
class formateur
{
    private $idFormateur;
    private $nom;
    private $email;
    private $specialite;

    public function __construct(string $nom, string $email, string $specialite)
    {
        $this->nom = $nom;
        $this->email = $email;
        $this->specialite = $specialite;
    }

    public function getIdFormateur()
    {
        return $this->idFormateur;
    }

    public function setIdFormateur($idFormateur)
    {
        $this->idFormateur = $idFormateur;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getSpecialite()
    {
        return $this->specialite;
    }
}
?>

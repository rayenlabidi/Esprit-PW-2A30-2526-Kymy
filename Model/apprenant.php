<?php
class apprenant
{
    private $idApprenant;
    private $nom;
    private $email;
    private $telephone;

    public function __construct(string $nom, string $email, string $telephone)
    {
        $this->nom = $nom;
        $this->email = $email;
        $this->telephone = $telephone;
    }

    public function getIdApprenant()
    {
        return $this->idApprenant;
    }

    public function setIdApprenant($idApprenant)
    {
        $this->idApprenant = $idApprenant;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }
}
?>

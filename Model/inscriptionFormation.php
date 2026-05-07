<?php
class inscriptionFormation
{
    private $idApprenant;
    private $idFormation;
    private $statut;

    public function __construct(int $idApprenant, int $idFormation, string $statut)
    {
        $this->idApprenant = $idApprenant;
        $this->idFormation = $idFormation;
        $this->statut = $statut;
    }

    public function getIdApprenant()
    {
        return $this->idApprenant;
    }

    public function setIdApprenant($idApprenant)
    {
        $this->idApprenant = $idApprenant;
    }

    public function getIdFormation()
    {
        return $this->idFormation;
    }

    public function setIdFormation($idFormation)
    {
        $this->idFormation = $idFormation;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;
    }
}
?>

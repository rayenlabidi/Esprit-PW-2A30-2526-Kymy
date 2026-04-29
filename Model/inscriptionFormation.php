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

    public function getIdFormation()
    {
        return $this->idFormation;
    }

    public function getStatut()
    {
        return $this->statut;
    }
}
?>

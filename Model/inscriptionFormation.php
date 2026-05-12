<?php
class inscriptionFormation
{
    private $userId;
    private $idFormation;
    private $statut;

    public function __construct(int $userId, int $idFormation, string $statut)
    {
        $this->userId = $userId;
        $this->idFormation = $idFormation;
        $this->statut = $statut;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getIdApprenant()
    {
        return $this->getUserId();
    }

    public function setIdApprenant($idApprenant)
    {
        $this->setUserId($idApprenant);
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

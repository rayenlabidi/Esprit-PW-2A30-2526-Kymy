<?php
class categorieFormation
{
    private $idCategorie;
    private $nomCategorie;
    private $descriptionCategorie;

    public function __construct(string $nomCategorie, string $descriptionCategorie)
    {
        $this->nomCategorie = $nomCategorie;
        $this->descriptionCategorie = $descriptionCategorie;
    }

    public function getIdCategorie()
    {
        return $this->idCategorie;
    }

    public function setIdCategorie($idCategorie)
    {
        $this->idCategorie = $idCategorie;
    }

    public function getNomCategorie()
    {
        return $this->nomCategorie;
    }

    public function getDescriptionCategorie()
    {
        return $this->descriptionCategorie;
    }
}
?>

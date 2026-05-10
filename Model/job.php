<?php
class job
{
    private $id;
    private $titre;
    private $description;
    private $budget;
    private $idCategorie;
    private $localisation;
    private $remote;
    private $type;
    private $statut;
    private $idPublisher;

    public function __construct($titre, $description, $budget, $idCategorie, $localisation, $remote, $type, $statut, $idPublisher)
    {
        $this->titre = $titre;
        $this->description = $description;
        $this->budget = $budget;
        $this->idCategorie = $idCategorie;
        $this->localisation = $localisation;
        $this->remote = $remote;
        $this->type = $type;
        $this->statut = $statut;
        $this->idPublisher = $idPublisher;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getBudget()
    {
        return $this->budget;
    }

    public function setBudget($budget)
    {
        $this->budget = $budget;
    }

    public function getIdCategorie()
    {
        return $this->idCategorie;
    }

    public function setIdCategorie($idCategorie)
    {
        $this->idCategorie = $idCategorie;
    }

    public function getLocalisation()
    {
        return $this->localisation;
    }

    public function setLocalisation($localisation)
    {
        $this->localisation = $localisation;
    }

    public function getRemote()
    {
        return $this->remote;
    }

    public function setRemote($remote)
    {
        $this->remote = $remote;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    public function getIdPublisher()
    {
        return $this->idPublisher;
    }

    public function setIdPublisher($idPublisher)
    {
        $this->idPublisher = $idPublisher;
    }
}
?>

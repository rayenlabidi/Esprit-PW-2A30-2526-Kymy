<?php
class formation
{
    private $id;
    private $titre;
    private $description;
    private $dateDebut;
    private $dateFin;
    private $duree;
    private $prix;
    private $niveau;
    private $statut;
    private $mode;
    private $places;
    private $idCategorie;
    private $idFormateur;

    public function __construct(string $titre, string $description, string $dateDebut, string $dateFin, int $duree, float $prix, string $niveau, string $statut, string $mode, int $places, int $idCategorie, int $idFormateur)
    {
        $this->titre = $titre;
        $this->description = $description;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->duree = $duree;
        $this->prix = $prix;
        $this->niveau = $niveau;
        $this->statut = $statut;
        $this->mode = $mode;
        $this->places = $places;
        $this->idCategorie = $idCategorie;
        $this->idFormateur = $idFormateur;
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

    public function getDescription()
    {
        return $this->description;
    }

    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    public function getDateFin()
    {
        return $this->dateFin;
    }

    public function getDuree()
    {
        return $this->duree;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    public function getNiveau()
    {
        return $this->niveau;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function getPlaces()
    {
        return $this->places;
    }

    public function getIdCategorie()
    {
        return $this->idCategorie;
    }

    public function getIdFormateur()
    {
        return $this->idFormateur;
    }
}
?>

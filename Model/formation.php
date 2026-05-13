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
    private $imageUrl;

    public function __construct(string $titre, string $description, string $dateDebut, string $dateFin, int $duree, float $prix, string $niveau, string $statut, string $mode, int $places, int $idCategorie, int $idFormateur, string $imageUrl = '')
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
        $this->imageUrl = $imageUrl;
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

    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;
    }

    public function getDateFin()
    {
        return $this->dateFin;
    }

    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;
    }

    public function getDuree()
    {
        return $this->duree;
    }

    public function setDuree($duree)
    {
        $this->duree = $duree;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    public function setPrix($prix)
    {
        $this->prix = $prix;
    }

    public function getNiveau()
    {
        return $this->niveau;
    }

    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function getPlaces()
    {
        return $this->places;
    }

    public function setPlaces($places)
    {
        $this->places = $places;
    }

    public function getIdCategorie()
    {
        return $this->idCategorie;
    }

    public function setIdCategorie($idCategorie)
    {
        $this->idCategorie = $idCategorie;
    }

    public function getIdFormateur()
    {
        return $this->idFormateur;
    }

    public function setIdFormateur($idFormateur)
    {
        $this->idFormateur = $idFormateur;
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }
}
?>

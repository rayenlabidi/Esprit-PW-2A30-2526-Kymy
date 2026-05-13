<?php
class candidature
{
    private $id;
    private $idUtilisateur;
    private $idJob;
    private $message;
    private $cvUrl;
    private $photoUrl;
    private $statut;

    public function __construct($idUtilisateur, $idJob, $message, $cvUrl = null, $photoUrl = null, $statut = 'pending')
    {
        $this->idUtilisateur = $idUtilisateur;
        $this->idJob = $idJob;
        $this->message = $message;
        $this->cvUrl = $cvUrl;
        $this->photoUrl = $photoUrl;
        $this->statut = $statut;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur($idUtilisateur)
    {
        $this->idUtilisateur = $idUtilisateur;
    }

    public function getIdJob()
    {
        return $this->idJob;
    }

    public function setIdJob($idJob)
    {
        $this->idJob = $idJob;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getCvUrl()
    {
        return $this->cvUrl;
    }

    public function setCvUrl($cvUrl)
    {
        $this->cvUrl = $cvUrl;
    }

    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl($photoUrl)
    {
        $this->photoUrl = $photoUrl;
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

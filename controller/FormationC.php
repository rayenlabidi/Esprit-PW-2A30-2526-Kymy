<?php
require_once __DIR__ . '/../Model/formation.php';
require_once __DIR__ . '/../Model/apprenant.php';
require_once __DIR__ . '/../Model/inscriptionFormation.php';
require_once __DIR__ . '/../Model/FormationModel.php';
require_once __DIR__ . '/../Model/CategorieFormationModel.php';
require_once __DIR__ . '/../Model/FormateurModel.php';
require_once __DIR__ . '/../Model/ApprenantModel.php';
require_once __DIR__ . '/../Model/InscriptionFormationModel.php';

class FormationC
{
    private $formationModel;
    private $categorieModel;
    private $formateurModel;
    private $apprenantModel;
    private $inscriptionModel;

    public function __construct()
    {
        $this->formationModel = new FormationModel();
        $this->categorieModel = new CategorieFormationModel();
        $this->formateurModel = new FormateurModel();
        $this->apprenantModel = new ApprenantModel();
        $this->inscriptionModel = new InscriptionFormationModel();
    }

    public function handleRequest()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';

        if ($action === 'add') {
            $this->ajouter();
        } elseif ($action === 'edit') {
            $this->modifier();
        } elseif ($action === 'delete') {
            $this->supprimer();
        } elseif ($action === 'detail') {
            $this->detail();
        } elseif ($action === 'enroll') {
            $this->inscrire();
        } elseif ($action === 'inscriptions') {
            $this->listeInscriptions();
        } else {
            $this->liste();
        }
    }

    private function office()
    {
        return (isset($_GET['office']) && $_GET['office'] === 'back') ? 'back' : 'front';
    }

    private function liste()
    {
        $office = $this->office();
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $idCategorie = isset($_GET['id_categorie']) ? trim($_GET['id_categorie']) : '';
        $statut = isset($_GET['statut']) ? trim($_GET['statut']) : '';
        $niveau = isset($_GET['niveau']) ? trim($_GET['niveau']) : '';

        $liste = $this->formationModel->listeFormations($search, $idCategorie, $statut, $niveau);
        $categories = $this->categorieModel->listeCategories();
        $statistiques = $this->formationModel->statistiquesFormations();

        include __DIR__ . '/../view/formations/list.php';
    }

    private function ajouter()
    {
        $office = $this->office();
        $categories = $this->categorieModel->listeCategories();
        $formateurs = $this->formateurModel->listeFormateurs();
        $errors = [];
        $formData = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = $_POST;
            $errors = $this->validerFormation($_POST);

            if (empty($errors)) {
                $formation = $this->construireFormation($_POST);
                $this->formationModel->addFormation($formation);
                header('Location: FormationC.php?office=' . $office . '&action=list');
                exit;
            }
        }

        include __DIR__ . '/../view/formations/form.php';
    }

    private function modifier()
    {
        $office = $this->office();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $formation = $this->formationModel->getFormationById($id);

        if (!$formation) {
            die('Formation introuvable.');
        }

        $categories = $this->categorieModel->listeCategories();
        $formateurs = $this->formateurModel->listeFormateurs();
        $errors = [];
        $formData = $formation;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = $_POST;
            $formData['id_formation'] = $id;
            $errors = $this->validerFormation($_POST);

            if (empty($errors)) {
                $formationObjet = $this->construireFormation($_POST);
                $this->formationModel->updateFormation($formationObjet, $id);
                header('Location: FormationC.php?office=' . $office . '&action=list');
                exit;
            }
        }

        include __DIR__ . '/../view/formations/form.php';
    }

    private function supprimer()
    {
        $office = $this->office();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id > 0) {
            $this->formationModel->deleteFormation($id);
        }

        header('Location: FormationC.php?office=' . $office . '&action=list');
        exit;
    }

    private function detail()
    {
        $office = $this->office();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $formation = $this->formationModel->getFormationById($id);
        $errors = [];
        $successMessage = '';

        if (!$formation) {
            die('Formation introuvable.');
        }

        include __DIR__ . '/../view/formations/detail.php';
    }

    private function inscrire()
    {
        $office = 'front';
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $formation = $this->formationModel->getFormationById($id);
        $errors = [];
        $successMessage = '';

        if (!$formation) {
            die('Formation introuvable.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validerInscription($_POST);

            if (empty($errors)) {
                $apprenant = $this->apprenantModel->getApprenantByEmail(trim($_POST['email']));
                if ($apprenant) {
                    $idApprenant = (int) $apprenant['id_apprenant'];
                } else {
                    $apprenantObjet = new apprenant(trim($_POST['nom']), trim($_POST['email']), trim($_POST['telephone']));
                    $idApprenant = (int) $this->apprenantModel->addApprenant($apprenantObjet);
                }

                if ($this->inscriptionModel->inscriptionExiste($idApprenant, $id)) {
                    $errors[] = 'Vous etes deja inscrit a cette formation.';
                } else {
                    $inscription = new inscriptionFormation($idApprenant, $id, 'en_attente');
                    $this->inscriptionModel->addInscription($inscription);
                    $formation = $this->formationModel->getFormationById($id);
                    $successMessage = 'Votre demande d inscription a ete envoyee.';
                }
            }
        }

        include __DIR__ . '/../view/formations/detail.php';
    }

    private function listeInscriptions()
    {
        $office = 'back';
        $inscriptions = $this->inscriptionModel->listeInscriptions();
        include __DIR__ . '/../view/formations/inscriptions.php';
    }

    private function construireFormation($data)
    {
        return new formation(
            trim($data['titre']),
            trim($data['description']),
            trim($data['date_debut']),
            trim($data['date_fin']),
            (int) $data['duree'],
            (float) $data['prix'],
            trim($data['niveau']),
            trim($data['statut']),
            trim($data['mode']),
            (int) $data['places'],
            (int) $data['id_categorie'],
            (int) $data['id_formateur']
        );
    }

    private function validerFormation($data)
    {
        $errors = [];
        $niveaux = ['Debutant', 'Intermediaire', 'Avance'];
        $statuts = ['planifiee', 'en_cours', 'terminee', 'annulee'];
        $modes = ['Presentiel', 'En ligne', 'Hybride'];

        if (!isset($data['titre']) || strlen(trim($data['titre'])) < 3) {
            $errors[] = 'Le titre doit contenir au moins 3 caracteres.';
        }

        if (!isset($data['description']) || strlen(trim($data['description'])) < 10) {
            $errors[] = 'La description doit contenir au moins 10 caracteres.';
        }

        if (!isset($data['id_categorie']) || (int) $data['id_categorie'] <= 0) {
            $errors[] = 'Veuillez choisir une categorie.';
        }

        if (!isset($data['id_formateur']) || (int) $data['id_formateur'] <= 0) {
            $errors[] = 'Veuillez choisir un formateur.';
        }

        if (!isset($data['date_debut']) || !$this->dateValide($data['date_debut'])) {
            $errors[] = 'La date de debut doit etre au format YYYY-MM-DD.';
        }

        if (!isset($data['date_fin']) || !$this->dateValide($data['date_fin'])) {
            $errors[] = 'La date de fin doit etre au format YYYY-MM-DD.';
        }

        if (
            isset($data['date_debut'], $data['date_fin']) &&
            $this->dateValide($data['date_debut']) &&
            $this->dateValide($data['date_fin']) &&
            strtotime($data['date_fin']) < strtotime($data['date_debut'])
        ) {
            $errors[] = 'La date de fin doit etre apres la date de debut.';
        }

        if (!isset($data['duree']) || filter_var($data['duree'], FILTER_VALIDATE_INT) === false || (int) $data['duree'] <= 0) {
            $errors[] = 'La duree doit etre un nombre entier positif.';
        }

        if (!isset($data['places']) || filter_var($data['places'], FILTER_VALIDATE_INT) === false || (int) $data['places'] <= 0) {
            $errors[] = 'Le nombre de places doit etre un entier positif.';
        }

        if (!isset($data['prix']) || !is_numeric($data['prix']) || (float) $data['prix'] < 0) {
            $errors[] = 'Le prix doit etre un nombre positif ou egal a zero.';
        }

        if (!isset($data['niveau']) || !in_array($data['niveau'], $niveaux)) {
            $errors[] = 'Veuillez choisir un niveau valide.';
        }

        if (!isset($data['statut']) || !in_array($data['statut'], $statuts)) {
            $errors[] = 'Veuillez choisir un statut valide.';
        }

        if (!isset($data['mode']) || !in_array($data['mode'], $modes)) {
            $errors[] = 'Veuillez choisir un mode valide.';
        }

        return $errors;
    }

    private function validerInscription($data)
    {
        $errors = [];

        if (!isset($data['nom']) || strlen(trim($data['nom'])) < 3) {
            $errors[] = 'Le nom doit contenir au moins 3 caracteres.';
        }

        if (!isset($data['email']) || !filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Veuillez saisir un email valide.';
        }

        if (!isset($data['telephone']) || strlen(trim($data['telephone'])) < 8) {
            $errors[] = 'Le telephone doit contenir au moins 8 chiffres.';
        }

        return $errors;
    }

    private function dateValide($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}

$formationController = new FormationC();
$formationController->handleRequest();
?>

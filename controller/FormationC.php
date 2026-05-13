<?php
require_once __DIR__ . '/../Model/formation.php';
require_once __DIR__ . '/../Model/inscriptionFormation.php';
require_once __DIR__ . '/FormationModel.php';
require_once __DIR__ . '/CategorieFormationModel.php';
require_once __DIR__ . '/FormateurModel.php';
require_once __DIR__ . '/InscriptionFormationModel.php';
require_once __DIR__ . '/UtilisateurModel.php';
require_once __DIR__ . '/AuthC.php';
require_once __DIR__ . '/Mailer.php';

class FormationC
{
    private $formationModel;
    private $categorieModel;
    private $formateurModel;
    private $inscriptionModel;
    private $utilisateurModel;

    public function __construct()
    {
        $this->formationModel = new FormationModel();
        $this->categorieModel = new CategorieFormationModel();
        $this->formateurModel = new FormateurModel();
        $this->inscriptionModel = new InscriptionFormationModel();
        $this->utilisateurModel = new UtilisateurModel();
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
        if ($office === 'back') {
            AuthC::requireAdmin();
        }

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
        AuthC::requireAdmin();
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
        AuthC::requireAdmin();
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
        AuthC::requireAdmin();
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
        if ($office === 'back') {
            AuthC::requireAdmin();
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $formation = $this->formationModel->getFormationById($id);
        $errors = [];
        $successMessage = '';
        $connectedUser = AuthC::isLoggedIn() ? $this->utilisateurModel->getUtilisateurById(AuthC::currentUserId()) : null;

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

        if (!AuthC::isLoggedIn()) {
            AuthC::startSession();
            $_SESSION['redirect_after_login'] = 'FormationC.php?office=front&action=detail&id=' . $id;
            header('Location: AuthController.php?action=login');
            exit;
        }

        $connectedUser = $this->utilisateurModel->getUtilisateurById(AuthC::currentUserId());
        if (!$connectedUser) {
            die('Utilisateur introuvable.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
            if ($telephone === '' && isset($connectedUser['phone'])) {
                $telephone = trim($connectedUser['phone']);
            }

            $errors = $this->validerInscription(['telephone' => $telephone]);

            if (empty($errors)) {
                $userId = (int) $connectedUser['id'];

                if ($this->inscriptionModel->inscriptionExiste($userId, $id)) {
                    $errors[] = 'Vous etes deja inscrit a cette formation.';
                } else {
                    $inscription = new inscriptionFormation($userId, $id, 'en_attente');
                    $this->inscriptionModel->addInscription($inscription);
                    $formation = $this->formationModel->getFormationById($id);
                    $receiptSent = $this->sendInscriptionReceipt($connectedUser, $formation, $telephone);
                    $successMessage = $receiptSent
                        ? 'Votre demande d inscription a ete envoyee. Un recu vous a ete envoye par email.'
                        : 'Votre demande d inscription a ete envoyee. Le recu email n a pas pu partir pour le moment.';
                }
            }
        }

        include __DIR__ . '/../view/formations/detail.php';
    }

    private function listeInscriptions()
    {
        AuthC::requireAdmin();
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
            (int) $data['id_formateur'],
            $this->cleanFormationImage(isset($data['image_url']) ? $data['image_url'] : '', isset($data['titre']) ? $data['titre'] : '')
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

        if (isset($data['image_url']) && trim($data['image_url']) !== '' && !filter_var(trim($data['image_url']), FILTER_VALIDATE_URL)) {
            $errors[] = 'L image de la formation doit etre une URL valide.';
        }

        return $errors;
    }

    private function cleanFormationImage($imageUrl, $title)
    {
        $imageUrl = trim((string) $imageUrl);

        if ($imageUrl !== '' && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }

        return $this->formationImageFor($title);
    }

    private function formationImageFor($title)
    {
        $text = strtolower((string) $title);

        if (strpos($text, 'mysql') !== false || strpos($text, 'data') !== false || strpos($text, 'sql') !== false) {
            return 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1200&q=80';
        }

        if (strpos($text, 'ui') !== false || strpos($text, 'ux') !== false || strpos($text, 'design') !== false) {
            return 'https://images.unsplash.com/photo-1559028012-481c04fa702d?auto=format&fit=crop&w=1200&q=80';
        }

        if (strpos($text, 'marketing') !== false || strpos($text, 'content') !== false) {
            return 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80';
        }

        if (strpos($text, 'php') !== false || strpos($text, 'mvc') !== false || strpos($text, 'web') !== false) {
            return 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1200&q=80';
        }

        return 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80';
    }

    private function validerInscription($data)
    {
        $errors = [];

        if (!isset($data['telephone']) || strlen(trim($data['telephone'])) < 8) {
            $errors[] = 'Le telephone doit contenir au moins 8 chiffres.';
        }

        return $errors;
    }

    private function sendInscriptionReceipt($user, $formation, $telephone)
    {
        $email = trim(isset($user['email']) ? $user['email'] : '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $name = trim((isset($user['first_name']) ? $user['first_name'] : '') . ' ' . (isset($user['last_name']) ? $user['last_name'] : ''));
        $safeName = htmlspecialchars($name !== '' ? $name : 'Workify', ENT_QUOTES);
        $safeTitle = htmlspecialchars($formation['titre'], ENT_QUOTES);
        $safeCategory = htmlspecialchars($formation['nom_categorie'], ENT_QUOTES);
        $safeTrainer = htmlspecialchars($formation['nom_formateur'], ENT_QUOTES);
        $safeMode = htmlspecialchars($formation['mode'], ENT_QUOTES);
        $safeStart = htmlspecialchars($formation['date_debut'], ENT_QUOTES);
        $safeEnd = htmlspecialchars($formation['date_fin'], ENT_QUOTES);
        $safePhone = htmlspecialchars($telephone, ENT_QUOTES);
        $price = number_format((float) $formation['prix'], 0, '.', ' ');

        $html = '<div style="font-family:Arial,sans-serif;color:#111827;line-height:1.6">'
            . '<h2 style="color:#2563eb;margin-bottom:8px">Recu d inscription Workify</h2>'
            . '<p>Bonjour ' . $safeName . ',</p>'
            . '<p>Votre demande d inscription a bien ete recue. Elle est maintenant en attente de validation.</p>'
            . '<div style="border:1px solid #dbeafe;border-radius:14px;padding:16px;background:#f8fbff">'
            . '<strong style="font-size:18px">' . $safeTitle . '</strong>'
            . '<p style="margin:8px 0 0">Categorie: ' . $safeCategory . '</p>'
            . '<p style="margin:4px 0 0">Formateur: ' . $safeTrainer . '</p>'
            . '<p style="margin:4px 0 0">Dates: ' . $safeStart . ' au ' . $safeEnd . '</p>'
            . '<p style="margin:4px 0 0">Mode: ' . $safeMode . '</p>'
            . '<p style="margin:4px 0 0">Prix: ' . $price . ' DT</p>'
            . '<p style="margin:4px 0 0">Telephone: ' . $safePhone . '</p>'
            . '</div>'
            . '<p>Merci de garder ce message comme recu de votre demande.</p>'
            . '</div>';

        $text = "Recu d inscription Workify\n\n"
            . "Bonjour " . $name . ",\n"
            . "Votre demande d inscription a bien ete recue et elle est en attente de validation.\n\n"
            . "Formation: " . $formation['titre'] . "\n"
            . "Categorie: " . $formation['nom_categorie'] . "\n"
            . "Formateur: " . $formation['nom_formateur'] . "\n"
            . "Dates: " . $formation['date_debut'] . " au " . $formation['date_fin'] . "\n"
            . "Mode: " . $formation['mode'] . "\n"
            . "Prix: " . $price . " DT\n"
            . "Telephone: " . $telephone . "\n";

        $mailer = new WorkifyMailer();
        return $mailer->send($email, 'Recu inscription formation - ' . $formation['titre'], $html, $text);
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

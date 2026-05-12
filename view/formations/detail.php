<?php
$pageTitle = 'Detail Formation';
$activeModule = 'formations';
$errors = isset($errors) ? $errors : [];
$successMessage = isset($successMessage) ? $successMessage : '';
$connectedUser = isset($connectedUser) ? $connectedUser : null;
$viewerLoggedIn = AuthC::isLoggedIn();
include __DIR__ . '/../includes/header.php';
?>

<div class="detail-box">
    <?php if ($successMessage !== '') { ?>
        <div class="success-box"><?= htmlspecialchars($successMessage, ENT_QUOTES); ?></div>
    <?php } ?>

    <div class="toolbar">
        <div>
            <p class="eyebrow"><?= htmlspecialchars($formation['nom_categorie'], ENT_QUOTES); ?> - <?= htmlspecialchars($formation['niveau'], ENT_QUOTES); ?></p>
            <h2><?= htmlspecialchars($formation['titre'], ENT_QUOTES); ?></h2>
            <p class="muted">Formateur: <?= htmlspecialchars($formation['nom_formateur'], ENT_QUOTES); ?> - <?= htmlspecialchars($formation['specialite'], ENT_QUOTES); ?></p>
        </div>
        <span class="badge badge-green"><?= htmlspecialchars($formation['mode'], ENT_QUOTES); ?></span>
    </div>

    <p><?= nl2br(htmlspecialchars($formation['description'], ENT_QUOTES)); ?></p>

    <div class="stats-grid">
        <div class="card">Duree <strong><?= (int) $formation['duree']; ?>h</strong></div>
        <div class="card">Prix <strong><?= number_format((float) $formation['prix'], 0, '.', ' '); ?> DT</strong></div>
        <div class="card">Places <strong><?= (int) $formation['total_inscrits']; ?>/<?= (int) $formation['places']; ?></strong></div>
        <div class="card">Statut <strong style="font-size: 22px;"><?= htmlspecialchars($formation['statut'], ENT_QUOTES); ?></strong></div>
    </div>

    <p><strong>Dates:</strong> <?= htmlspecialchars($formation['date_debut'], ENT_QUOTES); ?> au <?= htmlspecialchars($formation['date_fin'], ENT_QUOTES); ?></p>
    <p><strong>Email formateur:</strong> <?= htmlspecialchars($formation['email_formateur'], ENT_QUOTES); ?></p>

    <div class="actions" style="margin-top: 18px;">
        <a class="btn" href="../controller/FormationC.php?office=<?= $office; ?>&action=list">Retour</a>
        <?php if ($office === 'back') { ?>
            <a class="btn btn-primary" href="../controller/FormationC.php?office=back&action=edit&id=<?= (int) $formation['id_formation']; ?>">Modifier</a>
        <?php } ?>
    </div>
</div>

<?php if ($office === 'front') { ?>
    <?php if (!$viewerLoggedIn) { ?>
        <div class="detail-box gated-box" style="margin-top: 20px;">
            <div>
                <p class="eyebrow">Compte requis</p>
                <h2>Connectez-vous pour vous inscrire</h2>
                <p class="muted">Les inscriptions sont envoyees avec un compte Workify afin que votre demande reste liee a votre profil.</p>
            </div>
            <a class="btn btn-primary" href="../controller/AuthController.php?action=login&redirect=<?= urlencode('FormationC.php?office=front&action=detail&id=' . (int) $formation['id_formation']); ?>">Connexion</a>
        </div>
    <?php } else { ?>
    <form class="form-box" data-validate="inscription" action="../controller/FormationC.php?action=enroll&id=<?= (int) $formation['id_formation']; ?>" method="post" style="margin-top: 20px;">
        <h2>S'inscrire a cette formation</h2>
        <p class="muted">Votre demande sera envoyee avec le compte <?= htmlspecialchars(AuthC::currentUserName(), ENT_QUOTES); ?> (<?= htmlspecialchars(AuthC::currentUserEmail(), ENT_QUOTES); ?>).</p>

        <div class="error-box">
            <?php if (!empty($errors)) { ?>
                <ul>
                    <?php foreach ($errors as $error) { ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES); ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>

        <div class="form-grid">
            <div>
                <label for="telephone">Telephone</label>
                <input id="telephone" name="telephone" value="<?= htmlspecialchars(isset($connectedUser['phone']) ? $connectedUser['phone'] : '', ENT_QUOTES); ?>">
            </div>
        </div>

        <button class="btn btn-primary" type="submit" style="margin-top: 16px;">Envoyer l inscription</button>
    </form>
    <?php } ?>
<?php } ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

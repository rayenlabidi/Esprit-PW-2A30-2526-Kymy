<?php
$pageTitle = 'Detail Formation';
$activeModule = 'formations';
$errors = isset($errors) ? $errors : [];
$successMessage = isset($successMessage) ? $successMessage : '';
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
    <form class="form-box" data-validate="inscription" action="../controller/FormationC.php?action=enroll&id=<?= (int) $formation['id_formation']; ?>" method="post" style="margin-top: 20px;">
        <h2>Postuler / s'inscrire a cette formation</h2>

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
                <label for="nom">Nom complet</label>
                <input id="nom" name="nom">
            </div>
            <div>
                <label for="email">Email</label>
                <input id="email" name="email">
            </div>
            <div>
                <label for="telephone">Telephone</label>
                <input id="telephone" name="telephone">
            </div>
        </div>

        <button class="btn btn-primary" type="submit" style="margin-top: 16px;">Envoyer l inscription</button>
    </form>
<?php } ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

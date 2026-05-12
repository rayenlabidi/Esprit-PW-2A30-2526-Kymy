<?php
$pageTitle = 'Detail Job';
$activeModule = 'jobs';
$errors = isset($errors) ? $errors : [];
$successMessage = isset($successMessage) ? $successMessage : '';
$candidatures = isset($candidatures) ? $candidatures : [];
$viewerLoggedIn = AuthC::isLoggedIn();
include __DIR__ . '/../includes/header.php';
?>

<div class="detail-box">
    <?php if ($successMessage !== '') { ?>
        <div class="success-box"><?= htmlspecialchars($successMessage, ENT_QUOTES); ?></div>
    <?php } ?>

    <div class="toolbar">
        <div>
            <p class="eyebrow"><?= htmlspecialchars($job['nom_categorie'], ENT_QUOTES); ?> - <?= htmlspecialchars($job['job_type'], ENT_QUOTES); ?></p>
            <h2><?= htmlspecialchars($job['title'], ENT_QUOTES); ?></h2>
            <p class="muted">Publie par: <?= htmlspecialchars($job['nom_publisher'], ENT_QUOTES); ?> - <?= htmlspecialchars($job['email_publisher'], ENT_QUOTES); ?></p>
        </div>
        <span class="badge badge-green"><?= $job['is_remote'] ? 'A distance' : 'Sur site'; ?></span>
    </div>

    <p><?= nl2br(htmlspecialchars($job['description'], ENT_QUOTES)); ?></p>

    <div class="stats-grid">
        <div class="card">Budget <strong><?= number_format((float) $job['budget'], 0, '.', ' '); ?> DT</strong></div>
        <div class="card">Type <strong style="font-size: 22px;"><?= htmlspecialchars($job['job_type'], ENT_QUOTES); ?></strong></div>
        <div class="card">Candidatures <strong><?= (int) $job['total_candidatures']; ?></strong></div>
        <div class="card">Statut <strong style="font-size: 22px;"><?= htmlspecialchars($job['status'], ENT_QUOTES); ?></strong></div>
    </div>

    <p><strong>Localisation:</strong> <?= htmlspecialchars($job['location'], ENT_QUOTES); ?></p>
    <p><strong>Date publication:</strong> <?= htmlspecialchars($job['created_at'], ENT_QUOTES); ?></p>

    <div class="actions" style="margin-top: 18px;">
        <a class="btn" href="../controller/JobC.php?office=<?= $office; ?>&action=list">Retour</a>
        <?php if ($office === 'back') { ?>
            <a class="btn btn-primary" href="../controller/JobC.php?office=back&action=edit&id=<?= (int) $job['id']; ?>">Modifier</a>
            <a class="btn" href="../controller/JobC.php?office=back&action=applications">Voir toutes les candidatures</a>
        <?php } ?>
    </div>
</div>

<?php if ($office === 'front') { ?>
    <?php if (!$viewerLoggedIn) { ?>
        <div class="detail-box gated-box" style="margin-top: 20px;">
            <div>
                <p class="eyebrow">Compte requis</p>
                <h2>Connectez-vous pour postuler</h2>
                <p class="muted">Les candidatures sont envoyees depuis un compte Workify pour garder votre profil et vos fichiers au meme endroit.</p>
            </div>
            <a class="btn btn-primary" href="../controller/AuthController.php?action=login&redirect=<?= urlencode('JobC.php?office=front&action=detail&id=' . (int) $job['id']); ?>">Connexion</a>
        </div>
    <?php } else { ?>
    <form class="form-box" data-validate="candidature" action="../controller/JobC.php?action=apply&id=<?= (int) $job['id']; ?>" method="post" enctype="multipart/form-data" style="margin-top: 20px;">
        <h2>Postuler a ce job</h2>
        <p class="muted">Votre candidature sera envoyee avec le compte <?= htmlspecialchars(AuthC::currentUserName(), ENT_QUOTES); ?> (<?= htmlspecialchars(AuthC::currentUserEmail(), ENT_QUOTES); ?>).</p>

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
            <div class="field-full">
                <label for="message">Message de candidature</label>
                <textarea id="message" name="message"></textarea>
            </div>
            <div>
                <label for="cv_file">CV PDF</label>
                <input id="cv_file" name="cv_file" type="file" accept=".pdf">
            </div>
            <div>
                <label for="photo_file">Photo</label>
                <input id="photo_file" name="photo_file" type="file" accept="image/*">
            </div>
        </div>

        <button class="btn btn-primary" type="submit" style="margin-top: 16px;">Envoyer ma candidature</button>
    </form>
    <?php } ?>
<?php } ?>

<?php if ($office === 'back') { ?>
    <div class="table-box" style="margin-top: 20px;">
        <div class="section-head">
            <h2>Candidatures recues</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Candidat</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Fichiers</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($candidatures)) { ?>
                    <tr><td colspan="6">Aucune candidature recue.</td></tr>
                <?php } ?>
                <?php foreach ($candidatures as $candidatureItem) { ?>
                    <tr>
                        <td data-label="Candidat"><?= htmlspecialchars($candidatureItem['first_name'] . ' ' . $candidatureItem['last_name'], ENT_QUOTES); ?></td>
                        <td data-label="Email"><?= htmlspecialchars($candidatureItem['email'], ENT_QUOTES); ?></td>
                        <td data-label="Message"><?= nl2br(htmlspecialchars(substr($candidatureItem['cover_letter'], 0, 160), ENT_QUOTES)); ?></td>
                        <td data-label="Fichiers" class="actions">
                            <?php if (!empty($candidatureItem['cv_url'])) { ?>
                                <a class="btn" href="../<?= htmlspecialchars($candidatureItem['cv_url'], ENT_QUOTES); ?>" target="_blank">CV</a>
                            <?php } ?>
                            <?php if (!empty($candidatureItem['photo_url'])) { ?>
                                <a class="btn" href="../<?= htmlspecialchars($candidatureItem['photo_url'], ENT_QUOTES); ?>" target="_blank">Photo</a>
                            <?php } ?>
                        </td>
                        <td data-label="Statut"><span class="badge"><?= htmlspecialchars($candidatureItem['status'], ENT_QUOTES); ?></span></td>
                        <td data-label="Actions" class="actions">
                            <a class="btn btn-green" href="../controller/JobC.php?office=back&action=status&id=<?= (int) $candidatureItem['id']; ?>&statut=accepted">Accepter</a>
                            <a class="btn btn-danger" href="../controller/JobC.php?office=back&action=status&id=<?= (int) $candidatureItem['id']; ?>&statut=rejected">Refuser</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

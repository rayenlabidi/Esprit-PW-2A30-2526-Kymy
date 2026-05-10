<?php
$pageTitle = 'Candidatures Jobs';
$activeModule = 'jobs';
$candidatures = isset($candidatures) ? $candidatures : [];
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Backoffice jobs</p>
        <h2>Suivi des candidatures</h2>
    </div>
    <a class="btn" href="../controller/JobC.php?office=back&action=list">Retour aux jobs</a>
</div>

<div class="table-box">
    <table>
        <thead>
            <tr>
                <th>Job</th>
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
                <tr><td colspan="7">Aucune candidature trouvee.</td></tr>
            <?php } ?>
            <?php foreach ($candidatures as $candidatureItem) { ?>
                <tr>
                    <td data-label="Job"><strong><?= htmlspecialchars($candidatureItem['titre_job'], ENT_QUOTES); ?></strong></td>
                    <td data-label="Candidat"><?= htmlspecialchars($candidatureItem['first_name'] . ' ' . $candidatureItem['last_name'], ENT_QUOTES); ?></td>
                    <td data-label="Email"><?= htmlspecialchars($candidatureItem['email'], ENT_QUOTES); ?></td>
                    <td data-label="Message"><?= nl2br(htmlspecialchars(substr($candidatureItem['cover_letter'], 0, 150), ENT_QUOTES)); ?></td>
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
                        <a class="btn" href="../controller/JobC.php?office=back&action=detail&id=<?= (int) $candidatureItem['job_id']; ?>">Job</a>
                        <a class="btn btn-green" href="../controller/JobC.php?office=back&action=status&id=<?= (int) $candidatureItem['id']; ?>&statut=accepted">Accepter</a>
                        <a class="btn btn-danger" href="../controller/JobC.php?office=back&action=status&id=<?= (int) $candidatureItem['id']; ?>&statut=rejected">Refuser</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

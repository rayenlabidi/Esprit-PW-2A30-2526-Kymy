<?php
$pageTitle = 'Inscriptions';
$activeModule = 'formations';
$inscriptions = isset($inscriptions) ? $inscriptions : [];
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <h2>Inscriptions formations</h2>
    </div>
    <a class="btn" href="../controller/FormationC.php?office=back&action=list">Retour formations</a>
</div>

<div class="table-box">
    <table>
        <thead>
            <tr>
                <th>Apprenant</th>
                <th>Email</th>
                <th>Telephone</th>
                <th>Formation</th>
                <th>Formateur</th>
                <th>Date</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($inscriptions)) { ?>
                <tr><td colspan="7">Aucune inscription pour le moment.</td></tr>
            <?php } ?>
            <?php foreach ($inscriptions as $inscription) { ?>
                <tr>
                    <td data-label="Apprenant"><?= htmlspecialchars($inscription['nom_apprenant'], ENT_QUOTES); ?></td>
                    <td data-label="Email"><?= htmlspecialchars($inscription['email'], ENT_QUOTES); ?></td>
                    <td data-label="Telephone"><?= htmlspecialchars($inscription['telephone'], ENT_QUOTES); ?></td>
                    <td data-label="Formation"><?= htmlspecialchars($inscription['titre_formation'], ENT_QUOTES); ?></td>
                    <td data-label="Formateur"><?= htmlspecialchars($inscription['nom_formateur'], ENT_QUOTES); ?></td>
                    <td data-label="Date"><?= htmlspecialchars($inscription['date_inscription'], ENT_QUOTES); ?></td>
                    <td data-label="Statut"><span class="badge"><?= htmlspecialchars($inscription['statut'], ENT_QUOTES); ?></span></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

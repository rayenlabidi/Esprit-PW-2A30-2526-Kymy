<?php
$pageTitle = 'Workify Admin';
$office = 'back';
$activeModule = '';
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Formation management</p>
        <h2>Training Control Center</h2>
        <p class="muted">Manage Workify formations, trainers, categories and inscriptions.</p>
    </div>
    <a class="btn btn-primary" href="../controller/FormationC.php?office=back&action=add">Ajouter formation</a>
</div>

<div class="grid">
    <div class="card">
        Formations
        <strong><?= isset($stats['formations']) ? (int) $stats['formations'] : 0; ?></strong>
        <a class="btn btn-primary" href="../controller/FormationC.php?office=back&action=list">Gerer</a>
    </div>
</div>

<div class="detail-box">
    <h2>Jointures presentes</h2>
    <p>`formation.id_categorie` relie les formations aux categories, `formation.id_formateur` relie les formations aux formateurs, et `inscription_formation` relie les apprenants aux formations.</p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

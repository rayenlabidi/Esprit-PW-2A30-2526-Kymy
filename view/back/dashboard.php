<?php
$pageTitle = 'Workify Admin';
$office = 'back';
$activeModule = 'dashboard';
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Workify management</p>
        <h2>Integrated Control Center</h2>
        <p class="muted">Manage Workify formations, jobs, trainers, publishers and candidatures.</p>
    </div>
    <div class="actions">
        <a class="btn" href="../controller/JobC.php?office=back&action=add">Ajouter job</a>
        <a class="btn btn-primary" href="../controller/FormationC.php?office=back&action=add">Ajouter formation</a>
    </div>
</div>

<div class="grid">
    <div class="card">
        Formations
        <strong><?= isset($stats['formations']) ? (int) $stats['formations'] : 0; ?></strong>
        <a class="btn btn-primary" href="../controller/FormationC.php?office=back&action=list">Gerer</a>
    </div>
    <div class="card">
        Jobs
        <strong><?= isset($stats['jobs']) ? (int) $stats['jobs'] : 0; ?></strong>
        <a class="btn btn-primary" href="../controller/JobC.php?office=back&action=list">Gerer</a>
    </div>
    <div class="card">
        Utilisateurs
        <strong><?= isset($stats['users']) ? (int) $stats['users'] : 0; ?></strong>
        <a class="btn btn-primary" href="../controller/UtilisateurC.php?action=list">Gerer</a>
    </div>
</div>

<div class="detail-box">
    <h2>Jointures presentes</h2>
    <p>`formation.id_categorie` relie les formations aux categories, `formation.id_formateur` relie les formations aux formateurs, `inscription_formation` relie les apprenants aux formations, `jobs.category_id` relie les jobs aux categories, `jobs.publisher_id` relie les jobs aux utilisateurs et `candidatures` relie les utilisateurs aux jobs.</p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

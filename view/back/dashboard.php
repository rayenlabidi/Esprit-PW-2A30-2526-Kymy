<?php
$pageTitle = 'Espace Workify';
$office = 'back';
$activeModule = 'dashboard';
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Gestion Workify</p>
        <h2>Centre de pilotage</h2>
        <p class="muted">Suivez les formations, les jobs, les utilisateurs, publications et messages depuis un seul espace.</p>
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
    <div class="card">
        Contacts
        <strong><?= isset($stats['contacts']) ? (int) $stats['contacts'] : 0; ?></strong>
        <a class="btn btn-primary" href="../controller/ContactC.php?action=list">Lire</a>
    </div>
    <div class="card">
        Publications
        <strong><?= isset($stats['publications']) ? (int) $stats['publications'] : 0; ?></strong>
        <a class="btn btn-primary" href="../controller/PublicationC.php?office=back&action=list">Gerer</a>
    </div>
    <div class="card">
        Messages
        <strong><?= isset($stats['messages']) ? (int) $stats['messages'] : 0; ?></strong>
        <a class="btn btn-primary" href="../controller/MessageC.php?office=back&action=list">Lire</a>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

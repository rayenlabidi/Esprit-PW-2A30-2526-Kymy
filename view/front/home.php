<?php
$pageTitle = 'Workify';
$office = 'front';
$activeModule = '';
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Blue-white training marketplace</p>
        <h2>Learn practical skills with Workify</h2>
        <p class="muted">Browse formations, check the trainer, then send your inscription request.</p>
    </div>
    <a class="btn btn-primary" href="../controller/FormationC.php?office=front&action=list">Browse Formations</a>
</div>

<div class="grid">
    <div class="card">
        Formations
        <strong><?= isset($stats['formations']) ? (int) $stats['formations'] : 0; ?></strong>
        <a class="btn" href="../controller/FormationC.php?office=front&action=list">Ouvrir</a>
    </div>
</div>

<div class="detail-box">
    <h2>Gestion des Formations</h2>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

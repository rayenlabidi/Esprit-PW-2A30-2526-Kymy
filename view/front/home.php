<?php
$pageTitle = 'Workify';
$office = 'front';
$activeModule = '';
include __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <div>
        <p class="eyebrow">Blue-white Workify marketplace</p>
        <h2>Learn practical skills and find missions</h2>
        <p class="muted">Browse formations, check jobs, then send your inscription or candidature request.</p>
    </div>
    <div class="actions">
        <a class="btn" href="../controller/JobC.php?office=front&action=list">Browse Jobs</a>
        <a class="btn btn-primary" href="../controller/FormationC.php?office=front&action=list">Browse Formations</a>
    </div>
</div>

<div class="grid">
    <div class="card">
        Formations
        <strong><?= isset($stats['formations']) ? (int) $stats['formations'] : 0; ?></strong>
        <a class="btn" href="../controller/FormationC.php?office=front&action=list">Ouvrir</a>
    </div>
    <div class="card">
        Jobs
        <strong><?= isset($stats['jobs']) ? (int) $stats['jobs'] : 0; ?></strong>
        <a class="btn" href="../controller/JobC.php?office=front&action=list">Ouvrir</a>
    </div>
</div>

<div class="detail-box">
    <h2>Workify modules integres</h2>
    <p>Les jobs et les formations utilisent la meme sidebar, les memes boutons, les memes cards et la meme palette bleu-blanc.</p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

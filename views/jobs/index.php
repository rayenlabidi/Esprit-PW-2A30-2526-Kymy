<?php $pageTitle = 'Gestion des jobs'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-head">
    <div>
        <p class="eyebrow">Marketplace des jobs</p>
        <h1>Gestion des jobs</h1>
        <p class="muted">Le boss offre un job. Le freelancer postule. Les deux parcours sont differencies clairement dans l interface.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <?php if (has_role(['admin', 'boss'])): ?>
            <a class="btn btn-primary" href="<?= url(['module' => 'jobs', 'action' => 'create']) ?>">Offrir un job</a>
        <?php endif; ?>
        <button class="btn btn-outline" onclick="exportToPDF('jobs-container', 'liste_jobs.pdf')">Exporter en PDF</button>
    </div>
</section>

<div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem;">
    <div class="section-card" style="padding: 1rem; text-align: center;"><strong>Total Jobs</strong><br><span style="font-size: 1.5rem; color: var(--color-primary);"><?= $stats['total'] ?></span></div>
    <div class="section-card" style="padding: 1rem; text-align: center;"><strong>Jobs Ouverts</strong><br><span style="font-size: 1.5rem; color: var(--color-primary);"><?= $stats['open'] ?></span></div>
    <div class="section-card" style="padding: 1rem; text-align: center;"><strong>Candidatures</strong><br><span style="font-size: 1.5rem; color: var(--color-primary);"><?= $stats['applications'] ?></span></div>
    <div class="section-card" style="padding: 1rem; text-align: center;"><strong>Budget Moyen</strong><br><span style="font-size: 1.5rem; color: var(--color-primary);"><?= format_currency($stats['average_budget']) ?></span></div>
</div>

<section class="section-card" style="margin-bottom: 2rem;">
    <form method="GET" action="index.php" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
        <input type="hidden" name="module" value="jobs">
        <input type="hidden" name="action" value="index">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Recherche</label>
            <input type="text" name="search" class="form-control" value="<?= h($filters['search'] ?? '') ?>" placeholder="Titre, description...">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Trier par</label>
            <select name="sort" class="form-control">
                <option value="date_desc" <?= ($filters['sort'] ?? '') === 'date_desc' ? 'selected' : '' ?>>Plus recents d'abord</option>
                <option value="date_asc" <?= ($filters['sort'] ?? '') === 'date_asc' ? 'selected' : '' ?>>Plus anciens d'abord</option>
                <option value="budget_desc" <?= ($filters['sort'] ?? '') === 'budget_desc' ? 'selected' : '' ?>>Budget decroissant</option>
                <option value="budget_asc" <?= ($filters['sort'] ?? '') === 'budget_asc' ? 'selected' : '' ?>>Budget croissant</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filtrer & Trier</button>
    </form>
</section>

<div id="jobs-container">
<?php if ($jobs): ?>
    <section class="listing-stack">
        <?php foreach ($jobs as $job): ?>
            <article class="listing-card">
                <div>
                    <div class="chip-row">
                        <span class="badge badge-info"><?= h($job['category_name'] ?? 'General') ?></span>
                        <span class="badge <?= status_badge_class($job['status']) ?>"><?= h($job['status']) ?></span>
                        <span class="badge <?= $job['is_remote'] ? 'badge-success' : 'badge-neutral' ?>"><?= $job['is_remote'] ? 'Remote' : 'Sur site' ?></span>
                    </div>
                    <h2><?= h($job['title']) ?></h2>
                    <p class="card-copy"><?= h(mb_strimwidth($job['description'], 0, 170, '...')) ?></p>
                    <p class="muted"><?= h($job['job_type']) ?> • <?= h($job['location']) ?> • Publie par <?= h(trim(($job['first_name'] ?? '') . ' ' . ($job['last_name'] ?? ''))) ?></p>
                </div>
                <div class="listing-side">
                    <strong><?= format_currency($job['budget']) ?></strong>
                    <span><?= (int) $job['application_count'] ?> candidature(s)</span>
                    <a class="btn btn-outline btn-small" href="<?= url(['module' => 'jobs', 'action' => 'show', 'id' => $job['id']]) ?>">
                        <?= has_role(['freelancer']) ? 'Postuler a ce job' : 'Voir details' ?>
                    </a>
                    <?php if (has_role(['admin']) || (is_logged_in() && (int) auth_user()['id'] === (int) $job['publisher_id'])): ?>
                        <a class="btn btn-outline btn-small" href="<?= url(['module' => 'jobs', 'action' => 'edit', 'id' => $job['id']]) ?>">Modifier</a>
                        <a class="btn btn-danger btn-small" href="<?= url(['module' => 'jobs', 'action' => 'delete', 'id' => $job['id']]) ?>" onclick="return confirm('Supprimer ce job ?');">Supprimer</a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <section class="section-card empty-card">
        <h2>Aucun job ne correspond aux filtres.</h2>
        <p class="muted">Publiez une offre ou essayez un autre type de mission.</p>
    </section>
<?php endif; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function exportToPDF(elementId, filename) {
    const element = document.getElementById(elementId);
    const opt = {
      margin:       10,
      filename:     filename,
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2 },
      jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

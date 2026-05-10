<?php $pageTitle = 'Gestion des jobs'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-head">
    <div>
        <p class="eyebrow">Backoffice</p>
        <h1>Gestion des jobs</h1>
    </div>
    <div class="page-actions">
        <button class="btn btn-outline" onclick="exportToPDF('jobs-container', 'liste_jobs.pdf')">Exporter</button>
        <?php if (has_role(['admin', 'boss'])): ?>
            <a class="btn btn-primary" href="<?= url(['module' => 'jobs', 'action' => 'create']) ?>">+ Ajouter</a>
        <?php endif; ?>
    </div>
</section>

<section class="module-hero">
    <p class="eyebrow">Workify jobs</p>
    <h2>Job Control Center</h2>
</section>

<section class="stats-row">
    <article class="stat-card">
        <span class="stat-label">Total</span>
        <strong><?= (int) $stats['total'] ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Ouverts</span>
        <strong><?= (int) $stats['open'] ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Candidatures</span>
        <strong><?= (int) $stats['applications'] ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Budget moyen</span>
        <strong><?= format_currency($stats['average_budget']) ?></strong>
    </article>
</section>

<section class="section-card filters-card">
    <form method="GET" action="index.php" class="filters-grid jobs-filters">
        <input type="hidden" name="module" value="jobs">
        <input type="hidden" name="action" value="index">
        <input type="text" name="search" class="form-control" value="<?= h($filters['search'] ?? '') ?>" placeholder="Search by title or company">
        <select name="category_id" class="form-control">
            <option value="">Toutes les categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= ($filters['category_id'] == $category['id']) ? 'selected' : '' ?>><?= h($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="job_type" class="form-control">
            <option value="">Tous les types</option>
            <?php foreach (['Freelance', 'Full-time', 'Stage', 'Part-time'] as $type): ?>
                <option value="<?= $type ?>" <?= ($filters['job_type'] ?? '') === $type ? 'selected' : '' ?>><?= $type ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" class="form-control">
            <option value="">Tous les statuts</option>
            <?php foreach (['open', 'draft', 'closed'] as $status): ?>
                <option value="<?= $status ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="sort" class="form-control">
            <option value="date_desc" <?= ($filters['sort'] ?? '') === 'date_desc' ? 'selected' : '' ?>>Plus recents</option>
            <option value="date_asc" <?= ($filters['sort'] ?? '') === 'date_asc' ? 'selected' : '' ?>>Plus anciens</option>
            <option value="budget_desc" <?= ($filters['sort'] ?? '') === 'budget_desc' ? 'selected' : '' ?>>Budget decroissant</option>
            <option value="budget_asc" <?= ($filters['sort'] ?? '') === 'budget_asc' ? 'selected' : '' ?>>Budget croissant</option>
        </select>
        <button type="submit" class="btn btn-search">Rechercher</button>
        <a class="btn btn-outline" href="<?= url(['module' => 'jobs', 'action' => 'index']) ?>">Initialiser</a>
    </form>
</section>

<div id="jobs-container">
<?php if ($jobs): ?>
    <section class="card-grid jobs-card-grid">
        <?php foreach ($jobs as $job): ?>
            <article class="course-card job-card">
                <div class="job-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 6V5a2 2 0 0 1 4 0v1"></path>
                        <rect x="4" y="6" width="16" height="13" rx="2"></rect>
                        <path d="M4 12h16"></path>
                    </svg>
                </div>
                <div class="course-body">
                    <h2><?= h($job['title']) ?></h2>
                    <p class="card-copy"><?= h(mb_strimwidth($job['description'], 0, 132, '...')) ?></p>
                    <div class="chip-row">
                        <span class="badge badge-info"><?= h($job['category_name'] ?? 'General') ?></span>
                        <span class="badge <?= status_badge_class($job['status']) ?>"><?= h($job['status']) ?></span>
                        <span class="badge <?= $job['is_remote'] ? 'badge-success' : 'badge-neutral' ?>"><?= $job['is_remote'] ? 'Remote' : 'Sur site' ?></span>
                    </div>
                    <p class="job-meta"><strong><?= format_currency($job['budget']) ?></strong> - <?= h($job['job_type']) ?></p>
                    <p class="muted">Publie par <?= h(trim(($job['first_name'] ?? '') . ' ' . ($job['last_name'] ?? ''))) ?> - <?= h($job['location']) ?></p>
                    <div class="card-actions">
                        <a class="btn btn-outline btn-small" href="<?= url(['module' => 'jobs', 'action' => 'show', 'id' => $job['id']]) ?>">
                            <?= has_role(['freelancer']) ? 'Postuler' : 'Details' ?>
                        </a>
                        <?php if (has_role(['admin']) || (is_logged_in() && (int) auth_user()['id'] === (int) $job['publisher_id'])): ?>
                            <a class="btn btn-outline btn-small" href="<?= url(['module' => 'jobs', 'action' => 'edit', 'id' => $job['id']]) ?>">Modifier</a>
                            <a class="btn btn-danger btn-small" href="<?= url(['module' => 'jobs', 'action' => 'delete', 'id' => $job['id']]) ?>" onclick="return confirm('Supprimer ce job ?');">Supprimer</a>
                        <?php endif; ?>
                    </div>
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
      margin: 10,
      filename: filename,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

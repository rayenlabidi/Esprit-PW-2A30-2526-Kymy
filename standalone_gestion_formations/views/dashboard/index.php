<?php $pageTitle = 'Tableau de bord'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow"><?= t('dashboard.eyebrow') ?></p>
        <h1><?= t('dashboard.title') ?></h1>
        <p class="hero-text"><?= t('dashboard.copy') ?></p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="<?= url(['module' => 'jobs', 'action' => 'create']) ?>"><?= t('dashboard.post_job') ?></a>
            <a class="btn btn-outline" href="<?= url(['module' => 'jobs', 'action' => 'index']) ?>"><?= t('dashboard.browse_jobs') ?></a>
        </div>
    </div>
    <div class="hero-panel">
        <div class="stat-card accent">
            <span class="stat-label"><?= t('dashboard.users') ?></span>
            <strong><?= (int) $stats['users'] ?></strong>
        </div>
        <div class="stat-card">
            <span class="stat-label"><?= t('dashboard.formations') ?></span>
            <strong><?= (int) $stats['formations'] ?></strong>
        </div>
        <div class="stat-card">
            <span class="stat-label"><?= t('dashboard.jobs') ?></span>
            <strong><?= (int) $stats['jobs'] ?></strong>
        </div>
    </div>
</section>

<section class="split-section">
    <div class="section-card">
        <div class="section-head">
            <div>
                <p class="eyebrow"><?= t('dashboard.strong_module') ?></p>
                <h2><?= t('dashboard.featured_formations') ?></h2>
            </div>
            <a href="<?= url(['module' => 'formations', 'action' => 'index']) ?>"><?= t('common.view_all') ?></a>
        </div>
        <?php if ($featuredFormations): ?>
            <div class="mini-grid">
                <?php foreach ($featuredFormations as $formation): ?>
                    <article class="mini-card">
                        <span class="badge badge-info"><?= h($formation['category_name'] ?? 'Formation') ?></span>
                        <h3><?= h($formation['title']) ?></h3>
                        <p><?= h($formation['level']) ?> • <?= format_currency($formation['price']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="muted">Aucune formation publiee pour le moment.</p>
        <?php endif; ?>
    </div>

    <div class="section-card">
        <div class="section-head">
            <div>
                <p class="eyebrow"><?= t('dashboard.marketplace') ?></p>
                <h2><?= t('dashboard.recent_jobs') ?></h2>
            </div>
            <a href="<?= url(['module' => 'jobs', 'action' => 'index']) ?>"><?= t('common.view_all') ?></a>
        </div>
        <?php if ($featuredJobs): ?>
            <div class="mini-grid">
                <?php foreach ($featuredJobs as $job): ?>
                    <article class="mini-card">
                        <span class="badge <?= $job['is_remote'] ? 'badge-success' : 'badge-neutral' ?>">
                            <?= $job['is_remote'] ? 'Remote' : 'Sur site' ?>
                        </span>
                        <h3><?= h($job['title']) ?></h3>
                        <p><?= h($job['job_type']) ?> • <?= format_currency($job['budget']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="muted">Aucun job ouvert pour le moment.</p>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

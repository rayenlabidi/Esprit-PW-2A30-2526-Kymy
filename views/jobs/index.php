<?php $pageTitle = 'Gestion des jobs'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-head">
    <div>
        <p class="eyebrow">Marketplace des jobs</p>
        <h1>Gestion des jobs</h1>
        <p class="muted">Le boss offre un job. Le freelancer postule. Les deux parcours sont differencies clairement dans l interface.</p>
    </div>
    <?php if (has_role(['admin', 'boss'])): ?>
        <a class="btn btn-primary" href="<?= url(['module' => 'jobs', 'action' => 'create']) ?>">Offrir un job</a>
    <?php endif; ?>
</section>

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

<?php require __DIR__ . '/../layouts/footer.php'; ?>

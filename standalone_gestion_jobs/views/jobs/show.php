<?php $pageTitle = 'Details job'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="detail-layout">
    <article class="detail-card">
        <div class="detail-content">
            <div class="chip-row">
                <span class="badge badge-info"><?= h($job['category_name'] ?? 'General') ?></span>
                <span class="badge <?= status_badge_class($job['status']) ?>"><?= h($job['status']) ?></span>
                <span class="badge <?= $job['is_remote'] ? 'badge-success' : 'badge-neutral' ?>"><?= $job['is_remote'] ? 'Remote' : 'Sur site' ?></span>
            </div>
            <h1><?= h($job['title']) ?></h1>
            <p><?= nl2br(h($job['description'])) ?></p>
            <div class="detail-grid">
                <div><strong>Budget</strong><span><?= format_currency($job['budget']) ?></span></div>
                <div><strong>Type</strong><span><?= h($job['job_type']) ?></span></div>
                <div><strong>Lieu</strong><span><?= h($job['location']) ?></span></div>
                <div><strong>Publie par</strong><span><?= h(trim($job['first_name'] . ' ' . $job['last_name'])) ?></span></div>
                <div><strong>Candidatures</strong><span><?= (int) $job['application_count'] ?></span></div>
                <div><strong>Date</strong><span><?= format_date($job['created_at']) ?></span></div>
            </div>

            <?php if (has_role(['freelancer', 'admin']) && !$hasApplied): ?>
                <div class="apply-box">
                    <h2>Postuler a ce job</h2>
                    <form method="POST" action="<?= url(['module' => 'jobs', 'action' => 'apply', 'id' => $job['id']]) ?>" class="stack-form js-validate" novalidate>
                        <div class="form-group">
                            <label for="cover_letter">Message de candidature</label>
                            <textarea id="cover_letter" name="cover_letter" class="form-control" data-label="Message de candidature" data-required="1" data-minlength="20"></textarea>
                            <small class="field-error"></small>
                        </div>
                        <button class="btn btn-primary" type="submit">Envoyer ma candidature</button>
                    </form>
                </div>
            <?php elseif ($hasApplied): ?>
                <p class="badge badge-success">Vous avez deja postule a ce job.</p>
            <?php endif; ?>

            <div class="card-actions">
                <a class="btn btn-outline" href="<?= url(['module' => 'jobs', 'action' => 'index']) ?>">Retour aux jobs</a>
            </div>
        </div>
    </article>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

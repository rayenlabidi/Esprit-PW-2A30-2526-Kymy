<?php $pageTitle = t('formations.details_title'); require __DIR__ . '/../layouts/header.php'; ?>

<section class="detail-layout">
    <article class="detail-card">
        <div class="detail-banner" style="background-image:url('<?= h($formation['image_url'] ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80') ?>');"></div>
        <div class="detail-content">
            <div class="chip-row">
                <span class="badge badge-info"><?= h($formation['category_name'] ?? 'General') ?></span>
                <span class="badge <?= status_badge_class($formation['status']) ?>"><?= h($formation['status']) ?></span>
                <span class="badge badge-neutral"><?= h($formation['level']) ?></span>
            </div>
            <h1><?= h($formation['title']) ?></h1>
            <p><?= nl2br(h($formation['description'])) ?></p>
            <div class="detail-grid">
                <div><strong><?= t('formations.price') ?></strong><span><?= format_currency($formation['price']) ?></span></div>
                <div><strong><?= t('formations.duration') ?></strong><span><?= (int) $formation['duration_hours'] ?> <?= lang() === 'fr' ? 'heures' : 'hours' ?></span></div>
                <div><strong><?= t('formations.trainer') ?></strong><span><?= h(trim($formation['first_name'] . ' ' . $formation['last_name'])) ?></span></div>
                <div><strong><?= t('formations.learners') ?></strong><span><?= (int) $formation['enrolled_count'] ?></span></div>
                <div><strong><?= t('formations.tags') ?></strong><span><?= h($formation['tags']) ?></span></div>
                <div><strong><?= t('formations.created_on') ?></strong><span><?= format_date($formation['created_at']) ?></span></div>
            </div>
            <div class="card-actions">
                <?php if (is_logged_in() && has_role(['freelancer', 'admin']) && !$isEnrolled): ?>
                    <a class="btn btn-primary" href="<?= url(['module' => 'formations', 'action' => 'enroll', 'id' => $formation['id']]) ?>"><?= t('formations.enroll') ?></a>
                <?php elseif ($isEnrolled): ?>
                    <span class="badge badge-success"><?= t('formations.already_enrolled') ?></span>
                <?php endif; ?>
                <a class="btn btn-outline" href="<?= url(['module' => 'formations', 'action' => 'index']) ?>"><?= t('formations.back_catalog') ?></a>
            </div>
        </div>
    </article>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

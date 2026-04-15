<?php $pageTitle = 'Profil utilisateur'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="profile-shell">
    <article class="profile-card">
        <div class="profile-header">
            <div class="avatar-circle"><?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?></div>
            <div>
                <h1><?= h($user['first_name'] . ' ' . $user['last_name']) ?></h1>
                <p class="muted"><?= h($user['headline']) ?></p>
                <div class="chip-row">
                    <span class="badge badge-info"><?= h($user['role_name']) ?></span>
                    <span class="badge <?= status_badge_class($user['status']) ?>"><?= h($user['status']) ?></span>
                </div>
            </div>
        </div>
        <p><?= nl2br(h($user['bio'])) ?></p>
        <div class="detail-grid">
            <div><strong>Email</strong><span><?= h($user['email']) ?></span></div>
            <div><strong>Membre depuis</strong><span><?= format_date($user['created_at']) ?></span></div>
        </div>
    </article>

    <article class="section-card">
        <div class="section-head">
            <h2>Inscriptions aux formations</h2>
        </div>
        <?php if ($enrollments): ?>
            <ul class="simple-list">
                <?php foreach ($enrollments as $item): ?>
                    <li><strong><?= h($item['title']) ?></strong> • <?= h($item['level']) ?> • Inscrit le <?= format_date($item['enrolled_at']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="empty-copy">Aucune inscription pour le moment.</p>
        <?php endif; ?>
    </article>

    <article class="section-card">
        <div class="section-head">
            <h2>Candidatures envoyees</h2>
        </div>
        <?php if ($applications): ?>
            <ul class="simple-list">
                <?php foreach ($applications as $item): ?>
                    <li><strong><?= h($item['title']) ?></strong> • <?= h($item['job_type']) ?> • <?= h($item['status']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="empty-copy">Aucune candidature pour le moment.</p>
        <?php endif; ?>
    </article>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

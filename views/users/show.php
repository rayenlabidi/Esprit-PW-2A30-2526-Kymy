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
        <div class="section-head" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <h2>Candidatures envoyees</h2>
            <button class="btn btn-outline btn-small" onclick="exportToPDF('applications-container', 'mes_candidatures.pdf')">Exporter en PDF</button>
        </div>

        <form method="GET" action="index.php" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; margin-bottom: 1rem; background: var(--color-surface); padding: 1rem; border-radius: 8px;">
            <input type="hidden" name="module" value="users">
            <input type="hidden" name="action" value="show">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Recherche (Job)</label>
                <input type="text" name="search_app" class="form-control" value="<?= h($filters_app['search'] ?? '') ?>" placeholder="Titre du job...">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Trier par</label>
                <select name="sort_app" class="form-control">
                    <option value="date_desc" <?= ($filters_app['sort'] ?? '') === 'date_desc' ? 'selected' : '' ?>>Plus recentes d'abord</option>
                    <option value="date_asc" <?= ($filters_app['sort'] ?? '') === 'date_asc' ? 'selected' : '' ?>>Plus anciennes d'abord</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-small">Filtrer</button>
        </form>

        <div id="applications-container">
            <?php if ($applications): ?>
                <ul class="simple-list">
                    <?php foreach ($applications as $item): ?>
                        <li>
                            <strong><?= h($item['title']) ?></strong> • <?= h($item['job_type']) ?> • 
                            <span class="badge <?= status_badge_class($item['status']) ?>"><?= h($item['status']) ?></span>
                            <br><small class="muted">Postule le <?= format_date($item['applied_at']) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="empty-copy">Aucune candidature ne correspond aux filtres.</p>
            <?php endif; ?>
        </div>
    </article>
</section>

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
